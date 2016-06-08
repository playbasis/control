<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fitbit_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getFitbitPlayer($client_id, $site_id, $pb_player_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $result = $this->mongo_db->get('playbasis_fitbit');
        return $result[0];
    }

    public function addFitbitPlayer($client_id, $site_id, $pb_player_id, $fitbit_token, $subscription_id)
    {
        $insert_data = array(
            'client_id' => new MongoID($client_id),
            'site_id' => new MongoID($site_id),
            'pb_player_id' => new MongoID($pb_player_id),
            'subscription_id' => $subscription_id,
            'fitbit_token' => $fitbit_token,
            'data_added' => new MongoDate(),
            'data_modified' => new MongoDate(),
            'status' => true,
            'deleted' => false
        );
        return $this->mongo_db->insert('playbasis_fitbit', $insert_data);
    }

    public function updateFitbitPlayer($client_id, $site_id, $pb_player_id, $fitbit_token, $subscription_id=null)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        if($subscription_id != null){
            $this->mongo_db->set('subscription_id', $subscription_id);
        }
        $this->mongo_db->set('fitbit_token', $fitbit_token);
        $this->mongo_db->set('data_modified', new MongoDate());
        return $this->mongo_db->update('playbasis_fitbit');
    }

    public function deleteFitbitPlayer($client_id, $site_id, $pb_player_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('data_modified', new MongoDate());
        return $this->mongo_db->update('playbasis_fitbit');
    }


}