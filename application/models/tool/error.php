<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Error extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function setError($code, $dataArray = array())
	{
		$errorData = array();
		$errorData['success'] = false;
		$errorData['response'] = array();
		$errorData['error_code'] = '';
		switch($code)
		{
		case 'INVALID_TOKEN':
			$errorData['message'] = "Invalid token Key";
			$errorData['error_code'] = '0900';
			break;
		case 'REQUEST_EXCEEDED':
			$errorData['message'] = "Request exceeded , Too much request";
			$errorData['error_code'] = '0901';
			break;
		case 'TOKEN_REQUIRED':
			$errorData['message'] = "Token key required";
			$errorData['error_code'] = '0902';
			break;
		case 'PARAMETER_MISSING':
			if(!empty($dataArray))
			{
				$errorData['message'] = "Invalid parameter , [ " . implode(' , ', $dataArray) . " ] require , must not be blank or special character";
				$errorData['error_code'] = '0903';
			}
			else
			{
				$errorData['message'] = "Invalid parameter , must not be blank and special character";
				$errorData['error_code'] = '0903';
			}
			break;
		case 'INVALID_API_KEY_OR_SECRET':
			$errorData['message'] = "Invalid API-KEY OR API-SECRET";
			$errorData['error_code'] = '0001';
			break;
		case 'ACCESS_DENIED':
			$errorData['message'] = "Can\'t Access ,Permission Denied";
			$errorData['error_code'] = '0002';
			break;
		case 'USER_NOT_EXIST':
			$errorData['message'] = "User doesn't exist";
			$errorData['error_code'] = '0200';
			break;
		case 'USER_ALREADY_EXIST':
			$errorData['message'] = "User alredy exist";
			$errorData['error_code'] = '0201';
			break;
		case 'TOO_MANY_USERS':
			$errorData['message'] = "User registeration limit exceed";
			$errorData['error_code'] = '0202';
			break;
		case 'ACTION_NOT_FOUND':
			$errorData['message'] = "Action not available";
			$errorData['error_code'] = '0301';
			break;
		case 'REWARD_NOT_FOUND':
			$errorData['message'] = "Reward not available";
			$errorData['error_code'] = '0401';
			break;
        case 'GOODS_NOT_FOUND':
            $errorData['message'] = "Goods not available";
            $errorData['error_code'] = '0501';
            break;
        case 'OVER_LIMIT_REDEEM':
        	$errorData['message'] = "User has exceeded redeem limit";
            $errorData['error_code'] = '0601';
            break;
		default:
			$errorData['message'] = "Unknow";
			$errorData['error_code'] = '9999';
			break;
		}
		$errorData['timestamp'] = (int) time();
		$errorData['time'] = date('r e');
		return $errorData;
	}
}
?>