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
		$this->set_site($data['site_id']);
		$this->site_db()->select('name');
		$this->site_db()->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'reward_id' => $data['reward_id']
		));
		$result = db_get_row_array($this, 'playbasis_reward_to_client');
		return $result['name'];
	}
	public function findPoint($data)
	{
		$this->set_site($data['site_id']);
		$this->site_db()->select('reward_id');
		$this->site_db()->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'name' => strtolower($data['reward_name'])
		));
		$result = db_get_row_array($this, 'playbasis_reward_to_client');
		if($result)
			return $result['reward_id'];
		return array();
	}
}
?>