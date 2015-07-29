<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class jigsaw extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
		$this->load->library('mongo_db');
	}
	public function action($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		if($config['url'])
		{
			if(!isset($input['url']))
				return false;
			//validate url
			$input['url'] = urldecode($input['url']);
			$exInfo['input_url'] = $input['url'];
//			return (boolean) $this->matchUrl($input['url'], $config['url'], $config['regex']);
			return (boolean) $this->matchUrl($input['url'], $config['url']);
		}
		return true;
	}
	public function reward($config, $input, &$exInfo = array())
	{
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
		if(is_null($config['item_id']) || $config['item_id'] == ''){
            return $this->checkReward($config['reward_id'], $input['site_id']);
        }

		//if reward type is badge
		switch($config['reward_name'])
		{
			case 'badge':
				return $this->checkBadge($config['item_id'], $input['pb_player_id'], $input['site_id']);
			default:
				return false;
		}
	}
	public function customPointReward($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		$name = $config['reward_name'];
		$quan = $config['quantity'];
		if(!$name && isset($input['reward']) && $input['reward'])
		{
			$name = $input['reward'];
		}
		if(!$quan && isset($input['quantity']) && $input['quantity'])
		{
			$quan = $input['quantity'];
		}
		$exInfo['dynamic']['reward_name'] = $name;
		$exInfo['dynamic']['quantity'] = $quan;
		return $name && $quan;
	}
    public function specialReward($config, $input, &$exInfo = array())
    {
        assert($config != false);
        assert(is_array($config));
        $name = $config['reward_name'];
        $quan = $config['quantity'];
        if(!$name && isset($input['reward']) && $input['reward'])
        {
            $name = $input['reward'];
        }
        if(!$quan && isset($input['quantity']) && $input['quantity'])
        {
            $quan = $input['quantity'];
        }
        $exInfo['dynamic']['reward_name'] = $name;
        $exInfo['dynamic']['quantity'] = $quan;
        return $name && $quan;
    }
	public function counter($config, $input, &$exInfo = array())
	{
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
		$result = $this->getMostRecentJigsaw($input, array(
			'input',
			'date_added'
		));
		if(!$result)
		{
			$exInfo['remaining_counter'] = (int) $config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int) $config['interval'];
			return false;
		}
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		$log = $result['input'];
		if($config['interval'] == 0) //if config time = 0 reduce counter and return false
		{
			$log['remaining_counter'] -= 1;
			if((int) $log['remaining_counter'] == 0)
			{
				$exInfo['remaining_counter'] = (int) $config['counter_value'];
				$exInfo['remaining_time'] = -1; //reset timer, timer won't go down until counter triggers again
				return true;
			}
			$exInfo['remaining_counter'] = $log['remaining_counter'];
			$exInfo['remaining_time'] = $config['interval'];
			return false;
		}
		if($config['interval'] != 0 && $log['remaining_time'] == 0)
		{
			$exInfo['remaining_counter'] = $log['remaining_counter'] - 1;
			$exInfo['remaining_time'] = (int) $config['interval'];
			return false;
		}
		$lastTime = $result['date_added'];
		$timeDiff = ($log['interval_unit']) == 'second' ? (int) ($timeNow - $lastTime->sec) : (int) (date_diff(new DateTime("@$timeNow"), new DateTime(datetimeMongotoReadable($lastTime)))->d);
		$resetUnit = ($log['interval_unit'] != $config['interval_unit']);
		$remainingTime = $log['remaining_time'];
		$reset = ($remainingTime >= 0) && ($timeDiff > $remainingTime);
		if($resetUnit || $reset) //if reset, start counter timer and decrease counter by 1
		{
			$exInfo['remaining_counter'] = (int) $config['counter_value'] - 1;
			$exInfo['remaining_time'] = (int) $config['interval'];
			return false;
		}
		$log['remaining_counter'] -= 1;
		if((int) $log['remaining_counter'] == 0)
		{
			$exInfo['remaining_counter'] = (int) $config['counter_value'];
			$exInfo['remaining_time'] = -1; //reset timer, timer won't go down until counter triggers again
			return true;
		}
		else
		{
			$exInfo['remaining_counter'] = $log['remaining_counter'];
			if(($remainingTime < 0) || $config['reset_timeout'])
				$exInfo['remaining_time'] = (int) $config['interval'];
			else
				$exInfo['remaining_time'] = $remainingTime - $timeDiff;
			return false;
		}
	}
	public function cooldown($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert(isset($config['cooldown']));
		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);
		assert($input['rule_id']);
		assert($input['jigsaw_id']);
		$result = $this->getMostRecentJigsaw($input, array(
			'input',
			'date_added'
		));
		if(!$result)
		{
			$exInfo['remaining_cooldown'] = (int) $config['cooldown'];
			return true;
		}
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		$log = $result['input'];
		$lastTime = $result['date_added'];
		$timeDiff = (int) ($timeNow - $lastTime->sec);
		if($timeDiff > $log['remaining_cooldown'])
		{
			$exInfo['remaining_cooldown'] = (int) $config['cooldown'];
			return true;
		}
		else
		{
			$exInfo['remaining_cooldown'] = (int) $log['remaining_cooldown'] - $timeDiff;
			return false;
		}
	}
	public function before($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert(isset($config['timestamp']));
		assert($input != false);
		assert(is_array($input));
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		return (strtotime($config['timestamp']) > $timeNow);
	}
	public function after($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert(isset($config['timestamp']));
		assert($input != false);
		assert(is_array($input));
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		return (strtotime($config['timestamp']) < $timeNow);
	}
	public function between($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['start_time']));
		assert(isset($config['end_time']));
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		$start = $config['start_time'];
		$end = $config['end_time'];
		$start = strtotime("1970-01-01 $start:00");
		$end = strtotime("1970-01-01 $end:00");
		//check time range that crosses to the next day
		if($end < $start)
			$end = strtotime("1970-01-02 $end:00");
		$now = strtotime("1970-01-01 " . date('H:i', $timeNow) . ":00");
		return ($start < $now && $now < $end);
	}
	public function daily($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		$result = $this->getMostRecentJigsaw($input, array(
			'date_added'
		));
		if(!$result)
			return true;
		$lastTime = $result['date_added'];
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		$datediff = date_diff(new DateTime("@$timeNow"), new DateTime(datetimeMongotoReadable($lastTime)));
		//if more than 2 day
		if($datediff->d > 1)
			return true;
		//if same day
		if($datediff->d <= 0)
			return false;
		//if more than 1 day, according to current time
		$settingTime = $config['time_of_day'];
		$settingTime = strtotime("1970-01-01 $settingTime:00");
		$currentTime = strtotime("1970-01-01 " . date('H:i', $timeNow) . ":00");
		return $currentTime > $settingTime;
	}
	public function weekly($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['day_of_week']));
		$result = $this->getMostRecentJigsaw($input, array(
			'input'
		));
		if(!$result)
		{
			$exInfo['next_trigger'] = strtotime("next " . $config['day_of_week'] . " " . $config['time_of_day']);
			return true;
		}
		$logInput = $result['input'];
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		if($timeNow >= $logInput['next_trigger'])
		{
			$exInfo['next_trigger'] = strtotime("next " . $config['day_of_week'] . " " . $config['time_of_day']);
			return true;
		}
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}
	public function monthly($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['day_of_month']));
		$result = $this->getMostRecentJigsaw($input, array(
			'input'
		));
		if(!$result)
		{
			$lastDateOfMonth = date('d', strtotime("last day of next month"));
			$exInfo['next_trigger'] = $config['date_of_month'] > $lastDateOfMonth ? strtotime("last day of next month" . $config['time_of_day']) : strtotime("first day of next month " . $config['time_of_day']) + ($config['date_of_month'] - 1) * 3600 * 24;
			return true;
		}
		$logInput = $result['input'];
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		if($timeNow >= $logInput['next_trigger'])
		{
			$lastDateOfMonth = date('d', strtotime("last day of next month"));
			$exInfo['next_trigger'] = $config['date_of_month'] > $lastDateOfMonth ? strtotime("last day of next month" . $config['time_of_day']) : strtotime("first day of next month " . $config['time_of_day']) + ($config['date_of_month'] - 1) * 3600 * 24;
			return true;
		}
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}
	public function everyNDays($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['time_of_day']));
		assert(isset($config['num_of_days']));
		$result = $this->getMostRecentJigsaw($input, array(
			'input'
		));
		if(!$result)
		{
			$currentDate = new DateTime();
			$nextTrigger = $currentDate->modify("+" . $config['num_of_days'] . " day");
			assert($nextTrigger);
			$time = explode(':', $config['time_of_day']);
			$nextTrigger->setTime($time[0], $time[1]);
			assert($nextTrigger);
			$exInfo['next_trigger'] = $nextTrigger->getTimestamp();
			return true;
		}
		$logInput = $result['input'];
		$timeNow = isset($input['action_log_time']) ? $input['action_log_time'] : time();
		if($timeNow >= $logInput['next_trigger'])
		{
			$nextTrigger = new DateTime();
			$nextTrigger->setTimestamp($logInput['next_trigger']);
			assert($nextTrigger);
			$nextTrigger->modify("+" . $config['num_of_days'] . " day");
			assert($nextTrigger);
			$exInfo['next_trigger'] = $nextTrigger->getTimestamp();
			return true;
		}
		$exInfo['next_trigger'] = $logInput['next_trigger'];
		return false;
	}
	public function objective($config, $input, &$exInfo = array())
	{
		assert($config != false);
		assert(is_array($config));
		assert($input != false);
		assert(is_array($input));
		assert(isset($config['objective_id']));
		$objective_id = $config['objective_id'];
		assert(is_string($objective_id));
		$this->set_site_mongodb($input['site_id']);
		//check if this objective has been completed
		$this->mongo_db->where(array(
			'objective_id'=> new MongoId($objective_id),
			'pb_player_id'=> $input['pb_player_id'],
			));
		$count = $this->mongo_db->count('playbasis_objective_to_player');
		if($count > 0)
			return true;
		//objective not yet completed, check prerequisites
		$this->mongo_db->select(array(
			'prerequisites',
			'name'
		));
		$this->mongo_db->where(array('_id'=> new MongoId($objective_id)));
		$result = $this->mongo_db->get('playbasis_objective');
		assert($result);
		$result = $result[0];
		$prereqs = $result['prerequisites'];
		$objName = $result['name'];
		foreach ($prereqs as $value)
		{
			$this->mongo_db->where(array(
				'objective_id'=> $value,
				'pb_player_id'=> $input['pb_player_id'],
			));
			$count = $this->mongo_db->count('playbasis_objective_to_player');
			if(!$count || ($count <= 0))
				return false; //prereq objective not complete, can't complete this objective
		}
		$exInfo['objective_complete'] = array(
			'id' => $objective_id,
			'name' => $objName
		);
		return true;
	}
	public function email($config, $input, &$exInfo = array())
	{
		return $this->feedback('email', $config, $input, $exInfo);
	}
	public function sms($config, $input, &$exInfo = array())
	{
		return $this->feedback('sms', $config, $input, $exInfo);
	}
	private function feedback($type, $config, $input, &$exInfo = array())
	{
		$this->set_site_mongodb($input['site_id']);
		$this->mongo_db->where('status', true);
		$this->mongo_db->where('site_id', $input['site_id']);
		$this->mongo_db->where('link', $type);
		$this->mongo_db->limit(1);
		if ($this->mongo_db->count('playbasis_feature_to_client') > 0) {
			$this->mongo_db->where('_id', new MongoId($config['template_id']));
			$this->mongo_db->where('status', true);
			$this->mongo_db->where('deleted', false);
			return $this->mongo_db->count('playbasis_'.$type.'_to_client') > 0;
		}
		return false;
	}
	public function random($config, $input, &$exInfo = array())
	{
		$this->set_site_mongodb($input['site_id']);
		$sum = 0;
		$acc = array();
		foreach ($config['group_container'] as $each) {
			$sum += intval($each['weight']);
			array_push($acc, $sum);
		}
		$max = $acc[count($acc)-1];
		$ran = rand(0, $max-1);
		foreach ($acc as $i => $value) {
			if ($ran < $value) {
				$exInfo['index'] = $i;
				$exInfo['break'] = false;
				$conf = $config['group_container'][$i];
				if (array_key_exists('reward_name', $conf)) {
					foreach (array('item_id', 'reward_id') as $field) {
						if (array_key_exists($field, $conf)) $conf[$field] = $conf[$field] ? new MongoId($conf[$field]) : null;
					}
					return $this->reward($conf, $input, $exInfo);
				} else if (array_key_exists('feedback_name', $conf)) {
					return $this->feedback($conf['feedback_name'], $conf, $input, $exInfo);
				}
				return false; // should not reach this line
			}
		}
		return false; // should not reach this line
	}
	public function sequence($config, $input, &$exInfo = array())
	{
		$this->set_site_mongodb($input['site_id']);
		$result = $this->getMostRecentJigsaw($input, array(
			'input'
		));
		$i = !$result || !isset($result['input']['index']) ? 0 : $result['input']['index']+1;
		$exInfo['index'] = $i;
		$exInfo['break'] = true; // generally, "sequence" will block
		if ($i > count($config['group_container'])-1) {
			$exInfo['index'] = $result['input']['index']; // ensure that "index" has not been changed
			if ($config['loop'] === 'false' || !$config['loop']) return false;
			$i = 0; // looping, reset to be starting at 0
			$exInfo['index'] = 0;
		}
		if ($i == count($config['group_container'])-1) $exInfo['break'] = false; // if this is last item in the sequence jigsaw, we allow the rule to process next jigsaw
		$conf = $config['group_container'][$i];
		if (array_key_exists('reward_name', $conf)) {
			foreach (array('item_id', 'reward_id') as $field) {
				if (array_key_exists($field, $conf)) $conf[$field] = $conf[$field] ? new MongoId($conf[$field]) : null;
			}
			return $this->reward($conf, $input, $exInfo);
		} else if (array_key_exists('feedback_name', $conf)) {
			return $this->feedback($conf['feedback_name'], $conf, $input, $exInfo);
		}
		return false; // should not reach this line
	}
	public function getMostRecentJigsaw($input, $fields)
	{
		assert(isset($input['site_id']));
		$this->set_site_mongodb($input['site_id']);
		$this->mongo_db->select($fields);
		$this->mongo_db->where(array(
			'pb_player_id' => $input['pb_player_id'],
			'rule_id' => $input['rule_id'],
			'jigsaw_id' => $input['jigsaw_id'],
			'jigsaw_index' => $input['jigsaw_index']
		));
		$this->mongo_db->order_by(array(
			'date_added' => 'desc'
		));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->get('jigsaw_log');
		//for backward compatibility, check again without jigsaw_index
		if(!$result)
		{
			$this->mongo_db->select($fields);
			$this->mongo_db->where(array(
				'pb_player_id' => $input['pb_player_id'],
				'rule_id' => $input['rule_id'],
				'jigsaw_id' => $input['jigsaw_id'],
			));
			$this->mongo_db->order_by(array(
				'date_added' => 'desc'
			));
			$this->mongo_db->limit(1);
			$result = $this->mongo_db->get('jigsaw_log');
		}
		return ($result) ? $result[0] : $result;
	}
	private function checkBadge($badgeId, $pb_player_id, $site_id)
	{
		//get badge properties
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'stackable',
			'substract',
			'quantity'));
		$this->mongo_db->where(array(
            'site_id' => $site_id,
			'badge_id' => $badgeId,
			'deleted' => false
		));
		$this->mongo_db->limit(1);
		$badgeInfo = $this->mongo_db->get('playbasis_badge_to_client');
		if(!$badgeInfo || !$badgeInfo[0])
			return false;
		$badgeInfo = $badgeInfo[0];
		if(!$badgeInfo['quantity'])
			return false;
		if($badgeInfo['stackable'])
			return true;
		//badge not stackable, check if player already have the badge
		$this->mongo_db->where(array(
			'badge_id' => $badgeId,
			'pb_player_id' => $pb_player_id
		));
		$haveBadge = $this->mongo_db->count('playbasis_reward_to_player');
		if($haveBadge)
			return false;
		return true;
	}
	private function checkReward($rewardId, $siteId)
	{
		$this->set_site_mongodb($siteId);
		$this->mongo_db->select(array('limit'));
		$this->mongo_db->where(array(
			'reward_id' => $rewardId,
			'site_id' => $siteId
		));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->get('playbasis_reward_to_client');
		if (!$result) return false;
		$result = $result[0];
		if(is_null($result['limit'])){
            return true;
        }

		return $result['limit'] > 0;
	}
//	private function matchUrl($inputUrl, $compareUrl, $isRegEx)
    private function matchUrl($inputUrl, $compareUrl)
	{
		// return (boolean) $this->matchUrl($input['url'], $config['url'], $config['regex']);

		$urlFragment = parse_url($inputUrl);
		//check posible index page
		if(!$urlFragment['path'])
			$inputUrl = '/';
		//if($urlFragment['path'] == '/')
		//	$inputUrl = '/';
		if(preg_match('/\/index\.[a-zA-Z]{3,}$/', $urlFragment['path'])) // match all "/index.*" 
			$inputUrl = '/';
		if(preg_match('/\/index\.[a-zA-Z]{3,}\/$/', $urlFragment['path'])) // match all "/index.*/" 
			$inputUrl = '/';
		//check query
		if(isset($urlFragment['query']) && $urlFragment['query'])
			$inputUrl .= '?' . $urlFragment['query'];
		//check fragment
		if(isset($urlFragment['fragment']) && $urlFragment['fragment'])
			$inputUrl .= '#' . $urlFragment['fragment'];
		//compare url
//		if($isRegEx){
//			if ($compareUrl == '*') $compareUrl = '.*'; // quick-fix for handling a case of '*' pattern
//			if(!preg_match('/^\//', $compareUrl))
//				$compareUrl = "/".$compareUrl;
//			if(!preg_match('/\/$/', $compareUrl))
//				$compareUrl = $compareUrl."/";
//			$match = preg_match($compareUrl, $inputUrl);
//		}else{
//			$match = (string) $compareUrl === (string) $inputUrl;
//		}

        $match = (string) $compareUrl === (string) $inputUrl;

		return $match;
		//e.g.
		//inputurl domain/forum/hello-my-new-notebook
		//input domain/forum/test1234
		//url = domain/forum/(a-zA-Z0-9\_\-)+
	}

	public function calculateFrequency($from=null, $to=null) {
		ini_set('memory_limit', -1);
		$date_added = array();
		if ($from) $date_added['$gt'] = $from;
		if ($to) $date_added['$lt'] = $to;
		$default = array('action_log_id' => array('$exists' => 1));
		$match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);
		$results = $this->mongo_db->aggregate('jigsaw_log',
			array(
				array(
					'$match' => $match
				),
				array(
					'$project' => array('client_id' => 1, 'site_id' => 1, 'action_log_id' => 1, 'rule_id' => 1, 'date_added' => 1)
				),
				array(
					'$group' => array('_id' => array('action_log_id' => '$action_log_id', 'rule_id' => '$rule_id'), 'n' => array('$sum' => 1), 'client_id' => array('$first' => '$client_id'), 'site_id' => array('$first' => '$site_id'), 'date_added' => array('$max' => '$date_added'))
				),
			)
		);
		return $results ? $results['result'] : array();
	}

	public function storeFrequency($data) {
		return $this->mongo_db->insert('jigsaw_log_precomp', array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'action_log_id' => $data['_id']['action_log_id'],
			'rule_id' => $data['_id']['rule_id'],
			'n' => $data['n'],
			'date_added' => $data['date_added'],
			'date_modified' => $data['date_added'],
		), array('w' => 0, 'j' => false));
	}

	public function getLastCalculateFrequencyTime() {
		$this->mongo_db->select(array('date_added'));
		$this->mongo_db->order_by(array('date_added' => -1));
		$this->mongo_db->limit(1);
		$results = $this->mongo_db->get('jigsaw_log_precomp');
		return $results ? $results[0]['date_added'] : array();
	}
}
?>