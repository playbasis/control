<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Game extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);

        if (isset($query_data['tags']) && !empty($query_data['tags'])) {
            $query_data['tags'] = explode(',', $query_data['tags']);
        }

        if (!isset($query_data['status']) || (strtolower($query_data['status']) !== 'all')){
            $query_data['status'] = (isset($query_data['status']) && (strtolower($query_data['status'])==='false')) ? false : true;
        }else{
            unset($query_data['status']);
        }

        $games = $this->game_model->retrieveGame($this->client_id, $this->site_id, $query_data);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $games, 'processing_time' => $t)), 200);
    }
}