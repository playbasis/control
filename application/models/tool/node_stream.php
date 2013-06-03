<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//define('STREAM_URL', 'https://dev.pbapp.net/activitystream/');
define('STREAM_URL', 'https://node.pbapp.net/activitystream/');
// define('STREAM_URL', 'http://localhost/activitystream/');
define('STREAM_PORT', 443);
define('USERPASS', 'planes:capetorment852456');
class Node_stream extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
	}
	public function publish($data, $domain_name, $site_id)
	{
		//get chanel name
		$chanelName = preg_replace('/(http[s]?:\/\/)?([w]{3}\.)?/', '', $domain_name);
		$message = json_encode($this->activityFeedFormatter($data, $site_id));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, STREAM_URL . $chanelName);	// set url
		curl_setopt($ch, CURLOPT_PORT, STREAM_PORT);				// set port
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					// turn off output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);				// refuse response from called server
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: text/plain; charset=utf-8'				// set Content-Type
		));
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');			// set agent
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);						// times for execute
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);				// times for try to connect 
		curl_setopt($ch, CURLOPT_POST, TRUE);						// use POST 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $message);				// data
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);			// http authen
		curl_setopt($ch, CURLOPT_USERPWD, USERPASS);				// user password
		curl_exec($ch);
		//var_dump(curl_errno($ch));
		//$cinfo = curl_getinfo($ch);
		//var_dump($cinfo);
		curl_close($ch);
	}
	private function activityFeedFormatter($data, $site_id)
	{
		$playerData = $this->getPlayerInfo($data['pb_player_id'], $site_id);
		$activityFormat = array(
			'published' => date('c'), //rfc3339, atom, ISO 8601
			'actor' => array(
				'objectType' => 'person',
				'id' => $playerData['cl_player_id'],
				'displayName' => $playerData['first_name'] . ' ' . $playerData['last_name'],
				'image' => array(
					'url' => $playerData['image'],
					'width' => 150,
					'height' => 150
				)
			),
			'verb' => $data['action_name'],
			'object' => array(
				'message' => $data['message']
			),
			'target' => array()
		);
		if(isset($data['badge']))
		{
			$activityFormat['object']['badge'] = array(
				'id' => $data['badge']['badge_id'],
				'name' => $data['badge']['name'],
				'image' => array(
					'url' => $data['badge']['image'],
					'width' => 76,
					'height' => 76
				)
			);
		}
		else
		{
			$activityFormat['object']['badge'] = NULL;
		}
		$activityFormat['object']['level'] = isset($data['level']) ? $data['level'] : NULL;
		$activityFormat['object']['point'] = isset($data['point']) ? $data['point'] : NULL;
		$activityFormat['object']['amount'] = isset($data['amount']) ? $data['amount'] : NULL;
		return $activityFormat;
	}
	private function getPlayerInfo($pb_player_id, $site_id)
	{
		$this->set_site($site_id);
		$this->site_db()->select('cl_player_id,first_name,last_name,image');
		$this->site_db()->where('pb_player_id', $pb_player_id);
		return db_get_row_array($this, 'playbasis_player');
	}
}
?>