<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Service extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	/*public function index_get($param1)
	{
		$data = array(
			'status' => true,
			'message' => 'REST service BY PB ENGINE',
			'data' => $param1,
			'time' => date('r e'),
			'timestamp' => now()
		);
		$this->response($data, 200);
	}
	public function test_get($param1 = 'parameter 1 undefined', $param2 = 'parameter 2 undefined')
	{
		$data = array(
			'status' => true,
			'message' => 'simple REST service',
			'data' => $param1 . ' :: ' . $param2,
			'time' => date('r e'),
			'timestamp' => now()
		);
		$this->response($data, 200);
	}
	public function users_get()
	{
		$data = array(
			'user' => array(
				array(
					'name' => 'PM Master',
					'age' => 24
				),
				array(
					'name' => 'Aquario',
					'age' => 24
				)
			),
			'status' => true,
			'time' => date('r e'),
			'timestamp' => now()
		);
		$this->response($data, 200);
	}
	public function test_post()
	{
		$data = array(
			'status' => true,
			'time' => date('r e'),
			'timestamp' => now()
		);
		$data['post_data'] = $this->input->post();
		$this->response($data, 200);
	}*/
/*
	public function testCounter_get()
	{
		$this->load->model('engine/jigsaw', 'jg');
		$exInfo = array();
		$status = $this->jg->counter(array(
			'counter_value' => 5,
			'interval' => 0,
			'interval_unit' => 'second'
		), array(
			'pb_player_id' => 1,
			'rule_id' => 1,
			'jigsaw_id' => 1
		), $exInfo);
		var_dump($exInfo);
		var_dump($status);
	}
	public function testCooldown_get()
	{
		$this->load->model('engine/jigsaw', 'jg');
		$exInfo = array();
		$status = $this->jg->cooldown(array(
			'cooldown' => 180
		), array(
			'pb_player_id' => 1,
			'rule_id' => 1,
			'jigsaw_id' => 1
		), $exInfo);
		var_dump($exInfo);
		var_dump($status);
	}
	public function testdate_get()
	{
		$datediff = date_diff(new DateTime('2013-01-22 20:00:00'), new DateTime('2013-01-15 20:01:00'));
		var_dump($datediff->d);
		var_dump($datediff->h);
		var_dump($datediff->i);
		echo date('r', strtotime("first day of next month 15:00") + (25 - 1) * 3600 * 24);
		echo date('d', strtotime("last day of next month"));
		//echo date('r',1359417600);
	}
	public function testUpdateReward_get()
	{
		$this->load->model('client_model');
		$this->client_model->updatePlayerPointReward(2, 10, 1, 1);
	}
	public function testActivityStream_get($input)
	{
		$this->load->model('tool/node_stream', 'activity_stream');
		$info['domain_name'] = 'localhost';
		$data = array(
			'test' => $input
		);
		$this->activity_stream->publish($data, $info);
	}
	public function testGetbadges_get($cid, $sid)
	{
		$this->load->model('badge_model');
		var_dump($this->badge_model->getAllBadges(array(
			'client_id' => $cid,
			'site_id' => $sid
		)));
	}
	public function testGetcollections_get($cid, $sid)
	{
		$this->load->model('badge_model');
		var_dump($this->badge_model->getAllCollection(array(
			'client_id' => $cid,
			'site_id' => $sid
		)));
	}
	public function uploadUsers_get()
	{
		if(!$handle = fopen("users.csv", "r"))
			return;
		$this->load->model('player_model');
		$fields = fgetcsv($handle, 1000, ',');
		var_dump($fields);
		$fieldIndex = array();
		foreach($fields as $key => $value)
		{
			$fieldIndex[$value] = $key;
		}
		$count = 0;
		$startCount = 4882;
		while($data = fgetcsv($handle, 1000, ','))
		{
			//var_dump($data);
			$count++;
			if($count <= $startCount)
				continue;
			$genderStr = $data[$fieldIndex['gender']];
			$gender = 0;
			if($genderStr == 'Male')
				$gender = 1;
			else if($genderStr == 'Female')
				$gender = 2;
			$playerInfo = array(
				'client_id' => 3,
				'site_id' => 15,
				'email' => $data[$fieldIndex['email']],
				'first_name' => $data[$fieldIndex['first_name']],
				'last_name' => $data[$fieldIndex['last_name']],
				'player_id' => $data[$fieldIndex['user_id']],
				'gender' => $gender,
				'username' => $data[$fieldIndex['username']],
				'image' => $data[$fieldIndex['image_url']]
			);
			if($data[$fieldIndex['date_of_birth']])
			{
				$timestamp = strtotime($data[$fieldIndex['date_of_birth']]);
				$playerInfo['birth_date'] = date('Y-m-d', $timestamp);
			}
			var_dump($playerInfo['player_id']);
			$this->player_model->createPlayer($playerInfo);
		}
	}
*/

    public function point_history_get()
    {
        $required = $this->input->checkParam(array(
            'api_key'
        ));
        if($required){
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $required = array();

        if($required){
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));

        if(!$validToken){
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
        }
        $site_id = $validToken['site_id'];


        $offset = ($this->input->get('offset'))?$this->input->get('offset'):0;
        $limit = ($this->input->get('limit'))?$this->input->get('limit'):50;
        if($limit > 500){
            $limit = 500;
        }
        $reward_name = $this->input->get('point_name');

        $reward = array(
            'site_id'=>$site_id,
            'client_id'=>$validToken['client_id'],
            'reward_name'=>$reward_name
        );

        if($reward){
            $reward_id = $this->point_model->findPoint($reward);
        }else{
            $reward_id = null;
        }

        $respondThis['points'] = $this->player_model->getPointHistory($site_id, $reward_id, $offset, $limit);

        $this->response($this->resp->setRespond($respondThis), 200);
    }
}