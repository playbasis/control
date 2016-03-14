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

        $name = $this->input->post('name');
        if ($name) {
            $playerInfo['name'] = $name;
        }

        $url = $this->input->post('url');
        if ($url) {
            $playerInfo['url'] = $url;
        }

        $port = $this->input->post('port');
        if ($port) {
            $playerInfo['port'] = $port;
        }

        $username = $this->input->post('user_name');
        if ($username) {
            $playerInfo['user_name'] = $username;
        }

        $password = $this->input->post('password');
        if ($password) {
            $playerInfo['password'] = $password;
        }

        $importType = $this->input->post('import_type');
        if ($importType) {
            $playerInfo['import_type'] = $importType;
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

    public function importSetting_get()
    {
        $data = $this->input->get();

        if ((!isset($data['client_id'])) || (!isset($data['site_id'])) || (!isset($data['import_type']))){
            $this->response($this->error->setError('PARAMETER_MISSING',200));
        }

        $importData = $this->import_model->retrieveLatestDataByImportType($data['client_id'], $data['site_id'], $data['import_type']);
        $this->response(array($this->resp->setRespond($importData))[0], 200);

    }

    public function processImport_post()
    {
        $importType = $this->input->post('import_type');
        $importData = $this->import_model->retrieveLatestDataByImportType($importType);

        $data = array(
            'client_id' => $importData['client_id'],
            'site_id' => $importData['site_id'],
        );

        if ($importData['import_type'] == ('player')){
            $url = $importData['url'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $jsonData = json_decode($result, true);
            $return = $this->player_model->bulkRegisterPlayer($jsonData, $data, null);
        } elseif ($importData['import_type'] == ('transaction')){

        } elseif ($importData['import_type'] == ('store_org')){

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