<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Tracker extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('tracker_model');
        $this->load->model('player_model');
        $this->load->model('action_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        $token = $this->auth_model->getApiInfo($credential);
        $cl_player_id = '1';
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($token,
            array('cl_player_id' => $cl_player_id)));
        $action_name = 'like';
        $action_id = $this->action_model->findAction(array_merge($token, array('action_name' => $action_name)));
        echo '<br>trackAction:<br>';
        $result = $this->tracker_model->trackAction(array_merge($token, array(
            'pb_player_id' => $pb_player_id,
            'action_id' => $action_id,
            'action_name' => $action_name,
            'url' => 'testfilter'
        )));
        print_r($result);
        $action_log_id = $result;
        $event_type = 'LOGOUT';
        $event_message = 'test event message!';
        echo '<br>trackEvent:<br>';
        $result = $this->tracker_model->trackEvent($event_type, $event_message, array_merge($token, array(
            'pb_player_id' => $pb_player_id,
            'action_log_id' => $action_log_id,
        )));
        print_r($result);
        echo '</pre>';
    }
}

?>