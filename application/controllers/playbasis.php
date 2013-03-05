<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Playbasis extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		$this->load->model('social_model');
	}

	public function test(){
		// /$data['base_url'] = base_url();
		$this->load->view('playbasis/apitest');
	}
	
	public function fb(){
		$this->load->view('playbasis/fb');
		
		if ($_REQUEST) {
			$signed_request = $_REQUEST['signed_request'];
			echo json_encode($this->social_model->parse_signed_request($signed_request));
		} else {
			echo '$_REQUEST is empty';
		}
	}
}
?>