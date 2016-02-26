<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lithium_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function findSubscription($token)
    {
        $this->mongo_db->select(array('client_id', 'site_id', 'event'));
        $this->mongo_db->where('token', $token);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_lithium_subscription');
        return $result ? $result[0] : array();
    }

    public function getRegistration($site_id)
    {
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

    public function getMessage($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('message_id', $data['message_id'] . '');
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_lithium_message_to_client");
        return $results ? $results[0] : null;
    }

    public function insertMessage($data, $message)
    {
        $d = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_lithium_message_to_client', array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'message_id' => $message->id . '',
            'kudos' => intval($message->kudos->count . ''),
            'date_added' => $d,
            'date_modified' => $d
        ));
    }

    public function updateMessage($data, $message_id, $message)
    {
        $d = new MongoDate(time());
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('message_id', $message_id . '');
        $this->mongo_db->set('kudos', intval($message->kudos->count . ''));
        $this->mongo_db->set('date_modified', $d);
        return $this->mongo_db->update_all('playbasis_lithium_message_to_client');
    }
}

?>