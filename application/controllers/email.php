<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
	}

	public function send_post()
	{
		/* process parameters */
		$required = $this->input->checkParam(array('from', 'to', 'subject', 'message'));
		if ($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$from = $this->input->post('from');
		$to = explode(',', $this->input->post('to'));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		/* check to see if emails are not black list */
		$res = $this->email_model->isEmailInBlackList($this->site_id, $to);
		$_to = array();
		foreach ($res as $i => $banned) {
			if ($banned) continue;
			$_to[] = $to[$i];
		}
		if (count($_to) > 0) {
			/* send the email */
			$response = $this->utility->email($from, $_to, $subject, $message);
			$this->email_model->log($this->client_id, $this->site_id, $response, $from, $to, $subject, $message);
			/* check response from Amazon SES API */
			if ($response != false) {
				$this->response($this->resp->setRespond($response), 200);
			} else {
				$this->response($this->error->setError('CANNOT_SEND_EMAIL', implode(',', $_to)), 200);
			}
		} else {
			/* no email to send, return error */
			$this->response($this->error->setError('ALL_EMAILS_IN_BLACKLIST', $this->input->post('to')), 200);
		}
	}

	/* return TRUE if email is banned, FALSE otherwise */
	public function isBlackList_get($email)
	{
		$banned = $this->email_model->isEmailInBlackList($this->site_id, $email);
		$this->response($this->resp->setRespond($banned), 200);
	}

	public function addBlackList_post($email)
	{
		if ($this->email_model->isEmailInBlackList($this->site_id, $email)) {
			$this->response($this->error->setError('EMAIL_ALREADY_IN_BLACKLIST', $email), 200);
		}
		$this->email_model->addIntoBlackList($this->site_id, $email, 'API');
		$this->response($this->resp->setRespond('Add email into blacklist successfully'), 200);
	}

	public function removeBlackList_post($email)
	{
		if (!$this->email_model->isEmailInBlackList($this->site_id, $email)) {
			$this->response($this->error->setError('EMAIL_NOT_IN_BLACKLIST', $email), 200);
		}
		$this->email_model->removeFromBlackList($this->site_id, $email);
		$this->response($this->resp->setRespond('Email has been successfully removed from blacklist'), 200);
	}
}