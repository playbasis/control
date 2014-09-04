<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('EMAIL_FROM', 'info@playbasis.com');
define('DAYS_TO_BECOME_INACTIVE', 15);
define('DAYS_TO_SEND_ANOTHER_EMAIL', 7);
class Cron extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('client_model');
		$this->load->model('email_model');
		$this->load->model('service_model');
		$this->load->model('tool/utility', 'utility');
	}

	public function notifyInactiveClients() {
		$clients = $this->client_model->listAllActiveClients();
		if ($clients) foreach ($clients as $client) {
			$client_id = $client['_id'];
			$client['email'] = array('pechpras@playbasis.com','pascal@playbasis.com');
			$latest_activity = $this->service_model->findLatestAPIactivity($client_id);
			/* check status */
			if (!$latest_activity || $this->utility()->find_diff_in_days($latest_activity->sec, time()) >= DAYS_TO_BECOME_INACTIVE) {
				$email = $client['email'];
				$latest_sent = $this->email_model->findLatestSent($client_id, EMAIL_TYPE_NOTIFY_INACTIVE_CLIENTS);
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
	}

	public function notifyNearLimitUsageClientsToUpgrade() {
	}

	public function remindClientsToSetupSubscription() {
	}

	public function remindClientsEndOfTrialPeriod() {
	}

	public function notifyClientsEndOfGracePeriod() {
	}
}
?>