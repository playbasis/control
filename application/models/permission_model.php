<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Permission_model extends MY_Model
{
    public function getPermissionBySiteId($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));

        $this->mongo_db->limit(1);

        $result = $this->mongo_db->get("playbasis_permission");

        return $result ? $result[0]['plan_id'] : null;
    }

    public function addPlanToPermission($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if($data['site_id']){
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->delete('playbasis_permission');
        }

        $data_insert = array(
            'plan_id' =>  new MongoID($data['plan_id']),
            'client_id' =>  new MongoID($data['client_id']),
            'site_id' =>  null,
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
        );
        if($data['site_id']){
            $data_insert['site_id'] = new MongoID($data['site_id']);
        }

        return $this->mongo_db->insert('playbasis_permission', $data_insert);
    }
}
?>