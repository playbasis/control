<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Point_model extends CI_Model
{
	public function getRewardNameById($data)
	{
		$this->db->select('name');
		$this->db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'reward_id' => $data['reward_id']
		));
		$result = $this->db->get('playbasis_reward_to_client');
		$result = $result->row_array();
		return $result['name'];
	}
	public function findPoint($data)
	{
		$this->db->select('reward_id');
		$this->db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'name' => strtolower($data['reward_name'])
		));
		$result = $this->db->get('playbasis_reward_to_client');
		$result = $result->row_array();
		if($result)
			return $result['reward_id'];
		return array();
	}
}
?>