<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('VALID_RESPONSE', "throw 'allowIllegalResourceCall is false.';");

class JiveApi {

    private $_restClient;
    private $_cacheClient;

    public function __construct($config = array())
    {
        $ci =& get_instance();
        $this->_restClient = $ci->rest;
    }

    public function initialize($jiveUrl, $token = null) {
        $this->_restClient->initialize(array('server' => $jiveUrl));
        if ($token) {
            $this->_restClient->set_http_auth('bearer', $token, null);
            if (!$this->isValidResponse()) throw new Exception('TOKEN_EXPIRED');
        }
    }

    public function newToken($clientId, $clientSecret, $code) {
        $this->_restClient->set_http_auth('basic', $clientId, $clientSecret);
        $result = $this->_post('oauth2/token', array('code' => $code, 'grant_type' => 'authorization_code'));
        return $result;
    }

    public function refreshToken($clientId, $clientSecret, $refreshToken) {
        $this->_restClient->set_http_auth('basic', $clientId, $clientSecret);
        $result = $this->_post('oauth2/token', array('refresh_token' => $refreshToken, 'grant_type' => 'refresh_token'));
        return $result;
    }

    public function isValidResponse($result = null) {
        if (!$result) $result = $this->_get('api/core/v3/webhooks');
        return strpos($result, VALID_RESPONSE) === false ? false : true;
    }

    public function listWebhooks() {
        $result = $this->_get('api/core/v3/webhooks');
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    public function listPlaces() {
        $result = $this->_get('api/core/v3/places');
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    private function _get($uri,$params = array()) {
        $result = $this->_restClient->get($uri, $params);
        return $result;
    }

    private function _post($uri,$params = array()) {
        $result = $this->_restClient->post($uri, $params);
        return $result;
    }
}
?>