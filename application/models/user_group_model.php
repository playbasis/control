<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_model extends MY_model{

	public function __construct(){
		parent::__construct();
		$this->load->library('mongo_db');

	}

	public function getTotalNumUsers(){
		return $this->mongo_db->count('user_group');
	}

	public function fetchAllUserGroups($data){
		$this->set_site_mongodb(0);

		if(isset($data['filter_name']) && !is_null($data['filter_name'])){
			//Do something if filter name is set..
		}

		if(isset($data['start']) || isset($data['limit'])){
			if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
		}

		$results = $this->mongo_db->get('user_group');

		return $results;

	}

	public function getUserGroupInfo($user_group_id){
		$this->mongo_db->where('_id', new MongoID($user_group_id));
		$results = $this->mongo_db->get('user_group');

		return ($results)? $results[0]:null;
	}

	public function getAllFeatures(){
		return $this->mongo_db->get('playbasis_feature');
	}


}