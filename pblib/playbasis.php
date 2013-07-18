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
	const BASE_ASYNC_URL = 'https://api.pbapp.net/async/';

	private $token = null;
	private $apiKeyParam = null;
	private $respChannel = null;
	
	public function auth($apiKey, $apiSecret)
	{
		$this->apiKeyParam = "?api_key=$apiKey";
		$result = $this->call('Auth', array(
			'api_key' => $apiKey,
			'api_secret' => $apiSecret
		));
		$this->token = $result['response']['token'];
		return $this->token != false && is_string($this->token);
	}
	
	/*
	 * @param	$channel	Set this value to the domain of your site (ex. yoursite.com) to receive the response of async calls via our response server.
	 *						Please see our api documentation at doc.pbapp.net for more details.
	 */
	public function setAsyncResponseChannel($channel)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_ASYNC_URL."channel/verify/$channel");
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);

		if($result === 'true')
		{
			$this->respChannel = $channel;
			return true;
		}
		return false;
	}
	
	public function player($playerId)
	{
		return $this->call("Player/$playerId", array('token' => $this->token));
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
	public function register_async($playerId, $username, $email, $imageUrl, $optionalData=array())
	{
		return $this->call_async("Player/$playerId/register", array_merge(array(
			'token' => $this->token,
			'username' => $username,
			'email' => $email,
			'image' => $imageUrl
		), $optionalData), $this->respChannel);
	}
	
	/*
	 * @param	$updateData		Key-value for data to be updated.
	 * 							The following keys are supported:
	 *							- username
	 *							- email
	 *							- image
	 *							- exp
	 *							- level
	 * 							- facebook_id
	 * 							- twitter_id
	 * 							- password		assumed hashed
	 * 							- first_name
	 * 							- last_name
	 * 							- nickname
	 * 							- gender		1=Male, 2=Female
	 * 							- birth_date	format YYYY-MM-DD
	 */
	public function update($playerId, $updateData)
	{
		$updateData['token'] = $this->token;
		return $this->call("Player/$playerId/update", $updateData);
	}
	public function update_async($playerId, $updateData)
	{
		$updateData['token'] = $this->token;
		return $this->call_async("Player/$playerId/update", $updateData, $this->respChannel);
	}
	
	public function delete($playerId)
	{
		return $this->call("Player/$playerId/delete", array('token' => $this->token));
	}
	public function delete_async($playerId)
	{
		return $this->call_async("Player/$playerId/delete", array('token' => $this->token), $this->respChannel);
	}
	
	public function login($playerId)
	{
		return $this->call("Player/$playerId/login", array('token' => $this->token));
	}
	public function login_async($playerId)
	{
		return $this->call_async("Player/$playerId/login", array('token' => $this->token), $this->respChannel);
	}
	
	public function logout($playerId)
	{
		return $this->call("Player/$playerId/logout", array('token' => $this->token));
	}
	public function logout_async($playerId)
	{
		return $this->call_async("Player/$playerId/logout", array('token' => $this->token), $this->respChannel);
	}
	
	public function points($playerId)
	{
		return $this->call("Player/$playerId/points" . $this->apiKeyParam);
	}
	
	public function point($playerId, $pointName)
	{
		return $this->call("Player/$playerId/point/$pointName" . $this->apiKeyParam);
	}
	
	public function actionLastPerformed($playerId)
	{
		return $this->call("Player/$playerId/action/time" . $this->apiKeyParam);
	}
	
	public function actionLastPerformedTime($playerId, $actionName)
	{
		return $this->call("Player/$playerId/action/$actionName/time" . $this->apiKeyParam);
	}
	
	public function actionPerformedCount($playerId, $actionName)
	{
		return $this->call("Player/$playerId/action/$actionName/count" . $this->apiKeyParam);
	}
	
	public function badgeOwned($playerId)
	{
		return $this->call("Player/$playerId/badge" . $this->apiKeyParam);
	}
	
	public function rank($rankedBy, $limit)
	{
		return $this->call("Player/rank/$rankedBy/$limit" . $this->apiKeyParam);
	}
	
	public function badges()
	{
		return $this->call("Badge" . $this->apiKeyParam);
	}
	
	public function badge($badgeId)
	{
		return $this->call("Badge/$badgeId" . $this->apiKeyParam);
	}
	
	public function badgeCollections()
	{
		return $this->call("Badge/collection" . $this->apiKeyParam);
	}
	
	public function badgeCollection($collectionId)
	{
		return $this->call("Badge/collection/$collectionId" . $this->apiKeyParam);
	}
	
	public function actionConfig()
	{
		return $this->call("Engine/actionConfig" . $this->apiKeyParam);
	}
	
	/*
	 * @param	$optionalData	Key-value for additional parameters to be sent to the rule method.
	 * 							The following keys are supported:
	 * 							- url		url or filter string (for triggering non-global actions)
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
	public function rule_async($playerId, $action, $optionalData=array())
	{
		return $this->call_async("Engine/rule", array_merge(array(
			'token' => $this->token,
			'player_id' => $playerId,
			'action' => $action
		), $optionalData), $this->respChannel);
	}
	
	public function call($method, $data = null) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $method);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					// turn off output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);				// refuse response from called server
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');			// set agent
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);						// times for execute
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);				// times for try to connect
		if($data)
		{
			curl_setopt($ch, CURLOPT_POST, TRUE);					// use POST 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);			// data
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		curl_close($ch);
		return $result;
	}
	
	/*
	 * @param	$responseChannel	Set this value to the domain of your site (ex. yoursite.com) to receive the response of async calls via our response server.
	 *								Please see our api documentation at doc.pbapp.net for more details.
	 */
	public function call_async($method, $data, $responseChannel = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_ASYNC_URL.'call');
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					// turn off output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);				// refuse response from called server
		curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');			// set agent
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);						// times for execute
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);				// times for try to connect
		if($data)
		{
			$body['endpoint'] = $method;
			$body['data'] = $data;
			if($responseChannel)
				$body['channel'] = $responseChannel;
			$body = json_encode($body);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8'		// set Content-Type
			));
			curl_setopt($ch, CURLOPT_POST, TRUE);					// use POST
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);			// post body
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}