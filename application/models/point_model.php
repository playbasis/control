<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Point_model extends CI_Model
{
	public function getRewardNameById($data)
	{
//		$this->db->select('name');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id'],
//			'reward_id' => $data['reward_id']
//		));
//		$result = $this->db->get('playbasis_reward_to_client');
//		$result = $result->row_array();
//		return $result['name'];

        $this->load->library('memcached_library');

        // name for memcached
        $sql = "SELECT name FROM playbasis_reward_to_client WHERE reward_id = ".$data['reward_id']." AND client_id = ".$data['client_id']." AND site_id = ".$data['site_id'];
        $md5name = md5($sql);
        $table = "playbasis_reward_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        // so if cannot get any result
        $this->db->select('name');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'reward_id' => $data['reward_id']
        ));
        $result = $this->db->get('playbasis_reward_to_client');
        $result = $result->row_array();

        $this->memcached_library->add('sql_' . $md5name.".".$table, $result['name']);

        return $result['name'];
	}
	public function findPoint($data)
	{
//		$this->db->select('reward_id');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id'],
//			'name' => strtolower($data['reward_name'])
//		));
//		$result = $this->db->get('playbasis_reward_to_client');
//		$result = $result->row_array();
//		if($result)
//			return $result['reward_id'];
//		return array();

        $this->load->library('memcached_library');

        // name for memcached
        $sql = "SELECT reward_id FROM playbasis_reward_to_client WHERE name = ".strtolower($data['reward_name'])." AND client_id = ".$data['client_id']." AND site_id = ".$data['site_id'];
        $md5name = md5($sql);
        $table = "playbasis_reward_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        // so if cannot get any result
        $this->db->select('reward_id');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['reward_name'])
        ));
        $result = $this->db->get('playbasis_reward_to_client');
        $result = $result->row_array();
        if($result){
            $this->memcached_library->add('sql_' . $md5name.".".$table, $result['reward_id']);
            return $result['reward_id'];
        }

        $this->memcached_library->add('sql_' . $md5name.".".$table, array());

        return array();
	}
}
?>