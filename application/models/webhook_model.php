<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getTemplateById($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', new MongoId($template_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get('playbasis_webhook_to_client');
        return $results ? $results[0] : null;
    }

    public function log($client_id, $site_id, $data = array())
    {
        $this->set_site_mongodb($site_id);
        $data = array_merge($data, array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate(),
        ));
        return $this->mongo_db->insert('playbasis_webhook_log', $data);
    }
}
