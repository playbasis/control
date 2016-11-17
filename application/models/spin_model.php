<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spin_model extends MY_Model
{
    public function getPlayerAction($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($site_id);
        $date_added = array();
        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_end']) && $data['date_end'] != '') {
            $date_added['$gte'] = new MongoDate(strtotime($data['date_start']));
            $date_added['$lte'] = new MongoDate(strtotime($data['date_end']));
        }

        $default = array('client_id' => new MongoID($client_id),
            'site_id' => new MongoID($site_id),
            '$or' => array(array('request.action' => "paybill"), array('request.action' => "topup")),
            'uri' => "Engine/rule",
            'response.response.events.0.reward_type' => 'token');
        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
        $results = $this->mongo_db->aggregate('playbasis_web_service_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array('_id' => '$request.player_id', 'n' => array('$sum' => 1))
                ),
                array(
                    '$sort' => array('n' => -1),
                )
            )
        );
        return $results ? $results['result'] : array();
    }

    public function getPlayerSpin($client_id, $site_id, $data=array())
    {
        $this->set_site_mongodb($site_id);
        $date_added = array();
        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_end']) && $data['date_end'] != '') {
            $date_added['$gte'] = new MongoDate(strtotime($data['date_start']));
            $date_added['$lte'] = new MongoDate(strtotime($data['date_end']));
        }
        $default = array('client_id' => new MongoID($client_id),
                         'site_id' => new MongoID($site_id),
                         'uri' => "Engine/rule",
                         'request.action' => "spin",
                         'response.response.events.0.reward_type' => 'goods'
                        );
        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
        $results = $this->mongo_db->aggregate('playbasis_web_service_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array('_id' => '$request.player_id', 'n' => array('$sum' => 1))
                ),
                array(
                    '$sort' => array('n' => -1),
                )
            )
        );
        return $results ? $results['result'] : array();
    }
}