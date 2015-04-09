<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('VALID_RESPONSE', "throw 'allowIllegalResourceCall is false.';");
define('JIVE_RECORDS_PER_PAGE', 100);

class JiveApi {

    private $_restClient;
    private $_cacheClient;
    private $jiveUrl;

    public function __construct($config = array())
    {
        $ci =& get_instance();
        $this->_restClient = $ci->rest;
    }

    public function initialize($jiveUrl, $token = null) {
        $this->jiveUrl = $jiveUrl;
        $this->_restClient->initialize(array('server' => $this->jiveUrl));
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
        if (is_object($result) && isset($result->message)) {
            log_message('error', 'JiveApi, response = '.$result->message);
            return false;
        }
        if (is_object($result) && isset($result->error)) {
            throw new Exception($result->error->message.' ('.$result->error->status.')');
        }
        return !is_object($result) && strpos($result, VALID_RESPONSE) === false ? false : true;
    }

    public function listWebhooks($recordsPerPage, $offset) {
        $q = array('count' => $recordsPerPage, 'startIndex' => $offset);
        $result = $this->_get('api/core/v3/webhooks', $q);
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    public function totalWebhooks() {
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

    public function createContentWebhook($placeId) {
        $result = $this->_post('api/core/v3/webhooks', array('callback' => API_SERVER.'/notification', 'object' => $this->jiveUrl.'/api/core/v3/places/'.$placeId));
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        return $result;
    }

    public function createSystemWebhook($type, $action) {
        $result = $this->_post('api/core/v3/webhooks', array('callback' => API_SERVER.'/notification', 'events' => is_array($type) ? implode(',', $type) : $type, 'verb' => $action));
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        return $result;
    }

    public function deleteWebhook($webhookId) {
        $result = $this->_delete('api/core/v3/webhooks/'.$webhookId);
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    public function listPlaces($recordsPerPage, $offset, $type=null) {
        $q = array('fields' => 'placeID,name,description,type,followerCount,viewCount,creator,status', 'count' => $recordsPerPage, 'startIndex' => $offset, 'sort' => 'titleAsc');
        if ($type) $q = array_merge($q, array('filter' => 'type('.(is_array($type) ? implode(',', $type) : $type).')'));
        $result = $this->_get('api/core/v3/places', $q);
        if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
        $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
        return $result;
    }

    public function totalPlaces($type=null) {
        $offset = 0;
        for (;;) {
            $q = array('fields' => 'id', 'count' => JIVE_RECORDS_PER_PAGE, 'startIndex' => $offset);
            if ($type) $q = array_merge($q, array('filter' => 'type('.(is_array($type) ? implode(',', $type) : $type).')'));
            $result = $this->_get('api/core/v3/places', $q);
            if (!$this->isValidResponse($result)) throw new Exception('TOKEN_EXPIRED');
            $result = json_decode(str_replace(VALID_RESPONSE, '', $result));
            $offset += count($result->list);
            if (!isset($result->links->next)) break;
        }
        return $offset;
    }

    private function _get($uri, $params = array()) {
        $result = $this->_restClient->get($uri, $params);
        return $result;
    }

    private function _post($uri, $params = array()) {
        $result = $this->_restClient->post($uri, json_encode($params), 'json');
        return $result;
    }

    private function _delete($uri, $params = array()) {
        $result = $this->_restClient->delete($uri, $params);
        return $result;
    }
}
?>