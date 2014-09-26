<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Redeem extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('goods_model');
        $this->load->model('player_model');
        $this->load->model('sms_model');
        $this->load->model('redeem_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('tracker_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    private function sendEngine($from, $to, $message)
    {
        $this->benchmark->mark('send_start');

        try {
            $this->client_model->permissionProcess(
                $this->client_id,
                $this->site_id,
                "notifications",
                "sms"
            );
            $access = true;
        } catch(Exception $e) {
            log_message('error', 'Error = '.$e->getMessage());
        }

        if ($access) {
            $validToken = $this->validToken;

            // send SMS
            $this->config->load("twilio",TRUE);
            $config = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);
            $config['api_version'] = $this->config->item('twilio')['api_version'];
            $this->load->library('twilio', $config);

            $response = $this->twilio->sms($from, $to, $message);
            if ($response->IsError) {
                log_message('error', 'Error sending SMS using Twilio, response = '.print_r($response, true));

                $this->response($this->error->setError('INTERNAL_ERROR'), 200);
            }
            $this->benchmark->mark('send_end');
            $processing_time = $this->benchmark->elapsed_time('send_start', 'send_end');
            $this->response($this->resp->setRespond(array('to'=>$to, 'from'=>$from, 'message'=>$message, 'processing_time' => $processing_time)), 200);
        }
        $this->response($this->error->setError('LIMIT_EXCEED'), 200);
    }

    public function send_post()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'message',
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);

        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id']);
        if (!$player)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        if (array_key_exists('phone_number', $player) && !empty($player['phone_number'])) {
            if($this->input->post('from')){
                $from = $this->input->post('from');
            }else{
                $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);
                $from = $sms_data['name'];
            }

            $message = $this->input->post('message');

            $this->sendEngine($from, $player['phone_number'], $message);
        }else{
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }

    }


    public function send_goods_post()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'ref_id',
            'message'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);

        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);


        $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id']);
        if (!$player)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        if (array_key_exists('phone_number', $player) && !empty($player['phone_number'])) {

            $ref_id = $this->input->post('ref_id');
            $redeemData = $this->redeem_model->findByReferenceId('goods', $ref_id);

            $message = $this->input->post('message');
            $message = str_replace('{{code}}', $redeemData['code'], $message);

            $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);

            $this->sendEngine($sms_data['name'], $player['phone_number'], $message);

        }else{
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }
    }
}
?>