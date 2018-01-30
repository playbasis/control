<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_gift_model extends MY_Model
{

    function index_id($obj)
    {
        return $obj['_id'];
    }

    public function getTotalReportGift($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['status']) && !is_null($data['status'])) {
            $this->mongo_db->where('gift_type', $data['status']);
        }

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('$or', array(array('sender_player_id' => $data['username']),
                                                array('cl_player_id' => $data['username'])));
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        $results = $this->mongo_db->count("playbasis_gift_log");

        return $results;
    }

    public function getReportGift($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['status']) && !is_null($data['status'])) {
            $this->mongo_db->where('gift_type', $data['status']);
        }

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('$or', array(array('sender_player_id' => $data['username']),
                                                array('cl_player_id' => $data['username'])));
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
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
        $results = $this->mongo_db->get("playbasis_gift_log");

        return $results;
    }
}