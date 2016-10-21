<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_style_model extends MY_Model
{
    public function createStyle(
        $client_id,
        $site_id,
        $name,
        $key,
        $value = null
    ) {
        $this->load->helper('url');

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'key' => $key,
            'value' => $value,
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        $insert = $this->mongo_db->insert('playbasis_custom_style_to_client', $insert_data);

        return $insert;
    }

    public function retrieveByNameAndKey($client_id, $site_id, $name, $key)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('key', $key);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_custom_style_to_client");

        return $results ? $results[0] : null;
    }

    public function retrieveStyleById($client_id, $site_id, $id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_custom_style_to_client");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function retrieveStyle($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");

            $query = array( '$or' => array( array( "name" => $regex )));
            $this->mongo_db->where($query);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
            };
        }

        // Sorting
        $sort_data = array('_id', 'name', 'key', 'value');

        if (isset($optionalParams['order']) && (utf8_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        // Paging
        if (isset($optionalParams['offset']) || isset($optionalParams['limit'])) {
            if ($optionalParams['offset'] < 0) {
                $optionalParams['offset'] = 0;
            }

            if ($optionalParams['limit'] < 1) {
                $optionalParams['limit'] = 20;
            }

            $this->mongo_db->limit((int)$optionalParams['limit']);
            $this->mongo_db->offset((int)$optionalParams['offset']);
        }

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_custom_style_to_client");
    }
    public function countStyles($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_custom_style_to_client');

        return $total;
    }

    public function updateStyleById($style_id, $updateData)
    {
        $this->mongo_db->where('_id', new MongoID($style_id));
        $this->mongo_db->where('client_id', $updateData['client_id']);
        $this->mongo_db->where('site_id', $updateData['site_id']);

        $this->mongo_db->set('name', $updateData['name']);
        $this->mongo_db->set('key', $updateData['key']);
        $this->mongo_db->set('value', $updateData['value']);

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_custom_style_to_client');

        return $update;
    }

    public function deleteStyleById($style_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($style_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_custom_style_to_client');
    }

    public function retrieveStyleByNameAndKeyButNotID($client_id, $site_id, $name, $key, $node_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', $node_id);
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('key', $key);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_custom_style_to_client");

        return $results ? $results[0] : null;
    }

    public function deleteStyleByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_custom_style_to_client');

        return $update;
    }

}