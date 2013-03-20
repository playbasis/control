<?php defined('BASEPATH') OR exit('No direct script access allowed');
class jigsaw extends CI_Model{

	public function __construct(){
		parent::__construct();

		$this->config->load('playbasis');
	}
	
	//action jigsaw
	public function action($config,$input,&$exInfo=array()){
		
		assert($config != false);
		assert(is_array($config));		
		//assert(isset($config['url']));
		
		//validate url
		if($config['url']){
			$input['url'] = urldecode($input['url']);
			$exInfo['input_url'] = $input['url'];			
			return (boolean) $this->matchUrl($input['url'],$config['url'],$config['regex']);
		}
		else{
			return true;
		}
	}

	//reward jigsaw
	public function reward($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['reward_id']));
		assert(isset($config['reward_name']));
		assert($config["item_id"] == null || isset($config["item_id"]));
		assert(isset($config['quantity']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);

		//always true if reward type is point
		if(is_null($config['item_id']))
			return $this->checkReward($config['reward_id'],$input['site_id']);


		//if reward type is badge
		switch ($config['reward_name']) {
			case 'badge':
				return $this->checkBadge($config['item_id'],$input['pb_player_id']);
			default:
				return false;
		}

	}
	
	//reward jigsaw :: dynamic reward
	public function customPointReward($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));
		
		$name = $config['reward_name'];
		$quan = $config['quantity'];
		
		if(!$name && isset($input['reward']) && $input['reward']){
			$name = $input['reward'];
		}
		if(!$quan && isset($input['quantity']) && $input['quantity']){
			$quan = $input['quantity'];			
		}
		
		$exInfo['dynamic']['reward_name'] = $name;	
		$exInfo['dynamic']['quantity'] = $quan;	
		
		return $name && $quan;
	}
	
	//condition jigsaw : counter
	//if return true 
	public function counter($config,$input,&$exInfo=array()){
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
		//$this->db->select('input,date_added');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('input','date_added'), 'input,date_added');
		
		if(!$result){//->row_array()){
			$exInfo['remaining_counter'] = (int)$config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return false;
		}

		//$result = $result->row_array();
		$timeNow = date('Y-m-d H:i:s');		
		$log 		= unserialize($result['input']);
		
		if($config['interval'] == 0){ //if config time = 0 reduce counter and return false
			
			$log['remaining_counter']  -= 1;
			if((int)$log['remaining_counter'] == 0){
				$exInfo['remaining_counter'] = (int)$config['counter_value'];
				$exInfo['remaining_time'] = -1;	//reset timer, timer won't go down until counter triggers again
				return true;
			}


			
			$exInfo['remaining_counter'] = $log['remaining_counter'];
			$exInfo['remaining_time'] = $config['interval'];
			return false;
		}

		if($config['interval'] != 0 && $log['remaining_time'] == 0){
			$exInfo['remaining_counter'] = $log['remaining_counter'] - 1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return false;
		}

		$lastTime	= $result['date_added'];

		$timeDiff = ($log['interval_unit']) == 'second' ? (int)(strtotime($timeNow)-strtotime($lastTime)) : (int)(date_diff( new DateTime() , new DateTime($lastTime))->d);

		$resetUnit = ($log['interval_unit'] != $config['interval_unit']);
		$remainingTime = $log['remaining_time']; 
		$reset = ($remainingTime >= 0) && ($timeDiff > $remainingTime);

		if($resetUnit || $reset)	//if reset start counter time and decrease counter  1 time
		{
			$exInfo['remaining_counter'] = (int)$config['counter_value']-1;
			$exInfo['remaining_time'] = (int)$config['interval'];
			return false;
		}
		
		$log['remaining_counter']  -= 1;
		
		if((int)$log['remaining_counter'] == 0){
			$exInfo['remaining_counter'] = (int)$config['counter_value'];
			$exInfo['remaining_time'] = -1;	//reset timer, timer won't go down until counter triggers again
			return true;
		}
		else{
			$exInfo['remaining_counter'] = $log['remaining_counter'];
			if(($remainingTime<0) || $config['reset_timeout'])
				$exInfo['remaining_time'] = (int)$config['interval'];
			else
				$exInfo['remaining_time'] = $remainingTime - $timeDiff;
			return false;	
		}
	}

	
	public function cooldown($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['cooldown']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);
		assert($input['rule_id']);
		assert($input['jigsaw_id']);

		//$this->db->select('input,date_added');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('input','date_added'), 'input,date_added');

		if(!$result){//->row_array()){
			$exInfo['remaining_cooldown'] = (int)$config['cooldown'];
			return true;
		}

		// $result = array(
		// 	'input'			=> 'a:2:{s:8:"cooldown";i:180;s:18:"remaining_cooldown";i:100;}',
		// 	'date_added'	=> '2013-01-01 08:00:00',
		// );

		//$result = $result->row_array();
		$timeNow = /*'2013-01-01 08:03:00';*/ date('Y-m-d H:i:s');	
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

	public function before($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['timestamp']));

		assert($input != false);
		assert(is_array($input));
		
		return ($config['timestamp'] > time());
	}
	
	public function after($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['timestamp']));

		assert($input != false);
		assert(is_array($input));
		
		return ($config['timestamp'] < time());
	}
	
	public function between($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		

		assert($input != false);
		assert(is_array($input));
		assert(isset($config['start_time']));
		assert(isset($config['end_time']));
		
		
		$start	= $config['start_time'];
		$end	= $config['end_time'];
		
		$start	= strtotime("1970-01-01 $start:00");
		$end	= strtotime("1970-01-01 $end:00");
		#check range across day
		if($end < $start)
			$end	= strtotime("1970-01-02 $end:00");
		$now	= strtotime("1970-01-01 ".date('H:i').":00");
		
		return ($start < $now && $now < $end);
	}
	
	public function daily($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		

		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		
		//$this->db->select('date_added');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('date_added'), 'date_added');
		
		if(!$result){//->num_rows()){
			
			return true;
			
			/*$settingTime = $config['time_of_day'];
			$settingTime = strtotime("1970-01-01 $settingTime:00");
			$currentTime = strtotime("1970-01-01 ".date('H:i').":00");
			
			return $currentTime > $settingTime; */
		}
		
		//$result = $result->row_array();
		$lastTime = $result['date_added'];
				
		$datediff = date_diff( new DateTime() , new DateTime($lastTime));
		
		//more than 2 day
		if($datediff->d > 1)
			return true;
		
		//same day
		if($datediff->d <=0)
			return false;
		
		//more than 1 day judge by time
		$settingTime = $config['time_of_day'];
		$settingTime = strtotime("1970-01-01 $settingTime:00");
		$currentTime = strtotime("1970-01-01 ".date('H:i').":00");
		
		return $currentTime > $settingTime;
		
	}
	
	public function weekly($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		

		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['day_of_week']));
		
		//$this->db->select('input');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('input'), 'input');
		
		if(!$result){//->num_rows()){
			$exInfo['next_trigger'] = strtotime("next ".$config['day_of_week']." ".$config['time_of_day']);
			return true;
		}	
		
		//$result = $result->row_array();
		$logInput = unserialize($result['input']);
		if(strtotime('now') >= $logInput['next_trigger']){
			$exInfo['next_trigger'] = strtotime("next ".$config['day_of_week']." ".$config['time_of_day']);			
			return true;
		}
		
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}	
	
	
	public function monthly($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));		

		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['date_of_month']));
		
		//$this->db->select('input');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('input'), 'input');
		
		if(!$result){//->num_rows()){
			
			$lastDateOfMonth = date('d',strtotime("last day of next month"));
			$exInfo['next_trigger'] = $config['date_of_month']  > $lastDateOfMonth?	strtotime("last day of next month".$config['time_of_day']) :
																					strtotime("first day of next month ".$config['time_of_day'])+($config['date_of_month']-1)*3600*24;
			return true;
		}	
		
		//$result = $result->row_array();
		$logInput = unserialize($result['input']);
		if(strtotime('now') >= $logInput['next_trigger']){
			$lastDateOfMonth = date('d',strtotime("last day of next month"));
			$exInfo['next_trigger'] = $config['date_of_month']  > $lastDateOfMonth?	strtotime("last day of next month".$config['time_of_day']) :
																					strtotime("first day of next month ".$config['time_of_day'])+($config['date_of_month']-1)*3600*24;		
			return true;
		}
		
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}
	
	public function everyNDays($config,$input,&$exInfo=array()){
		assert($config != false);
		assert(is_array($config));

		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['num_of_days']));
		
		//$this->db->select('input');
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		$result = $this->getMostRecentJigsaw($input, array('input'), 'input');
		
		if(!$result){//->num_rows()){
			$currentDate = new DateTime();
			$nextTrigger = $currentDate->modify("+".$config['num_of_days']." day");
			assert($nextTrigger);
			$time = explode(':',$config['time_of_day']);
			$nextTrigger->setTime($time[0],$time[1]);
			assert($nextTrigger);
			$exInfo['next_trigger'] = $nextTrigger->getTimestamp();
			return true;
		}
		
		//$result = $result->row_array();
		$logInput = unserialize($result['input']);
		if(time() >= $logInput['next_trigger']){
			
			$nextTrigger = new DateTime();
			$nextTrigger->setTimestamp($logInput['next_trigger']);
			assert($nextTrigger);
			$nextTrigger->modify("+".$config['num_of_days']." day");
			assert($nextTrigger);
			$exInfo['next_trigger'] = $nextTrigger->getTimestamp();
			return true;
		}
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}

	private function getMostRecentJigsaw($input, $fields, $fields_str){
		
		//$this->db->select($fields_str);
		//$this->db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
		//$this->db->order_by('date_added','desc');
		//$result = $this->db->get('playbasis_jigsaw_log');
		//$result = $result->row_array();
		
		//if($this->config->item('TEST_MONGO')){
			
			$this->mongo_db->select($fields);
			$this->mongo_db->where(array('pb_player_id'=>$input['pb_player_id'],'rule_id'=>$input['rule_id'],'jigsaw_id'=>$input['jigsaw_id']));
			$this->mongo_db->order_by(array('date_added'=>'desc'));
			$result = $this->mongo_db->get('jigsaw_log');
			return ($result) ? $result[0] : $result;
				
		//}
		//return $result;
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

	public function checkReward($rewardId,$siteId){
		$this->db->select('limit');
		$this->db->where(array('reward_id'=>$rewardId,'site_id'=>$siteId));
		$result = $this->db->get('playbasis_reward_to_client');
		
		assert($result->row_array());

		$result = $result->row_array();
		
		if(is_null($result['limit']))
			return true;

		return $result['limit'] > 0;
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
			if(isset($urlFragment['query']) && $urlFragment['query'])
				$inputUrl.= '?'.$urlFragment['query'];

			//check fragment
			if(isset($urlFragment['fragment']) && $urlFragment['fragment'])
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