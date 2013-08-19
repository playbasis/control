<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Action extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('action_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/respond', 'resp');
	}
	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
			);
		$token = $this->auth_model->getApiInfo($credential);
		echo '<br>findAction:<br>';
		$result = $this->action_model->findAction(array_merge($token, array('action_name'=>'like')));
		print_r($result);
		echo '</pre>';
	}
}
?>