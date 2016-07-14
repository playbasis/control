<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaderboard_model extends MY_Model
{
    public function countLeaderBoards($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('status', true);
        $total = $this->mongo_db->count('playbasis_leaderboard');

        return $total;
    }

    public function retrieveLeaderBoards($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
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

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        return $this->mongo_db->get("playbasis_leaderboard");
    }

    public function retrieveLeaderBoard($leaderboard_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($leaderboard_id));
        $c = $this->mongo_db->get('playbasis_leaderboard');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function createLeaderBoard($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date = new MongoDate();
        $date_array = array(
            'date_added' => $date,
            'date_modified' => $date
        );
        $insert_data = array_merge($data, $date_array);

        $insert = $this->mongo_db->insert('playbasis_leaderboard', $insert_data);

        return $insert;
    }

    public function updateLeaderBoard($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($data['_id']));

        $date = new MongoDate();
        $date_array = array(
            'date_modified' => $date
        );

        $update_data = array_merge($data, $date_array);
        // ignore _id
        unset($update_data['_id']);
        foreach ($update_data as $key => $value) {
            $this->mongo_db->set($key, $value);
        }
        $update = $this->mongo_db->update('playbasis_leaderboard');

        return $update;
    }

    public function deleteLeaderBoard($leaderboard_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($leaderboard_id));

        return $this->mongo_db->delete('playbasis_leaderboard');
    }

    public function getRewards($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('group', 'POINT');

        return $this->mongo_db->get('playbasis_reward_to_client');
    }

    public function listLeaderBoards($query_date_check = false)
    {

        $this->mongo_db->where(array(
            'status' => true,
        ));

        if ($query_date_check == true) {
            $selected_time = time();

            $first = date('Y-m-01', $selected_time);
            $from = strtotime($first . ' 00:00:00');

            $last = date('Y-m-t', $selected_time);
            $to = strtotime($last . ' 23:59:59');
            $this->mongo_db->where(array(
                'month' => array('$gte' => $this->new_mongo_date($from), '$lte' => $this->new_mongo_date($to))
            ));
        }

        $result = $this->mongo_db->get('playbasis_leaderboard');

        return !empty($result) ? $result : array();
    }
}