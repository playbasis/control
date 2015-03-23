<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tracker_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function trackAction($input, $action_time=null)
	{
        $this->set_site_mongodb($input['site_id']);
        $current_time = time();
        if ($action_time && $action_time > $current_time) $action_time = $current_time; // cannot be something from the future
        $mongoDate = new MongoDate($action_time ? $action_time : $current_time);
        try {
            $d = strtotime(date('Y-m-d', $mongoDate->sec));
            /* insert into playbasis_player_dau */
            $this->mongo_db->insert('playbasis_player_dau', array(
                'pb_player_id'	=> $input['pb_player_id'],
                'client_id'		=> $input['client_id'],
                'site_id'		=> $input['site_id'],
                'date_added'	=> new MongoDate($d)
            ), array("w" => 0, "j" => false));
            /* insert into playbasis_player_mau */
            $curr = strtotime(date('Y-m-d', strtotime('+30 day', $d)));
            while ($curr != $d) {
                $curr = strtotime(date('Y-m-d', strtotime('-1 day', $curr)));
                $this->mongo_db->insert('playbasis_player_mau', array(
                    'pb_player_id'	=> $input['pb_player_id'],
                    'client_id'		=> $input['client_id'],
                    'site_id'		=> $input['site_id'],
                    'date_added'	=> new MongoDate($curr)
                ), array("w" => 0, "j" => false));
            }
            $this->mongo_db->insert('playbasis_player_mau', array(
                'pb_player_id'	=> $input['pb_player_id'],
                'client_id'		=> $input['client_id'],
                'site_id'		=> $input['site_id'],
                'date_added'	=> new MongoDate($d)
            ), array("w" => 0, "j" => false));
        } catch(Exception $e) {
            /* duplicate entries detected by MongoDB unique index, break early */
        }
        return $this->mongo_db->insert('playbasis_action_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'action_id'		=> $input['action_id'],
            'action_name'	=> $input['action_name'],
            'url'			=> (isset($input['url'])) ? $input['url'] : null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
    public function trackEvent($type, $message, $input)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'event_type'	=> $type,
            'action_log_id' => (isset($input['action_log_id']))	? $input['action_log_id']	: null,
            'message'		=> $message,
            'reward_id'		=> (isset($input['reward_id']))		? $input['reward_id']		: null,
            'reward_name'	=> (isset($input['reward_name']))	? $input['reward_name']		: null,
            'item_id'		=> (isset($input['item_id']))		? $input['item_id']			: null,
            'value'			=> (isset($input['amount']))		? intval($input['amount'])	: null,
            'objective_id'	=> (isset($input['objective_id']))	? $input['objective_id']	: null,
            'objective_name'=> (isset($input['objective_name']))? $input['objective_name']	: null,
            'goods_id'		=> (isset($input['goods_id']))		? $input['goods_id']		: null,
            'goods_name'	=> (isset($input['goods_name']))	? $input['goods_name']		: null,
            'goods_log_id'  => (isset($input['goods_log_id']))	? $input['goods_log_id']	: null,
            'quest_id'      => (isset($input['quest_id']))	? $input['quest_id']	: null,
            'mission_id'    => (isset($input['mission_id']))	? $input['mission_id']	: null,
            'quiz_id'       => (isset($input['quiz_id']))	? $input['quiz_id']	: null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
    public function trackGoods($input)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
        $goods_log_id = $this->mongo_db->insert('playbasis_goods_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'goods_id'		=> $input['goods_id'],
            'goods_name'	=> $input['goods_name'],
            'redeem'        => $input['redeem'],
            'amount'        => $input['amount'],
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));

        return $this->trackEvent('REDEEM', $input['message'], array_merge($input, array('goods_log_id' => $goods_log_id)));
    }
    public function trackBadge($input)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_badges_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'badge_id'		=> $input['badge_id'],
            'type'	        => $input['type'],
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
    public function trackQuest($input)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
        $this->mongo_db->insert('playbasis_quest_reward_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'quest_id'		=> $input['quest_id'],
            'mission_id'	=> $input['mission_id'],
            'reward_type'	=> $input['reward_type'],
            'reward_id'	    => $input['reward_id'],
            'reward_name'	=> $input['reward_name'],
            'reward_value'	=> $input['amount'],
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));

        return $this->trackEvent('REWARD', $input['message'], $input);
    }
}
?>
