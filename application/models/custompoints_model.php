<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custompoints_model extends MY_Model
{

	public function insertCustompoints($data){

		$field1 = array(
			"field_type" => "read_only",
			"label" => "Name",
			"param_name" => "reward_name",
			"placeholder" => "",
			"sortOrder" => "0",
			"value" => strtolower($data['name']), 
		);

		$field2 = array(
			"param_name" => "item_id",
			"label" => "",
			"placeholder" => "",
			"sortOrder" => "0",
			"field_type" => "hidden",
			"value" => "",
		);

		$field3 = array(
			"field_type" => "number",
			"label" => strtolower($data['name']),
			"param_name" => "quantity",
			"placeholder" => "How many ...",
			"sortOrder" => "0",
			"value" => "0",
		);

		$insert = $this->mongo_db->insert('playbasis_reward_to_client', array(
		    'reward_id' => new MongoId(),
		    'client_id' => $data['client_id'],
		    'site_id' => $data['site_id'],
		    'group' => 'POINT',
		    'name' => strtolower($data['name']),
            'limit' => null,
            'description' => null,
            'sort' => 1,
            'status' => true,
            'is_custom' => true,
            'init_dataset'=>array($field1,$field2,$field3),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
		));

		return $insert;
	}

	public function getCustompoints($data){
		$this->set_site_mongodb($this->session->userdata('site_id'));

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$regex = new MongoRegex("/".preg_quote(utf8_strtolower($data['filter_name']))."/i");
			$this->mongo_db->where('name', $regex);
		}

		$sort_data = array(
			'_id',
			'name',
			'status',
			'sort_order'
		);

		if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
			$order = -1;
		} else {
			$order = 1;
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$this->mongo_db->order_by(array($data['sort'] => $order));
		} else {
			$this->mongo_db->order_by(array('name' => $order));
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

		$this->mongo_db->where('client_id', $data['client_id']);
		$this->mongo_db->where('site_id', $data['site_id']);
		$this->mongo_db->where('is_custom', true);
		$this->mongo_db->where('status', true);
		return $this->mongo_db->get("playbasis_reward_to_client");
	}

	public function countCustompoints($client_id, $site_id){
		$this->mongo_db->where('client_id', new MongoId($client_id));
		$this->mongo_db->where('site_id', new MongoId($site_id));
		$this->mongo_db->where('is_custom', true);
		$this->mongo_db->where('status', true);
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

		$update = $this->mongo_db->update('playbasis_reward_to_client');

        // update rule engine //
        $this->mongo_db->where(array('jigsaw_set.specific_id' => $data['reward_id']));
        $this->mongo_db->set(array('jigsaw_set.$.name' => $data['name']));
        $this->mongo_db->set(array('jigsaw_set.$.dataSet.0.value' => $data['name']));
        $this->mongo_db->set(array('jigsaw_set.$.config.reward_name' => $data['name']));
        $this->mongo_db->update_all('playbasis_rule');
        // end update rule engine //

		return $update;
	}

	public function deleteCustompoints($custompoint_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('reward_id', new MongoID($custompoint_id));
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_reward_to_client');
        
    }

}	