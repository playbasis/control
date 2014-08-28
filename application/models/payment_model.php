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
		$client = $this->getClientById($client_id);

		/* find details of the current plan of the client */
		$myplan_id = $this->getPlanIdByClientId($client_id);
		$myplan = $myplan_id ? $this->getPlanById($myplan_id) : null;
		if (!array_key_exists('price', $myplan)) {
			$myplan['price'] = DEFAULT_PLAN_PRICE;
		}

		/* find details of the subscribed plan of the client */
		$plan = $this->getPlanById($plan_id);
		if (!array_key_exists('price', $plan)) {
			$plan['price'] = DEFAULT_PLAN_PRICE;
		}

		/* process PayPal IPN message differently according to 'txn_type' */
		switch ($POST['txn_type']) {
		case PAYPAL_TXN_TYPE_SUBSCR_SIGNUP:
			$this->setDateBilling($client_id, $plan, $POST['subscr_id']);
			if ($myplan['price'] <= 0) { // change the plan if current plan is free
				$this->changePlan($client_id, $myplan_id, $plan_id);
			}
			break;
		case PAYPAL_TXN_TYPE_SUBSCR_MODIFY:
			break;
		case PAYPAL_TXN_TYPE_SUBSCR_PAYMENT:
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

				/* send email to the user notifying the payment amount and status */
				// TODO:

				/* check payment status */
				switch ($POST['payment_status']) {
				case PAYPAL_PAYMENT_STATUS_COMPLETED:
					/* adjust billing period, 'date_start' and 'date_expire', to allow client to use our API */
					$this->setDateStartAndDateExpire($client_id, GRACE_PERIOD_IN_DAYS);

					/* check if the plan has been changed */
					if ($myplan_id != $plan_id && $amount == $myplan['price']) {
						/* enable new plan immediately after we charge for the old price */
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

	private function getPlanIdByClientId($client_id) {
		$permission = $this->getLatestPermissionByClientId($client_id);
		return $permission ? $permission['plan_id'] : null;
	}

	private function getLatestPermissionByClientId($client_id) {
		$this->mongo_db->where('client_id', $client_id);
		$this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
		$this->mongo_db->limit(1);
		$results = $this->mongo_db->get('playbasis_permission');
		return $results ? $results[0] : null;
	}

	private function getById($id, $collection) {
		$this->mongo_db->where(array('_id' => $id));
		$results = $this->mongo_db->get($collection);
		return $results ? $results[0] : null;
	}

	private function getClientById($client_id) {
		return $this->getById($client_id, 'playbasis_client');
	}

	private function getPlanById($plan_id) {
		return $this->getById($plan_id, 'playbasis_plan');
	}

	private function getSytemRewardById($reward_id) {
		return $this->getById($reward_id, 'playbasis_reward');
	}

	private function getSytemFeatureById($feature_id) {
		return $this->getById($feature_id, 'playbasis_feature');
	}

	private function getSytemActionById($action_id) {
		return $this->getById($action_id, 'playbasis_action');
	}

	private function getSytemJigsawById($jigsaw_id) {
		return $this->getById($jigsaw_id, 'playbasis_jigsaw');
	}

	private function listSitesByClientId($client_id) {
		$this->mongo_db->where(array('client_id' => $client_id));
		return $this->mongo_db->get('playbasis_client_site');
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
		/* get detail of the destination plan */
		$plan = $to_plan_id ? $this->getPlanById($to_plan_id) : null;
		if (!$plan) return;

		/* associate all client's sites to a new plan */
		$this->mongo_db->where(array(
			'client_id' => $client_id,
			'plan_id' => $from_plan_id,
		));
		$this->mongo_db->set('plan_id', $plan['_id']);
		$this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
		$this->mongo_db->update('playbasis_permission');

		/* loop over all sites of the clients */
		$sites = $this->listSitesByClientId($client_id);
		if ($sites) foreach ($sites as $site) {
			$site_id = $site['_id'];

			/* populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client */
			$this->copyRewardToClient($client_id, $site_id, $plan);
			$this->copyFeaturedToClient($client_id, $site_id, $plan);
			$this->copyActionToClient($client_id, $site_id, $plan);
			$this->copyJigsawToClient($client_id, $site_id, $plan);
		}
	}

	private function copyRewardToClient($client_id, $site_id, $plan) {
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

	private function copyFeaturedToClient($client_id, $site_id, $plan) {
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

	private function copyActionToClient($client_id, $site_id, $plan) {
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

	private function copyJigsawToClient($client_id, $site_id, $plan) {
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
