<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('EMAIL_FROM', 'info@playbasis.com');
define('DAYS_TO_BECOME_INACTIVE', 15);
define('DAYS_TO_SEND_ANOTHER_EMAIL', 7);
define('PERCENTAGE_TO_ALERT_USAGE_NEAR_LIMIT', 0.9);
define('RECENT_DAYS_SENDING_API_TO_BE_CONSIDERED_ACTIVE', 7);
class Cron extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('client_model');
		$this->load->model('email_model');
		$this->load->model('plan_model');
		$this->load->model('service_model');
		$this->load->model('tool/utility', 'utility');
	}

	public function notifyInactiveClients() {
		$clients = $this->client_model->listAllActiveClients();
		if ($clients) foreach ($clients as $client) {
			$client_id = $client['_id'];
			$latest_activity = $this->service_model->findLatestAPIactivity($client_id);
			/* check status */
			if (!$latest_activity || $this->utility()->find_diff_in_days($latest_activity->sec, time()) >= DAYS_TO_BECOME_INACTIVE) {
				$email = $client['email'];
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility()->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Notify Inactive Clients';
					$message = 'You are currently not using our API ['.$client_id.']';
					$response = $this->utility->email($from, $to, $subject, $message);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id, null, $response, $from, $to, $subject, $message);
				}
			}
		}
	}

	public function notifyFreeActiveClientsToSubscribe() {
		$results = $this->service_model->listActiveClientsUsingAPI(RECENT_DAYS_SENDING_API_TO_BE_CONSIDERED_ACTIVE);
		if ($results) foreach ($results as $client_id) {
			if (!$client_id) continue;

			/* get client detail */
			$client = $this->client_model->getById($client_id);

			/* get current associated plan of the client */
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id);
			$myplan = $this->plan_model->getPlanById($myplan_id);
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;
			if ($free_flag) {
				$email = $client['email'];
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility()->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Notify Free Active Clients to Subscribe';
					$message = 'Enjoy using our Playbasis API? Please take a look into our subscription plan ['.$client_id.']';
					$response = $this->utility->email($from, $to, $subject, $message);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS, $client_id, null, $response, $from, $to, $subject, $message);
				}
			}
		}
	}

	public function notifyNearLimitUsage() {
		$sites = $this->client_model->listAllActivesSites();
		if ($sites) foreach ($sites as $site) {
			$client_id = $site['client_id'];
			$site_id = $site['_id'];

			/* get client detail */
			$client = $this->client_model->getById($client_id);

			/* get current associated plan of the client */
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id);
			$myplan = $this->plan_model->getPlanById($myplan_id);
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;

			/* get the value of limit imposed by the plan */
			$type = 'requests';
			$limit = $this->client_model->getPlanLimitById($site_id, $myplan_id, $type);
			if ($limit) {
				$clientDate = ($free_flag ? $this->client_model->getFreeClientStartEndDate($client_id) : $this->client_model->getClientStartEndDate($client_id));
				foreach ($limit as $field => $usage_limit) {
					/* find current usage for the field */
					$usage = $this->client_model->getPermissionUsage($client_id, $site_id, $type, $field, $clientDate);

					/* check usage */
					if ($usage < $usage_limit && $usage >= PERCENTAGE_TO_ALERT_USAGE_NEAR_LIMIT*$usage_limit) { // this will not send if the client has already go over the limit
						$email = $client['email'];
$email = 'pechpras@playbasis.com';
						$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE.$field, $client_id, $site_id);
						/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
						if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility()->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
							/* email */
							$from = EMAIL_FROM;
							$to = $email;
							$subject = '[Playbasis] Notify Near Limit Usage';
							$message = 'Your API usage for "'.$type.'.'.$field.'" is approaching the limit ['.$client_id.']'.' ['.$site_id.']';
							$response = $this->utility->email($from, $to, $subject, $message);
							$this->email_model->log(EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE.$field, $client_id, $site_id, $response, $from, $to, $subject, $message);
						}
					}
				}
			}
		}
	}

	public function remindClientsToSetupSubscription() {
		// $plan['paid_flag'] && !$client['date_billing']
	}

	public function remindClientsEndOfTrialPeriod() {
		// time() >= date_billing, but no greater than 1 month
	}

	public function notifyClientsEndOfGracePeriod() {
		// time() >= date_expire + 5 days, but no greater than 1 month
	}
}
?>