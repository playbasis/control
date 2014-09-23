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

    public function updateSMS($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if(isset($data['client_id'])){
             if($this->getSMSClient($data['client_id'])){
                $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
                $this->mongo_db->set('mode', $data['sms-mode']);
                $this->mongo_db->set('account_sid', $data['sms-account_sid']);
                $this->mongo_db->set('auth_token', $data['sms-auth_token']);
                $this->mongo_db->set('number', $data['sms-number']);
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_sms');
            }else{
                 $this->mongo_db->insert('playbasis_sms', array(
                     'client_id' => new MongoID($data['client_id']),
                     'mode' => $data['sms-mode'],
                     'account_sid' => $data['sms-account_sid'],
                     'auth_token' => $data['sms-auth_token'] ,
                     'number' => $data['sms-number'],
                     'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                     'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                 ));
            }
        }else{
            if($this->getSMSMaster()){
                $this->mongo_db->where('client_id',  null);
                $this->mongo_db->set('mode', $data['sms-mode']);
                $this->mongo_db->set('account_sid', $data['sms-account_sid']);
                $this->mongo_db->set('auth_token', $data['sms-auth_token']);
                $this->mongo_db->set('number', $data['sms-number']);
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_sms');
            }else{
                $this->mongo_db->insert('playbasis_sms', array(
                    'client_id' => null,
                    'mode' => $data['sms-mode'],
                    'account_sid' => $data['sms-account_sid'],
                    'auth_token' => $data['sms-auth_token'] ,
                    'number' => $data['sms-number'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                ));
            }
        }
    }
}
?>