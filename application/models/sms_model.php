<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sms_model extends MY_Model
{
    public function getSMSClient($client_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id',  new MongoID($client_id));
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }

    public function getSMSMaster(){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id',  null);
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }

    public function updateSMS(){

    }
}
?>