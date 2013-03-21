<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//define('STREAM_URL', 'https://dev.pbapp.net/activitystream/');
define('STREAM_URL', 'https://pbapp.net/activitystream/');
// define('STREAM_URL', 'http://localhost/activitystream/');
define('STREAM_PORT', 3000);
define('USERPASS', 'planes:capetorment852456');
class Node_stream extends CI_Model
{
	public function publish($data, $info)
	{
		//prepare chanel name
		$chanelName = preg_replace('/(http[s]?:\/\/)?([w]{3}\.)?/', '', $info['domain_name']);
		//set curl
		$ch = curl_init();
		//set curl option
		curl_setopt($ch, CURLOPT_URL, STREAM_URL . $chanelName); // set url
		curl_setopt($ch, CURLOPT_PORT, STREAM_PORT); // set port
		curl_setopt($ch, CURLOPT_HEADER, FALSE); // turn off output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // refuse response from called server
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));# set Content-Type
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: text/plain; charset=utf-8'
		)); // set Content-Type
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT'); // set  agent    	
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // time for execute
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // time for try to connect 
		curl_setopt($ch, CURLOPT_POST, TRUE); // use POST 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->activityFeedFormatter($data))); // wrap data 
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // http authen
		curl_setopt($ch, CURLOPT_USERPWD, USERPASS); // http authrn user password
		//process 
		curl_exec($ch);
		//debugging
		// 	var_dump(curl_errno($ch));
		// $cinfo = curl_getinfo($ch);
		// var_dump($cinfo);
		//close connection
		curl_close($ch);
	}
	private function activityFeedFormatter($data)
	{
		$playerData = $this->getPlayerInfo($data['pb_player_id']);
		$activityFormat = array(
			'published' => date('c'), //rfc3339 , atom , ISO 8601
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
	private function getPlayerInfo($pb_player_id)
	{
		$this->db->select('cl_player_id,first_name,last_name,image');
		$this->db->where('pb_player_id', $pb_player_id);
		$result = $this->db->get('playbasis_player');
		return $result->row_array();
	}
}
?>