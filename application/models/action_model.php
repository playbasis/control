<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_Model
{
    public function getAction($action_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results ? $results[0] : null;
    }

    public function getActions(){
        $this->set_site_mongodb(0);

        $results = $this->mongo_db->get("playbasis_action");

        return $results;
    }

    public function getTotalActions(){
        $this->set_site_mongodb(0);

        $results = $this->mongo_db->count("playbasis_action");

        return $results;
    }

    public function getTotalActionReport($data) {

        $this->set_site_mongodb(0);

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['action_id']) && $data['action_id'] != 0) {
            $this->mongo_db->where('action_id',  new MongoID($data['action_id']));
        }

        $results = $this->mongo_db->count("playbasis_action_log");

        return $results;

    }

    public function getActionClient($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
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

        $results = $this->mongo_db->get("playbasis_action_to_client");

        $actions = array();
        $tmp = array();
        foreach ($results as $result) {
            if (!in_array($result['action_id'], $tmp)) {
                $a = $result;
                $actions[] = $a;
                $tmp[] = $result['action_id'];
            }
        }

        return $actions;
    }

    //dupicate function just count on getActionClient
    /*public function getTotalActionClient($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->get("playbasis_action_to_client");

        $actions = array();
        $tmp = array();
        foreach ($results as $result) {
            if (!in_array($result['action_id'], $tmp)) {
                $a = $result;
                $actions[] = $a;
                $tmp[] = $result['action_id'];
            }
        }

        return count($actions);
    }*/

    public function getActionReport($data) {

        $this->set_site_mongodb(0);

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['action_id']) && $data['action_id'] != 0) {
            $this->mongo_db->where('action_id',  new MongoID($data['action_id']));
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

        $results = $this->mongo_db->get("playbasis_action_log");

        return $results;

    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }
}
?>