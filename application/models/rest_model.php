<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rest_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function logRequest($data)
    {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($data['site_id']);
        $data['date_added'] = $mongoDate;
        $data['date_modified'] = null;
        return $this->mongo_db->insert('playbasis_web_service_log', $data, array("w" => 0, "j" => false));
    }

    public function logResponse($id, $site_id, $data)
    {
        if (!$id) {
            return false;
        }
        $data['date_modified'] = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', $id);
        $this->mongo_db->set($data);
        return $this->mongo_db->update('playbasis_web_service_log', array("w" => 0, "j" => false));
    }
}

?>