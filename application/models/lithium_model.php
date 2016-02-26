<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lithium_model extends MY_Model
{

    protected $events = array(
        array('id' => 'UserRegistered', 'type' => 'user', 'description' => 'User has registered'),
        array('id' => 'UserSignOn', 'type' => 'user', 'description' => 'User has signed on'),
        array('id' => 'UserUpdate', 'type' => 'user', 'description' => 'User profile has been updated'),
        array('id' => 'MessageCreate', 'type' => 'message', 'description' => 'Message has been created'),
        array('id' => 'MessageUpdate', 'type' => 'message', 'description' => 'Message has been edited'),
        array('id' => 'MessageDelete', 'type' => 'message', 'description' => 'Message has been deleted'),
        /*array('id' => 'UserCreate', 'type' => 'user', 'description' => 'User account has been created'),
        array('id' => 'UserSignOff', 'type' => 'user', 'description' => 'User has signed off'),
        array('id' => 'ImageCreated', 'type' => 'image', 'description' => 'Image has been created'),
        array('id' => 'ImageUpdated', 'type' => 'image', 'description' => 'Image has been update'),
        array('id' => 'MessageMove', 'type' => 'message', 'description' => 'Message has been moved'),
        array('id' => 'MessageRootPublished', 'type' => 'message', 'description' => 'Message has been published'),
        array('id' => 'EscalateThread', 'type' => 'thread', 'description' => 'Thread has been escalated'),
        array('id' => 'SendPrivateMessage', 'type' => 'message', 'description' => 'Private message has been sent'),*/
    );

    public function hasValidRegistration($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_lithium_to_client") > 0;
    }

    public function getRegistration($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array(
            'lithium_url',
            'lithium_username',
            'lithium_password',
            'http_auth_username',
            'http_auth_password',
            'tenant_id',
            'lithium_client_id',
            'lithium_client_secret',
            'token'
        ));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_lithium_to_client");
        return $results ? $results[0] : null;
    }

    public function insertRegistration($site_id, $lithium)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        return $this->mongo_db->insert('playbasis_lithium_to_client', array(
            'site_id' => $site_id,
            'lithium_url' => $lithium['lithium_url'],
            'lithium_username' => $lithium['lithium_username'],
            'lithium_password' => $lithium['lithium_password'],
            'http_auth_username' => isset($lithium['http_auth_username']) ? $lithium['http_auth_username'] : null,
            'http_auth_password' => isset($lithium['http_auth_password']) ? $lithium['http_auth_password'] : null,
            'date_added' => $date_added,
            'date_modified' => $date_added,
        ));
    }

    public function updateRegistration($site_id, $lithium)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->set('lithium_url', $lithium['lithium_url']);
        $this->mongo_db->set('lithium_username', $lithium['lithium_username']);
        $this->mongo_db->set('lithium_password', $lithium['lithium_password']);
        if (isset($lithium['http_auth_username'])) {
            $this->mongo_db->set('http_auth_username', $lithium['http_auth_username']);
        }
        if (isset($lithium['http_auth_password'])) {
            $this->mongo_db->set('http_auth_password', $lithium['http_auth_password']);
        }
        $this->mongo_db->set('date_modified', $date_modified);
        $this->mongo_db->update('playbasis_lithium_to_client');
    }

    public function updateToken($site_id, $token)
    {
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

    public function listEvents($site_id)
    {
        return $this->events;
    }

    public function getEventType($eventId)
    {
        foreach ($this->events as $event) {
            if ($event['id'] == $eventId) {
                return $event['type'];
            }
        }
        return false;
    }

    public function saveSubscriptions($site_id, $subscriptions)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->delete_all("playbasis_lithium_subscription");
        if (!$subscriptions) {
            return null;
        }
        return $this->mongo_db->batch_insert('playbasis_lithium_subscription', $subscriptions,
            array("w" => 0, "j" => false));
    }
}

?>