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

	function index_get()
	{
		$this->response($this->resp->setRespond('Not supported method'), 200);
	}

	function index_post()
	{
		$message = $this->request->body;
		log_message('debug', $message);
		if (array_key_exists('notificationType', $message)) { // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
			switch ($message['notificationType']) {
			case 'Bounce':
				switch ($message['bounce']['bounceType']) { // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
				case 'Permanent':
					$bounce = $message['bounce'];
					foreach ($bounce['bouncedRecipients'] as $each) {
						$email = $each['emailAddress'];
						if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
						$this->email_model->addIntoBlackList($this->site_id, $email, $message['notificationType'], $bounce['bounceSubType'], $bounce['feedbackId']);
					}
					$this->response($this->resp->setRespond('Process Amazon SES bounce notification successfully (hard bounce)'), 200);
					break;
				case 'Transient':
					$this->response($this->resp->setRespond('Process Amazon SES bounce notification successfully (soft bounce)'), 200);
					break;
				case 'Undetermined':
				default:
					$this->response($this->resp->setRespond('Unsupported bounceType: '.$message['bounce']['bounceType']), 200);
					break;
				}
				break;
			case 'Complaint':
				$complaint = $message['complaint'];
				foreach ($complaint['complainedRecipients'] as $each) {
					$email = $each['emailAddress'];
					if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
					$this->email_model->addIntoBlackList($this->site_id, $email, $message['notificationType'], $complaint['complaintFeedbackType'], $complaint['feedbackId']);
				}
				$this->response($this->resp->setRespond('Process Amazon SES complaint notification successfully'), 200);
				break;
			default:
				$this->response($this->error->setError('UNSUPPORTED_NOTIFICATION_TYPE', $message['notificationType']), 200);
				break;
			}
		}
		$this->response($this->resp->setRespond('Unknown message: '.$message), 200);
	}
}