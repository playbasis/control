<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
	}
	public function findAction($data)
	{
		$this->db->select('action_id');
		$this->db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'name' => strtolower($data['action_name'])
		));
		$result = db_get_row_array($this, 'playbasis_action_to_client');
		if($result)
			return $result['action_id'];
		return array();
	}
}
?>