<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class import_model extends MY_Model
{

    public function addImportData($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date = new MongoDate();
        $date_array = array(
            'md5_id'        => null,
            'date_added'    => $date,
            'date_modified' => $date
        );
        $insert_data = array_merge($data, $date_array);

        $insert = $this->mongo_db->insert('playbasis_import', $insert_data);

        return $insert;
    }

    public function updateCompleteImport($client_id, $site_id, $name, $importResult, $importKey, $import_type)
    {
        $mongoDate = new MongoDate(time());
        $result = array('import_name' => $name, 'import_key' => $importKey);
        $result += array_merge($importResult, array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'import_type' => $import_type,
            'import_id' => null,
            'date_added' => $mongoDate
        ));
        return $this->mongo_db->insert('playbasis_import_log', $result);
    }

    public function retrieveImportDataByRoutine($client_id, $site_id, $routine)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id'));

        $this->mongo_db->where('routine', $routine);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->order_by(array('date_added' => 'desc'));

        return $this->mongo_db->get("playbasis_import");
    }

    public function retrieveImportDataByName($client_id, $site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id'));

        $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($name)) . "/i");
        $this->mongo_db->where('name', $regex);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->order_by(array('date_added' => 'desc'));

        return $this->mongo_db->get("playbasis_import");
    }

    public function retrieveImportData($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_import_type']) && !is_null($data['filter_import_type'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_import_type'])) . "/i");
            $this->mongo_db->where('import_type', $regex);
        }

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);

        $this->mongo_db->order_by(array('date_added' => 'desc'));

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        return $this->mongo_db->get("playbasis_import");
    }

    public function countImportData($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_import_type']) && !is_null($data['filter_import_type'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_import_type'])) . "/i");
            $this->mongo_db->where('import_type', $regex);
        }

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);

        return $this->mongo_db->count('playbasis_import');
    }

    public function retrieveImportResults($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_import_method']) && !empty($data['filter_import_method'])) {
            if(utf8_strtolower($data['filter_import_method'])=="adhoc"){
                if (isset($data['import_name']) && $data['import_name']) {
                    $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['import_name'])) . "/i");
                    $this->mongo_db->where('import_name', $regex);
                }else {
                    $this->mongo_db->where_in('import_id', array(null, ""));
                }
            }elseif(utf8_strtolower($data['filter_import_method'])=="cron"){
                if (isset($data['import_id']) && $data['import_id']) {
                    $or_where = array();
                    foreach($data['import_id'] as $import_id){
                        array_push($or_where,array('import_id' => $import_id));
                    }
                    $this->mongo_db->where(array('$or' => $or_where));
                }else{
                    $this->mongo_db->where_not_in('import_id', array(null,""));
                }
            }
        }else{
            $or_where = array();
            if (isset($data['import_id']) && $data['import_id']) {
                foreach ($data['import_id'] as $import_id) {
                    array_push($or_where, array('import_id' => $import_id));
                }
            }

            if (isset($data['import_name']) && $data['import_name']) {
                $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['import_name'])) . "/i");
                array_push($or_where, array('import_name' => $regex));

            }
            if($or_where){
                $this->mongo_db->where(array('$or' => $or_where));
            }
        }

        if (isset($data['filter_import_type']) && $data['filter_import_type']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_import_type'])) . "/i");
            $this->mongo_db->where('import_type', $regex);
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_end']) && $data['date_end'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_end']))
            ));
        }

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);

        $this->mongo_db->order_by(array('date_added' => 'desc'));

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        return $this->mongo_db->get("playbasis_import_log");
    }

    public function countImportResults($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_import_method']) && !empty($data['filter_import_method'])) {
            if(utf8_strtolower($data['filter_import_method'])=="adhoc"){
                if (isset($data['import_name']) && $data['import_name']) {
                    $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['import_name'])) . "/i");
                    $this->mongo_db->where('import_name', $regex);
                }else {
                    $this->mongo_db->where_in('import_id', array(null, ""));
                }
            }elseif(utf8_strtolower($data['filter_import_method'])=="cron"){
                if (isset($data['import_id']) && $data['import_id']) {
                    $or_where = array();
                    foreach($data['import_id'] as $import_id){
                        array_push($or_where,array('import_id' => $import_id));
                    }
                    $this->mongo_db->where(array('$or' => $or_where));
                }else{
                    $this->mongo_db->where_not_in('import_id', array(null,""));
                }
            }
        }else{
            $or_where = array();
            if (isset($data['import_id']) && $data['import_id']) {
                foreach ($data['import_id'] as $import_id) {
                    array_push($or_where, array('import_id' => $import_id));
                }
            }

            if (isset($data['import_name']) && $data['import_name']) {
                $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['import_name'])) . "/i");
                array_push($or_where, array('import_name' => $regex));

            }
            if($or_where){
                $this->mongo_db->where(array('$or' => $or_where));
            }
        }

        if (isset($data['filter_import_type']) && $data['filter_import_type']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_import_type'])) . "/i");
            $this->mongo_db->where('import_type', $regex);
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_end']) && $data['date_end'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_end']))
            ));
        }

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);

        return $this->mongo_db->count("playbasis_import_log");
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

    public function deleteImportData($import_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($import_id));

        return $this->mongo_db->delete('playbasis_import');
    }

    public function updateImportData($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($data['_id']));

        $date = new MongoDate();
        $date_array = array(
            'date_modified' => $date
        );

        $update_data = array_merge($data, $date_array);

        unset($update_data['_id']);
        foreach ($update_data as $key => $value) {
            $this->mongo_db->set($key, $value);
        }
        $update = $this->mongo_db->update('playbasis_import');

        return $update;
    }
}