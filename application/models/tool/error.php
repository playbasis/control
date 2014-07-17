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
        case 'INTERNAL_ERROR':
            $errorData['message'] = "There is an internal server error: ".$dataArray;
            $errorData['error_code'] = '0800';
            break;
        case 'CANNOT_SEND_EMAIL':
            $errorData['message'] = "Email error, cannot send email to: ".$dataArray;
            $errorData['error_code'] = '0801';
            break;
        case 'ALL_EMAILS_IN_BLACKLIST':
            $errorData['message'] = "Email error, all designated recipients are in black list: ".$dataArray;
            $errorData['error_code'] = '0802';
            break;
        case 'EMAIL_ALREADY_IN_BLACKLIST':
            $errorData['message'] = "Email is already in black list: ".$dataArray;
            $errorData['error_code'] = '0803';
            break;
        case 'EMAIL_NOT_IN_BLACKLIST':
            $errorData['message'] = "Email is not in black list: ".$dataArray;
            $errorData['error_code'] = '0804';
            break;
        case 'UNSUPPORTED_NOTIFICATION_TYPE':
            $errorData['message'] = "This notification type is not supported: ".$dataArray;
            $errorData['error_code'] = '0805';
            break;
        case 'UNKNOWN_NOTIFICATION_TYPE':
            $errorData['message'] = "Notification type is unknown";
            $errorData['error_code'] = '0806';
            break;
        case 'INVALID_API_KEY_OR_SECRET':
            $errorData['message'] = "Invalid API-KEY OR API-SECRET";
            $errorData['error_code'] = '0001';
            break;
        case 'ACCESS_DENIED':
            $errorData['message'] = "Can\'t Access ,Permission Denied";
            $errorData['error_code'] = '0002';
            break;
        case 'LIMIT_EXCEED':
            $errorData['message'] = "Limit Exceed, Contact Admin";
            $errorData['error_code'] = '0003';
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
        case "USER_OR_REWARD_NOT_EXIST":
            $errorData["message"] = "The user or reward type does not exist";
            $errorData["error_code"] = "0203";
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
        case "QUEST_JOINED":
            $errorData["message"] = "User has already join this quest";
            $errorData["error_code"] = "0701";
            break;
        case "QUEST_FINISHED":
            $errorData["message"] = "User has finished this quest";
            $errorData["error_code"] = "0702";
            break;
        case "QUEST_CONDITION":
            $errorData["message"] = "User has no permission to join this quest";
            $errorData["error_code"] = "0703";
            break;
        case "QUEST_CANCEL_FAILED":
            $errorData["message"] = "User has not yet join this quest";
            $errorData["error_code"] = "0704";
            break;
        case "QUEST_JOIN_OR_CANCEL_NOTFOUND":
            $errorData["message"] = "Quest not found";
            $errorData["error_code"] = "0705";
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
