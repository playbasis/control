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
			'action_log_id' => $action_log_id,
			'pb_player_id' => $input['pb_player_id'],
			'client_id' => $input['client_id'],
			'site_id' => $input['site_id'],
			'action_id' => $input['action_id'],
			'action_name' => $input['action_name'],
			'url' => (isset($input['url'])) ? $input['url'] : '',
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
		$data['reward_id'] = (isset($input['reward_id'])) ? $input['reward_id'] : '';
		$data['reward_name'] = (isset($input['reward_name'])) ? $input['reward_name'] : '';
		$data['item_id'] = (isset($input['item_id'])) ? $input['item_id'] : '';
		$data['value'] = (isset($input['amount'])) ? $input['amount'] : '';
		$this->mongo_db->insert('event_log', $data);
	}
}
?>