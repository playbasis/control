<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
		$this->load->library('mongo_db');
	}
	public function findAction($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array('action_id'));
		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'name' => strtolower($data['action_name'])
		));
		$result = $this->mongo_db->get('playbasis_action_to_client');
		if($result && $result[0])
		{
			unset($result[0]['_id']);
			return $result[0]['action_id'];
		}
		return array();
	}
}
?>