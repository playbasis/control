<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
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
	public function index_post($badgeId = 0)
	{
		$required = $this->input->checkParam(array(
			'token'
		));
		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'), 200);
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		if($badgeId)
		{
			//get badge by specific id
			$badge['badge'] = $this->badge_model->getBadge(array_merge($validToken, array(
				'badge_id' => $badgeId
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
	public function getCollection_post($collectionId = 0)
	{
		$required = $this->input->checkParam(array(
			'token'
		));
		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'), 200);
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		if($collectionId)
		{
			//get collection by id
			$collection['collection'] = $this->badge_model->getCollection(array_merge($validToken, array(
				'collection_id' => $collectionId
			)));
			$this->response($this->resp->setRespond($collection), 200);
		}
		else
		{
			$collectionsList['collections'] = $this->badge_model->getAllCollection($validToken);
			$this->response($this->resp->setRespond($collectionsList), 200);
		}
	}
}
?>