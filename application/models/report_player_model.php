<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_player_model extends MY_Model
{


    public function getTotalPlayers($data)
    {

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['username']) && $data['username'] != '') {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
        }

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        $result = $this->mongo_db->count('playbasis_player');

        return $result;
    }

    public function getReportPlayers($data)
    {

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['username']) && $data['username'] != '') {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->or_where(array('username' => $regex, 'email' => $data['username']));
        }

        if (isset($data['filter_site_id']) && $data['filter_site_id'] != '') {
            $this->mongo_db->where('site_id', new MongoID($data['filter_site_id']));
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

        $result = $this->mongo_db->get("playbasis_player");

        return $result;
    }

    public function getAllSitesFromClient($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('status', true);

        return $this->mongo_db->get('playbasis_client_site');
    }
}