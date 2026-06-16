<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  TwilioMini
 *
 * Author: Wee Yeung
 *		  wee_weerapat@hotmail.com
 *
 *
 * Created:  2014-10-24
 *
 * Description:  Modified Twilio API classes to work as a CodeIgniter library.
 *
 *
 */

Class TwilioMini
{


    protected $_ci;
    protected $_twilio;
    protected $mode;
    protected $account_sid;
    protected $auth_token;
    protected $api_version;
    protected $number;
    protected $config;
    protected $configuration_error;

    function __construct($config)
    {
        //initialize the CI super-object
        $this->_ci =& get_instance();

        $this->config = $config;

        $this->mode        = isset($config['mode']) ? $config['mode'] : 'sandbox';
        $this->account_sid = isset($config['account_sid']) ? $config['account_sid'] : '';
        $this->auth_token  = isset($config['auth_token']) ? $config['auth_token'] : '';
        $this->api_version = isset($config['api_version']) ? $config['api_version'] : '2010-04-01';
        $this->number      = isset($config['number']) ? $config['number'] : '';

        if (!$this->account_sid || !$this->auth_token) {
            $this->configuration_error = 'Missing Twilio configuration: account_sid and auth_token are required';
            log_message('error', $this->configuration_error);
        }
    }

    private function ensureTwilioClient()
    {
        if ($this->_twilio) {
            return true;
        }

        if ($this->configuration_error) {
            return false;
        }

        $adapter = __DIR__ . '/Services/Twilio.php';
        if (!is_file($adapter)) {
            $this->configuration_error = 'Missing Twilio adapter library';
            log_message('error', $this->configuration_error);
            return false;
        }

        require_once $adapter;

        try {
            $this->_twilio = new Services_Twilio($this->account_sid, $this->auth_token);
        } catch (Exception $e) {
            $this->configuration_error = $e->getMessage();
            log_message('error', 'Unable to initialize Twilio adapter: ' . $this->configuration_error);
            return false;
        }

        return true;
    }

    private function configurationErrorResponse()
    {
        $res = (object)array();
        $res->IsError = true;
        $res->error_message = $this->configuration_error ? $this->configuration_error : 'Twilio adapter is unavailable';
        return $res;
    }


    /**
     * dial
     *
     * @desc Interface with rest client
     *
     * @from <string> Phone Number
     * @to <string> Phone Number
     * @make <string> It's a Url or ApplicationSid
     *
     */
    public function dial($from, $to, $make, $optional = array())
    {

        if (!$this->ensureTwilioClient()) {
            return $this->configurationErrorResponse();
        }

        try {
            // make call
            $call = $this->_twilio->account->calls->create($from, $to, $make, $optional);

            $res = $call->subresources["media"]->client->last_response;
            $res->IsError = false;
            if($res->error_message){
                $res->IsError = true;
            }
		} catch (Exception $e) {
            $res = (object)array();
            $res->IsError = true;
            $res->error_message = $e->getMessage();
        }
        return $res;
    }

    /**
     * Send SMS
     *
     * @desc Send a basic SMS
     *
     * @from <string> Phone Number
     * @to <string> Phone Number
     * @message <string> Text Message
     */
    public function sms($from, $to, $message)
    {

        if (!$this->ensureTwilioClient()) {
            return $this->configurationErrorResponse();
        }

        try {
            // make sms
            $message = $this->_twilio->account->messages->create(array(
                'To' => $to,
                'From' => $from,
                'Body' => $message
            ));
            $res = $message->subresources["media"]->client->last_response;
            $res->IsError = false;
            if($res->error_message){
                $res->IsError = true;
            }
		} catch (Exception $e) {
            $res = (object)array();
            $res->IsError = true;
            $res->error_message = $e->getMessage();
        }
        return $res;
    }

}
