<?php

if (!function_exists('curl_init')) {
	throw new Exception('Playbasis needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
	throw new Exception('Playbasis needs the JSON PHP extension.');
}

class Playbasis
{
	const BASE_URL = 'https://api.pbapp.net/';
	private $token = null;
	
	public function auth($apiKey, $apiSecret)
	{
		$result = $this->call('Auth', array(
			'api_key' => $apiKey,
			'api_secret' => $apiSecret
		));
		$this->token = $result['response']['token'];
		return $this->token != false && is_string($this->token);
	}
	
	public function player($playerId)
	{
		return $this->call("Player/$playerId", array(
			'token' => $this->token
		));
	}
	
	/*
	 * @param	$optionalData	Key-value for additional parameters to be sent to the register method.
	 * 							The following keys are supported:
	 * 							- facebook_id
	 * 							- twitter_id
	 * 							- password		assumed hashed
	 * 							- first_name
	 * 							- last_name
	 * 							- nickname
	 * 							- gender		1=Male, 2=Female
	 * 							- birth_date	format YYYY-MM-DD
	 */
	public function register($playerId, $username, $email, $imageUrl, $optionalData=array())
	{
		return $this->call("Player/$playerId/register", array_merge(array(
			'token' => $this->token,
			'username' => $username,
			'email' => $email,
			'image' => $imageUrl
		), $optionalData));
	}
	
	public function login($playerId)
	{
		return $this->call("Player/$playerId/login", array('token' => $this->token));
	}
	
	public function logout($playerId)
	{
		return $this->call("Player/$playerId/logout", array('token' => $this->token));
	}
	
	public function points($playerId)
	{
		return $this->call("Player/$playerId/points", array('token' => $this->token));
	}
	
	public function point($playerId, $pointName)
	{
		return $this->call("Player/$playerId/point/$pointName", array('token' => $this->token));
	}
	
	public function actionLastPerformed($playerId)
	{
		return $this->call("Player/$playerId/action/time", array('token' => $this->token));
	}
	
	public function actionLastPerformedTime($playerId, $actionName)
	{
		return $this->call("Player/$playerId/action/$actionName/time", array('token' => $this->token));
	}
	
	public function actionPerformedCount($playerId, $actionName)
	{
		return $this->call("Player/$playerId/action/$actionName/count", array('token' => $this->token));
	}
	
	public function badgeOwned($playerId)
	{
		return $this->call("Player/$playerId/badge", array('token' => $this->token));
	}
	
	public function rank($rankedBy, $limit)
	{
		return $this->call("Player/rank/$rankedBy/$limit", array('token' => $this->token));
	}
	
	public function badges()
	{
		return $this->call("Badge", array('token' => $this->token));
	}
	
	public function badge($badgeId)
	{
		return $this->call("Badge/$badgeId", array('token' => $this->token));
	}
	
	public function badgeCollections()
	{
		return $this->call("Badge/collection", array('token' => $this->token));
	}
	
	public function badgeCollection($collectionId)
	{
		return $this->call("Badge/collection/$collectionId", array('token' => $this->token));
	}
	
	public function actionConfig()
	{
		return $this->call("Engine/actionConfig", array('token' => $this->token));
	}
	
	/*
	 * @param	$optionalData	Key-value for additional parameters to be sent to the rule method.
	 * 							The following keys are supported:
	 * 							- url		url of the page that trigger the action (for triggering non-global actions)
	 * 							- reward	name of the custom-point reward to give (for triggering rules with custom-point reward)
	 * 							- quantity	amount of points to give (for triggering rules with custom-point reward)
	 */
	public function rule($playerId, $action, $optionalData=array())
	{
		return $this->call("Engine/rule", array_merge(array(
			'token' => $this->token,
			'player_id' => $playerId,
			'action' => $action
			), $optionalData));
	}
	
	public function call($method, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $method);	// set url
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					// turn off output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);				// refuse response from called server
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');			// set agent
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);						// times for execute
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);				// times for try to connect 
		curl_setopt($ch, CURLOPT_POST, TRUE);						// use POST 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);				// data
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		curl_close($ch);
		return $result;
	}
}