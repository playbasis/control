<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_model extends MY_Model
{
    public function insertCampaign($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();

        $insert_Data = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => $data['name'],
            'image' => $data['image'],
            'date_start' => $data['date_start'],
            'date_end' => $data['date_end'],
            'weight' => $data['weight'],
            'date_modified' => $d,
            'date_added' => $d,
            'deleted' => false,
        );

        return $this->mongo_db->insert('playbasis_campaign_to_client', $insert_Data);
    }

    public function updateCampaign($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();

        $this->mongo_db->where('client_id', new MongoId($data['client_id']));
        $this->mongo_db->where('site_id', new MongoId($data['site_id']));
        $this->mongo_db->where('_id', new MongoId($data['_id']));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('image', $data['image']);
        $this->mongo_db->set('date_start', $data['date_start']);
        $this->mongo_db->set('date_end', $data['date_end']);
        $this->mongo_db->set('weight', $data['weight']);

        return $this->mongo_db->update('playbasis_campaign_to_client');
    }

    public function getCampaign($client_id, $site_id, $data=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);

        if(isset($data['campaign_id']) && !empty($data['campaign_id'])){
            $this->mongo_db->where('_id', $data['campaign_id']);
        }
        if(isset($data['name']) && !empty($data['name'])){
            $this->mongo_db->where('name', $data['name']);
        }

        if (isset($data['limit'])) {
            $this->mongo_db->limit((int)$data['limit']);
        } else {
            $this->mongo_db->limit(10);
        }

        if (isset($data['offset'])) {
            $this->mongo_db->offset((int)$data['offset']);
        } else {
            $this->mongo_db->offset(0);
        }

        $this->mongo_db->order_by(array('weight' => 'asc','date_start' => 'asc','name' => 'asc'));
        $results = $this->mongo_db->get("playbasis_campaign_to_client");
        return $results;
    }

    public function countCampaign($client_id, $site_id, $data=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);

        if(isset($data['campaign_id']) && !empty($data['campaign_id'])){
            $this->mongo_db->where('_id', $data['campaign_id']);
        }
        if(isset($data['name']) && !empty($data['name'])){
            $this->mongo_db->where('name', $data['name']);
        }

        $results = $this->mongo_db->count("playbasis_campaign_to_client");

        return $results;
    }

    public function deleteCampaign($campaign_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', $campaign_id);
        $this->mongo_db->set('deleted', true);
        $results = $this->mongo_db->update("playbasis_campaign_to_client");

        if($results){
            $this->mongo_db->where('campaign_id', $campaign_id);
            $this->mongo_db->set('deleted', true);
            $results = $this->mongo_db->update_all("playbasis_game_campaign_to_client");
        }

        return $results;
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
        $this->mongo_db->where(array('$and' => array( array('$or' => array(array("date_start" => null), array("date_start" => array('$lte'=> $d)))),
                                                      array('$or' => array(array("date_end" => array('$gte'=> $d)), array("date_end" => null))))));
        $this->mongo_db->order_by(array('weight' => 'desc','date_start' => 'desc', "date_end" => 'asc' , 'name' => 'asc'));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_campaign_to_client');

        return $result ? $result[0]:array();
    }
}
