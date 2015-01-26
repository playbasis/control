<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once(APPPATH.'controllers/quest.php');

//class Engine extends REST2_Controller
class Engine extends Quest
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('action_model');
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
//					'regex' => $actionInput['regex']
				);
				$found = false;
				foreach($actionConfig[$actionId]['config'] as $configElement)
				{
//					if($config['url'] != $configElement['url'] || $config['regex'] != $configElement['regex'])
					if($config['url'] != $configElement['url'])
						continue;
					$found = true;
					break;
				}
				if(!$found)
					array_push($actionConfig[$actionId]['config'], $config);
			}
			else
			{
                $actionName = $this->client_model->getActionName(array(
                    'client_id' => $validToken['client_id'],
                    'site_id' => $validToken['site_id'],
                    'action_id' => new MongoId($jigsawSet[0]['config']['action_id'])
                ));
				$actionConfig[$actionId] = array(
					'name' => $actionName,
					'config' => array(
						array(
							'url' => $actionInput['url'],
//							'regex' => $actionInput['regex']
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

    /*
     * Get response from Quest but not edit
     * any database
     */
    public function quest_post()
    {
        $token = array(
            "client_id" => $this->input->post("client_id"),
            "site_id" => $this->input->post("site_id"),
            "domain_name" => NULL
        );
        $quest_id = $this->input->post("quest_id");

        $response =  $this->QuestProcess(NULL, $token, $quest_id);
		$this->response($this->resp->setRespond($response), 200);
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
//			$actionId = $this->client_model->getActionId(array(
//				'client_id' => $validToken['client_id'],
//				'site_id' => $validToken['site_id'],
//				'action_name' => $actionName
//			));
//            if(!$actionId)
//                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            $action = $this->client_model->getAction(array(
                'client_id' => $validToken['client_id'],
                'site_id' => $validToken['site_id'],
                'action_name' => $actionName
            ));
			if(!$action)
				$this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            $actionId = $action['action_id'];
            $actionIcon = $action['icon'];
			$input = array_merge($validToken, array(
				'pb_player_id' => $pb_player_id,
				'action_id' => $actionId,
				'action_name' => $actionName,
				'action_icon' => $actionIcon
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
//				$actionId = $this->client_model->getActionId(array(
//					'client_id' => $validToken['client_id'],
//					'site_id' => $validToken['site_id'],
//					'action_name' => $actionName
//				));
//				if(!$actionId)
//					continue;
                $action = $this->client_model->getAction(array(
                    'client_id' => $validToken['client_id'],
                    'site_id' => $validToken['site_id'],
                    'action_name' => $actionName
                ));
                if(!$action)
                    continue;
                $actionId = $action['action_id'];
                $actionIcon = $action['icon'];
                $input = array_merge($validToken, array(
                    'pb_player_id' => $pb_player_id,
                    'action_id' => $actionId,
                    'action_name' => $actionName,
                    'action_icon' => $actionIcon
                ));
				$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
			}
		}
		else
		{
            $test = $this->input->post("test");

            $required = NULL;
			//process regular data
            if (!$test)
                $required = $this->input->checkParam(array('token'));

			if($required)
				$this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);

            if (!$test)
                $required = $this->input->checkParam(array('action', 'player_id'));

			if($required)
				$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);

            if (!$test)
                $validToken = $this->auth_model->findToken($this->input->post('token'));
            else
                $validToken = array("client_id" => new MongoId($this->input->post("client_id")),
                                    "site_id" => new MongoId($this->input->post("site_id")),
                                    "domain_name" => NULL);

			if(!$validToken)
				$this->response($this->error->setError('INVALID_TOKEN'), 200);

			//get playbasis player id from client player id
            $pb_player_id = array();
            if (!$test) {
                $cl_player_id = $this->input->post('player_id');
                $pb_player_id = $this->player_model->getPlaybasisId(
                    array_merge($validToken, array( 'cl_player_id' => $cl_player_id)));
            }

            if(!$pb_player_id && !$test)
            {
                log_message('error', '[debug-case-pbapp_auto_user] player_id = '.$this->input->post('player_id').', action = '.$this->input->post('action'));
                //$this->response($this->error->setError('USER_NOT_EXIST'), 200);
                //create user with tmp data for this id
                $pb_player_id = $this->player_model->createPlayer(array_merge($validToken, array(
                    'player_id' => $cl_player_id,
                    'image' => $this->config->item('DEFAULT_PROFILE_IMAGE'),
                    'email' => 'pbapp_auto_user@playbasis.com',
                    'username' => $cl_player_id,
                    'first_name' => $cl_player_id,
                    'nickname' => $cl_player_id,
                )));
            }

            //get action id by action name
            $actionName = $this->input->post('action');
//            $actionId = $this->client_model->getActionId(array(
//                'client_id' => $validToken['client_id'],
//                'site_id' => $validToken['site_id'],
//                'action_name' => $actionName
//            ));
//            if(!$actionId)
//                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);

            $action = $this->client_model->getAction(array(
                'client_id' => $validToken['client_id'],
                'site_id' => $validToken['site_id'],
                'action_name' => $actionName
            ));
            if(!$action)
                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            $actionId = $action['action_id'];
            $actionIcon = $action['icon'];

            $postData = $this->input->post();
            $input = array_merge($postData, $validToken, array(
                'pb_player_id' => $pb_player_id,
                'action_id' => $actionId,
                'action_name' => $actionName,
                'action_icon' => $actionIcon
            ));
            if (!$test)
                $input["test"] = false;

            $apiResult = $this->processRule($input, $validToken, $fbData, $twData);
        }
        //Quest Process
        if (!$test){
            $apiQuestResult = $this->QuestProcess($pb_player_id, $validToken);
            $apiResult = array_merge($apiResult, $apiQuestResult);
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
		if(!isset($input['player_id']) || !$input['player_id']) {
			if (!$input["test"])
				$input['player_id'] = $this->player_model->getClientPlayerId(
			$input['pb_player_id'], $validToken['site_id']);
		}

		$headers = $this->input->request_headers();
		$action_time = array_key_exists('Date', $headers) ? strtotime($headers['Date']) : null;
		if (!$input["test"]) {
			$input['action_log_id'] = $this->tracker_model->trackAction($input, $action_time); //track action
			$input['action_log_time'] = $this->action_model->findActionLogTime($input['action_log_id']);
		}

		$client_id = $validToken['client_id'];
		$site_id = $validToken['site_id'];
		$domain_name = $validToken['domain_name'];

		if(!isset($input['site_id']) || !$input['site_id'])
			$input['site_id'] = $site_id;

		if ($input["test"])
			$ruleSet = $this->client_model->getRuleSetById($input["rule_id"]);
		else
			$ruleSet = $this->client_model->getRuleSetByActionId(array(
				'client_id' => $client_id,
				'site_id' => $site_id,
				'action_id' => $input['action_id']
			));

		$apiResult = array(
			'events' => array()
		);

		/* if no matched rules then log empty info in jigsaw_log */
		if(!$ruleSet) {
			if (!$input["test"]) $this->client_model->log($input);
			return $apiResult;
		}

		/* [rule usage] check rule usage against the associated plan */
		if (!$input["test"]) {
			$this->client_model->permissionCheck(
				$this->client_id,
				$this->site_id,
				"others",
				"rule"
			);
		}

		foreach($ruleSet as $rule) {
			/* [rule usage] init */
			$count = 0;
			$last_jigsaw = null;

			$input['rule_id'] = new MongoId($rule['rule_id']);
			$input['rule_name'] = $rule['name'];
			$input['rule_time'] = new MongoDate(time());
			$jigsawSet = (isset($rule['jigsaw_set']) && !empty($rule['jigsaw_set'])) ? $rule['jigsaw_set']: array();

			foreach($jigsawSet as $jigsaw) {
                try {
                    $jigsaw_id = new MongoId($jigsaw['id']);
                } catch (MongoException $ex) {
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

                if (!$input["test"])
                    $jigsaw_model = $this->jigsaw_model->$processor($jigsawConfig, $input, $exInfo);
                else
                    $jigsaw_model = true;

				/* [rule usage] increase rule usage counter if a reward is given on chunk-based basis */
				if ($jigsaw['category'] === 'REWARD' && $last_jigsaw !== 'REWARD') $count++;

				if($jigsaw_model) {
					if($jigsaw['category'] == 'REWARD') {
						if(isset($exInfo['dynamic'])) {
							//reward is a custom point
							assert('$exInfo["dynamic"]["reward_name"]');
							assert('$exInfo["dynamic"]["quantity"]');

                            if (!$input["test"])
                                $lv = $this->client_model->updateCustomReward(
                                    $exInfo['dynamic']['reward_name'],
                                    $exInfo['dynamic']['quantity'],
                                    $input,
                                    $jigsawConfig);

							$event = array(
								'event_type' => 'REWARD_RECEIVED',
								'reward_type' => $jigsawConfig['reward_name'],
								'value' => $jigsawConfig['quantity']
							);
							array_push($apiResult['events'], $event);

                            if (!$input["test"]) {
                                $eventMessage = $this->utility->getEventMessage(
                                    'point',
                                    $jigsawConfig['quantity'],
                                    $jigsawConfig['reward_name']);

                                //log event - reward, custom point
                                $this->tracker_model->trackEvent(
                                    'REWARD',
                                    $eventMessage,
                                    array_merge($input, array(
                                        'reward_id' => $jigsawConfig['reward_id'],
                                        'reward_name' => $jigsawConfig['reward_name'],
                                        'amount' => $jigsawConfig['quantity'])));

                                //publish to node stream
                                $this->node->publish(array_merge($input, array(
                                    'message' => $eventMessage,
                                    'amount' => $jigsawConfig['quantity'],
                                    'point' => $jigsawConfig['reward_name']
                                )), $domain_name, $site_id);

                                //publish to facebook notification
                                if($fbData)
                                    $this->social_model->sendFacebookNotification(
                                        $client_id,
                                        $site_id,
                                        $fbData['facebook_id'],
                                        $eventMessage,
                                        '');

                                if($lv > 0) {
                                    $eventMessage = $this->levelup($lv, $apiResult, $input);
                                    //publish to node stream
                                    $this->node->publish(array_merge($input, array(
                                        'message' => $eventMessage,
                                        'level' => $lv
                                    )), $domain_name, $site_id);
                                    //publish to facebook notification
                                    if($fbData)
                                        $this->social_model->sendFacebookNotification(
                                            $client_id,
                                            $site_id,
                                            $fbData['facebook_id'],
                                            $eventMessage,
                                            '');
                                }
                            }  // close if (!$input["test"])
                        } else if(is_null($jigsawConfig['item_id']) || $jigsawConfig['item_id'] == '') {
                            //item_id is null, process standard point-based rewards (exp, point)
                            if($jigsawConfig['reward_name'] == 'exp') {
                                //check if player level up
                                if (!$input["test"]) {
                                    $lv = $this->client_model->updateExpAndLevel(
                                        $jigsawConfig['quantity'],
                                        $input['pb_player_id'],
                                        $input['player_id'],
                                        array(
                                            'client_id' => $validToken['client_id'],
                                            'site_id' => $validToken['site_id']));
                                    if($lv > 0) {
                                        $eventMessage = $this->levelup($lv, $apiResult, $input);
                                        //publish to node stream
                                        $this->node->publish(array_merge($input, array(
                                            'message' => $eventMessage,
                                            'level' => $lv
                                        )), $domain_name, $site_id);
                                        //publish to facebook notification
                                        if($fbData)
                                            $this->social_model->sendFacebookNotification(
                                                $client_id,
                                                $site_id,
                                                $fbData['facebook_id'],
                                                $eventMessage,
                                                '');
                                    }
                                }  // close if (!$input["test"])
                            } else {
                                //update point-based reward
                                if (!$input["test"])
                                    $this->client_model->updatePlayerPointReward(
                                        $jigsawConfig['reward_id'],
                                        $jigsawConfig['quantity'],
                                        $input['pb_player_id'],
                                        $input['player_id'],
                                        $input['client_id'],
                                        $input['site_id']);
                            }  // close if ($jigsawConfig["reward_name"] == 'exp')

                            $event = array(
                                'event_type' => 'REWARD_RECEIVED',
                                'reward_type' => $jigsawConfig['reward_name'],
                                'value' => $jigsawConfig['quantity']);
                            array_push($apiResult['events'], $event);

                            if (!$input["test"]) {
                                $eventMessage = $this->utility->getEventMessage(
                                    'point',
                                    $jigsawConfig['quantity'],
                                    $jigsawConfig['reward_name']);
                                //log event - reward, non-custom point
                                $this->tracker_model->trackEvent(
                                    'REWARD',
                                    $eventMessage,
                                    array_merge($input, array(
                                        'reward_id' => $jigsawConfig['reward_id'],
                                        'reward_name' => $jigsawConfig['reward_name'],
                                        'amount' => $jigsawConfig['quantity'])));
                                //publish to node stream
                                $this->node->publish(array_merge($input, array(
                                    'message' => $eventMessage,
                                    'amount' => $jigsawConfig['quantity'],
                                    'point' => $jigsawConfig['reward_name']
                                )), $domain_name, $site_id);
                                //publish to facebook notification
                                if($fbData)
                                    $this->social_model->sendFacebookNotification(
                                        $client_id,
                                        $site_id,
                                        $fbData['facebook_id'],
                                        $eventMessage,
                                        '');
                            }  // close if (!$input["test"])
                        } else {
                            switch($jigsawConfig['reward_name']) {
                            case 'badge':
                                if (!$input["test"])
                                    $this->client_model->updateplayerBadge(
                                        $jigsawConfig['item_id'],
                                        $jigsawConfig['quantity'],
                                        $input['pb_player_id'],
                                        $input['player_id'],
                                        $client_id, $site_id);

                                $badgeData = $this->client_model->getBadgeById(
                                    $jigsawConfig['item_id'],
                                    $site_id);
                                if(!$badgeData)
                                    break;

                                $event = array(
                                    'event_type' => 'REWARD_RECEIVED',
                                    'reward_type' => $jigsawConfig['reward_name'],
                                    'reward_data' => $badgeData,
                                    'value' => $jigsawConfig['quantity']
                                );
                                array_push($apiResult['events'], $event);

                                if (!$input["test"]) {
                                    $eventMessage = $this->utility->getEventMessage(
                                        $jigsawConfig['reward_name'],
                                        '',
                                        '',
                                        $event['reward_data']['name']);
                                    //log event - reward, badge
                                    $this->tracker_model->trackEvent('REWARD', $eventMessage, array_merge($input, array(
                                        'reward_id' => $jigsawConfig['reward_id'],
                                        'reward_name' => $jigsawConfig['reward_name'],
                                        'item_id' => $jigsawConfig['item_id'],
                                        'amount' => $jigsawConfig['quantity'])));
                                    //publish to node stream
                                    $this->node->publish(array_merge($input, array(
                                        'message' => $eventMessage,
                                        'badge' => $event['reward_data']
                                    )), $domain_name, $site_id);
                                    //publish to facebook notification
                                    if($fbData)
                                        $this->social_model->sendFacebookNotification(
                                            $client_id,
                                            $site_id,
                                            $fbData['facebook_id'],
                                            $eventMessage,
                                            '');
                                    break;
                                }  // close if (!$input["test"])
                                break;
                            default:
                                log_message('error', 'Unknown reward: '.$jigsawConfig['reward_name']);
                                break;
                            }  // close switch($jigsawConfig['reward_name'])
                        }  // close if(isset($exInfo['dynamic']))

                        //log jigsaw - reward
                        if (!$input["test"])
                            $this->client_model->log($input, $exInfo);
                    } else {
                        //check for completed objective
                        /*if(isset($exInfo['objective_complete'])) {
                            $objId = $exInfo['objective_complete']['id'];
                            $objName = $exInfo['objective_complete']['name'];

                            if (!$input["test"])
                                $this->player_model->completeObjective(
                                    $input['pb_player_id'],
                                    new MongoId($objId),
                                    $client_id,
                                    $site_id);

							$event = array(
								'event_type' => 'OBJECTIVE_COMPLETE',
								'objective_id' => $objId,
								'objective_name' => $objName
							);
							array_push($apiResult['events'], $event);

                            if (!$input["test"]) {
                                $eventMessage = $this->utility->getEventMessage(
                                    'objective', '', '', '', '', $objName);

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
                                    $this->social_model->sendFacebookNotification(
                                        $client_id,
                                        $site_id,
                                        $fbData['facebook_id'],
                                        $eventMessage,
                                        '');
                            }  // close if (!$input["test"])
						}  // close if(isset($exInfo['objective_complete']))*/

						//log jigsaw - condition or action
                        if (!$input["test"])
                            $this->client_model->log($input, $exInfo);
					}  // close if($jigsaw['category'] == 'REWARD')
				} else {  // jigsaw return false
					if($jigsaw['category'] == 'REWARD') {
						continue;
					} else {
						//log jigsaw - condition or action
                        if (!$input["test"])
                            $this->client_model->log($input, $exInfo);
						break;
					}
				}  // close if($jigsaw_model)

				/* [rule usage] set last_jigsaw */
				$last_jigsaw = $jigsaw['category'];
			}  // close foreach($jigsawSet as $jigsaw)

			/* [rule usage] increase usage value on client's account */
			if (!$input["test"] && $count > 0) {
				$this->client_model->insertRuleUsage(
					$this->client_id,
					$this->site_id,
					$input['rule_id'],
					$input['pb_player_id'],
					$count
				);
				$this->client_model->permissionProcess(
					$this->client_id,
					$this->site_id,
					"others",
					"rule",
					$count
				);
			}
		}  // close foreach($ruleSet as $rule)
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
