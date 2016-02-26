<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tool/utility', 'utility');
        $this->load->library('mongo_db');
        $this->load->library('parser');
    }

    public function processVerifiedIPN($client_id, $plan_id, $POST, $log_id)
    {
        /* find details of the client */
        $client = $this->getClientById($client_id);

        /* find details of the current plan of the client */
        $myplan_id = $this->getPlanIdByClientId($client_id);
        $myplan = $myplan_id ? $this->getPlanById($myplan_id) : null;

        /* find details of the subscribed plan of the client */
        $plan = $this->getPlanById($plan_id);

        /* process PayPal IPN message differently according to 'txn_type' */
        log_message('info', 'IPN PayPal txn_type: ' . $POST['txn_type']);
        switch ($POST['txn_type']) {
            case PAYPAL_TXN_TYPE_SUBSCR_SIGNUP:
                $this->setDateBilling($client_id, $plan, $POST['subscr_id']);
                $this->setDateStartAndDateExpire($client_id); // we have to put this because if the chosen plan has trial period, then we will not receive IPN payment
                if ($myplan['price'] <= 0) { // change the plan if current plan is free
                    $this->changePlan($client, $myplan_id, $plan_id);
                }
                $html = $this->parser->parse('message_signup_plan.html', array(
                    'firstname' => $client['first_name'],
                    'lastname' => $client['last_name'],
                    'channel' => PAYMENT_CHANNEL_PAYPAL,
                    'plan_name' => $plan['name'],
                    'plan_price' => $plan['price'],
                    'reference_number' => $log_id->{'$id'},
                    'date' => date('l, F d, Y', time())
                ), true);
                $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                    '[Playbasis] Registration Confirmation', $html);
                break;
            case PAYPAL_TXN_TYPE_SUBSCR_MODIFY:
                $html = $this->parser->parse('message.html', array(
                    'firstname' => $client['first_name'],
                    'lastname' => $client['last_name'],
                    'message' => 'At your request, we will change your Playbasis subscription plan to ' . $plan['name'] . '.<br>The billing for your new subscription will take effect on ' . $POST['subscr_effective'] . '.<br>Below are the details of the order you have placed with us:<br><table><tr><td>&nbsp;</td><td>Old Plan</td><td>New Plan</td></tr> <tr><td>Client ID</td><td>' . $client_id . '</td><td>' . $client_id . '</td></tr> <tr><td>Subscription plan</td><td>' . $myplan['name'] . '</td><td>' . $plan['name'] . '</td></tr> <tr><td>Monthly price</td><td>' . $myplan['price'] . '</td><td>' . $plan['price'] . '</td></tr> </table><br>We are looking forward to hearing from you soon.'
                ), true);
                $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                    '[Playbasis] Request to Change Your Subscription Plan', $html);
                break;
            case PAYPAL_TXN_TYPE_SUBSCR_PAYMNT:
                /* check if we already process this 'txn_id' */
                if (!$this->hasAlreadyProcessed($POST['txn_id'])) { // skip possible duplicate IPN messages
                    $amount = intval($POST['mc_gross']);

                    /* insert into payment log */
                    $this->insertPaymentLog($client_id, $plan_id, array(
                        'channel' => PAYMENT_CHANNEL_PAYPAL,
                        'status' => $POST['payment_status'],
                        'amount' => $amount,
                        'currency' => $POST['mc_currency'],
                        'txn_id' => $POST['txn_id'],
                        'ipn_track_id' => $POST['ipn_track_id'],
                    ));

                    /* check payment status */
                    switch ($POST['payment_status']) {
                        case PAYPAL_PAYMENT_STATUS_COMPLETED:
                            log_message('info', 'Client ' . $client_id . ' has paid successfully');
                            /* check the amount that client has paid with the plan */
                            if ($amount != $plan['price']) { /* for security, we have to check payment amount */
                                log_message('error',
                                    'Client ' . $client_id . ' has paid incorrect amount ' . $amount . ', should be ' . $plan['price']);
                                $html = $this->parser->parse('message.html', array(
                                    'firstname' => $client['first_name'],
                                    'lastname' => $client['last_name'],
                                    'message' => 'The payment is successful, but the payment amount (' . $amount . ' USD) is incorrect.<br>Please contact us for further investigation.'
                                ), true);
                                $this->utility->email_bcc(EMAIL_FROM,
                                    array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                                    '[Playbasis] Unable to Process Your Playbasis Payment', $html);
                            } else {
                                /* adjust billing period, 'date_start' and 'date_expire', allowing client to use our API */
                                log_message('info',
                                    'Client ' . $client_id . ' has been set billing period ("date_start" and "date_expire")');
                                $this->setDateStartAndDateExpire($client_id);
                                $html = $this->parser->parse('message_pay_plan.html', array(
                                    'firstname' => $client['first_name'],
                                    'lastname' => $client['last_name'],
                                    'transaction_id' => $log_id->{'$id'},
                                    'date' => $POST['payment_date'],
                                    'plan_name' => $plan['name'],
                                    'plan_price' => $plan['price']
                                ), true);
                                $this->utility->email_bcc(EMAIL_FROM,
                                    array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                                    '[Playbasis] Receipt for Your Payment to Playbasis', $html);

                                /* detecting plan has been changed */
                                if ($myplan_id != $plan_id) {
                                    $this->changePlan($client, $myplan_id, $plan_id);
                                    log_message('info',
                                        'Client ' . $client_id . ' has changed the plan from ' . $myplan_id . ' to ' . $plan_id);
                                }
                            }
                            break;
                        default:
                            log_message('error',
                                'Client ' . $client_id . ' has paid, but the payment status from IPN is ' . $POST['payment_status']);
                            $html = $this->parser->parse('message.html', array(
                                'firstname' => $client['first_name'],
                                'lastname' => $client['last_name'],
                                'message' => 'We were unable to process the subscription plan payment ' . $plan['name'] . ' of ' . $plan['price'] . ' USD for Playbasis account, ' . $client['first_name'] . ' ' . $client['last_name'] . ', on ' . date('l, F d, Y',
                                        time()) . '. PayPal informed us the payment is not completed.<br><br>What should you do now?<br>Please check the payment setting in your PayPal account and try to resolve the problem.<br><br>What about your account?<br>We\'ll keep your account active for now. However, five days after the initial payment failure we\'ll automatically downgrade your account to the Free plan. You\'ll be able to upgrade later with a valid payment without losing any of your settings.'
                            ), true);
                            $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                                '[Playbasis] Unable to Process Your Playbasis Payment', $html);
                            break;
                    }
                }
                break;
            case PAYPAL_TXN_TYPE_SUBSCR_FAILED:
                /* As we rely on PayPal to do reattempt for us if the payment has failed.
                Eventually, PayPal will send 'subscr_cancel' after few reattempts within 3 days.
                So we will not block the usage when we receive 'subscr_failed'.

                /* send email to the user notifying the failure of payment with reason */
                $html = $this->parser->parse('message.html', array(
                    'firstname' => $client['first_name'],
                    'lastname' => $client['last_name'],
                    'message' => 'We were unable to process the subscription plan payment ' . $plan['name'] . ' of ' . $plan['price'] . ' USD for Playbasis account, ' . $client['first_name'] . ' ' . $client['last_name'] . ', on ' . date('l, F d, Y',
                            time()) . '. PayPal informed us the payment has been declined ' . $POST['retry_at'] . ' times without giving a specific reason.<br><br>What should you do now?<br>Please check the payment setting in your PayPal account and try to resolve the problem.<br><br>What about your account?<br>We\'ll keep your account active for now. However, five days after the initial payment failure we\'ll automatically downgrade your account to the Free plan. You\'ll be able to upgrade later with a valid payment without losing any of your settings.'
                ), true);
                $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                    '[Playbasis] Unable to Process Your Playbasis Payment', $html);
                break;
            case PAYPAL_TXN_TYPE_SUBSCR_CANCEL:
                /* remove "date_billing" */
                $this->unsetDateBilling($client_id);

                /* remove "date_start" and "date_expire" */
                $this->unsetDateStartAndDateExpire($client_id);

                $html = $this->parser->parse('message_cancel_plan.html', array(
                    'firstname' => $client['first_name'],
                    'lastname' => $client['last_name'],
                    'plan_name' => $plan['name'],
                    'plan_price' => $plan['price'],
                    'date' => date('l, F d, Y', time())
                ), true);
                $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                    '[Playbasis] Subscription Plan Cancellation', $html);

                /* change the client's plan to be a free plan */
                $this->changePlan($client, $myplan_id, new MongoId(FREE_PLAN));
                log_message('info',
                    'Client ' . $client_id . ' has canceled the subscription and has been changed to free plan');
                break;
            default:
                log_message('error', 'Unsupported PayPal txn_type: ' . $POST['txn_type']);
                return false;
        }
        return true;
    }

    public function invoiceCreated($client, $plan, $subscription_id)
    {
    }

    public function invoiceUpdated($client, $plan, $subscription_id)
    {
    }

    public function invoicePaymentSucceeded($client, $plan, $subscription_id)
    {
        /* adjust billing period, 'date_start' and 'date_expire', allowing client to use our API */
        log_message('info',
            'Client ' . $client['_id'] . ' has been set billing period ("date_start" and "date_expire")');
        $this->setDateStartAndDateExpire($client['_id']);
    }

    public function invoicePaymentFailed($client, $plan, $subscription_id, $retry_at)
    {
        /* after several attempts, send email to the user notifying the failure of payment */
        $html = $this->parser->parse('message.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'message' => 'We were unable to process the subscription plan payment ' . $plan['name'] . ' of ' . $plan['price'] . ' USD for Playbasis account, ' . $client['first_name'] . ' ' . $client['last_name'] . ', on ' . date('l, F d, Y',
                    time()) . '. The payment has been declined ' . $retry_at . ' times.<br><br>What about your account?<br>We\'ll keep your account active for now. However, five days after the initial payment failure we\'ll automatically downgrade your account to the Free plan. You\'ll be able to upgrade later with a valid payment without losing any of your settings.'
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Your Playbasis Payment has Failed', $html);
    }

    public function chargeSucceeded($client, $channel, $txn_id, $txn_date)
    {
        $html = $this->parser->parse('message_confirm_payment.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'transaction_id' => $txn_id,
            'date' => date('l, F d, Y', $txn_date),
            'channel' => $channel
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Receipt for Your Payment to Playbasis', $html);
    }

    public function chargeFailed($client, $channel, $txn_id, $txn_date, $failure_code, $failure_message)
    {
        /* send email to the user notifying the failure of payment with reason */
        $html = $this->parser->parse('message.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'message' => 'We were unable to process the subscription plan payment for Playbasis account, ' . $client['first_name'] . ' ' . $client['last_name'] . ', on ' . date('l, F d, Y',
                    time()) . '. ' . $channel . ' informed us the payment has been declined with a following reason: ' . $failure_message . ' (' . $failure_code . ').<br><br>What should you do now?<br>Please check the payment setting in your ' . $channel . ' account and try to resolve the problem.'
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Unable to Process Your Playbasis Payment', $html);
    }

    public function log(
        $client_id,
        $channel,
        $event_id,
        $txn_id,
        $amount,
        $currency,
        $status,
        $failure_code,
        $failure_message
    ) {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $this->mongo_db->insert('playbasis_payment_log', array(
            'channel' => $channel,
            'client_id' => $client_id,
            'status' => $status,
            'amount' => $amount,
            'currency' => $currency,
            'txn_id' => $txn_id,
            'event_id' => $event_id,
            'failure_code' => $failure_code,
            'failure_message' => $failure_message,
            'date_added' => $d,
            'date_modified' => $d,
        ));
    }

    public function subscriptionCreated(
        $client,
        $plan,
        $myplan,
        $subscription_id,
        $period_start,
        $period_end,
        $trial_start,
        $trial_end
    ) {
        $this->setDateBilling($client['_id'], $plan, $subscription_id);
        $this->setDateStartAndDateExpire($client['_id']);
        $this->changePlan($client, $myplan['_id'], $plan['_id']);
        $html = $this->parser->parse('message_signup_plan.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'channel' => PAYMENT_CHANNEL_STRIPE,
            'plan_name' => $plan['name'],
            'plan_price' => $plan['price'],
            'reference_number' => $subscription_id,
            'date' => date('l, F d, Y', time())
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Registration Confirmation', $html);
    }

    public function subscriptionUpdated(
        $client,
        $plan,
        $myplan,
        $subscription_id,
        $period_start,
        $period_end,
        $trial_start,
        $trial_end
    ) {
        if ($myplan['_id'] != $plan['_id']) { /* detecting plan has been changed */
            log_message('info',
                'Client ' . $client['_id'] . ' has changed the plan from ' . $myplan['_id'] . ' to ' . $plan['_id']);
            $this->changePlan($client, $myplan['_id'], $plan['_id']);
            $html = $this->parser->parse('message.html', array(
                'firstname' => $client['first_name'],
                'lastname' => $client['last_name'],
                'message' => 'At your request, we have changed your Playbasis subscription plan to ' . $plan['name'] . '.<br>The next billing for your subscription will be on the same date, but the amount will be different as we will prorate the cost.<br>Below are the details of the order you have placed with us:<br><table><tr><td>&nbsp;</td><td>Old Plan</td><td>New Plan</td></tr> <tr><td>Client ID</td><td>' . $client['_id'] . '</td><td>' . $client['_id'] . '</td></tr> <tr><td>Subscription plan</td><td>' . $myplan['name'] . '</td><td>' . $plan['name'] . '</td></tr> <tr><td>Monthly price</td><td>' . $myplan['price'] . '</td><td>' . $plan['price'] . '</td></tr> </table><br>We are looking forward to hearing from you soon.'
            ), true);
            $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
                '[Playbasis] Request to Change Your Subscription Plan', $html);
        } else { /* end of trial period */

        }
    }

    public function subscriptionDeleted(
        $client,
        $plan,
        $myplan,
        $subscription_id,
        $period_start,
        $period_end,
        $trial_start,
        $trial_end
    ) {
        $this->unsetDateBilling($client['_id']);
        $this->unsetDateStartAndDateExpire($client['_id']);
        $this->changePlan($client, $myplan['_id'], new MongoId(FREE_PLAN));
        log_message('info',
            'Client ' . $client['_id'] . ' has canceled the subscription and has been changed to free plan');
        $html = $this->parser->parse('message_cancel_plan.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'plan_name' => $plan['name'],
            'plan_price' => $plan['price'],
            'date' => date('l, F d, Y', time())
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Subscription Plan Cancellation', $html);
    }

    public function trialPeriodWillEnd(
        $client_id,
        $plan_id,
        $myplan,
        $subscription_id,
        $period_start,
        $period_end,
        $trial_start,
        $trial_end
    ) {
    }

    public function getClientIdByStripeId($stripe_id)
    {
        $this->mongo_db->select(array('client_id'));
        $this->mongo_db->where('stripe_id', $stripe_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_stripe');
        return $results ? $results[0]['client_id'] : null;
    }

    public function existPaymentEvent($event_id)
    {
        $this->mongo_db->where('_id', $event_id);
        return $this->mongo_db->count('playbasis_payment_event');
    }

    public function insertPaymentEvent($event_id, $event)
    {
        $this->mongo_db->insert('playbasis_payment_event', array(
            '_id' => $event_id,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate(),
        ));
    }

    public function getPlanIdByClientId($client_id)
    {
        $permission = $this->getLatestPermissionByClientId($client_id);
        return $permission ? $permission['plan_id'] : null;
    }

    public function getLatestPermissionByClientId($client_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_permission');
        return $results ? $results[0] : null;
    }

    private function getById($id, $collection)
    {
        $this->mongo_db->where(array('_id' => $id));
        $results = $this->mongo_db->get($collection);
        return $results ? $results[0] : null;
    }

    public function getClientById($client_id)
    {
        return $this->getById($client_id, 'playbasis_client');
    }

    public function getPlanById($plan_id)
    {
        $plan = $this->getById($plan_id, 'playbasis_plan');
        if ($plan && !array_key_exists('price', $plan)) {
            $plan['price'] = DEFAULT_PLAN_PRICE;
        }
        return $plan;
    }

    public function getSytemRewardById($reward_id)
    {
        return $this->getById($reward_id, 'playbasis_reward');
    }

    public function getSytemFeatureById($feature_id)
    {
        return $this->getById($feature_id, 'playbasis_feature');
    }

    public function getSytemActionById($action_id)
    {
        return $this->getById($action_id, 'playbasis_action');
    }

    public function getSytemJigsawById($jigsaw_id)
    {
        return $this->getById($jigsaw_id, 'playbasis_jigsaw');
    }

    public function listSitesByClientId($client_id)
    {
        $this->mongo_db->where(array('client_id' => $client_id));
        return $this->mongo_db->get('playbasis_client_site');
    }

    private function setDateBilling($client_id, $plan, $subscriber_id)
    {
        /* find number of trial days */
        $trial_days = array_key_exists('limit_others', $plan) && array_key_exists('trial',
            $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
        if ($trial_days == null) {
            $trial_days = 0;
        }
        /* set billing date */
        $date_billing = strtotime("+" . $trial_days . " day", time());
        /* update billing in client's record */
        $this->mongo_db->where(array('_id' => $client_id));
        $this->mongo_db->set('subscr_id', $subscriber_id);
        $this->mongo_db->set('plan_id', $plan['_id']);
        $this->mongo_db->set('date_billing', new MongoDate($date_billing));
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_client');
    }

    private function unsetDateBilling($client_id)
    {
        /* update billing in client's record */
        $this->mongo_db->where(array('_id' => $client_id));
        $this->mongo_db->unset_field(array('subscr_id', 'date_billing')); // "plan_id" will not be removed for now
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_client');
    }

    private function setDateStartAndDateExpire($client_id)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $today = time();
        $date_start = $today;
        $date_expire = strtotime("+1 month", $date_start);
        $this->mongo_db->where(array('_id' => $client_id));
        $this->mongo_db->set('date_start', new MongoDate($date_start));
        $this->mongo_db->set('date_expire', new MongoDate($date_expire));
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update('playbasis_client');
    }

    private function unsetDateStartAndDateExpire($client_id)
    {
        $this->mongo_db->where(array('_id' => $client_id));
        $this->mongo_db->unset_field(array('date_start', 'date_expire'));
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_client');
    }

    private function changePlan($client, $from_plan_id, $to_plan_id)
    {
        $client_id = $client['_id'];

        /* get detail of the destination plan */
        $plan = $to_plan_id ? $this->getPlanById($to_plan_id) : null;
        if (!$plan) {
            return;
        } // early return if we cannot find the chosen plan

        /* get detail of the destination plan */
        $myplan = $from_plan_id ? $this->getPlanById($from_plan_id) : null;

        /* associate all client's sites to a new plan */
        $this->mongo_db->where(array('client_id' => $client_id));
        $this->mongo_db->set('plan_id', $plan['_id']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update_all('playbasis_permission');

        /* populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client */
        $sites = $this->listSitesByClientId($client_id); // loop over all sites of the clients
        if ($sites) {
            foreach ($sites as $site) {
                $site_id = $site['_id'];
                $this->copyRewardToClient($client_id, $site_id, $plan);
                $this->copyFeaturedToClient($client_id, $site_id, $plan);
                $this->copyActionToClient($client_id, $site_id, $plan);
                $this->copyJigsawToClient($client_id, $site_id, $plan);
            }
        }

        $html = $this->parser->parse('message_change_plan.html', array(
            'firstname' => $client['first_name'],
            'lastname' => $client['last_name'],
            'client_id' => $client_id,
            'old_plan_name' => $myplan['name'],
            'new_plan_name' => $plan['name'],
            'old_plan_price' => $myplan['price'],
            'new_plan_price' => $plan['price']
        ), true);
        $this->utility->email_bcc(EMAIL_FROM, array($client['email'], EMAIL_BCC_PLAYBASIS_EMAIL),
            '[Playbasis] Your Subscription Plan has been Changed', $html);
    }

    private function copyRewardToClient($client_id, $site_id, $plan)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_reward_to_client");

        if (isset($plan['reward_to_plan'])) {
            foreach ($plan['reward_to_plan'] as $reward) {
                $limit = empty($reward['limit']) ? null : (int)$reward['limit'];

                $reward_data = $this->getSytemRewardById($reward['reward_id']);

                $this->mongo_db->insert('playbasis_reward_to_client', array(
                    'reward_id' => new MongoID($reward['reward_id']),
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'group' => $reward_data['group'],
                    'name' => $reward_data['name'],
                    'description' => $reward_data['description'],
                    'init_dataset' => $reward_data['init_dataset'],
                    'limit' => $limit,
                    'sort_order' => $reward_data['sort_order'],
                    'status' => (bool)$reward_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d,
                    'is_custom' => false,
                ));
            }
        }
    }

    private function copyFeaturedToClient($client_id, $site_id, $plan)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->delete_all("playbasis_feature_to_client");

        if (isset($plan['feature_to_plan'])) {
            foreach ($plan['feature_to_plan'] as $feature_id) {

                $feature_data = $this->getSytemFeatureById($feature_id);

                $this->mongo_db->insert('playbasis_feature_to_client', array(
                    'feature_id' => new MongoID($feature_id),
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'name' => $feature_data['name'],
                    'description' => $feature_data['description'],
                    'link' => $feature_data['link'],
                    'icon' => $feature_data['icon'],
                    'sort_order' => $feature_data['sort_order'],
                    'status' => (bool)$feature_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d,
                ));
            }
        }
    }

    private function copyActionToClient($client_id, $site_id, $plan)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_action_to_client");

        if (isset($plan['action_to_plan'])) {
            foreach ($plan['action_to_plan'] as $action_id) {
                $this->mongo_db->where('client_id', $client_id);
                $this->mongo_db->where('site_id', $site_id);
                $this->mongo_db->where('action_id', $action_id);
                $allClients = $this->mongo_db->get('playbasis_action_to_client');

                if (!$allClients) {
                    $action_data = $this->getSytemActionById($action_id);

                    $this->mongo_db->insert('playbasis_action_to_client', array(
                        'action_id' => new MongoID($action_id),
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'name' => $action_data['name'],
                        'description' => $action_data['description'],
                        'icon' => $action_data['icon'],
                        'color' => $action_data['color'],
                        'init_dataset' => $action_data['init_dataset'],
                        'sort_order' => $action_data['sort_order'],
                        'status' => (bool)$action_data['status'],
                        'date_modified' => $d,
                        'date_added' => $d,
                        'is_custom' => false,
                    ));
                }
            }
        }
    }

    private function copyJigsawToClient($client_id, $site_id, $plan)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->delete_all("playbasis_game_jigsaw_to_client");

        if (isset($plan['jigsaw_to_plan'])) {
            foreach ($plan['jigsaw_to_plan'] as $jigsaw_id) {

                $jigsaw_data = $this->getSytemJigsawById($jigsaw_id);

                $this->mongo_db->insert('playbasis_game_jigsaw_to_client', array(
                    'jigsaw_id' => new MongoID($jigsaw_id),
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'name' => $jigsaw_data['name'],
                    'description' => $jigsaw_data['description'],
                    'category' => $jigsaw_data['category'],
                    'class_path' => $jigsaw_data['class_path'],
                    'init_dataset' => $jigsaw_data['init_dataset'],
                    'sort_order' => $jigsaw_data['sort_order'],
                    'status' => (bool)$jigsaw_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d,
                ));
            }
        }
    }

    private function hasAlreadyProcessed($txn_id)
    {
        $this->mongo_db->where(array('txn_id' => $txn_id));
        return $this->mongo_db->count('playbasis_payment_log') > 0;
    }

    private function insertPaymentLog($client_id, $plan_id, $payment)
    {
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $this->mongo_db->insert('playbasis_payment_log', array(
            'channel' => $payment['channel'],
            'client_id' => $client_id,
            'plan_id' => $plan_id,
            'status' => $payment['status'],
            'amount' => $payment['amount'],
            'currency' => $payment['currency'],
            'txn_id' => $payment['txn_id'],
            'ipn_track_id' => $payment['ipn_track_id'],
            'date_added' => $d,
            'date_modified' => $d,
        ));
    }
}

?>
