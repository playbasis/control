<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reward_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function listRewards($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        if (!$result) {
            $result = array();
        }
        return $result;
    }

    public function listPendingRewards($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(
            array('_id',
                  'cl_player_id',
                  'reward_id',
                  'value',
                  'status',
                  'date_added'
            ));

        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
        ));
        if (isset($data['status'])){
            $this->mongo_db->where('status', $data['status']);
        }

        if (isset($data['player_list']) && !empty($data['player_list']) && is_array($data['player_list'])){
            $this->mongo_db->where_in('cl_player_id', $data['player_list']);
        }
        if (isset($data['from']) && !empty($data['from'])){
            $this->mongo_db->where_gte('date_added', $data['from']);
        }
        if (isset($data['to']) && !empty($data['to'])){
            $this->mongo_db->where_lte('date_added', $data['to']);
        }

        if (isset($data['offset']) && !empty($data['offset'])) {
            if ($data['offset'] < 0) {
                $data['offset'] = 0;
            }
        } else {
            $data['offset'] = 0;
        }

        if (isset($data['limit']) && !empty($data['limit'])) {
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
        } else {
            $data['limit'] = 20;
        }

        $this->mongo_db->limit((int)$data['limit']);
        $this->mongo_db->offset((int)$data['offset']);
        $result = $this->mongo_db->get('playbasis_reward_status_to_player');
        return $result;
    }

    public function getPendingRewardsById($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(
            array('_id',
                'cl_player_id',
                'reward_id',
                'value',
                'status',
                'date_added'
            ));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['transaction_id']
        ));
        $result = $this->mongo_db->get('playbasis_reward_status_to_player');
        return $result? $result[0]: null;
    }
    public function approvePendingReward($data,$approve)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['transaction_id'],
            'status' => 'pending'
        ));
        $pending_reward = $this->mongo_db->get('playbasis_reward_status_to_player');

        if ($pending_reward){
            $pending_reward = $pending_reward[0];
            if ($approve) {
                $this->mongo_db->where(array(
                    'client_id' => $data['client_id'],
                    'site_id' => $data['site_id'],
                    '_id' => $data['transaction_id']
                ));
                $this->mongo_db->set('status', 'approve');
                $this->mongo_db->update('playbasis_reward_status_to_player');

                $this->mongo_db->where(array(
                    'client_id' => $data['client_id'],
                    'site_id' => $data['site_id'],
                    'reward_id' => $pending_reward['reward_id'],
                    'pb_player_id' => $pending_reward['pb_player_id']
                ));
                $player_reward = $this->mongo_db->get('playbasis_reward_to_player');
                if ($player_reward){
                    $this->mongo_db->where(array(
                        'client_id' => $data['client_id'],
                        'site_id' => $data['site_id'],
                        'reward_id' => $pending_reward['reward_id'],
                        'pb_player_id' => $pending_reward['pb_player_id']
                    ));
                    $this->mongo_db->inc('value', intval($pending_reward['value']));
                    $this->mongo_db->set('date_modified', new MongoDate());
                    $this->mongo_db->update('playbasis_reward_to_player');
                } else {
                    unset($pending_reward['status']);
                    $pending_reward['date_added'] = new MongoDate();
                    $pending_reward['date_modified'] = new MongoDate();
                    $this->mongo_db->insert('playbasis_reward_to_player',$pending_reward);
                }
            } else {
                $this->mongo_db->where(array(
                    'client_id' => $data['client_id'],
                    'site_id' => $data['site_id'],
                    '_id' => $data['transaction_id']
                ));
                $this->mongo_db->set('status', 'reject');
                $this->mongo_db->update('playbasis_reward_status_to_player');

                $this->mongo_db->where(array(
                    'client_id' => $data['client_id'],
                    'site_id' => $data['site_id'],
                    'reward_id' => $pending_reward['reward_id']
                ));
                $this->mongo_db->inc('quantity', intval($pending_reward['value']));
                $this->mongo_db->set('date_modified', new MongoDate());
                $this->mongo_db->update('playbasis_reward_to_client');
            }
            return true;
        } else {
            return false;
        }
    }

    public function getRewardName($data, $reward_id)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'reward_id' => $reward_id
        ));
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0]['name'] : array();
    }

    public function findByName($data, $reward_name)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => $reward_name
        ));

        if (isset($data['group']) && !empty($data['group'])){
            $this->mongo_db->where('group', $data['group']);
        }

        $result = $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0]['reward_id'] : array();
    }

    public function getPlayerReward($client_id, $site_id, $pb_player_id, $reward_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('reward_id', 'value'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'reward_id' => $reward_id,
        ));
        $result = $this->mongo_db->get('playbasis_reward_to_player');
        return $result ? $result[0] : array();
    }

    public function getPlayerBadge($client_id, $site_id, $pb_player_id, $badge_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('reward_id', 'value'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'badge_id' => $badge_id,
        ));
        $result = $this->mongo_db->get('playbasis_reward_to_player');
        return $result ? $result[0] : array();
    }

    public function getItemToPlayerRecords($client_id, $site_id, $pb_player_id, $badge_id)
    {
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'pb_player_id' => new MongoId($pb_player_id),
            'badge_id' => new MongoId($badge_id)
        ));

        $result = $this->mongo_db->get('playbasis_reward_to_player');
        return $result ? $result[0] : null;
    }

    public function setPlayerReward($client_id, $site_id, $pb_player_id, $reward_id, $value)
    {
        $d = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'reward_id' => $reward_id,
        ));
        $this->mongo_db->set('value', $value);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update('playbasis_reward_to_player');
    }

    public function setPlayerBadge($client_id, $site_id, $pb_player_id, $badge_id, $value)
    {
        $d = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'badge_id' => $badge_id,
        ));
        $this->mongo_db->set('value', $value);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update('playbasis_reward_to_player');
    }

    public function rewardLog($data, $reward_name, $from = null, $to = null)
    {
        $reward_id = $this->findByName($data, $reward_name);
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), this.value); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('site_id' => $data['site_id'], 'event_type' => 'REWARD', 'reward_id' => $reward_id);
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
            'mapReduce' => 'playbasis_event_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => array('inline' => 1),
        ));
        $result = $result ? $result['results'] : array();
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

    public function badgeLog($data, $badge_id, $from = null, $to = null)
    {
        if (!($badge_id instanceof MongoId)) {
            $badge_id = new MongoId($badge_id);
        }
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), this.value); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('site_id' => $data['site_id'], 'event_type' => 'REWARD', 'item_id' => $badge_id);
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
            'mapReduce' => 'playbasis_event_log',
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

    public function levelupLog($data, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), 1); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('site_id' => $data['site_id'], 'event_type' => 'LEVEL');
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
            'mapReduce' => 'playbasis_event_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => array('inline' => 1),
        ));
        $result = $result ? $result['results'] : array();
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

    public function customLog($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('_id','value'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'pb_player_id' => $data['pb_player_id'],
            'reward_id' => $data['reward_id'],
            'key' => $data['key'],
            'status' => true,
        ));
        $this->mongo_db->order_by(array('date_added' => $data['sort']));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_reward_custom_log');
        return $result ? $result[0] : null;
    }

    public function setCustomLog($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['log_id'],
        ));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('status', $data['status']);
        return $this->mongo_db->update('playbasis_reward_custom_log');
    }

    public function addCustomLog($client_id, $site_id, $cl_player_id, $pb_player_id, $reward_id, $key, $value)
    {
        $this->set_site_mongodb($site_id);

        $insert_data = array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'cl_player_id' => $cl_player_id,
            'pb_player_id' => new MongoId($pb_player_id),
            'reward_id' => new MongoId($reward_id),
            'key' => $key,
            'value' => $value,
            'status' => true,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        return $this->mongo_db->insert('playbasis_reward_custom_log', $insert_data);
    }

    public function remainingPoint($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('name','quantity'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        
        if(isset($data['name'])){
            $this->mongo_db->where('name', $data['name']);
        }
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        return $result;
    }
}

?>