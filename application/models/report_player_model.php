<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_player_model extends MY_Model
{


    public function getTotalPlayers($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('action_name', 'invited');

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('cl_player_id', $data['username']);
        }

        $result = $this->mongo_db->count('playbasis_validated_action_log');

        return $result;
    }

    public function getReportPlayers($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('action_name', 'invited');

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

            if (isset($data['username']) && $data['username'] != '') {
                $this->mongo_db->where('cl_player_id', $data['username']);
            }


            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }
        $this->mongo_db->order_by(array('date_added' => 'ASC'));
        $result = $this->mongo_db->get("playbasis_validated_action_log");

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