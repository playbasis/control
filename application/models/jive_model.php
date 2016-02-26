<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jive_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function insert($site_id, $jive)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_jive_to_client', array(
            'site_id' => $site_id,
            'jive_tenant_id' => $jive['tenantId'],
            'jive_client_id' => $jive['clientId'],
            'jive_client_secret' => $jive['clientSecret'],
            'jive_url' => $jive['jiveUrl'],
            'date_added' => $d,
            'date_modified' => $d
        ));
    }

    public function delete($site_id)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(time());
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', $d);
        return $this->mongo_db->update_all('playbasis_jive_to_client');
    }

    public function findByTenantId($tenant_id)
    {
        $this->mongo_db->where('jive_tenant_id', $tenant_id);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_jive_to_client');
        return $result ? $result[0] : array();
    }
}

?>