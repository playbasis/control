<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

class Push extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('player_model');
        $this->load->model('push_model');
        $this->load->model('redeem_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    private function sendEngine($type, $from, $to, $message)
    {
        // TODO: implement push notification here

        $this->response($this->resp->setRespond(array('to' => $to, 'from' => $from, 'message' => $message)), 200);
    }

    public function send_post()
    {
        /* check permission to send push notification in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "push"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $this->response($this->error->setError(
                    "LIMIT_EXCEED", array()), 200);
            } else {
                $this->response($this->error->setError(
                    "INTERNAL_ERROR", array()), 200);
            }
        }

        $player_id = $this->input->post('player_id');
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }
        
        $not_message = $this->input->checkParam(array('message'));
        $not_template_id = $this->input->checkParam(array('template_id'));
        if ($not_message && $not_template_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array("message/template_id")), 200);
        }

        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $message = null;
        if (!$not_template_id) {
            $template = $this->push_model->getTemplateByTemplateId($this->site_id, $this->input->post('template_id'));
            if (!$template) {
                $this->response($this->error->setError('TEMPLATE_NOT_FOUND', $this->input->post('template_id')), 200);
            }
            $message = $template['body'];
        } else {
            $message = $this->input->post('message');
        }

        $devices = $this->player_model->listDevices($this->client_id, $this->site_id, $pb_player_id, array('device_token', 'os_type'));
        $site_name = $this->client_model->findSiteNameBySiteId($this->site_id);
        if ($devices) {
            foreach ($devices as $device) {
                $notificationInfo = array(
                    'title' => $site_name,
                    'device_token' => $device['device_token'],
                    'messages' => $message,
                    'data' => array(
                        'player_id' => $player_id
                    ),
                    'badge_number' => 1
                );
                $api_key = $this->auth_model->getApikeyBySite($this->site_id);
                $params = array('notification_info' => http_build_query($notificationInfo) ,'type' => $device['os_type'], 'api_key' => $api_key);
                $this->utility->request('Push','sendPush', http_build_query($params, '', '&'));
                $this->push_model->log($notificationInfo, $device, $pb_player_id, $player_id);
            }
        }
        $this->response($this->resp->setRespond(''), 200);
    }

    public function send_goods_post()
    {
        /* check permission to send push notification in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "push"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $this->response($this->error->setError(
                    "LIMIT_EXCEED", array()), 200);
            } else {
                $this->response($this->error->setError(
                    "INTERNAL_ERROR", array()), 200);
            }
        }
        
        $required = $this->input->checkParam(array(
            'player_id',
            'ref_id',
            'message'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }


        $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id']);
        if (!$player) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $not_message = $this->input->checkParam(array('message'));
        $not_template_id = $this->input->checkParam(array('template_id'));
        if ($not_message && $not_template_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $ref_id = $this->input->post('ref_id');
        $redeemData = $this->redeem_model->findByReferenceId('goods', new MongoId($ref_id));
        if (!$redeemData) {
            $this->response($this->error->setError('REFERENCE_ID_INVALID'), 200);
        }

        /* check valid template_id */
        $message = null;
        if (!$not_template_id) {
            $template = $this->push_model->getTemplateByTemplateId($validToken['site_id'],
                $this->input->post('template_id'));
            if (!$template) {
                $this->response($this->error->setError('TEMPLATE_NOT_FOUND', $this->input->post('template_id')), 200);
            }
            $message = $template['body'];
        } else {
            $message = $this->input->post('message');
        }

        if (!isset($player['code']) && strpos($message, '{{code}}') !== false) {
            $player['code'] = $this->player_model->generateCode($pb_player_id);
        }
        $message = $this->utility->replace_template_vars($message,
            array_merge($player, array('coupon' => $redeemData['code'])));

        $devices = $this->player_model->listDevices($this->client_id, $this->site_id, $pb_player_id, array('device_token', 'os_type'));
        $site_name = $this->client_model->findSiteNameBySiteId($this->site_id);
        if ($devices) {
            foreach ($devices as $device) {
                $notificationInfo = array(
                    'title' => $site_name,
                    'device_token' => $device['device_token'],
                    'messages' => $message,
                    'data' => array(
                        'player_id' => $cl_player_id
                    ),
                    'badge_number' => 1
                );
                $api_key = $this->auth_model->getApikeyBySite($this->site_id);
                $params = array('notification_info' => http_build_query($notificationInfo) ,'type' => $device['os_type'], 'api_key' => $api_key);
                $this->utility->request('Push','sendPush', http_build_query($params, '', '&'));
                $this->push_model->log($notificationInfo, $device, $pb_player_id, $cl_player_id);
            }
        }
        $this->response($this->resp->setRespond(''), 200);
    }

    function push_async_get (){
        
        parse_str($this->input->get('notification_info'), $notificationInfo);
        $site_data = $this->auth_model->getClientSiteByApiKey($this->input->get('api_key'));
        $notificationInfo['data']['client_id'] = $site_data['client_id'];
        $notificationInfo['data']['site_id'] = $site_data['site_id'];
        $type = $this->input->get('type');
        $this->push_model->initial($notificationInfo, $type);
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
        $this->benchmark->mark('start');

        $required = $this->input->checkParam(array(
            'player_id',
            'device_token',
            'device_description',
            'device_name',
            'os_type'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $this->input->post('player_id')
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        if(strtolower($this->input->post('os_type')) != "ios" && strtolower($this->input->post('os_type')) != "android"){
            $this->response($this->error->setError('OS_TYPE_INVALID'), 200);
        }

        $result = $this->player_model->storeDeviceToken(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'pb_player_id' => $pb_player_id,
            'device_token' => $this->input->post('device_token'),
            'device_description' => $this->input->post('device_description'),
            'device_name' => $this->input->post('device_name'),
            'os_type' => strtolower($this->input->post('os_type'))
        ));
        if (!$result) {
            $this->response($this->error->setError('INTERNAL_ERROR'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function deviceDeRegistration_post()
    {
        $this->benchmark->mark('start');

        $not_player_id = $this->input->checkParam(array('player_id'));
        $not_device_token = $this->input->checkParam(array('device_token'));
        
        if ($not_player_id && $not_device_token) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('player_id/device_token')), 200);
        }

        if (!$not_player_id){
            //get playbasis player id
            $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
                'cl_player_id' => $this->input->post('player_id')
            )));

            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }
        
        if (!$not_device_token) {
            $device_token = $this->input->post('device_token');
            $devices = $this->player_model->getDeviceByToken($this->client_id, $this->site_id,$device_token);
            if (is_null($devices)) {
                $this->response($this->error->setError('DEVICE_NOT_EXIST'), 200);
            }
        }

        $this->player_model->deRegisterDevices($this->client_id, $this->site_id, 
                                               isset($pb_player_id) && !empty($pb_player_id)? $pb_player_id : null,
                                               isset($device_token) && !empty($device_token) ? $device_token : null);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
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
    public function recent_get()
    {
        /* process parameters */
        $required = $this->input->checkParam(array('player_id'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $cl_player_id = $this->input->get('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id']);
        if (!$player) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $since = $this->input->get('since');
        $results = $this->push_model->recent($validToken['site_id'], $cl_player_id, $since);
        array_walk_recursive($results, array($this, 'convert_mongo_date'));
        $this->response($this->resp->setRespond($results), 200);
    }

    public function template_get($template_id = '')
    {
        $result = array();
        if ($template_id) {
            $template = $this->push_model->getTemplateByTemplateId($this->site_id, $template_id);
            if (!$template) {
                $this->response($this->error->setError('TEMPLATE_NOT_FOUND', $template_id), 200);
            }
            $result = $template['body'];
            $player_id = $this->input->get('player_id');
            if ($player_id) {
                $validToken = array_merge($this->validToken, array(
                    'cl_player_id' => $player_id
                ));
                $pb_player_id = $this->player_model->getPlaybasisId($validToken);
                if (!$pb_player_id) {
                    $this->response($this->error->setError('USER_NOT_EXIST'), 200);
                }
                $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id']);
                if (!$player) {
                    $this->response($this->error->setError('USER_NOT_EXIST'), 200);
                }
                if (!isset($player['code']) && strpos($result, '{{code}}') !== false) {
                    $player['code'] = $this->player_model->generateCode($pb_player_id);
                }
                $result = $this->utility->replace_template_vars($result, array_merge($player));
            }
        } else {
            $result = $this->push_model->listTemplates($this->site_id, array('name'), array('_id'));
        }
        $this->response($this->resp->setRespond($result), 200);
    }

    private function convert_mongo_date(&$item, $key)
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