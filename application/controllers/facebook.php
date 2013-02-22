<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Facebook extends REST_Controller{
	
	public function realtimeupdate_get(){
					
		//$mode = $this->input->get('hub_mode');
		//$verifyToken = $this->input->get('hub_verify_token');
		$challenge = $this->input->get('hub_challenge');
		echo $challenge;
	}
	
	public function realtimeupdate_post(){
		
		$changedData = $this->input->post();		
		$this->db->insert('playbasis_action_log',
						  array('pb_player_id'=> 0,
								'client_id'=> 0,
								'site_id'=> 0,
								'action_id'=> 0,
								'action_name'=> 'fbupdate',
								'object_Info'=>serialize($changedData),
								'date_added'=>date('Y-m-d H:i:s'),
								'date_modified'=>date('Y-m-d H:i:s')));
						
		$this->response($changedData, 200);
	}
}