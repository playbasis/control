<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Badge extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('badge_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/respond', 'resp');
	}
	public function index_get($badgeId = 0)
	{
		$required = $this->input->checkParam(array(
			'api_key'
		));
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
		if($badgeId)
		{
			//get badge by specific id
			$badge['badge'] = $this->badge_model->getBadge(array_merge($validToken, array(
				'badge_id' => new MongoId($badgeId)
			)));
			$this->response($this->resp->setRespond($badge), 200);
		}
		else
		{
			//get all badge relate to  clients
			$badgesList['badges'] = $this->badge_model->getAllBadges($validToken);
			$this->response($this->resp->setRespond($badgesList), 200);
		}
	}
	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
			);
		$token = $this->auth_model->getApiInfo($credential);
		echo '<br>getAllBadges:<br>';
		$result = $this->badge_model->getAllBadges($token);
		print_r($result);
		echo '<br>getBadge:<br>';
		$result = $this->badge_model->getBadge(array_merge($token, array(
			'badge_id' => $result[0]['badge_id']
			)));
		print_r($result);
		echo '</pre>';
	}
}
?>