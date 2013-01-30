<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends CI_Model{
	public function findAction($data){
		$this->db->select('action_id');
		$this->db->where(array('client_id'=>$data['client_id'],'site_id'=>$data['site_id'],'name'=>strtolower($data['action_name'])));
		$result = $this->db->get('playbasis_action_to_client');

		$result = $result->row_array();

		if($result)
			return $result['action_id'];

		return array();
	}
}
?>