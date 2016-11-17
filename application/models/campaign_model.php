<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getCampaign($client_id, $site_id)
    {
        //get badge name by $badge_id
        $this->set_site_mongodb($site_id);
        $d = new MongoDate();
        $this->mongo_db->select(array('name','image','date_start','date_end','weight'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false
        ));
        $this->mongo_db->order_by(array('weight' => 'ASC', 'name' => 'ASC'));
        $result = $this->mongo_db->get('playbasis_campaign_to_client');

        return $result;
    }

    public function getActiveCampaign($client_id, $site_id)
    {
        //get badge name by $badge_id
        $this->set_site_mongodb($site_id);
        $d = new MongoDate();
        $this->mongo_db->select(array('name','image','date_start','date_end','weight'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false
        ));
        $this->mongo_db->where_lte('date_start', $d);
        $this->mongo_db->where_gte('date_end', $d);
        $this->mongo_db->order_by(array('weight' => 'ASC', 'name' => 'ASC'));
        $result = $this->mongo_db->get('playbasis_campaign_to_client');

        return $result ? $result[0]:array();
    }
}