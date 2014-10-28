<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Report_reward_model extends MY_Model{


	public function getTotalReportReward($data){

        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        if (isset($data['action_id']) && $data['action_id'] != ''){
            $this->mongo_db->where( 'reward_id', new MongoID($data['action_id']));
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $this->mongo_db->where('event_type',  "REWARD");

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_modified', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        $results = $this->mongo_db->count("playbasis_event_log");

        return $results;
	}

	public function getReportReward($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        if (isset($data['action_id']) && $data['action_id'] != ''){
                $this->mongo_db->where( 'reward_id', new MongoID($data['action_id']));
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $this->mongo_db->where('event_type',  "REWARD");

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_modified', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results = $this->mongo_db->get("playbasis_event_log");

        return $results;
    }

    public function getRewardName($reward_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('reward_id', new MongoID($reward_id));
    	$var = $this->mongo_db->get('playbasis_reward_to_client');
    	return isset($var[0])?$var[0]:null;
    }

    public function getRewardsBadgesSite($data){

        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        return $this->mongo_db->get('playbasis_reward_to_client');

    }



}

?>