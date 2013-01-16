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


		$this->db->insert('playbasis_action_log',array('pb_player_id'=>$input['pb_player_id'],'action_id'=>$input['action_id'],'object_Info'=>serialize($objectInfo),'date_added'=>date('Y-m-d H:i:s'),'date_modified'=>date('Y-m-d H:i:s')));
	}
}
?>