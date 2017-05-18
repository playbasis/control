<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badge_model extends MY_Model
{
    public function getBadge($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $results = $this->mongo_db->get("playbasis_badge");

        return $results ? $results[0] : null;
    }

    public function getBadgeIDByName($client_id, $site_id, $badge_name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('badge_id'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        //$this->mongo_db->where('status', true);
        $this->mongo_db->where('name', $badge_name);
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        return $results ? $results[0]['badge_id']."" : null;
    }

    public function getBadgeByName($client_id, $site_id, $badge_name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        //$this->mongo_db->where('status', true);
        $this->mongo_db->where('name', $badge_name);
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        return $results ? $results[0] : null;
    }

    public function getBadgeToClient($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $results = $this->mongo_db->get("playbasis_badge_to_client");
        if ((isset($results[0])) && (!isset($results[0]['tags']))){
            $results[0] = array_merge($results[0], array(
               'tags' => null
            ));
        }

        return $results ? $results[0] : null;
    }

    public function auditBeforeBadge($event,$badge_id, $user_id)
    {
        $badge_data = $this->getBadgeToClient($badge_id);
        $insert_data = array('client_id' => $badge_data['client_id'],
                             'site_id' => $badge_data['site_id'],
                             'badge_id' => $badge_data['badge_id'],
                             'event' => $event,
                             'before' => $badge_data,
                             'user_id' => $user_id);
        return $this->mongo_db->insert('playbasis_badge_to_client_audit', $insert_data);
    }

    public function auditAfterBadge($event, $badge_id, $user_id, $audit_id=null)
    {
        $badge_data = $this->getBadgeToClient($badge_id);
        $audit_log = array();
        if ($audit_id){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $audit_log = $this->mongo_db->get('playbasis_badge_to_client_audit');
        }

        if ($audit_log){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $this->mongo_db->set('after', $badge_data);
            $this->mongo_db->update('playbasis_badge_to_client_audit');
        } else {
            $insert_data = array('client_id' => $badge_data['client_id'],
                                 'site_id' => $badge_data['site_id'],
                                 'badge_id' => $badge_data['badge_id'],
                                 'event' => $event,
                                 'before' => null,
                                 'after' => $badge_data,
                                 'user_id' => $user_id);
            $this->mongo_db->insert('playbasis_badge_to_client_audit', $insert_data);
        }
    }

    public function countItemCategory($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_badge_category_to_client');

        return $total;
    }

    public function createItemCategory($client_id, $site_id, $name)
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
        $insert = $this->mongo_db->insert('playbasis_badge_category_to_client', $insert_data);

        return $insert;
    }

    public function retrieveItemCategory($client_id, $site_id, $optionalParams = array())
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
        return $this->mongo_db->get("playbasis_badge_category_to_client");
    }

    public function retrieveItemCategoryName($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_badge_category_to_client");
    }

    public function retrieveItemCategoryByName($client_id, $site_id, $name)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        $result = $this->mongo_db->get("playbasis_badge_category_to_client");
        return $result ? $result[0] : null;
    }


    public function retrieveItemCategoryByNameFilter($client_id, $site_id, $name)
    {
        $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($name)) . "/i");
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $regex);
        $result = $this->mongo_db->get("playbasis_badge_category_to_client");
        return $result;
    }

    public function retrieveItemCategoryByNameButNotID($client_id, $site_id, $name, $category_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', $category_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        $result = $this->mongo_db->get("playbasis_badge_category_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveItemCategoryById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if(isset($id) && !empty($id)){
            $this->mongo_db->where('_id', new MongoId($id));
            $this->mongo_db->where('deleted', false);
            $c = $this->mongo_db->get("playbasis_badge_category_to_client");

            return isset($c) && !empty($c) ? $c[0] : null;
        }
    }

    public function updateItemCategory($category_id, $data)
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

        $update = $this->mongo_db->update('playbasis_badge_category_to_client');

        return $update;
    }

    public function deleteItemCategory($client_id, $site_id, $category_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('_id', new MongoID($category_id));
        } catch (Exception $e) {
            return false;
        };
        $this->mongo_db->set('deleted', true);
        $result = $this->mongo_db->update('playbasis_badge_category_to_client');

        if($result){
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('category', new MongoID($category_id));
            $this->mongo_db->set('category', "");
            $this->mongo_db->set('date_modified', new MongoDate());
            $this->mongo_db->update_all('playbasis_badge_to_client');
        }

        return $result;
    }

    public function deleteItemCategoryByIdArray($client_id, $site_id, $id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }
        $this->mongo_db->set('date_modified', new MongoDate());
        $result = $this->mongo_db->update_all('playbasis_badge_category_to_client');

        if($result){
            if (!empty($id_array)) {
                array_walk($id_array, array($this, "makeMongoIdObj"));
                $this->mongo_db->where('client_id', new MongoID($client_id));
                $this->mongo_db->where('site_id', new MongoID($site_id));
                $this->mongo_db->where_in('category', $id_array);
                $this->mongo_db->set('category', "");
            }
            $this->mongo_db->set('date_modified', new MongoDate());
            $this->mongo_db->update_all('playbasis_badge_to_client');
        }

        return $result;
    }

    public function getBadges($data = array(), $onlyTemplate = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
            $filter_category = array();
            foreach ($data['filter_category'] as $catetory_data){
                $filter_category[]= $catetory_data['_id'];
            }
            $this->mongo_db->where_in('category', $filter_category);
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
        $this->mongo_db->where('deleted', false);

        if ($onlyTemplate) {
            $this->mongo_db->where(array("is_template" => true));
        }

        $badges_data = $this->mongo_db->get("playbasis_badge");

        return $badges_data;
    }

    public function getNameOfBadgeID($client_id, $site_id, $badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where('badge_id', new MongoID($badge_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        //$this->mongo_db->where('deleted', false);
        $this->mongo_db->where('status', true);
        $result = $this->mongo_db->get('playbasis_badge_to_client');
        return $result ? $result[0]['name'] : null;
    }

    public function getTotalBadges($data, $onlyTemplate = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
            $filter_category = array();
            foreach ($data['filter_category'] as $catetory_data){
                $filter_category[]= $catetory_data['_id'];
            }
            $this->mongo_db->where_in('category', $filter_category);
        }

        $this->mongo_db->where('deleted', false);

        if ($onlyTemplate) {
            $this->mongo_db->where(array("is_template" => true));
        }

        $total = $this->mongo_db->count("playbasis_badge");

        return $total;
    }

    public function getBadgeBySiteId($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
            $filter_category = array();
            foreach ($data['filter_category'] as $catetory_data){
                $filter_category[]= $catetory_data['_id'];
            }
            $this->mongo_db->where_in('category', $filter_category);
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

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id'] ? new MongoID($data['site_id']) : null);
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        return $results;
    }

    public function getBadgeByClientId($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
            $filter_category = array();
            foreach ($data['filter_category'] as $catetory_data){
                $filter_category[]= $catetory_data['_id'];
            }
            $this->mongo_db->where_in('category', $filter_category);
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

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', $data['client_id'] ? new MongoID($data['client_id']) : null);
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        return $results;
    }

    public function getTotalBadgeBySiteId($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
            $filter_category = array();
            foreach ($data['filter_category'] as $catetory_data){
                $filter_category[]= $catetory_data['_id'];
            }
            $this->mongo_db->where_in('category', $filter_category);
        }
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id'] ? new MongoID($data['site_id']) : null);
        $total = $this->mongo_db->count("playbasis_badge_to_client");

        return $total;
    }

    public function getTotalBadgeBySiteIdWithoutSponsor($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->or_where(array('sponsor' => null, 'sponsor' => false));
        // $this->mongo_db->where('sponsor', null);
        $total = $this->mongo_db->count("playbasis_badge_to_client");
        return $total;
    }

    public function getCommonBadges()
    {

        $results = $this->mongo_db->get("playbasis_badge");

        $badges = array();

        if (count($results) > 0) {
            foreach ($results as &$rown) {
                array_push($badges, array(
                    "id" => $rown['_id'] . "",
                    "name" => $rown['name'],
                    "img_path" => $rown['image'],
                    "description" => $rown['description']
                ));
            }
        }//end if


        $output = array(
            "badges_set_id" => 0,
            "badges_customer_id" => 0,
            "badges_set" => array(
                "set_label" => "Basic Badge",
                "set_id" => "0",
                "items" => $badges
            )
        );

        return $output;
    }

    public function listBadgesTemplate()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('is_template', true);
        return $this->mongo_db->get("playbasis_badge");
    }

    public function copyBadgesFromTemplate($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $badges = $this->listBadgesTemplate();
        if (!$badges) {
            return false;
        }
        foreach ($badges as &$badge) {
            $badge['client_id'] = $client_id;
            $badge['site_id'] = $site_id;
            $badge['badge_id'] = $badge['_id'];
            $badge['date_added'] = $d;
            $badge['date_modified'] = $d;
            unset($badge['is_template']);
            unset($badge['_id']);
        }
        return $this->mongo_db->batch_insert('playbasis_badge_to_client', $badges, array("w" => 0, "j" => false));
    }

    public function addBadge($data, $isTemplate = false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $data['tags'] = isset($data['tags']) && $data['tags'] ? explode(',', $data['tags']) : null;

        $newData = array(
            'stackable' => (int)$data['stackable'] | 0,
            'substract' => (int)$data['substract'] | 0,
            'quantity' => (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null,
            'category' => (isset($data['category']) && !empty($data['category'])) ? new MongoID($data['category']) : null,
            'per_user' => (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'visible' => (bool)$data['visible'],
            'auto_notify' => (bool)$data['auto_notify'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => $d,
            'date_added' => $d,
            'name' => $data['name'] | '',
            'description' => $data['description'] | '',
            'tags' => $data['tags'],
            'hint' => $data['hint'] | '',
            'language_id' => (int)1,
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? (bool)$data['sponsor'] : false,
            'claim' => isset($data['claim']) ? (bool)$data['claim'] : false,
            'redeem' => isset($data['redeem']) ? (bool)$data['redeem'] : false,
        );
        if ($isTemplate) {
            $newData["is_template"] = true;
        }

        return $this->mongo_db->insert('playbasis_badge', $newData);
    }

    public function addBadgeToClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $data['tags'] = isset($data['tags']) && $data['tags'] ? explode(',', $data['tags']) : null;

        return $this->mongo_db->insert('playbasis_badge_to_client', array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'badge_id' => new MongoID($data['badge_id']),
            'stackable' => (int)$data['stackable'] | 0,
            'substract' => (int)$data['substract'] | 0,
            'quantity' => (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null,
            'category' => (isset($data['category']) && !empty($data['category'])) ? new MongoID($data['category']) : null,
            'per_user' => (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'visible' => (bool)$data['visible'],
            'auto_notify' => (bool)$data['auto_notify'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => $d,
            'date_added' => $d,
            'name' => $data['name'] | '',
            'description' => $data['description'] | '',
            'tags' => $data['tags'],
            'hint' => $data['hint'] | '',
            'language_id' => (int)1,
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? (bool)$data['sponsor'] : false,
            'claim' => isset($data['claim']) ? (bool)$data['claim'] : false,
            'redeem' => isset($data['redeem']) ? (bool)$data['redeem'] : false,
            "is_template" => isset($data["is_template"]) ? (bool)$data["is_template"] : false,
        ));
    }

    public function editBadge($badge_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data['tags'] = isset($data['tags']) && $data['tags'] ? explode(',', $data['tags']) : null;

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->set('stackable', (int)$data['stackable']);
        $this->mongo_db->set('substract', (int)$data['substract']);
        $this->mongo_db->set('quantity', (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null);
        $this->mongo_db->set('category', (isset($data['category']) && !empty($data['category'])) ? new MongoID($data['category']) : null);
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('visible', (bool)$data['visible']);
        $this->mongo_db->set('auto_notify', (bool)$data['auto_notify']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('tags', $data['tags']);
        $this->mongo_db->set('hint', $data['hint']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
        $this->mongo_db->set('claim', isset($data['claim']) ? (bool)$data['claim'] : false);
        $this->mongo_db->set('redeem', isset($data['redeem']) ? (bool)$data['redeem'] : false);

        $this->mongo_db->update('playbasis_badge');

        if (isset($data['image'])) {
            $this->mongo_db->where('_id', new MongoID($badge_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update('playbasis_badge');
        }
    }

    public function editBadgeToClient($badge_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $badge = $this->getBadgeToClient($badge_id);
        if ($badge) {
            $_data = $data;
            $_data['tags'] = isset($_data['tags']) && $_data['tags'] ? explode(',', $_data['tags']) : null;
            if (!$this->isSameBadge($badge, $_data)) {
                if (isset($badge["is_template"]) && $badge["is_template"]) {
                    $new_badge = $this->addBadge($data);
                    $this->mongo_db->set("badge_id", $new_badge);
                    $this->mongo_db->unset_field("is_template");
                    if ($badge["name"] == $data["name"]) {
                        $this->mongo_db->set("name", "Cloned from " . $data["name"]);
                    }
                } else {
                    $this->mongo_db->set("name", $data["name"]);
                }
                $data = $_data;

                $this->mongo_db->set('client_id', new MongoID($data['client_id']));
                $this->mongo_db->set('site_id', new MongoID($data['site_id']));
                $this->mongo_db->set('stackable', (int)$data['stackable']);
                $this->mongo_db->set('substract', (int)$data['substract']);
                $this->mongo_db->set('quantity', (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null);
                $this->mongo_db->set('category', (isset($data['category']) && !empty($data['category'])) ? new MongoID($data['category']) : null);
                $this->mongo_db->set('per_user', (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
                $this->mongo_db->set('status', (bool)$data['status']);
                $this->mongo_db->set('visible', (bool)$data['visible']);
                $this->mongo_db->set('auto_notify', (bool)$data['auto_notify']);
                $this->mongo_db->set('sort_order', (int)$data['sort_order']);
                $this->mongo_db->set('description', $data['description']);
                $this->mongo_db->set('tags', $data['tags']);
                $this->mongo_db->set('hint', $data['hint']);
                $this->mongo_db->set('language_id', (int)1);
                $this->mongo_db->set('sponsor',
                    isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
                $this->mongo_db->set('claim',
                    isset($data['claim']) ? (bool)$data['claim'] : false);
                $this->mongo_db->set('redeem',
                    isset($data['redeem']) ? (bool)$data['redeem'] : false);
                $this->mongo_db->set("date_modified", new MongoDate());

                if (isset($data['image'])) {
                    $this->mongo_db->set('image',
                        html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
                }
                $this->mongo_db->where('_id', new MongoID($badge_id));
                $this->mongo_db->update('playbasis_badge_to_client');
            }
        }
    }

    public function editBadgeToClientFromAdmin($badge_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data['tags'] = isset($data['tags']) && $data['tags'] ? explode(',', $data['tags']) : null;

        $this->mongo_db->where('badge_id', new MongoID($badge_id));
        $this->mongo_db->set('stackable', (int)$data['stackable']);
        $this->mongo_db->set('substract', (int)$data['substract']);
        $this->mongo_db->set('quantity', (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null);
        $this->mongo_db->set('category', (isset($data['category']) && !empty($data['category'])) ?  new MongoID($data['category']) : null);
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('visible', (bool)$data['visible']);
        $this->mongo_db->set('auto_notify', (bool)$data['auto_notify']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('tags', $data['tags']);
        $this->mongo_db->set('hint', $data['hint']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
        $this->mongo_db->set('claim', isset($data['claim']) ? (bool)$data['claim'] : false);
        $this->mongo_db->set('redeem', isset($data['redeem']) ? (bool)$data['redeem'] : false);

        $this->mongo_db->update_all('playbasis_badge_to_client');

        if (isset($data['image'])) {
            $this->mongo_db->where('badge_id', new MongoID($badge_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update_all('playbasis_badge_to_client');
        }
    }

    public function deleteBadge($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_badge');
    }

    public function deleteBadgeClient($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_badge_to_client');
    }

    public function increaseOrderByOne($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));

        $badge = $this->mongo_db->get('playbasis_badge');

        $currentOrder = $badge[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_badge');
    }

    public function decreaseOrderByOne($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $badge = $this->mongo_db->get('playbasis_badge');

        $currentOrder = $badge[0]['sort_order'];

        if ($currentOrder != 0) {
            $newOrder = $currentOrder - 1;
            $this->mongo_db->where('_id', new MongoID($badge_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_badge');
        }
    }

    public function increaseOrderByOneClient($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $badge = $this->mongo_db->get('playbasis_badge_to_client');

        $currentOrder = $badge[0]['sort_order'];
        $newOrder = $currentOrder + 1;
        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_badge_to_client');
    }

    public function decreaseOrderByOneClient($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $badge = $this->mongo_db->get('playbasis_badge_to_client');

        $currentOrder = $badge[0]['sort_order'];
        if ($currentOrder != 0) {
            $newOrder = $currentOrder - 1;
            $this->mongo_db->where('_id', new MongoID($badge_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_badge_to_client');
        }
    }

    public function checkBadgeIsPublic($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('badge_id', $badge_id);
        return $this->mongo_db->get('playbasis_badge_to_client');
    }

    public function checkBadgeIsSponsor($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $badge = $this->mongo_db->get('playbasis_badge_to_client');
        return isset($badge[0]['sponsor']) ? $badge[0]['sponsor'] : null;
    }

    public function deleteClientBadgeFromAdmin($badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('badge_id', new MongoID($badge_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update_all('playbasis_badge_to_client');
    }

    /*
     * getAllBadgeIdBySiteId
     *
     * get all badge_id from site_id
     * @param $client_id string
     * @return array
     */
    public function getAllBadgeIdByClientId($client_id, $site_id, $filter_data=array())
    {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
        ));
        return $this->mongo_db->get("playbasis_badge_to_client");
    }

    public function countAllBadgeByCategory($client_id, $site_id, $category)
    {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false,
            'status' => true,
        ));

        if(isset($category) && !empty($category)){
            try {
                $id = new MongoId($category);
                $this->mongo_db->where('category' , $id);
            } catch (Exception $e) {}
        }

        return $this->mongo_db->count("playbasis_badge_to_client");
    }

    public function getAllBadgeByCategory($client_id, $site_id, $category)
    {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false,
            'status' => true,
        ));

        if(isset($category) && !empty($category)){
            try {
                $id = new MongoId($category);
                $this->mongo_db->where('category' , $id);
            } catch (Exception $e) {}
        }

        return $this->mongo_db->get("playbasis_badge_to_client");
    }

    public function getBadgeById($client_id, $site_id, $badge_id)
    {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false,
            'status' => true,
        ));

        if(isset($badge_id) && !empty($badge_id)){
            try {
                $id = new MongoId($badge_id);
                $this->mongo_db->where('badge_id' , $id);
            } catch (Exception $e) {}
        }
        $badge = $this->mongo_db->get("playbasis_badge_to_client");
        return $badge ? $badge[0]:array();
    }
    /*
     * getTemplate
     *
     * Get template badge
     * @return array
     */
    private function getTemplate()
    {
        $this->mongo_db->where(array(
            "is_template" => true,
            "status" => true
        ));
        return $this->mongo_db->get("playbasis_badge");
    }

    /*
     * syncTemplate
     *
     * Sync badges from template if not yet has
     * automatically added with status false
     * @param $badges array of current client's badges
     * @param $client_id string
     * @param $site_id string
     * @return true if already sync otherwise false
     */
    public function syncTemplate($badges, $client_id, $site_id)
    {
        $templates = $this->getTemplate();

        // find which one is not sync to client
        $user_badges = array();
        foreach ($badges as $badge) {
            array_push($user_badges, $badge["badge_id"]);
        }
        $new_badges = array();
        foreach ($templates as $template) {
            if (!in_array($template["_id"], $user_badges)) {
                array_push($new_badges, $template);
            }
        }

        // add to client
        if ($new_badges) {
            foreach ($new_badges as $badge) {
                $badge["badge_id"] = $badge["_id"];
                $badge["is_template"] = true;
                $badge["status"] = false;
                $badge["client_id"] = $client_id;
                $badge["site_id"] = $site_id;
                isset($badge["tags"]) ? $badge["tags"] = implode(',',$badge['tags']) : null;

                $this->addBadgeToClient($badge);
            }
        }

        if ($new_badges) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * isSameBadge
     *
     * check if these metadata is same
     * @param $badge1 array
     * @param $badge2 array
     * @return bool
     */
    private function isSameBadge($badge1, $badge2)
    {
        $badge2["claim"] = isset($badge2["claim"]) ? (bool)$badge2["claim"] : false;
        $badge2["redeem"] = isset($badge2["redeem"]) ? (bool)$badge2["redeem"] : false;
        $badge2["visible"] = isset($badge2["visible"]) ? (bool)$badge2["visible"] : false;
        $badge2["auto_notify"] = isset($badge2["auto_notify"]) ? (bool)$badge2["auto_notify"] : false;
        $badge2["image"] = html_entity_decode($badge2['image'], ENT_QUOTES, "UTF-8");

        return ($badge1["stackable"] == (int)$badge2["stackable"] &&
            $badge1["substract"] == (int)$badge2["substract"] &&
            ((isset($badge1['quantity']) && !($badge1['quantity'] === "")) ? (int)$badge1['quantity'] : null) ===
            ((isset($badge2['quantity']) && !($badge2['quantity'] === "")) ? (int)$badge2['quantity'] : null) &&
            ((isset($badge1['category']) && !empty($badge1['category'])) ? $badge1['category'] : null) ===
            ((isset($badge2['category']) && !empty($badge2['category'])) ? $badge2['category'] : null) &&
            ((isset($badge1['per_user']) && !($badge1['per_user'] === "")) ? (int)$badge1['per_user'] : null) ===
            ((isset($badge2['per_user']) && !($badge2['per_user'] === "")) ? (int)$badge2['per_user'] : null) &&
            $badge1["sort_order"] == (int)$badge2["sort_order"] &&
            $badge1["name"] == $badge2["name"] &&
            $badge1["description"] == $badge2["description"] &&
            (($badge1["tags"] == $badge2["tags"]) || ( !$badge1['tags'] && (!$badge2['tags']) ) ) &&
            $badge1["hint"] == $badge2["hint"] &&
            $badge1["claim"] == $badge2["claim"] &&
            $badge1["redeem"] == $badge2["redeem"] &&
            $badge1["image"] == $badge2["image"] &&
            $badge1["status"] == (bool)$badge2["status"] &&
            ((isset($badge1["visible"]) ? (bool)$badge1["visible"] : false) == $badge2["visible"]) &&
            ((isset($badge1["auto_notify"]) ? (bool)$badge1["auto_notify"] : false) == $badge2["auto_notify"])
        );
    }
    function makeMongoIdObj(&$value)
    {
        $value = new MongoId($value);
    }

    public function getAllBadges($data)
    {
        //get badge ids
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array(
            'badge_id',
            'image',
            'name',
            'description',
            'hint',
            'sponsor',
            'tags',
            'sort_order'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'deleted' => false
        ));
        if (isset($data['tags'])) {
            $this->mongo_db->where_in('tags', $data['tags']);
        }
        $badges = $this->mongo_db->get('playbasis_badge_to_client');
        if ($badges) {
            foreach ($badges as &$badge) {
                $badge['badge_id'] = $badge['badge_id'] . "";
                $badge['image'] = $this->config->item('IMG_PATH') . $badge['image'];
            }
        }
        return $badges;
    }
}
