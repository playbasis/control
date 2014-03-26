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
	public function index_get()
	{
		/* GET */
		$required = $this->input->checkParam(array(
			'api_key'
		));
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
		$site_id = $validToken['site_id'];
		/* main */
		//db.playbasis_action_to_client.find({status: true, client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{'name': 1})
		$action = array('like', 'visit', 'comment', 'share');
		$this->response($this->resp->setRespond($action), 200);
	}
	public function log_get()
	{
		/* GET */
		$required = $this->input->checkParam(array(
			'api_key'
		));
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
		$site_id = $validToken['site_id'];
		/* main */
		//db.playbasis_action_log.find({client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{action_name: 1, date_added: 1})
		//process with Map-Reduce
		$log = array(
			'2014-03-21' => array('like' => 100, 'share' => 17),
			'2014-03-22' => array('like' => 123, 'share' => 40),
		);
		$this->response($this->resp->setRespond($log), 200);
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