<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Engine extends REST_Controller{
	public function __construct(){
		parent::__construct();

		//model
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('client_model');
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

	public function rule_get(){
		//$required = $this->input->checkParam(array('token'));

		//if($required)
			//$this->response($this->error->setError('TOKEN_REQUIRED',$required),200);

		//$required = $this->input->checkParam(array('action','player_id'));
		
		//if($required){
			//$this->response($this->error->setError('PARAMETER_MISSING',$required),200);
		//}
		
		

		//validate token
		//$validToken = $this->auth_model->findToken(array('token'=>$this->input->post('token')));
		$validToken = array('client_id'=>1,'site_id'=>1); //for debugging
		
		if(!$validToken)
			$this->response($this->error->setError('INVALID_TOKEN'),200);
		
		//get playbasis player id
		//$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken,array('cl_player_id'=>$this->input->post('player_id'))));

		//if($pb_player_id < 0){
			//$this->response($this->error->setError('USER_NOT_EXIST'),200);
		//}
		 
		//get action id by action name
		$actionId = $this->client_model->getActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_name'=> 'want'/*$this->input->post('action')*/));
		if(!$actionId){
			$this->response($this->error->setError('ACTION_NOT_FOUND'),200);
		}
		
		//get input data from POST
		
		//get rule related to action id
		$ruleSet = $this->client_model->getRuleSetByActionId(array('client_id'=>$validToken['client_id'],'site_id'=>$validToken['site_id'],'action_id'=>$actionId));
		
		//misc data  : use for log and process any jigsaws
		$input = array_merge($this->input->post(),$validToken,array('pb_player_id'=>1/*$pb_player_id*/));
		//var_dump($ruleSet);	
		foreach($ruleSet as $rule){
			$input['rule_id'] = $rule['rule_id'];
			$jigsawSet = unserialize($rule['jigsaw_set']);
			
			foreach($jigsawSet as $jigsaw){
				$input['jigsaw_id'] = $jigsaw['id'];
				$exInfo = array();
				//get class path to precess jigsaw
				$processor = $this->client_model->getJigsawProcessor($jigsaw['id']);
				
				if($processor($jigsaw['config'],$input,$exInfo)){
					if($jigsaw['category'] == 'REWARD'){
						if(is_null($jigsaw['config']['item_id'])){
							if($jigsaw['config']['reward_name'] == 'exp' ) {
								#got level here if player level up
								$lv = $this->client_info->updateExpAndLevel($jigsaw['config']['quantity'],$input['pb_player_id']);
							}
							else{
								$this->client_model->updatePlayerpointReward($jigsaw['config']['reward_id'],$jigsaw['config']['quantity'],$input['pb_player_id']);
							}//update reward [type point]
						}
						else{
							switch($jigsaw['config']['reward_name']){
								case 'badge' :
									$this->client_model->updateplayerBadge($jigsaw['config']['item_id'],$jigsaw['config']['quantity'],$input['pb_player_id']);
									break;
								default :
									break;
							}
						}
						//log
						$this->client_model->log($input);
					}
					else{
						//log
						$this->client_model->log($input);
						continue;
					}
				}
				else{ // jigsaw return false
					if($jigsaw['category'] == 'REWARD'){
						continue;
					}
					else{
						//log
						$this->client_model->log($input);
						break;
					}
				}
				
			}//foreach jigsaw
			
		}//foreach rule
			
	}
	
}
?>