<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function cmp_id($a, $b)
{
    if ($a['_id'] == $b['_id']) {
        return 0;
    }
    return ($a['_id'] < $b['_id']) ? -1 : 1;
}

class Action_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function listActions($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name', 'icon'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_action_to_client');
        if (!$result) {
            $result = array();
        }
        return $result;
    }

    public function listActionsOnlyUsed($data)
    {
        /*
        | -----------------------------------------------------------------------
        | ACTIONS THAT ARE IN PLAYERS HAVE...
        | -----------------------------------------------------------------------
        */
        $rawUsedActions = $this->mongo_db->command(array(
            'distinct' => 'playbasis_action_log',
            'key' => 'action_id',
            'query' => array('client_id' => $data['client_id'], 'site_id' => $data['site_id'])
        ));
        $usedActions = $rawUsedActions['values'];
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name', 'icon'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where_in('action_id', $usedActions);
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_action_to_client');
        if (!$result) {
            $result = array();
        }

        /*
        | -----------------------------------------------------------------------
        | ACTIONS THAT ARE IN RULE ENGINES BUT PLAYERS MAY OR MAY HAVE NOT HAVE THEM...
        | -----------------------------------------------------------------------
        $rawUsedActions = $this->getUsedActionByClientSiteId($data['client_id'], $data['site_id']);
        $usedActions = $rawUsedActions['values'];
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name','icon'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where_in('action_id', $usedActions);
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_action_to_client');
        if (!$result) $result = array();
        */

        return $result;
    }

    public function getUsedActionByClientSiteId($client_id, $site_id)
    {
        return $this->mongo_db->command(array(
            'distinct' => 'playbasis_rule',
            'key' => 'action_id',
            'query' => array('client_id' => $client_id, 'site_id' => $site_id, 'active_status' => true)
        ));
    }

    public function findAction($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('action_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['action_name'])
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_action_to_client');
        return $result ? $result[0]['action_id'] : array();
    }

    public function findActionName($site_id, $_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where(array('site_id' => $site_id));
        $this->mongo_db->where(array('action_id' => $_id));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_action_to_client');
        return $result && isset($result[0]['name']) ? $result[0]['name'] : null;
    }

    public function findActionLogTime($site_id, $_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('date_added'));
        $this->mongo_db->where(array('_id' => $_id));
        $result = $this->mongo_db->get('playbasis_action_log');
        return $result && isset($result[0]['date_added']->sec) ? $result[0]['date_added']->sec : array();
    }

    public function actionLog1($data, $action_name, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), 1); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id'], 'action_name' => $action_name);
        if ($from || $to) {
            $query['date_added'] = array();
        }
        if ($from) {
            $query['date_added']['$gte'] = $this->new_mongo_date($from);
        }
        if ($to) {
            $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
        }
        $result = $this->mongo_db->command(array(
            'mapReduce' => 'playbasis_action_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => array('inline' => 1),
        ));
        $result = $result ? $result['results'] : array();
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 'SKIP'));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 'SKIP'));
        }
        return $result;
    }

    public function actionLogByURL($data, $action_name, $url, $pb_player_id)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->where(array(
            'pb_player_id' => $pb_player_id,
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'action_name' => strtolower($action_name),
            'url' => $url

        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_action_log');
        return $results ? $results[0] : null;

    }

    public function actionLog($data, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $match = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
        );
        if (($from || $to) && !isset($match['date_added'])) {
            $match['date_added'] = array();
        }
        if ($from) {
            $match['date_added']['$gte'] = new MongoDate(strtotime($from . ' 00:00:00'));
        }
        if ($to) {
            $match['date_added']['$lte'] = new MongoDate(strtotime($to . ' 23:59:59'));
        }
        $_result = $this->mongo_db->aggregate('playbasis_player_dau', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array(
                    '_id' => array('date_added' => '$date_added', 'action_id' => '$action_id'),
                    'value' => array('$sum' => '$count')
                )
            ),
        ));
        $_result = $_result ? $_result['result'] : array();
        $result = array();
        $cache = array();
        if (is_array($_result)) {
            foreach ($_result as $value) {
                $key = date('Y-m-d', $value['_id']['date_added']->sec);
                $action_id = $value['_id']['action_id']->{'$id'};
                if (array_key_exists($action_id, $cache)) {
                    $action_name = $cache[$action_id];
                } else {
                    $action_name = $this->findActionName($data['site_id'], $value['_id']['action_id']);
                    $cache[$action_id] = $action_name;
                }
                if (!array_key_exists($key, $result)) {
                    $result[$key] = array();
                }
                $result[$key][$action_name] = $value['value'];
            }
        }
        $_result = array();
        foreach ($result as $key => $value) {
            $_result[] = array('_id' => $key, 'value' => $value);
        }
        $result = array_values($_result);
        usort($result, 'cmp_id');
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 'SKIP'));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 'SKIP'));
        }
        return $result;
    }
}

?>