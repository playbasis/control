<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Player_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
	}
	public function createPlayer($data)
	{
		$inputData = array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'cl_player_id' => $data['player_id'],
			'image' => $data['image'],
			'email' => $data['email'],
			'username' => $data['username'],
			'date_added' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s')
		);
		if(isset($data['first_name']))
			$inputData['first_name'] = $data['first_name'];
		if(isset($data['last_name']))
			$inputData['last_name'] = $data['last_name'];
		if(isset($data['nickname']))
			$inputData['nickname'] = $data['nickname'];
		if(isset($data['facebook_id']))
			$inputData['facebook_id'] = $data['facebook_id'];
		if(isset($data['twitter_id']))
			$inputData['twitter_id'] = $data['twitter_id'];
		if(isset($data['password']))
			$inputData['password'] = $data['password'];
		if(isset($data['gender']))
			$inputData['gender'] = $data['gender'];
		if(isset($data['birth_date']))
			$inputData['birth_date'] = $data['birth_date'];
		$this->db->insert('playbasis_player', $inputData);
		return $this->db->insert_id();
	}
	public function readPlayer($id, $fields)
	{
		if(!$id)
			return array();
		if($fields)
			$this->db->select($fields);
		$this->db->where('pb_player_id', $id);
		$result = $this->db->get('playbasis_player');
		return $result->row_array();
	}
	public function readPlayers($fields, $offset = 0, $limit = 10)
	{
		if($fields)
			$this->db->select($fields);
		$result = $this->db->get('playbasis_player', $offset, $limit);
		return $result->result_array();
	}
	public function updatePlayer($id, $fieldData)
	{
		if(!$id)
			return false;
		$fieldData['date_modified'] = date('Y-m-d H:i:s');
		$this->db->where('pb_player_id', $id);
		$this->db->update('playbasis_player', $fieldData);
		return true;
	}
	public function deletePlayer($id)
	{
		if(!$id)
			return false;
		$this->db->where('pb_player_id', $id);
		$this->db->delete('playbasis_player');
		return true;
	}
	public function getPlaybasisId($clientData)
	{
		if(!$clientData)
			return -1;
		$this->db->where(array(
			'client_id' => $clientData['client_id'],
			'site_id' => $clientData['site_id'],
			'cl_player_id' => $clientData['cl_player_id']
		));
		$this->db->select('pb_player_id');
		$result = $this->db->get('playbasis_player');
		if(!$result->row_array())
			return -1;
		$id = $result->row_array();
		return $id['pb_player_id'];
	}
	public function getClientPlayerId($pb_player_id)
	{
		if(!$pb_player_id)
			return -1;
		$this->db->select('cl_player_id');
		$this->db->where('pb_player_id', $pb_player_id);
		$result = $this->db->get('playbasis_player');
		if(!$result->row_array())
			return -1;
		$id = $result->row_array();
		return $id['cl_player_id'];
	}
	public function getPlayerPoints($data)
	{
		$this->db->select('reward_id,value');
		$this->db->where('pb_player_id', $data['pb_player_id']);
		$result = $this->db->get('playbasis_reward_to_player');
		return $result->result_array();
	}
	public function getPlayerPoint($data)
	{
		$this->db->select('reward_id,value');
		$this->db->where(array(
			'pb_player_id' => $data['pb_player_id'],
			'reward_id' => $data['reward_id']
		));
		$result = $this->db->get('playbasis_reward_to_player');
		return $result->result_array();
	}
	public function getLastActionPerform($data)
	{
		$this->db->select('action_id,action_name,date_added AS time');
		$this->db->where(array(
			'pb_player_id' => $data['pb_player_id']
		));
		$this->db->order_by('date_added', 'DESC');
		$result = $this->db->get('playbasis_action_log');
		return $result->row_array();
	}
	public function getActionPerform($data)
	{
		$this->db->select('action_id,action_name,date_added AS time');
		$this->db->where(array(
			'pb_player_id' => $data['pb_player_id'],
			'action_id' => $data['action_id']
		));
		$this->db->order_by('date_added', 'DESC');
		$result = $this->db->get('playbasis_action_log');
		return $result->row_array();
	}
	public function getActionCount($data)
	{
		$this->db->where(array(
			'pb_player_id' => $data['pb_player_id'],
			'action_id' => $data['action_id']
		));
		$count = $this->db->count_all_results('playbasis_action_log');
		$this->db->select('action_id,action_name');
		$this->db->where(array(
			'pb_player_id' => $data['pb_player_id'],
			'action_id' => $data['action_id']
		));
		$result = $this->db->get('playbasis_action_log');
		$result = $result->row_array();
		$result['count'] = $count;
		return $result;
	}
	public function getBadge($data)
	{
		$this->db->select('badge_id,amount');
		$this->db->where('pb_player_id', $data['pb_player_id']);
		$result = $this->db->get('playbasis_badge_to_player');
		$badges = $result->result_array();
		if(!$badges)
			return array();
		foreach($badges as &$badge)
		{
			//badge data
			$this->db->select('name,description');
			$this->db->where('badge_id', $badge['badge_id']);
			$result = $this->db->get('playbasis_badge_description');
			$badge = array_merge($badge, $result->row_array());
			//badge image
			$this->db->select('image');
			$this->db->where('badge_id', $badge['badge_id']);
			$result = $this->db->get('playbasis_badge');
			$result = $result->row_array();
			$badge['image'] = $this->config->item('IMG_PATH') . $result['image'];
		}
		return $badges;
	}
	public function getLastEventTime($pb_player_id, $eventType)
	{
		$this->db->select('date_added');
		$this->db->where(array(
			'pb_player_id' => $pb_player_id,
			'event_type' => $eventType
		));
		$this->db->order_by('date_added', 'DESC');
		$result = $this->db->get('playbasis_event_log');
		$result = $result->row_array();
		if($result)
			return $result['date_added'];
		return '0000-00-00 00:00:00';
	}
	public function getLeaderboard($ranked_by, $limit, $client_id, $site_id)
	{
		//get reward id
		$this->db->select('reward_id');
		$this->db->where(array(
            'name' => $ranked_by,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
		$result = $this->db->get('playbasis_reward_to_client');
		$result = $result->row_array();
		if(!$result)
			return array();
		//get points for the reward id
		$this->db->select("cl_player_id AS player_id,value AS $ranked_by");
		$this->db->from('playbasis_reward_to_player,playbasis_player');
		$this->db->where(array(
			'reward_id' => $result['reward_id'],
			'playbasis_reward_to_player.client_id' => $client_id,
			'playbasis_reward_to_player.site_id' => $site_id
		));
		$this->db->where('playbasis_reward_to_player.pb_player_id = playbasis_player.pb_player_id');
		$this->db->order_by('value', 'DESC');
		$this->db->limit($limit);
		$result = $this->db->get();
		$result = $result->result_array();
		return $result;
	}
}
?>