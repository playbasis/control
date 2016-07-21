<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stat_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
        // calling an anonymous function which has been assigned to a property is not directly possible.
        // doing this will allow to call function using member as a variable.
        // http://php.net/manual/en/language.oop5.basic.php
        $mongo_db = $this->mongo_db;
        $f = function($collection) use ($mongo_db) {
            return function($arr, $async=true) use ($collection, $mongo_db) {
                if (!$arr || !is_array($arr)) return false;
                $options = array_merge(array('continueOnError' => true), $async ? array("w" => 0, "j" => false) : array());
                return $mongo_db->batch_insert($collection, $arr, $options);
            };
        };
        $this->insertDAUs = $f('playbasis_stat_dau');
        $this->insertMAUs = $f('playbasis_stat_mau');
    }

    public function insertDAUs($arr, $async=true)
    {
        $f = $this->insertDAUs;
        return $f($arr, $async);
    }

    public function insertMAUs($arr, $async=true)
    {
        $f = $this->insertMAUs;
        return $f($arr, $async);
    }

    public function countPlayerTotal($client_id, $site_id, $from, $to)
    {
        if ($client_id) $this->mongo_db->where('client_id', $client_id);
        if ($site_id) $this->mongo_db->where('site_id', $site_id);
        if ($from) $this->mongo_db->where_gte('date_added', $from);
        if ($to) $this->mongo_db->where_lt('date_added', $to);
        return $this->mongo_db->count('playbasis_player');
    }

    public function countPlayer($client_id, $site_id, $from, $to)
    {
        $match = array('date_added' => array('$gte' => $from, '$lt' => $to));
        if ($client_id) $match['client_id'] = $client_id;
        if ($site_id) $match['site_id'] = $site_id;
        return $this->summarize('playbasis_player', $match, null, null, array('d' => array('$dayOfMonth' => '$date_added'), 'm' => array('$month' => '$date_added'), 'y' => array('$year' => '$date_added')));
    }

    public function countAction($client_id, $site_id, $action_id, $from, $to)
    {
        $match = array('action_id' => $action_id);
        if ($client_id) $match['client_id'] = $client_id;
        if ($site_id) $match['site_id'] = $site_id;
        return $this->summarize('playbasis_stat_action', $match, $from, $to, '$d', '$c');
    }

    public function countActions($client_id, $site_id, $from, $to)
    {
        $match = array();
        if ($client_id) $match['client_id'] = $client_id;
        if ($site_id) $match['site_id'] = $site_id;
        return $this->summarize('playbasis_stat_action', $match, $from, $to, '$action_id', '$c');
    }

    public function countDAU($client_id, $site_id, $from, $to)
    {
        $match = array();
        if ($client_id) $match['client_id'] = $client_id;
        if ($site_id) $match['site_id'] = $site_id;
        return $this->summarize('playbasis_stat_dau', $match, $from, $to);
    }

    public function countMAU($client_id, $site_id, $from, $to)
    {
        $match = array();
        if ($client_id) $match['client_id'] = $client_id;
        if ($site_id) $match['site_id'] = $site_id;
        return $this->summarize('playbasis_stat_mau', $match, $from, $to);
    }

    protected function summarize($collection, $match, $from, $to, $group_by='$d', $sum_val=1)
    {
        if (($from || $to) && !isset($match['d'])) {
            $match['d'] = array();
        }
        if ($from) {
            $match['d']['$gte'] = $from;
        }
        if ($to) {
            $match['d']['$lt'] = $to;
        }
        $result = $this->mongo_db->aggregate($collection, array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => $group_by, 'c' => array('$sum' => $sum_val))
            ),
        ));
        if (!$result || !isset($result['ok']) || !$result['ok'] || !$result['result']) return false;
        $ret = null;
        if (is_array($result['result'])) {
            $ret = array('n' => 0, 'data' => array());
            $sum = 0;
            $min = null;
            $max = null;
            foreach ($result['result'] as $data) {
                $ret['data'][json_encode($data['_id'])] = $data['c'];
                $sum += $data['c'];
                if (!isset($ret['n'])) $ret['n'] = 0;
                $ret['n']++;
                if (is_null($min)) {
                    $min = $max = $data;
                } else {
                    if ($data['c'] < $min['c']) $min = $data;
                    if ($data['c'] > $max['c']) $max = $data;
                }
            }
            $ret['sum'] = $sum;
            $ret['avg'] = $sum/(1.0*$ret['n']);
            $ret['min'] = $min;
            $ret['max'] = $max;
        }
        return $ret;
    }
}
?>