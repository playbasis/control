<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: thanayuthanakitworawat
 * Date: 3/28/2014 AD
 * Time: 10:48 PM
 */
class PlaybasisApi
{
    private $_restClient;
    private $_cacheClient;
    private $api_key;
    private $api_secret;
    private $token;

    public function __construct() {

        $ci =& get_instance();

        $this->_restClient = $ci->rest;
        $this->_restClient->initialize(array('server' => API_SERVER));

        //set_error_handler('handleError');
    }

    public function set_api_key($api_key) {
        $this->api_key = $api_key;
    }

    public function set_api_secret($api_secret) {
        $this->api_secret = $api_secret;
    }

    public function auth($pkg_name=null) {
        $result = $this->_post('Auth', array(
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'pkg_name' => $pkg_name,
        ));
        if (isset($result->response->token)) $this->token = $result->response->token;
        return $result;
    }

    public function register($player_id, $username, $email, $optionalParams=array()) {
        $result = $this->_post('Player/'.$player_id.'/register/', array_merge(array(
            'username' => $username,
            'email' => $email,
        ), $optionalParams));
        return $result;
    }

    public function addPlayerToNode($player_id, $node_id) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/addPlayer/'.$player_id);
        return $result;
    }

    public function addContentToNode($content_id, $node_id) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/addContent/'.$content_id);
        return $result;
    }

    public function addPlayerToNodeByName($player_id, $node_name, $organize_type) {
        $result = $this->_post('StoreOrg/nodes/name/'.$node_name.'/type/'.$organize_type.'/addPlayer/'.$player_id);
        return $result;
    }

    public function setPlayerRole($player_id, $node_id, $role) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/setPlayerRole/'.$player_id,$role);
        return $result;
    }

    public function unsetPlayerRole($player_id, $node_id, $role) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/unsetPlayerRole/'.$player_id,$role);
        return $result;
    }

    public function setContentRole($content_id, $node_id, $role) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/setContentRole/'.$content_id,$role);
        return $result;
    }

    public function unsetContentRole($content_id, $node_id, $role) {
        $result = $this->_post('StoreOrg/nodes/'.$node_id.'/unsetContentRole/'.$content_id,$role);
        return $result;
    }

    public function updatePlayer($player_id, $Params=array()) {
        $result = $this->_post('Player/'.$player_id.'/update/', $Params);
        return $result;
    }

    public function deletePlayer($player_id) {
        $result = $this->_post('Player/'.$player_id.'/delete/');
        return $result;
    }

    public function testRule($data) {
        $result = $this->_get('Engine/json/'.urlencode(json_encode($data)), $data);
        return $result;
    }

    public function engine($player_id, $action, $optionalParams=array()) {
        $result = $this->_post('Engine/rule', array_merge(array(
            'player_id' => $player_id,
            'action' => $action,
        ), $optionalParams));
        return $result;
    }

    public function emailPlayer($player_id, $subject, $message) {
        $result = $this->_post('Email/send', array(
            'player_id' => $player_id,
            'subject' => $subject,
            'message' => $message,
        ));
        return $result;
    }

    public function emailPlayerTemplate($player_id, $subject, $template_id) {
        $result = $this->_post('Email/send', array(
            'player_id' => $player_id,
            'subject' => $subject,
            'template_id' => $template_id,
        ));
        return $result;
    }

    public function getAction() {
        $result = $this->_get('Action');
        return $result;
    }

    public function getActionUsedonly() {
        $result = $this->_get('Action/usedonly');
        return $result;
    }

    public function getActionLog($startDate,$endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Action/log', $param);
        return $result;
    }

    public function getReward() {
        $result = $this->_get('Reward');
        return $result;
    }

    public function getRewardBadge() {
        $result = $this->_get('Reward/badge');
        return $result;
    }

    public function getRewardBadgeLog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/badge/log', $param);
        return $result;
    }

    public function getRewardPointLog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/point/log', $param);
        return $result;
    }

    public function getRewardExpLog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/exp/log', $param);
        return $result;
    }

    public function getRewardLevelLog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Reward/level/log', $param);
        return $result;
    }

    public function getUserRegLog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Player/new', $param);
        return $result;
    }

    public function getUserDAULog($startDate, $endDate) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );
        $result = $this->_get('Player/dau_per_day', $param);
        return $result;
    }

    public function getUserMAULog($startDate, $endDate, $userUnitType) {
        $param = array(
            'from' => $startDate,
            'to' => $endDate
        );

        switch($userUnitType){
            case 'day':
                $result = $this->_get('Player/mau_per_day', $param);
                break;
            case 'week':
                $result = $this->_get('Player/mau_per_week', $param);
                break;
            case 'month':
                $result = $this->_get('Player/mau_per_month', $param);
                break;
        }
        return $result;
    }

    public function addContent($Params=array()) {
        $result = $this->_post('Content/addContent/', $Params);
        return $result;
    }

    public function playQuest($params) {
        $result = $this->_post('Engine/quest', $params);
        return $result;
    }

    private function _get($uri,$params = array()) {
        $defaultParam = array('api_key' => $this->api_key);
        $sendParam = array_merge($defaultParam, $params);
        $result = $this->_restClient->get($uri, $sendParam);
        return $result;
    }

    private function _post($uri,$params = array()) {
        $defaultParam = array('token' => $this->token);
        $sendParam = array_merge($defaultParam, $params);
        $result = $this->_restClient->post($uri, $sendParam);
        return $result;
    }

    public function setHeader($header, $content = NULL) {
        $this->_restClient->http_header($header,$content);
    }
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}