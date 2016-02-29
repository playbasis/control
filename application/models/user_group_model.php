<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');

    }

    public function getTotalNumUsers()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        return $this->mongo_db->count('user_group');
    }

    public function getUserGroupInfo($user_group_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($user_group_id));
        $results = $this->mongo_db->get('user_group');

        return ($results) ? $results[0] : null;
    }

    public function getAllFeatures()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        return $this->mongo_db->get('playbasis_feature');
    }

    public function insertUserGroup()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $usergroup_name = $this->input->post('usergroup_name');
        $permissions_access_modify = $this->input->post("permission");

        $data = array(
            'name' => $usergroup_name,
//          'permission'=> serialize($permissions_access_modify)
            'permission' => $permissions_access_modify
        );

        $this->mongo_db->insert('user_group', $data);
    }

    public function deleteUserGroup($usergroup_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($usergroup_id));
        $this->mongo_db->delete('user_group');
    }

    public function editUserGroup($user_group_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($user_group_id));
        $this->mongo_db->set('name', $data['usergroup_name']);
        if (isset($data['permission'])) {
//          $this->mongo_db->set('permission', serialize($data['permission']));
            $this->mongo_db->set('permission', $data['permission']);
        } else {
            $this->mongo_db->set('permission', "");
        }
        $this->mongo_db->update('user_group');

    }

    public function fetchAllUserGroups($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

        $results = $this->mongo_db->get("user_group");

        return $results;
    }

    public function checkUsersInUserGroup($user_group_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('user_group_id', new MongoID($user_group_id));
        return $this->mongo_db->get('user');
    }

}