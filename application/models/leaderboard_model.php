<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaderboard_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getLeaderBoard($data, $query_date_check = false)
    {
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->select(array('name', 'desc', 'image'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true,
            'deleted' => false
        ));

        if (isset($data['leaderboard_id']) && !empty($data['leaderboard_id'])) {
            $this->mongo_db->where(array(
                '_id' => $data['leaderboard_id']
            ));
        }

        if (isset($data['name']) && !empty($data['name'])) {
            $this->mongo_db->where(array(
                'name' => $data['name']
            ));
        }

        if ($query_date_check == true) {
            $this->mongo_db->where_gte('date_end', new MongoDate());
            $this->mongo_db->where_lt('date_start', new MongoDate());
        }

        $result = $this->mongo_db->get('playbasis_leaderboard');

        return !empty($result) ? $result : array();
    }
}