<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET', true);

class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Client_model');
//      $this->load->model('Domain_model');
        $this->load->model('App_model');
        $this->load->model('Plan_model');
        $this->load->model('Sms_model');

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        if ($this->input->get('site_id')) {
            $this->User_model->updateSiteId($this->input->get('site_id'));
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("account", $lang['folder']);

        $this->purchase = array('subscribe', 'upgrade', 'downgrade');
    }

    /*
        playbasis_client - store client's subscription status
        playbasis_permission - store the current plan of a client
        playbasis_plan - store plan details and plan price (note: also 'display' flags, which is to determine whether to feature the plan in sign-up page of playbasis.com)
        playbasis_payment_log - store payment transactions (due to their subscription plan)
        playbasis_notification_log - store all PayPal IPN messages
        playbasis_payment_channel - store all available payment channels
    */

//  public function index() {
//
//      if(!$this->validateAccess()){
//          echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
//      }
//
//      $this->data['meta_description'] = $this->lang->line('meta_description');
//      $this->data['title'] = $this->lang->line('title');
//      $this->data['heading_title'] = $this->lang->line('account_title');
//      $this->data['text_no_results'] = $this->lang->line('text_no_results');
//      $this->data['main'] = 'account';
//
//      /* check that current logged in user is normal user (not super admin) */
//      if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
//          $this->data['main'] = 'account_admin';
//          $this->load->vars($this->data);
//          $this->render_page('template');
//          return;
//      }
//
//      $site_id = $this->User_model->getSiteId();
//      if (!empty($site_id)) {
//          /* clear session from 'add_site' if applicable */
//          if ($this->session->userdata('site')) $this->session->unset_userdata('site');
//          if ($this->session->userdata('plan_id')) $this->session->unset_userdata('plan_id');
//
//          /* find details of the subscribed plan of the client */
//          $plan_subscription = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
//          $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
//          if (!array_key_exists('price', $plan)) {
//          	$plan['price'] = DEFAULT_PLAN_PRICE;
//          }
//          $price = $plan['price'];
//          $this->session->set_userdata('plan', $plan);
//          $plan_days_total = array_key_exists('limit_others', $plan) && array_key_exists('trial', $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
//          if ($plan_days_total == null) $plan_days_total = 0;
//          $plan_free_flag = $price <= 0;
//          $plan_paid_flag = !$plan_free_flag;
//          $plan_trial_flag = $plan_paid_flag && $plan_days_total > 0;
//
//          /* find details of the client */
//          $client = $this->Client_model->getClientById($this->User_model->getClientId());
//          // "date_start" and "date_expire" will be set when we receive payment confirmation in each month
//          // So if whenever payment fails, the two fields would not be updated, which results in blocking the usage of API.
//          // In addition, "date_expire" will include extra days to cover grace period.
//          $date_start = array_key_exists('date_start', $client) && !empty($client['date_start']) ? $client['date_start']->sec : null;
//          $date_expire = array_key_exists('date_expire', $client) && !empty($client['date_expire']) ? $client['date_expire']->sec : null;
//          // Whenever we set "date_billing", it means that the client has already set up subscription.
//          // The date will be immediately after the trial period (if exits),
//          // of which the date is the first day of the client in billing period of the plan.
//          // After the billing period has ended, "date_billing" is unset from client's record,
//          // so the client has to extend the subscription before contract expires.
//          $date_billing = array_key_exists('date_billing', $client) && !empty($client['date_billing']) ? $client['date_billing']->sec : null;
//          $days_remaining = $this->find_diff_in_days(time(), $date_billing);
//
//          $this->data['client'] = $client;
//          $this->data['client']['valid'] = ($plan_free_flag || ($date_billing && $this->check_valid_payment($client)));
//          $this->data['client']['trial_remaining_days'] = $days_remaining;
//          $this->data['client']['date_billing'] = $date_billing;
//          $this->data['client']['date_start'] = $date_start;
//          $this->data['client']['date_expire'] = $date_expire;
//          $this->data['client']['date_added'] = $client['date_added']->sec;
//          $this->data['client']['date_modified'] = $client['date_modified']->sec;
//          $this->data['plan'] = $plan;
//          $this->data['plan']['free_flag'] = $plan_free_flag;
//          $this->data['plan']['paid_flag'] = $plan_paid_flag;
//          $this->data['plan']['trial_flag'] = $plan_trial_flag;
//          $this->data['plan']['trial_total_days'] = $plan_days_total;
//          $this->data['plan']['date_added'] = $plan_subscription['date_added']->sec;
//          $this->data['plan']['date_modified'] = $plan_subscription['date_modified']->sec;
//      } else {
//              redirect('/account/add_site', 'refresh');
//          redirect('/account/update_profile', 'refresh');
//      }
//
//      $this->load->vars($this->data);
//      $this->render_page('template');
//  }

    public function index()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('account_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'account';

        /* check that current logged in user is normal user (not super admin) */
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            $this->data['main'] = 'account_admin';
            $this->load->vars($this->data);
            $this->render_page('template');
            return;
        }

        if ($this->session->userdata('plan_id')) {
            $this->session->unset_userdata('plan_id');
        }

        /* find details of the subscribed plan of the client */
        $plan_subscription = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
        $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
        if (!array_key_exists('price', $plan)) {
            $plan['price'] = DEFAULT_PLAN_PRICE;
        }
        $price = $plan['price'];
        $this->session->set_userdata('plan', $plan);
        $plan_days_total = array_key_exists('limit_others', $plan) && array_key_exists('trial',
            $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
        if ($plan_days_total == null) {
            $plan_days_total = 0;
        }
        $plan_free_flag = $price <= 0;
        $plan_paid_flag = !$plan_free_flag;
        $plan_trial_flag = $plan_paid_flag && $plan_days_total > 0;

        /* find details of the client */
        $client = $this->Client_model->getClientById($this->User_model->getClientId());
        // "date_start" and "date_expire" will be set when we receive payment confirmation in each month
        // So if whenever payment fails, the two fields would not be updated, which results in blocking the usage of API.
        // In addition, "date_expire" will include extra days to cover grace period.
        $date_start = array_key_exists('date_start',
            $client) && !empty($client['date_start']) ? $client['date_start']->sec : null;
        $date_expire = array_key_exists('date_expire',
            $client) && !empty($client['date_expire']) ? $client['date_expire']->sec : null;
        // Whenever we set "date_billing", it means that the client has already set up subscription.
        // The date will be immediately after the trial period (if exits),
        // of which the date is the first day of the client in billing period of the plan.
        // After the billing period has ended, "date_billing" is unset from client's record,
        // so the client has to extend the subscription before contract expires.
        $date_billing = array_key_exists('date_billing',
            $client) && !empty($client['date_billing']) ? $client['date_billing']->sec : null;
        $days_remaining = $this->find_diff_in_days(time(), $date_billing);

        $this->data['client'] = $client;
        $this->data['client']['valid'] = ($plan_free_flag || ($date_billing && $this->check_valid_payment($client)));
        $this->data['client']['trial_remaining_days'] = $days_remaining;
        $this->data['client']['date_billing'] = $date_billing;
        $this->data['client']['date_start'] = $date_start;
        $this->data['client']['date_expire'] = $date_expire;
        $this->data['client']['date_added'] = $client['date_added']->sec;
        $this->data['client']['date_modified'] = $client['date_modified']->sec;
        $this->data['plan'] = $plan;
        $this->data['plan']['free_flag'] = $plan_free_flag;
        $this->data['plan']['paid_enterprise_flag'] = $plan_free_flag && ($plan_subscription['plan_id'] != FREE_PLAN);
        $this->data['plan']['paid_flag'] = $plan_paid_flag;
        $this->data['plan']['trial_flag'] = $plan_trial_flag;
        $this->data['plan']['trial_total_days'] = $plan_days_total;
        $this->data['plan']['date_added'] = $plan_subscription['date_added']->sec;
        $this->data['plan']['date_modified'] = $plan_subscription['date_modified']->sec;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function subscribe()
    {
        $this->purchase(PURCHASE_SUBSCRIBE);
    }

    public function upgrade()
    {
        $this->purchase(PURCHASE_UPGRADE);
    }

    public function downgrade()
    {
        $this->purchase(PURCHASE_DOWNGRADE);
    }

    public function changeplan()
    {
        $this->purchase(PURCHASE_UPGRADE);
    }

    private function purchase($mode)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $client = $this->Client_model->getClientById($this->User_model->getClientId());

        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('plan', $this->lang->line('form_package'), 'trim|required');
            $this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
            $channel = $this->input->post('channel');
            if (!$this->check_valid_payment_channel($channel)) {
                $this->data['message'] = 'Invalid payment channel';
            }
            if ($mode != PURCHASE_SUBSCRIBE && $mode != PURCHASE_UPGRADE && $mode != PURCHASE_DOWNGRADE) {
                $this->data['message'] = 'Invalid payment mode';
                $mode = PURCHASE_SUBSCRIBE; // set mode to be default "subscribe"
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $success = true;

                $selected_plan = $this->Plan_model->getPlanById(new MongoId($this->input->post('plan')));
                if (!array_key_exists('price', $selected_plan)) {
                    $selected_plan['price'] = DEFAULT_PLAN_PRICE;
                }

                $plan_free_flag = $selected_plan['price'] <= 0;
                $date_today = time();
                $trial_days = 0;
                $modify = false;
                switch ($mode) {
                    case PURCHASE_SUBSCRIBE:
                        $days_total = array_key_exists('limit_others', $selected_plan) && array_key_exists('trial',
                            $selected_plan['limit_others']) ? $selected_plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
                        $date_trial_end = strtotime("+" . $days_total . " day", $date_today);
                        $trial_days = $plan_free_flag ? 0 : $this->find_diff_in_days($date_today,
                            $date_trial_end); // free account would not get trial days when they decide to subscribe
                        break;
                    case PURCHASE_UPGRADE:
                    case PURCHASE_DOWNGRADE:
                        $date_billing = array_key_exists('date_billing',
                            $client) && !empty($client['date_billing']) ? $client['date_billing']->sec : null;
                        $days_remaining = $this->find_diff_in_days($date_today, $date_billing);
                        $trial_days = $days_remaining >= 0 ? $days_remaining : 0;
                        $modify = true;
                        break;
                    default:
                        log_message('error', 'Invalid mode = ' . $mode);
                        break;
                }

                /* set the parameters for payment */
                $this->data['heading_title'] = $this->lang->line('order_title');
                $this->data['params'] = array(
                    'plan_id' => $selected_plan['_id'],
                    'plan_name' => $selected_plan['name'],
                    'price' => $selected_plan['price'],
                    'trial_days' => $trial_days > MAX_ALLOWED_TRIAL_DAYS ? MAX_ALLOWED_TRIAL_DAYS : $trial_days,
                    'callback' => API_SERVER . '/notification',
                    'modify' => $modify,
                );

                $plan_id = $this->data['params']['plan_id'] . '';
                switch ($channel) {
                    case PAYMENT_CHANNEL_PAYPAL:
                        $this->data['main'] = 'account_purchase_paypal';
                        break;
                    case PAYMENT_CHANNEL_STRIPE:
                        require_once(APPPATH . '/libraries/stripe/init.php');
                        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
                        try {
                            $plan = \Stripe\Plan::retrieve($plan_id);
                        } catch (Exception $e) {
                            \Stripe\Plan::create(array(
                                'amount' => $this->data['params']['price'] * 100,
                                'interval' => 'month',
                                'name' => $this->data['params']['plan_name'],
                                'currency' => 'usd',
                                'id' => $plan_id,
                                'trial_period_days' => $this->data['params']['trial_days'],
                            ));
                        }
                        $this->data['params']['publishable_key'] = STRIPE_PUBLISHABLE_KEY;
                        $this->data['main'] = 'account_purchase_stripe';
                        $this->data['form'] = 'account/stripe';
                        break;
                }
            }
        }
        if (!$success) {
            $this->data['mode'] = $mode;
            $this->data['plans'] = $this->Plan_model->listDisplayPlans();
            $this->data['heading_title'] = $this->lang->line('channel_title');

            $this->data['main'] = 'account_purchase';
            $this->data['form'] = 'account/' . $this->purchase[$mode];
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function stripe()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once(APPPATH . '/libraries/stripe/init.php');
            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
            $token = $this->input->post('stripeToken');
            $plan_id = $this->input->post('plan_id');
            $client_id = $this->User_model->getClientId();
            $client = $this->Client_model->getClient($client_id);
            $stripe = $this->Client_model->getStripe($client_id);
            $customer = null;
            if (!$stripe) { // new customer, assuming subscribe
                try {
                    $customer = \Stripe\Customer::create(array(
                        'source' => $token,
                        'plan' => $plan_id,
                        'email' => $client['email'],
                    ));
                    $this->Client_model->insertOrUpdateStripe($client_id, $customer->id,
                        $customer->subscriptions->data[0]->id);
                } catch (Exception $e) {
                    $this->session->set_flashdata("fail", $this->lang->line("error_payment_declined"));
                    redirect('/account', 'refresh');
                }
            } else { // existing customer, assuming upgrade/downgrade
                $customer = \Stripe\Customer::retrieve($stripe['stripe_id']);
                $subscription = $customer->subscriptions->retrieve($stripe['subscription_id']);
                $subscription->plan = $plan_id;
                $subscription->save();
            }
            redirect('/account/stripe_completed', 'refresh');
        }
        redirect('/account', 'refresh');
    }

    public function pay()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['message'] = null;

        $selected_plan = $this->User_model->getPlan();
        if (!array_key_exists('price', $selected_plan)) {
            $selected_plan['price'] = DEFAULT_PLAN_PRICE;
        }

        $plan_free_flag = $selected_plan['price'] <= 0;
        $date_today = time();
        $days_total = array_key_exists('limit_others', $selected_plan) && array_key_exists('trial',
            $selected_plan['limit_others']) ? $selected_plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
        $date_trial_end = $days_total ? strtotime("+" . $days_total . " day", $date_today) : null;
        $trial_days = $plan_free_flag ? 0 : ($date_trial_end ? $this->find_diff_in_days($date_today,
            $date_trial_end) : null); // free account would not get trial days when they decide to subscribe

        /* set the parameters for PayPal */
        $this->data['params'] = array(
            'plan_id' => $selected_plan['_id'],
            'plan_name' => $selected_plan['name'],
            'price' => $selected_plan['price'],
            'trial_days' => $trial_days > MAX_ALLOWED_TRIAL_DAYS ? MAX_ALLOWED_TRIAL_DAYS : $trial_days,
            'callback' => API_SERVER . '/notification',
            'modify' => false,
        );

        $this->data['heading_title'] = $this->lang->line('order_title');
        $plan_id = $this->data['params']['plan_id'] . '';
        switch (PAYMENT_CHANNEL_DEFAULT) {
            case PAYMENT_CHANNEL_PAYPAL:
                $this->data['main'] = 'account_purchase_paypal';
                break;
            case PAYMENT_CHANNEL_STRIPE:
                require_once(APPPATH . '/libraries/stripe/init.php');
                \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
                try {
                    $plan = \Stripe\Plan::retrieve($plan_id);
                } catch (Exception $e) {
                    \Stripe\Plan::create(array(
                        'amount' => $this->data['params']['price'] * 100,
                        'interval' => 'month',
                        'name' => $this->data['params']['plan_name'],
                        'currency' => 'usd',
                        'id' => $plan_id,
                        'trial_period_days' => $this->data['params']['trial_days'],
                    ));
                }
                $this->data['params']['publishable_key'] = STRIPE_PUBLISHABLE_KEY;
                $this->data['main'] = 'account_purchase_stripe';
                $this->data['form'] = 'account/stripe';
                break;
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function cancel_subscription()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['heading_title'] = $this->lang->line('cancel_title');

        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
            $channel = $this->input->post('channel');
            if (!$this->check_valid_payment_channel($channel)) {
                $this->data['message'] = 'Invalid payment channel';
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $success = true;

                switch ($channel) {
                    case PAYMENT_CHANNEL_PAYPAL:
                        $this->data['main'] = 'account_cancel_subscription_paypal';
                        break;
                    case PAYMENT_CHANNEL_STRIPE:
                        require_once(APPPATH . '/libraries/stripe/init.php');
                        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
                        $client_id = $this->User_model->getClientId();
                        $stripe = $this->Client_model->getStripe($client_id);
                        $customer = \Stripe\Customer::retrieve($stripe['stripe_id']);
                        $subscription = $customer->subscriptions->retrieve($stripe['subscription_id']);
                        $subscription->cancel();
                        $this->Client_model->removeStripe($client_id);
                        $this->data['main'] = 'account_cancel_subscription_stripe';
                        break;
                }
            }
        }
        if (!$success) {
            $this->data['main'] = 'account_cancel_subscription';
            $this->data['form'] = 'account/cancel_subscription';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function paypal_completed()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('congrat_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'account_purchase_paypal_done';

        /* clear basket in the session */
        $this->session->unset_userdata('plan');

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function stripe_completed()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('congrat_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'account_purchase_stripe_done';

        /* clear basket in the session */
        $this->session->unset_userdata('plan');

        $this->load->vars($this->data);
        $this->render_page('template');
    }

//  public function add_site() {
//
//      if(!$this->validateAccess()){
//          echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
//          die();
//      }
//
//      $this->data['meta_description'] = $this->lang->line('meta_description');
//      $this->data['title'] = $this->lang->line('title');
//      $this->data['text_no_results'] = $this->lang->line('text_no_results');
//
//      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//          $this->data['message'] = null;
//
//          $this->form_validation->set_rules('site', $this->lang->line('entry_site'), 'trim|required|min_length[3]|max_length[100]|xss_clean|check_space|url_exists_without_http');
//          $site = $this->input->post('site');
//          $this->session->set_userdata('site', $site);
//          if (!empty($site) && $this->Domain_model->checkDomainExists(array('domain_name' => $site))) $this->data['message'] = 'This site has already been registered';
//
//          if($this->form_validation->run() && $this->data['message'] == null){
//              redirect('/account/choose_plan', 'refresh');
//          }
//      }
//
//      if ($this->session->userdata('site')) $this->data['site']  = $this->session->userdata('site');
//      $this->data['heading_title'] = $this->lang->line('add_site_title');
//      $this->data['main'] = 'account_add_site';
//      $this->data['form'] = 'account/add_site';
//
//      $this->load->vars($this->data);
//      $this->render_page('template');
//  }

    public function first_app()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['heading_title'] = $this->lang->line('add_site_title');
        $this->data['main'] = 'partial/landingpage_partial';

        /* find details of the subscribed plan of the client */
        $plan_subscription = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
        $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
        if (!array_key_exists('price', $plan)) {
            $plan['price'] = DEFAULT_PLAN_PRICE;
        }
        $price = $plan['price'];
        $plan_free_flag = $price <= 0;
        $plan_paid_flag = !$plan_free_flag;
        $this->data['plan'] = $plan;
        $this->data['plan']['free_flag'] = $plan_free_flag;
        $this->data['plan']['paid_enterprise_flag'] = $plan_free_flag && ($plan_subscription['plan_id'] != FREE_PLAN);
        $this->data['plan']['paid_flag'] = $plan_paid_flag;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function update_password()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                'trim|required|min_length[5]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
                'required|matches[password]');

            if ($this->form_validation->run()) {
                $new_password = $this->input->post('password');
                $random_key = $this->input->post('random_key');
                $user = $this->User_model->checkRandomKey($random_key);
                $user_id = $user[0]['_id'];
                $this->User_model->insertNewPassword($user_id, $new_password);
                $this->User_model->logout();

                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'success', 'message' => 'Your password has been update!'));
                    exit();
                }

                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id
                );

                if ($client_id) {
                    $total = $this->App_model->getTotalAppsByClientId($data);
                } else {
                    $total = $this->App_model->getTotalApps($data);
                }

                if ($total == 0) {
                    $this->session->unset_userdata('site_id');
                    redirect('/first_app', 'refresh');
                } else {
                    redirect('/', 'refresh');
                }

            } else {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    exit();
                }
            }
        }

        $this->data['heading_title'] = $this->lang->line('add_site_title');
        $this->data['main'] = 'partial/completeprofile_partial';
        $this->data['form'] = 'account/update_password';
        $this->data['random_key'] = $this->input->get('random_key');
        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function choose_plan()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['plan_data'] = $this->Plan_model->listDisplayPlans();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('plan_id', $this->lang->line('entry_plan'), 'trim|required');
            $this->session->set_userdata('plan_id', $this->input->post('plan_id'));

            if ($this->form_validation->run() && $this->data['message'] == null) {
                redirect('/account/start', 'refresh');
            }
        }

        $this->data['plan_id'] = $this->session->userdata('plan_id') ? $this->session->userdata('plan_id') : null;
        $this->data['heading_title'] = $this->lang->line('add_site_title');
        $this->data['main'] = 'account_choose_plan';
        $this->data['form'] = 'account/choose_plan';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function setup_mobile()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('code', $this->lang->line('form_code'), 'trim|required');
            $code = $this->input->post('code');

            if (empty($code)) {
                $this->data['message'] = 'Please provide an authorization code';
            }

            $code_generated = $this->session->userdata('verify-code');
            if (empty($code_generated)) {
                $this->data['message'] = 'Please make a request to generate authorization code first';
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                if ($code_generated == $code) {
                    $mobile = $this->session->userdata('verify-mobile');
                    $this->User_model->updateMobile($this->User_model->getClientId(), $mobile);
                    $this->session->unset_userdata('verify-mobile');
                    $this->session->unset_userdata('verify-code');
                    redirect('.', 'refresh');
                } else {
                    $this->data['message'] = 'You have inserted an invalid authorization code';
                }
            }
        }

        $this->data['heading_title'] = $this->lang->line('setup_mobile_title');
        $this->data['main'] = 'account_mobile';
        $this->data['form'] = 'account/setup_mobile';

        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function request_code()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('phone_number', $this->lang->line('form_phone_number'), 'trim|required');
            $mobile = $this->input->post('phone_number');

            $c = $this->User_model->usedMobile($mobile);
            if ($c) {
                $this->data['message'] = 'This number has already been used for sign-up';
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $code = get_random_code(5, false, false, true);
                $this->session->set_userdata('verify-mobile', $mobile);
                $this->session->set_userdata('verify-code', $code);
                $ret = $this->sendSMS($mobile, 'Your Playbasis authorization code is: ' . $code . '');
                if ($ret) {
                    echo json_encode(array('status' => 'success', 'message' => 'Authorization code has been sent.'));
                } else {
                    echo json_encode(array('status' => 'failure', 'message' => 'There is a problem sending an SMS.'));
                }
            } else {
                $message = 'Mobile phone number is required.';
                if ($this->data['message']) {
                    $message = $this->data['message'];
                }
                echo json_encode(array('status' => 'failure', 'message' => $message));
            }
        } else {
            echo json_encode(array('status' => 'failure', 'message' => 'Only POST request is supported.'));
        }
        exit();
    }

    public function survey()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('business_sector', $this->lang->line('text_business_sector'),
                'trim|required');
            $this->form_validation->set_rules('feature', $this->lang->line('text_feature'), 'required');
            $this->form_validation->set_rules('objective', $this->lang->line('text_objective'), 'required');

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $this->Client_model->setSurveyData($this->User_model->getClientId(), $this->input->post());
                echo json_encode(array('status' => 'success', 'message' => 'Survey has been saved.'));
            } else {
                echo json_encode(array('status' => 'failure', 'message' => $this->data['message']));
            }
        } else {
            echo json_encode(array('status' => 'failure', 'message' => 'Only POST request is supported.'));
        }
        exit();
    }

    public function is_survey()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $result = $this->Client_model->isSurveyData($this->User_model->getClientId());
        echo json_encode(array('status' => 'success', 'message' => $result));
        exit();
    }

    private function sendSMS($to, $message)
    {
        $this->config->load("twilio", true);
        $config = $this->config->item('twilio');
        $this->load->library('twilio/twiliomini', $config);
        $from = isset($config['name']) ? $config['name'] : $config['number'];
        $response = $this->twiliomini->sms($from, $to, $message);
        $this->Sms_model->log($this->User_model->getClientId(), $this->User_model->getSiteId(), 'sys', $from, $to,
            $message, $response);
        return $response && !$response->IsError;
    }

//  public function start() {
//
//      if(!$this->validateAccess()){
//          echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
//          die();
//      }
//
//      $this->data['meta_description'] = $this->lang->line('meta_description');
//      $this->data['title'] = $this->lang->line('title');
//      $this->data['text_no_results'] = $this->lang->line('text_no_results');
//
//      $this->data['plan_data'] = $this->Plan_model->listDisplayPlans();
//
//      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//          $this->data['message'] = null;
//
//          $site = $this->session->userdata('site');
//          $plan_id = $this->session->userdata('plan_id');
//          if (empty($site)) $this->data['message'] = 'Invalid site';
//          if (empty($plan_id)) $this->data['message'] = 'Invalid plan';
//
//          if($this->data['message'] == null){
//              $site = $this->session->userdata('site');
//              $plan_id = new MongoId($this->session->userdata('plan_id'));
//              $client_id = $this->User_model->getClientId();
//
//              /* add domain in playbasis_client_site */
//              $site_id = $this->Domain_model->addDomain(array(
//                  'client_id' => $this->User_model->getClientId(),
//                  'domain_name' => $site,
//                  'site_name' => $site
//              ));
//
//              /* bind plan to client in playbasis_permission */
//              $this->Client_model->addPlanToPermission(array(
//                  'client_id' => $client_id->{'$id'},
//                  'plan_id' => $plan_id->{'$id'},
//                  'site_id' => $site_id->{'$id'},
//              ));
//
//              /* populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client */
//              $another_data['domain_value'] = array(
//                  'site_id' => $site_id,
//                  'status' => true
//              );
//              $this->Client_model->editClientPlan($client_id, $plan_id, $another_data); // [6] finally, populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client
//
//              /* reset site_id, so that we con't have to force the user to log out */
//              $site_id = $this->User_model->fetchSiteId($client_id);
//              $this->User_model->updateSiteId($site_id);
//              redirect('/account', 'refresh');
//          }
//      }
//
//      $this->data['plan_id'] = $this->session->userdata('plan_id') ? $this->session->userdata('plan_id') : null;
//      $this->data['heading_title'] = $this->lang->line('start_title');
//      $this->data['main'] = 'account_start';
//      $this->data['form'] = 'account/start';
//
//      $this->load->vars($this->data);
//      $this->render_page('template');
//  }

    private function find_next_billing_date_of($date_billing, $date_as_of)
    {
        $current = $date_billing;
        while ($current < $date_as_of) {
            $current = strtotime("+1 month", $current);
        }
        return $current;
    }

    private function find_diff_in_months($from, $to)
    {
        return intval($this->find_diff_in_fmt($from, $to, '%r%m'));
    }

    private function find_diff_in_days($from, $to)
    {
        return intval($this->find_diff_in_fmt($from, $to, '%r%a'));
    }

    private function find_diff_in_fmt($from, $to, $fmt)
    {
        $_from = new DateTime(date("Y-m-d", $from));
        $_to = new DateTime(date("Y-m-d", $to));
        $interval = $_from->diff($_to);
        return $interval->format($fmt);
    }

    private function check_valid_payment($client)
    {
        $date_start = array_key_exists('date_start',
            $client) && !empty($client['date_start']) ? $client['date_start']->sec : null;
        $date_expire = array_key_exists('date_expire',
            $client) && !empty($client['date_expire']) ? $client['date_expire']->sec : null;
        $t = time();
        return ($date_start ? $date_start <= $t : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET) && ($date_expire ? $t <= $date_expire : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET);
    }

    private function check_valid_payment_channel($channel)
    {
        return in_array($channel, array(PAYMENT_CHANNEL_PAYPAL, PAYMENT_CHANNEL_STRIPE));
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        if ($this->User_model->hasPermission('access', 'account')) {
            return true;
        } else {
            return false;
        }
    }
}

?>
