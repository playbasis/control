<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once(APPPATH . 'controllers/engine.php');

class import extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('import_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function importSetting_post()
    {
 /*       $required = $this->input->checkParam(array(
            'email',
            'username'
        ));
        if (!$player_id) {
            array_push($required, 'player_id');
        }
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $client_id = $this->input->post('client_id');
        if ($client_id) {
            $playerInfo['client_id'] = $client_id;
        }

        $site_id = $this->input->post('site_id');
        if ($site_id) {
            $playerInfo['site_id'] = $site_id;
        }
        */
        $url = $this->input->post('url');
        if ($url) {
            $playerInfo['url'] = $url;
        }

        $port = $this->input->post('port');
        if ($port) {
            $playerInfo['port'] = $port;
        }

        $username = $this->input->post('username');
        if ($username) {
            $playerInfo['username'] = $username;
        }

        $password = $this->input->post('password');
        if ($password) {
            $playerInfo['password'] = $password;
        }

        $date = $this->input->post('action');
        if ($date) {
            $playerInfo['action'] = $date;
        }

        $date = $this->input->post('routine');
        if ($date) {
            $playerInfo['routine'] = $date;
        }

        $result = $this->import_model->insertData(
        array_merge($this->validToken, $playerInfo), 0);

        return $result;

    }

    public function retrieveUrl_get()
    {
        $url = $this->import_model->readUrl($this->validToken['client_id'], $this->validToken['site_id']);
        $url = $url['url'];

        //$result = file_get_contents($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonData = json_decode($result, true);

        $bank = $this->player_model->bulkRegisterPlayer($jsonData, $this->validToken, null);

        return $bank;
    }

    public function processImport_post()
    {
        $action = $this->input->post('action');
        $importData = $this->import_model->readLatestDataAction($this->validToken['client_id'], $this->validToken['site_id'], $action);

        if ($importData['action'] == ('player')){
            $url = $importData['url'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $jsonData = json_decode($result, true);
            $return = $this->player_model->bulkRegisterPlayer($jsonData, $this->validToken, null);
        } elseif ($importData['action'] == ('transaction')){

        } elseif ($importData['action'] == ('store_org')){

        } else {
            $this->response($this->error->setError('PARAMETER_MISSING'), 200);
        }

        if ($return) {
            $this->response($this->resp->setRespond(), 200);
        } else {
            $this->response($this->error->setError('ANONYMOUS_CANNOT_REFERRAL'), 200);
        }
    }

}