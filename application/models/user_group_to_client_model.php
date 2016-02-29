<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_to_client_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');

    }

    public function getTotalNumUsers($client_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        return $this->mongo_db->count('user_group_to_client');
    }

    public function fetchAllUserGroups($client_id, $data = null)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        // if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
        //     $this->mongo_db->where('status', (bool)$data['filter_status']);
        // }

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

        $results = $this->mongo_db->get("user_group_to_client");

        return $results;
    }

    public function getUserGroupInfo($client_id, $user_group_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));

        $this->mongo_db->where('_id', new MongoID($user_group_id));
        $results = $this->mongo_db->get('user_group_to_client');

        return ($results) ? $results[0] : null;
    }

    public function getAllFeatures($client_id, $site_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        return $this->mongo_db->get('playbasis_feature_to_client');
    }

    public function editUserGroup($client_id, $user_group_id, $data)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('_id', new MongoID($user_group_id));

        $this->mongo_db->set('name', $data['usergroup_name']);
        if (isset($data['permission'])) {
            $this->mongo_db->set('permission', $data['permission']);
        } else {
            $this->mongo_db->set('permission', "");
        }
        $this->mongo_db->update('user_group_to_client');

    }

    public function insertUserGroup($client_id)
    {

        $usergroup_name = $this->input->post('usergroup_name');
        $permissions_access_modify = $this->input->post("permission");

        $data = array(
            'client_id' => $client_id,
            'name' => $usergroup_name,
            'permission' => $permissions_access_modify
        );

        $this->mongo_db->insert('user_group_to_client', $data);
    }

    public function deleteUserGroup($client_id, $usergroup_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('_id', new MongoID($usergroup_id));
        $this->mongo_db->delete('user_group_to_client');
    }
}