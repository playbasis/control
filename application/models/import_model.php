<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class import_model extends MY_Model
{

    public function addImportData($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date = new MongoDate();
        $date_array = array(
            'date_added' => $date,
            'date_modified' => $date
        );
        $insert_data = array_merge($data, $date_array);

        $insert = $this->mongo_db->insert('playbasis_import', $insert_data);

        return $insert;
    }

    public function retrieveImportData($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('import_type', $regex);
        }

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->order_by(array('date_added' => 'desc'));

        return $this->mongo_db->get("playbasis_import");
    }

    public function retrieveSingleImportData($import_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($import_id));
        $c = $this->mongo_db->get('playbasis_import');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function countImportData($client_id, $site_id)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $countImportData = $this->mongo_db->count('playbasis_import');

        return $countImportData;
    }

    public function deleteImportData($import_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($import_id));

        return $this->mongo_db->delete('playbasis_import');
    }
}