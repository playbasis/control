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
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $message = $this->input->post('message');

        $this->sendEngine($from, $to, $message);
    }


    public function send_goods_post()
    {
        $this->benchmark->mark('goods_redeem_start');

        //Now type support only Goods
        //item_id it's a type of id goods_id
        $required = $this->input->checkParam(array(
            'player_id',
            'referer_id',
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

            $referer_id = $this->input->post('referer_id');

            $redeemData = $this->Redeem_model()->getReferrer($referer_id);

            $message = $redeemData['item']['sms_message'];
            if(isset($goodsData['name'])){
                $message = str_replace('{{goods_name}}', $redeemData['item']['name'], $message);
            }
            if(isset($goodsData['group'])){
                $message = str_replace('{{goods_group}}', $redeemData['item']['group'], $message);
            }
            if(isset($player['username'])){
                $message = str_replace('{{username}}', $redeemData['item']['username'], $message);
            }

            $this->sendEngine($redeemData['item']['sms_from'], $player['phone_number'], $message);

        }else{
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }
    }
}
?>