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
        $this->_restClient = $ci->rest;
        $this->response = array('restapi.response_format' => JSON_RESPONSE, 'restapi.format_detail' => 'full_list_element');
    }

    public function initialize($lithiumUrl, $token = null) {
        $this->lithiumUrl = $lithiumUrl;
        $this->_restClient->initialize(array('server' => $this->lithiumUrl));
        if ($token) {
            $this->setHttpAuth('bearer', $token, null);
            if (!$this->isValidResponse()) throw new Exception('TOKEN_EXPIRED');
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

    public function isValidResponse($result = null) {
        if (!$result) $result = $this->_get('api/core/v3/webhooks');
        if (is_object($result) && isset($result->message)) {
            log_message('error', 'JiveApi, response = '.$result->message);
            return false;
        }
        if (is_object($result) && isset($result->error)) {
            throw new Exception($result->error->message.' ('.$result->error->status.')');
        }
        return !is_object($result) && strpos($result, VALID_RESPONSE) === false ? false : true;
    }

    public function listSubscriptions($recordsPerPage, $offset) {
        $q = array('count' => $recordsPerPage, 'startIndex' => $offset);
        $result = $this->_get('api/core/v3/webhooks', $q);
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    public function totalSubscriptions() {
        $offset = 0;
        for (;;) {
            $q = array('fields' => 'id', 'count' => JIVE_RECORDS_PER_PAGE, 'startIndex' => $offset);
            $result = $this->_get('api/core/v3/webhooks', $q);
            if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
            $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
            $offset += count($result->list);
            if (!isset($result->links->next)) break;
        }
        return $offset;
    }

    public function subscribeEvent($type, $action) {
        $result = $this->_post('api/core/v3/webhooks', array('callback' => API_SERVER.'/notification', 'events' => is_array($type) ? implode(',', $type) : $type, 'verb' => $action), 'json');
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        return $result;
    }

    public function unsubscribeEvent($webhookId) {
        $result = $this->_delete('api/core/v3/webhooks/'.$webhookId);
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    private function _get($uri, $params = array()) {
        $result = $this->_restClient->get(REST_API.$uri, $params);
        return $result;
    }

    private function _post($uri, $params = array(), $format = null) {
        $result = $format && $format == 'json' ? $this->_restClient->post(REST_API.$uri, json_encode($params), 'json') : $this->_restClient->post($uri, $params);
        return $result;
    }

    private function _delete($uri, $params = array()) {
        $result = $this->_restClient->delete(REST_API.$uri, $params);
        return $result;
    }
}
?>