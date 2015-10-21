<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Push extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('player_model');
        $this->load->model('push_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    private function sendEngine($type, $from, $to, $message)
    {
        // TODO: implement push notification here

        $this->response($this->resp->setRespond(array('to'=>$to, 'from'=>$from, 'message'=>$message)), 200);
    }

    public function send_post()
    {
        $player_id =$this->input->post('player_id');
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        $devices = $this->player_model->listDevices($this->client_id, $this->site_id, $pb_player_id, array('device_token', 'os_type'));
        if ($devices) foreach ($devices as $device) {
            $notificationInfo = array(
                'device_token' => $device['device_token'],
                'messages' => $this->input->post('message'),
                'data' => array(
                    'client_id' => $this->client_id,
                    'site_id' => $this->site_id
                ),
                'badge_number' => 1
            );
            $this->push_model->initial($notificationInfo,$device['os_type']);
        }
        $this->response($this->resp->setRespond(''), 200);
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
            $redeemData = $this->redeem_model->findByReferenceId('goods', new MongoId($ref_id));

            $message = $this->input->post('message');
            $message = str_replace('{{code}}', $redeemData['code'], $message);

            $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);

            $this->sendEngine('goods', isset($sms_data['name'])?$sms_data['name']:$sms_data['number'], $player['phone_number'], $message);

        }else{
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }
    }
    /*
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
    }*/

    public function deviceRegistration_post()
    {
        $player_id = $this->input->post('player_id');
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        $this->player_model->storeDeviceToken(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'pb_player_id' => $pb_player_id,
            'device_token' => $this->input->post('device_token'),
            'device_description' => $this->input->post('device_description'),
            'device_name' => $this->input->post('device_name'),
            'os_type' => $this->input->post('os_type')
        ));
        $this->response($this->resp->setRespond(''), 200);
    }
    /*
    public function adhocSend_post()
    {
        $player_id =$this->input->post('player_id');
        if(!$player_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        $devices = $this->player_model->listDevices($this->client_id, $this->site_id, $pb_player_id, array('device_token', 'os_type'));
        if ($devices) foreach ($devices as $device) {
            $notificationInfo = array(
                'device_token' => $device['device_token'],
                'messages' => $this->input->post('message'),
                'data' => array(
                    'client_id' => $this->client_id,
                    'site_id' => $this->site_id
                ),
                'badge_number' => 1
            );
            $this->push_model->initial($notificationInfo,$device['os_type']);
        }
        $this->response($this->resp->setRespond(''), 200);
    }*/
}
?>