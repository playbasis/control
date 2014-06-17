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
		$this->load->model('notification_model');
		$this->load->model('email_model');
		$this->load->library('curl');
	}

	public function index_get()
	{
		$messages = $this->notification_model->list_messages($this->site_id);
		$this->response($this->resp->setRespond($messages), 200);
	}

	public function index_post()
	{
		// headers = HTTP_X_AMZ_SNS_MESSAGE_TYPE, HTTP_X_AMZ_SNS_MESSAGE_ID, HTTP_X_AMZ_SNS_TOPIC_ARN, HTTP_X_AMZ_SNS_SUBSCRIPTION_ARN
		// body = $this->request->body
		$message = $this->request->body;
		log_message('debug', '_SERVER = '.print_r($_SERVER, true));
		log_message('debug', 'message = '.print_r($message, true));
		$this->notification_model->log($this->site_id, $message);
		if (array_key_exists('HTTP_X_AMZ_SNS_MESSAGE_TYPE', $_SERVER)) { // Amazon SNS: http://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-header
			log_message('error', 'type = '.print_r($_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'], true));
			switch ($_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE']) {
				case 'SubscriptionConfirmation': // http://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-subscription-confirmation-json
					// fields: Type, MessageId, Token, TopicArn, Message, SubscribeURL, Timestamp, SignatureVersion, Signature, SigningCertURL
					log_message('debug', 'SubscribeURL = '.print_r($message['SubscribeURL'], true));
					$response = $this->curl->simple_get($message['SubscribeURL']); // http://philsturgeon.co.uk/code/codeigniter-curl
					log_message('debug', 'response = '.$response);
					break;
				case 'Notification': // http://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-notification-json
					// fields: Type, MessageId, TopicArn, Subject, Message, Timestamp, SignatureVersion, Signature, SigningCertURL, UnsubscribeURL
					log_message('debug', 'message = '.$message['Message']);
					$response = $this->handle($this->convertToJson($message['Message']));
					log_message('debug', 'response = '.$response);
					break;
				case 'UnsubscribeConfirmation': // http://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-unsubscribe-confirmation-json
					// fields: Type, MessageId, Token, TopicArn, Message, SubscribeURL, Timestamp, SignatureVersion, Signature, SigningCertURL
					break;
				default:
					$this->response($this->error->setError('UNKNOWN_SNS_MESSAGE_TYPE', $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE']), 200);
					break;
			}
			$this->response($this->resp->setRespond('Handle notification message successfully'), 200);
		}
		// non-Amazon SNS
		$this->response($this->error->setError('UNKNOWN_MESSAGE', $message), 200);
	}

	private function convertToJson($str)
	{
		$str = trim($str);
		if ($str[0] == '{' && $str[strlen($str)-1] == '}') {
			return $this->format->factory($str, 'json')->to_array();
		}
		return $str;
	}

	private function handle($message)
	{
		$ret = false;
		if (!empty($message)) {
			if (array_key_exists('notificationType', $message)) { // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
				switch ($message['notificationType']) {
				// example: http://sesblog.amazon.com/post/TxJE1JNZ6T9JXK/Handling-Bounces-and-Complaints
				case 'Bounce': // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
					switch ($message['bounce']['bounceType']) {
					case 'Transient': // soft bounce
						switch ($message['bounce']['bounceSubType']) {
							case 'MailboxFull':
							case 'MessageTooLarge':
							case 'ContentRejected':
							case 'AttachmentRejected':
								$this->handleBounce($message['bounce']);
								break;
							case 'General':
							default:
								break;
						}
						break;
					case 'Permanent': // hard bounce
					case 'Undetermined':
					default:
						$this->handleBounce($message['bounce']);
						break;
					}
					$ret = true;
					break;
				case 'Complaint': // http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#complaint-object
					$this->handleComplaint($message['complaint']);
					$ret = true;
					break;
				default:
					break;
				}
			}
		}
		return $ret;
	}

	private function handleBounce($bounce)
	{
		foreach ($bounce['bouncedRecipients'] as $each) {
			$email = $each['emailAddress'];
			if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
			$this->email_model->addIntoBlackList($this->site_id, $email, 'Bounce', $bounce['bounceType'], $bounce['bounceSubType'], $bounce['feedbackId']);
		}
	}

	private function handleComplaint($complaint)
	{
		foreach ($complaint['complainedRecipients'] as $each) {
			$email = $each['emailAddress'];
			if ($this->email_model->isEmailInBlackList($this->site_id, $email)) continue;
			$this->email_model->addIntoBlackList($this->site_id, $email, 'Complaint', $complaint['userAgent'], $complaint['complaintFeedbackType'], $complaint['feedbackId']);
		}
	}
}
?>