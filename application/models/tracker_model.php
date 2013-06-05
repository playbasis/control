<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tracker_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
	}
	public function trackAction($input)
	{
		$this->set_site($input['site_id']);
		if(isset($input['url']))
			$this->site_db()->set('url', $input['url']);
		$this->site_db()->insert('playbasis_action_log', array(
			'pb_player_id' => $input['pb_player_id'],
			'client_id' => $input['client_id'],
			'site_id' => $input['site_id'],
			'action_id' => $input['action_id'],
			'action_name' => $input['action_name'],
			'date_added' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		));
		$this->memcached_library->update_delete('playbasis_action_log');
		return $this->site_db()->insert_id();
	}
	public function trackEvent($type, $message, $input)
	{
		$this->set_site($input['site_id']);
		$this->site_db()->set('pb_player_id', $input['pb_player_id']);
		$this->site_db()->set('client_id', $input['client_id']);
		$this->site_db()->set('site_id', $input['site_id']);
		$this->site_db()->set('event_type', $type);
		if(isset($input['reward_id']))
			$this->site_db()->set('reward_id', $input['reward_id']);
		if(isset($input['reward_name']))
			$this->site_db()->set('reward_name', $input['reward_name']);
		if(isset($input['item_id']))
			$this->site_db()->set('item_id', $input['item_id']);
		if(isset($input['amount']))
			$this->site_db()->set('value', $input['amount']);
		$this->site_db()->set('action_log_id', $input['action_log_id']);
		$this->site_db()->set('message', $message);
		$this->site_db()->set('date_added', date('Y-m-d H:i:s'));
		$this->site_db()->set('date_modified', date('Y-m-d H:i:s'));
		$this->site_db()->insert('playbasis_event_log');
		$this->memcached_library->update_delete('playbasis_event_log');
	}
}
?>