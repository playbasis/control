<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class REST_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}

	public function logRequest($data)
	{
		$mongoDate = new MongoDate(time());
		$this->set_site_mongodb($data['site_id']);
		$data['date_added'] = $mongoDate;
		$data['date_modified'] = $mongoDate;
		return $this->mongo_db->insert('playbasis_web_service_log', $data);
		/*array(
			// [POST] $validToken = $this->auth_model->findToken($token); [GET] $validToken = $this->auth_model->createTokenFromAPIKey($api_key);
			'client_id', $client_id = $validToken['client_id'];
			'site_id', $site_id = $validToken['site_id'];
			'api_key', [GET] $token = $this->input->get('api_key'); (or [POST] when do auth)
			'token', [POST] $token = $this->input->post('token');
			'method', $this->request->method
			'scheme', $_SERVER['REQUEST_SCHEME']
			'uri', $this->uri->uri_string()
			'query', $_SERVER['QUERY_STRING']
			'request', $this->request->body
			'response',
			'format',
			'ip', $this->input->ip_address()
			'agent', $_SERVER['HTTP_USER_AGENT']
		);*/
	}

	public function logResponse($id, $site_id, $data)
	{
		if (!$id)
			return false;
		$data['date_modified'] = new MongoDate(time());
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where('_id', $id);
		$this->mongo_db->set($data);
		return $this->mongo_db->update('playbasis_web_service_log');
		/*array(
			'response', XXX
			'format', $this->response->format
		);*/
	}
}
?>