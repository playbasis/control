<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends CI_Model
{
	public function findAction($data)
	{

//        $this->db->select('action_id');
//        $this->db->where(array(
//            'client_id' => $data['client_id'],
//            'site_id' => $data['site_id'],
//            'name' => strtolower($data['action_name'])
//        ));
//        $result = $this->db->get('playbasis_action_to_client');
//        $result = $result->row_array();
//        if($result)
//            return $result['action_id'];
//        return array();

        $this->load->library('memcached_library');

        // name for memcached
        $sql = "SELECT action_id FROM playbasis_action_to_client WHERE client_id = ".$data['client_id']." AND site_id = ".$data['site_id']." AND name = ".strtolower($data['action_name']);
        $md5name = md5($sql);
        $table = "playbasis_action_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results['action_id'];


        // so if cannot get any result
        $this->db->select('action_id');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['action_name'])
        ));
        $result = $this->db->get('playbasis_action_to_client');
        $result = $result->row_array();

        $this->memcached_library->add('sql_' . $md5name.".".$table, $result);

        if($result)
            return $result['action_id'];
        return array();

	}
}
?>