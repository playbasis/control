<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language_model extends MY_Model
{

    public function retrieveLanguageByID($client_id, $site_id, $language_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($language_id));
        $this->mongo_db->where('deleted', false);

        $result = $this->mongo_db->get('playbasis_language_to_client');

        return $result ? $result[0] : null;
    }

    public function retrieveLanguageByName($client_id, $site_id, $language)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('language', $language);
        $result = $this->mongo_db->get("playbasis_language_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveLanguageByNameButNotID($client_id, $site_id, $language, $language_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', new MongoID($language_id));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('language', $language);
        $result = $this->mongo_db->get("playbasis_language_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveLanguageByAbbr($client_id, $site_id, $abbr)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('abbreviation', $abbr);
        $result = $this->mongo_db->get("playbasis_language_to_client");
        return $result ? $result[0] : null;
    }

    public function retrieveLanguageByAbbrButNotID($client_id, $site_id, $abbr, $language_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', new MongoID($language_id));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('abbreviation', $abbr);
        $result = $this->mongo_db->get("playbasis_language_to_client");
        return $result ? $result[0] : null;
    }

    public function getLanguageList($client_id, $site_id, $status = true)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('language', 'abbreviation'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', $status);
        $this->mongo_db->where('deleted', false);

        $results = $this->mongo_db->get("playbasis_language_to_client");

        return $results;
    }

    public function retrieveLanguage($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($data['client_id']));
        $this->mongo_db->where('site_id', new MongoId($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_language']) && !is_null($data['filter_language'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_language'])) . "/i");
            $this->mongo_db->where('language', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'language',
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


        $result = $this->mongo_db->get('playbasis_language_to_client');

        return $result;
    }

    public function getTotalLanguage($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_language']) && !is_null($data['filter_language'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_language'])) . "/i");
            $this->mongo_db->where('language', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        return $this->mongo_db->count("playbasis_language_to_client");
    }

    public function insertLanguage($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'language' =>$data['language'],
            'abbreviation' =>$data['abbreviation'],
            'status' => (bool)$data['status'],
            'tags' => isset($tags) ? $tags : null,
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_language_to_client', $insert_data);

        return $insert;
    }

    public function updateLanguage($client_id, $site_id, $language_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $this->mongo_db->where('_id', new MongoID($language_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        $this->mongo_db->set('language', $data['language']);
        $this->mongo_db->set('abbreviation', $data['abbreviation']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('date_modified', new MongoDate());

        $result = $this->mongo_db->update('playbasis_language_to_client');

        return $result;
    }

    public function deleteLanguage($client_id, $site_id, $language_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($language_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);

        $result = $this->mongo_db->update('playbasis_language_to_client');

        return $result;
    }

}

?>