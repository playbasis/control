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

    public function itemList_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $item_id_list = array();

        $required = $this->input->checkParam(array(
            'game_name',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (empty($game)){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }

        // Get item list if stage_level or stage_name has been queried
        if ((isset($query_data['stage_level']) && !empty($query_data['stage_level'])) ||
            (isset($query_data['stage_name']) && !empty($query_data['stage_name']))) {
            $stage = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game[0]['_id'], $query_data);
            $item_id_list = (isset($stage[0]['item_id']) && !empty($stage[0]['item_id']) ? $stage[0]['item_id'] : null);
        }

        // Return empty array if item_id_list is null which means invalid stage input
        if (isset($item_id_list)) {
            $item = $this->game_model->retrieveItem($this->client_id, $this->site_id, $game[0]['_id'], $query_data, $item_id_list);
        }else{
            $item = array();
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $item, 'processing_time' => $t)), 200);
    }

    public function stageList_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $required = $this->input->checkParam(array(
            'game_name',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (empty($game)){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }

        $stage = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game[0]['_id'], $query_data);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $stage, 'processing_time' => $t)), 200);
    }

    private function getList($data, $objectType)
    {
        // Add object into list of array
        $list = array();
        foreach ($data as $val){
            try{
                array_push($list, new MongoId($val[$objectType]));
            }
            catch(MongoException $e){
                array_push($list, $val[$objectType]);
            }
        }
        return $list;
    }
}