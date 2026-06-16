<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* Name:  Twilio
	*
	* Author: Ben Edmunds
	*		  ben.edmunds@gmail.com
	*         @benedmunds
	*
	* Location:
	*
	* Created:  03.29.2011
	*
	* Description:  Twilio configuration settings.
	*
	*
	*/

	/**
	 * Mode ("sandbox" or "prod")
	 **/
	$config['mode']   = getenv('TWILIO_MODE') ?: 'sandbox';

	/**
	 * Account SID
	 **/
	$config['account_sid']   = getenv('TWILIO_ACCOUNT_SID') ?: '';

	/**
	 * Auth Token
	 **/
	$config['auth_token']    = getenv('TWILIO_AUTH_TOKEN') ?: '';

	/**
	 * API Version
	 **/
	$config['api_version']   = getenv('TWILIO_API_VERSION') ?: '2010-04-01';

	/**
	 * Twilio Phone Number
	 **/
	$config['number']        = getenv('TWILIO_NUMBER') ?: '';


/* End of file twilio.php */
