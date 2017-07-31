<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_custompoint_model extends MY_Model
{

    function index_id($obj)
    {
        return $obj['_id'];
    }

    public function getTotalReportPoint($data)
    {

        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
            $users1 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('email', $data['username']);
            $users2 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where_in('pb_player_id',
                array_merge(array_map('index_id', $users1), array_map('index_id', $users2)));
        }

        if (isset($data['reward_id']) && !empty($data['reward_id'])) {
            $this->mongo_db->where_in('reward_id', $data['reward_id']);
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('item_id', null);
        $this->mongo_db->where('event_type', "REWARD");
        $this->mongo_db->where_gt('value', 0);

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_modified', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['ex_id']) && $data['ex_id']){
            $this->mongo_db->where('transaction_id', array('$exists' => true , '$nin' => $data['ex_id'], '$ne' => null));
        }

        if (isset($data['in_id']) && $data['in_id']){
            $this->mongo_db->where_in('transaction_id', $data['in_id']);
        }

        $results = $this->mongo_db->count("playbasis_event_log");

        return $results;
    }

    public function getReportPoint($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
            $users1 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('email', $data['username']);
            $users2 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where_in('pb_player_id',
                array_merge(array_map('index_id', $users1), array_map('index_id', $users2)));
        }

        if (isset($data['reward_id']) && !empty($data['reward_id'])) {
            $this->mongo_db->where_in('reward_id', $data['reward_id']);
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('item_id', null);
        $this->mongo_db->where('event_type', "REWARD");
        $this->mongo_db->where_gt('value', 0);

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_modified', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['ex_id']) && $data['ex_id']){
            $this->mongo_db->where('transaction_id', array('$exists' => true , '$nin' => $data['ex_id'], '$ne' => null));
        }

        if (isset($data['in_id']) && $data['in_id']){
            $this->mongo_db->where_in('transaction_id', $data['in_id']);
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
        $this->mongo_db->order_by(array('date_added' => 'ASC'));
        $results = $this->mongo_db->get("playbasis_event_log");

        return $results;
    }

    public function getPointName($reward_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('reward_id', new MongoID($reward_id));
        $var = $this->mongo_db->get('playbasis_reward_to_client');
        return isset($var[0]) ? $var[0] : null;
    }

    public function getPointBySite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where_ne('name', 'badge');

        return $this->mongo_db->get('playbasis_reward_to_client');
    }

    public function getListRewardStatusToPlayer($data, $status)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('date_added', array('$gte' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        if ($status == 'approve'){
            $this->mongo_db->where_ne('status', 'approve');
        } else {
            $this->mongo_db->where('status', $status);
        }
        return $this->mongo_db->get('playbasis_reward_status_to_player');
    }
}

?>