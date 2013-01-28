<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Auth extends REST_Controller{
	public function __construct(){
		parent::__construct();

		//load model
		$this->load->model('auth_model');
		$this->load->model('tool/error','error');
		$this->load->model('tool/respond','resp');

		//load library

		//load config
	}

	public function index_post(){
		
		$required = $this->input->checkParam(array('api_key','api_secret'));

		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);


		$API['key'] = $this->input->post('api_key');
		$API['secret'] = $this->input->post('api_secret');


		//validate api_key & api_secret
		#get client relate to api key 
		$clientInfo = $this->auth_model->getApiInfo($API);

		$this->respons($clientInfo,200);
		die();
		#validate 
		if($clientInfo){
			//gen token
			$token = $this->auth_model->generateToken(array_merge($clientInfo,$API));
			
			$this->response($this->resp->setRespond($token),200);
		}
		else{
			$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET',$required),200);
		}
	}
}
?>