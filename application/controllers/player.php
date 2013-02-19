<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Player extends REST_Controller{
	public function __construct(){
		parent::__construct();

		//model
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('tracker_model');
		$this->load->model('point_model');
		$this->load->model('action_model');
		$this->load->model('tool/error','error');
		$this->load->model('tool/utility','utility');
		$this->load->model('tool/respond','resp');
		$this->load->model('tool/node_stream','node');

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
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging


		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));
		if($pb_player_id < 0)
			$this->response($this->error->setError('USER_NOT_EXIST'),200);

		
		//read player information
		$player['player']  =  $this->player_model->readPlayer($pb_player_id,array('username','first_name','last_name','gender','image','exp','level','date_added AS registered','birth_date'));

		//get ladt login
		$player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, 'LOGIN');
		$player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, 'LOGOUT');

		$this->response($this->resp->setRespond($player),200);
	}

	//register player
	public function register_post($player_id=''){

		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		$required = $this->input->checkParam(array('image','email','username'));
		
		if(!$player_id){
			array_push($required, 'player_id');
		}
		
		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		}

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));

		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id > 0){
			$this->response($this->error->setError('USER_ALREADY_EXIST'),200);
		}
		
		$playerInfo = array(
			'email'			=>	$this->input->post('email'),
			'image'			=>	$this->input->post('image'),
			'username'		=>	$this->input->post('username'),
			'player_id'		=>	$player_id,
		);
		
		$firstName = $this->input->post('first_name');
		if($firstName)
			$playerInfo['first_name'] = $firstName;
		
		$lastName = $this->input->post('last_name');
		if($lastName)
			$playerInfo['last_name'] = $lastName;

		$nickName = $this->input->post('nickname');
		if($nickName)
			$playerInfo['nickname'] = $nickName;		

		$password = $this->input->post('password');
		if($password)
			$playerInfo['password'] = $password;
		
		$gender = $this->input->post('gender');
		if($gender)
			$playerInfo['gender'] = $gender;
		
		$birthdate = $this->input->post('birth_date');
		if($birthdate){
			$timestamp = strtotime($birthdate);
			$playerInfo['birth_date'] = date('Y-m-d', $timestamp);
		}
				
		$this->player_model->createPlayer(array_merge($validToken,$playerInfo));

		$this->response($this->resp->setRespond(),200);
		
	}

	//log-in player
	public function login_post($player_id=''){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		## TRIGGER EVENT ##

		$eventMessage = $this->utility->getEventMessage('login');

			#log event
			$this->tracker_model->trackEvent('LOGIN',$eventMessage,array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'pb_player_id'=>$pb_player_id,'action_log_id'=>0));
		
		//node stream
		$this->node->publish(array('pb_player_id'=>$pb_player_id, 'action_name'=>'login', 'message'=>$eventMessage), $validToken);
		
		$this->response($this->resp->setRespond(),200);
	}

	//log-out player
	public function logout_post($player_id=''){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		## TRIGGER EVENT ##

		$eventMessage = $this->utility->getEventMessage('logout');

		#log event
		$this->tracker_model->trackEvent('LOGOUT',$eventMessage,array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'pb_player_id'=>$pb_player_id,'action_log_id'=>0));
		
		//node stream
		$this->node->publish(array('pb_player_id'=>$pb_player_id, 'action_name'=>'logout', 'message'=>$eventMessage), $validToken);

		$this->response($this->resp->setRespond(),200);
	}

	//get player points
	public function points_post($player_id=''){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		$input = array_merge($validToken,array('pb_player_id'=>$pb_player_id));
		//get player points
		$points['points'] = $this->player_model->getPlayerPoints($input);

		foreach ($points['points'] as &$point){
			$point['reward_name'] = $this->point_model->getRewardNameById(array_merge($input,array('reward_id'=>$point['reward_id'])));
			ksort($point);
		}

		//response
		$this->response($this->resp->setRespond($points),200);
	}

	//get player point
	public function point_post($player_id='',$reward=''){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		$required = array();
		if(!$player_id)
			array_push($required,'player_id');
		if(!$reward)
			array_push($required,'reward');

		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		}

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		//check point 
		$input = array_merge($validToken,array('reward_name'=>$reward));
		$haspoint = $this->point_model->findPoint($input);

		if(!$haspoint){
			$this->response($this->error->setError('REWARD_NOT_FOUND'),200);
		}

		$point['point'] = $this->player_model->getPlayerPoint(array_merge($input,array('reward_id'=>$haspoint),array('pb_player_id'=>$pb_player_id)));
		$point['point'][0]['reward_name'] = $reward;
		ksort($point);

		//response
		$this->response($this->resp->setRespond($point),200);

	}


	//get player actions statistics
	public function action_post($player_id='',$action='',$option='time'){
		// var_dump($player_id);
		// var_dump($action);
		// var_dump($option);
		// die();

		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		$required = array();
		if(!$player_id)
			array_push($required,'player_id');
		// if(!$action)
		// 	array_push($required,'action');

		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		}

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}
		
		$actions = array();
		//check action
		if($action){
			//check action
			$hasAction = $this->action_model->findAction(array_merge($validToken,array('action_name'=>$action)));
			//var_dump($hasAction);
			//die();

			if(!$hasAction){
				$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
			}

			$actions['action'] = ($option=='time')?	$this->player_model->getActionPerform(array_merge($validToken,array('pb_player_id'=>$pb_player_id),array('action_id'=>$hasAction))) :
													$this->player_model->getActionCount(array_merge($validToken,array('pb_player_id'=>$pb_player_id),array('action_id'=>$hasAction)));
			
		}
		else{	//lastest
			if($option!='time'){
				$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
			}

			$actions['action'] =  $this->player_model->getLastestActionPerform(array_merge($validToken,array('pb_player_id'=>$pb_player_id)));		
		}

		$this->response($this->resp->setRespond($actions),200);
	}


	//get player badge
	public function badge_post($player_id=''){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING',array('player_id')),200);

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$player_id)));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}

		//get player badge
		$badgeList = $this->player_model->getBadge(array_merge($validToken,array('pb_player_id'=>$pb_player_id)));

		$this->response($this->resp->setRespond($badgeList),200);
	}
	
	public function rank_post($ranked_by, $limit=20){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);
		
		if(!$ranked_by)
			$this->response($this->error->setError('PARAMETER_MISSING',array('ranked_by')),200);
		
		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		$leaderboard = $this->player_model->getLeaderboard($ranked_by, $limit, $validToken['client_id'], $validToken['site_id']);
		
		$this->response($this->resp->setRespond($leaderboard),200);
	}
}

?>