<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/facebook-php-sdk/facebook.php';

class Facebook extends REST_Controller{
	
	private $facebook = null;
	
	public function __construct(){
		parent::__construct();

		$this->load->model('social_model');
	}
	
	public function realtimeupdate_get(){
					
		//$mode = $this->input->get('hub_mode');
		//$verifyToken = $this->input->get('hub_verify_token');
		$challenge = $this->input->get('hub_challenge');
		echo $challenge;
	}
	
	public function realtimeupdate_post(){
		
		$rawFacebookData = $this->request->body;
		$data = $this->social_model->processFacebookData($rawFacebookData);
		$objectInfo = array('message' => $data['message'],
							'raw_data' => $rawFacebookData);
		$this->db->insert('playbasis_action_log',
						  array('pb_player_id'=> $data['pb_player_id'],
								'client_id'=> $data['client_id'],
								'site_id'=> $data['site_id'],
								'action_id'=> 0,
								'action_name'=> $data['action'],
								'object_Info'=> serialize($objectInfo),
								'date_added'=>date('Y-m-d H:i:s'),
								'date_modified'=>date('Y-m-d H:i:s')));
						
		$this->response($data, 200);
	}
}
