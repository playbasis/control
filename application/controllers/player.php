<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once(APPPATH . 'controllers/engine.php');
class Player extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('client_model');
		$this->load->model('player_model');
		$this->load->model('tracker_model');
		$this->load->model('point_model');
		$this->load->model('action_model');
		$this->load->model('level_model');
		$this->load->model('reward_model');
		$this->load->model('quest_model');
		$this->load->model('badge_model');
		$this->load->model('energy_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/utility', 'utility');
		$this->load->model('tool/respond', 'resp');
		$this->load->model('tool/node_stream', 'node');
        $this->load->model('store_org_model');
		$this->load->library('form_validation');
	}
	public function index_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
			'username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'exp',
			'level',
			'date_added',
			'birth_date'
		));

		//get last login/logout
		$player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
		$player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGOUT');
		$player['player']['cl_player_id'] = $player_id;
		$this->response($this->resp->setRespond($player), 200);
	}
	public function index_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
			'username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'email',
			'phone_number',
			'exp',
			'level',
			'date_added',
			'birth_date'
		));

		//get last login/logout
		$player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
		$player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGOUT');
		$player['player']['cl_player_id'] = $player_id;
		$this->response($this->resp->setRespond($player), 200);
	}
    /*public function list_get()
    {
        $required = $this->input->checkParam(array(
            'list_player_id'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $list_player_id = explode(",", $this->input->get('list_player_id'));
        //read player information
        $player['player'] = $this->player_model->readListPlayer($list_player_id, $this->site_id, array(
            'username',
            'first_name',
            'last_name',
            'gender',
            'image',
            'exp',
            'level',
            'date_added AS registered',
            'birth_date'
        ));

        $this->response($this->resp->setRespond($player), 200);
    }*/

    public function list_post()
    {
        $required = $this->input->checkParam(array(
            'list_player_id'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $list_player_id = explode(",", $this->input->post('list_player_id'));
        //read player information
		for($i = 0; $i<count($list_player_id); $i++){
			$data = array('client_id'=>$this->validToken['client_id'], 'site_id'=>$this->site_id, 'cl_player_id'=>$list_player_id[$i]);
			$pb_player_id = $this->player_model->getPlaybasisId($data);

			$player['player'][] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array('cl_player_id','username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'email',
			'phone_number',
			'exp',
			'level',
			'date_added',
			'birth_date'));
			
			$player['player'][$i]['last_login']= $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
			$player['player'][$i]['last_logout']= $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGOUT');
		}     

        $this->response($this->resp->setRespond($player), 200);
    }
    
	public function details_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);

		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
			'username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'exp',
			'level',
			'date_added',
			'birth_date'
		));
        //percent exp of level
//        $level = $this->level_model->getLevelDetail($player['player']['level'], $this->validToken['client_id'], $this->validToken['site_id']);
        $level = $this->level_model->getLevelByExp($player['player']['exp'], $this->validToken['client_id'], $this->validToken['site_id']);
        if($level){
            $base_exp = $level['min_exp'];
            $max_exp = $level['max_exp'] - $base_exp;
        }else{
            $base_exp = 0;
            $max_exp = 0;
        }
        $now_exp = $player['player']['exp'] - $base_exp;
        if(isset($level['max_exp']) && $max_exp != 0){
            $percent_exp = (floatval($now_exp) * floatval (100)) / floatval($max_exp);
            $player['player']['percent_of_level'] = round($percent_exp,2);
            if ($player['player']['percent_of_level'] >= 100) $player['player']['percent_of_level'] = 99;
        }else{
            $player['player']['percent_of_level'] = 100;
        }
        $player['player']['level'] = $level['level'];
        $player['player']['level_title'] = $level['level_title'];
        $player['player']['level_image'] = $level['level_image'];

        $player['player']['badges'] = $this->player_model->getBadge($pb_player_id, $this->site_id);
        $player['player']['goods'] = $this->player_model->getGoods($pb_player_id, $this->site_id);
        $points = $this->player_model->getPlayerPoints($pb_player_id, $this->site_id);
        foreach($points as &$point)
        {
			$point['reward_name'] = $this->point_model->getRewardNameById(array_merge($this->validToken, array(
                'reward_id' => $point['reward_id']
            )));
            $point['reward_id'] = $point['reward_id']."";
            ksort($point);
        }
        $player['player']['points'] = $points;
		//get last login/logout
		$player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
		$player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGOUT');
		$this->response($this->resp->setRespond($player), 200);
	}
	public function details_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);

		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
			'username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'email',
			'phone_number',
			'exp',
			'level',
			'date_added',
			'birth_date'
		));

        //percent exp of level
//        $level = $this->level_model->getLevelDetail($player['player']['level'], $this->validToken['client_id'], $this->validToken['site_id']);
        $level = $this->level_model->getLevelByExp($player['player']['exp'], $this->validToken['client_id'], $this->validToken['site_id']);
        if($level){
            $base_exp = $level['min_exp'];
            $max_exp = $level['max_exp'] - $base_exp;
        }else{
            $base_exp = 0;
            $max_exp = 0;
        }
        $now_exp = $player['player']['exp'] - $base_exp;
        if(isset($level['max_exp']) && $max_exp != 0){
            $percent_exp = (floatval($now_exp) * floatval (100)) / floatval($max_exp);
            $player['player']['percent_of_level'] = round($percent_exp,2);
        }else{
            $player['player']['percent_of_level'] = 100;
        }
        $player['player']['level'] = $level['level'];
        $player['player']['level_title'] = $level['level_title'];
        $player['player']['level_image'] = $level['level_image'];

        $player['player']['badges'] = $this->player_model->getBadge($pb_player_id, $this->site_id);
        $player['player']['goods'] = $this->player_model->getGoods($pb_player_id, $this->site_id);
        $points = $this->player_model->getPlayerPoints($pb_player_id, $this->site_id);
        foreach($points as &$point)
        {
            $point['reward_name'] = $this->point_model->getRewardNameById(array_merge($this->validToken, array(
                'reward_id' => $point['reward_id']
            )));
            $point['reward_id'] = $point['reward_id']."";
            ksort($point);
        }
        $player['player']['points'] = $points;
		//get last login/logout
		$player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
		$player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGOUT');
		$this->response($this->resp->setRespond($player), 200);
	}
	public function status_get($player_id = '') {
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);

		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array('status'));
		$this->response($this->resp->setRespond($player), 200);
	}

	public function register_post($player_id = '')
	{
		$required = $this->input->checkParam(array(
//			'image',
			'email',
			'username'
		));
		if (!$player_id) {
			array_push($required, 'player_id');
		}
		if ($required) {
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		}

		if (!$this->validClPlayerId($player_id)) {
			$this->response($this->error->setError('USER_ID_INVALID'), 200);
		}

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));

		if ($pb_player_id) {
			$this->response($this->error->setError('USER_ALREADY_EXIST'), 200);
		}

		$playerInfo = array(
			'email' => $this->input->post('email'),
			'image' => $this->input->post('image') ? $this->input->post('image') : "https://www.pbapp.net/images/default_profile.jpg",
			'username' => $this->input->post('username'),
			'player_id' => $player_id
		);
		$firstName = $this->input->post('first_name');
		if ($firstName) {
			$playerInfo['first_name'] = $firstName;
		}
		$lastName = $this->input->post('last_name');
		if ($lastName) {
			$playerInfo['last_name'] = $lastName;
		}
		$nickName = $this->input->post('nickname');
		if ($nickName) {
			$playerInfo['nickname'] = $nickName;
		}
		$phoneNumber = $this->input->post('phone_number');
		if ($phoneNumber) {
			if ($this->validTelephonewithCountry($phoneNumber)) {
				$playerInfo['phone_number'] = $phoneNumber;
			} else {
				$this->response($this->error->setError('USER_PHONE_INVALID'), 200);
			}
		}
		$facebookId = $this->input->post('facebook_id');
		if ($facebookId) {
			$playerInfo['facebook_id'] = $facebookId;
		}
		$twitterId = $this->input->post('twitter_id');
		if ($twitterId) {
			$playerInfo['twitter_id'] = $twitterId;
		}
		$instagramId = $this->input->post('instagram_id');
		if ($instagramId) {
			$playerInfo['instagram_id'] = $instagramId;
		}
		if ($this->password_validation($this->validToken['client_id'],$this->validToken['site_id'],$playerInfo['username'])) {
			$this->player_model->unlockPlayer($this->validToken['site_id'],$pb_player_id);
			$password = $this->input->post('password');
			if($password)
				$playerInfo['password'] = do_hash($password);
		}
		else{
			$this->response($this->error->setError('FORM_VALIDATION_FAILED',$this->validation_errors()[0]), 200);
		}
		$gender = $this->input->post('gender');
		if ($gender) {
			$playerInfo['gender'] = $gender;
		}
		$birthdate = $this->input->post('birth_date');
		if ($birthdate) {
			$timestamp = strtotime($birthdate);
			$playerInfo['birth_date'] = date('Y-m-d', $timestamp);
		}
		$approve_status = $this->input->post('approve_status');
		if ($approve_status) {
			$playerInfo['approve_status'] = $approve_status;
		}
		$device_id = $this->input->post('device_id');
		if ($device_id) {
			$playerInfo['device_id'] = $device_id;
		}
		$referral_code = $this->input->post('code');
		$anonymous = $this->input->post('anonymous');

		if ($anonymous && $referral_code) $this->response($this->error->setError('ANONYMOUS_CANNOT_REFERRAL'), 200);

		// check referral code
		$playerA = null;
		if ($referral_code) {
			$playerA = $this->player_model->findPlayerByCode($this->validToken["site_id"], $referral_code, array('cl_player_id'));
			if (!$playerA) $this->response($this->error->setError('REFERRAL_CODE_INVALID'), 200);
		}

		//check anonymous feature depend on plan
		if ($anonymous) {
			$clientData = array(
				'client_id' => $this->validToken['client_id'],
				'site_id' => $this->validToken['site_id']
			);
			$result = $this->client_model->checkFeatureByFeatureName($clientData, "Anonymous");
			if ($result) {
				$playerInfo['anonymous'] = $anonymous;
			} else {
				$this->response($this->error->setError('ANONYMOUS_NOT_FOUND'), 200);
			}
		}

		// get plan_id
		$plan_id = $this->client_model->getPlanIdByClientId($this->validToken["client_id"]);
		try {
			$player_limit = $this->client_model->getPlanLimitById(
				$this->client_plan,
				"others",
				"player");
		} catch (Exception $e) {
			$this->response($this->error->setError('INTERNAL_ERROR'), 200);
		}

		$pb_player_id = $this->player_model->createPlayer(
			array_merge($this->validToken, $playerInfo), $player_limit);

		/* trigger reward for referral program (if any) */
		if ($playerA) {
			$inviteAction = $this->client_model->getAction(array(
				'client_id' => $this->validToken["client_id"],
				'site_id' => $this->validToken['site_id'],
				'action_name' => 'invite',
			));
			if(!$inviteAction)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);

			$invitedAction = $this->client_model->getAction(array(
				'client_id' => $this->validToken["client_id"],
				'site_id' => $this->validToken['site_id'],
				'action_name' => 'invited',
			));
			if(!$invitedAction)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);

			$engine = new Engine();

			// A invite B
			$input = array_merge($this->validToken, array(
				'pb_player_id' => $playerA['_id'],
				'action_id' => $inviteAction['action_id'],
				'action_name' => 'invite',
				'player-2' => $pb_player_id,
				'test' => false
			));
			$engine->processRule($input, $this->validToken, null, null);

			// B invited by A
			$input = array_merge($this->validToken, array(
				'pb_player_id' => $pb_player_id,
				'action_id' => $invitedAction['action_id'],
				'action_name' => 'invited',
				'player-2' => $playerA['_id'],
				'test' => false
			));
			$engine->processRule($input, $this->validToken, null, null);
		}

		/* track action=register automatically after creating a new player */
		$action = $this->client_model->getAction(array(
			'client_id' => $this->validToken['client_id'],
			'site_id' => $this->validToken['site_id'],
			'action_name' => 'register'
		));
		if ($action) {
			$engine = new Engine();
			$input = array_merge($this->validToken, array(
				'pb_player_id' => $pb_player_id,
				'action_id' => $action['action_id'],
				'action_name' => 'register',
				'url' => null,
				'test' => false
			));
			$engine->processRule($input, $this->validToken, null, null);
		}

		/* Automatically energy initialization after creating a new player*/
		foreach ($this->energy_model->findActiveEnergyRewardsById($this->validToken['client_id'],
				$this->validToken['site_id']) as $energy) {

			$energy_reward_id = $energy['reward_id'];
			$energy_max = (int)$energy['energy_props']['maximum'];
			$batch_data = array();
			if ($energy['type'] == 'gain') {
				array_push($batch_data, array(
					'pb_player_id' => $pb_player_id,
					'cl_player_id' => $player_id,
					'client_id' => $this->validToken['client_id'],
					'site_id' => $this->validToken['site_id'],
					'reward_id' => $energy_reward_id,
					'value' => $energy_max,
					'date_cron_modified' => new MongoDate(),
					'date_added' => new MongoDate(),
					'date_modified' => new MongoDate()
				));
			} elseif ($energy['type'] == 'loss') {
				array_push($batch_data, array(
					'pb_player_id' => $pb_player_id,
					'cl_player_id' => $player_id,
					'client_id' => $this->validToken['client_id'],
					'site_id' => $this->validToken['site_id'],
					'reward_id' => $energy_reward_id,
					'value' => 0,
					'date_cron_modified' => new MongoDate(),
					'date_added' => new MongoDate(),
					'date_modified' => new MongoDate()
				));
			}
			if (!empty($batch_data)) {
				$this->energy_model->bulkInsertInitialValue($batch_data);
			}
		}
		if ($pb_player_id) {
			$this->response($this->resp->setRespond(), 200);
		} else {
			$this->response($this->error->setError('LIMIT_EXCEED'), 200);
		}
	}
	public function update_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);		
		$playerInfo = array();
		$email = $this->input->post('email');
		if($email)
			$playerInfo['email'] = $email;
		$image = $this->input->post('image');
		if($image)
			$playerInfo['image'] = $image;
		$username = $this->input->post('username');
		if($username)
			$playerInfo['username'] = $username;
		$exp = $this->input->post('exp');
		if(is_numeric($exp))
			$playerInfo['exp'] = intval($exp);
		$level = $this->input->post('level');
		if(is_numeric($level))
			$playerInfo['level'] = intval($level);
		$firstName = $this->input->post('first_name');
		if($firstName)
			$playerInfo['first_name'] = $firstName;
		$lastName = $this->input->post('last_name');
		if($lastName)
			$playerInfo['last_name'] = $lastName;
		$nickName = $this->input->post('nickname');
		if($nickName)
			$playerInfo['nickname'] = $nickName;
		$phoneNumber = $this->input->post('phone_number');
        if($phoneNumber){
            if($this->validTelephonewithCountry($phoneNumber)){
                $playerInfo['phone_number'] = $phoneNumber;
            }else{
                $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
            }
        }
		$facebookId = $this->input->post('facebook_id');
		if($facebookId)
			$playerInfo['facebook_id'] = $facebookId;
		$twitterId = $this->input->post('twitter_id');
		if($twitterId)
			$playerInfo['twitter_id'] = $twitterId;
		$instagramId = $this->input->post('instagram_id');
		if($instagramId)
			$playerInfo['instagram_id'] = $instagramId;
		$deviceId = $this->input->post('device_id');
		if($deviceId)
			$playerInfo['device_id'] = $deviceId;
		$password = $this->input->post('password');
		if ($password){
			if (!isset($username) ||  $username == ''){
				$username = $this->player_model->readPlayer($pb_player_id, $this->site_id, array('username' ))['username'];
			}
			if ($this->password_validation($this->validToken['client_id'],$this->validToken['site_id'],$username)) {
				$this->player_model->unlockPlayer($this->validToken['site_id'],$pb_player_id);
				$playerInfo['password'] = do_hash($password);
			}
			else{
				$this->response($this->error->setError('FORM_VALIDATION_FAILED',$this->validation_errors()), 200);
			}
		}

		$gender = $this->input->post('gender');
		if($gender)
			$playerInfo['gender'] = intval($gender);
		$birthdate = $this->input->post('birth_date');
		if($birthdate)
		{
			$timestamp = strtotime($birthdate);
			$playerInfo['birth_date'] = date('Y-m-d', $timestamp);
		}
		$approve_status = $this->input->post('approve_status');
		if ($approve_status)
			$playerInfo['approve_status'] = $approve_status;

		$this->player_model->updatePlayer($pb_player_id, $this->validToken['site_id'], $playerInfo);
		$this->response($this->resp->setRespond(), 200);
	}
	public function custom_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		//read player information
		$player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
			'custom',
		));
		$this->response($this->resp->setRespond($player), 200);
	}
	public function custom_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		$playerInfo = array();
		$key = $this->input->post('key');
		if ($key) {
			$playerInfo['custom'] = array();
			$keys = explode(',', $key);
			$value = $this->input->post('value');
			$values = explode(',', $value);
			foreach ($keys as $i => $key) {
				$playerInfo['custom'][$key] = isset($values[$i]) ? $values[$i] : null;
			}
		}
		$this->player_model->updatePlayer($pb_player_id, $this->validToken['site_id'], $playerInfo);
		$this->response($this->resp->setRespond(), 200);
	}
	public function delete_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		$this->player_model->deletePlayer($pb_player_id, $this->validToken['site_id']);
		$this->response($this->resp->setRespond(), 200);
	}

	public function login_post($player_id = '')
	{
		if (!$player_id) {
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		}

		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if (!$pb_player_id) {
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		}

		//check anonymous user
		$anonymousFeature = $this->client_model->checkFeatureByFeatureName($this->validToken, "Anonymous");
		if ($anonymousFeature) {
			$sessions = $this->player_model->findBySessionId($this->client_id, $this->site_id, $this->input->post('session_id'), true);
			if (count($sessions) > 0) {
				$anonymousUser = $this->player_model->isAnonymous($this->client_id, $this->site_id, null, $sessions['pb_player_id']);
				if ($anonymousUser) {
					$this->mongo_db->where('pb_player_id', $sessions['pb_player_id']);
					$action_logs = $this->mongo_db->get('playbasis_action_log');
					foreach ($action_logs as $action_log) {
						$engine = new Engine();
						$input = array_merge($this->validToken, array(
							'pb_player_id' => $pb_player_id,
							'action_id' => $action_log['action_id'],
							'action_name' => $action_log['action_name'],
							'url' => $action_log['url'],
							'date_added' => $action_log['date_added'],
							'test' => false
						));
						$engine->processRule($input, $this->validToken, null, null, $action_log['date_added']);
						$this->player_model->deletePlayer($sessions['pb_player_id'], $this->validToken['site_id']);
					}
				}
			}
		}

		//trigger and log event
		$eventMessage = $this->utility->getEventMessage('login');
		$this->tracker_model->trackEvent('LOGIN', $eventMessage, array(
			'client_id' => $this->client_id,
			'site_id' => $this->site_id,
			'pb_player_id' => $pb_player_id,
			'action_log_id' => null
		));
		//publish to node stream
		$this->node->publish(array(
			'pb_player_id' => $pb_player_id,
			'action_name' => 'login',
			'action_icon' => 'fa-sign-in',
			'message' => $eventMessage
		), $this->validToken['domain_name'], $this->validToken['site_id']);

		/* Optionally, keep track of session */
		$session_id = $this->input->post('session_id');
		$session_expires_in = $this->input->post('session_expires_in');
		if ($session_id) {
			$this->player_model->login($this->client_id, $this->site_id, $pb_player_id, $session_id, $session_expires_in);
		}

		$this->response($this->resp->setRespond(), 200);
	}
	public function logout_post($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		//trigger and log event
		$eventMessage = $this->utility->getEventMessage('logout');
		$this->tracker_model->trackEvent('LOGOUT', $eventMessage, array(
			'client_id' => $this->validToken['client_id'],
			'site_id' => $this->validToken['site_id'],
			'pb_player_id' => $pb_player_id,
			'action_log_id' => null
		));
		//publish to node stream
		$this->node->publish(array(
			'pb_player_id' => $pb_player_id,
			'action_name' => 'logout',
			'action_icon' => 'fa-sign-out',
			'message' => $eventMessage
		), $this->validToken['domain_name'], $this->validToken['site_id']);

		/* Optionally, remove session */
		$session_id = $this->input->post('session_id');
		if ($session_id) {
			$this->player_model->logout($this->client_id, $this->site_id, $session_id);
		}

		$this->response($this->resp->setRespond(), 200);
	}
	public function sessions_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);

		/* List all active sessions of the player */
		$sessions = $this->player_model->listSessions($this->client_id, $this->site_id, $pb_player_id);

		$this->response($this->resp->setRespond($sessions), 200);
	}
	public function session_get($session_id = '')
	{
		if(!$session_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'session_id'
			)), 200);

		/* Find a player given login session ID */
		$session = $this->player_model->findBySessionId($this->client_id, $this->site_id, $session_id);
		if(!$session)
			$this->response($this->error->setError('SESSION_NOT_VALID'), 200);
		$player = $this->player_model->readPlayer($session['pb_player_id'], $this->site_id, array(
			'cl_player_id',
			'username',
			'first_name',
			'last_name',
			'gender',
			'image',
			'exp',
			'level',
			'date_added',
			'birth_date'
		));

		$this->response($this->resp->setRespond($player), 200);
	}

	public function auth_post()
	{
		$required = $this->input->checkParam(array(
			'password'
		));
		$username = $this->input->post('username');
		$email = $this->input->post('email');
		if (!$email && !$username) {
			array_push($required, 'username', 'email');
		}
		if ($required) {
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		}

		$password = do_hash($this->input->post('password'));

		$player = null;
		if ($email) {
			$player = $this->player_model->getPlayerByEmail($this->site_id, $email);
		} elseif ($username && is_null($player)) {
			$player = $this->player_model->getPlayerByUsername($this->site_id, $username);
		} else {
			$player = null;
		}
		if (!$player || !isset($player['approve_status']) || $player['approve_status'] != "approved") {
			$this->response($this->error->setError('AUTHENTICATION_FAIL'), 200);
		}elseif (isset($player['locked']) && $player['locked']){
			$this->response($this->error->setError('ACCOUNT_IS_LOCKED'), 200);
		}

		$setting = $this->player_model->getSecuritySetting($this->client_id,$this->site_id);
		if (isset($setting['max_retries']) && ($setting['max_retries'] > 0) && ($player['login_attempt'] >= $setting['max_retries'])){
			$this->player_model->lockPlayer( $this->site_id, $player['_id']);
			$this->response($this->error->setError('ACCOUNT_IS_LOCKED'), 200);
		}
		$auth = $this->player_model->authPlayer($this->site_id, $player['_id'], $password);
		if (!$auth) {
			$this->player_model->increaseLoginAttempt($this->site_id,$player['_id']);
			$this->response($this->error->setError('AUTHENTICATION_FAIL'), 200);
		} else {
			$device_id = $this->input->post('device_id');
			if (!empty($device_id) && isset($player['device_id'])){
				//Change new device
				if(($device_id !== $player['device_id']) && !empty($player['phone_number'])){
					$this->response($this->error->setError('SMS_VERIFICATION_REQUIRED'), 200);
				}elseif(empty($player['phone_number'])){
					$this->response($this->error->setError('SMS_VERIFICATION_PHONE_NUMBER_NOT_FOUND'), 200);
				}
				$this->player_model->increaseLoginAttempt($this->client_id,$this->site_id);
			}
		}

		$this->player_model->resetLoginAttempt($this->site_id,$player['_id']);
		//trigger and log event
		$eventMessage = $this->utility->getEventMessage('login');
		$this->tracker_model->trackEvent('LOGIN', $eventMessage, array(
			'client_id' => $this->client_id,
			'site_id' => $this->site_id,
			'pb_player_id' => $player['_id'],
			'action_log_id' => null
		));
		//publish to node stream
		$this->node->publish(array(
			'pb_player_id' => $player['_id'],
			'action_name' => 'login',
			'action_icon' => 'fa-sign-in',
			'message' => $eventMessage
		), $this->validToken['domain_name'], $this->validToken['site_id']);

		/* Optionally, keep track of session */
		$session_id = get_random_code(40, true, true, true);

		$timeout = (isset($setting['timeout']))? ($setting['timeout'] > 0 ? $setting['timeout']:0): PLAYER_AUTH_SESSION_TIMEOUT;
		$session_expires_in = $timeout;
		if ($session_id) {
			$this->player_model->login($this->client_id, $this->site_id, $player['_id'], $session_id,
				$session_expires_in);
		}

		$this->response($this->resp->setRespond(array(
			'cl_player_id' => $player['cl_player_id'],
			'session_id' => $session_id
		)), 200);
	}

    public function verifyOTPCode_post($player_id = '')
    {
        $required = $this->input->checkParam(array(
            'code'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }

        $player = $this->player_model->getPlayerByPlayerId($this->site_id, $player_id);
        if (!$player) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        $code = $this->input->post('code');
        $result = $this->player_model->getPlayerOTPCode($player['_id'],$code);
        if (!$result) {
            $this->response($this->error->setError('SMS_VERIFICATION_CODE_INVALID'), 200);
        }

        if ($result['date_expire']->sec <= time()){
            $this->response($this->error->setError('SMS_VERIFICATION_CODE_EXPIRED'), 200);
        }

        $this->player_model->deleteOTPCode($result['code']);

        $this->response($this->resp->setRespond(), 200);
    }

	public function forgotPasswordEmail_post()
	{
		$required = $this->input->checkParam(array(
			'email'
		));
		if ($required) {
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		}

		$email = $this->input->post('email');
		$player = $this->player_model->getPlayerByEmail($this->site_id, $email);
		if (!$player) {
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		}
		$player['pwd_reset_code'] = $this->player_model->generatePasswordResetCode($player['_id']);

		$this->response($this->resp->setRespond(array(
			'url' => $this->config->item('CONTROL_DASHBOARD_URL') . 'player/password/reset/' . $player['pwd_reset_code'])
		), 200);
	}

	public function points_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		$input = array_merge($this->validToken, array(
			'pb_player_id' => $pb_player_id
		));
		//get player points
		$points['points'] = $this->player_model->getPlayerPoints($pb_player_id, $this->site_id);
		foreach($points['points'] as &$point)
		{
			$point['reward_name'] = $this->point_model->getRewardNameById(array_merge($input, array(
				'reward_id' => $point['reward_id']
			)));
            $point['reward_id'] = $point['reward_id']."";
			ksort($point);
		}
		$this->response($this->resp->setRespond($points), 200);
	}
	public function point_get($player_id = '', $reward = '')
	{
		$required = array();
		if(!$player_id)
			array_push($required, 'player_id');
		if(!$reward)
			array_push($required, 'reward');
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		$input = array_merge($this->validToken, array(
			'reward_name' => $reward
		));
		$reward_id = $this->point_model->findPoint($input);
		if(!$reward_id)
			$this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
		$point['point'] = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $this->site_id);
        $point['point'][0]['reward_id'] = $reward_id."";
		$point['point'][0]['reward_name'] = $reward;
		ksort($point);
		$this->response($this->resp->setRespond($point), 200);
	}

    public function pointLastUsed_get($player_id = '', $reward = '')
    {
        $required = array();
        if (!$player_id) {
            array_push($required, 'player_id');
        }
        if (!$reward) {
            array_push($required, 'reward');
        }
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        $input = array_merge($this->validToken, array(
            'reward_name' => $reward
        ));
        $reward_id = $this->point_model->findPoint($input);
        if (!$reward_id) {
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }
        $point['point'] = $this->player_model->getLastUsedPoint($pb_player_id, $reward_id, $this->site_id);
        $point['point'][0]['reward_id'] = $reward_id . "";
        $point['point'][0]['reward_name'] = $reward;
        $point['point'][0]['reward_last_used'] = datetimeMongotoReadable($point['point'][0]['date_modified']);
        unset($point['point'][0]['date_modified']);
        ksort($point);
        $this->response($this->resp->setRespond($point), 200);
    }

    public function pointLastCronModified_get($player_id = '', $reward = '')
    {
        $required = array();
        if (!$player_id) {
            array_push($required, 'player_id');
        }
        if (!$reward) {
            array_push($required, 'reward');
        }
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        $input = array_merge($this->validToken, array(
            'reward_name' => $reward
        ));
        $reward_id = $this->point_model->findPoint($input);
        if (!$reward_id) {
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }
        $point['point'] = $this->player_model->getLastCronModifiedPoint($pb_player_id, $reward_id, $this->site_id);
        $point['point'][0]['reward_id'] = $reward_id . "";
        $point['point'][0]['reward_name'] = $reward;
        $point['point'][0]['reward_last_filled'] = isset($point['point'][0]['date_cron_modified']) ? datetimeMongotoReadable($point['point'][0]['date_cron_modified']) : null;
        unset($point['point'][0]['date_cron_modified']);
        ksort($point);
        $this->response($this->resp->setRespond($point), 200);
    }

	public function point_history_get($player_id =''){
		$required = array();
		if(!$player_id){
			array_push($required, 'player_id');
		}
		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		}

		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id){
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		}


		$offset = ($this->input->get('offset'))?$this->input->get('offset'):0;			
		$limit = ($this->input->get('limit'))?$this->input->get('limit'):20;
        if($limit > 500){
            $limit = 500;
        }
		$reward_name = $this->input->get('point_name');

		$reward = array(
				'site_id'=>$this->site_id,
				'client_id'=>$this->validToken['client_id'],
				'reward_name'=>$reward_name
			);

		if($reward){
			$reward_id = $this->point_model->findPoint($reward);	
		}else{
			$reward_id = null;
		}

		$respondThis['points'] = $this->player_model->getPointHistoryFromPlayerID($pb_player_id, $this->site_id, $reward_id, $offset, $limit);

		$this->response($this->resp->setRespond($respondThis), 200);
	}

	public function quest_reward_history_get($player_id =''){
		$required = array();
		if(!$player_id){
			array_push($required, 'player_id');
		}
		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		}

		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id){
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		}

		$offset = ($this->input->get('offset'))?$this->input->get('offset'):0;
		$limit = ($this->input->get('limit'))?$this->input->get('limit'):20;
		if($limit > 500){
			$limit = 500;
		}

		$respondThis['rewards'] = $this->quest_model->getRewardHistoryFromPlayerID($this->client_id, $this->site_id, $pb_player_id, $offset, $limit);
		array_walk_recursive($respondThis, array($this, "convert_mongo_object"));

		$this->response($this->resp->setRespond($respondThis), 200);
	}

	public function action_get($player_id = '', $action = '', $option = 'time')
	{
		$required = array();
		if(!$player_id)
			array_push($required, 'player_id');
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		$actions = array();
		if($action)
		{
			$action_id = $this->action_model->findAction(array_merge($this->validToken, array(
				'action_name' => urldecode($action)
			)));
			if(!$action_id)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
			$actions['action'] = ($option == 'time') ? $this->player_model->getActionPerform($pb_player_id, $action_id, $this->site_id) : $this->player_model->getActionCount($pb_player_id, $action_id, $this->site_id);
		}
		else //get last action performed
		{
			if($option != 'time')
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
			$actions['action'] = $this->player_model->getLastActionPerform($pb_player_id, $this->site_id);
		}
		$this->response($this->resp->setRespond($actions), 200);
	}
	public function badge_get($player_id = '')
	{
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		//get player badge
		$badgeList = $this->player_model->getBadge($pb_player_id, $this->site_id);
		$this->response($this->resp->setRespond($badgeList), 200);
	}
	public function badgeAll_get($player_id = '')
	{
		$pb_player_id = null;
		if ($player_id) {
			$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
				'cl_player_id' => $player_id
			)));
			if(!$pb_player_id)
				$this->response($this->error->setError('USER_NOT_EXIST'), 200);
		}
		$badges = $this->badge_model->getAllBadges($this->validToken);
		if ($badges && $pb_player_id) foreach ($badges as &$badge) {
			$c = $this->player_model->getBadgeCount($this->site_id, $pb_player_id, new MongoId($badge['badge_id']));
			$badge['amount'] = $c;
		}
		$this->response($this->resp->setRespond($badges), 200);
	}
	public function claimBadge_post($player_id='', $badge_id='')
	{
		if(!$player_id || !$badge_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id',
				'badge_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
        try{
            $badge_id = new MongoId($badge_id);
        } catch (Exception $e) {
            $badge_id = $badge_id;
        }
		$result = $this->player_model->claimBadge($pb_player_id, $badge_id, $this->site_id, $this->client_id);
        if($result){
            $this->response($this->resp->setRespond($result), 200);
        }else{
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }
	}
	public function redeemBadge_post($player_id='', $badge_id='')
	{
		if(!$player_id || !$badge_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
				'player_id',
				'badge_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
			'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);
        try{
            $badge_id = new MongoId($badge_id);
        } catch (Exception $e) {
            $badge_id = $badge_id;
        }
		$result = $this->player_model->redeemBadge($pb_player_id, $badge_id, $this->site_id, $this->client_id);
        if($result){
            $this->response($this->resp->setRespond($result), 200);
        }else{
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }
	}
    public function rank_get($ranked_by, $limit = 20)
    {
        if(!$ranked_by)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'ranked_by'
            )), 200);
        if ($ranked_by == 'level') {
            $leaderboard = $this->player_model->getLeaderboardByLevel($limit, $this->validToken['client_id'], $this->validToken['site_id']);
        } else {
            $mode = $this->input->get('mode');
            switch ($mode) {
            case 'weekly':
                $leaderboard = $this->player_model->getWeeklyLeaderboard($ranked_by, $limit, $this->validToken['client_id'], $this->validToken['site_id']);
                break;
            case 'monthly':
                $leaderboard = $this->player_model->getMonthlyLeaderboard($ranked_by, $limit, $this->validToken['client_id'], $this->validToken['site_id']);
                break;
            default: // all-time
                $leaderboard = $this->player_model->getLeaderboard($ranked_by, $limit, $this->validToken['client_id'], $this->validToken['site_id']);
                break;
            }
        }
        $this->response($this->resp->setRespond($leaderboard), 200);
    }
    public function ranks_get($limit = 20)
    {
        $mode = $this->input->get('mode');
        switch ($mode) {
        case 'weekly':
            $leaderboards = $this->player_model->getWeeklyLeaderboards($limit, $this->validToken['client_id'], $this->validToken['site_id']);
            break;
        case 'monthly':
            $leaderboards = $this->player_model->getMonthlyLeaderboards($limit, $this->validToken['client_id'], $this->validToken['site_id']);
            break;
        default: // all-time
            $leaderboards = $this->player_model->getLeaderboards($limit, $this->validToken['client_id'], $this->validToken['site_id']);
            break;
        }
        $this->response($this->resp->setRespond($leaderboards), 200);
    }
    public function rankuser_get($player_id = '', $ranked_by = ''){
    	if($player_id == '' || $ranked_by == ''){
    		$this->response($this->error->setError('PARAMETER_MISSING', array(
    		    'ranked_by','player_id'
    		)), 200);
    	}else{
            $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
                'cl_player_id' => $player_id
            )));
            if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

            $mode = $this->input->get('mode');
            switch ($mode) {
            case 'weekly':
                $value = $this->player_model->getWeeklyPlayerReward($this->validToken['client_id'], $this->validToken['site_id'], $this->reward_model->findByName($this->validToken, $ranked_by), $pb_player_id);
                $c = $this->player_model->countWeeklyPlayersHigherReward($this->validToken['client_id'], $this->validToken['site_id'], $this->reward_model->findByName($this->validToken, $ranked_by), $value);
                $player = array(
                    'player_id' => $player_id,
                    'rank' => $c+1,
                    'ranked_by' => $ranked_by,
                    'ranked_value' => $value,
                );
                break;
            case 'monthly':
                $value = $this->player_model->getMonthlyPlayerReward($this->validToken['client_id'], $this->validToken['site_id'], $this->reward_model->findByName($this->validToken, $ranked_by), $pb_player_id);
                $c = $this->player_model->countMonthlyPlayersHigherReward($this->validToken['client_id'], $this->validToken['site_id'], $this->reward_model->findByName($this->validToken, $ranked_by), $value);
                $player = array(
                    'player_id' => $player_id,
                    'rank' => $c+1,
                    'ranked_by' => $ranked_by,
                    'ranked_value' => $value,
                );
                break;
            default:
                $players = $this->player_model->sortPlayersByReward($this->validToken['client_id'], $this->validToken['site_id'], $this->reward_model->findByName($this->validToken, $ranked_by));
                $cl_player_ids = array_map('index_cl_player_id', $players);
                $idx = array_search($player_id, $cl_player_ids);
                $player = ($idx !== false ? array(
                    'player_id' => $player_id,
                    'rank' => $idx+1,
                    'ranked_by' => $ranked_by,
                    'ranked_value' => $players[$idx]['value'],
                ) : array(
                    'player_id' => $player_id,
                    'rank' => count($players)+1,
                    'ranked_by' => $ranked_by,
                    'ranked_value' => 0,
                ));
                break;
            }
            $this->response($this->resp->setRespond($player), 200);
    	}
    }
	public function rankParam_get($action,$param)
	{
		// Check validity of action and parameter
		if(!$action)
			$this->response($this->error->setError('ACTION_NOT_FOUND', array(
					'action'
			)), 200);
		if(!$param)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
					'parameter'
			)), 200);

		$action_id = $this->action_model->findAction(array_merge($this->validToken, array(
				'action_name' => urldecode($action)
		)));
		$valid = false;
		if (!$action_id) {
			$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
		}

		// Action and parameter are valid !
		// Now, getting all input
		$input = $this->input->get();

		// default mode is sum
		$input['mode'] = isset($input['mode'])? $input['mode']: "sum";

		// default limit is infinite
		$input['limit'] = isset($input['limit'])? $input['limit']: -1;

		// default group_by is player_id the smallest resolution, it could be distrct/ area as well
		$input['group_by'] = (isset($input['group_by']) && $input['group_by'] !== 'cl_player_id')? $input['group_by']: 'cl_player_id';
		$input['action_name'] = $action;
		$input['param'] = $param;

		// Let's Rank !!
		$result = $this->player_model->getMonthLeaderboardsByCustomParameter($input,$this->validToken['client_id'],$this->validToken['site_id']);

		$this->response($this->resp->setRespond($result), 200);
	}

    public function level_get($level='')
    {
        if(!$level)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'level'
            )), 200);

        $level= $this->level_model->getLevelDetail($level, $this->validToken['client_id'], $this->validToken['site_id']);
        $this->response($this->resp->setRespond($level), 200);
    }
    public function levels_get()
    {
        $level= $this->level_model->getLevelsDetail($this->validToken['client_id'], $this->validToken['site_id']);
        $this->response($this->resp->setRespond($level), 200);
    }
    public function goods_get($player_id='')
    {
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        //get player goods
        $goodsList['goods'] = $this->player_model->getGoods($pb_player_id, $this->site_id);
        $this->response($this->resp->setRespond($goodsList), 200);
    }
    public function code_get($player_id='')
    {
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        $player = $this->player_model->getPlayerByPlayerId($this->site_id, $player_id, array('code'));
        if(!$player)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        if (!isset($player['code'])) {
            $player['code'] = $this->player_model->generateCode($player['_id']);
        }
        $this->response($this->resp->setRespond(array('code' => $player['code'])), 200);
    }

    public function requestOTPCode_post($player_id = '')
    {
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }

        $player = $this->player_model->getPlayerByPlayerId($this->site_id, $player_id);
        if (!$player) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $code = $this->player_model->generateOTPCode($player['_id']);

        $this->response($this->resp->setRespond(array('code' => $code)), 200);
    }

    public function contact_get($player_id=0, $N=10) {
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        /* FIXME: random conact from randomeuser.me */
        $players = array();
        $i = 0;
        while ($i < $N) {
            $player = json_decode(file_get_contents('http://api.randomuser.me/'));
            if (!isset($player->results[0]->user)) continue;
            $user = $player->results[0]->user;
            $user->cl_player_id = $i+1000;
            $user->first_name = $user->name->first;
            $user->last_name = $user->name->last;
            $user->phone = $user->cell;
            $user->image = $user->picture->thumbnail;
            $user->gender = $user->gender == 'male' ? 1 : 0;
            $user->birth_date = date(DATE_ISO8601, intval($user->dob));
            $user->registered = date(DATE_ISO8601, intval($user->registered));
            $user->type = $this->getSource($user);
            switch ($user->type) {
                case 'phone':
                    unset($user->email);
                    break;
                case 'g+':
                case 'fb':
                case 'tw':
                case 'gmail':
                default:
                    unset($user->phone);
                    break;
            }
            unset($user->name);
            unset($user->location);
            unset($user->picture);
            unset($user->password);
            unset($user->salt);
            unset($user->md5);
            unset($user->sha1);
            unset($user->sha256);
            unset($user->dob);
            unset($user->cell);
            unset($user->SSN);
            unset($user->PPS);
            unset($user->BSN);
            unset($user->TFN);
            unset($user->DNI);
            unset($user->NINO);
            unset($user->HETU);
            unset($user->INSEE);
            unset($user->nationality);
            unset($user->version);
            array_push($players, $user);
            $i++;
        }

        $this->response($this->resp->setRespond($players), 200);
    }
    private function getSource($user) {
        $r = rand(0, 100);
        if ($r <= 5) return 'g+';
        if ($r <= 15) return 'tw';
        if ($r <= 25) return 'gmail';
        if ($r <= 50) return 'fb';
        return 'phone';
    }
    public function deduct_reward_post($player_id) {
        /* param "player_id" */
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        /* param "reward" */
        $reward = $this->input->post('reward');
        if ($reward === false) $this->response($this->error->setError('PARAMETER_MISSING', array('reward')), 200);
        $reward_id = $this->reward_model->findByName(array('client_id' => $this->client_id, 'site_id' => $this->site_id), $reward);
        if (!$reward_id) $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);

        /* param "amount" */
        $amount = $this->input->post('amount');
        if ($amount === false) $this->response($this->error->setError('PARAMETER_MISSING', array('amount')), 200);
        $amount = intval($amount);

        /* param "force" */
        $force = $this->input->post('force');

        /* get current reward value */
        $record = $this->reward_model->getPlayerReward($this->client_id, $this->site_id, $pb_player_id, $reward_id);
        if (!$record) $this->response($this->error->setError('REWARD_FOR_USER_NOT_EXIST'), 200);

        /* set new reward value */
        if (!$force && $record['value'] < $amount) $this->response($this->error->setError('REWARD_FOR_USER_NOT_ENOUGH'), 200);
        $new_value = $record['value'] - $amount;
        if ($new_value < 0) $new_value = 0;
        $value_deducted = $record['value'] - $new_value;
        $this->reward_model->setPlayerReward($this->client_id, $this->site_id, $pb_player_id, $reward_id, $new_value);
        if ($reward == 'exp') {
            $this->player_model->setPlayerExp($this->client_id, $this->site_id, $pb_player_id, $new_value);
        }

        $this->response($this->resp->setRespond(array("old_value" => $record['value'], "new_value" => $new_value, "value_deducted" => $value_deducted)), 200);
    }
	public function total_get()
	{
		$log = array();
		$sum = 0;
		$prev = null;
		foreach ($this->player_model->new_registration($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('count' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			$sum += $value['value'];
			array_push($log, array($key => array('count' => $sum)));
		}
		$this->response($this->resp->setRespond($log), 200);
	}
	public function new_get()
	{
        // Limit
        $plan_id = $this->client_model->getPlanIdByClientId($this->validToken['client_id']);
        $limit = $this->client_model->getPlanLimitById(
            $this->client_plan,
            'others',
            'insight'
        );

        $now = new Datetime();
        $startDate      = new DateTime($this->input->get('from', TRUE));
        $endDate        = new DateTime($this->input->get('to', TRUE));

		$log = array();
		$prev = null;
		$this->player_model->set_read_preference_secondary();
		foreach ($this->player_model->new_registration(
            $this->validToken,
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')) as $key => $value) {
                $dDiff = $now->diff(new DateTime($value["_id"]));
                if ($limit && $dDiff->days > $limit) {
                    continue;
                }
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('count' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			array_push($log, array($key => array('count' => $value['value'])));
		}
		$this->player_model->set_read_preference_primary();
		$this->response($this->resp->setRespond($log), 200);
	}
	public function dauDay_get()
	{
		$log = array();
		$prev = null;
		$this->player_model->set_read_preference_secondary();
		foreach ($this->player_model->daily_active_user_per_day($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('count' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			array_push($log, array($key => array('count' => ($value['value'] instanceof MongoId ? 1 : $value['value']))));
		}
		$this->player_model->set_read_preference_primary();
		$this->response($this->resp->setRespond($log), 200);
	}
	public function mauDay_get()
	{
		$log = array();
		$prev = null;
		$this->player_model->set_read_preference_secondary();
		foreach ($this->player_model->monthy_active_user_per_day($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if (strtotime($key.' 00:00:00') <= time()) { // suppress future calculated results
				if ($prev) {
					$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
					while (strtotime($d) < strtotime($key)) {
						array_push($log, array($d => array('count' => 0)));
						$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
					}
				}
				$prev = $key;
				array_push($log, array($key => array('count' => ($value['value'] instanceof MongoId ? 1 : $value['value']))));
			} else break;
		}
		$this->player_model->set_read_preference_primary();
		$this->response($this->resp->setRespond($log), 200);
	}
	public function mauWeek_get()
	{
		$log = array();
		$prev = null;
		$this->player_model->set_read_preference_secondary();
		foreach ($this->player_model->monthy_active_user_per_week($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if (strtotime($key.' 00:00:00') <= time()) { // suppress future calculated results
				if ($prev) {
					$str = explode('-', $prev, 3);
					$year_month = $str[0].'-'.$str[1];
					$next_month = date('m', strtotime('+1 month', strtotime($prev)));
					$d = $str[2] == '01' ? $year_month.'-08' : ($str[2] == '08' ? $year_month.'-15' : ($str[2] == '15' ? $year_month.'-22' : $str[0].'-'.$next_month.'-01'));
					while (strtotime($d) < strtotime($key)) {
						array_push($log, array($d => array('count' => 0)));
						$str = explode('-', $d, 3);
						$year_month = $str[0].'-'.$str[1];
						$next_month = date('m', strtotime('+1 month', strtotime($prev)));
						$d = $str[2] == '01' ? $year_month.'-08' : ($str[2] == '08' ? $year_month.'-15' : ($str[2] == '15' ? $year_month.'-22' : $str[0].'-'.$next_month.'-01'));
					}
				}
				$prev = $key;
				array_push($log, array($key => array('count' => ($value['value'] instanceof MongoId ? 1 : $value['value']))));
			} else break;
		}
		$this->player_model->set_read_preference_primary();
		$this->response($this->resp->setRespond($log), 200);
	}
	public function mauMonth_get()
	{
		$log = array();
		$prev = null;
		$this->player_model->set_read_preference_secondary();
		foreach ($this->player_model->monthy_active_user_per_month($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if (strtotime($key.'-01 00:00:00') <= time()) { // suppress future calculated results
				if ($prev) {
					$d = date('Y-m', strtotime('+1 month', strtotime($prev.'-01 00:00:00')));
					while (strtotime($d.'-01 00:00:00') < strtotime($key.'-01 00:00:00')) {
						array_push($log, array($d => array('count' => 0)));
						$d = date('Y-m', strtotime('+1 month', strtotime($d.'-01 00:00:00')));
					}
				}
				$prev = $key;
				array_push($log, array($key => array('count' => ($value['value'] instanceof MongoId ? 1 : $value['value']))));
			} else break;
		}
		$this->player_model->set_read_preference_primary();
		$this->response($this->resp->setRespond($log), 200);
	}
	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
		);
		$cl_player_id = 'test1234';
		$image = 'profileimage.jpg';
		$email = 'test123@email.com';
		$username = 'test-1234';
		$token = $this->auth_model->getApiInfo($credential);
		echo '<br>createPlayer:<br>';
		$pb_player_id = $this->player_model->createPlayer(array_merge($token, array(
			'player_id' => $cl_player_id,
			'image' => $image,
			'email' => $email,
			'username' => $username,
			'birth_date' => '1982-09-08',
			'gender' => 1
		)));
		print_r($pb_player_id);
		echo '<br>readPlayer:<br>';
		$result = $this->player_model->readPlayer($pb_player_id, $token['site_id'], array(
			'cl_player_id',
			'pb_player_id',
			'username',
			'email',
			'image',
			'date_added',
			'birth_date'
		));
		print_r($result);
		echo '<br>updatePlayer:<br>';
		$result = $this->player_model->updatePlayer($pb_player_id, $token['site_id'], array(
			'username' => 'test-4567',
			'email' => 'test4567@email.com'
		));
		$result = $this->player_model->readPlayer($pb_player_id, $token['site_id'], array(
			'username',
			'email'
		));
		print_r($result);
		echo '<br>deletePlayer:<br>';
		$result = $this->player_model->deletePlayer($pb_player_id, $token['site_id']);
		print_r($result);
		echo '<br>';
		$cl_player_id = '1';
		echo '<br>getPlaybasisId:<br>';
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($token, array(
			'cl_player_id' => $cl_player_id
		)));
		print_r($pb_player_id);
		echo '<br>getClientPlayerId:<br>';
		$cl_player_id = $this->player_model->getClientPlayerId($pb_player_id, $token['site_id']);
		print_r($cl_player_id);
		echo '<br>';
		echo '<br>getPlayerPoints:<br>';
		$result = $this->player_model->getPlayerPoints($pb_player_id, $token['site_id']);
		print_r($result);
		$reward_id = $this->point_model->findPoint(array_merge($token, array('reward_name'=>'exp')));
		echo '<br>getPlayerPoint:<br>';
		$result = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $token['site_id']);
		print_r($result);		
		echo '<br>getLastActionPerform:<br>';
		$result = $this->player_model->getLastActionPerform($pb_player_id, $token['site_id']);
		print_r($result);
		echo '<br>getActionPerform:<br>';
		$action_id = $this->action_model->findAction(array_merge($token, array('action_name' => 'like')));
		$result = $this->player_model->getActionPerform($pb_player_id, $action_id, $token['site_id']);
		print_r($result);
		echo '<br>getActionCount:<br>';
		$result = $this->player_model->getActionCount($pb_player_id, $action_id, $token['site_id']);
		print_r($result);
		echo '<br>getBadge:<br>';
		$result = $this->player_model->getBadge($pb_player_id, $token['site_id']);
		print_r($result);
		echo '<br>getLastEventTime<br>';
		$result = $this->player_model->getLastEventTime($pb_player_id, $token['site_id'], 'LOGIN');
		print_r($result);
		echo '<br>';
		echo '<br>getLeaderboard<br>';
		$result = $this->player_model->getLeaderboard('exp', 20, $token['client_id'], $token['site_id']);
		print_r($result);
		echo '<br>getLeaderboards<br>';
		$result = $this->player_model->getLeaderboards(20, $token['client_id'], $token['site_id']);
		print_r($result);
		echo '</pre>';
	}

    private function validClPlayerId($cl_player_id){
        return ( ! preg_match("/^([-a-z0-9_-])+$/i", $cl_player_id)) ? FALSE : TRUE;
    }

    private function validTelephonewithCountry($number){
        return ( ! preg_match("/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d| 2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]| 4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/", $number)) ? FALSE : TRUE ;
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key) {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else if (get_class($item) === 'MongoDate') {
                $item =  datetimeMongotoReadable($item);
            }
        }
    }

	public function getAssociatedNode_get($player_id = '')
	{
		$result = array();
		if(!$player_id)
			$this->response($this->error->setError('PARAMETER_MISSING', array(
					'player_id'
			)), 200);
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
				'cl_player_id' => $player_id
		)));
		if(!$pb_player_id)
			$this->response($this->error->setError('USER_NOT_EXIST'), 200);

		$temp = $this->store_org_model->getAssociatedNodeOfPlayer($this->validToken['client_id'],$this->validToken['site_id'],$pb_player_id);
        foreach($temp as $entry){
            //$result[$key]["_id"]=$entry["_id"]."";
			$temp2= $this->store_org_model->retrieveNodeById($this->validToken['site_id'],$entry["node_id"]);
			$temp3['node_id']=$entry["node_id"]."";
			$temp3['name']=$temp2['name'];

			array_push($result, $temp3);
        }

		$this->response($this->resp->setRespond($result), 200);
	}

    public function getRole_get($player_id = '',$node_id='') {
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        if(!$node_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);

        $node= $this->store_org_model->retrieveNodeById($this->validToken['site_id'],new MongoId($node_id));
        if ($node == null) {
            $this->response($this->error->setError('STORE_ORG_NODE_NOT_FOUND'), 200);
        }

        $role_info = $this->store_org_model->getRoleOfPlayer($this->validToken['client_id'],$this->validToken['site_id'],$pb_player_id,new MongoId($node_id));
        if($role_info==null){
            $this->response($this->error->setError('STORE_ORG_PLAYER_NOT_EXISTS_WITH_NODE'), 200);
        }else{

            $org_info= $this->store_org_model->retrieveOrganizeById($this->validToken['client_id'],$this->validToken['site_id'],$node['organize']);
            $roles = array();
            $array_role = array_keys($role_info['roles']);
            foreach($array_role as $role) {
                $roles[]=array('role'=>$role,
                               'join_date'=>datetimeMongotoReadable($role_info['roles'][$role]));
            }

            $result=array(
                'organize_type'=>$org_info['name'],
                'roles'=>$roles
            );
            $this->response($this->resp->setRespond($result), 200);
        }
    }
    public function unlock_post($player_id = '') {
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        $this->player_model->unlockPlayer($this->validToken['site_id'],$pb_player_id);
        $this->response($this->resp->setRespond(), 200);
    }

    private function recurGetChildUnder($client_id, $site_id, $parent_node, &$result, &$layer = 0, $num = 0)
    {
        //array_push($result,$num);
        //array_push($result,$layer);
        if ($num++ <= $layer || $layer == 0) {
            array_push($result, $parent_node);
        }

        $nodes = $this->store_org_model->findAdjacentChildNode($client_id, $site_id, new MongoId($parent_node));
        if (isset($nodes)) {
            foreach ($nodes as $node) {

                $this->recurGetChildUnder($client_id, $site_id, $node['_id'], $result, $layer, $num);
            }
        } else {
            return $result;
        }
    }

    public function saleReport_get($player_id = '') {
        $result = array();

        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $month = $this->input->get('month');
        if(!$month)
            $month = date("m", time());
        $year = $this->input->get('year');
        if(!$year)
            $year = date("Y", time());
        $action = $this->input->get('action');
        if(!$action)
            $action = "sell";
        $parameter = $this->input->get('parameter');
        if (!$parameter) {
            $parameter = "amount";
        }

        $parent_node = $this->store_org_model->getAssociatedNodeOfPlayer($this->validToken['client_id'],$this->validToken['site_id'],$pb_player_id);

        foreach($parent_node as $node){
            $list = array();
            $this->recurGetChildUnder($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node['node_id']),$list);

            $table=$this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],$this->validToken['site_id'],$list,$action,$parameter,$month,$year,2);

            $this_month_time = strtotime($year . "-" . $month);
            $previous_month_time = strtotime('-1 month', $this_month_time);

            $current_month = date("m", $this_month_time);
            $current_year = date("Y", $this_month_time);

            $previous_month = date("m", $previous_month_time);
            $previous_year = date("Y", $previous_month_time);

            $current_month_sales =  $table[$current_year][$current_month][$parameter];
            $previous_month_sales = $table[$previous_year][$previous_month][$parameter];

			$temp2 = $this->store_org_model->retrieveNodeById($this->validToken['site_id'],$node['node_id']);
			$temp['name'] = $temp2['name'];
            $temp[$parameter] = $current_month_sales;
			$temp['previous_'.$parameter] = $previous_month_sales;

            if ($current_month_sales == 0 && $previous_month_sales == 0) {
                $temp['percent_changed'] = 0;
            } elseif ($previous_month_sales == 0) {
                $temp['percent_changed'] = 100;
            } else {
                $temp['percent_changed'] = (($current_month_sales - $previous_month_sales) * 100) / $previous_month_sales;
            }

            $node["node_id"]=$node["node_id"]."";

            array_push($result,array_merge(array('node_id' => $node['node_id']),$temp));
        }

        $this->response($this->resp->setRespond($result), 200);
    }
    private function password_validation($client_id,$site_id,$inhibited_str=''){
        $return_status = false;
        $setting = $this->player_model->getSecuritySetting($client_id,$site_id);
        if (isset($setting['password_policy'])){
            $password_policy = $setting['password_policy'];
            $rule = 'trim|xss_clean';
            if ($password_policy['min_char'] && $password_policy['min_char'] > 0){
                $rule = $rule.'|'.'min_length['.$password_policy['min_char'].']';
            }
            if ($password_policy['alphabet'] && $password_policy['numeric']){
                $rule = $rule.'|callback_require_at_least_number_and_alphabet';
            }
            elseif ($password_policy['alphabet']){
                $rule = $rule.'|callback_require_at_least_alphabet';
            }elseif($password_policy['numeric']){
                $rule = $rule.'|callback_require_at_least_number';
            }

            if ($password_policy['user_in_password'] && ($inhibited_str != '')){
                $rule = $rule.'|callback_word_in_password['.$inhibited_str.']';
            }
            $this->form_validation->set_rules('password', 'password', $rule);
            if ($this->form_validation->run()) {
                $return_status = true;
            } else {
                $return_status = false;
            }
        } else {
            $return_status =  true;
        }
        return $return_status;

    }

    public function require_at_least_number_and_alphabet($str)
    {
        if (preg_match('#[0-9]#', $str) && preg_match('#[a-zA-Z]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_number_and_alphabet',
            'The %s field require at least one numeric character and one alphabet');
        return false;
    }

    public function require_at_least_number($str)
    {
        if (preg_match('#[0-9]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_number',
            'The %s field require at least one number');
        return false;
    }

    public function require_at_least_alphabet($str)
    {
        if (preg_match('#[a-zA-Z]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_alphabet',
            'The %s field require at least one alphabet');
        return false;
    }
    public function word_in_password($str, $val)
    {
        if (strpos($str,$val) !== false) {
            $this->form_validation->set_message('word_in_password',
                'The %s field disallow to contain logon IDs');
            return false;
        }
        return true;
    }

}

function index_cl_player_id($obj) {
    return $obj['cl_player_id'];
}
?>
