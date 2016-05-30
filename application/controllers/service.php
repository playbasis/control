<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Service extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('service_model');
        $this->load->model('point_model');
        $this->load->model('player_model');
        $this->load->model('tracker_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
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

    public function recent_point_get($player_id = '')
    {
        $offset = ($this->input->get('offset')) ? $this->input->get('offset') : 0;
        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : 50;

        $show_login = ($this->input->get('show_login')) ? $this->input->get('show_login') : false;
        $show_quest = ($this->input->get('show_quest')) ? $this->input->get('show_quest') : false;
        $show_redeem = ($this->input->get('show_redeem')) ? $this->input->get('show_redeem') : false;
        $show_quiz = ($this->input->get('show_quiz')) ? $this->input->get('show_quiz') : false;
        $pb_player_id = $player_id ? $this->player_model->getPlaybasisId(array_merge($this->validToken,
            array('cl_player_id' => $player_id))) : null;
        if ($player_id && !$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        if ($limit > 500) {
            $limit = 500;
        }
        $reward_name = $this->input->get('point_name');

        $reward = array(
            'site_id' => $this->site_id,
            'client_id' => $this->client_id,
            'reward_name' => $reward_name
        );

        if ($reward) {
            $reward_id = $this->point_model->findPoint($reward);
        } else {
            $reward_id = null;
        }

        $respondThis['points'] = $this->service_model->getRecentPoint($this->site_id, $reward_id, $pb_player_id,
            $offset, $limit, $show_login, $show_quest, $show_redeem, $show_quiz);

        $this->response($this->resp->setRespond($respondThis), 200);
    }

    public function recent_activities_get()
    {
        $offset = ($this->input->get('offset')) ? $this->input->get('offset') : 0;
        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : 50;
        $last_read_activity_id = ($this->input->get('last_read_activity_id')) ? $this->input->get('last_read_activity_id') : null;

        $player_id = ($this->input->get('player_id')) ? $this->input->get('player_id') : 0;
        $pb_player_id = $player_id ? $this->player_model->getPlaybasisId(array_merge($this->validToken,
            array('cl_player_id' => $player_id))) : null;
        if ($player_id && !$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $mode = $this->input->get('mode') ? $this->input->get('mode') : 'all';
        if ($mode != 'all' && !$pb_player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        }

        $respondThis['activities'] = $this->service_model->getRecentActivities($this->site_id, $offset,
            $limit > 500 ? 500 : $limit, $pb_player_id, $last_read_activity_id, $mode);
        $this->response($this->resp->setRespond($respondThis), 200);
    }

    public function detail_activity_get($activity_id = '')
    {
        if (!$activity_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('activity_id')), 200);
        }
        $event_id = new MongoId($activity_id);
        $activity = $this->service_model->getEventById($this->site_id, $event_id);
        if (!$activity) {
            $this->response($this->error->setError('EVENT_NOT_EXIST'), 200);
        }

        $respondThis['activity'] = $this->service_model->getSocialActivitiesOfEventId($this->site_id, $event_id);
        $this->response($this->resp->setRespond($respondThis), 200);
    }

    public function like_activity_post($activity_id = '')
    {
        if (!$activity_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('activity_id')), 200);
        }
        $activity = $this->service_model->getEventById($this->site_id, new MongoId($activity_id));
        if (!$activity) {
            $this->response($this->error->setError('EVENT_NOT_EXIST'), 200);
        }
        $player_id = ($this->input->post('player_id'));
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        }
        $from_pb_player_id = $player_id ? $this->player_model->getPlaybasisId(array_merge($this->validToken,
            array('cl_player_id' => $player_id))) : null;
        $this->tracker_model->trackSocial(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'event_id' => $activity['_id'],
            'pb_player_id' => $activity['pb_player_id'],
            'from_pb_player_id' => $from_pb_player_id,
            'action_name' => 'like',
            'message' => null,
        ));
        $this->response($this->resp->setRespond(array('result' => true)), 200);
    }

    public function comment_activity_post($activity_id = '')
    {
        if (!$activity_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('activity_id')), 200);
        }
        $activity = $this->service_model->getEventById($this->site_id, new MongoId($activity_id));
        if (!$activity) {
            $this->response($this->error->setError('EVENT_NOT_EXIST'), 200);
        }
        $player_id = ($this->input->post('player_id'));
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        }
        $from_pb_player_id = $player_id ? $this->player_model->getPlaybasisId(array_merge($this->validToken,
            array('cl_player_id' => $player_id))) : null;
        $this->tracker_model->trackSocial(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'event_id' => $activity['_id'],
            'pb_player_id' => $activity['pb_player_id'],
            'from_pb_player_id' => $from_pb_player_id,
            'action_name' => 'comment',
            'message' => $this->input->post('message') ? $this->input->post('message') : null,
        ));
        $this->response($this->resp->setRespond(array('result' => true)), 200);
    }

    public function domain_get()
    {
        $data_token = $this->validToken;
        $res['site_name'] = $data_token['site_name'];
        $this->response($this->resp->setRespond($res), 200);
    }

    public function reset_point_post()
    {
        $reward_name = $this->input->post('point_name');

        if (strtolower($reward_name) == "exp") {
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }

        $reward = array(
            'site_id' => $this->site_id,
            'client_id' => $this->client_id,
            'reward_name' => $reward_name
        );

        if ($reward) {
            $reward_id = $this->point_model->findPoint($reward);
        } else {
            $reward_id = null;
        }

        if ($reward_id) {
            $this->service_model->resetPlayerPoints($this->site_id, $this->client_id, $reward_id, $reward_name);
        } else {
            $this->response($this->error->setError('REWARD_NOT_FOUND'), 200);
        }

        $this->response($this->resp->setRespond(array("reset" => true)), 200);
    }
}