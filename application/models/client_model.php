<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define("CUSTOM_POINT_START_ID", 10000);
class Client_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
        $this->load->library('memcached_library');
		$this->load->helper('memcache');
	}
	public function getRuleSet($clientData)
	{
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		$this->set_site($clientData['site_id']);
		$this->site_db()->select('jigsaw_set');
		$this->site_db()->where($clientData);
		return db_get_result_array($this, 'playbasis_rule');
	}
	public function getActionId($clientData)
	{
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		assert(isset($clientData['action_name']));
		$this->set_site($clientData['site_id']);
		$this->site_db()->select('action_id');
		$this->site_db()->where(array(
			'client_id' => $clientData['client_id'],
			'site_id' => $clientData['site_id'],
			'name' => $clientData['action_name']
		));
		$id = db_get_row_array($this, 'playbasis_action_to_client');
		return ($id) ? $id['action_id'] : 0;
	}
	public function getRuleSetByActionId($clientData)
	{
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		assert(isset($clientData['action_id']));
		$clientData['active_status'] = '1';
		$this->set_site($clientData['site_id']);
		$this->site_db()->select('rule_id,name,jigsaw_set');
		$this->site_db()->where($clientData);
		return db_get_result_array($this, 'playbasis_rule');
	}
	public function getJigsawProcessor($jigsawId, $site_id)
	{
		assert($jigsawId);
		$this->set_site($site_id);
		$this->site_db()->where(array(
			'jigsaw_id' => $jigsawId
		));
		$this->site_db()->select('class_path');
		$jigsawProcessor = db_get_row_array($this, 'playbasis_game_jigsaw_to_client');
		return $jigsawProcessor['class_path'];
	}
	public function updatePlayerPointReward($rewardId, $quantity, $pbPlayerId, $clPlayerId, $clientId, $siteId, $overrideOldValue = FALSE)
	{
		assert(isset($rewardId));
		assert(isset($siteId));
		assert(isset($quantity));
		assert(isset($pbPlayerId));
		$this->set_site_mongodb($siteId);
		$this->mongo_db->where(array(
			'pb_player_id' => $pbPlayerId,
			'reward_id' => $rewardId
		));
		$hasReward = $this->mongo_db->count('reward_to_player');
		if($hasReward)
		{
			$this->mongo_db->where(array(
				'pb_player_id' => $pbPlayerId,
				'reward_id' => $rewardId
			));
			$this->mongo_db->set('date_modified', date('Y-m-d H:i:s'));
			if($overrideOldValue)
				$this->mongo_db->set('value', $quantity);
			else
				$this->mongo_db->inc('value', intval($quantity));
			$this->mongo_db->update('reward_to_player');
		}
		else
		{
			$this->mongo_db->insert('reward_to_player', array(
				'pb_player_id' => $pbPlayerId,
				'cl_player_id' => $clPlayerId,
				'client_id' => $clientId,
				'site_id' => $siteId,
				'reward_id' => $rewardId,
				'value' => intval($quantity),
				'date_added' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s')
			));
		}
		//upadte client reward limit
		$this->site_db()->select('limit');
		$this->site_db()->where(array(
			'reward_id' => $rewardId,
			'site_id' => $siteId
		));
		$result = $this->site_db()->get('playbasis_reward_to_client');
		assert($result->row_array());
		$result = $result->row_array();
		if(!is_null($result['limit']))
		{
			$this->site_db()->where(array(
				'reward_id' => $rewardId,
				'site_id' => $siteId
			));
			$this->site_db()->set('limit', "`limit`-$quantity", FALSE);
			$this->site_db()->update('playbasis_reward_to_client');
			$this->memcached_library->update_delete('playbasis_reward_to_client');
		}
	}
	public function updateCustomReward($rewardName, $quantity, $input, &$jigsawConfig)
	{
		//get reward id
		$this->set_site($input['site_id']);
		$this->site_db()->select('reward_id');
		$this->site_db()->where(array(
			'client_id' => $input['client_id'],
			'site_id' => $input['site_id'],
			'name' => strtolower($rewardName)
		));
		$this->site_db()->from('playbasis_reward_to_client');
		$result = $this->site_db()->get();
		$result = $result->row_array();
		$customRewardId = isset($result['reward_id']) ? $result['reward_id'] : false;
		if(!$customRewardId)
		{
			//reward does not exist, add new custom point where id is max(reward_id)+1
			$this->site_db()->select_max('reward_id');
			$this->site_db()->where(array(
				'client_id' => $input['client_id'],
				'site_id' => $input['site_id']
			));
			$result = $this->site_db()->get('playbasis_reward_to_client');
			$result = $result->row_array();
			$customRewardId = $result['reward_id'] + 1;
			if($customRewardId < CUSTOM_POINT_START_ID)
				$customRewardId = CUSTOM_POINT_START_ID;
			$this->site_db()->insert('playbasis_reward_to_client', array(
				'reward_id' => $customRewardId,
				'client_id' => $input['client_id'],
				'site_id' => $input['site_id'],
				'group' => 'POINT',
				'name' => strtolower($rewardName),
				'date_added' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s')
			));
			$this->memcached_library->update_delete('playbasis_reward_to_client');
		}
		//update player reward
		$this->updatePlayerPointReward($customRewardId, $quantity, $input['pb_player_id'], $input['player_id'], $input['client_id'], $input['site_id']);
		$jigsawConfig['reward_id'] = $customRewardId;
		$jigsawConfig['reward_name'] = $rewardName;
		$jigsawConfig['quantity'] = $quantity;
	}
	public function updateplayerBadge($badgeId, $quantity, $pbPlayerId, $site_id)
	{
		assert(isset($badgeId));
		assert(isset($quantity));
		assert(isset($pbPlayerId));
		//update badge master table
		$this->set_site($site_id);
		$this->site_db()->select('substract,quantity');
		$this->site_db()->where(array(
			'badge_id' => $badgeId
		));
		$result = $this->site_db()->get('playbasis_badge');
		$badgeInfo = $result->row_array();
		if($badgeInfo['substract'])
		{
			$remainingQuantity = $badgeInfo['quantity'] - $quantity;
			if($remainingQuantity < 0)
			{
				$remainingQuantity = 0;
				$quantity = $badgeInfo['quantity'];
			}
			$this->site_db()->set('quantity', $remainingQuantity);
			$this->site_db()->set('date_modified', date('Y-m-d H:i:s'));
			$this->site_db()->where('badge_id', $badgeId);
			$this->site_db()->update('playbasis_badge');
			$this->memcached_library->update_delete('playbasis_badge');
		}
		//update player badge table
		$this->site_db()->where(array(
			'pb_player_id' => $pbPlayerId,
			'badge_id' => $badgeId
		));
		$this->site_db()->from('playbasis_badge_to_player');
		$hasBadge = $this->site_db()->count_all_results();
		if($hasBadge)
		{
			$this->site_db()->where(array(
				'pb_player_id' => $pbPlayerId,
				'badge_id' => $badgeId
			));
			$this->site_db()->set('date_modified', date('Y-m-d H:i:s'));
			$this->site_db()->set('amount', "`amount`+$quantity", FALSE);
			$this->site_db()->update('playbasis_badge_to_player');
		}
		else
		{
			$this->site_db()->insert('playbasis_badge_to_player', array(
				'pb_player_id' => $pbPlayerId,
				'badge_id' => $badgeId,
				'amount' => $quantity,
				'date_added' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s')
			));
		}
		$this->memcached_library->update_delete('playbasis_badge_to_player');
	}
	public function updateExpAndLevel($exp, $pb_player_id, $cl_player_id, $clientData)
	{
		assert($exp);
		assert($pb_player_id);
		assert($clientData);
		assert(is_array($clientData));
		assert(isset($clientData['client_id']));
		assert(isset($clientData['site_id']));
		//get player exp
		$this->set_site($clientData['site_id']);
		$this->site_db()->select('exp,level');
		$this->site_db()->where('pb_player_id', $pb_player_id);
		$result = db_get_row_array($this, 'playbasis_player');
		$playerExp = $result['exp'];
		$playerLevel = $result['level'];
		$newExp = $exp + $playerExp;
		//check if client have their own exp table setup
		$this->site_db()->select_max('level');
		$this->site_db()->where($clientData);
		$this->site_db()->where("exp <=", $newExp);
		$level = db_get_row_array($this, 'playbasis_client_exp_table');
		if(!$level['level'])
		{
			//get level from default exp table instead
			$this->site_db()->select_max('level');
			$this->site_db()->where("exp <=", $newExp);
			$level = db_get_row_array($this, 'playbasis_exp_table');
		}
		if($level['level'] && $level['level'] > $playerLevel)
			$level = $level['level'];
		else
			$level = -1;
		$this->site_db()->where('pb_player_id', $pb_player_id);
		$this->site_db()->set('date_modified', date('Y-m-d H:i:s'));
		$this->site_db()->set('exp', "`exp`+$exp", FALSE);
		if($level > 0)
			$this->site_db()->set('level', $level);
		$this->site_db()->update('playbasis_player');
		$this->memcached_library->update_delete('playbasis_player');
		//get reward id to update the reward to player table
		$this->site_db()->select('reward_id');
		$this->site_db()->where('name', 'exp');
		$result = db_get_row_array($this, 'playbasis_reward');
		$this->updatePlayerPointReward($result['reward_id'], $newExp, $pb_player_id, $cl_player_id, $clientData['client_id'], $clientData['site_id'], TRUE);
		return $level;
	}
	public function log($logData, $jigsawOptionData = array())
	{
		assert($logData);
		assert(is_array($logData));
		assert($logData['pb_player_id']);
		assert($logData['action_id']);
		assert('$logData["action_name"]');
		assert($logData['client_id']);
		assert($logData['site_id']);
		assert('$logData["domain_name"]');
		if(isset($logData['input']))
			$logData['input'] = serialize(array_merge($logData['input'], $jigsawOptionData));
		else
			$logData['input'] = 'NO-INPUT';
		$data = array();
		static $required = array('pb_player_id', 'input', 'client_id', 'site_id', 'domain_name');
		static $optional = array('action_id', 'action_name', 'rule_id', 'rule_name', 'jigsaw_id', 'jigsaw_name', 'jigsaw_category', 'site_name', 'ip_address', 'user_agent');
		foreach($required as $field)
		{
			$data[$field] = $logData[$field];
		}
		foreach($optional as $field)
		{
			if(isset($logData[$field]))
				$data[$field] = $logData[$field];
		}
		$data['date_added'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
		$this->set_site_mongodb($logData['site_id']);
		$this->mongo_db->insert('jigsaw_log', $data);
	}
	public function getBadgeById($badgeId, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('badge_id,image');
		$this->site_db()->where('badge_id', $badgeId);
		$badgeImage = db_get_row_array($this, 'playbasis_badge');
		$badgeImage['image'] = $this->config->item('IMG_PATH') . $badgeImage['image'];
		$this->site_db()->select('name,description');
		$this->site_db()->where('badge_id', $badgeId);
		$badgeDesc = db_get_row_array($this, 'playbasis_badge_description');
		$badge = array_merge($badgeImage, $badgeDesc);
		return $badge;
	}
}
?>