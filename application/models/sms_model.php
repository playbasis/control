<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function log($client_id, $site_id, $type, $from, $to, $message, $response)
    {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        return $this->mongo_db->insert('playbasis_sms_log', array(
            'type' => $type,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'from' => $from,
            'to' => $to,
            'message' => $message,
            'response' => $response,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate,
        ));
    }

    public function getSMSClient($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('client_id', $client_id);
        $results = $this->mongo_db->get("playbasis_sms");

        return $results ? $results[0] : null;
    }

    public function getTemplateById($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', new MongoId($template_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get('playbasis_sms_to_client');
        return $results ? $results[0] : null;
    }

    public function getTemplateByTemplateId($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('name', $template_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_sms_to_client');
        return $results ? $results[0] : null;
    }

    public function listTemplates($site_id, $includes = null, $excludes = null)
    {
        $this->set_site_mongodb($site_id);
        if ($includes) {
            $this->mongo_db->select($includes);
        }
        if ($excludes) {
            $this->mongo_db->select(null, $excludes);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get('playbasis_sms_to_client');
    }

    public function recent($site_id, $phone_number, $since)
    {
        if (!$phone_number) {
            return array();
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('message', 'date_added'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('to', $phone_number);
        if ($since) {
            $this->mongo_db->where_gt('date_added', new MongoDate($since));
        }
        $this->mongo_db->order_by(array('date_added' => -1));
        return $this->mongo_db->get('playbasis_sms_log');
    }
}

?>