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
}

?>