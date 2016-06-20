<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Link_model extends MY_Model
{
    public function getConfig($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $results = $this->mongo_db->get('playbasis_link_to_client_setting');
        return $results ? $results[0] : null;
    }

    public function setConfig($client_id, $site_id, $type, $key)
    {
        $d = new MongoDate();
        $this->set_site_mongodb($site_id);
        return $this->mongo_db->insert('playbasis_link_to_client_setting', array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'type' => $type,
            'key' => $key,
            'date_added' => $d,
            'date_modified' => $d
        ));
    }

    public function updateConfig($client_id, $site_id, $type, $key)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        $this->mongo_db->set('type', $type);
        $this->mongo_db->set('key', $key);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        return $this->mongo_db->update('playbasis_link_to_client_setting');
    }
}

?>