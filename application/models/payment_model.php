<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}

	public function processVerifiedIPN($channel, $client_id, $plan_id, $POST) {
	    $this->set_site_mongodb($this->session->userdata('site_id'));

		/* [1] validate client */

		/* find details of the client */
		$this->mongo_db->where(array('_id' => $client_id));
		$clients = $this->mongo_db->get('playbasis_client');
		$client = $clients ? $clients[0] : null;

		/* find details of the subscribed plan of the client */
		$this->mongo_db->where('client_id', $client_id);
		$this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
		$this->mongo_db->limit(1);
		$results = $this->mongo_db->get('playbasis_permission');
		$myplan_id = $results ? $results[0]['plan_id'] : null;
		$myplan = null;
		if ($myplan_id) {
			$this->mongo_db->where(array('_id' => $plan_id));
			$plans = $this->mongo_db->get('playbasis_plan');
			$myplan = $plans ? $plans[0] : null;
		}

		/* process */
		$d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
		switch ($POST['txn_type']) {
		case PAYPAL_TXN_TYPE_SUBSCR_SIGNUP:
			/* set "subscr_id" and "date_billing" on playbasis_client */
			$trial_days = array_key_exists('limit_others', $myplan) && array_key_exists('trial', $myplan['limit_others']) ? $myplan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
			$date_billing = strtotime("+".$trial_days." day", time());
			$this->mongo_db->where(array(
				'_id' => $client_id,
			));
			$this->mongo_db->set('subscr_id', $POST['subscr_id']);
			$this->mongo_db->set('plan_id', $plan_id);
			$this->mongo_db->set('date_billing', new MongoDate($date_billing));
			$this->mongo_db->set('date_modified', $d);
			$this->mongo_db->update('playbasis_client');
			break;
		case PAYPAL_TXN_TYPE_SUBSCR_PAYMENT:
			/* check if we already process this txn_id */
			$this->mongo_db->where(array('txn_id' => $POST['txn_id']));
			if ($this->mongo_db->count('playbasis_payment_log') === 0) {
				/* insert into payment log */
				$this->mongo_db->insert('playbasis_payment_log', array(
					'channel' => $channel,
					'client_id' => $client_id,
					'plan_id' => $plan_id,
					'status' => $POST['payment_status'],
					'amount' => $POST['mc_gross'],
					'currency' => $POST['mc_currency'],
					'subscr_id' => $POST['subscr_id'],
					'txn_id' => $POST['txn_id'],
					'ipn_track_id' => $POST['ipn_track_id'],
					'date_added' => $d,
					'date_modified' => $d,
				));

				/* check if status is 'Completed' */
				if ($POST['payment_status'] == 'Completed') {
					/* adjust date_start & date_expire if the transaction is finalized */
					$date_start = time();
					$date_expire = strtotime("+".(30+GRACE_PERIOD_IN_DAYS)." day", time());
					$this->mongo_db->where(array(
						'_id' => $client_id,
					));
					$this->mongo_db->set('date_start', new MongoDate($date_start));
					$this->mongo_db->set('date_expire', new MongoDate($date_expire));
					$this->mongo_db->set('date_modified', $d);
					$this->mongo_db->update('playbasis_client');

					/* check if plan has been changed */
					if ($myplan_id != $plan_id) {
						/* bind new plan to the client */
						$data_insert = array(
							'plan_id' => $plan_id,
							'client_id' => $client_id,
							'site_id' => null,
							'date_added' => $d,
							'date_modified' => $d,
						);
						$this->mongo_db->insert('playbasis_permission', $data_insert);

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
				}

				/* send email to the user notifying the payment amount and status */
				// TODO:
			}
			break;
		default:
			log_message('error', 'Unsupported PayPal txn_type: '.$POST['txn_type']);
			break;
		}
	}
}
?>
