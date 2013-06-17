<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Point_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
	}
	public function getRewardNameById($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select('name');
		$this->mongo_db->where(array(
			'client_id' => intval($data['client_id']),
			'site_id' => intval($data['site_id']),
			'reward_id' => intval($data['reward_id'])
		));
		$result = $this->mongo_db->get('reward_to_client');
		return ($result) ? $result[0]['name'] : $result;
	}
	public function findPoint($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select('reward_id');
		$this->mongo_db->where(array(
			'client_id' => intval($data['client_id']),
			'site_id' => intval($data['site_id']),
			'name' => strtolower($data['reward_name'])
		));
		$result = $this->mongo_db->get('reward_to_client');
		if($result)
			return $result[0]['reward_id'];
		return array();
	}
}
?>