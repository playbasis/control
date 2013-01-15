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
		$this->load->model('tool/respond','resp');



		//library



		//config
	}
	
	//get initial information for client site script
	public function init_post(){
		
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		//validate token
		$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//$validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
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

	public function rule_post(){
		$required = $this->input->checkParam(array('token'));

		if($required)
			$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		$required = $this->input->checkParam(array('action','player_id'));
		
		if($required){
			$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		}
		
		

		//validate token
		$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));
		//$validToken = array('client_id'=>1,'site_id'=>1,'domain_name'=>"https://pbapp.net",'site_name'=>'playbasis demo site'); //for debugging
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//get playbasis player id
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$this->input->post('player_id'))));

		if($pb_player_id < 0){
			$this->response($this->error->setError('USER_NOT_EXIST'),200);
		}
		 
		//get action id by action name
		$actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=>$this->input->post('action')));
		if(!$actionId){
			$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
		}
		
		//get input data from POST
		
		//misc data  : use for log and process any jigsaws
		$input = array_merge(/*$this->input->get()*/$this->input->post(),$validToken,array('pb_player_id'=>$pb_player_id),array('action_id'=>$actionId,'action_name'=>$this->input->post('action')));
		
		


		//track action
		$this->tracker_model->trackAction($input);
		

		//get rule related to action id
		$ruleSet = $this->client_model->getRuleSetByActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_id'=>$actionId));
		
			
		//result array
		$apiResult = array('events'=>array());		
					
						
		if(!$ruleSet){
			//log
			$this->client_model->log($input);
			$this->response($this->resp->setRespond($apiResult),200);
		}
			
		//var_dump($ruleSet);	
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
						if(is_null($jigsawConfig['item_id'])){
							if($jigsawConfig['reward_name'] == 'exp' ) {
								#got level here if player level up
								$lv = $this->client_model->updateExpAndLevel($jigsawConfig['quantity'],$input['pb_player_id']);
								if($lv > 0){
									$event = array(
										'event_type'	=>  'LEVEL_UP',
										'value'			=>	$lv,
									);
									array_push($apiResult['events'],$event);		
								}
							}
							else{
								$this->client_model->updatePlayerpointReward($jigsawConfig['reward_id'],$jigsawConfig['quantity'],$input['pb_player_id']);
							}//update reward [type point]
							
							$event = array(
								'event_type'	=>  'REWARD_RECEIVED',
								'reward_type'	=> 	$jigsawConfig['reward_name'],
								'value'			=>	$jigsawConfig['quantity'],
							);
							array_push($apiResult['events'],$event);
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
										);
									array_push($apiResult['events'],$event);
									break;
								default :
									break;
							}
						}
						//log
						$this->client_model->log(array_merge($input,$exInfo));
					}
					else{
						//log
						$this->client_model->log($input,$exInfo);
						continue;
					}
				}
				else{ // jigsaw return false
					if($jigsaw['category'] == 'REWARD'){
						continue;
					}
					else{
						//log
						$this->client_model->log($input,$exInfo);
						break;
					}
				}
				
			}//foreach jigsaw
			
		}//foreach rule
		$this->response($this->resp->setRespond($apiResult),200);	
	}
	
}
?>