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
		echo $changedData;
		
		$this->response($changedData, 200);
	}
}