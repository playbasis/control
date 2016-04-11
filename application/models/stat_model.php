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

    public function getLastProcessedAction()
    {
        $this->mongo_db->limit(1);
        $res = $this->mongo_db->get('playbasis_stat_latest');
        return $res ? $res[0]['last'] : null;
    }

    public function setLastProcessedAction($last)
    {
        $this->mongo_db->limit(1);
        $r = $this->mongo_db->get('playbasis_stat_latest');
        if ($r) {
            $this->mongo_db->where(array('_id' => $r[0]['_id']));
            $this->mongo_db->set('last', $last);
            $this->mongo_db->update('playbasis_stat_latest');
        } else {
            $this->mongo_db->insert('playbasis_stat_latest', array('last' => $last));
        }
    }

    public function upsertAction($doc, $async=true)
    {
        $this->mongo_db->where(array(
            'd' => $doc['d'],
            'client_id' => $doc['client_id'],
            'site_id' => $doc['site_id'],
            'action_id' => $doc['action_id'],
        ));
        $this->mongo_db->set('d', $doc['d']);
        $this->mongo_db->set('client_id', $doc['client_id']);
        $this->mongo_db->set('site_id', $doc['site_id']);
        $this->mongo_db->set('action_id', $doc['action_id']);
        $this->mongo_db->inc('c', $doc['c']); // https://gist.github.com/rantav/3001646
        return $this->mongo_db->update('playbasis_stat_action', array('upsert' => true));
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

    public function countPlayer($client_id, $site_id, $from, $to)
    {
        if ($client_id) $this->mongo_db->where('client_id', $client_id);
        if ($site_id) $this->mongo_db->where('site_id', $site_id);
        if ($from) $this->mongo_db->where_gte('date_added', $from);
        if ($to) $this->mongo_db->where_lt('date_added', $to);
        return $this->mongo_db->count('playbasis_player');
    }

    public function countAction($client_id, $site_id, $action_id, $from, $to)
    {
        return $this->summarize('playbasis_stat_action', array('client_id' => $client_id, 'site_id' => $site_id, 'action_id' => $action_id), $from, $to, '$d', '$c');
    }

    public function countActions($client_id, $site_id, $from, $to)
    {
        return $this->summarize('playbasis_stat_action', array('client_id' => $client_id, 'site_id' => $site_id), $from, $to, '$action_id', '$c');
    }

    public function countDAU($client_id, $site_id, $from, $to)
    {
        return $this->summarize('playbasis_stat_dau', $site_id ? array('client_id' => $client_id, 'site_id' => $site_id) : null, $from, $to);
    }

    public function countMAU($client_id, $site_id, $from, $to)
    {
        return $this->summarize('playbasis_stat_mau', $site_id ? array('client_id' => $client_id, 'site_id' => $site_id) : null, $from, $to);
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
                $ret['data'][$data['_id']] = $data['c'];
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