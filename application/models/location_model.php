<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends MY_Model
{

    public function retrieveLocationByID($client_id, $site_id, $location_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($location_id));
        $this->mongo_db->where('deleted', false);

        $result = $this->mongo_db->get('playbasis_location_to_client');

        return $result ? $result[0] : null;
    }

    public function getLocationList($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        //$this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);

        $results = $this->mongo_db->get("playbasis_location_to_client");

        return $results;
    }

    public function retrieveLocation($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($data['client_id']));
        $this->mongo_db->where('site_id', new MongoId($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

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


        $result = $this->mongo_db->get('playbasis_location_to_client');

        return $result;
    }

    public function getTotalLocation($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        return $this->mongo_db->count("playbasis_location_to_client");
    }

    public function insertLocation($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'name' =>$data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'object_type' => $data['object_type'],
            'object_id' => new MongoId($data['object_id']),
            'status' => (bool)$data['status'],
            'tags' => isset($tags) ? $tags : null,
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_location_to_client', $insert_data);

        return $insert;
    }

    public function updateLocation($client_id, $site_id, $location_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $this->mongo_db->where('_id', new MongoID($location_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('latitude', $data['latitude']);
        $this->mongo_db->set('longitude', $data['longitude']);
        $this->mongo_db->set('object_type', $data['object_type']);
        $this->mongo_db->set('object_id', new MongoID($data['object_id']));
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('date_modified', new MongoDate());

        $result = $this->mongo_db->update('playbasis_location_to_client');

        return $result;
    }

    public function deleteLocation($client_id, $site_id, $location_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($location_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);

        $result = $this->mongo_db->update('playbasis_location_to_client');

        return $result;
    }

}

?>