<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once APPPATH . '/libraries/REST2_Controller.php';

/**
 * Endpoint for Sending Email via Amazon Simple Email Service (SES)
 *
 * @see /application/libraries/Amazon_ses.php
 */
class Email extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/error', 'error');
        $this->load->model('email_model');
        $this->load->model('client_model');
        $this->load->model('redeem_model');
        $this->load->model('player_model');
    }

    public function send_post()
    {
        /* check permission to send email in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "email"
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

        /* process parameters */
        $required = $this->input->checkParam(array('from', 'subject'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $required_to = $this->input->checkParam(array('to'));
        $required_bcc = $this->input->checkParam(array('bcc'));
        if ($required_to && $required_bcc) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required_to), 200);
        }

        /* variables */
        $from = $this->input->post('from');
        $to = $this->input->post('to') ? explode(',', $this->input->post('to')) : array();
        $bcc = $this->input->post('bcc') ? explode(',', $this->input->post('bcc')) : array();
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        if ($message == false) {
            $message = '';
        } // $message is optional

        $this->processEmail($from, $to, $bcc, $subject, $message);
    }

    public function send_goods_post()
    {
        /* check permission to send email in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "email"
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

        /* process parameters */
        $required = $this->input->checkParam(array('player_id', 'ref_id', 'subject'));
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
        $ref_id = $this->input->post('ref_id');
        $redeemData = $this->redeem_model->findByReferenceId('goods', new MongoId($ref_id));
        if (!$redeemData) {
            $this->response($this->error->setError('REFERENCE_ID_INVALID'), 200);
        }
        /* check valid template_id */
        $message = null;
        if (!$not_template_id) {
            $template = $this->email_model->getTemplateByTemplateId($validToken['site_id'],
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

        /* variables */
        $email = $player['email'];
        /* before send, check whether custom domain was set by user or not*/
        $from = verify_custom_domain($this->client_id, $this->site_id);
        $to = array($email);
        $subject = $this->input->post('subject');

        $this->processEmail($from, $to, null, $subject, $message);
    }

    public function send_player_post()
    {
        /* check permission to send email in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "notifications",
                "email"
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

        /* process parameters */
        $required = $this->input->checkParam(array('player_id', 'subject'));
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

        /* check valid template_id */
        $message = null;
        if (!$not_template_id) {
            $template = $this->email_model->getTemplateByTemplateId($validToken['site_id'],
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
        $message = $this->utility->replace_template_vars($message, $player);

        /* variables */
        $email = $player['email'];
        /* before send, check whether custom domain was set by user or not*/
        $from = verify_custom_domain($this->client_id, $this->site_id);
        $to = array($email);
        $subject = $this->input->post('subject');

        $this->processEmail($from, $to, null, $subject, $message);
    }

    private function processEmail($from, $to, $bcc, $subject, $message)
    {
        if (!empty($to)) { // 'to-cc' mode
            $_to = $this->filter_email_out($to, $this->site_id);
            if (count($_to) > 0) {
                /* send the email */
                $response = $this->utility->email($from, $_to, $subject, $message);
                $this->email_model->log(EMAIL_TYPE_USER, $this->client_id, $this->site_id, $response, $from, $_to,
                    $subject, $message);
                /* check response from Amazon SES API */
                if ($response != false) {
                    $this->response($this->resp->setRespond($response), 200);
                } else {
                    $this->response($this->error->setError('CANNOT_SEND_EMAIL', implode(',', $_to)), 200);
                }
            } else {
                /* no email to send, return error */
                $this->response($this->error->setError('ALL_EMAILS_IN_BLACKLIST', implode(',', $to)), 200);
            }
        } else { // 'bcc' mode
            $_bcc = $this->filter_email_out($bcc, $this->site_id);
            if (count($_bcc) > 0) {
                /* send the email */
                $response = $this->utility->email_bcc($from, $_bcc, $subject, $message);
                $this->email_model->log(EMAIL_TYPE_USER, $this->client_id, $this->site_id, $response, $from, null,
                    $subject, $message, null, array(), null, $_bcc);
                /* check response from Amazon SES API */
                if ($response != false) {
                    $this->response($this->resp->setRespond($response), 200);
                } else {
                    $this->response($this->error->setError('CANNOT_SEND_EMAIL', implode(',', $_bcc)), 200);
                }
            } else {
                /* no email to send, return error */
                $this->response($this->error->setError('ALL_EMAILS_IN_BLACKLIST', $bcc), 200);
            }
        }
    }

    private function filter_email_out($emails)
    {
        $res = $this->email_model->isEmailInBlackList($emails, $this->site_id);
        $_email = array();
        foreach ($res as $i => $banned) {
            if ($banned) {
                continue;
            }
            $_email[] = $emails[$i];
        }
        return $_email;
    }

    /* return TRUE if email is banned, FALSE otherwise */
    public function isBlackList_post()
    {
        /* process parameters */
        $required = $this->input->checkParam(array('email'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $email = $this->input->post('email');
        /* main */
        $banned = $this->email_model->isEmailInBlackList($email, $this->site_id);
        $this->response($this->resp->setRespond($banned), 200);
    }

    public function addBlackList_post()
    {
        /* process parameters */
        $required = $this->input->checkParam(array('email'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $email = $this->input->post('email');
        /* main */
        if ($this->email_model->isEmailInBlackList($email, $this->site_id)) {
            $this->response($this->error->setError('EMAIL_ALREADY_IN_BLACKLIST', $email), 200);
        }
        $this->email_model->addIntoBlackList($this->site_id, $email, 'API');
        $this->response($this->resp->setRespond('Add email into blacklist successfully'), 200);
    }

    public function removeBlackList_post()
    {
        /* process parameters */
        $required = $this->input->checkParam(array('email'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $email = $this->input->post('email');
        /* main */
        if (!$this->email_model->isEmailInBlackList($email, $this->site_id)) {
            $this->response($this->error->setError('EMAIL_NOT_IN_BLACKLIST', $email), 200);
        }
        $this->email_model->removeFromBlackList($this->site_id, $email);
        $this->response($this->resp->setRespond('Email has been successfully removed from blacklist'), 200);
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
        $results = $this->email_model->recent($validToken['site_id'], $player['email'],
            $since ? strtotime($since) : null);
        array_walk_recursive($results, array($this, 'convert_mongo_date'));
        $this->response($this->resp->setRespond($results), 200);
    }

    public function template_get($template_id = '')
    {
        $result = array();
        if ($template_id) {
            $template = $this->email_model->getTemplateByTemplateId($this->site_id, $template_id);
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
            $result = $this->email_model->listTemplates($this->site_id, array('name'), array('_id'));
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
