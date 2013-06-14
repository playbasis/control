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
		$action_log_id = $this->generate_id_mongodb('action_log');
		$this->mongo_db->insert('action_log', array(
			'action_log_id' => new MongoInt64("$action_log_id"),
			'pb_player_id'	=> new MongoInt64((string)$input['pb_player_id']),
			'client_id'		=> intval($input['client_id']),
			'site_id'		=> intval($input['site_id']),
			'action_id'		=> intval($input['action_id']),
			'action_name'	=> $input['action_name'],
			'url'			=> (isset($input['url'])) ? $input['url'] : '',
			'date_added'	=> date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		));
		return $action_log_id;
	}
	public function trackEvent($type, $message, $input)
	{
		$this->set_site_mongodb($input['site_id']);
		$event_log_id = $this->generate_id_mongodb('event_log');
		$this->mongo_db->insert('event_log', array(
			'event_log_id'	=> new MongoInt64("$event_log_id"),
			'pb_player_id'	=> new MongoInt64((string)$input['pb_player_id']),
			'client_id'		=> intval($input['client_id']),
			'site_id'		=> intval($input['site_id']),
			'event_type'	=> $type,
			'action_log_id' => new MongoInt64((string)$input['action_log_id']),
			'message'		=> $message,
			'reward_id'		=> (isset($input['reward_id']))	  ? intval($input['reward_id']) : 0,
			'reward_name'	=> (isset($input['reward_name'])) ? $input['reward_name']		: '',
			'item_id'		=> (isset($input['item_id']))	  ? intval($input['item_id'])	: 0,
			'value'			=> (isset($input['amount']))	  ? intval($input['amount'])	: 0,
			'date_added'	=> date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		));
	}
}
?>