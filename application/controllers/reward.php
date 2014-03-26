<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Reward extends REST_Controller
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
		//db.playbasis_reward_to_client.find({status: true, client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{name: 1})
		//db.playbasis_reward_to_player.distinct("reward_id")
		//db.playbasis_reward_to_player.distinct("reward_id",{client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5"), reward_id: {$exists: true,$ne: ""}})
		//db.playbasis_reward_to_player.find({reward_id: null})
		//db.playbasis_reward_to_player.distinct("badge_id")
		//db.playbasis_event_log.distinct("event_type")
		//db.collection.find( { _id : { $in : [1,2,3,4] } } );
		$action = array('point', 'exp', 'level', 'badge');
		$this->response($this->resp->setRespond($action), 200);
	}
	public function pointLog_get()
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
		//db.playbasis_reward_to_player.find({client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{reward_id: 1, badge_id: 1, value: 1, date_added: 1})
		$log = array(
			'2014-03-21' => array('point' => 500),
			'2014-03-22' => array('point' => 1300),
		);
		$this->response($this->resp->setRespond($log), 200);
	}
	public function expLog_get()
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
		//db.playbasis_reward_to_player.find({client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{reward_id: 1, badge_id: 1, value: 1, date_added: 1})
		//
		$log = array(
			'2014-03-21' => array('exp' => 100),
			'2014-03-22' => array('exp' => 120),
		);
		$this->response($this->resp->setRespond($log), 200);
	}
	public function levelLog_get()
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
		//EVENT LOG!
		$log = array(
			'2014-03-21' => array('level' => 6),
			'2014-03-22' => array('level' => 7),
		);
		$this->response($this->resp->setRespond($log), 200);
	}
	public function badgeLog_get()
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
		//db.playbasis_reward_to_player.find({client_id: ObjectId("52ea1eab8d8c89401c0000d9"), site_id: ObjectId("52ea1eac8d8c89401c0000e5")},{reward_id: 1, badge_id: 1, value: 1, date_added: 1})
		$log = array(
			'2014-03-21' => array('52ea1ea78d8c89401c000046' => 6, '52ea1ea78d8c89401c000047' => 3),
			'2014-03-22' => array('52ea1ea78d8c89401c000046' => 4),
		);
		$this->response($this->resp->setRespond($log), 200);
	}
}
?>