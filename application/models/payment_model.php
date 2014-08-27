<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}

	public function processVerifiedIPN($client_id, $plan_id, $POST) {
	    $this->set_site_mongodb($this->session->userdata('site_id'));

		/* find details of the client */
		$this->mongo_db->where(array('_id' => $client_id));
		$clients = $this->mongo_db->get('playbasis_client');
		$client = $clients ? $clients[0] : null;

		/* find details of the subscribed plan of the client */
		$result = $this->getClientById($client_id);
		$myplan_id = $result ? $result['plan_id'] : null;
		$myplan = $myplan_id ? $this->getPlanById($myplan_id) : null;

		/* process PayPal IPN message differently according to 'txn_type' */
		switch ($POST['txn_type']) {
		case PAYPAL_TXN_TYPE_SUBSCR_SIGNUP:
			$this->setDateBilling($client, $myplan, $POST['subscr_id']);
			break;
		case PAYPAL_TXN_TYPE_SUBSCR_PAYMENT:
			/* check if we already process this 'txn_id' */
			if (!$this->hasAlreadyProcessed($POST['txn_id'])) { // skip possible duplicate IPN messages
				/* insert into payment log */
				$this->insertPaymentLog($client_id, $plan_id, array(
					'channel' => PAYMENT_CHANNEL_PAYPAL,
					'status' => $POST['payment_status'],
					'amount' => $POST['mc_gross'],
					'currency' => $POST['mc_currency'],
					'txn_id' => $POST['txn_id'],
					'ipn_track_id' => $POST['ipn_track_id'],
				));

				/* send email to the user notifying the payment amount and status */
				// TODO:

				/* check payment status */
				switch ($POST['payment_status']) {
				case PAYPAL_PAYMENT_STATUS_COMPLETED:
					/* adjust billing period, 'date_start' and 'date_expire', to allow client to use our API */
					$this->setDateStartAndDateExpire($client_id, GRACE_PERIOD_IN_DAYS);

					/* check if the plan has been changed */
					if ($myplan_id != $plan_id) {
						$this->changePlan($client_id, $myplan_id, $plan_id);
						log_message('info', 'Client '.$client_id.' has changed the plan from '.$myplan_id.' to '.$plan_id);
					}
					break;
				}
			}
			break;
		default:
			log_message('error', 'Unsupported PayPal txn_type: '.$POST['txn_type']);
			return false;
		}
		return true;
	}

	private function getClientById($client_id) {
		$this->mongo_db->where('client_id', $client_id);
		$this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
		$this->mongo_db->limit(1);
		$results = $this->mongo_db->get('playbasis_permission');
		return $results ? $results[0] : null;
	}

	private function getPlanById($plan_id) {
		$this->mongo_db->where(array('_id' => $plan_id));
		$plans = $this->mongo_db->get('playbasis_plan');
		return $plans ? $plans[0] : null;
	}

	private function setDateBilling($client_id, $plan, $subscriber_id) {
		/* find number of trial days */
		$trial_days = array_key_exists('limit_others', $plan) && array_key_exists('trial', $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
		/* set billing date */
		$date_billing = strtotime("+".$trial_days." day", time());
		/* update billing in client's record */
		$this->mongo_db->where(array('_id' => $client_id));
		$this->mongo_db->set('subscr_id', $subscriber_id);
		$this->mongo_db->set('plan_id', $plan['_id']);
		$this->mongo_db->set('date_billing', new MongoDate($date_billing));
		$this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
		$this->mongo_db->update('playbasis_client');
	}

	private function setDateStartAndDateExpire($client_id, $grace_period_in_days) {
		$d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
		$today = time();
		$date_start = $today;
		$date_end = strtotime("+1 month", $date_start);
		$date_expire = strtotime("+".$grace_period_in_days." day", $date_end);
		$this->mongo_db->where(array('_id' => $client_id));
		$this->mongo_db->set('date_start', new MongoDate($date_start));
		$this->mongo_db->set('date_expire', new MongoDate($date_expire));
		$this->mongo_db->set('date_modified', $d);
		$this->mongo_db->update('playbasis_client');
	}

	private function changePlan($client_id, $from_plan_id, $to_plan_id) {
		/* associate all client's sites to a new plan */
		$this->mongo_db->where(array(
			'client_id' => $client_id,
			'plan_id' => $from_plan_id,
		));
		$this->mongo_db->set('plan_id', $to_plan_id);
		$this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
		$this->mongo_db->update('playbasis_permission');

		/* populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client */
		/*$data_filter = array(
			'client_id' => $client_id,
			'site_id' => null,
			'plan_id' => $plan_subscription['plan_id']->{'$id'},
			'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
			'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
		);
		$this->copyRewardToClient($data_filter);
		$this->copyFeaturedToClient($data_filter);
		$this->copyActionToClient($data_filter);
		$this->copyJigsawToClient($data_filter);*/
	}

	private function hasAlreadyProcessed($txn_id) {
		$this->mongo_db->where(array('txn_id' => $txn_id));
		return $this->mongo_db->count('playbasis_payment_log') > 0;
	}

	private function insertPaymentLog($client_id, $plan_id, $payment) {
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
