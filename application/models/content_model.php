<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveContent($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($site_id);

        // Searching
        if (isset($optionalParams['title']) && !is_null($optionalParams['title'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($optionalParams['title'])) . "/i");
            $this->mongo_db->where('title', $regex);
        }
        if (isset($optionalParams['category']) && !is_null($optionalParams['category'])) {
            $category_result = $this->retrieveContentCategory($client_id, $site_id,
                array('name' => $optionalParams['category']));
            $this->mongo_db->where('category', $category_result[0]['_id']);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
                return null;
            }
        }

        // Sorting
        $sort_data = array('_id', 'title', 'date_start', 'date_end', 'date_added', 'date_modified');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('title' => $order));
        }

        // Paging
        if (isset($optionalParams['offset']) || isset($optionalParams['limit'])) {
            if (isset($optionalParams['offset'])) {
                if ($optionalParams['offset'] < 0) {
                    $optionalParams['offset'] = 0;
                }
            } else {
                $optionalParams['offset'] = 0;
            }

            if (isset($optionalParams['limit'])) {
                if ($optionalParams['limit'] < 1) {
                    $optionalParams['limit'] = 20;
                }
            } else {
                $optionalParams['limit'] = 20;
            }

            $this->mongo_db->limit((int)$optionalParams['limit']);
            $this->mongo_db->offset((int)$optionalParams['offset']);
        }

        if (isset($optionalParams['date_check']) && !is_null($optionalParams['date_check'])) {
            $bool = filter_var($optionalParams['date_check'], FILTER_VALIDATE_BOOLEAN);
            if ($bool == true) {
                $this->mongo_db->where_gte('date_end', new MongoDate());
                $this->mongo_db->where_lt('date_start', new MongoDate());
            }
        }

        $this->mongo_db->select(array('title', 'summary', 'detail', 'image', 'category', 'date_start', 'date_end'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'status' => true,
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_content_to_client');

        return !empty($result) ? $result : array();
    }

    public function retrieveContentCategory($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['name']) && !is_null($optionalParams['name'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($optionalParams['name'])) . "/i");
            $this->mongo_db->where('name', $regex);
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
        $sort_data = array('_id', 'name', 'sort_order');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
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
        return $this->mongo_db->get("playbasis_content_category_to_client");
    }

    public function getContentCategoryNameById($client_id, $site_id, $categoryId)
    {
        $this->mongo_db->where('_id', $categoryId);
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->select(array(), array('_id'));
        $result = $this->mongo_db->get("playbasis_content_category_to_client");
        return $result ? $result[0]['name'] : null;
    }
}