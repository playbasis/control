<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Client_model extends CI_Model{
	public function updateExpAndLevel($exp,$pb_player_id){
		assert($exp);
		assert($pb_player_id);

		//get level
		$this->db->select_max('level');
		$this->db->where("exp <=",$exp);
		$result = $this->db->get('playbasis_exp_table');

		$level = -1;
		if($result->num_rows()){
			$level = $result->row_array();
			$level = $level['level'];
		}
		
		$this->db->where('pb_player_id',$pb_player_id);
		$this->db->set('exp', "exp+$exp", FALSE);
		if($level>0)
			$this->db->set('level', $level['level']);
		
		$this->db->update('playbasis_player');

		return $level;			
	}

	public function log($logData){
		assert($logData);
		assert(is_array($logData));
		assert($logData['pb_player_id']);
		assert($logData['action_id']);
		assert($logData['client_id']);
		assert($logData['site_id']);
		assert($logData['domain_name']);


		

	} 
}
?>