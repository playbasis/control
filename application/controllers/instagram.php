<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
class Instagram extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
	}
	public function index_get()
	{
		$hub_mode = $this->input->get('hub.mode');
		if($hub_mode == 'subscribe')
		{
			$verify_token = $this->input->get('hub.verify_token');
			if($verify_token == 'pbapp')
				echo $this->input->get('hub.challenge');
		}
		else
		{
			echo 'playbasis <3 instagram';
		}
	}
	
	public function index_post()
	{
		echo 'instagram <3 playbasis';
	}
}
