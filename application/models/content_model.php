<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveContent($client_id, $site_id, $optionalParams = array(), $exclude_id = null)
    {
        $this->set_site_mongodb($site_id);
        log_message('error',print_r($exclude_id, true));

        // Searching
        if (isset($optionalParams['category']) && !is_null($optionalParams['category'])) {
            $category_result = $this->retrieveContentCategory($client_id, $site_id,
                array('name' => $optionalParams['category']));
        }
        if (isset($optionalParams['title']) && !is_null($optionalParams['title'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($optionalParams['title'])) . "/i");
            $this->mongo_db->where('title', $regex);
        }
        if ($exclude_id){
            $this->mongo_db->where_not_in('_id', $exclude_id);
        }
        if (isset($optionalParams['category']) && !is_null($optionalParams['category'])) {
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
        if (isset($optionalParams['pin'])){
            $this->mongo_db->where('pin', $optionalParams['pin']);
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
        } else if(isset($optionalParams['sort']) && $optionalParams['sort'] == "random") {
            $this->mongo_db->order_by(array('date_added' => 1));
        } else {
            $this->mongo_db->order_by(array('title' => $order));
        }

        // Paging
        if ((isset($optionalParams['offset']) || isset($optionalParams['limit'])) && !(isset($optionalParams['sort']) && $optionalParams['sort'] == "random")) {
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
        } else {
            $this->mongo_db->where_gte('date_end', new MongoDate());
            $this->mongo_db->where_lt('date_start', new MongoDate());
        }

        $this->mongo_db->select(array('_id', 'title', 'summary', 'detail', 'image','pb_player_id', 'category', 'date_start', 'date_end', 'pin'));
        //$this->mongo_db->select(array(), array('_id'));
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
        $sort_data = array('_id', 'name', 'date_added', 'date_modified');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('_id' => $order));
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

    public function createContent($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data = array_merge($data, array(
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        ));
        $insert = $this->mongo_db->insert('playbasis_content_to_client', $data);
        return $insert;
    }

    public function updateContent($client_id, $site_id, $content_id, $data)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('_id', new MongoID($content_id));

        $this->mongo_db->set($data);

        $this->mongo_db->set('date_modified', new MongoDate());
        $update = $this->mongo_db->update('playbasis_content_to_client');
        return $update;
    }

    public function addPlayerAction($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'content_id' => new MongoId($data['content_id']),
            'pb_player_id' => new MongoId($data['pb_player_id']),
            'action' => $data['action'],
            //'star' => isset($data['star'])?$data['star']:null,
            //'feedback' => (isset($data['feedback'])) ? $data['feedback'] : null,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $action = $this->mongo_db->insert('playbasis_content_to_player', $insert_data);

        return $action;
    }

    public function retrieveExistingPlayerContent($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('content_id', new MongoID($data['content_id']));
        $this->mongo_db->where('pb_player_id', new MongoID($data['pb_player_id']));
        $result = $this->mongo_db->get("playbasis_content_to_player");
        return $result;
    }

    public function updatePlayerContent($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('content_id', new MongoID($data['content_id']));
        $this->mongo_db->where('pb_player_id', new MongoID($data['pb_player_id']));

        $this->mongo_db->set($data);

        $this->mongo_db->set('date_modified', new MongoDate());
        $update = $this->mongo_db->update('playbasis_content_to_player');

        return $update;
    }

    public function setPinToContent($client_id, $site_id, $content_id, $pin_data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($content_id));

        $this->mongo_db->set('pin', $pin_data);

        $update = $this->mongo_db->update('playbasis_content_to_client');

        return $update;
    }

    public function deleteContent($client_id, $site_id, $content_id)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($content_id));

        $this->mongo_db->set(array(
            'deleted' => true
        ));

        $delete = $this->mongo_db->update('playbasis_content_to_client');
        return $delete;
    }

    public function setContentFeedback($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'content_id' => isset($data['content_id'])?new MongoId($data['content_id']):null,
            'pb_player_id' => isset($data['pb_player_id'])?new MongoId($data['pb_player_id']):null,
            'feedback' => isset($data['feedback'])?$data['feedback']:null,
            'custom' => $data['custom'],
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $result = $this->mongo_db->insert('playbasis_content_feedback', $insert_data);

        return $result;
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

    public function getContentIDToPlayer($client_id, $site_id, $pb_player_id)
    {
        try {
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        } catch (Exception $e) {
            return false;
        };

        $player_content = $this->mongo_db->get('playbasis_content_to_player');

        $return = array();
        foreach ($player_content as $key => $val){
            array_push($return, $val['content_id']);
        }
        return $return;
    }

    public function countContent($client_id, $site_id, $query_data)
    {
        if (isset($query_data['player_exclude']) && !is_null($query_data['player_exclude'])) {
            $this->mongo_db->where_not_in('_id', $query_data['player_exclude']);
        }
        if (isset($query_data['category']) && !is_null($query_data['category'])) {
            $this->mongo_db->where('category', $query_data['category'][0]['_id']);
        }

        try {
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
        } catch (Exception $e) {
            return false;
        };

        $result = $this->mongo_db->count('playbasis_content_to_client');
        return $result;
    }
}