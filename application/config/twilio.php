<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
    * Name:  Twilio
    *
    * Description:  Twilio configuration settings.
    */

    $twilio_mode = getenv('TWILIO_MODE');
    $twilio_account_sid = getenv('TWILIO_ACCOUNT_SID');
    $twilio_auth_token = getenv('TWILIO_AUTH_TOKEN');
    $twilio_api_version = getenv('TWILIO_API_VERSION');
    $twilio_number = getenv('TWILIO_NUMBER');

    /**
     * Mode ("sandbox" or "prod")
     **/
    $config['mode']   = $twilio_mode !== false && $twilio_mode !== '' ? $twilio_mode : 'sandbox';

    /**
     * Account SID
     **/
    $config['account_sid']   = $twilio_account_sid !== false ? $twilio_account_sid : '';

    /**
     * Auth Token
     **/
    $config['auth_token']    = $twilio_auth_token !== false ? $twilio_auth_token : '';

    /**
     * API Version
     **/
    $config['api_version']   = $twilio_api_version !== false && $twilio_api_version !== '' ? $twilio_api_version : '2010-04-01';

    /**
     * Twilio Phone Number
     **/
    $config['number']        = $twilio_number !== false ? $twilio_number : '';


/* End of file twilio.php */
