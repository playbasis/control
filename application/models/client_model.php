<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Client_model extends CI_Model{
	//get action configuration from all rule that relate to site id & client id
	public function getRuleSet($clientData){
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		
		$this->db->select('jigsaw_set');
		$this->db->where($clientData);
		
		$result = $this->db->get('playbasis_rule');
		
		return $result->result_array();
	}
	
	//get action id use action name
	public function getActionId($clientData){
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		assert(isset($clientData['action_name']));
		
		$this->db->select('action_id');
		$this->db->where(array('client_id'=>$clientData['client_id'],'site_id'=>$clientData['site_id'],'name'=>$clientData['action_name']));
		
		$result = $this->db->get('playbasis_action_to_client');
		
		assert(count($result) == 1);
		$id = $result->row_array();
		
		return $id['action_id'];
	}
	
	//get action configuration from all rule that relate to site id & client id
	public function getRuleSetByActionId($clientData){
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		assert(isset($clientData['action_id']));
		
		$this->db->select('rule_id,jigsaw_set');
		$this->db->where($clientData);
		
		$result = $this->db->get('playbasis_rule');
		
		return $result->result_array();
	}
	
	//get class path relate to jigsaw
	public function getJigsawProcessor($jigsawId){
		assert($jigsawId);
		
		$this->db->where(array('jigsaw_id'=>$jigsawId));
		$this->db->select('class_path');
		
		$result = $this->db->get('playbasis_game_jigsaw');
		
		$jigsawProcessor = $result->row_array();
		
		return $jigsawProcessor['class_path'];
	}
	
	//update point reward
	public function updatePlayerpointReward($rewardId,$quantity,$pbPlayerId){
		assert(isset($rewardId));
		assert(isset($quantity));
		assert(isset($pbPlayerId));
		
		$this->db->where(array('pb_player_id'=>$pbPlayerId,'reward_id'=>$rewardId));
		$this->db->set('value',"value+$quantity",FALSE);
		$this->db->update('playbasis_reward_to_player');
	}
	
	public function updateplayerBadge($badgeId,$quantity,$pbPlayerId){
		assert(isset($badgeId));
		assert(isset($quantity));
		assert(isset($pbPlayerId));
		
		$this->db->where(array('pb_player_id'=>$pbPlayerId,'badge_id'=>$badgeId));
		$this->db->from('playbasis_badge');
		$hasBadge = $this->db->count_all_results();
		
		if($hasBadge){
			$this->db->where(array('pb_player_id'=>$pbPlayerId,'badge_id'=>$badgeId));
			$this->db->set('amount',"amount+$quantity",FALSE);
			$this->db->update('playbasis_badge');
		}
		else{
			$this->db->insert('playbasis_badge',array('pb_player_id'=>$pbPlayerId,'badge_id'=>$badgeId,'amount'=>$quantity,'date_added'=>date('Y-m-d H:i:s')));
		}
	}

	public function updateExpAndLevel($exp,$pb_player_id){
		assert($exp);
		assert($pb_player_id);

		//get level
		$this->db->select_max('level');
		$this->db->where("exp <=",$exp);
		$result = $this->db->get('playbasis_exp_table');

		$level = -1;
		if($result->num_rows()){
			$level = $result->row_array();
			$level = $level['level'];
		}
		
		$this->db->where('pb_player_id',$pb_player_id);
		$this->db->set('exp', "exp+$exp", FALSE);
		if($level>0)
			$this->db->set('level', $level['level']);
		
		$this->db->update('playbasis_player');

		return $level;			
	}

	public function log($logData){
		assert($logData);
		assert(is_array($logData));
		assert($logData['pb_player_id']);
		assert($logData['action_id']);
		assert($logData['client_id']);
		assert($logData['site_id']);
		assert($logData['domain_name']);
	} 
}
?>