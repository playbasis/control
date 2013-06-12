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
		if(isset($input['url']))
			$this->site_db()->set('url', $input['url']);
		$this->mongo_db->insert('action_log', array(
			'action_log_id' => $action_log_id,
			'pb_player_id' => $input['pb_player_id'],
			'client_id' => $input['client_id'],
			'site_id' => $input['site_id'],
			'action_id' => $input['action_id'],
			'action_name' => $input['action_name'],
			'date_added' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		));
		return $action_log_id;
	}
	public function trackEvent($type, $message, $input)
	{
		$this->set_site_mongodb($input['site_id']);
		$event_log_id = $this->generate_id_mongodb('event_log');
		$data = array(
			'event_log_id' => $event_log_id,
			'pb_player_id' => $input['pb_player_id'],
			'client_id' => $input['client_id'],
			'site_id' => $input['site_id'],
			'event_type' => $type,
			'action_log_id' => $input['action_log_id'],
			'message' => $message,
			'date_added' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		);
		if(isset($input['reward_id']))
			$data['reward_id'] = $input['reward_id'];
		if(isset($input['reward_name']))
			$data['reward_name'] = $input['reward_name'];
		if(isset($input['item_id']))
			$data['item_id'] = $input['item_id'];
		if(isset($input['amount']))
			$data['value'] = $input['amount'];		
		$this->mongo_db->insert('event_log', $data);
	}
}
?>