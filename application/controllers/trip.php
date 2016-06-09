<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Trip extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('Trip_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function getTrip_get()
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

        if(isset($query_data['finished']) && !empty($query_data['finished'])){
            $finished = $query_data['finished'];
        }

        if(isset($query_data['trip_id']) && !empty($query_data['trip_id'])){
            $trip_id = $query_data['trip_id'];
        }

        $result = $this->Trip_model->getTrip($client_id, $site_id, isset($finished) ? $finished : false, $pb_player_id,isset($trip_id) ? $trip_id : false);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }
    
    public function startTrip_post()
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

        $result = $this->Trip_model->addTrip($client_id, $site_id, $pb_player_id);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function finishTrip_post($trip_id)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        if(!$trip_id){
            $this->response($this->error->setError('PARAMETER_MISSING', 'trip_id'), 200);
        } else {
            $trip_data = $this->Trip_model->getDrive($client_id, $site_id, false,null, $trip_id);
            if (empty($trip_data)) {
                $this->response($this->error->setError('TRIP_NOT_EXIST'), 200);
            }
        }

        //To do list graph

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function getTripLog_get($trip_id)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        if(!$trip_id){
            $this->response($this->error->setError('PARAMETER_MISSING', 'trip_id'), 200);
        } else {
            $trip_data = $this->Trip_model->getTrip($client_id, $site_id, false,null, $trip_id);
            if (empty($trip_data)) {
                $this->response($this->error->setError('TRIP_NOT_EXIST'), 200);
            }
        }

        $result = $this->Trip_model->getDriveLog($client_id, $site_id, $trip_id);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function insertTripLog_post($trip_id)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        if(!$trip_id){
            $this->response($this->error->setError('PARAMETER_MISSING', 'trip_id'), 200);
        } else {
            $trip_data = $this->Trip_model->getTrip($client_id, $site_id, false, null, $trip_id);
            if (empty($trip_data)) {
                $this->response($this->error->setError('TRIP_NOT_EXIST'), 200);
            }
        }
        $drive_data = array(
            'client_id' => $client_id,
            'site_id'   => $site_id,
            'trip_id'   => $trip_id
        );

        if($this->input->post('speed')) {
            $drive_data['speed'] = $this->input->post('speed');
        }

        if($this->input->post('latitude')) {
            $drive_data['latitude'] = $this->input->post('latitude');
        }

        if($this->input->post('longitude')) {
            $drive_data['longitude'] = $this->input->post('longitude');
        }

        if($this->input->post('altitude')) {
            $drive_data['altitude'] = $this->input->post('altitude');
        }

        if($this->input->post('rpm')) {
            $drive_data['rpm'] = $this->input->post('rpm');
        }

        if($this->input->post('distance')) {
            $drive_data['distance'] = $this->input->post('distance');
        }

        $this->Trip_model->addDriveLog($drive_data);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }
}