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
	}
	public function createPlayer($data)
	{
		$this->checkClientUserLimitWarning($data['client_id'], $data['site_id']);
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
		$this->set_site($data['site_id']);
		$this->site_db()->insert('playbasis_player', $inputData);
		$this->memcached_library->update_delete('playbasis_player');
		return $this->site_db()->insert_id();
	}
	public function readPlayer($id, $site_id, $fields)
	{
        if(!$id)
            return array();
		$this->set_site($site_id);
        if($fields)
            $this->site_db()->select($fields);
        $this->site_db()->where('pb_player_id', $id);
		return db_get_row_array($this, 'playbasis_player');
	}
	public function readPlayers($site_id, $fields, $offset = 0, $limit = 10)
	{
		$this->set_site($site_id);
		if($fields)
			$this->site_db()->select($fields);
		$this->site_db()->limit($limit, $offset);
		return db_get_result_array($this, 'playbasis_player');
	}
	public function updatePlayer($id, $site_id, $fieldData)
	{
		if(!$id)
			return false;
		$fieldData['date_modified'] = date('Y-m-d H:i:s');
		$this->set_site($site_id);
		$this->site_db()->where('pb_player_id', $id);
		$this->site_db()->update('playbasis_player', $fieldData);
		$this->memcached_library->update_delete('playbasis_player');
		return true;
	}
	public function deletePlayer($id, $site_id)
	{
		if(!$id)
			return false;
		$this->set_site($site_id);
		$this->site_db()->where('pb_player_id', $id);
		$this->site_db()->delete('playbasis_player');
		$this->memcached_library->update_delete('playbasis_player');
		return true;
	}
	public function getPlaybasisId($clientData)
	{
		if(!$clientData)
			return -1;
		$this->set_site($clientData['site_id']);
		$this->site_db()->where(array(
			'client_id' => $clientData['client_id'],
			'site_id' => $clientData['site_id'],
			'cl_player_id' => $clientData['cl_player_id']
		));
		$this->site_db()->select('pb_player_id');
		$id = db_get_row_array($this, 'playbasis_player');
		if(!$id)
			return -1;
		return $id['pb_player_id'];
	}
	public function getClientPlayerId($pb_player_id, $site_id)
	{
		if(!$pb_player_id)
			return -1;
		$this->set_site($site_id);
		$this->site_db()->select('cl_player_id');
		$this->site_db()->where('pb_player_id', $pb_player_id);
		$id = db_get_row_array($this, 'playbasis_player');
		if(!$id)
			return -1;
		return $id['cl_player_id'];
	}
	public function getPlayerPoints($pb_player_id, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('reward_id,value');
		$this->site_db()->where('pb_player_id', $pb_player_id);
		return db_get_result_array($this, 'playbasis_reward_to_player');
	}
	public function getPlayerPoint($pb_player_id, $reward_id, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('reward_id,value');
		$this->site_db()->where(array(
			'pb_player_id' => $pb_player_id,
			'reward_id' => $reward_id
		));
		return db_get_result_array($this, 'playbasis_reward_to_player');
	}
	public function getLastActionPerform($pb_player_id, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('action_id,action_name,date_added AS time');
		$this->site_db()->where('pb_player_id', $pb_player_id);
		$this->site_db()->order_by('date_added', 'DESC');
		return db_get_row_array($this, 'playbasis_action_log');
	}
	public function getActionPerform($pb_player_id, $action_id, $site_id)
	{
		$this->set_site($site_id);
        $this->site_db()->select('action_id,action_name,date_added AS time');
        $this->site_db()->where(array(
            'pb_player_id' => $pb_player_id,
            'action_id' => $action_id
        ));
        $this->site_db()->order_by('date_added', 'DESC');
		return db_get_row_array($this, 'playbasis_action_log');
	}
	public function getActionCount($pb_player_id, $action_id, $site_id)
	{
		$fields = array(
			'pb_player_id' => $pb_player_id,
			'action_id' => $action_id
		);
		$this->set_site($site_id);
		$this->site_db()->where($fields);
		$count = db_count_all_results($this, 'playbasis_action_log');
		$this->site_db()->select('action_id,action_name');
		$this->site_db()->where($fields);
		$result = db_get_row_array($this, 'playbasis_action_log');
		$result['count'] = $count;
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
		$this->set_site($site_id);
		$this->site_db()->select('date_added');
		$this->site_db()->where(array(
			'pb_player_id' => $pb_player_id,
			'event_type' => $eventType
		));
		$this->site_db()->order_by('date_added', 'DESC');
		$result = db_get_row_array($this, 'playbasis_event_log');
		if($result)
			return $result['date_added'];
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
		$this->site_db()->select("cl_player_id AS player_id,value AS $ranked_by");
		$this->site_db()->where(array(
			'reward_id' => $result['reward_id'],
			'client_id' => $client_id,
			'site_id' => $site_id
		));
		$this->site_db()->order_by('value', 'DESC');
		$this->site_db()->limit($limit);
		$result = db_get_result_array($this, 'playbasis_reward_to_player');
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
		foreach($rewards as $reward)
		{
			//get points for the reward id
			$reward_id = $reward['reward_id'];
			$name = $reward['name'];
			$this->site_db()->select("cl_player_id AS player_id,value AS '$name'");
			$this->site_db()->where(array(
				'reward_id' => $reward_id,
				'client_id' => $client_id,
				'site_id' => $site_id
			));
			$this->site_db()->order_by('value', 'DESC');
			$this->site_db()->limit($limit);
			$result[$name] = db_get_result_array($this, 'playbasis_reward_to_player');
		}
		return $result;
	}
	private function checkClientUserLimitWarning($client_id, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('limit_users, last_send_limit_users');
		$this->site_db()->where(array(
			'client_id' => $client_id,
			'site_id' => $site_id
		));
		$result = db_get_row_array($this, 'playbasis_client_site');
        assert($result);
		$limit = $result['limit_users'];
        $last_send = $result['last_send_limit_users'];

        $date1 = new DateTime($last_send);
		$date1->modify('+1 week');
        $date2 = new DateTime("now");

        if($date1 > $date2)
            return;
		if(!$limit)
			return;

		$this->site_db()->where(array(
			'client_id' => $client_id,
			'site_id' => $site_id
		));
		$usersCount = db_count_all_results($this, 'playbasis_player');
		if($usersCount > ($limit * 0.95))
		{
            $this->set_site($site_id);
            $this->site_db()->select('user_id');
            $this->site_db()->where(array(
                'client_id' => $client_id
            ));
            $result = db_get_result_array($this, 'user_to_client');

            $user_id_list=array();
            foreach ($result as $r) {
                array_push($user_id_list,$r['user_id']);
            }

            $this->set_site($site_id);
            $this->site_db()->select('email');
            $this->site_db()->where_in(
                'user_id', $user_id_list
            );
            $result = db_get_result_array($this, 'user');

            $email_list=array();
            foreach ($result as $r) {
                array_push($email_list,$r['email']);
            }

            $email_string = implode(",", $email_list);

            $this->load->library('email');

            $this->load->library('parser');
			$data = array(
				'user_left' => ($limit-$usersCount),
				'user_count' => $usersCount,
				'user_limit' => $limit
			);

            $config['mailtype'] = 'html';
            $config['charset'] = 'utf-8';
            $email = $email_string;
            $subject = "Playbasis user limit alert";
            $htmlMessage = $this->parser->parse('limit_user_alert.html', $data, true);

			//email client to upgrade account
            $this->email->initialize($config);
            $this->email->clear();
            $this->email->from('info@playbasis.com', 'Playbasis');
            $this->email->to($email);
            $this->email->bcc('cscteam@playbasis.com');
            $this->email->subject($subject);
            $this->email->message($htmlMessage);
            $this->email->send();

            $this->updateLastAlertLimitUser($client_id, $site_id);
		}
	}

    public function updateLastAlertLimitUser($client_id, $site_id)
    {
        $fieldData['last_send_limit_users'] = date('Y-m-d H:i:s');
        $this->set_site($site_id);
        $this->site_db()->where('client_id', $client_id);
        $this->site_db()->where('site_id', $site_id);
        $this->site_db()->update('playbasis_client_site', $fieldData);
        $this->memcached_library->update_delete('playbasis_client_site');
        return true;
    }
}
?>
