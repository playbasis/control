<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lithium_model extends MY_Model {

    protected $events = array(
        array('id' => 'jive:UserRegistered', 'type' => 'user', 'description' => 'User has registered'),
        array('id' => 'jive:UserCreate', 'type' => 'user', 'description' => 'User account has been created'),
        array('id' => 'jive:UserSignOn', 'type' => 'user', 'description' => 'User has signed on'),
        array('id' => 'jive:UserUpdate', 'type' => 'user', 'description' => 'User profile has been updated'),
        array('id' => 'jive:UserSignOff', 'type' => 'user', 'description' => 'User has signed off'),
        array('id' => 'jive:MessageCreate', 'type' => 'message', 'description' => 'Message has been created'),
        array('id' => 'jive:MessageUpdate', 'type' => 'message', 'description' => 'Message has been edited'),
        array('id' => 'jive:MessageMove', 'type' => 'message', 'description' => 'Message has been moved'),
        array('id' => 'jive:MessageDelete', 'type' => 'message', 'description' => 'Message has been deleted'),
        array('id' => 'jive:MessageRootPublished', 'type' => 'message', 'description' => 'Message has been published'),
    );

    public function hasValidRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_lithium_to_client") > 0;
    }

    public function getRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('login', 'password', 'basicAuth', 'tenant_id', 'lithium_client_id', 'lithium_client_secret', 'lithium_url', 'token'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_lithium_to_client");
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
        $this->mongo_db->update("playbasis_lithium_to_client");
    }

    public function hasToken($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_exists('token');
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_lithium_to_client") > 0;
    }

    public function listEvents($site_id, $per_page, $offset) {
        return array_slice($this->events, $offset, $per_page);
    }

    public function totalEvents($site_id) {
        return count($this->events);
    }

    public function getEventType($eventId) {
        foreach ($this->events as $event) {
            if ($event['id'] == $eventId) return $event['type'];
        }
        return false;
    }
}
?>