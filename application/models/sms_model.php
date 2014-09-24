<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sms_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
		$this->load->library('mongo_db');
	}

	public function log($client_id, $site_id, $type, $from, $to, $message, $response)
	{
		$mongoDate = new MongoDate(time());
		$this->set_site_mongodb($site_id);
		return $this->mongo_db->insert('playbasis_sms_log', array(
			'type' => $type,
			'client_id' => $client_id,
			'site_id' => $site_id,
			'from' => $from,
			'to' => $to,
			'message' => $message,
			'response' => $response,
			'date_added' => $mongoDate,
			'date_modified' => $mongoDate,
		));
	}

    public function getSMSClient($client_id, $site_id){
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('client_id',  $client_id);
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }
}
?>