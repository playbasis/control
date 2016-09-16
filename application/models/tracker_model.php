<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracker_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function trackAction($input, $action_time = null)
    {
        $this->set_site_mongodb($input['site_id']);
        $current_time = time();
        if ($action_time && $action_time > $current_time) {
            $action_time = $current_time;
        } // cannot be something from the future
        $mongoDate = new MongoDate($action_time ? $action_time : $current_time);
        $d = strtotime(date('Y-m-d', $mongoDate->sec));
        //$this->computeDau($input, $d);
        //$this->updateLatestProcessActionLogTime($mongoDate);
        //$this->computeMau($input, $d);
        $action_log_id = $this->mongo_db->insert('playbasis_action_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'pb_player_id-2' => isset($input['pb_player_id-2']) ? $input['pb_player_id-2'] : null,
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'action_id' => $input['action_id'],
            'action_name' => $input['action_name'],
            'url' => (isset($input['url'])) ? $input['url'] : null,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ));
        $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'pb_player_id-2' => isset($input['pb_player_id-2']) ? $input['pb_player_id-2'] : null,
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'event_type' => 'ACTION',
            'action_log_id' => $action_log_id,
            'action_id' => $input['action_id'],
            'action_name' => $input['action_name'],
            'url' => (isset($input['url'])) ? $input['url'] : null,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), array("w" => 0, "j" => false));
        return $action_log_id;
    }

    public function trackEvent($type, $message, $input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $options = $async ? array("w" => 0, "j" => false) : array();
        $_id = $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'event_type' => $type,
            'action_log_id' => (isset($input['action_log_id'])) ? $input['action_log_id'] : null,
            'message' => $message,
            'reward_id' => (isset($input['reward_id'])) ? $input['reward_id'] : null,
            'reward_name' => (isset($input['reward_name'])) ? $input['reward_name'] : null,
            'item_id' => (isset($input['item_id'])) ? $input['item_id'] : null,
            'value' => (isset($input['amount'])) ? intval($input['amount']) : null,
            'objective_id' => (isset($input['objective_id'])) ? $input['objective_id'] : null,
            'objective_name' => (isset($input['objective_name'])) ? $input['objective_name'] : null,
            'goods_id' => (isset($input['goods_id'])) ? $input['goods_id'] : null,
            'goods_name' => (isset($input['goods_name'])) ? $input['goods_name'] : null,
            'goods_log_id' => (isset($input['goods_log_id'])) ? $input['goods_log_id'] : null,
            'quest_id' => (isset($input['quest_id'])) ? $input['quest_id'] : null,
            'mission_id' => (isset($input['mission_id'])) ? $input['mission_id'] : null,
            'quiz_id' => (isset($input['quiz_id'])) ? $input['quiz_id'] : null,
            'leaderboard_id' => (isset($input['leaderboard_id'])) ? $input['leaderboard_id'] : null,
            'node_id' => (isset($input['node_id'])) ? $input['node_id'] : null,
            'sender' => (isset($input['sent_pb_player_id'])) ? $input['sent_pb_player_id'] : null,
            'gift_log_id' => (isset($input['gift_log_id'])) ? $input['gift_log_id'] : null,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), $options);
        return $async ? null : $_id;
    }

    public function trackSocial($input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $options = $async ? array("w" => 0, "j" => false) : array();
        $_id = $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'event_type' => 'SOCIAL',
            'event_id' => $input['event_id'],
            'from_pb_player_id' => $input['from_pb_player_id'],
            'action_name' => (isset($input['action_name'])) ? $input['action_name'] : null,
            'message' => (isset($input['message'])) ? $input['message'] : null,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), $options);
        return $async ? null : $_id;
    }

    public function trackGoods($input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $goods_log_id = $this->mongo_db->insert('playbasis_goods_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'goods_id' => $input['goods_id'],
            'goods_name' => $input['goods_name'],
            'is_sponsor' => (isset($input['is_sponsor'])) ? $input['is_sponsor'] : false,
            'redeem' => $input['redeem'],
            'amount' => $input['amount'],
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ));
        return $this->trackEvent('REDEEM', $input['message'],
            array_merge($input, array('goods_log_id' => $goods_log_id)), $async);
    }

    public function trackBadge($input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $options = $async ? array("w" => 0, "j" => false) : array();
        $_id = $this->mongo_db->insert('playbasis_badges_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'badge_id' => $input['badge_id'],
            'type' => $input['type'],
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), $options);
        return $async ? null : $_id;
    }

    public function trackGift($input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $options = $async ? array("w" => 0, "j" => false) : array();
        $id = $this->mongo_db->insert('playbasis_gift_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'gift_type' => $input['reward_type'],
            'gift_id' => $input['reward_id'],
            'gift_name' => $input['reward_name'],
            'gift_value' => $input['amount'],
            'sender' => $input['sent_pb_player_id'],
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), $options);

        $input['gift_log_id'] = $id;
        if ($input['reward_type'] == 'BADGE') {
            $input['reward_name'] = 'badge';
            $input['item_id'] = $input['reward_id'];
            $input['reward_id'] = $this->get_reward_id_by_name(array(
                'client_id' => $input['client_id'],
                'site_id' => $input['site_id']
            ), 'badge');
        } elseif($input['reward_type'] == "GOODS"){
            $input['goods_id'] = $input['reward_id'];
            unset($input['reward_id']);
        }
        return $this->trackEvent('REWARD', $input['message'], $input, $async);
    }

    public function trackQuest($input, $async = true)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate();
        $options = $async ? array("w" => 0, "j" => false) : array();
        $this->mongo_db->insert('playbasis_quest_reward_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'quest_id' => $input['quest_id'],
            'mission_id' => $input['mission_id'],
            'reward_type' => $input['reward_type'],
            'reward_id' => $input['reward_id'],
            'reward_name' => $input['reward_name'],
            'reward_value' => $input['amount'],
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), $options);

        if ($input['reward_type'] == 'BADGE') {
            $input['reward_name'] = 'badge';
            $input['item_id'] = $input['reward_id'];
            $input['reward_id'] = $this->get_reward_id_by_name(array(
                'client_id' => $input['client_id'],
                'site_id' => $input['site_id']
            ), 'badge');
        } // reward_name should be "badge" in playbasis_event_log
        return $this->trackEvent('REWARD', $input['message'], $input, $async);
    }

    /* copied from player_model as model cannot call each other */
    public function get_reward_id_by_name($data, $name)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id'], 'name' => $name);
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where($query);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_reward_to_client');
        return $results ? $results[0]['reward_id'] : null;
    }

    /* copied from player_model as model cannot call each other */
    public function updateLatestProcessActionLogTime($d)
    {
        $this->mongo_db->limit(1);
        $r = $this->mongo_db->get('playbasis_player_dau_latest');
        if ($r) {
            $r = $r[0];
            if ($d->sec < $r['date_added']->sec) {
                return false;
            }
            $this->mongo_db->where(array('_id' => $r['_id']));
            $this->mongo_db->set('date_added', $d);
            $this->mongo_db->update('playbasis_player_dau_latest', array("w" => 0, "j" => false));
        } else {
            $this->mongo_db->insert('playbasis_player_dau_latest', array('date_added' => $d),
                array("w" => 0, "j" => false));
        }
        return true;
    }

    /* copied from player_model as model cannot call each other */
    public function computeDau($action, $d)
    {
        $this->mongo_db->select(array());
        $this->mongo_db->where(array(
            'pb_player_id' => $action['pb_player_id'],
            'client_id' => $action['client_id'],
            'site_id' => $action['site_id'],
            'action_id' => $action['action_id'],
            'date_added' => new MongoDate($d)
        ));
        $this->mongo_db->limit(1);
        $r = $this->mongo_db->get('playbasis_player_dau');
        if ($r) {
            $r = $r[0];
            $this->mongo_db->where(array('_id' => $r['_id']));
            $this->mongo_db->inc('count', 1);
            $this->mongo_db->update('playbasis_player_dau', array("w" => 0, "j" => false));
        } else {
            $this->mongo_db->insert('playbasis_player_dau', array(
                'pb_player_id' => $action['pb_player_id'],
                'client_id' => $action['client_id'],
                'site_id' => $action['site_id'],
                'action_id' => $action['action_id'],
                'count' => 1,
                'date_added' => new MongoDate($d)
            ), array("w" => 0, "j" => false));
        }
    }

    /* copied from player_model as model cannot call each other */
    public function computeMau($action, $d)
    {
        $data = array();
        $end = strtotime(date('Y-m-d', strtotime('+30 day', $d)));
        $cur = $d;
        while ($cur != $end) {
            $data[] = array(
                'pb_player_id' => $action['pb_player_id'],
                'client_id' => $action['client_id'],
                'site_id' => $action['site_id'],
                'date_added' => new MongoDate($cur)
            );
            $cur = strtotime(date('Y-m-d', strtotime('+1 day', $cur)));
        }
        return $this->mongo_db->batch_insert('playbasis_player_mau', $data, array("w" => 0, "j" => false, "continueOnError" => true));
    }

    public function trackValidatedAction($input, $action_time = null)
    {
        $this->set_site_mongodb($input['site_id']);
        $current_time = time();
        if ($action_time && $action_time > $current_time) {
            $action_time = $current_time;
        } // cannot be something from the future
        $mongoDate = new MongoDate($action_time ? $action_time : $current_time);

        if (is_array($input['parameters'])) {
            foreach ($input['parameters'] as $name => $value) {
                if (is_numeric($value)) {
                    $input['parameters'][$name . POSTFIX_NUMERIC_PARAM] = floatval($value);
                }
            }
        }
        $this->mongo_db->insert('playbasis_validated_action_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'pb_player_id-2' => isset($input['pb_player_id-2']) ? $input['pb_player_id-2'] : null,
            'cl_player_id' => $input['player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'action_id' => $input['action_id'],
            'action_name' => $input['action_name'],
            'node_id' => (isset($input['node_id'])) ? $input['node_id'] : null,
            'url' => (isset($input['url'])) ? $input['url'] : null,
            'parameters' => (isset($input['parameters'])) ? $input['parameters'] : null,
            'action_log_id' => (isset($input['action_log_id'])) ? $input['action_log_id'] : null,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), array("w" => 0, "j" => false));
    }

    public function trackQuiz($input, $action_time = null)
    {
        $this->set_site_mongodb($input['site_id']);
        $current_time = time();
        if ($action_time && $action_time > $current_time) {
            $action_time = $current_time;
        } // cannot be something from the future
        $mongoDate = new MongoDate($action_time ? $action_time : $current_time);

        $this->mongo_db->insert('playbasis_quiz_log', array(
            'pb_player_id' => $input['pb_player_id'],
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'quiz_id' => $input['quiz_id'],
            'quiz_name' => $input['quiz_name'],
            'question' => (isset($input['question'])) ? $input['question'] : null,
            'option' => (isset($input['option'])) ? $input['option'] : null,
            'grade' => (isset($input['grade'])) ? $input['grade'] : null,
            'quiz_completed' => (isset($input['quiz_completed'])) ? $input['quiz_completed'] : false,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ), array("w" => 0, "j" => false));
    }
}

?>
