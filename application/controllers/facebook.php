<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Facebook extends REST_Controller{
	
	public function realtimeupdate_get(){
				
		$mode = $this->input->get('hub_mode');
		$challenge = $this->input->get('hub_challenge');
		$verifyToken = $this->input->get('hub_verify_token');
		
		$this->response($challenge, 200);
	}
	
	public function realtimeupdate_post(){
		$this->response('fbrtu', 200);
	}
}