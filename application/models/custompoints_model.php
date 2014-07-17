<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custompoints_model extends MY_Model
{

	public function insertCustompoints($data){

		$insert = $this->mongo_db->insert('playbasis_reward_to_client', array(
		    'reward_id' => new MongoId(),
		    'client_id' => $data['client_id'],
		    'site_id' => $data['site_id'],
		    'group' => 'POINT',
		    'name' => strtolower($data['name']),
            'limit' => null,
            'description' => null,
            'sort' => 1,
            'status' => (bool)$data['status'],
            'is_custom' => true,
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
		));

		return $insert;
	}

	public function getCustompoints($client_id, $site_id){

		$this->mongo_db->where('client_id', new MongoId($client_id));
		$this->mongo_db->where('site_id', new MongoId($site_id));
		$allCustomPoints = $this->mongo_db->get('playbasis_reward_to_client');

		return $allCustomPoints;

	}

	public function countCustompoints($client_id, $site_id){
		$this->mongo_db->where('client_id', new MongoId($client_id));
		$this->mongo_db->where('site_id', new MongoId($site_id));
		$this->mongo_db->where('is_custom', true);
		$countCustompoints = $this->mongo_db->count('playbasis_reward_to_client');

		return $countCustompoints;
	}

	public function getCustompoint($custompoint_id){

		$this->mongo_db->where('reward_id', new MongoId($custompoint_id));
		$c = $this->mongo_db->get('playbasis_reward_to_client');

		if ($c){
			return $c[0];	
		}else{
			return null;
		}
	}

	public function updateCustompoints($data){

		$this->mongo_db->where('reward_id',  new MongoID($data['reward_id']));
		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));

		$this->mongo_db->set('name', $data['name']);
		$this->mongo_db->set('status', (bool)$data['status']);
		

		$update = $this->mongo_db->update('playbasis_reward_to_client');

		return $update;
	}

	public function deleteCustompoints($custompoint_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('reward_id', new MongoID($custompoint_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_reward_to_client');
        
    }

}	