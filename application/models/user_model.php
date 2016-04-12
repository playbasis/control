<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getById($user_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('_id', new MongoID($user_id));
        $results = $this->mongo_db->get("user");
        return ($results && count($results) > 0) ? $results[0] : null;
    }

    public function getByUsername($username)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('username', $username);
        $results = $this->mongo_db->get("user");
        return ($results && count($results) > 0) ? $results[0] : null;
    }
}