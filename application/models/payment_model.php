<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}

	// status = Pending, Completed
	public function add_credit_event($client_id, $credit, $channel, $status) {
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    // insert into payment log
	    $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
	    $this->mongo_db->insert('playbasis_payment_log', array(
	        'client_id' => $client_id,
	        'credit' => $credit,
	        'channel' => $channel,
	        'status' => $status,
	        'date_added' => $d,
	        'date_modified' => $d,
	    ));

	    // adjust actual credit if the transaction is finalized
	    if ($status == 'completed') {
		    // get current credit
		    $this->mongo_db->where(array(
			    '_id' => $client_id,
		    ));
		    $res = $this->mongo_db->get('playbasis_client');
		    if ($res) {
			    $old = ($res && array_key_exists('credit', $res) ? $res['credit'] : 0);
			    // set new credit
			    $this->mongo_db->where(array(
				    '_id' => $client_id,
			    ));
			    $this->mongo_db->set('credit', $old + $credit);
			    $this->mongo_db->update('playbasis_client');
		    }
	    }
	}
}
?>
