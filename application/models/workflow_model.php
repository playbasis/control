<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workflow_model extends MY_Model
{
    public function test($goods_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods");

        return $results ? $results[0] : null;
    }

    public function getPlayerByApprovalStatus($client_id, $site_id, $approval_status) {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where(array(
            'approved' => $approval_status,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));

        return $this->mongo_db->get('playbasis_player');
    }

    public function approvePlayer($user_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approved', "approved");
        $this->mongo_db->set('date_approved', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        return $this->mongo_db->update('playbasis_player');
    }

    public function rejectPlayer($user_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approved', "rejected");
        $this->mongo_db->set('date_approved', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        return $this->mongo_db->update('playbasis_player');
    }
}