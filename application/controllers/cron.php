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
		$tmpfname = tempnam('/tmp', 'listClientRegistration');
		$fp = fopen($tmpfname, 'w');
		$clients = $this->client_model->listAllActiveClients();
		if ($clients) foreach ($clients as $client) {
			$sum = 0;
			$sites = $this->client_model->findActiveSites($client['_id']);
			foreach ($sites as $site) {
				$n = $this->client_model->countActionLog($site['_id']);
				$sum += $n;
			}
			fputcsv($fp, array($client['_id']."", $client['first_name'], $client['last_name'], $client['company'], $client['email'], $client['mobile'], datetimeMongotoReadable($client['date_added']), $sum));
		}
		fclose($fp);

		/* email */
		$from = EMAIL_FROM;
		$to = array('pechpras@playbasis.com', 'pascal@playbasis.com');
		$subject = '[Playbasis] Dashboard User Registration';
		$message = 'The attachment is a CSV file for the data about current user registration (as of '.date('Y-m-d').').';
		$html = $this->parser->parse('message.html', array('firstname' => 'Playbasis', 'lastname' => 'Team', 'message' => $message), true);
		$response = $this->utility->email($from, $to, $subject, $html, $message, array($tmpfname => 'user-registration_'.date('Y-m-d').'.csv'));
		$this->email_model->log(EMAIL_TYPE_CLIENT_REGISTRATION, null, null, $response, $from, $to, $subject, $html);

		unlink($tmpfname);
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