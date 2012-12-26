<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Service extends REST_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$data = array(
			'status'	=> true,
			'message'	=> 'REST service BY PB ENGINE',
			'time'		=> date('r e'),
			'timestamp'	=> now(),
		);

		$this->response($data,200);
	}

	public function test_get(){
		$data = array(
			'status'	=> true,
			'message'	=> 'simple REST service',
			'time'		=> date('r e'),
			'timestamp'	=> now(),
		);

		$this->response($data,200);
	}

	public function users_get(){
		$data = array(
			'user'	=> array(
				array(
					'name'	=> 'PM Master',
					'age'	=> 24,
				),
				array(
					'name'	=> 'Aquario',
					'age'	=> 24,
				),
			),
			'status'	=> true,
			'time'		=> date('r e'),
			'timestamp'	=> now(),
		);

		$this->response($data,200);

	}

	public function test_post(){
		
		$data = array(
			'status'	=> true,
			'time'		=> date('r e'),
			'timestamp'	=> now(),
		);

		$data['post_data'] = $this->input->post();

		$this->response($data,200);
	}
}