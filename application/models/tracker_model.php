<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Tracker_model extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	public function trackAction($input){
		
		$objectInfo = array(
			'object_id'	=> isset($input['object_id'])? $input['object_id'] : '',
			'website_location'	=> isset($input['url'])? $input['url'] : '',
			'physical_location'	=> isset($input['position'])? $input['position'] : '',
		);

		if(isset($input['object_id']))
			$this->db->set('object_id',$input['object_id']);
		if(isset($input['url']))
			$this->db->set('location',$input['url']);
		if(isset($input['position']))
			$this->db->set('location',$input['position']);


		$this->db->insert('playbasis_action_log',array(	'pb_player_id'=>$input['pb_player_id'],
														'action_id'=>$input['action_id'],
														'action_name'=>$input['action_name'],
														'object_Info'=>serialize($objectInfo),
														'date_added'=>date('Y-m-d H:i:s'),
														'date_modified'=>date('Y-m-d H:i:s')
														)
						);
		
		return $this->db->insert_id();
	}
	
	public function trackEvent($type,$message,$input){
		$this->db->set('pb_player_id',$input['pb_player_id']);
		$this->db->set('event_type',$type);
		if(isset($input['reward_id']))
			$this->db->set('reward_id',$input['reward_id']);
		if(isset($input['reward_name']))
			$this->db->set('reward_name',$input['reward_name']);
		if(isset($input['item_id']))
			$this->db->set('item_id',$input['item_id']);
		$this->db->set('amount',$input['amount']);
		$this->db->set('action_log_id',$input['action_log_id']);
		$this->db->set('message',$message);
		$this->db->set('date_added',date('Y-m-d H:i:s'));
		$this->db->set('date_modified',date('Y-m-d H:i:s'));
		
		$this->db->insert('playbasis_event_log');
	}
}
?>