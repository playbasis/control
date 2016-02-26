<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Point extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('point_model');
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
        echo '<br>findPoint:<br>';
        $result = $this->point_model->findPoint(array_merge($token, array('reward_name' => 'point')));
        print_r($result);
        echo '<br>getRewardNameById:<br>';
        $result = $this->point_model->getRewardNameById(array_merge($token, array('reward_id' => $result)));
        print_r($result);
        echo '</pre>';
    }
}

?>