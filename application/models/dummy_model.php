<?php 
class Dummy_model extends CI_Model{
	public function dummyAddPlayer($data){
		$this->db->insert('playbasis_player',$data);
	}

	public function getActionToClient($cid,$sid){
		$this->db->select('name');
		$this->db->where(array('client_id'=>$cid,'site_id'=>$sid));
		$result = $this->db->get('playbasis_action_to_client');

		return $result->result_array();
	}

	public function getRandomPlayer($cid,$sid){
		// $this->db->select('cl_player_id');
		// $this->db->where(array('client_id'=>$cid,'site_id'=>$sid));
		// $this->db->order_by('pb_player_id','random');
		// $result = $this->db->get('playbasis_player',0,100);

		$sql  = "SELECT `cl_player_id` FROM `playbasis_player` WHERE `client_id` = $cid AND `site_id` = $sid ORDER BY RAND() LIMIT 0,2000";
			
		$result = $this->db->query($sql);

		return $result->result_array();
	}
}
?>