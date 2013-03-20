<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

define('API_KEY','ddfa2aaffe9cd021f2b42d7a3c5f5712abca5d43');

class Janrain extends REST_Controller{
	
	public function __construct(){
		parent::__construct();
	}
		
	public function token_post(){
		
		var_dump($this->input->post());
		var_dump($this->input->server('HTTP_HOST'));
		
		$required = $this->input->checkParam(array('token'));
		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);
		
		$token = $this->input->post('token');
		if(strlen($token) != 40) {//test the length of the token; it should be 40 characters
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		}

		$post_data = array(
			'token'  => $token,
			'apiKey' => API_KEY,
			'format' => 'json',
			'extended' => 'true'); //Extended is not available to Basic.

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		$result = curl_exec($curl);
		if ($result == false){
			echo "\n".'Curl error: ' . curl_error($curl);
			echo "\n".'HTTP code: ' . curl_errno($curl);
			echo "\n"; var_dump($post_data);
		}
		curl_close($curl);

		$auth_info = json_decode($result, true);
		var_dump($auth_info);
	}
}
