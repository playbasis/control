<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Fitbit extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('Fitbit_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function getFitBitPlayer_get()
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $query_data = $this->input->get();

        if (isset($query_data['player_id']) && !empty($query_data['player_id'])) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $query_data['player_id']
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        $result = $this->Fitbit_model->getFitbitPlayer($client_id, $site_id, $pb_player_id);
        array_walk_recursive($result, array($this, "convert_mongo_object_and_category"));
        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function addFitBitPlayer_post()
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $query_data = $this->input->post();

        if (isset($query_data['player_id']) && !empty($query_data['player_id'])) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $query_data['player_id']
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        if(!(isset($query_data['fitbit_token']) && !empty($query_data['fitbit_token']))){
            $this->response($this->error->setError('PARAMETER_MISSING', 'fitbit_token'), 200);
        }

        $count = $this->Fitbit_model->findFitbitPlayer($client_id, $site_id, $pb_player_id);
        if($count){
            $this->response($this->error->setError('FITBIT_PLAYER_ALREADY_EXIST'), 200);
        }

        $result = $this->Fitbit_model->addFitbitPlayer($client_id, $site_id, $pb_player_id,$query_data['fitbit_token'],
            isset($query_data['subscription_id']) && !empty($query_data['subscription_id']) ? $query_data['subscription_id'] : $pb_player_id."");

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function updateFitBitPlayer_post($cl_player_id)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $query_data = $this->input->post();

        if($cl_player_id){
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $cl_player_id
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        } else {
            $this->response($this->error->setError('PARAMETER_MISSING', 'player_id'), 200);
        }

        if(!(isset($query_data['fitbit_token']) && !empty($query_data['fitbit_token']))){
            $this->response($this->error->setError('PARAMETER_MISSING', 'fitbit_token'), 200);
        }

        $result = $this->Fitbit_model->getFitbitPlayer($client_id, $site_id, $pb_player_id);

        if($result){
            $result = $this->Fitbit_model->updateFitbitPlayer($client_id, $site_id, $pb_player_id, $query_data['fitbit_token']);
        }
        else{
            $this->response($this->error->setError('FITBIT_PLAYER_NOT_EXIST'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function deleteFitBitPlayer_post($cl_player_id)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $query_data = $this->input->post();

        if($cl_player_id){
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $cl_player_id
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        } else {
            $this->response($this->error->setError('PARAMETER_MISSING', 'player_id'), 200);
        }

        $result = $this->Fitbit_model->getFitbitPlayer($client_id, $site_id, $pb_player_id);

        if($result){
            $result = $this->Fitbit_model->deleteFitbitPlayer($client_id, $site_id, $pb_player_id);
        }
        else{
            $this->response($this->error->setError('FITBIT_PLAYER_NOT_EXIST'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function getFitBitPlayerData_get($cl_player_id, $category_input=null, $date_input=null, $period_input=null)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $query_data = $this->input->get();
        
        if ($cl_player_id) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' =>  $cl_player_id
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }
        else{
            $this->response($this->error->setError('PARAMETER_MISSING', 'player_id'), 200);
        }
        $fitbit_player = $this->Fitbit_model->getFitbitPlayer($client_id, $site_id, $pb_player_id);
        
        if($category_input){
            $query_data['category'] = $category_input;
        }
        if($date_input){
            $query_data['date'] = $date_input;
        }
        if($period_input){
            $query_data['period'] = $period_input;
        }
        
        switch ($query_data['category']) {
            case "ACTIVITY":
                $category = "activities";
                break;
            case "BODY":
                $category = "body";
                break;
            case "BP":
                $category = "activities";
                break;
            case "FOOD":
                $category = "foods/log";
                break;
            case "GLUCOSE":
                $category = "glucose";
                break;
            case "HEART":
                $category = "heart";
                break;
            case "PROFILE":
                $category = "profile";
                break;
            case "SLEEP":
                $category = "sleep";
                break;
            default:
                $category = "profile";
                break;
        }

        if(!(isset($query_data['date']) && !empty($query_data['date']))) {
            $query_data['date'] = "today";
        }
        $result = $this->getFitBitData($fitbit_player['fitbit_token'], $category, 
            (isset($query_data['date']) && !empty($query_data['date']) && $category != "profile") ? "/date/".$query_data['date'] : null,
            (isset($query_data['period']) && !empty($query_data['period']) && $category != "profile") ? "/".$query_data['period'] : null);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function setFitBitSubscriptions($fitbit_token, $subscription_id, $method="post"){
        $url = "https://api.fitbit.com/1/user/-/apiSubscriptions/$subscription_id.json";
        $headers = [
            "Authorization: Bearer $fitbit_token"
        ];
        $response = $this->curl_request($url, [], $headers, $method);
        $response = json_decode($response, true);
        if(isset($response['errors'])){
            $this->response($this->error->setError('FITBIT_TOKEN_EXPIRED'), 200);
        }

        return $response;
    }

    private function getFitBitData($fitbit_token, $category, $date="", $period=""){
        $url = "https://api.fitbit.com/1/user/-/".$category.$date.$period.".json";
        $headers = [
            "Authorization: Bearer $fitbit_token"
        ];
        $response = $this->curl_request($url, [], $headers, "get");
        $response = json_decode($response, true);
        if(isset($response['errors'])){
            $this->response($this->error->setError('FITBIT_TOKEN_EXPIRED'), 200);
        }

        return $response;
    }

    private function curl_request($url, $postData, $headers, $method="post"){
        $posts = http_build_query($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method == 'post'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$posts);
        }
        else if($method == 'get'){
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        }else{
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        }
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $response = $response ? $response: curl_error($ch);
        curl_close ($ch);
        return $response;
    }

    private function convert_mongo_object_and_category(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }

    }
}
?>