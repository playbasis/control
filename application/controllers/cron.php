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
			if (!$latest_activity || $this->utility->find_diff_in_days($latest_activity->sec, time()) >= DAYS_TO_BECOME_INACTIVE) {
				$email = $client['email'];
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
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
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id); if (!$myplan_id) continue;
			$myplan = $this->plan_model->getPlanById($myplan_id); if (!$myplan) continue;
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;

			if ($free_flag) {
				$email = $client['email'];
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
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
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id); if (!$myplan_id) continue;
			$myplan = $this->plan_model->getPlanById($myplan_id); if (!$myplan) continue;
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;

			/* check "limit_requests", "limit_notifications" specified by the plan */
			foreach (array('requests', 'notifications') as $type) {
				$limit = $this->client_model->getPlanLimitById($site_id, $myplan_id, $type);
				if ($limit) {
					$clientDate = ($free_flag ? $this->client_model->getFreeClientStartEndDate($client_id) : $this->client_model->getClientStartEndDate($client_id));
					foreach ($limit as $field => $usage_limit) {
						/* find current usage for the field */
						$usage = $this->client_model->getPermissionUsage($client_id, $site_id, $type, $field, $clientDate);

						/* check usage */
						if ($usage < $usage_limit && $usage >= PERCENTAGE_TO_ALERT_USAGE_NEAR_LIMIT*$usage_limit) { // this will not send if the client has already go over the limit
							$email = $client['email'];
							$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE.$field, $client_id, $site_id);
							/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
							if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
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

			/* check "limit_others" */
			// TODO:
		}
	}

	public function remindClientsToSetupSubscription() {
		$clients = $this->client_model->listClientsWithoutDateBilling();
		if ($clients) foreach ($clients as $client) {

			$client_id = $client['_id'];

			/* get current associated plan of the client */
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id); if (!$myplan_id) continue;
			$myplan = $this->plan_model->getPlanById($myplan_id); if (!$myplan) continue;
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;
			$paid_flag = !$free_flag;

			if ($paid_flag) {
				$email = $client['email'];
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_REMIND_TO_SETUP_SUBSCRIPTION, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Reminder to Finish Setting Up Subscription';
					$message = 'You have to finish setting up the subscription before you can really start using our API ['.$client_id.']. Your plan ID ['.$myplan_id.']';
					$response = $this->utility->email($from, $to, $subject, $message);
					$this->email_model->log(EMAIL_TYPE_REMIND_TO_SETUP_SUBSCRIPTION, $client_id, null, $response, $from, $to, $subject, $message);
				}
			}
		}
	}

	public function remindClientsEndOfTrialPeriod() {
		$clients = $this->client_model->listClientsWithDateBilling();
		if ($clients) foreach ($clients as $client) {
			if (!$client['date_billing']) continue;

			$client_id = $client['_id'];

			/* get current associated plan of the client */
			$myplan_id = $this->plan_model->getPlanIdByClientId($client_id); if (!$myplan_id) continue;
			$myplan = $this->plan_model->getPlanById($myplan_id); if (!$myplan) continue;
			if (!array_key_exists('price', $myplan)) {
				$myplan['price'] = DEFAULT_PLAN_PRICE;
			}
			$free_flag = $myplan['price'] <= 0;
			$paid_flag = !$free_flag;
			$trial_days = array_key_exists('limit_others', $myplan) && array_key_exists('trial', $myplan['limit_others']) ? $myplan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;

			if ($paid_flag && $trial_days > 0) { // we proceed only if the plan has been set with trial days > 0
				/* check that it has passed the end of trial period */
				$date_billing = $client['date_billing']->sec;
				$today = time();

				if ($today >= $date_billing && $this->utility->find_diff_in_days($date_billing, $today) == 1) {
					$email = $client['email'];
					$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_REMIND_END_OF_TRIAL_PERIOD, $client_id);
					/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
					if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
						/* email */
						$from = EMAIL_FROM;
						$to = $email;
						$subject = '[Playbasis] Remind End of Trial Period';
						$message = 'This is to let you know that your trial period has ended ['.$client_id.']. Your plan ID ['.$myplan_id.']';
						$response = $this->utility->email($from, $to, $subject, $message);
						$this->email_model->log(EMAIL_TYPE_REMIND_END_OF_TRIAL_PERIOD, $client_id, null, $response, $from, $to, $subject, $message);
					}
				}
			}
		}
	}

	public function notifyClientsShutdownAPI() {
		$today = time();
		$clients = $this->client_model->listExpiredClients($today);
		if ($clients) foreach ($clients as $client) {

			$client_id = $client['_id'];

			/* check that it has passed the end of trial period */
			$date_expire = $client['date_expire']->sec;

			if ($today >= $date_expire && $this->utility->find_diff_in_days($date_expire, $today) == GRACE_PERIOD_IN_DAYS+1) {
				$email = $client['email'];
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_API_ACCESS_SHUTDOWN_PERIOD, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Alert! Shutdown Your Access to Our API';
					$message = 'This is to let you know that your access to our API has been shut down ['.$client_id.']';
					$response = $this->utility->email($from, $to, $subject, $message);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_API_ACCESS_SHUTDOWN_PERIOD, $client_id, null, $response, $from, $to, $subject, $message);
				}
			}
		}
	}
}
?>