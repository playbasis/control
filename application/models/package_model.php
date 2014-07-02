<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Package_model extends MY_Model
{

	public function getCurrentPlan($client_id, $site_id){
		$this->mongo_db->where('client_id', $client_id);
		$this->mongo_db->where('site_id', $site_id);

		$permission = $this->mongo_db->get('playbasis_permission');

		$planId = $permission[0]['plan_id'];

		$this->mongo_db->where('_id', new MongoId($planId));
		return $this->mongo_db->get('playbasis_plan');

	}

	public function getLimitPlayers($client_id, $site_id){
		$this->mongo_db->where('client_id', $client_id);
		$this->mongo_db->where('_id', $site_id);

		return $this->mongo_db->get('playbasis_client_site');
	}

	public function getAllPlans(){
		return $this->mongo_db->get('playbasis_plan');
	}
	

}