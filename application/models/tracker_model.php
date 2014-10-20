<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tracker_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->library('mongo_db');
	}
	public function trackAction($input)
	{
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
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
            'action_log_id' => $input['action_log_id'],
            'message'		=> $message,
            'reward_id'		=> (isset($input['reward_id']))		? $input['reward_id']		: null,
            'reward_name'	=> (isset($input['reward_name']))	? $input['reward_name']		: null,
            'item_id'		=> (isset($input['item_id']))		? $input['item_id']			: null,
            'value'			=> (isset($input['amount']))		? intval($input['amount'])	: null,
            'objective_id'	=> (isset($input['objective_id']))	? $input['objective_id']	: null,
            'objective_name'=> (isset($input['objective_name']))? $input['objective_name']	: null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
    public function trackGoods($input)
    {
        $this->set_site_mongodb($input['site_id']);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_goods_log', array(
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
            'reward_value'	=> $input['reward_value'],
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));

        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> $input['pb_player_id'],
            'client_id'		=> $input['client_id'],
            'site_id'		=> $input['site_id'],
            'event_type'	=> 'REWARD',
            'action_log_id' => null,
            'message'		=> $input['message'],
            'reward_id'		=> (isset($input['reward_id']))	? $input['reward_id'] : null,
            'reward_name'	=> (isset($input['reward_name'])) ? $input['reward_name'] : null,
            'item_id'		=> null,
            'value'			=> intval($input['reward_value']),
            'objective_id'	=> null,
            'objective_name'=> null,
            'quest_id'		=> $input['quest_id'],
            'mission_id'	=> $input['mission_id'],
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
}
?>
