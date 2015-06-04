<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lithium_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function findSubscription($token) {
        $this->mongo_db->select(array('client_id', 'site_id', 'event'));
        $this->mongo_db->where('token', $token);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_lithium_subscription');
        return $result ? $result[0] : array();
    }

    public function getRegistration($site_id) {
        $this->mongo_db->select(array('lithium_url', 'lithium_username', 'lithium_password', 'http_auth_username', 'http_auth_password', 'tenant_id', 'lithium_client_id', 'lithium_client_secret', 'token'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_lithium_to_client");
        return $results ? $results[0] : null;
    }
}
?>