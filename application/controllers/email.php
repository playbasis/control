<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

define('EMAIL_FROM', 'info@playbasis.com');

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
	}

	public function send_post()
	{
        /* check permission to send email in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_id,
                $this->site_id,
                "notifications",
                "email"
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $this->response($this->error->setError(
                    "LIMIT_EXCEED", array()), 200);
            else
                $this->response($this->error->setError(
                    "INTERNAL_ERROR", array()), 200);
        }

		/* process parameters */
		$required = $this->input->checkParam(array('from', 'subject'));
		if ($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$required_to = $this->input->checkParam(array('to'));
		$required_bcc = $this->input->checkParam(array('bcc'));
		if ($required && $required_bcc)
			$this->response($this->error->setError('PARAMETER_MISSING', $required_to), 200);

		/* variables */
		$from = $this->input->post('from');
		$to = $this->input->post('to') ? explode(',', $this->input->post('to')) : array();
		$bcc = $this->input->post('bcc') ? explode(',', $this->input->post('bcc')) : array();
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		if ($message == false) $message = ''; // $message is optional

		$this->processEmail($from, $to, $bcc, $subject, $message);
	}

    public function send_goods_post()
    {
        /* check permission to send email in this bill cycle */
        try {
            $this->client_model->permissionProcess(
                $this->client_id,
                $this->site_id,
                "notifications",
                "email"
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $this->response($this->error->setError(
                    "LIMIT_EXCEED", array()), 200);
            else
                $this->response($this->error->setError(
                    "INTERNAL_ERROR", array()), 200);
        }

        /* process parameters */
        $required = $this->input->checkParam(array(
            'player_id',
            'ref_id',
            'subject'
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
        $ref_id = $this->input->post('ref_id');
        $redeemData = $this->redeem_model->findByReferenceId('goods', new MongoId($ref_id));

        /* variables */
        $email = $player['email'];
        $from = EMAIL_FROM;
        $to = $email;
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        if ($message == false) $message = ''; // $message is optional
        $message = str_replace('{{code}}', $redeemData['code'], $message);

        $this->processEmail($from, $to, null, $subject, $message);
    }

    private function processEmail($from, $to, $bcc, $subject, $message) {
        /* check to see if emails are not black list */
        $_to = $this->filter_email_out($to, $this->site_id);
        $_bcc = $this->filter_email_out($bcc, $this->site_id);
        if (!empty($to)) { // 'to-cc' mode
            if (count($_to) > 0) {
                /* send the email */
                $response = $this->utility->email($from, $_to, $subject, $message);
                $this->email_model->log(EMAIL_TYPE_USER, $this->client_id, $this->site_id, $response, $from, $_to, $subject, $message);
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
            if (count($_bcc) > 0) {
                /* send the email */
                $response = $this->utility->email_bcc($from, $_bcc, $subject, $message);
                $this->email_model->log(EMAIL_TYPE_USER, $this->client_id, $this->site_id, $response, $from, null, $subject, $message, null, array(), null, $_bcc);
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

	private function filter_email_out($emails) {
		$res = $this->email_model->isEmailInBlackList($emails, $this->site_id);
		$_email = array();
		foreach ($res as $i => $banned) {
			if ($banned) continue;
			$_email[] = $emails[$i];
		}
		return $_email;
	}

	/* return TRUE if email is banned, FALSE otherwise */
	public function isBlackList_post()
	{
		/* process parameters */
		$required = $this->input->checkParam(array('email'));
		if ($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$email = $this->input->post('email');
		/* main */
		$banned = $this->email_model->isEmailInBlackList($email, $this->site_id);
		$this->response($this->resp->setRespond($banned), 200);
	}

	public function addBlackList_post()
	{
		/* process parameters */
		$required = $this->input->checkParam(array('email'));
		if ($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
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
		if ($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$email = $this->input->post('email');
		/* main */
		if (!$this->email_model->isEmailInBlackList($email, $this->site_id)) {
			$this->response($this->error->setError('EMAIL_NOT_IN_BLACKLIST', $email), 200);
		}
		$this->email_model->removeFromBlackList($this->site_id, $email);
		$this->response($this->resp->setRespond('Email has been successfully removed from blacklist'), 200);
	}
}
