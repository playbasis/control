<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Engine extends REST_Controller{
	public function __construct(){
		parent::__construct();

		//model
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('engine/jigsaw','jigsaw_model');
		$this->load->model('client_model');
		$this->load->model('tracker_model');
		$this->load->model('tool/error','error');
		$this->load->model('tool/utility','utility');
		$this->load->model('tool/respond','resp');
		$this->load->model('tool/node_stream','node');
		$this->load->model('social_model');
		

		//library
		$this->load->library('mongo_db');


		//config
	}
	
	//get initial information for client site script
	public function getActionConfig_post(){
		
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		//validate token
		$validToken = $this->auth_model->findToken($this->input->post('token'));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		// $validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		//get jigsaw set
		$ruleSet = $this->client_model->getRuleSet(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id']));
		
		$actionConfig = array();
		foreach($ruleSet as $rule){
			$jigsawSet = unserialize($rule['jigsaw_set']);
		
			$actionId = $jigsawSet[0]['config']['action_id'];
			$actionInput = $jigsawSet[0]['config'];
			if(isset($actionConfig[$actionId])){
				$config = array(
					'action_target'	=> $actionInput['action_target'],
					'object_target'	=> $actionInput['object_target'],
				);
				$found = false;
				foreach($actionConfig[$actionId]['config'] as $configElement){
					if($config['action_target'] != $configElement['action_target'] || $config['object_target'] != $configElement['object_target'])
						continue;
					$found = true;
					break;
				}
				if(!$found)
					array_push($actionConfig[$actionId]['config'],$config);
			}
			else{
				$actionConfig[$actionId] = array(
					'name'	=> $jigsawSet[0]['config']['action_name'],
					'config'=>	array(
							array(
								'action_target'	=> $actionInput['action_target'],
								'object_target'	=> $actionInput['object_target'],		
							),
					),
				);	
			}
		}
		
		$this->response($this->resp->setRespond($actionConfig),200);
		
	}

	public function rule_get($option=0){
		if($option != 'facebook'){
			$this->response($this->error->setError('ACCESS_DENIED'),200);
		}
		$challenge = $this->input->get('hub_challenge');
		echo $challenge;
	}

	public function rule_post($option=0){

		$this->benchmark->mark('engine_rule_start');
		
		$this->mongo_db->insert('rule_log', array('option'=>$option, 'date_added'=>date('Y-m-d H:i:s'), 'date_modified'=>date('Y-m-d H:i:s')));
		
		$fbData = null;
		$twData = null;
		
		if($option == 'facebook'){
			$fbData = $this->social_model->processFacebookData($this->request->body);
			
			if(!$fbData['pb_player_id'])
				$this->response($this->error->setError('USER_NOT_EXIST'),200);

			if(!$fbData['action'])
				$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
		}
		else if($option == 'twitter'){
			$twData = $this->social_model->processTwitterData($this->request->body);
		}
		
		if($fbData){
			
			//process facebook data
			$validToken = $this->auth_model->createToken($fbData['client_id'], $fbData['site_id']);
			if(!$validToken)
				$this->response($this->error->setError('INVALID_TOKEN'),200);
			
			$pb_player_id = $fbData['pb_player_id'];
			if($pb_player_id < 0)
				$this->response($this->error->setError('USER_NOT_EXIST'),200);
			
			$actionName = $fbData['action'];
			$actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=>$actionName));
			if(!$actionId)
				$this->response($this->error->setError('ACTION_NOT_FOUND'),200);

			$input = array_merge($validToken,array('pb_player_id'=>$pb_player_id,'action_id'=>$actionId,'action_name'=>$actionName));	
			$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
		}
		else if($twData){
			
			//proces twitter data
			$apiResult = null;
			foreach($twData['sites'] as $site){
				
				$validToken = $this->auth_model->createToken($site['client_id'], $site['site_id']);
				if(!$validToken)
					continue;
				
				$pb_player_id = $site['pb_player_id'];
				if($pb_player_id < 0)
					continue;
				
				$actionName = $twData['action'];
				$actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=>$actionName));
				if(!$actionId)
					continue;
				
				$input = array_merge($validToken,array('pb_player_id'=>$pb_player_id,'action_id'=>$actionId,'action_name'=>$actionName));				
				$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
			}
		}
		else{
			//process regular data
			$required = $this->input->checkParam(array('token'));
			if($required)
				$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

			$required = $this->input->checkParam(array('action','player_id'));
			if($required)
				$this->response($this->error->setError('PARAMETER_MISSING',$required),200);

			//validate token
			$validToken = $this->auth_model->findToken($this->input->post('token'));
			// $validToken = array('client_id'=>1,'site_id'=>1,'domain_name'=>"https://pbapp.net/demo",'site_name'=>'playbasis demo site'); //for debugging
			if(!$validToken)
				$this->response($this->error->setError('INVALID_TOKEN'),200);
			
			//get playbasis player id
			$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$this->input->post('player_id'))));
			// $pb_player_id = $this->input->get('player_id'); //for debugging
			if($pb_player_id < 0)
				$this->response($this->error->setError('USER_NOT_EXIST'),200);
			
			//get action id by action name
			$actionName = $this->input->post('action');
			$actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=>$actionName));
			// $actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=>$this->input->get('action')));  //for debugging
			if(!$actionId)
				$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
			
			$postData = $this->input->post();
			$input = array_merge($postData,$validToken,array('pb_player_id'=>$pb_player_id,'action_id'=>$actionId,'action_name'=>$actionName));	
			$apiResult = $this->processRule($input, $validToken, $fbData, $twData);
		}
		
		$this->benchmark->mark('engine_rule_end');
		$apiResult['processing_time'] = $this->benchmark->elapsed_time('engine_rule_start', 'engine_rule_end');

		$this->response($this->resp->setRespond($apiResult),200);
	}
	
	private function processRule($input, $validToken, $fbData, $twData){

		//track action
		$input['action_log_id'] = $this->tracker_model->trackAction($input);

		//get rule related to action id
		$ruleSet = $this->client_model->getRuleSetByActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_id'=>$input['action_id']));
			
		//result array
		$apiResult = array('events'=>array());

		if(!$ruleSet){
			//log jigsaw
			$this->client_model->log($input);
			return $apiResult; //$this->response($this->resp->setRespond($apiResult),200);
		}
			
		foreach($ruleSet as $rule){
			$input['rule_id'] = $rule['rule_id'];
			$input['rule_name'] = $rule['name'];
			
			$jigsawSet = unserialize($rule['jigsaw_set']);
			
			foreach($jigsawSet as $jigsaw){
				$input['jigsaw_id']			= $jigsaw['id'];
				$input['jigsaw_name']		= $jigsaw['name'];
				$input['jigsaw_category']	= $jigsaw['category'];
				$input['input']				= $jigsaw['config'];
				$exInfo = array();
				
				$jigsawConfig = $jigsaw['config'];
				
				//get class path to precess jigsaw
				$processor = $this->client_model->getJigsawProcessor($jigsaw['id']);
				
				if($this->jigsaw_model->$processor($jigsawConfig,$input,$exInfo)){
					if($jigsaw['category'] == 'REWARD'){
						if(isset($exInfo['dynamic'])){
							//reward is a custom point
							assert('$exInfo["dynamic"]["reward_name"]');
							assert('$exInfo["dynamic"]["quantity"]');
							
							$this->client_model->updateCustomReward($exInfo['dynamic']['reward_name'],$exInfo['dynamic']['quantity'],$input,$jigsawConfig);
							
							$event = array(
								'event_type'	=>  'REWARD_RECEIVED',
								'reward_type'	=> 	$jigsawConfig['reward_name'],
								'value'			=>	$jigsawConfig['quantity'],
							);
							array_push($apiResult['events'],$event);
							
							$eventMessage = $this->utility->getEventMessage('point', $jigsawConfig['quantity'], $jigsawConfig['reward_name']);
							
							//log event :: reward > custom point
							$this->tracker_model->trackEvent('REWARD',$eventMessage,array_merge($input,array('reward_id'=>$jigsawConfig['reward_id'],'reward_name'=>$jigsawConfig['reward_name'],'amount'=>$jigsawConfig['quantity'])));
							
							//node stream
							$this->node->publish(array_merge($input,array('message'=>$eventMessage, 'amount'=>$jigsawConfig['quantity'], 'point'=>$jigsawConfig['reward_name'])),$input);
							if($fbData){
								$this->social_model->sendFacebookNotification($fbData['facebook_id'], $eventMessage, '');
							}
						}
						else if(is_null($jigsawConfig['item_id'])){
							//item_id is null, process standard point-based rewards (exp, point)
							if($jigsawConfig['reward_name'] == 'exp' ) {
								#got level here if player level up
								$lv = $this->client_model->updateExpAndLevel($jigsawConfig['quantity'],$input['pb_player_id'], array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id']));
								if($lv > 0){
									$event = array(
										'event_type'	=>  'LEVEL_UP',
										'value'			=>	$lv,
									);
									array_push($apiResult['events'],$event);

									$eventMessage = $this->utility->getEventMessage('level','','','',$lv);
									
									//log event :: level
									$this->tracker_model->trackEvent('LEVEL',$eventMessage,array_merge($input,array('amount'=>$lv)));

									//node stream
									$this->node->publish(array_merge($input,array('message'=>$eventMessage, 'level'=>$lv)),$input);
									if($fbData){
										$this->social_model->sendFacebookNotification($fbData['facebook_id'], $eventMessage, '');
									}
								}
							}
							else{
								$this->client_model->updatePlayerPointReward($jigsawConfig['reward_id'],$jigsawConfig['quantity'],$input['pb_player_id'],$input['client_id'],$input['site_id']);
							}//update reward [type point]
							
							$event = array(
								'event_type'	=>  'REWARD_RECEIVED',
								'reward_type'	=> 	$jigsawConfig['reward_name'],
								'value'			=>	$jigsawConfig['quantity'],
							);
							array_push($apiResult['events'],$event);
							
							$eventMessage = $this->utility->getEventMessage('point', $jigsawConfig['quantity'], $jigsawConfig['reward_name']);
							
							//log event :: reward > non-custom point
							$this->tracker_model->trackEvent('REWARD',$eventMessage,array_merge($input,array('reward_id'=>$jigsawConfig['reward_id'],'reward_name'=>$jigsawConfig['reward_name'],'amount'=>$jigsawConfig['quantity'])));

							//node stream
							$this->node->publish(array_merge($input,array('message'=>$eventMessage, 'amount'=>$jigsawConfig['quantity'], 'point'=>$jigsawConfig['reward_name'])),$input);
							if($fbData){
								$this->social_model->sendFacebookNotification($fbData['facebook_id'], $eventMessage, '');
							}
						}
						else{
							switch($jigsawConfig['reward_name']){
								case 'badge' :
									$this->client_model->updateplayerBadge($jigsawConfig['item_id'],$jigsawConfig['quantity'],$input['pb_player_id']);
									$event = array(
										'event_type'	=>  'REWARD_RECEIVED',
										'reward_type'	=> 	$jigsawConfig['reward_name'],
										'reward_data'	=>  $this->client_model->getBadgeById($jigsawConfig['item_id']),
										'value'			=>	$jigsawConfig['quantity'],
									)	;
									array_push($apiResult['events'],$event);
									
									$eventMessage = $this->utility->getEventMessage($jigsawConfig['reward_name'], '','', $event['reward_data']['name']);
									
									//log event :: reward > badge
									$this->tracker_model->trackEvent('REWARD',$eventMessage,array_merge($input,array('reward_id'=>$jigsawConfig['reward_id'],'reward_name'=>$jigsawConfig['reward_name'],'item_id'=>$jigsawConfig['item_id'],'amount'=>$jigsawConfig['quantity'])));
									//node stream
									$this->node->publish(array_merge($input,array('message'=>$eventMessage, 'badge'=>$event['reward_data'])),$input);
									if($fbData){
										$this->social_model->sendFacebookNotification($fbData['facebook_id'], $eventMessage, '');
									}
									break;
								default :
									break;
							}
						}
						//log jigsaw :: reward 
						$this->client_model->log($input);
						
					}
					else{
						//log jigsaw :: condition or action
						$this->client_model->log($input,$exInfo);
						continue;
					}
				}
				else{ // jigsaw return false
					if($jigsaw['category'] == 'REWARD'){
						continue;
					}
					else{
						//log jigsaw :: condition or action
						$this->client_model->log($input,$exInfo);
						break;
					}
				}
			}
		}
		return $apiResult; //$this->response($this->resp->setRespond($apiResult),200);	
	}
	
}
?>