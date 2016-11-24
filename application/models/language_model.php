<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function retrieveLanguageByName($client_id, $site_id, $language)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('language', $language);
        $result = $this->mongo_db->get("playbasis_language_to_client");
        return $result ? $result[0] : null;
    }

}