<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_style_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveStyle($client_id, $site_id, $query_data)
    {
        $this->set_site_mongodb($site_id);

        // Searching
        if (isset($query_data['name']) && !empty($query_data['name'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($query_data['name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($query_data['key']) && !empty($query_data['key'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($query_data['key'])) . "/i");
            $this->mongo_db->where('key', $regex);
        }

        // Sorting
        $sort_data = array('_id', 'name', 'key', 'date_modified');

        if (isset($query_data['order']) && (mb_strtolower($query_data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($query_data['sort']) && in_array($query_data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($query_data['sort'] => $order));
        } else{
            $this->mongo_db->order_by(array('_id' => $order));
        }

        // Paging
        if ((isset($query_data['offset']) || isset($query_data['limit']))) {
            if (isset($query_data['offset']) && !empty($query_data['offset'])) {
                if ($query_data['offset'] < 0) {
                    $query_data['offset'] = 0;
                }
            } else {
                $query_data['offset'] = 0;
            }

            if (isset($query_data['limit']) && !empty($query_data['limit'])) {
                if ($query_data['limit'] < 1) {
                    $query_data['limit'] = 20;
                }
            } else {
                $query_data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$query_data['limit']);
            $this->mongo_db->offset((int)$query_data['offset']);
        }

        $this->mongo_db->select(array('name', 'key', 'value', 'date_added', 'date_added', 'date_modified'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false
        ));

        if (isset($query_data['status'])) $this->mongo_db->where('status', $query_data['status']);

        $result = $this->mongo_db->get('playbasis_custom_style_to_client');

        return !empty($result) ? $result : array();
    }

}
