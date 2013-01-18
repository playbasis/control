<?php 
class Dummy_model extends CI_Model{
	public function dummyAddPlayer($data){
		$this->db->insert('playbasis_player',$data);
	}
}
?>