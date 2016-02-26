<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

//require_once APPPATH . '/libraries/GCM/loader.php';
class Universe extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('global_player_model');
        $this->load->model('push_model');
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
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
    }

    public function register_post()
    {
        $playerInfo = array(
            'email' => $this->input->post('_id'),
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')

        );
        $this->global_player_model->createGlobalPlayer($playerInfo, null);
        $this->response($this->resp->setRespond(''), 200);

    }

    public function login_post()
    {
        $playerInfo = array(
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        );
        $result = $this->global_player_model->loginAction($playerInfo, 'login');
        $result = $result[0];
        $this->response($this->resp->setRespond($result['_id']), 200);
    }

    public function join_post()
    {
        $joinInfo = array(
            'player_id' => $this->input->post('player_id'),
            'client_id' => $this->input->post('client_id'),
            'site_id' => $this->input->post('site_id')
        );

        $this->global_player_model->requestClientSite($joinInfo);
        $this->response($this->resp->setRespond(''), 200);

    }

    public function searchClientSite_post()
    {
        $keyword = $this->input->post('company');
        $results = $this->global_player_model->searchClient($keyword);


        foreach ($results as $result) {
            //echo('id : '.$result['_id'].' company : '.$result['company']."\r\n");
            $sites = $this->global_player_model->searchSite($result['_id']);

            foreach ($sites as $site) {
                //echo('id : '.$site['_id'].' site : '.$site['site_name']."\r\n");
                $site = array(
                    'id' => $site['_id'],
                    'site' => $site['site_name']

                );

            }
            $company = array(
                'id' => $result['_id'],
                'company' => $result['company'],
                'sites' => $site
            );
        }
        $this->response($this->resp->setRespond($company), 200);

    }

    public function feature_post()
    {

        $client_id = $this->input->post('client_id');
        $site_id = $this->input->post('site_id');

        $menus = $this->global_player_model->searchFeatureForClient($client_id, $site_id);
        foreach ($menus as $menu) {
            //echo($menu['name'].' : '.$menu['_id']."\r\n");
            $feature = array(
                'id' => $menu['_id'],
                'feature' => $menu['name']
            );
        }
        $this->response($this->resp->setRespond($feature), 200);
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
        $this->response($this->resp->setRespond(''), 200);

    }

    public function deviceRegistration_post()
    {
        $deviceInfo = array(
            'player_id' => $this->input->post('player_id'),
            'site_id' => $this->input->post('site_id'),
            'device_token' => $this->input->post('device_token'),
            'device_description' => $this->input->post('device_description'),
            'device_name' => $this->input->post('device_name'),
            'type' => $this->input->post('type')
        );
        $this->global_player_model->storeDeviceToken($deviceInfo);
        $this->response($this->resp->setRespond(''), 200);
    }

    public function directMsg_post($data)
    {
        /*
        $data = array(
            'title' => $this->input->post('msg'),
            'reward' => 'badge',
            'type' => 'popup',
            'value' => 'unlocked',
            'text' => '100',
            'status' => 'confirm'
        );*/
        $type = $this->input->post('type');
        $data = array(
            'title' => $data['title'],
            'reward' => $data['badge'],
            'type' => 'exp',//$data['type'],
            'value' => $data['value'],
            'text' => 'description test',//$data['text'],
            'status' => $data['status']
        );

        $notificationInfo = array(
            'device_token' => $this->input->post('device_token'),
            //'messages' => $this->input->post('msg'),
            'messages' => 'Congratulations',
            'data' => $data,
            'badge_number' => 1
        );
        $this->push_model->initial($notificationInfo, $type);
        $this->response($this->resp->setRespond(''), 200);

    }

}


?>
