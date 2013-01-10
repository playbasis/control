<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Service extends REST_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index_get($param1){
		$data = array(
			'status'	=> true,
			'message'	=> 'REST service BY PB ENGINE',
			'data'		=> $param1,
			'time'		=> date('r e'),
			'timestamp'	=> now(),
		);

		$this->response($data,200);
	}

	public function test_get($param1,$param2){
		$data = array(
			'status'	=> true,
			'message'	=> 'simple REST service',
			'data'		=> $param1.$param2,
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

	public function testCounter_get(){
		$this->load->model('engine/jigsaw','jg');
		
		$exInfo = array();
		$status = $this->jg->counter(array('counter_value'=>5,'interval'=>2,'interval_unit'=>'day'),array('pb_player_id'=>1,'rule_id'=>1,'jigsaw_id'=>1),$exInfo);

		var_dump($exInfo);
		var_dump($status);
	}

	public function testCooldown_get(){
		$this->load->model('engine/jigsaw','jg');
		
		$exInfo = array();
		$status = $this->jg->cooldown(array('cooldown'=>180),array('pb_player_id'=>1,'rule_id'=>1,'jigsaw_id'=>1),$exInfo);

		var_dump($exInfo);
		var_dump($status);
	}
}