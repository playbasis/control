<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

/**
 * Endpoint for Amazon Simple Notification Service (SNS)
 */
class Notification extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('tool/respond', 'resp');
		$this->load->model('tool/error', 'error');
		$this->load->model('email_model');
	}

	function index_post()
	{
		$message = $this->request->body;
		log_message('debug', $message);
		if (array_key_exists('notificationType', $message)) { // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
			switch ($message['notificationType']) {
			case 'Bounce':
				foreach ($message['bounce']['bouncedRecipients'] as $each) {
					$email = $each['emailAddress'];
					if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
					$this->email_model->addIntoBlackList($this->site_id, $email);
				}
				$this->response($this->resp->setRespond('Process Amazon SES bounce notification successfully'), 200);
				break;
			case 'Complaint':
				foreach ($message['complaint']['complainedRecipients'] as $each) {
					$email = $each['emailAddress'];
					if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
					$this->email_model->addIntoBlackList($this->site_id, $email);
				}
				$this->response($this->resp->setRespond('Process Amazon SES complaint notification successfully'), 200);
				break;
			default:
				$this->response($this->error->setError('UNSUPPORT_NOTIFICATION_TYPE', $message['notificationType']), 200);
				break;
			}
		}
		$this->response($this->resp->setRespond($message), 200); // FIXME: this line is for debugging only
		//$this->response($this->error->setError('UNKNOWN_NOTIFICATION_TYPE'), 200);
	}
}