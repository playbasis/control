<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once APPPATH . '/libraries/REST2_Controller.php';

define('NOT_FOUND', 'N/A');

class Node extends REST2_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
    }

    public function last_action_get()
    {
        $required = $this->input->checkParam(array(
            'channel',
            'player_id',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $channel = $this->input->get('channel');
        $cl_player_id = $this->input->get('player_id');
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken,
            array('cl_player_id' => $cl_player_id)));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST', $required), 200);
        }
        $action = $this->player_model->getLastActionPerform($pb_player_id, $this->validToken['site_id']);
        $action_name = $action ? $action['action_name'] : NOT_FOUND;
        $action_time = $action ? $action['time'] : NOT_FOUND;
        $message = 'Performed: ' . $action_time;
        $data = array(
            'pb_player_id' => $pb_player_id,
            'action_name' => $action_name,
            'message' => $message,
        );
        $player = array_merge($this->player_model->readPlayer($pb_player_id, $this->validToken['site_id'],
            array('cl_player_id', 'first_name', 'last_name', 'image', 'username')), $data);
        $this->node->publish($player, $channel, $this->validToken['site_id']);
        $this->response($this->resp->setRespond($data), 200);
    }
}