<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Player_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
        $this->load->library('memcached_library');
		$this->load->helper('memcache');
		$this->load->library('mongo_db');
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
		$pb_player_id = $this->generate_id_mongodb('player');
		$inputData['pb_player_id'] = $pb_player_id;
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->insert('player', $inputData);
		return $pb_player_id;
	}
	public function readPlayer($id, $site_id, $fields)
	{
        if(!$id)
            return array();
		$this->set_site_mongodb($site_id);
		if($fields)
			$this->mongo_db->select($fields);
		$this->mongo_db->where('pb_player_id', $id);
		$result = $this->mongo_db->get('player');
		return ($result) ? $result[0] : $result;
	}
	public function readPlayers($site_id, $fields, $offset = 0, $limit = 10)
	{
		$this->set_site_mongodb($site_id);
		if($fields)
			$this->mongo_db->select($fields);
		$this->mongo_db->limit($limit);
		$this->mongo_db->offset($offset);
		return $this->mongo_db->get('player');
	}
	public function updatePlayer($id, $site_id, $fieldData)
	{
		if(!$id)
			return false;
		$fieldData['date_modified'] = date('Y-m-d H:i:s');
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where('pb_player_id', $id);
		$this->mongo_db->set($fieldData);
		$this->mongo_db()->update('player');
		return true;
	}
	public function deletePlayer($id, $site_id)
	{
		if(!$id)
			return false;
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where('pb_player_id', $id);
		$this->mongo_db->delete('player');
		return true;
	}
	public function getPlaybasisId($clientData)
	{
		if(!$clientData)
			return -1;
		$this->set_site_mongodb($clientData['site_id']);
		$this->mongo_db->select('pb_player_id');
		$this->mongo_db->where(array(
			'client_id' => $clientData['client_id'],
			'site_id' => $clientData['site_id'],
			'cl_player_id' => $clientData['cl_player_id']
		));
		$id = $this->mongo_db->get('player');
		return ($id) ? $id[0]['pb_player_id'] : -1;
	}
	public function getClientPlayerId($pb_player_id, $site_id)
	{
		if(!$pb_player_id)
			return -1;
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select('cl_player_id');
		$this->mongo_db->where('pb_player_id', $pb_player_id);
		$id = $this->mongo_db->get('player');
		return ($id) ? $id[0]['cl_player_id'] : -1;
	}
	public function getPlayerPoints($pb_player_id, $site_id)
	{
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'reward_id',
			'value'
		));
		$this->mongo_db->where('pb_player_id', $pb_player_id);
		$result = $this->mongo_db->get('reward_to_player');
		$count = count($result);
		for($i=0; $i < $count; ++$i)
			unset($result[$i]['_id']);
		return $result;
	}
	public function getPlayerPoint($pb_player_id, $reward_id, $site_id)
	{
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'reward_id',
			'value'
		));
		$this->mongo_db->where(array(
			'pb_player_id' => $pb_player_id,
			'reward_id' => $reward_id
		));
		$result = $this->mongo_db->get('reward_to_player');
		$count = count($result);
		for($i=0; $i < $count; ++$i)
			unset($result[$i]['_id']);
		return $result;
	}
	public function getLastActionPerform($pb_player_id, $site_id)
	{
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'action_id',
			'action_name',
			'date_added'
		));
		$this->mongo_db->where('pb_player_id', $pb_player_id);
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('action_log');
		if(!$result)
			return $result;
		$result = $result[0];
		$result['time'] = $result['date_added'];
		unset($result['date_added']);
		unset($result['_id']);
		return $result;
	}
	public function getActionPerform($pb_player_id, $action_id, $site_id)
	{
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'action_id',
			'action_name',
			'date_added'
		));
		$this->mongo_db->where(array(
            'pb_player_id' => $pb_player_id,
            'action_id' => $action_id
        ));
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('action_log');
		if(!$result)
			return $result;
		$result = $result[0];
		$result['time'] = $result['date_added'];
		unset($result['date_added']);
		unset($result['_id']);
		return $result;
	}
	public function getActionCount($pb_player_id, $action_id, $site_id)
	{
		$fields = array(
			'pb_player_id' => $pb_player_id,
			'action_id' => $action_id
		);
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where($fields);
		$count = $this->mongo_db->count('action_log');
		$this->mongo_db->select(array(
			'action_id',
			'action_name'
		));
		$this->mongo_db->where($fields);
		$result = $this->mongo_db->get('action_log');
		$result = ($result) ? $result[0] : array();
		$result['count'] = $count;
		unset($result['_id']);
		return $result;
	}
	public function getBadge($pb_player_id, $site_id)
	{
		$this->set_site($site_id);
        $this->site_db()->select('badge_id,amount');
        $this->site_db()->where('pb_player_id', $pb_player_id);
		$badges = db_get_result_array($this, 'playbasis_badge_to_player');
        if(!$badges)
            return array();
        foreach($badges as &$badge)
        {
            //badge data
            $this->site_db()->select('name,description');
            $this->site_db()->where('badge_id', $badge['badge_id']);
			$result = db_get_row_array($this, 'playbasis_badge_description');
            $badge = array_merge($badge, $result);
            //badge image
            $this->site_db()->select('image');
            $this->site_db()->where('badge_id', $badge['badge_id']);
			$result = db_get_row_array($this, 'playbasis_badge');
            $badge['image'] = $this->config->item('IMG_PATH') . $result['image'];
        }
        return $badges;
	}
	public function getLastEventTime($pb_player_id, $site_id, $eventType)
	{
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select('date_added');
		$this->mongo_db->where(array(
			'pb_player_id' => $pb_player_id,
			'event_type' => $eventType
		));
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('event_log');
		if($result)
			return $result[0]['date_added'];
		return '0000-00-00 00:00:00';
	}
	public function getLeaderboard($ranked_by, $limit, $client_id, $site_id)
	{
		//get reward id
		$this->set_site($site_id);
		$this->site_db()->select('reward_id');
		$this->site_db()->where(array(
			'name' => $ranked_by,
			'site_id' => $site_id,
			'client_id' => $client_id
		));
		$result = db_get_row_array($this, 'playbasis_reward_to_client');

		if(!$result)
			return array();
		//get points for the reward id
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'cl_player_id',
			'value'
		));
		$this->mongo_db->where(array(
			'reward_id' => $result['reward_id'],
			'client_id' => $client_id,
			'site_id' => $site_id
		));
		$this->mongo_db->order_by(array('value' => 'desc'));
		$this->mongo_db->limit($limit);
		$result = $this->mongo_db->get('reward_to_player');
		$count = count($result);
		for($i=0; $i < $count; ++$i)
		{
			$result[$i]['player_id'] = $result[$i]['cl_player_id'];
			$result[$i][$ranked_by] = $result[$i]['value'];
			unset($result[$i]['cl_player_id']);
			unset($result[$i]['value']);
			unset($result[$i]['_id']);
		}
		return $result;
	}
	public function getLeaderboards($limit, $client_id, $site_id)
	{
		//get all rewards
		$this->set_site($site_id);
		$this->site_db()->select('reward_id,name');
		$this->site_db()->where(array(
			'site_id' => $site_id,
			'client_id' => $client_id,
			'group' => 'POINT'
		));
		$rewards = db_get_result_array($this, 'playbasis_reward_to_client');
		if(!$rewards)
			return array();
		$result = array();
		$this->set_site_mongodb($site_id);
		foreach($rewards as $reward)
		{
			//get points for the reward id
			$reward_id = $reward['reward_id'];
			$name = $reward['name'];
			$this->mongo_db->select(array(
				'cl_player_id',
				'value'
			));
			$this->mongo_db->where(array(
				'reward_id' => $reward_id,
				'client_id' => $client_id,
				'site_id' => $site_id
			));
			$this->mongo_db->order_by(array('value' => 'desc'));
			$this->mongo_db->limit($limit);
			$ranking = $this->mongo_db->get('reward_to_player');
			$count = count($ranking);
			for($i=0; $i < $count; ++$i)
			{
				$ranking[$i]['player_id'] = $ranking[$i]['cl_player_id'];
				$ranking[$i][$name] = $ranking[$i]['value'];
				unset($ranking[$i]['cl_player_id']);
				unset($ranking[$i]['value']);
				unset($ranking[$i]['_id']);
			}
			$result[$name] = $ranking;
		}
		return $result;
	}
}
?>
