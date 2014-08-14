<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends MY_Model
{
	// status = init, pending, completed
    public function add_credit_event($credit, $channel, $status) {
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    $client_id = $this->session->userdata('client_id');

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
		    $this->mongo_db->where(array(
			    '_id' => $client_id,
		    ));
		    $this->mongo_db->inc('credit', $credit);
		    $this->mongo_db->update('playbasis_client');
	    }
    }
}
?>
