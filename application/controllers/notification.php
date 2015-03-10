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
		$this->load->model('player_model');
		$this->load->model('payment_model');
		$this->load->model('email_model');
		$this->load->library('curl');
	}

	public function index_get()
	{
		$messages = $this->notification_model->list_messages($this->site_id);
		$this->response($this->resp->setRespond($messages), 200);
	}

	public function index_post($arg = null)
	{
		// headers = HTTP_X_AMZ_SNS_MESSAGE_TYPE, HTTP_X_AMZ_SNS_MESSAGE_ID, HTTP_X_AMZ_SNS_TOPIC_ARN, HTTP_X_AMZ_SNS_SUBSCRIPTION_ARN
		// body = $this->request->body
		$message = !empty($this->request->body) ? $this->request->body : $_POST;
		log_message('debug', '_SERVER = '.print_r($_SERVER, true));
		log_message('debug', 'message = '.print_r($message, true));
		$log_id = $this->notification_model->log($this->site_id, $message);
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
					$response = $this->handleNotification($this->convertToJson($message['Message']));
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
		} else if (strpos($_SERVER['HTTP_USER_AGENT'], PAYMENT_CHANNEL_PAYPAL) === false ? false : true) { // PayPal IPN: https://developer.paypal.com/docs/classic/ipn/ht_ipn/
			// STEP 1: read POST data

			// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
			// Instead, read raw POST data from the input stream.
			$myPost = array();
			$raw_post_array = explode('&', $this->request->raw);
			foreach ($raw_post_array as $keyval) {
				$keyval = explode ('=', $keyval);
				if (count($keyval) == 2)
					$myPost[$keyval[0]] = urldecode($keyval[1]);
			}
			// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
			$req = 'cmd=_notify-validate';
			$get_magic_quotes_exists = false;
			if(function_exists('get_magic_quotes_gpc')) {
				$get_magic_quotes_exists = true;
			}
			foreach ($myPost as $key => $value) {
				if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
					$value = urlencode(stripslashes($value));
				} else {
					$value = urlencode($value);
				}
				$req .= "&$key=$value";
			}

			// Step 2: POST IPN data back to PayPal to validate

			$ch = curl_init('https://www.'.(PAYPAL_ENV == 'sandbox' ? PAYPAL_ENV.'.' : '').'paypal.com/cgi-bin/webscr');
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			// In wamp-like environments that do not come bundled with root authority certificates,
			// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set
			// the directory path of the certificate as shown below:
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/../certs/cacert.pem');
			if( !($res = curl_exec($ch)) ) {
				$msg = 'Getting a problem when trying to verify PayPal IPN, response: '.curl_error($ch);
				log_message('error', $msg);
				curl_close($ch);
				$this->response($this->error->setError('CANNOT_VERIFY_PAYPAL_IPN', $msg), 200);
			}
			curl_close($ch);

			// inspect IPN validation result and act accordingly
			if (strcmp($res, PAYPAL_IPN_VERIFIED) == 0) { // The IPN is verified
				// extract 'client_id' and 'plan_id' from 'custom' field in IPN message
				$custom = $_POST['custom'];
				$pieces = explode(',', $custom);
				$client_id = new MongoId($pieces[0]);
				$plan_id = new MongoId($pieces[1]);

				log_message('debug', 'process: _POST = '.print_r($_POST, true));
				$result = $this->payment_model->processVerifiedIPN($client_id, $plan_id, $_POST, $log_id);
				log_message('debug', 'process: result = '.$result);

				$this->response($this->resp->setRespond('Handle notification message successfully'), 200);
			} else if (strcmp($res, PAYPAL_IPN_INVALID) == 0) { // IPN invalid, log for further investigation
				log_message('error', 'Invalid PayPal IPN message, response: '.$res);
				$this->response($this->error->setError('INVALID_PAYPAL_IPN', $res), 200);
			} else {
				log_message('error', 'Unknow return status from PayPal, response: '.$res);
				$this->response($this->error->setError('INVALID_PAYPAL_IPN', $res), 200);
			}
		} else if (strpos($_SERVER['HTTP_USER_AGENT'], FULLCONTACT_USER_AGENT) === false ? false : true) {
			log_message('debug', 'arg = '.print_r($arg, true));
			$email = urlsafe_b64decode($arg);
			log_message('debug', 'email = '.print_r($email, true));
			$this->player_model->insertFullContact($email, $message);
			$this->response($this->resp->setRespond('Handle notification message successfully'), 200);
		}
		$this->response($this->error->setError('UNKNOWN_NOTIFICATION_MESSAGE'), 200);
	}

	private function convertToJson($str)
	{
		$str = trim($str);
		if ($str[0] == '{' && $str[strlen($str)-1] == '}') {
			return $this->format->factory($str, 'json')->to_array();
		}
		return $str;
	}

	private function handleNotification($message)
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
			if ($this->email_model->isEmailInBlackList($email, $this->site_id)) continue;
			$this->email_model->addIntoBlackList($this->site_id, $email, 'Bounce', $bounce['bounceType'], $bounce['bounceSubType'], $bounce['feedbackId']);
		}
	}

	private function handleComplaint($complaint)
	{
		foreach ($complaint['complainedRecipients'] as $each) {
			$email = $each['emailAddress'];
			if ($this->email_model->isEmailInBlackList($email, $this->site_id)) continue;
			$this->email_model->addIntoBlackList($this->site_id, $email, 'Complaint', $complaint['userAgent'], $complaint['complaintFeedbackType'], $complaint['feedbackId']);
		}
	}
}

function urlsafe_b64decode($string) {
	$data = str_replace(array('-','_'), array('+','/'), $string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}
?>