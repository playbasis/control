<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

class GlobalPlayer extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('global_player_model');
        $this->load->model('push_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
    }

    public function mytest_get()
    {
        echo "mytest success";
    }

    public function register_post()
    {
        $playerInfo = array(
            'email' => $this->input->post('_id'),
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')

        );
        $this->global_player_model->createGlobalPlayer($playerInfo, null);

    }

    public function login_post()
    {
        $playerInfo = array(
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        );
        $result = $this->global_player_model->loginAction($playerInfo, 'login');
        //echo('login success');
        $result = $result[0];
        echo($result['_id']);
    }

    public function join_post()
    {
        $joinInfo = array(
            'player_id' => $this->input->post('player_id'),
            'client_id' => $this->input->post('client_id'),
            'site_id' => $this->input->post('site_id')
        );

        $this->global_player_model->requestClientSite($joinInfo);
        echo('Send request success');

    }

    public function searchClientSite_post()
    {
        $keyword = $this->input->post('company');
        $searchInfo = array(
            'company' => $keyword
        );
        $results = $this->global_player_model->searchClient($keyword);

        foreach ($results as $result) {
            echo('id : ' . $result['_id'] . ' company : ' . $result['company'] . "\r\n");
            $sites = $this->global_player_model->searchSite($result['_id']);

            foreach ($sites as $site) {
                echo('id : ' . $site['_id'] . ' site : ' . $site['site_name'] . "\r\n");

            }
        }

    }

    public function feature_post()
    {

        $client_id = $this->input->post('client_id');
        $site_id = $this->input->post('site_id');

        $menus = $this->global_player_model->searchFeatureForClient($client_id, $site_id);
        foreach ($menus as $menu) {
            echo($menu['name'] . ' : ' . $menu['_id'] . "\r\n");
        }
    }

    public function service_post()
    {
        $serviceInfo = array(
            'player_id' => $this->input->post('player_id'),
            'feature_id' => $this->input->post('feature_id'),
            'site_id' => $this->input->post('site_id'),
            'service_id' => $this->input->post('service_id'),
            'status' => $this->input->post('status')
        );
        $this->global_player_model->chooseService($serviceInfo);

    }

    public function index_get($player_id = '')
    {
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }
        echo('index_get');
    }

    public function index_post($player_id = '')
    {
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }
        echo('index_post');
    }

    public function deviceRegistration_post()
    {
        //echo('device');exit;
        $deviceInfo = array(
            'player_id' => $this->input->post('player_id'),
            'site_id' => $this->input->post('site_id'),
            'device_token' => $this->input->post('device_token'),
            'device_description' => $this->input->post('device_description')
        );
        //print_r($deviceInfo);exit;
        $this->global_player_model->storeDeviceToken($deviceInfo);
    }

    public function directMsg_post()
    {
        $temp = new DateTime('now');
        $notificationInfo = array(
            'device_token' => $this->input->post('device_token'),
            'messages' => $this->input->post('msg'),
            'badge_number' => 9
        );
        print_r($notificationInfo);
        //print_r($temp);
        $this->push_model->initial($notificationInfo);
        //$this->push_model->server($notificationInfo);
        echo('Send Notification Success');

    }
}

?>
