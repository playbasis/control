<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Googles_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getRegistration($site_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('google_client_id', 'google_client_secret', 'google_url', 'token'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_google_to_client");
        return $results ? $results[0] : null;
    }

    public function updateWebhook($site_id, $calendar_id, $resource_id, $resource_uri, $date_expire) {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('calendar_id', $calendar_id);
        $this->mongo_db->set('resource_id', $resource_id);
        $this->mongo_db->set('resource_uri', $resource_uri);
        $this->mongo_db->set('date_expire', $date_expire);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update('playbasis_google_subscription');
    }
}
?>