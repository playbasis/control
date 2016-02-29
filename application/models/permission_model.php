<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends MY_Model
{
    public function getPermissionBySiteId($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));

        $this->mongo_db->limit(1);

        $result = $this->mongo_db->get("playbasis_permission");

        return $result ? $result[0]['plan_id'] : null;
    }
}

?>