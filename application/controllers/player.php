<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Player extends REST_Controller{
	public function __Construct(){
		parent::__construct();

		//model
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('tracker_model');
		$this->load->model('tool/error','error');
		$this->load->model('tool/utility','utility');
		$this->load->model('tool/respond','resp');

		//library


		//config
	}

	//get player info
	public function index_post($player_id=''){
		
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));
		if($pb_player_id < 0)
			$this->response($this->error->setError('USER_NOT_EXIST'),200);

		
		//read player information
		$player['player']  = $this->player_model->readPlayer($pb_player_id,array('first_name','last_name','image','exp','level','date_added'));

		$this->response($this->resp->setRespond($player),200);
	

	}

	//register player
	public function register_post($player_id=''){

		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		$required = $this->input->checkParam(array('first_name','last_name','image','email'));
		
		if(!$player_id){
			array_push($required, 'player_id');
		}
		
		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		}

		//validate token
		$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));

		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id > 0){
			$this->response($this->error->setError('USER_ALREADY_EXIST'),200);
		}
		
		$playerInfo = array(
			'first_name'	=>	$this->input->post('first_name'),
			'last_name'		=>	$this->input->post('last_name'),
			'email'			=>	$this->input->post('email'),
			'image'			=>	$this->input->post('image'),
			'player_id'		=>	$player_id,
		);
		
		$this->player_model->createPlayer(array_merge($validToken,$playerInfo));

		$this->response($this->resp->setRespond(),200);
		
	}

	//log-in player
	public function login_post($player_id){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		## TRIGGER EVENT ##

		#log event
		$this->tracker_model->trackEvent('OTHER',$this->utility->getEventMessage('login'),array('pb_player_id'=>$pb_player_id,'action_log_id'=>0));
		
		$this->response($this->resp->setRespond(),200);
	}
}

?>