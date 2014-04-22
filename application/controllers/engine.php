<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Engine extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('engine/jigsaw', 'jigsaw_model');
		$this->load->model('client_model');
		$this->load->model('tracker_model');
		$this->load->model('point_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/utility', 'utility');
		$this->load->model('tool/respond', 'resp');
		$this->load->model('tool/node_stream', 'node');
		$this->load->model('social_model');
	}
	public function getActionConfig_get()
	{
		$required = $this->input->checkParam(array(
			'api_key'
		));
		if($required)
			$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
		$validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
		if(!$validToken)
			$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
		$ruleSet = $this->client_model->getRuleSet(array(
			'client_id' => $validToken['client_id'],
			'site_id' => $validToken['site_id']
		));
		$actionConfig = array();
		foreach($ruleSet as $rule)
		{
//			$jigsawSet = unserialize($rule['jigsaw_set']);
			$jigsawSet = $rule['jigsaw_set'];
			$actionId = $jigsawSet[0]['config']['action_id'];
			$actionInput = $jigsawSet[0]['config'];
			if(isset($actionConfig[$actionId]))
			{
				$config = array(
					'url' => $actionInput['url'],
					'regex' => $actionInput['regex']
				);
				$found = false;
				foreach($actionConfig[$actionId]['config'] as $configElement)
				{
					if($config['url'] != $configElement['url'] || $config['regex'] != $configElement['regex'])
						continue;
					$found = true;
					break;
				}
				if(!$found)
					array_push($actionConfig[$actionId]['config'], $config);
			}
			else
			{
				$actionConfig[$actionId] = array(
					'name' => $jigsawSet[0]['config']['action_name'],
					'config' => array(
						array(
							'url' => $actionInput['url'],
							'regex' => $actionInput['regex']
						)
					)
				);
			}
		}
		$this->response($this->resp->setRespond($actionConfig), 200);
	}
	public function rule_get($option = 0)
	{
		if($option != 'facebook')
		{
			$this->response($this->error->setError('ACCESS_DENIED'), 200);
		}
		$challenge = $this->input->get('hub_challenge');
		echo $challenge;
	}
	public function rule_post($option = 0)
	{
		$this->benchmark->mark('engine_rule_start');
		$fbData = null;
		$twData = null;
		if($option == 'facebook')
		{
			$fbData = $this->social_model->processFacebookData($this->request->body);
			if(!$fbData['pb_player_id'])
				$this->response($this->error->setError('USER_NOT_EXIST'), 200);
			if(!$fbData['action'])
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
		}
		else if($option == 'twitter')
		{
			$twData = $this->social_model->processTwitterData($this->request->body);
		}
		if($fbData)
		{
			//process facebook data
			$validToken = $this->auth_model->createToken($fbData['client_id'], $fbData['site_id']);
			if(!$validToken)
				$this->response($this->error->setError('INVALID_TOKEN'), 200);
			$pb_player_id = $fbData['pb_player_id'];
			if($pb_player_id < 0)
				$this->response($this->error->setError('USER_NOT_EXIST'), 200);
			$actionName = $fbData['action'];
			$actionId = $this->client_model->getActionId(array(
				'client_id' => $validToken['client_id'],
				'site_id' => $validToken['site_id'],
				'action_name' => $actionName
			));
			if(!$actionId)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
			$input = array_merge($validToken, array(
				'pb_player_id' => $pb_player_id,
				'action_id' => $actionId,
				'action_name' => $actionName
			));
			$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
		}
		else if($twData)
		{
			//proces twitter data
			$apiResult = null;
			foreach($twData['sites'] as $site)
			{
				$validToken = $this->auth_model->createToken($site['client_id'], $site['site_id']);
				if(!$validToken)
					continue;
				$pb_player_id = $site['pb_player_id'];
				if($pb_player_id < 0)
					continue;
				$actionName = $twData['action'];
				$actionId = $this->client_model->getActionId(array(
					'client_id' => $validToken['client_id'],
					'site_id' => $validToken['site_id'],
					'action_name' => $actionName
				));
				if(!$actionId)
					continue;
				$input = array_merge($validToken, array(
					'pb_player_id' => $pb_player_id,
					'action_id' => $actionId,
					'action_name' => $actionName
				));
				$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
			}
		}
		else
		{
			//process regular data
			$required = $this->input->checkParam(array(
				'token'
			));
			if($required)
				$this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
			$required = $this->input->checkParam(array(
				'action',
				'player_id'
			));
			if($required)
				$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
			$validToken = $this->auth_model->findToken($this->input->post('token'));
			if(!$validToken)
				$this->response($this->error->setError('INVALID_TOKEN'), 200);
			//get playbasis player id from client player id
			$cl_player_id = $this->input->post('player_id');
			$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
				'cl_player_id' => $cl_player_id
			)));
			if(!$pb_player_id)
			{
				log_message('error', '[debug-case-pbapp_auto_user] player_id = '.$this->input->post('player_id').', action = '.$this->input->post('action'));
				//$this->response($this->error->setError('USER_NOT_EXIST'), 200);
				//create user with tmp data for this id
				$pb_player_id = $this->player_model->createPlayer(array_merge($validToken, array(
					'player_id' => $cl_player_id,
					'image' => $this->config->item('DEFAULT_PROFILE_IMAGE'),
					'email' => 'pbapp_auto_user@playbasis.com',
					'username' => 'pbapp_auto_user',
					'first_name' => 'pbapp_auto_user',
					'nickname' => 'pbapp_auto_user',
				)));
			}
			//get action id by action name
			$actionName = $this->input->post('action');
			$actionId = $this->client_model->getActionId(array(
				'client_id' => $validToken['client_id'],
				'site_id' => $validToken['site_id'],
				'action_name' => $actionName
			));
			if(!$actionId)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
			$postData = $this->input->post();
			$input = array_merge($postData, $validToken, array(
				'pb_player_id' => $pb_player_id,
				'action_id' => $actionId,
				'action_name' => $actionName
			));
			$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
		}
		$this->benchmark->mark('engine_rule_end');
		$apiResult['processing_time'] = $this->benchmark->elapsed_time('engine_rule_start', 'engine_rule_end');
		$this->response($this->resp->setRespond($apiResult), 200);
	}
	private function levelup($lv, &$apiResult, $input)
	{
		$event = array(
			'event_type' => 'LEVEL_UP',
			'value' => $lv
		);
		array_push($apiResult['events'], $event);
		$eventMessage = $this->utility->getEventMessage('level', '', '', '', $lv);
		//log event - level
		$this->tracker_model->trackEvent('LEVEL', $eventMessage, array_merge($input, array(
			'amount' => $lv
		)));
		return $eventMessage;
	}
	private function processRule($input, $validToken, $fbData, $twData)
	{
		if(!isset($input['player_id']) || !$input['player_id'])
			$input['player_id'] = $this->player_model->getClientPlayerId($input['pb_player_id'], $validToken['site_id']);
		$input['action_log_id'] = $this->tracker_model->trackAction($input); //track action
		$client_id = $validToken['client_id'];
		$site_id = $validToken['site_id'];
		$domain_name = $validToken['domain_name'];
		if(!isset($input['site_id']) || !$input['site_id'])
			$input['site_id'] = $site_id;
		$ruleSet = $this->client_model->getRuleSetByActionId(array(
			'client_id' => $client_id,
			'site_id' => $site_id,
			'action_id' => $input['action_id']
		));
		$apiResult = array(
			'events' => array()
		);
		if(!$ruleSet)
		{
			$this->client_model->log($input); //log jigsaw
			return $apiResult;
		}
		foreach($ruleSet as $rule)
		{
			$input['rule_id'] = new MongoId($rule['rule_id']);
			$input['rule_name'] = $rule['name'];
//			$jigsawSet = unserialize($rule['jigsaw_set']);
			$jigsawSet = (isset($rule['jigsaw_set'])&& !empty($rule['jigsaw_set']))?$rule['jigsaw_set']:array();
			foreach($jigsawSet as $jigsaw)
			{
                try {
                    $jigsaw_id = new MongoId($jigsaw['id']);
                }catch (MongoException $ex) {
                    $jigsaw_id = "";
                }
				$input['jigsaw_id'] = $jigsaw_id;
				$input['jigsaw_name'] = $jigsaw['name'];
				$input['jigsaw_category'] = $jigsaw['category'];
				$input['jigsaw_index'] = $jigsaw['jigsaw_index'];
				if(isset($jigsaw['config']['action_id']) && !empty($jigsaw['config']['action_id']))
					$jigsaw['config']['action_id'] = new MongoId($jigsaw['config']['action_id']);
				if(isset($jigsaw['config']['reward_id']) && !empty($jigsaw['config']['reward_id']))
					$jigsaw['config']['reward_id'] = new MongoId($jigsaw['config']['reward_id']);
				if(isset($jigsaw['config']['item_id']) && !empty($jigsaw['config']['item_id']))
					$jigsaw['config']['item_id'] = new MongoId($jigsaw['config']['item_id']);
				$input['input'] = $jigsaw['config'];
				$exInfo = array();
				$jigsawConfig = $jigsaw['config'];
				//get class path to precess jigsaw
				$processor = $this->client_model->getJigsawProcessor($jigsaw_id, $site_id);
				if($this->jigsaw_model->$processor($jigsawConfig, $input, $exInfo))
				{
					if($jigsaw['category'] == 'REWARD')
					{
						if(isset($exInfo['dynamic']))
						{
							//reward is a custom point
							assert('$exInfo["dynamic"]["reward_name"]');
							assert('$exInfo["dynamic"]["quantity"]');
							$lv = $this->client_model->updateCustomReward($exInfo['dynamic']['reward_name'], $exInfo['dynamic']['quantity'], $input, $jigsawConfig);
							$event = array(
								'event_type' => 'REWARD_RECEIVED',
								'reward_type' => $jigsawConfig['reward_name'],
								'value' => $jigsawConfig['quantity']
							);
							array_push($apiResult['events'], $event);
							$eventMessage = $this->utility->getEventMessage('point', $jigsawConfig['quantity'], $jigsawConfig['reward_name']);
							//log event - reward, custom point
							$this->tracker_model->trackEvent('REWARD', $eventMessage, array_merge($input, array(
								'reward_id' => $jigsawConfig['reward_id'],
								'reward_name' => $jigsawConfig['reward_name'],
								'amount' => $jigsawConfig['quantity']
							)));
							//publish to node stream
							$this->node->publish(array_merge($input, array(
								'message' => $eventMessage,
								'amount' => $jigsawConfig['quantity'],
								'point' => $jigsawConfig['reward_name']
							)), $domain_name, $site_id);
							//publish to facebook notification
							if($fbData)
								$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
							if($lv > 0)
							{
								$eventMessage = $this->levelup($lv, $apiResult, $input);
								//publish to node stream
								$this->node->publish(array_merge($input, array(
									'message' => $eventMessage,
									'level' => $lv
								)), $domain_name, $site_id);
								//publish to facebook notification
								if($fbData)
									$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
							}
						}
						else if(is_null($jigsawConfig['item_id']) || $jigsawConfig['item_id'] == '')
						{
							//item_id is null, process standard point-based rewards (exp, point)
							if($jigsawConfig['reward_name'] == 'exp')
							{
								//check if player level up
								$lv = $this->client_model->updateExpAndLevel($jigsawConfig['quantity'], $input['pb_player_id'], $input['player_id'], array(
									'client_id' => $validToken['client_id'],
									'site_id' => $validToken['site_id']
								));
								if($lv > 0)
								{
									$eventMessage = $this->levelup($lv, $apiResult, $input);
									//publish to node stream
									$this->node->publish(array_merge($input, array(
										'message' => $eventMessage,
										'level' => $lv
									)), $domain_name, $site_id);
									//publish to facebook notification
									if($fbData)
										$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
								}
							}
							else
							{
								//update point-based reward
								$this->client_model->updatePlayerPointReward($jigsawConfig['reward_id'], $jigsawConfig['quantity'], $input['pb_player_id'], $input['player_id'], $input['client_id'], $input['site_id']);
							}
							$event = array(
								'event_type' => 'REWARD_RECEIVED',
								'reward_type' => $jigsawConfig['reward_name'],
								'value' => $jigsawConfig['quantity']
							);
							array_push($apiResult['events'], $event);
							$eventMessage = $this->utility->getEventMessage('point', $jigsawConfig['quantity'], $jigsawConfig['reward_name']);
							//log event - reward, non-custom point
							$this->tracker_model->trackEvent('REWARD', $eventMessage, array_merge($input, array(
								'reward_id' => $jigsawConfig['reward_id'],
								'reward_name' => $jigsawConfig['reward_name'],
								'amount' => $jigsawConfig['quantity']
							)));
							//publish to node stream
							$this->node->publish(array_merge($input, array(
								'message' => $eventMessage,
								'amount' => $jigsawConfig['quantity'],
								'point' => $jigsawConfig['reward_name']
							)), $domain_name, $site_id);
							//publish to facebook notification
							if($fbData)
								$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
						}
						else
						{
							switch($jigsawConfig['reward_name'])
							{
							case 'badge':
								$this->client_model->updateplayerBadge($jigsawConfig['item_id'], $jigsawConfig['quantity'], $input['pb_player_id'], $input['player_id'], $client_id, $site_id);
								$badgeData = $this->client_model->getBadgeById($jigsawConfig['item_id'], $site_id);
								if(!$badgeData)
									break;
								$event = array(
									'event_type' => 'REWARD_RECEIVED',
									'reward_type' => $jigsawConfig['reward_name'],
									'reward_data' => $badgeData,
									'value' => $jigsawConfig['quantity']
								);
								array_push($apiResult['events'], $event);
								$eventMessage = $this->utility->getEventMessage($jigsawConfig['reward_name'], '', '', $event['reward_data']['name']);
								//log event - reward, badge
								$this->tracker_model->trackEvent('REWARD', $eventMessage, array_merge($input, array(
									'reward_id' => $jigsawConfig['reward_id'],
									'reward_name' => $jigsawConfig['reward_name'],
									'item_id' => $jigsawConfig['item_id'],
									'amount' => $jigsawConfig['quantity']
								)));
								//publish to node stream
								$this->node->publish(array_merge($input, array(
									'message' => $eventMessage,
									'badge' => $event['reward_data']
								)), $domain_name, $site_id);
								//publish to facebook notification
								if($fbData)
									$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
								break;
							default:
								break;
							}
						}
						//log jigsaw - reward 
						$this->client_model->log($input, $exInfo);
					}
					else
					{
						//check for completed objective
						if(isset($exInfo['objective_complete']))
						{
							$objId = $exInfo['objective_complete']['id'];
							$objName = $exInfo['objective_complete']['name'];
							$this->player_model->completeObjective($input['pb_player_id'], new MongoId($objId), $client_id, $site_id);
							$event = array(
								'event_type' => 'OBJECTIVE_COMPLETE',
								'objective_id' => $objId,
								'objective_name' => $objName
							);
							array_push($apiResult['events'], $event);
							$eventMessage = $this->utility->getEventMessage('objective', '', '', '', '', $objName);
							//log event - objective complete
							$this->tracker_model->trackEvent('OBJECTIVE_COMPLETE', $eventMessage, array_merge($input, array(
								'objective_id' => $objId,
								'objective_name' => $objName,
							)));
							//publish to node stream
							$this->node->publish(array_merge($input, array(
								'message' => $eventMessage,
								'objective' => array(
									'id' => $objId,
									'name' => $objName
								)
							)), $domain_name, $site_id);
							//publish to facebook notification
							if($fbData)
								$this->social_model->sendFacebookNotification($client_id, $site_id, $fbData['facebook_id'], $eventMessage, '');
						}
						//log jigsaw - condition or action
						$this->client_model->log($input, $exInfo);
						continue;
					}
				}
				else //jigsaw return false
				{
					if($jigsaw['category'] == 'REWARD')
					{
						continue;
					}
					else
					{
						//log jigsaw - condition or action
						$this->client_model->log($input, $exInfo);
						break;
					}
				}
			}
		}
		return $apiResult;
	}
	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
		);
		$token = $this->auth_model->getApiInfo($credential);
		//echo '<br>getPlayerInfo (node_stream)<br>';
		//$pb_player_id = $this->player_model->getPlaybasisId(array(
		//	'site_id' => $token['site_id'],
		//	'client_id' => $token['client_id'],
		//	'cl_player_id' => '1'
		//));
		//$result = $this->node->getPlayerInfo($pb_player_id, $token['site_id']);
		//print_r($result);
		//echo '<br>getMostRecentJigsaw (jigsaw)<br>';
		//$rule_id = new MongoId('51f1b3506d6cfb64170e81cd');
		//$jigsaw_id = new MongoId('51f120906d6cfb64170000b4');
		//$result = $this->jigsaw_model->getMostRecentJigsaw(array(
		//	'site_id' => $token['site_id'],
		//	'pb_player_id' => $pb_player_id,
		//	'rule_id' => $rule_id,
		//	'jigsaw_id' => $jigsaw_id
		//), array(
		//	'input',
		//	'date_added'
		//));
		//print_r($result);
		//echo '<br>checkBadge (jigsaw)<br>';
		//$badge_id = new MongoId('51f120906d6cfb641700001f');
		//$result = $this->jigsaw_model->checkBadge($badge_id, $pb_player_id, $token['site_id']);
		//var_dump($result);
		//echo '<br>checkReward (jigsaw)<br>';
		//$reward_id = $this->point_model->findPoint(array_merge($token, array('reward_name'=>'point')));
		//$result = $this->jigsaw_model->checkReward($reward_id, $token['site_id']);
		//var_dump($result);
		echo '</pre>';
	}
}
?>