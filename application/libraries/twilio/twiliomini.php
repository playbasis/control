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
require_once ("Services/Twilio.php");

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

    function __construct($config)
    {
        //initialize the CI super-object
        $this->_ci =& get_instance();

        $this->config = $config;

        $this->mode        = $config['mode'];
        $this->account_sid = $config['account_sid'];
        $this->auth_token  = $config['auth_token'];
        $this->api_version = $config['api_version'];
        $this->number      = $config['number'];

        //initialize the client
        $this->_twilio = new Services_Twilio($this->account_sid, $this->auth_token);
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