<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Auth extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function index_post()
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'api_secret'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $API['key'] = $this->input->post('api_key');
        $API['secret'] = $this->input->post('api_secret');
        $clientInfo = $this->auth_model->getApiInfo($API);
        if ($clientInfo) {
            $token = $this->auth_model->generateToken(array_merge($clientInfo, $API));
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }

    public function renew_post()
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'api_secret'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $API['key'] = $this->input->post('api_key');
        $API['secret'] = $this->input->post('api_secret');
        $clientInfo = $this->auth_model->getApiInfo($API);
        if ($clientInfo) {
            $token = $this->auth_model->renewToken(array_merge($clientInfo, $API));
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }
    /*public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        echo '<br>getApiInfo:<br>';
        $result = $this->auth_model->getApiInfo($credential);
        print_r($result);
        echo 'site id: ' . $result['site_id']->{'$id'};
        echo '<br>';
        echo 'client id: ' . $result['client_id']->{'$id'};
        echo '<br>';
        echo '<br>generateToken:<br>';
        $result = $this->auth_model->generateToken(array_merge($result, $credential));
        print_r($result);
        echo '<br>findToken:<br>';
        $result = $this->auth_model->findToken($result['token']);
        print_r($result);
        echo '<br>createToken:<br>';
        $result = $this->auth_model->createToken($result['client_id'], $result['site_id']);
        print_r($result);
        echo '<br>createTokenFromAPIKey<br>';
        $result = $this->auth_model->createTokenFromAPIKey($credential['key']);
        print_r($result);
        echo '</pre>';
    }*/
}

?>
