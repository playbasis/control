<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('MAX_EXECUTION_TIME', 0);

define('EMAIL_MAX_SENT', 3);
define('DAYS_TO_BECOME_INACTIVE', 30);
define('DAYS_TO_SEND_ANOTHER_EMAIL', 7);
define('PERCENTAGE_TO_ALERT_USAGE_NEAR_LIMIT', 0.9);
define('ACCOUNT_HAS_TO_BE_REGISTERED_AT_LEAST_DAYS', 30);
define('RECENT_DAYS_SENDING_API_TO_BE_CONSIDERED_ACTIVE', 7);
define('MONTHS_TO_STORE_IN_SERVICE_LOG', 3);
define('S3_BUCKET', 'elasticbeanstalk-ap-southeast-1-007834438823');
define('S3_FOLDER', 'log/playbasis_web_service_log');
define('DAYS_TO_BECOME_ACTIVE', 3);

define('ENERGY_UPDATER_THRESHOLD', 5);
define('LIMIT_PLAYERS_QUERY', 10000);

define('WIDGET_NUMBER_OF_CUSTOMERS', '166318-19e7a021-f8a7-42fe-be4e-7c51464706a7');
define('WIDGET_NUMBER_OF_ACTIVE_USAGE_CUSTOMERS', '166318-83c41412-4d61-4fd8-b42f-067b907d961e');
define('WIDGET_NUMBER_OF_ACTIVE_PAYING_CUSTOMERS', '166318-b0ca1250-c8f0-4084-a073-b5efecd7d07c');
define('WIDGET_TOP_COUNTRIES', '166318-6a706411-8061-4bb8-9291-5a2f03c66a67');
define('WIDGET_DAILY_REGISTRATION', '166318-27b2c04f-54a9-41d2-9e19-9b2f886e6121');
define('WIDGET_MONTHLY_REGISTRATION', '166318-c6af157c-6e47-41de-bc42-444a2bd540ab');
define('WIDGET_TOTAL_CUSTOMERS_TREND', '166318-d30dbd5f-b377-4128-acbb-4de8fa405e1a');
define('WIDGET_PERCENT_GROWTH', '166318-74b9a3ab-1a46-4811-b742-ebaf91244f6a');

class Cron extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('client_model');
		$this->load->model('player_model');
		$this->load->model('email_model');
		$this->load->model('plan_model');
		$this->load->model('service_model');
		$this->load->model('googles_model');
		$this->load->model('engine/jigsaw', 'jigsaw_model');
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
			if ($myplan_id == FREE_PLAN) {
				$email = $client['email'];
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
			$client_date = $this->client_model->getClientStartEndDate($client_id);
			$client_usage = $this->client_model->getClientSiteUsage($client_id, $site_id);
			$client_plan = $this->client_model->getPlanById($client_usage['plan_id']);
			$free_flag = !isset($client_plan['price']) || $client_plan['price'] <= 0;
			if ($free_flag) {
				$client_date = $this->client_model->adjustCurrentUsageDate($client_date['date_start']);
			}
			$client_data = array('date' => $client_date, 'usage' => $client_usage, 'plan' => $client_plan);

			/* check "limit_requests", "limit_notifications" specified by the plan */
			foreach (array('requests', 'notifications') as $type) {
				$limit = $this->client_model->getPlanLimitById($client_plan, $type);
				if ($limit) {
					foreach ($limit as $field => $usage_limit) {
						/* find current usage for the field */
						$usage = $this->client_model->getPermissionUsage($type, $field, $client_data);

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

	public function archiveWebServiceLog() {
		set_time_limit(0);
		$results = $this->service_model->archive(MONTHS_TO_STORE_IN_SERVICE_LOG, S3_BUCKET, S3_FOLDER);
		print('Total records archived = '.$results);
	}

	public function preComputeJigsawLog() {
		$from = $this->jigsaw_model->getLastCalculateFrequencyTime();
		$to = new MongoDate(strtotime(date('Y-m-d', time()).' 00:00:00'));
		$results = $this->jigsaw_model->calculateFrequency($from, $to);
		foreach ($results as $result) {
			$this->jigsaw_model->storeFrequency($result);
		}
	}

	public function processActionLog() {
		set_time_limit(MAX_EXECUTION_TIME);
		$start = $this->player_model->findLatestProcessActionLogTime();
		$cursor = $this->player_model->listActionLog($start ? $start[0]['date_added'] : null);
		while ($cursor->hasNext()) {
			try {
				$action = $cursor->getNext();
				$d = strtotime(date('Y-m-d', $action['date_modified']->sec));
				$this->player_model->computeDau($action, $d);
				$this->player_model->updateLatestProcessActionLogTime($action['date_modified']);
				$this->player_model->computeMau($action, $d);
			} catch (MongoCursorException $exception) {
				log_message('error', 'Error when processing processActionLog: '.$exception->getMessage());
				break;
			}
		}
	}

	public function pullFullContact() {
		$results = $this->player_model->findRecentPlayers(DAYS_TO_BECOME_ACTIVE);
		$emails = $this->player_model->findDistinctEmails($results);
		$emails = $this->player_model->findNewEmails($emails);
		foreach ($emails as $email) {
			print($email."\n");
			$resp = json_decode(file_get_contents(FULLCONTACT_API.'/v2/person.json?email='.$email.'&apiKey='.FULLCONTACT_API_KEY.'&webhookUrl='.str_replace('%s', urlsafe_b64encode($email), FULLCONTACT_CALLBACK_URL).'&webhookBody=json'));
			if (!($resp && isset($resp->status) && $resp->status == FULLCONTACT_REQUEST_WEBHOOK_ACCEPTED)) {
				print_r($resp);
			}
			usleep(1.0/FULLCONTACT_RATE_LIMIT*1000000);
		}
	}

	public function pullFullContactForDemo() {
		$results = $this->player_model->findPlayersBySiteId(new MongoId(DEMO_SITE_ID));
		$emails = array_map('index_email', $results);
		$emails = $this->player_model->findNewEmails($emails);
		foreach ($emails as $email) {
			print($email."\n");
			$resp = json_decode(file_get_contents(FULLCONTACT_API.'/v2/person.json?email='.$email.'&apiKey='.FULLCONTACT_API_KEY.'&webhookUrl='.str_replace('%s', urlsafe_b64encode($email), FULLCONTACT_CALLBACK_URL).'&webhookBody=json'));
			if (!($resp && isset($resp->status) && $resp->status == FULLCONTACT_REQUEST_WEBHOOK_ACCEPTED)) {
				print_r($resp);
			}
			usleep(1.0/FULLCONTACT_RATE_LIMIT*1000000);
		}
	}

	/* Improve quality of stored FullContact records by using Facebook/Twitter ID */
	public function improveFullContactForDemo() {
		$results = $this->player_model->findPlayersBySiteId(new MongoId(DEMO_SITE_ID));
		foreach ($results as $result) {
			print($result['email']."\n");
			print($result['cl_player_id']."\n");
			$idx = strpos($result['cl_player_id'], 'facebook');
			if ($idx !== false) {
				print(substr($result['cl_player_id'], 0, $idx)."\n");
			}
			$idx = strpos($result['cl_player_id'], 'twitter');
			if ($idx !== false) {
				print(substr($result['cl_player_id'], 0, $idx)."\n");
				print($result['username']."\n");
			}
			// email with 404 and it is either facebook or twitter
			// email with 200, no facebook and it is facebook
			// email with 200, no twitter and it is twitter
			// is there any case that email with 200, but has less social than what Playbasis actually has
		}
	}

	public function listClientRegistration() {
		$this->load->library('RestClient');
		$this->restclient->initialize(array('server' => GECKO_URL));

		/* init */
		set_time_limit(0);
		$m0 = date('Y-m-d', strtotime('first day of this month'));
		$m1 = date('Y-m-d', strtotime('first day of last month'));
		$m2 = date('Y-m-d', strtotime('first day of -2 month'));
		$csv1 = tempnam('/tmp', 'list-customers');
		$csv2 = tempnam('/tmp', 'registration-daily');
		$csv3 = tempnam('/tmp', 'registration-monthly');
		/* stat */
		$f_country = array();
		$c_active_usage = 0;
		$c_active_paying = 0;

		/* CSV1 */
		$fp = fopen($csv1, 'w');
		$clients = $this->client_model->listAllActiveClients();
		$cache_c = array();
		$cache_p = array();
		if ($clients) foreach ($clients as $client) {
			/* mobile & country */
			$mobile = explode(' ', $client['mobile']);
			$d_code = $mobile[0];
			if (!isset($cache_c[$d_code])) $cache_c[$d_code] = $this->service_model->findCountryByDialCode($mobile[0]);
			$country = $cache_c[$d_code];
			/* stat: country */
			if ($country) {
				if (!isset($f_country[$country])) $f_country[$country] = 0;
				$f_country[$country]++;
			}
			/* plan */
			$record = $this->client_model->getLatestPermissionByClientId($client['_id']);
			$plan_id = $record['plan_id'];
			if (!isset($cache_p[$plan_id.""])) $cache_p[$plan_id.""] = $this->client_model->getPlanById($plan_id);
			$plan = $cache_p[$plan_id.""];
			$plan['price'] = isset($plan['price']) ? $plan['price'] : DEFAULT_PLAN_PRICE;
			$active_paying = $plan['price'] > 0;
			/* stat: active paying */
			if ($active_paying) $c_active_paying++;
			$c_m2 = $this->service_model->countApiUsage($client['_id'], $m2, $m1);
			$c_m1 = $this->service_model->countApiUsage($client['_id'], $m1, $m0);
			$c_m0 = $this->service_model->countApiUsage($client['_id'], $m0);
			/* stat: active usage */
			if ($c_m1 || $c_m0) $c_active_usage++;
			$data = array(
				$client['_id']."",
				$client['first_name'],
				$client['last_name'],
				$client['company'],
				$client['email'],
				$client['mobile'],
				$country,
				datetimeMongotoReadable($client['date_added']), // registration date
				isset($client['paying_ever']) ? 1 : 0, // paying ever
				$active_paying, // active paying
				$plan['name'],
				$plan['price'],
				datetimeMongotoReadable($record['date_modified']), // plan subscription date
				$c_m2, // API usage for M-2
				$c_m1, // API usage for M-1
				$c_m0, // API usage for M
			);
			fputcsv($fp, $data);
		}
		fclose($fp);

		/* stat: customers */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'item' => array(
				array(
					'value' => count($clients),
					'text' => 'Total customers',
				)
			),
		));
		$result = $this->restclient->post(WIDGET_NUMBER_OF_CUSTOMERS, json_encode($data), 'json');

		/* stat: active usage customers */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'item' => array(
				array(
					'value' => $c_active_usage,
					'text' => 'Total active customers',
				)
			),
		));
		$result = $this->restclient->post(WIDGET_NUMBER_OF_ACTIVE_USAGE_CUSTOMERS, json_encode($data), 'json');

		/* stat: active paying customers */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'item' => array(
				array(
					'value' => $c_active_paying,
					'text' => 'Total paying customers',
				)
			),
		));
		$result = $this->restclient->post(WIDGET_NUMBER_OF_ACTIVE_PAYING_CUSTOMERS, json_encode($data), 'json');

		/* stat: top countries */
		$items = array();
		arsort($f_country);
		foreach ($f_country as $country => $f) {
			$items[] = array('label' => $country, 'value' => $f);
		}
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'items' => $items,
		));
		$result = $this->restclient->post(WIDGET_TOP_COUNTRIES, json_encode($data), 'json');

		/* calculate registration frequency */
		$f_daily = array();
		$f_monthly = array();
		foreach ($clients as $client) {
			$d = $client['date_added']->sec;
			$k1 = date('Y-m-d', $d);
			$k2 = date('Y-m', $d);
			if (!isset($f_daily[$k1])) $f_daily[$k1] = 0;
			$f_daily[$k1]++;
			if (!isset($f_monthly[$k2])) $f_monthly[$k2] = 0;
			$f_monthly[$k2]++;
		}

		/* CSV 2 */
		$fp = fopen($csv2, 'w');
		$cur = min(min(array_keys($f_daily)), '2013-01-01');
		$end = date('Y-m-d');
		$sum = 0;
		$labels = array();
		$labels2 = array();
		$series = array();
		$series2 = array();
		while ($cur <= $end) {
			$n = isset($f_daily[$cur]) ? $f_daily[$cur] : 0;
			$sum += $n;
			$labels[] = $cur;
			$labels2[] = $cur;
			$series[] = $n;
			$series2[] = $sum;
			fputcsv($fp, array($cur, $n, $sum));
			$cur = date('Y-m-d', strtotime('+1 day', strtotime($cur)));
		}
		fclose($fp);

		/* stat: daily registration */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'x_axis' => array(
				'labels' => $labels,
				'type' => 'datetime',
			),
			'series' => array(
				array(
					'name' => 'Registration',
					'data' => $series,
				)
			)
		));
		$result = $this->restclient->post(WIDGET_DAILY_REGISTRATION, json_encode($data), 'json');

		/* stat: total customers trend */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'x_axis' => array(
				'labels' => $labels2,
				'type' => 'datetime',
			),
			'series' => array(
				array(
					'name' => 'Total Customers',
					'data' => $series2,
				)
			)
		));
		$result = $this->restclient->post(WIDGET_TOTAL_CUSTOMERS_TREND, json_encode($data), 'json');

		/* CSV 3 */
		$fp = fopen($csv3, 'w');
		$cur = min(array_keys($f_monthly));
		$end = date('Y-m');
		$sum = 0;
		$labels = array();
		$labels2 = array();
		$series = array();
		$series2 = array();
		$last = 0;
		while ($cur <= $end) {
			$n = isset($f_monthly[$cur]) ? $f_monthly[$cur] : 0;
			$sum += $n;
			$y_m = explode('-', $cur);
			$labels[] = $cur;
			if ($cur >= '2013-06') $labels2[] = $cur;
			$series[] = $n;
			if ($cur >= '2013-06') $series2[] = $last != 0 ? ($sum - $last)/$last*100 : 0;
			$last = $sum;
			fputcsv($fp, array($y_m[0], $y_m[1], $n, $sum));
			$cur = date('Y-m', strtotime('+1 month', strtotime($cur)));
		}
		fclose($fp);

		/* stat: monthly registration */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'x_axis' => array(
				'labels' => $labels,
				'type' => 'datetime',
			),
			'series' => array(
				array(
					'name' => 'Registration',
					'data' => $series,
				)
			)
		));
		$result = $this->restclient->post(WIDGET_MONTHLY_REGISTRATION, json_encode($data), 'json');

		/* stat: % growth (month over month) */
		$data = array('api_key' => GECKO_API_KEY, 'data' => array(
			'x_axis' => array(
				'labels' => $labels2,
				'type' => 'datetime',
			),
			'series' => array(
				array(
					'name' => 'Growth',
					'data' => $series2,
				)
			)
		));
		$result = $this->restclient->post(WIDGET_PERCENT_GROWTH, json_encode($data), 'json');

		/* email */
		$from = EMAIL_FROM;
		$to = array('pechpras@playbasis.com', 'pascal@playbasis.com', 'napada.w@playbasis.com', 'sanit.l@playbasis.com', 'mariya.v@playbasis.com');
		$subject = '[Playbasis] Dashboard User Registration';
		$message = 'The attachment includes 3 CSV files for (1) list of customers (2) statistics of daily registration and (3) statistics of monthly registration (as of '.date('Y-m-d').').';
		$html = $this->parser->parse('message.html', array('firstname' => 'Playbasis', 'lastname' => 'Team', 'message' => $message), true);
		$response = $this->utility->email($from, $to, $subject, $html, $message, array(
			$csv1 => 'list-customers_'.date('Y-m-d').'.csv',
			$csv2 => 'registration-daily_'.date('Y-m-d').'.csv',
			$csv3 => 'registration-monthly_'.date('Y-m-d').'.csv',
		));
		$this->email_model->log(EMAIL_TYPE_CLIENT_REGISTRATION, null, null, $response, $from, $to, $subject, $html);

		unlink($csv1);
		unlink($csv2);
		unlink($csv3);
	}

	public function insertCountries() {
		$countries = array();
		$countries[] = array("code"=>"AF","name"=>"Afghanistan","d_code"=>"+93");
		$countries[] = array("code"=>"AL","name"=>"Albania","d_code"=>"+355");
		$countries[] = array("code"=>"DZ","name"=>"Algeria","d_code"=>"+213");
		$countries[] = array("code"=>"AS","name"=>"American Samoa","d_code"=>"+1");
		$countries[] = array("code"=>"AD","name"=>"Andorra","d_code"=>"+376");
		$countries[] = array("code"=>"AO","name"=>"Angola","d_code"=>"+244");
		$countries[] = array("code"=>"AI","name"=>"Anguilla","d_code"=>"+1");
		$countries[] = array("code"=>"AG","name"=>"Antigua","d_code"=>"+1");
		$countries[] = array("code"=>"AR","name"=>"Argentina","d_code"=>"+54");
		$countries[] = array("code"=>"AM","name"=>"Armenia","d_code"=>"+374");
		$countries[] = array("code"=>"AW","name"=>"Aruba","d_code"=>"+297");
		$countries[] = array("code"=>"AU","name"=>"Australia","d_code"=>"+61");
		$countries[] = array("code"=>"AT","name"=>"Austria","d_code"=>"+43");
		$countries[] = array("code"=>"AZ","name"=>"Azerbaijan","d_code"=>"+994");
		$countries[] = array("code"=>"BH","name"=>"Bahrain","d_code"=>"+973");
		$countries[] = array("code"=>"BD","name"=>"Bangladesh","d_code"=>"+880");
		$countries[] = array("code"=>"BB","name"=>"Barbados","d_code"=>"+1");
		$countries[] = array("code"=>"BY","name"=>"Belarus","d_code"=>"+375");
		$countries[] = array("code"=>"BE","name"=>"Belgium","d_code"=>"+32");
		$countries[] = array("code"=>"BZ","name"=>"Belize","d_code"=>"+501");
		$countries[] = array("code"=>"BJ","name"=>"Benin","d_code"=>"+229");
		$countries[] = array("code"=>"BM","name"=>"Bermuda","d_code"=>"+1");
		$countries[] = array("code"=>"BT","name"=>"Bhutan","d_code"=>"+975");
		$countries[] = array("code"=>"BO","name"=>"Bolivia","d_code"=>"+591");
		$countries[] = array("code"=>"BA","name"=>"Bosnia and Herzegovina","d_code"=>"+387");
		$countries[] = array("code"=>"BW","name"=>"Botswana","d_code"=>"+267");
		$countries[] = array("code"=>"BR","name"=>"Brazil","d_code"=>"+55");
		$countries[] = array("code"=>"IO","name"=>"British Indian Ocean Territory","d_code"=>"+246");
		$countries[] = array("code"=>"VG","name"=>"British Virgin Islands","d_code"=>"+1");
		$countries[] = array("code"=>"BN","name"=>"Brunei","d_code"=>"+673");
		$countries[] = array("code"=>"BG","name"=>"Bulgaria","d_code"=>"+359");
		$countries[] = array("code"=>"BF","name"=>"Burkina Faso","d_code"=>"+226");
		$countries[] = array("code"=>"MM","name"=>"Burma Myanmar" ,"d_code"=>"+95");
		$countries[] = array("code"=>"BI","name"=>"Burundi","d_code"=>"+257");
		$countries[] = array("code"=>"KH","name"=>"Cambodia","d_code"=>"+855");
		$countries[] = array("code"=>"CM","name"=>"Cameroon","d_code"=>"+237");
		$countries[] = array("code"=>"CA","name"=>"Canada","d_code"=>"+1");
		$countries[] = array("code"=>"CV","name"=>"Cape Verde","d_code"=>"+238");
		$countries[] = array("code"=>"KY","name"=>"Cayman Islands","d_code"=>"+1");
		$countries[] = array("code"=>"CF","name"=>"Central African Republic","d_code"=>"+236");
		$countries[] = array("code"=>"TD","name"=>"Chad","d_code"=>"+235");
		$countries[] = array("code"=>"CL","name"=>"Chile","d_code"=>"+56");
		$countries[] = array("code"=>"CN","name"=>"China","d_code"=>"+86");
		$countries[] = array("code"=>"CO","name"=>"Colombia","d_code"=>"+57");
		$countries[] = array("code"=>"KM","name"=>"Comoros","d_code"=>"+269");
		$countries[] = array("code"=>"CK","name"=>"Cook Islands","d_code"=>"+682");
		$countries[] = array("code"=>"CR","name"=>"Costa Rica","d_code"=>"+506");
		$countries[] = array("code"=>"CI","name"=>"Côte d'Ivoire" ,"d_code"=>"+225");
		$countries[] = array("code"=>"HR","name"=>"Croatia","d_code"=>"+385");
		$countries[] = array("code"=>"CU","name"=>"Cuba","d_code"=>"+53");
		$countries[] = array("code"=>"CY","name"=>"Cyprus","d_code"=>"+357");
		$countries[] = array("code"=>"CZ","name"=>"Czech Republic","d_code"=>"+420");
		$countries[] = array("code"=>"CD","name"=>"Democratic Republic of Congo","d_code"=>"+243");
		$countries[] = array("code"=>"DK","name"=>"Denmark","d_code"=>"+45");
		$countries[] = array("code"=>"DJ","name"=>"Djibouti","d_code"=>"+253");
		$countries[] = array("code"=>"DM","name"=>"Dominica","d_code"=>"+1");
		$countries[] = array("code"=>"DO","name"=>"Dominican Republic","d_code"=>"+1");
		$countries[] = array("code"=>"EC","name"=>"Ecuador","d_code"=>"+593");
		$countries[] = array("code"=>"EG","name"=>"Egypt","d_code"=>"+20");
		$countries[] = array("code"=>"SV","name"=>"El Salvador","d_code"=>"+503");
		$countries[] = array("code"=>"GQ","name"=>"Equatorial Guinea","d_code"=>"+240");
		$countries[] = array("code"=>"ER","name"=>"Eritrea","d_code"=>"+291");
		$countries[] = array("code"=>"EE","name"=>"Estonia","d_code"=>"+372");
		$countries[] = array("code"=>"ET","name"=>"Ethiopia","d_code"=>"+251");
		$countries[] = array("code"=>"FK","name"=>"Falkland Islands","d_code"=>"+500");
		$countries[] = array("code"=>"FO","name"=>"Faroe Islands","d_code"=>"+298");
		$countries[] = array("code"=>"FM","name"=>"Federated States of Micronesia","d_code"=>"+691");
		$countries[] = array("code"=>"FJ","name"=>"Fiji","d_code"=>"+679");
		$countries[] = array("code"=>"FI","name"=>"Finland","d_code"=>"+358");
		$countries[] = array("code"=>"FR","name"=>"France","d_code"=>"+33");
		$countries[] = array("code"=>"GF","name"=>"French Guiana","d_code"=>"+594");
		$countries[] = array("code"=>"PF","name"=>"French Polynesia","d_code"=>"+689");
		$countries[] = array("code"=>"GA","name"=>"Gabon","d_code"=>"+241");
		$countries[] = array("code"=>"GE","name"=>"Georgia","d_code"=>"+995");
		$countries[] = array("code"=>"DE","name"=>"Germany","d_code"=>"+49");
		$countries[] = array("code"=>"GH","name"=>"Ghana","d_code"=>"+233");
		$countries[] = array("code"=>"GI","name"=>"Gibraltar","d_code"=>"+350");
		$countries[] = array("code"=>"GR","name"=>"Greece","d_code"=>"+30");
		$countries[] = array("code"=>"GL","name"=>"Greenland","d_code"=>"+299");
		$countries[] = array("code"=>"GD","name"=>"Grenada","d_code"=>"+1");
		$countries[] = array("code"=>"GP","name"=>"Guadeloupe","d_code"=>"+590");
		$countries[] = array("code"=>"GU","name"=>"Guam","d_code"=>"+1");
		$countries[] = array("code"=>"GT","name"=>"Guatemala","d_code"=>"+502");
		$countries[] = array("code"=>"GN","name"=>"Guinea","d_code"=>"+224");
		$countries[] = array("code"=>"GW","name"=>"Guinea-Bissau","d_code"=>"+245");
		$countries[] = array("code"=>"GY","name"=>"Guyana","d_code"=>"+592");
		$countries[] = array("code"=>"HT","name"=>"Haiti","d_code"=>"+509");
		$countries[] = array("code"=>"HN","name"=>"Honduras","d_code"=>"+504");
		$countries[] = array("code"=>"HK","name"=>"Hong Kong","d_code"=>"+852");
		$countries[] = array("code"=>"HU","name"=>"Hungary","d_code"=>"+36");
		$countries[] = array("code"=>"IS","name"=>"Iceland","d_code"=>"+354");
		$countries[] = array("code"=>"IN","name"=>"India","d_code"=>"+91");
		$countries[] = array("code"=>"ID","name"=>"Indonesia","d_code"=>"+62");
		$countries[] = array("code"=>"IR","name"=>"Iran","d_code"=>"+98");
		$countries[] = array("code"=>"IQ","name"=>"Iraq","d_code"=>"+964");
		$countries[] = array("code"=>"IE","name"=>"Ireland","d_code"=>"+353");
		$countries[] = array("code"=>"IL","name"=>"Israel","d_code"=>"+972");
		$countries[] = array("code"=>"IT","name"=>"Italy","d_code"=>"+39");
		$countries[] = array("code"=>"JM","name"=>"Jamaica","d_code"=>"+1");
		$countries[] = array("code"=>"JP","name"=>"Japan","d_code"=>"+81");
		$countries[] = array("code"=>"JO","name"=>"Jordan","d_code"=>"+962");
		$countries[] = array("code"=>"KZ","name"=>"Kazakhstan","d_code"=>"+7");
		$countries[] = array("code"=>"KE","name"=>"Kenya","d_code"=>"+254");
		$countries[] = array("code"=>"KI","name"=>"Kiribati","d_code"=>"+686");
		$countries[] = array("code"=>"XK","name"=>"Kosovo","d_code"=>"+381");
		$countries[] = array("code"=>"KW","name"=>"Kuwait","d_code"=>"+965");
		$countries[] = array("code"=>"KG","name"=>"Kyrgyzstan","d_code"=>"+996");
		$countries[] = array("code"=>"LA","name"=>"Laos","d_code"=>"+856");
		$countries[] = array("code"=>"LV","name"=>"Latvia","d_code"=>"+371");
		$countries[] = array("code"=>"LB","name"=>"Lebanon","d_code"=>"+961");
		$countries[] = array("code"=>"LS","name"=>"Lesotho","d_code"=>"+266");
		$countries[] = array("code"=>"LR","name"=>"Liberia","d_code"=>"+231");
		$countries[] = array("code"=>"LY","name"=>"Libya","d_code"=>"+218");
		$countries[] = array("code"=>"LI","name"=>"Liechtenstein","d_code"=>"+423");
		$countries[] = array("code"=>"LT","name"=>"Lithuania","d_code"=>"+370");
		$countries[] = array("code"=>"LU","name"=>"Luxembourg","d_code"=>"+352");
		$countries[] = array("code"=>"MO","name"=>"Macau","d_code"=>"+853");
		$countries[] = array("code"=>"MK","name"=>"Macedonia","d_code"=>"+389");
		$countries[] = array("code"=>"MG","name"=>"Madagascar","d_code"=>"+261");
		$countries[] = array("code"=>"MW","name"=>"Malawi","d_code"=>"+265");
		$countries[] = array("code"=>"MY","name"=>"Malaysia","d_code"=>"+60");
		$countries[] = array("code"=>"MV","name"=>"Maldives","d_code"=>"+960");
		$countries[] = array("code"=>"ML","name"=>"Mali","d_code"=>"+223");
		$countries[] = array("code"=>"MT","name"=>"Malta","d_code"=>"+356");
		$countries[] = array("code"=>"MH","name"=>"Marshall Islands","d_code"=>"+692");
		$countries[] = array("code"=>"MQ","name"=>"Martinique","d_code"=>"+596");
		$countries[] = array("code"=>"MR","name"=>"Mauritania","d_code"=>"+222");
		$countries[] = array("code"=>"MU","name"=>"Mauritius","d_code"=>"+230");
		$countries[] = array("code"=>"YT","name"=>"Mayotte","d_code"=>"+262");
		$countries[] = array("code"=>"MX","name"=>"Mexico","d_code"=>"+52");
		$countries[] = array("code"=>"MD","name"=>"Moldova","d_code"=>"+373");
		$countries[] = array("code"=>"MC","name"=>"Monaco","d_code"=>"+377");
		$countries[] = array("code"=>"MN","name"=>"Mongolia","d_code"=>"+976");
		$countries[] = array("code"=>"ME","name"=>"Montenegro","d_code"=>"+382");
		$countries[] = array("code"=>"MS","name"=>"Montserrat","d_code"=>"+1");
		$countries[] = array("code"=>"MA","name"=>"Morocco","d_code"=>"+212");
		$countries[] = array("code"=>"MZ","name"=>"Mozambique","d_code"=>"+258");
		$countries[] = array("code"=>"NA","name"=>"Namibia","d_code"=>"+264");
		$countries[] = array("code"=>"NR","name"=>"Nauru","d_code"=>"+674");
		$countries[] = array("code"=>"NP","name"=>"Nepal","d_code"=>"+977");
		$countries[] = array("code"=>"NL","name"=>"Netherlands","d_code"=>"+31");
		$countries[] = array("code"=>"AN","name"=>"Netherlands Antilles","d_code"=>"+599");
		$countries[] = array("code"=>"NC","name"=>"New Caledonia","d_code"=>"+687");
		$countries[] = array("code"=>"NZ","name"=>"New Zealand","d_code"=>"+64");
		$countries[] = array("code"=>"NI","name"=>"Nicaragua","d_code"=>"+505");
		$countries[] = array("code"=>"NE","name"=>"Niger","d_code"=>"+227");
		$countries[] = array("code"=>"NG","name"=>"Nigeria","d_code"=>"+234");
		$countries[] = array("code"=>"NU","name"=>"Niue","d_code"=>"+683");
		$countries[] = array("code"=>"NF","name"=>"Norfolk Island","d_code"=>"+672");
		$countries[] = array("code"=>"KP","name"=>"North Korea","d_code"=>"+850");
		$countries[] = array("code"=>"MP","name"=>"Northern Mariana Islands","d_code"=>"+1");
		$countries[] = array("code"=>"NO","name"=>"Norway","d_code"=>"+47");
		$countries[] = array("code"=>"OM","name"=>"Oman","d_code"=>"+968");
		$countries[] = array("code"=>"PK","name"=>"Pakistan","d_code"=>"+92");
		$countries[] = array("code"=>"PW","name"=>"Palau","d_code"=>"+680");
		$countries[] = array("code"=>"PS","name"=>"Palestine","d_code"=>"+970");
		$countries[] = array("code"=>"PA","name"=>"Panama","d_code"=>"+507");
		$countries[] = array("code"=>"PG","name"=>"Papua New Guinea","d_code"=>"+675");
		$countries[] = array("code"=>"PY","name"=>"Paraguay","d_code"=>"+595");
		$countries[] = array("code"=>"PE","name"=>"Peru","d_code"=>"+51");
		$countries[] = array("code"=>"PH","name"=>"Philippines","d_code"=>"+63");
		$countries[] = array("code"=>"PL","name"=>"Poland","d_code"=>"+48");
		$countries[] = array("code"=>"PT","name"=>"Portugal","d_code"=>"+351");
		$countries[] = array("code"=>"PR","name"=>"Puerto Rico","d_code"=>"+1");
		$countries[] = array("code"=>"QA","name"=>"Qatar","d_code"=>"+974");
		$countries[] = array("code"=>"CG","name"=>"Republic of the Congo","d_code"=>"+242");
		$countries[] = array("code"=>"RE","name"=>"Réunion" ,"d_code"=>"+262");
		$countries[] = array("code"=>"RO","name"=>"Romania","d_code"=>"+40");
		$countries[] = array("code"=>"RU","name"=>"Russia","d_code"=>"+7");
		$countries[] = array("code"=>"RW","name"=>"Rwanda","d_code"=>"+250");
		$countries[] = array("code"=>"BL","name"=>"Saint Barthélemy" ,"d_code"=>"+590");
		$countries[] = array("code"=>"SH","name"=>"Saint Helena","d_code"=>"+290");
		$countries[] = array("code"=>"KN","name"=>"Saint Kitts and Nevis","d_code"=>"+1");
		$countries[] = array("code"=>"MF","name"=>"Saint Martin","d_code"=>"+590");
		$countries[] = array("code"=>"PM","name"=>"Saint Pierre and Miquelon","d_code"=>"+508");
		$countries[] = array("code"=>"VC","name"=>"Saint Vincent and the Grenadines","d_code"=>"+1");
		$countries[] = array("code"=>"WS","name"=>"Samoa","d_code"=>"+685");
		$countries[] = array("code"=>"SM","name"=>"San Marino","d_code"=>"+378");
		$countries[] = array("code"=>"ST","name"=>"São Tomé and Príncipe" ,"d_code"=>"+239");
		$countries[] = array("code"=>"SA","name"=>"Saudi Arabia","d_code"=>"+966");
		$countries[] = array("code"=>"SN","name"=>"Senegal","d_code"=>"+221");
		$countries[] = array("code"=>"RS","name"=>"Serbia","d_code"=>"+381");
		$countries[] = array("code"=>"SC","name"=>"Seychelles","d_code"=>"+248");
		$countries[] = array("code"=>"SL","name"=>"Sierra Leone","d_code"=>"+232");
		$countries[] = array("code"=>"SG","name"=>"Singapore","d_code"=>"+65");
		$countries[] = array("code"=>"SK","name"=>"Slovakia","d_code"=>"+421");
		$countries[] = array("code"=>"SI","name"=>"Slovenia","d_code"=>"+386");
		$countries[] = array("code"=>"SB","name"=>"Solomon Islands","d_code"=>"+677");
		$countries[] = array("code"=>"SO","name"=>"Somalia","d_code"=>"+252");
		$countries[] = array("code"=>"ZA","name"=>"South Africa","d_code"=>"+27");
		$countries[] = array("code"=>"KR","name"=>"South Korea","d_code"=>"+82");
		$countries[] = array("code"=>"ES","name"=>"Spain","d_code"=>"+34");
		$countries[] = array("code"=>"LK","name"=>"Sri Lanka","d_code"=>"+94");
		$countries[] = array("code"=>"LC","name"=>"St. Lucia","d_code"=>"+1");
		$countries[] = array("code"=>"SD","name"=>"Sudan","d_code"=>"+249");
		$countries[] = array("code"=>"SR","name"=>"Suriname","d_code"=>"+597");
		$countries[] = array("code"=>"SZ","name"=>"Swaziland","d_code"=>"+268");
		$countries[] = array("code"=>"SE","name"=>"Sweden","d_code"=>"+46");
		$countries[] = array("code"=>"CH","name"=>"Switzerland","d_code"=>"+41");
		$countries[] = array("code"=>"SY","name"=>"Syria","d_code"=>"+963");
		$countries[] = array("code"=>"TW","name"=>"Taiwan","d_code"=>"+886");
		$countries[] = array("code"=>"TJ","name"=>"Tajikistan","d_code"=>"+992");
		$countries[] = array("code"=>"TZ","name"=>"Tanzania","d_code"=>"+255");
		$countries[] = array("code"=>"TH","name"=>"Thailand","d_code"=>"+66");
		$countries[] = array("code"=>"BS","name"=>"The Bahamas","d_code"=>"+1");
		$countries[] = array("code"=>"GM","name"=>"The Gambia","d_code"=>"+220");
		$countries[] = array("code"=>"TL","name"=>"Timor-Leste","d_code"=>"+670");
		$countries[] = array("code"=>"TG","name"=>"Togo","d_code"=>"+228");
		$countries[] = array("code"=>"TK","name"=>"Tokelau","d_code"=>"+690");
		$countries[] = array("code"=>"TO","name"=>"Tonga","d_code"=>"+676");
		$countries[] = array("code"=>"TT","name"=>"Trinidad and Tobago","d_code"=>"+1");
		$countries[] = array("code"=>"TN","name"=>"Tunisia","d_code"=>"+216");
		$countries[] = array("code"=>"TR","name"=>"Turkey","d_code"=>"+90");
		$countries[] = array("code"=>"TM","name"=>"Turkmenistan","d_code"=>"+993");
		$countries[] = array("code"=>"TC","name"=>"Turks and Caicos Islands","d_code"=>"+1");
		$countries[] = array("code"=>"TV","name"=>"Tuvalu","d_code"=>"+688");
		$countries[] = array("code"=>"UG","name"=>"Uganda","d_code"=>"+256");
		$countries[] = array("code"=>"UA","name"=>"Ukraine","d_code"=>"+380");
		$countries[] = array("code"=>"AE","name"=>"United Arab Emirates","d_code"=>"+971");
		$countries[] = array("code"=>"GB","name"=>"United Kingdom","d_code"=>"+44");
		$countries[] = array("code"=>"US","name"=>"United States","d_code"=>"+1");
		$countries[] = array("code"=>"UY","name"=>"Uruguay","d_code"=>"+598");
		$countries[] = array("code"=>"VI","name"=>"US Virgin Islands","d_code"=>"+1");
		$countries[] = array("code"=>"UZ","name"=>"Uzbekistan","d_code"=>"+998");
		$countries[] = array("code"=>"VU","name"=>"Vanuatu","d_code"=>"+678");
		$countries[] = array("code"=>"VA","name"=>"Vatican City","d_code"=>"+39");
		$countries[] = array("code"=>"VE","name"=>"Venezuela","d_code"=>"+58");
		$countries[] = array("code"=>"VN","name"=>"Vietnam","d_code"=>"+84");
		$countries[] = array("code"=>"WF","name"=>"Wallis and Futuna","d_code"=>"+681");
		$countries[] = array("code"=>"YE","name"=>"Yemen","d_code"=>"+967");
		$countries[] = array("code"=>"ZM","name"=>"Zambia","d_code"=>"+260");
		$countries[] = array("code"=>"ZW","name"=>"Zimbabwe","d_code"=>"+263");
		$this->service_model->insertCountries($countries);
	}

	public function notifyClientsToSetupMobile() {
		$clients = $this->client_model->listAllActiveClientsWithoutMobile();
		if ($clients) foreach ($clients as $client) {
			$client_id = $client['_id'];
			$email = $client['email'];

			$myplan_id = $this->client_model->getPlanIdByClientId($client_id);
			if ($myplan_id != FREE_PLAN) continue;

			/* email should: (1) not be in black list  */
			if (!$this->email_model->isEmailInBlackList($email)) {
				$from = EMAIL_FROM;
				$to = $email;
				$subject = '[Playbasis] Please Verify Your Account';
				$html = $this->parser->parse('message_verify_mobile.html', array('firstname' => $client['first_name'], 'lastname' => $client['last_name'], 'date' => date('M d, Y', strtotime(DATE_FREE_ACCOUNT_SHOULD_SETUP_MOBILE))), true);
				$response = $this->utility->email_bcc($from, array($to, EMAIL_BCC_PLAYBASIS_EMAIL), $subject, $html);
				$this->email_model->log(EMAIL_TYPE_CLIENT_REGISTRATION, $client_id, null, $response, $from, $to, $subject, $html);
			}
		}
	}

	public function renewCalendarWebhooks() {
		$this->load->library('GoogleApi');
		$webhooks = $this->googles_model->listAlmostExpiredCalendarChannels();
		if ($webhooks) foreach ($webhooks as $webhook) {
			$record = $this->googles_model->getRegistration($webhook['site_id']);
			if ($record) {
				$client = $this->googleapi->initialize($record['google_client_id'], $record['google_client_secret']);
				if (isset($record['token'])) {
					if (isset($webhook['calendar_id'])) {
						$service = $client->setAccessToken($record['token'])->calendar();
						$channel_id = $webhook['channel_id'];
						$new_channel_id = get_random_code(12,true,true,true);
						/* there has to be some overlapping for the same resource_id */
						$client->watchCalendar($service, $webhook['calendar_id'], $new_channel_id, array('site_id' => $webhook['site_id'].'', 'callback_url' => $webhook['callback_url']));
						$this->googles_model->insertWebhook($webhook['client_id'], $webhook['site_id'], $webhook['calendar_id'], $new_channel_id, $webhook['callback_url']);
						$client->unwatchCalendar($service, $channel_id, $webhook['resource_id']);
						$this->googles_model->removeWebhook($webhook['client_id'], $webhook['site_id'], $channel_id, $webhook['resource_id']);
					}
				}
			}
		}
	}

    public function energyUpdater()
    {
        $this->load->model('energy_model');
        $this->load->model('player_model');

        $now = time();

        if ($this->input->is_cli_request()) {
            foreach ($this->energy_model->findActiveEnergyRewards() as $energy) {
                $client_id = $energy['client_id'];
                $site_id = $energy['site_id'];
                $energy_reward_id = $energy['reward_id'];
                $energy_change_period = $energy['energy_props']['changing_period'];
                $energy_change_per_period = (int)$energy['energy_props']['changing_per_period'];

                //FIXME(Rook): to check if client and site is still active, if not just break this loop

                $completed_flag = $this->energy_model->updatePlayersEnergyValueWithConditions($client_id,
                    $site_id, $energy_reward_id, $now, $energy_change_period, $energy_change_per_period);
                if ($completed_flag) {
                    echo "Updated value success!" . PHP_EOL;
                } else {
                    echo "Skip update" . PHP_EOL;
                }
            }
        }
    }

    public function energyInitialInsertion()
    {
        $this->load->model('energy_model');
        $this->load->model('player_model');

        if ($this->input->is_cli_request()) {
            foreach ($this->energy_model->findActiveEnergyRewards() as $energy) {
                $client_id = $energy['client_id'];
                $site_id = $energy['site_id'];
                $energy_reward_id = $energy['reward_id'];
                $energy_max = (int)$energy['energy_props']['maximum'];

                //FIXME(Rook): to check if client and site is still active, if not just break this loop

                $total = $this->energy_model->findPlayersToInsert($client_id, $site_id, true);
                echo "Total Player to insert = $total" . PHP_EOL;
                for ($i = 0; $i <= round($total / LIMIT_PLAYERS_QUERY); $i++) {
                    $offset = LIMIT_PLAYERS_QUERY * $i;
                    $players_without_energy = $this->energy_model->findPlayersToInsert($client_id, $site_id, false,
                        $offset, LIMIT_PLAYERS_QUERY);

                    $batch_data = array();
                    foreach ($players_without_energy as $player) {
                        // Note: $player here is from player table
                        if ($energy['type'] == 'gain') {
                            array_push($batch_data, array(
                                'pb_player_id' => $player['_id'],
                                'cl_player_id' => $player['cl_player_id'],
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'reward_id' => $energy_reward_id,
                                'value' => $energy_max,
                                'date_cron_modified' => new MongoDate(),
                                'date_added' => new MongoDate(),
                                'date_modified' => new MongoDate()
                            ));
                        } elseif ($energy['type'] == 'loss') {
                            array_push($batch_data, array(
                                'pb_player_id' => $player['_id'],
                                'cl_player_id' => $player['cl_player_id'],
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'reward_id' => $energy_reward_id,
                                'value' => 0,
                                'date_cron_modified' => new MongoDate(),
                                'date_added' => new MongoDate(),
                                'date_modified' => new MongoDate()
                            ));
                        }
                    }
                    if (!empty($batch_data)) {
                        $completed_flag = $this->energy_model->bulkInsertInitialValue($batch_data);
                        if ($completed_flag) {
                            echo "Bulk Insert Initial value success! at offset#$offset" . PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}

function urlsafe_b64encode($string) {
	$data = base64_encode($string);
	return str_replace(array('+','/','='), array('-','_',''), $data);
}

function index_email($obj) {
	return $obj['email'];
}
?>