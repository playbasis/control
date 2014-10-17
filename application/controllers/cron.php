<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('EMAIL_FROM', 'info@playbasis.com');
define('EMAIL_MAX_SENT', 3);
define('DAYS_TO_BECOME_INACTIVE', 30);
define('DAYS_TO_SEND_ANOTHER_EMAIL', 7);
define('PERCENTAGE_TO_ALERT_USAGE_NEAR_LIMIT', 0.9);
define('ACCOUNT_HAS_TO_BE_REGISTERED_AT_LEAST_DAYS', 30);
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
		$this->load->library('parser');
	}

	public function notifyInactiveClients() {
		$today = time();
		$refDate = strtotime("-".ACCOUNT_HAS_TO_BE_REGISTERED_AT_LEAST_DAYS." day", $today);
		$clients = $this->client_model->listAllActiveClients($refDate);
		if ($clients) foreach ($clients as $client) {
			$client_id = $client['_id'];
			$latest_activity = $this->service_model->findLatestAPIactivity($client_id);
			/* check status */
			if (!$latest_activity || $this->utility->find_diff_in_days($latest_activity->sec, time()) >= DAYS_TO_BECOME_INACTIVE) {
				$email = $client['email'];
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL) && $this->email_model->countSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id) < EMAIL_MAX_SENT) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Playbasis Wants to Hear from You';
					$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'It has been a while since you subscribed our gamification services. Thousands of people are enjoying gamification with Playbasis every day. Here are a few ideas that you can implement on your web sit using gamification and improve the level of your customers\' engagement your business needs.<br><br>- Reward your users when they like, comment or read something on your page.<br>-Give your users a badge to reward them when they complete several actions you want them to do.<br>- Build a quest to ask your users to achieve a goal.<br><br>The more gamification you propose to your users, the easier it is to engage and let them want to hear from you.'), true);
					$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id, null, $response, $from, $to, $subject, $html);
				}
			}
		}
	}

	public function notifyFreeActiveClientsToSubscribe() {
		$today = time();
		$refDate = strtotime("-".ACCOUNT_HAS_TO_BE_REGISTERED_AT_LEAST_DAYS." day", $today);
		$clients = $this->client_model->listAllActiveClients($refDate);
		$list_client_ids = array();
		if ($clients) foreach ($clients as $client) {
			$list_client_ids[] = $client['_id'];
		}
		$results = $this->service_model->listActiveClientsUsingAPI(RECENT_DAYS_SENDING_API_TO_BE_CONSIDERED_ACTIVE, $list_client_ids);
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
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times  */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL) && $this->email_model->countSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id) < EMAIL_MAX_SENT) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Enjoy using Playbasis - Keep it Going';
					$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'We hope you have enjoyed using Playbasis so far. You may want to select a plan that offers you more features and options to go further and engage your users even better.<br><br>Check out the <a href="http://playbasis.com/plans.html">pricing page</a> to see our plans and options and pick up the one that match with your number of users and your need.<br><br>If you have any questions about our plans or features, please contact us at support@playbasis.com.'), true);
					$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_FREE_ACTIVE_CLIENTS, $client_id, null, $response, $from, $to, $subject, $html);
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
$email = 'pechpras@playbasis.com';
							$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE.$field, $client_id, $site_id);
							/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times  */
							if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL) && $this->email_model->countSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id) < EMAIL_MAX_SENT) {
								/* email */
								$from = EMAIL_FROM;
								$to = $email;
								$subject = '[Playbasis] Almost Reach the Limit Usage';
								$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'You almost reach the limit: '.$usage.' of '.$usage_limit.' for the feature '.$type.'/'.$field.'.<br>We may suggest you to subscribe a more advanced plan to extend the limit of this feature in order to better match your need.<br><br>If you have any questions about our plans or features, please contact us at support@playbasis.com.'), true);
								$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
								$this->email_model->log(EMAIL_TYPE_NOTIFY_NEAR_LIMIT_USAGE.$field, $client_id, $site_id, $response, $from, $to, $subject, $html);
							}
						}
					}
				}
			}
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
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_REMIND_TO_SETUP_SUBSCRIPTION, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times  */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL)) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Get Started with Playbasis';
					$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'You\'re just a step away from starting using Playbasis gamification services and improve the level of your customers\' engagement. To do this, we need your billing detail in order to activate your API.<br><br>You can update your billing information by logging in to your administration interface and by clicking on the "Account" menu on the left side.<br><br>If you have any questions about our plans or features, please contact us at support@playbasis.com.'), true);
					$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
					$this->email_model->log(EMAIL_TYPE_REMIND_TO_SETUP_SUBSCRIPTION, $client_id, null, $response, $from, $to, $subject, $html);
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
$email = 'pechpras@playbasis.com';
					$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_REMIND_END_OF_TRIAL_PERIOD, $client_id);
					/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times  */
					if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL) && $this->email_model->countSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id) < EMAIL_MAX_SENT) {
						/* email */
						$from = EMAIL_FROM;
						$to = $email;
						$subject = '[Playbasis] Trial Period Ended';
						$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'Your trial period has just ended on '.date('l, F d, Y', time()).'<br>You enter now in your regular plan billing period.<br>Your payment date will on be on '.date('l, F d, Y', $date_billing).'.<br><br>If you have any questions about our plans or features, please contact us at support@playbasis.com.'), true);
						$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
						$this->email_model->log(EMAIL_TYPE_REMIND_END_OF_TRIAL_PERIOD, $client_id, null, $response, $from, $to, $subject, $html);
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
$email = 'pechpras@playbasis.com';
				$latest_sent = $this->email_model->findLatestSent(EMAIL_TYPE_NOTIFY_API_ACCESS_SHUTDOWN_PERIOD, $client_id);
				/* email should: (1) not be in black list and (2) we skip if we just recently sent this type of email, and (3) be sent more than 3 times  */
				if (!$this->email_model->isEmailInBlackList($email) && (!$latest_sent || $this->utility->find_diff_in_days($latest_sent->sec, time()) >= DAYS_TO_SEND_ANOTHER_EMAIL) && $this->email_model->countSent(EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS, $client_id) < EMAIL_MAX_SENT) {
					/* email */
					$from = EMAIL_FROM;
					$to = $email;
					$subject = '[Playbasis] Shutdown Your API Access';
					$html = $this->parser->parse('message.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'message' => 'This is to let you know that your access to our API has been shut down.<br><br>If you have any questions about our plans or features, please contact us at support@playbasis.com.'), true);
					$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
					$this->email_model->log(EMAIL_TYPE_NOTIFY_API_ACCESS_SHUTDOWN_PERIOD, $client_id, null, $response, $from, $to, $subject, $html);
				}
			}
		}
	}
}
?>