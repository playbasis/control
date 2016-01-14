<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_Model
{
    public function countContents($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_content_to_client');

        return $total;
    }

    public function retrieveContents($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($optionalParams['title']) && !is_null($optionalParams['title'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['title'])) . "/i");
            $this->mongo_db->where('title', $regex);
        }

        if (isset($optionalParams['status']) && !is_null($optionalParams['status'])) {
            $bool = filter_var($optionalParams['status'], FILTER_VALIDATE_BOOLEAN);
            $this->mongo_db->where('status', $bool);
        }

        $sort_data = array(
            '_id',
            'title',
            'status',
            'sort_order'
        );

        if (isset($optionalParams['order']) && (utf8_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('title' => $order));
        }

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
        return $this->mongo_db->get("playbasis_content_to_client");
    }

    public function retrieveContent($content_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('_id', new MongoId($content_id));
        } catch (Exception $e) {
            return null;
        }
        $c = $this->mongo_db->get('playbasis_content_to_client');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function createContent($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'title' => $data['title'],
            'summary' => $data['summary'],
            'detail' => $data['detail'],
            'date_start' => new MongoDate(strtotime($data['date_start'])),
            'date_end' => new MongoDate(strtotime($data['date_end'])),
            'image' => $data['image'],
            'status' => $data['status'],
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        if (isset($data['category'])) {
            $insert_data['category'] = new MongoId($data['category']);
        }
        $insert = $this->mongo_db->insert('playbasis_content_to_client', $insert_data);

        return $insert;
    }

    public function updateContent($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($data['_id']));

        $this->mongo_db->set('title', $data['title']);
        $this->mongo_db->set('summary', $data['summary']);
        $this->mongo_db->set('detail', $data['detail']);
        if (isset($data['category'])) {
            if (empty($data['category'])) {
                $this->mongo_db->unset_field('category');
            } else {
                $this->mongo_db->set('category', new MongoId($data['category']));
            }
        }
        $this->mongo_db->set('image', $data['image']);
        $this->mongo_db->set('date_start', new MongoDate(strtotime($data['date_start'])));
        $this->mongo_db->set('date_end', new MongoDate(strtotime($data['date_end'])));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('status', $data['status']);

        $update = $this->mongo_db->update('playbasis_content_to_client');

        return $update;
    }

    public function deleteContent($content_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($content_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_content_to_client');
    }

    public function countContentCategory($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_content_category_to_client');

        return $total;
    }

    public function createContentCategory($client_id, $site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_content_category_to_client', $insert_data);

        return $insert;
    }

    public function retrieveContentCategory($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");
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
        return $this->mongo_db->get("playbasis_content_category_to_client");
    }

    public function retrieveContentCategoryById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_content_category_to_client");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function updateContentCategory($category_id, $data)
    {
        try {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('_id', new MongoID($category_id));
        } catch (Exception $e) {
            return false;
        };

        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_content_category_to_client');

        return $update;
    }

    public function deleteContentCategory($category_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('_id', new MongoID($category_id));
        } catch (Exception $e) {
            return false;
        };

        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_content_category_to_client');
    }

    public function deleteContentCategoryByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_content_category_to_client');

        return $update;
    }

    function makeMongoIdObj(&$value)
    {
        $value = new MongoId($value);
    }
}