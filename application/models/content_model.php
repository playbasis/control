<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_Model
{
    public function countContents($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($optionalParams['title']) && !is_null($optionalParams['title'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['title'])) . "/i");
            $this->mongo_db->where('title', $regex);
        }

        if (isset($optionalParams['filter_id']) && !is_null($optionalParams['filter_id'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['filter_id'])) . "/i");
            $this->mongo_db->where('node_id', $regex);
        }

        if(isset($optionalParams['filter_tags']) && $optionalParams['filter_tags']) {
            $tags = explode(',', $optionalParams['filter_tags']);
            $this->mongo_db->where_in('tags', $tags);
        }

        if (isset($optionalParams['author'])) {
            if(!empty($optionalParams['author'])){
                $this->mongo_db->where('pb_player_id', new MongoId($optionalParams['author']));
            }
            else{
                return 0;
            }
        }

        if (isset($optionalParams['category']) && !is_null($optionalParams['category'])) {
            $this->mongo_db->where('category', new MongoId($optionalParams['category']));
        }

        if (isset($optionalParams['status']) && !is_null($optionalParams['status'])) {
            $bool = $optionalParams['status'] == 'enable' ? true : false;
            $this->mongo_db->where('status', $bool);
        }

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

        if (isset($optionalParams['filter_id']) && !is_null($optionalParams['filter_id'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['filter_id'])) . "/i");
            $this->mongo_db->where('node_id', $regex);
        }

        if(isset($optionalParams['filter_tags']) && $optionalParams['filter_tags']) {
            $tags = explode(',', $optionalParams['filter_tags']);
            $this->mongo_db->where_in('tags', $tags);
        }

        if (isset($optionalParams['author'])) {
            if(!empty($optionalParams['author'])){
                $this->mongo_db->where('pb_player_id', new MongoId($optionalParams['author']));
            }
            else{
                return array();
            }
        }

        if (isset($optionalParams['category']) && !is_null($optionalParams['category'])) {
            $this->mongo_db->where('category', new MongoId($optionalParams['category']));
        }

        if (isset($optionalParams['status']) && !is_null($optionalParams['status'])) {
            $bool = $optionalParams['status'] == 'enable' ? true : false;
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

    public function retrieveContent($client_id, $site_id, $content_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', new MongoId($content_id));
        $c = $this->mongo_db->get('playbasis_content_to_client');
        return $c ? $c[0] : null;
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

    public function findContent($client_id, $site_id, $node_id, $content_id=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('node_id', $node_id);
        $this->mongo_db->where('deleted', false);
        if($content_id){
            $this->mongo_db->where_ne('_id', new MongoId($content_id));
        }
        $c = $this->mongo_db->count('playbasis_content_to_client');
        return $c > 0;
    }

    public function getContentByNodeID($client_id, $site_id, $node_id)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('node_id', $node_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->limit(1);

        $result =  $this->mongo_db->get('playbasis_content_to_client');
        return $result ? $result[0] : null;
    }

    public function updateContent($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($data['_id']));

        $this->mongo_db->set($data);
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

    public function retrieveContentCategoryByName($client_id, $site_id, $name)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        $result = $this->mongo_db->get("playbasis_content_category_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveContentCategoryByNameButNotID($client_id, $site_id, $name, $category_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', $category_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        $result = $this->mongo_db->get("playbasis_content_category_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveContentCategoryById($client_id, $site_id, $id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
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

    public function deleteContentCategory($client_id, $site_id, $category_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('_id', new MongoID($category_id));
        } catch (Exception $e) {
            return false;
        };

        $this->mongo_db->set('deleted', true);
        $result = $this->mongo_db->update('playbasis_content_category_to_client');

        if($result){
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('category', new MongoID($category_id));
            $this->mongo_db->set('category', "");
            $this->mongo_db->set('date_modified', new MongoDate());

            $this->mongo_db->update_all('playbasis_content_to_client');
        }

        return $result;
    }

    public function deleteContentCategoryByIdArray($client_id, $site_id, $id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $result = $this->mongo_db->update_all('playbasis_content_category_to_client');

        if($result){
            if (!empty($id_array)) {
                array_walk($id_array, array($this, "makeMongoIdObj"));
                $this->mongo_db->where('client_id', new MongoID($client_id));
                $this->mongo_db->where('site_id', new MongoID($site_id));
                $this->mongo_db->where_in('category', $id_array);
                $this->mongo_db->set('category', "");
            }

            $this->mongo_db->set('date_modified', new MongoDate());

            $this->mongo_db->update_all('playbasis_content_to_client');
        }

        return $result;
    }

    public function getOrganizationToContent($client_id, $site_id, $content_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where(array(
            'content_id' => new MongoId($content_id),
            'site_id' => new MongoId($site_id),
            'client_id' => new MongoId($client_id)
        ));
        $results = $this->mongo_db->get("playbasis_store_organize_to_content");
        return $results;
    }

    public function addContentToNode($content_id, $node_id)
    {
        $status = $this->_api->addContentToNode($content_id, $node_id);
        return $status;
    }

    public function setContentRole($content_id, $node_id, $role)
    {
        $status = $this->_api->setContentRole($content_id, $node_id, array('role' => $role));
        return $status;
    }

    public function editOrganizationOfContent($client_id, $site_id, $org_id, $content_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoID($org_id));

        $this->mongo_db->set('content_id', new MongoID($content_id));
        $this->mongo_db->set('node_id', new MongoID($node_id));
        return $this->mongo_db->update('playbasis_store_organize_to_content');
    }

    public function getRole($client_id, $site_id, $content_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('roles'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $results = $this->mongo_db->get("playbasis_store_organize_to_content");
        return $results;
    }

    public function clearContentRole( $content_id, $node_id, $role)
    {
        $status = $this->_api->unsetContentRole($content_id, $node_id, array('role' => $role));
        return $status;
    }

    function makeMongoIdObj(&$value)
    {
        $value = new MongoId($value);
    }

    public function createContentToLanguage($client_id, $site_id, $content_id , $language_id, $content_data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date = new MongoDate();
        $data =  array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'content_id' => new MongoId($content_id),
            'language_id' => new MongoId($language_id),
            'title' => (isset($content_data['title']) && $content_data['title']) ? $content_data['title'] : "",
            'summary' => (isset($content_data['summary']) && $content_data['summary']) ? $content_data['summary'] : "",
            'detail' => (isset($content_data['detail']) && $content_data['detail']) ? $content_data['detail'] : "",
            'deleted' => false,
            'date_added' => $date,
            'date_modified' => $date
        );
        $insert = $this->mongo_db->insert('playbasis_content_to_language', $data);

        return $insert;
    }

    public function getContentToLanguage($client_id, $site_id, $content_id , $language_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('title', 'summary', 'detail'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('language_id', new MongoId($language_id));

        $result = $this->mongo_db->get('playbasis_content_to_language');
        return $result ? $result[0] : null;
    }

    public function updateContentToLanguage($client_id , $site_id, $content_id , $language_id, $content_data)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('language_id', new MongoId($language_id));

        $this->mongo_db->set('title', (isset($content_data['title']) && $content_data['title']) ? $content_data['title'] : "");
        $this->mongo_db->set('summary', (isset($content_data['summary']) && $content_data['summary']) ? $content_data['summary'] : "");
        $this->mongo_db->set('detail', (isset($content_data['detail']) && $content_data['detail']) ? $content_data['detail'] : "");
        $this->mongo_db->set('date_modified', new MongoDate());
        $update = $this->mongo_db->update('playbasis_content_to_language');

        return $update;
    }

    public function deleteContentToLanguage($content_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($content_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_content_to_language');
    }

}