<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Custompoint extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reward_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function pending_get()
    {
        $data = $this->input->get();
        $data['client_id'] = $this->validToken['client_id'];
        $data['site_id'] = $this->validToken['site_id'];
        if (isset($data['to']) && strtotime($data['to'])){
            $data['to'] = new MongoDate(strtotime($data['to']));
        }
        if (isset($data['from']) && strtotime($data['from'])){
            $data['from'] = new MongoDate(strtotime($data['from']));
        }
        if (isset($data['player_list']) && !empty($data['player_list'])){
            $data['player_list'] = explode(",",$data['player_list']);
        }
        $pending_list = $this->reward_model->listPendingRewards($data);
        foreach ($pending_list as &$item)
        {
            $item['pending_id'] = $item['_id']->{'$id'};
            unset($item['_id']);
        }
        array_walk_recursive($pending_list, array($this, "convert_mongo_object"));
        $this->response($this->resp->setRespond($pending_list), 200);
    }

    public function approval_post()
    {
        $required = $this->input->checkParam(array(
            'pending_list',
            'approve'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $data = array(
            'client_id' => $this->validToken['client_id'],
            'site_id' => $this->validToken['site_id']
        );
        $approve = $this->input->post('approve') === "true" ? true : false;
        $pending_list = explode(",",$this->input->post('pending_list'));
        $response = array();
        if (is_array($pending_list)) foreach ($pending_list as $pending_id){
            try{
                $data['pending_id'] = new MongoId($pending_id);
                $status = $this->reward_model->approvePendingReward($data,$approve);
                array_push($response, array('pending_id' => $pending_id, 'status' => $status ? "success" : "Pending ID not found"));
            } catch (Exception $e){
                array_push($response, array('pending_id' => $pending_id, 'status' => "Pending ID is invalid"));
            }
        }
        $this->response($this->resp->setRespond($response), 200);
    }
    private function convert_mongo_object(&$item, $key)
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