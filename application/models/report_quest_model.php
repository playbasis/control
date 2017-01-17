<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_quest_model extends MY_Model
{

    function index_id($obj)
    {
        return $obj['_id'];
    }

    public function getTotalReportQuest($data)
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

        if (isset($data['quest_id']) && $data['quest_id'] != '') {
            $this->mongo_db->where('quest_id', new MongoID($data['quest_id']));
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_modified', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        $results = $this->mongo_db->count("playbasis_quest_log");

        return $results;
    }

    public function getReportQuest($data)
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

        if (isset($data['quest_id']) && $data['quest_id'] != '') {
            $this->mongo_db->where('quest_id', new MongoID($data['quest_id']));
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_modified', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
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
        $results = $this->mongo_db->get("playbasis_quest_log");

        return $results;
    }

    public function getQuestName($quest_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quest_id));
        $this->mongo_db->where_not_in('deleted', array(true));
        $var = $this->mongo_db->get('playbasis_quest_to_client');
        return isset($var[0]) ? $var[0] : null;
    }


}

?>