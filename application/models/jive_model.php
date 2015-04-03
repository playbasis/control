<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jive_model extends MY_Model {

    public function hasValidRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_jive_to_client") > 0;
    }

    public function getJiveRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('jive_tenant_id', 'jive_client_id', 'jive_client_secret', 'jive_url', 'token'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_jive_to_client");
        return $results ? $results[0] : null;
    }

    public function updateToken($site_id, $token) {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(time());
        $token['date_start'] = $d;
        $token['date_expire'] = new MongoDate($d->sec + $token['expires_in']);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->set('token', $token);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update("playbasis_jive_to_client");
    }

    public function hasToken($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_exists('token');
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_jive_to_client") > 0;
    }
}
?>