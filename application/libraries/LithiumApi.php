<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('REST_API', '/restapi/vc/');
define('JSON_RESPONSE', 'json');

class LithiumApi {

    private $_restClient;
    private $_cacheClient;
    private $lithiumUrl;
    private $response;
    private $sessionKey;

    public function __construct($config = array())
    {
        $ci =& get_instance();
        $this->_restClient = $ci->restclient;
        $this->response = array('restapi.response_format' => JSON_RESPONSE, 'restapi.format_detail' => 'full_list_element');
        $this->callback = array('event.callback_url' => base_url().'/notification');
    }

    public function initialize($lithiumUrl, $token = null) {
        $this->lithiumUrl = $lithiumUrl;
        $this->_restClient->initialize(array('server' => $this->lithiumUrl));
        if ($token) {
            $this->setHttpAuth('bearer', $token, null);
        }
    }

    private function isSuccess($result) {
        return isset($result->response->status) && $result->response->status == 'success';
    }

    private function getError($result) {
        return isset($result->response->error) ? $result->response->error : null;
    }

    // GET authentication/sessions/login
    public function login($login, $password) {
        $result = $this->_get('authentication/sessions/login', array_merge(array(
            'user.login' => $login,
            'user.password' => $password,
        ), $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "login" failed: '.print_r($this->getError($result),true));
        $sessionKey = $result->response->value->{'$'};
        $this->sessionKey = array('restapi.session_key' => $sessionKey);
        return $sessionKey;
    }

    // GET authentication/sessions/logout
    public function logout() {
        $result = $this->_get('authentication/sessions/logout', array_merge($this->sessionKey, $this->response));
        $this->sessionKey = null;
        $success = $this->isSuccess($result);
        if (!$success) throw new Exception('[Lithium] "logout" failed: '.print_r($this->getError($result),true));
        return $success;
    }

    // GET authentication/sessions/current/id
    public function current_id() {
        $result = $this->_get('authentication/sessions/current/id', array_merge($this->sessionKey, $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "current_id" failed: '.print_r($this->getError($result),true));
        return $result->response->value->{'$'};
    }

    // GET authentication/sessions/current/user
    public function current_user() {
        $result = $this->_get('authentication/sessions/current/user', array_merge($this->sessionKey, $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "current_user" failed: '.print_r($this->getError($result),true));
        return $result->response->user;
    }

    public function newToken($clientId, $clientSecret, $code) {
        $this->setHttpAuth('basic', $clientId, $clientSecret);
        $result = $this->_post('oauth2/token', array('code' => $code, 'grant_type' => 'authorization_code'));
        return $result;
    }

    public function refreshToken($clientId, $clientSecret, $refreshToken) {
        $this->setHttpAuth('basic', $clientId, $clientSecret);
        $result = $this->_post('oauth2/token', array('refresh_token' => $refreshToken, 'grant_type' => 'refresh_token'));
        return $result;
    }

    public function setHttpAuth($type, $username, $password) {
        $this->_restClient->set_http_auth($type, $username, $password);
    }

    public function subscriptions() {
        $result = $this->_get('events/subscriptions', array_merge($this->sessionKey, $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "subscriptions" failed: '.print_r($this->getError($result),true));
        return $result->response->eventsubscriptions->eventsubscription;
    }

    public function subscribe($eventId) {
        $result = $this->_post('events/subscriptions/events/name/'.$eventId.'/subscribe'.'?'.$this->build_query_string(array_merge($this->sessionKey, $this->response, $this->callback)));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "subscribe" failed: '.print_r($this->getError($result),true));
        return $result;
    }

    public function unsubscribe($token) {
        $result = $this->_post('events/subscriptions/token/'.$token.'/unsubscribe'.'?'.$this->build_query_string(array_merge($this->sessionKey, $this->response)));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "unsubscribe" failed: '.print_r($this->getError($result),true));
        return $result;
    }

    public function user($id) {
        $result = $this->_get('users/id/'.$id, array_merge($this->sessionKey, $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "user" failed: '.print_r($this->getError($result),true));
        return $result->response->user;
    }

    public function kudosGivers($messageId) {
        $result = $this->_get('messages/id/'.$messageId.'/kudos/givers', array_merge($this->sessionKey, $this->response));
        if (!$this->isSuccess($result)) throw new Exception('[Lithium] "kudosGivers" failed: '.print_r($this->getError($result),true));
        return $result->response->users;
    }

    private function build_query_string($arr) {
        $s = null;
        if (is_array($arr)) {
            $_arr = array();
            foreach ($arr as $k => $v) $_arr[] = $k.'='.$v;
            $s = implode('&', $_arr);
        }
        return $s;
    }

    private function _get($uri, $params = array()) {
        $result = $this->_restClient->get(REST_API.$uri, $params);
        return $result;
    }

    private function _post($uri, $params = array(), $format = null) {
        $result = $format && $format == 'json' ? $this->_restClient->post(REST_API.$uri, json_encode($params), 'json') : $this->_restClient->post(REST_API.$uri, $params);
        return $result;
    }

    private function _delete($uri, $params = array()) {
        $result = $this->_restClient->delete(REST_API.$uri, $params);
        return $result;
    }
}
?>