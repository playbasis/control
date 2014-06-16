<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Email_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}

	public function listBlackListEmails($site_id)
	{
		$this->set_site_mongodb($site_id);
		$ret = $this->mongo_db->get('playbasis_email_blacklist');
		$emails = array();
		foreach ($ret as $each) {
			$emails[] = $each['_id'];
		}
		return $emails;
	}

	public function isEmailInBlackList($site_id, $emails)
	{
		$blackList = $this->listBlackListEmails($site_id);
		$banned = array();
		if (is_array($emails)) foreach ($emails as $email) {
			$banned[] = in_array($email, $blackList);
		} else {
			$banned = in_array($emails, $blackList);
		}
		return $banned;
	}

	public function addIntoBlackList($site_id, $email, $bounce_complaint, $type, $sub_type=null, $ref_id=null)
	{
		$mongoDate = new MongoDate(time());
		$this->set_site_mongodb($site_id);
		$data['_id'] = $email;
		$data['bounce_complaint'] = $bounce_complaint;
		$data['type'] = $type;
		$data['sub_type'] = $sub_type;
		$data['ref_id'] = $ref_id;
		$data['date_added'] = $mongoDate;
		$data['date_modified'] = $mongoDate;
		return $this->mongo_db->insert('playbasis_email_blacklist', $data);
	}

	public function removeFromBlackList($site_id, $email)
	{
		if (!$email)
			return false;
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where(array(
			'_id' => $email,
		));
		return $this->mongo_db->delete('playbasis_email_blacklist');
	}

	public function log($client_id, $site_id, $response, $from, $to, $subject, $message, $message_alt=null, $attachments=array())
	{
		$mongoDate = new MongoDate(time());
		$this->set_site_mongodb($site_id);
		$data = array(
			'client_id' => $client_id,
			'site_id' => $site_id,
			'from' => $from,
			'to' => $to,
			'subject' => $subject,
			'message' => $message,
			'response' => $response,
		);
		if ($message_alt != null) $data['message_alt'] = $message_alt;
		if (count($attachments) > 0) $data['attachments'] = implode(',', $attachments);
		$data['date_added'] = $mongoDate;
		$data['date_modified'] = $mongoDate;
		return $this->mongo_db->insert('playbasis_email_log', $data);
	}
}
?>