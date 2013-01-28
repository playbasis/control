<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Playbasis extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}

	public function test(){
		// /$data['base_url'] = base_url();
		$this->load->view('playbasis/apitest');
	}
}
?>