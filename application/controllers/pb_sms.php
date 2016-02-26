<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Pb_sms extends REST2_Controller
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
    }

    private function sendEngine($type, $from, $to, $message)
    {
        $access = false;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "sms"
            );
            $access = true;
        } catch (Exception $e) {
            log_message('error', 'Error = ' . $e->getMessage());
        }

        if ($access) {
            $this->benchmark->mark('send_start');
            $validToken = $this->validToken;

            // send SMS
            $this->config->load("twilio", true);
            $config = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);
            $twilio = $this->config->item('twilio');
            $config['api_version'] = $twilio['api_version'];
            $this->load->library('twilio/twiliomini', $config);

            $response = $this->twiliomini->sms($from, $to, $message);
            $this->sms_model->log($validToken['client_id'], $validToken['site_id'], $type, $from, $to, $message,
                $response);
            if ($response->IsError) {
                log_message('error', 'Error sending SMS using Twilio, response = ' . print_r($response, true));
                $this->response($this->error->setError('INTERNAL_ERROR', $response), 200);
            }
            $this->benchmark->mark('send_end');
            $processing_time = $this->benchmark->elapsed_time('send_start', 'send_end');
            $this->response($this->resp->setRespond(array(
                'to' => $to,
                'from' => $from,
                'message' => $message,
                'processing_time' => $processing_time
            )), 200);
        }
        $this->response($this->error->setError('LIMIT_EXCEED'), 200);
    }

    public function sendTo_post()
    {
        $required = $this->input->checkParam(array('phone_number', 'message'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $validToken = $this->validToken;

        if ($this->input->post('from')) {
            $from = $this->input->post('from');
        } else {
            $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);
            $from = isset($sms_data['name']) ? $sms_data['name'] : $sms_data['number'];
        }

        $this->sendEngine('user', $from, $this->input->post('phone_number'), $this->input->post('message'));
    }

    public function send_post()
    {
        $required = $this->input->checkParam(array('player_id'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $not_message = $this->input->checkParam(array('message'));
        $not_template_id = $this->input->checkParam(array('template_id'));
        if ($not_message && $not_template_id) {
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

        if (array_key_exists('phone_number', $player) && !empty($player['phone_number'])) {
            if ($this->input->post('from')) {
                $from = $this->input->post('from');
            } else {
                $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);
                $from = isset($sms_data['name']) ? $sms_data['name'] : $sms_data['number'];
            }

            /* check valid template_id */
            $message = null;
            if (!$not_template_id) {
                $template = $this->sms_model->getTemplateByTemplateId($validToken['site_id'],
                    $this->input->post('template_id'));
                if (!$template) {
                    $this->response($this->error->setError('TEMPLATE_NOT_FOUND', $this->input->post('template_id')),
                        200);
                }
                $message = $template['body'];
            } else {
                $message = $this->input->post('message');
            }
            if (!isset($player['code']) && strpos($message, '{{code}}') !== false) {
                $player['code'] = $this->player_model->generateCode($pb_player_id);
            }
            $message = $this->utility->replace_template_vars($message, $player);

            $this->sendEngine('user', $from, $player['phone_number'], $message);
        } else {
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }
    }

    public function send_goods_post()
    {
        $required = $this->input->checkParam(array('player_id', 'ref_id'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $not_message = $this->input->checkParam(array('message'));
        $not_template_id = $this->input->checkParam(array('template_id'));
        if ($not_message && $not_template_id) {
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

        if (array_key_exists('phone_number', $player) && !empty($player['phone_number'])) {

            $ref_id = $this->input->post('ref_id');
            $redeemData = $this->redeem_model->findByReferenceId('goods', new MongoId($ref_id));

            /* check valid template_id */
            $message = null;
            if (!$not_template_id) {
                $template = $this->sms_model->getTemplateByTemplateId($validToken['site_id'],
                    $this->input->post('template_id'));
                if (!$template) {
                    $this->response($this->error->setError('TEMPLATE_NOT_FOUND', $this->input->post('template_id')),
                        200);
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

            $sms_data = $this->sms_model->getSMSClient($validToken['client_id'], $validToken['site_id']);

            $this->sendEngine('goods', isset($sms_data['name']) ? $sms_data['name'] : $sms_data['number'],
                $player['phone_number'], $message);

        } else {
            $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
        }
    }

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
        $results = $this->sms_model->recent($validToken['site_id'],
            isset($player['phone_number']) ? $player['phone_number'] : null, $since ? strtotime($since) : null);
        array_walk_recursive($results, array($this, 'convert_mongo_date'));
        $this->response($this->resp->setRespond($results), 200);
    }

    public function template_get($template_id = '')
    {
        $result = array();
        if ($template_id) {
            $template = $this->sms_model->getTemplateByTemplateId($this->site_id, $template_id);
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
            $result = $this->sms_model->listTemplates($this->site_id, array('name'), array('_id'));
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