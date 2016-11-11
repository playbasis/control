<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getLocation($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'deleted' => false,
            'client_id' => $client_id,
            'site_id' => $site_id
        ));

        if(isset($data['location_id']) && $data['location_id']){
            $this->mongo_db->where('_id',new MongoID($data['location_id']));
        }

        if(isset($data['status']) && $data['status']){
            $this->mongo_db->where('_id',new MongoID($data['location_id']));
        }

        $result = $this->mongo_db->get('playbasis_location_to_client');

        return $result ? $result : array();
    }
}