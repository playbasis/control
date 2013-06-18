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
		$pb_player_id = $this->generate_id_mongodb('player');
		$this->set_site_mongodb($data['site_id']);
		$mongoDate = new MongoDate(time());
		$this->mongo_db->insert('player', array(
			'pb_player_id'  => new MongoInt64("$pb_player_id"),
			'client_id'		=> intval($data['client_id']),
			'site_id'		=> intval($data['site_id']),
			'cl_player_id'	=> $data['player_id'],
			'image'			=> $data['image'],
			'email'			=> $data['email'],
			'username'		=> $data['username'],
			'exp'			=> 0,
			'level'			=> 0,
			'status'		=> 1,			
			'first_name'	=> (isset($data['first_name']))	 ? $data['first_name']	: $data['username'],
			'last_name'		=> (isset($data['last_name']))	 ? $data['last_name']	: '',
			'nickname'		=> (isset($data['nickname']))	 ? $data['nickname']	: '',
			'facebook_id'	=> (isset($data['facebook_id'])) ? $data['facebook_id'] : '',
			'twitter_id'	=> (isset($data['twitter_id']))	 ? $data['twitter_id']	: '',
			'password'		=> (isset($data['password']))	 ? $data['password']	: '',
			'gender'		=> (isset($data['gender']))		 ? intval($data['gender']) : 0,
			'birth_date'	=> (isset($data['birth_date']))	 ? new MongoDate(strtotime($data['birth_date'])) : '',
			'date_added'	=> $mongoDate,
			'date_modified' => $mongoDate
		));
		return $pb_player_id;
	}
	public function readPlayer($id, $site_id, $fields)
	{
        if(!$id)
            return array();
		$this->set_site_mongodb($site_id);
		if($fields)
			$this->mongo_db->select($fields);
		$this->mongo_db->where('pb_player_id', intval($id));
		$result = $this->mongo_db->get('player');
		if(!$result)
			return $result;
		$result = $result[0];
		unset($result['_id']);
		$result['registered'] = date('Y-m-d H:i:s', $result['date_added']->sec);
		unset($result['date_added']);
		return $result;
	}
	public function updatePlayer($id, $site_id, $fieldData)
	{
		if(!$id)
			return false;
		$fieldData['date_modified'] = new MongoDate(time());
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where('pb_player_id', intval($id));
		$this->mongo_db->set($fieldData);
		$this->mongo_db->update('player');
		return true;
	}
	public function deletePlayer($id, $site_id)
	{
		if(!$id)
			return false;
		$this->set_site_mongodb($site_id);
		$this->mongo_db->where('pb_player_id', intval($id));
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
			'client_id' => intval($clientData['client_id']),
			'site_id' => intval($clientData['site_id']),
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
		$this->mongo_db->where('pb_player_id', intval($pb_player_id));
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
		$this->mongo_db->where('pb_player_id', intval($pb_player_id));
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
			'pb_player_id' => intval($pb_player_id),
			'reward_id' => intval($reward_id)
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
		$this->mongo_db->where('pb_player_id', intval($pb_player_id));
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('action_log');
		if(!$result)
			return $result;
		$result = $result[0];
		$result['time'] = date('Y-m-d H:i:s', $result['date_added']->sec);
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
			'pb_player_id' => intval($pb_player_id),
            'action_id' => intval($action_id)
        ));
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('action_log');
		if(!$result)
			return $result;
		$result = $result[0];
		$result['time'] = date('Y-m-d H:i:s', $result['date_added']->sec);
		unset($result['date_added']);
		unset($result['_id']);
		return $result;
	}
	public function getActionCount($pb_player_id, $action_id, $site_id)
	{
		$fields = array(
			'pb_player_id' => intval($pb_player_id),
			'action_id' => intval($action_id)
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
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'badge_id',
			'amount'
		));
		$this->mongo_db->where('pb_player_id', intval($pb_player_id));
		$badges = $this->mongo_db->get('badge_to_player');
        if(!$badges)
            return array();
		$this->set_site($site_id);
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
			'pb_player_id' => intval($pb_player_id),
			'event_type' => $eventType
		));
		$this->mongo_db->order_by(array('date_added' => 'desc'));
		$result = $this->mongo_db->get('event_log');
		if($result)
			return date('Y-m-d H:i:s', $result[0]['date_added']->sec);
		return '0000-00-00 00:00:00';
	}
	public function getLeaderboard($ranked_by, $limit, $client_id, $site_id)
	{
		//get reward id
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select('reward_id');
		$this->mongo_db->where(array(
			'name' => $ranked_by,
			'site_id' => intval($site_id),
			'client_id' => intval($client_id)
		));
		$result = $this->mongo_db->get('reward_to_client');
		if(!$result)
			return array();
		$result = $result[0];
		//get points for the reward id
		$this->mongo_db->select(array(
			'cl_player_id',
			'value'
		));
		$this->mongo_db->where(array(
			'reward_id' => intval($result['reward_id']),
			'client_id' => intval($client_id),
			'site_id' => intval($site_id)
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
		$this->set_site_mongo($site_id);
		$this->mongo_db->select(array(
			'reward_id',
			'name'
		));
		$this->mongo_db->where(array(
			'site_id' => intval($site_id),
			'client_id' => intval($client_id),
			'group' => 'POINT'
		));
		$rewards = $this->mongo_db->get('reward_to_client');
		if(!$rewards)
			return array();
		$result = array();
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
				'reward_id' => intval($reward_id),
				'client_id' => intval($client_id),
				'site_id' => intval($site_id)
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
