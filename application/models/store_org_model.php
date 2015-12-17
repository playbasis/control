<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_org_model extends MY_Model
{
    public function createNode(
        $client_id,
        $site_id,
        $name,
        $description = null,
        $organize = null,
        $parent = null,
        $status = true
    )
    {
        $this->load->helper('url');

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'slug' => url_title($name, 'dash', true),
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        if (isset($organize)) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($organize));
            if (isset($parent_result)) {
                $parent_data = array(
                    '_id' => $parent_result['_id'],
                    'name' => $parent_result['name']
                );
                $insert_data['organize'] = $parent_data;
            }
        }

        if (isset($parent)) {
            $parent_result = $this->retrieveNodeById(new MongoId($parent));
            if (isset($parent_result)) {
                $parent_data = array(
                    '_id' => $parent_result['_id'],
                    'name' => $parent_result['name']
                );
                $insert_data['parent'] = $parent_data;
            }
        }

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_client', $insert_data);

        return $insert;
    }

    public function retrieveNode($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['id'])) {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            }
        }
        if (isset($optionalParams['organize']) && !is_null($optionalParams['organize'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['organize'])) {
                $organize = new MongoId($optionalParams['organize']);
                $this->mongo_db->where('organize._id', $organize);
            }
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

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
        return $this->mongo_db->get("playbasis_store_organize_to_client");
    }

    public function retrieveNodeById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize_to_client");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function updateNodeById($nodeId, $updateData)
    {
        $parent_data = null;
        if (isset($updateData['parent'])) {
            $parent_result = $this->retrieveNodeById(new MongoId($updateData['parent']));
            if (isset($parent_result)) {
                $parent_data = array(
                    '_id' => $parent_result['_id'],
                    'name' => $parent_result['name']
                );
            }
        }

        $organize_data = null;
        if (isset($updateData['organize'])) {
            $organize_result = $this->retrieveOrganizeById(new MongoId($updateData['organize']));
            if (isset($organize_result)) {
                $organize_data = array(
                    '_id' => $organize_result['_id'],
                    'name' => $organize_result['name']
                );
            }
        }

        if (isset($parent_data)) {
            $this->mongo_db->set('parent', $parent_data);
        } else {
            $this->mongo_db->unset_field('parent');
        }

        if (isset($organize_data)) {
            $this->mongo_db->set('organize', $organize_data);
        } else {
            $this->mongo_db->unset_field('organize');
        }

        $this->mongo_db->where('_id', new MongoID($nodeId));
        $this->mongo_db->where('client_id', $updateData['client_id']);
        $this->mongo_db->where('site_id', $updateData['site_id']);

        $this->mongo_db->set('name', $updateData['name']);
        $this->mongo_db->set('description', $updateData['description']);

        $this->mongo_db->set('status', $updateData['status']);
        $this->mongo_db->set('slug', url_title($updateData['name'], 'dash', true));
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_store_organize_to_client');

        return $update;
    }

    public function deleteNodeById($nodeId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($nodeId));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_store_organize_to_client');
    }

    public function deleteNodeByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_store_organize_to_client');

        return $update;
    }

    public function createOrganize($client_id, $site_id, $name, $description = null, $parent = null, $status = true)
    {
        $this->load->helper('url');

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'slug' => url_title($name, 'dash', true),
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        if (isset($parent)) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($parent));
            if (isset($parent_result)) {
                $parent_data = array(
                    '_id' => $parent_result['_id'],
                    'name' => $parent_result['name']
                );
                $insert_data['parent'] = $parent_data;
            }
        }

        $insert = $this->mongo_db->insert('playbasis_store_organize', $insert_data);

        return $insert;
    }

    public function retrieveOrganize($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['id'])) {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            }
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

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
        return $this->mongo_db->get("playbasis_store_organize");
    }

    public function retrieveOrganizeById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function updateOrganizeById($organizeId, $updateData)
    {
        $parent_data = null;
        if (isset($updateData['parent'])) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($updateData['parent']));
            if (isset($parent_result)) {
                $parent_data = array(
                    '_id' => $parent_result['_id'],
                    'name' => $parent_result['name']
                );
                $this->mongo_db->set('parent', $parent_data);
            }
        } else {
            $this->mongo_db->unset_field('parent');
        }

        $this->mongo_db->where('_id', new MongoID($organizeId));
        $this->mongo_db->where('client_id', $updateData['client_id']);
        $this->mongo_db->where('site_id', $updateData['site_id']);

        $this->mongo_db->set('name', $updateData['name']);
        $this->mongo_db->set('description', $updateData['description']);

        $this->mongo_db->set('status', $updateData['status']);
        $this->mongo_db->set('slug', url_title($updateData['name'], 'dash', true));
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_store_organize');

        return $update;
    }

    public function deleteOrganizeById($organizeId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($organizeId));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_store_organize');
    }

    public function deleteOrganizeByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, 'makeMongoIdObj'));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_store_organize');

        return $update;
    }

    function makeMongoIdObj(&$value)
    {
        $value = new MongoId($value);
    }
}