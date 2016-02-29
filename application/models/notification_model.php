<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function list_messages($site_id)
    {
        $this->set_site_mongodb($site_id);
        return $this->mongo_db->get('playbasis_notification_log');
    }

    public function log($site_id, $data)
    {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $data['date_added'] = $mongoDate;
        $data['date_modified'] = $mongoDate;
        return $this->mongo_db->insert('playbasis_notification_log', $data);
    }
}

?>