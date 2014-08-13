<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: thanayuthanakitworawat
 * Date: 3/28/2014 AD
 * Time: 10:48 PM
 */
class PlaybasisApi{

    private $_restClient;
    private $_cacheClient;
	private $api_key;
	private $api_secret;

    public function __construct(){

        $ci =& get_instance();

	    $config = array();
	    $ci->config->load('playbasis');
		$config['server'] = $ci->config->config['server'];

        $this->_restClient = $ci->rest;
        $this->_restClient->initialize($config);

        //set_error_handler('handleError');
    }

	public function set_api_key($api_key) {
		$this->api_key = $api_key;
	}

	public function set_api_secret($api_secret) {
		$this->api_secret = $api_secret;
	}

    public function getAction(){
        $result = $this->_get('Action');
        return $result;
    }

    public function getActionUsedonly(){
        $result = $this->_get('Action/usedonly');
        return $result;
    }

    public function getActionLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Action/log',$param);
        return $result;
    }

    public function getReward(){
        $result = $this->_get('Reward');
        return $result;
    }

    public function getRewardBadge(){
        $result = $this->_get('Reward/badge');
        return $result;
    }

    public function getRewardBadgeLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/badge/log',$param);
        return $result;
    }

    public function getRewardPointLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/point/log',$param);
        return $result;
    }

    public function getRewardExpLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/exp/log',$param);
        return $result;
    }

    public function getRewardLevelLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/level/log',$param);
        return $result;
    }

    public function getUserRegLog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Player/new',$param);
        return $result;
    }

    public function getUserDAULog($startDate,$endDate){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Player/dau_per_day',$param);
        return $result;
    }

    public function getUserMAULog($startDate,$endDate, $userUnitType){
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );

        switch($userUnitType){
            case 'day':
                $result = $this->_get('Player/mau_per_day',$param);
                break;
            case 'week':
                $result = $this->_get('Player/mau_per_week',$param);
                break;
            case 'month':
                $result = $this->_get('Player/mau_per_month',$param);
                break;
        }
        return $result;
    }

    private function _get($uri,$params = array()){
        $defaultParam = array('api_key' => $this->api_key);
        $sendParam = array_merge($defaultParam, $params);
        $result = $this->_restClient->get($uri, $sendParam);
        return $result;
    }
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext){
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}