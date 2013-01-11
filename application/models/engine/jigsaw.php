<?php defined('BASEPATH') OR exit('No direct script access allowed');
class jigsaw extends CI_Model{

	public function __construct(){
		parent::__construct();

		//load model

	}
	
	//action jigsaw
	public function action($config,$input){
		
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['url']));
		
		//validate url
		if($config['url']){
			return (boolean) $this->matchUrl($input['url'],$config['url'],$config['regex']);
		}
		else{
			return true;
		}
	}

	//reward jigsaw
	public function reward($config,$input){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['reward_id']));
		assert(isset($config['reward_name']));
		assert(isset($config['item_id']));
		assert(isset($config['quantity']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);

		//always true if reward type is point
		if(is_null($config['item_id']))
			return true;


		//if reward type is badge
		switch ($config['reward_name']) {
			case 'badge':
				return $this->checkBadge($config['item_id'],$input['pb_player_id']);
			default:
				return false;
		}

	}
	
	//condition jigsaw : counter
	//if return true 
	public function counter($config,$input,&$exInfo){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['counter_value']));
		assert(isset($config['interval']));
		assert(isset($config['interval_unit']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);
		assert($input['rule_id']);
		assert($input['jigsaw_id']);
		
		//reset consider
		$this->db->select('input,date_added');
		$this->db->where($input);
		$this->db->order_by('date_added','desc');
		$result = $this->db->get('playbasis_history');
		
		if(!$result->row_array()){
			$exInfo['remaining_counter'] = (int)$config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return false;
		}

		$result = $result->row_array();
		$timeNow = date('Y-m-d H:i:s');		
		$log 		= unserialize($result['input']);
		$lastTime	= $result['date_added'];

		$timeDiff = ($log['interval_unit']) == 'second' ? (int)(strtotime($timeNow)-strtotime($lastTime)) : (int)(date_diff( new DateTime() , new DateTime($lastTime))->d);

		$resetUnit = ($log['interval_unit'] != $config['interval_unit']); 
		$reset = $timeDiff > (int)$log['remaining_time'];

		if($resetUnit || $reset)
		{
			$exInfo['remaining_counter'] = (int)$config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return false;
		}
		
		if((int)$log['remaining_counter'] == 0){
			$exInfo['remaining_counter'] = (int)$config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return true;
		}
		else{
			$exInfo['remaining_counter'] = (int)$log['remaining_counter'] - 1;
			$exInfo['remaining_time'] = (int)$log['remaining_time'] - $timeDiff;
			return false;	
		}
	}

	
	public function cooldown($config,$input,&$exInfo){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['cooldown']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);
		assert($input['rule_id']);
		assert($input['jigsaw_id']);

		// $this->db->select('input,date_added');
		// $this->db->where($input);
		// $this->db->order_by('date_added','desc');
		// $result = $this->db->get('playbasis_history');

		// if(!$result->row_array()){
		// 	$exInfo['remaining_cooldown'] = (int)$config['cooldown'];
		// 	return false;
		// }

		$result = array(
			'input'			=> 'a:2:{s:8:"cooldown";i:180;s:18:"remaining_cooldown";i:100;}',
			'date_added'	=> '2013-01-01 08:00:00',
		);

		//$result = $result->row_array();
		$timeNow = '2013-01-01 08:03:00'; //date('Y-m-d H:i:s');	
		$log 		= unserialize($result['input']);
		$lastTime	= $result['date_added'];	

		$timeDiff = (int)(strtotime($timeNow)-strtotime($lastTime));

		if($timeDiff > $log['remaining_cooldown']){
			$exInfo['remaining_cooldown'] = (int)$config['cooldown'];
			return true;
		}
		else{
			$exInfo['remaining_cooldown'] = (int)$log['remaining_cooldown'] - $timeDiff;
			return false;
		}
	}



























	//util :: badge checker
	public function checkbadge($badgeId,$pb_player_id){
		//get badge properties
		$this->db->select('stackable,substract,quantity');
		$this->db->where(array('badge_id'=>$badgeId));
		$result = $this->db->get('playbasis_badge');

		$badgeInfo = $result->row_array();
		
		//search badge own by player
		$this->db->where(array('badge_id'=>$badgeId,'pb_player_id'=>$pb_player_id));
		$this->db->from('playbasis_badge_to_player');
		$haveBadge = $this->db->count_all_results();
		
		if(!$badgeInfo['quantity'])
			return false;
		
		if($badgeInfo['stackable'])
			return true;
		
		if($haveBadge)
			return false;
		
		return true;
			
	}


	//util :: url matching
	public function matchUrl($inputUrl,$compareUrl,$isRegEx){
			

			$urlFragment = parse_url($inputUrl);

			//check posible index page
			if(!$urlFragment['path'])
				$inputUrl = '/';
			if($urlFragment['path'] == '/')
				$inputUrl = '/';
			if(preg_match('/\/index\.[a-zA-Z]{3,}$/', $urlFragment['path']))   // match all  "/index.*" 
			 	$inputUrl = '/';
			if(preg_match('/\/index\.[a-zA-Z]{3,}\/$/', $urlFragment['path']))   // match all  "/index.*/" 
			 	$inputUrl = '/';

			//check query
			if($urlFragment['query'])
				$inputUrl.= '?'.$urlFragment['query'];

			//check fragment
			if($urlFragment['fragment'])
				$inputUrl.= '#'.$urlFragment['fragment'];



			//compare url
			$match;
			if($isRegEx){
				$match = preg_match($compareUrl, $inputUrl);
			}
			else{
				$match = (string)$compareUrl === (string)$inputUrl;
			}
			
			return $match;
			//e.g.
			//inputurl domain/forum/hello-my-new-notebook
			//input domain/forum/test1234
			//url = domain/forum/(a-zA-Z0-9\_\-)+
	}



































	// public function findAction($action,$client){
	// 	assert($action != false);
	// 	assert('is_array($client)');
	// 	assert('!empty($client)');
		
	// 	$this->db->select('action_id');
	// 	$this->db->where(array('client_id'=>$client['client_id'],'site_id'=>$client['site_id'],'name'=>$action));

	// 	$result = $this->db->get('playbasis_action_to_client');

	// 	return $result->row_array(); 
	// }
}
?>