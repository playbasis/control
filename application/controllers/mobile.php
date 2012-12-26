<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Mobile extends REST_Controller{
	public function __construct(){
		parent::__construct();
	}

	//mock mobile request
	public function checkin_post(){
		
		$data = array(
			'status'	=> true,
			'message'	=> 'success',
			'data'		=> array(
				'image'			=>	null,
				'text_message'	=> 'some message',
				'post'			=>	null ,
			),
		);

		$post = $this->input->post();

		if(!isset($post['keyword']) || empty($post['keyword'])){
			$data['status']		= false;
			$data['message']	= 'keyword required';
			$data['data']		= null;
		}

		switch ($post['keyword']) {
			//select image here
		}

		//response
		$this->response($data,200);
	}
}