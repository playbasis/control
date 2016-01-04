<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Service_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('mongo_db');
    }

    public function getEventById($site_id, $event_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where('_id', $event_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_event_log');
        return $results ? $results[0] : array();
    }

    public function getDateAddedOfEventById($site_id, $event_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('date_added'));
        $this->mongo_db->where('_id', $event_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_event_log');
        return $results ? $results[0]['date_added'] : array();
    }

    public function getRecentPoint($site_id, $reward_id, $pb_player_id, $offset, $limit, $show_login=false, $show_quest=false, $show_redeem=false, $show_quiz=false){

        $this->set_site_mongodb($site_id);

        $event_type = array('REWARD');

        if($show_login){
            if($reward_id){

                $reset = $this->getResetRewardEvent($site_id, $reward_id);

                if($reset){
                    $reset_time = array_values($reset);
                    $starttime = $reset_time[0];

                    $this->mongo_db->where('date_added', array('$gt' => $starttime));
                }

                $this->mongo_db->where('reward_id', $reward_id);
            }else{
                $reset = $this->getResetRewardEvent($site_id);

                if($reset){
                    $reset_where = array();
                    $reset_not_id = array();
                    foreach($reset as $k => $v){
                        $reset_not_id[] = new MongoId($k);
                        $reset_where[] = array('reward_id' => new MongoId($k), 'date_added' => array('$gte' => $v));
                    }
                    $reset_where[] = array('reward_id' => array('$nin' => $reset_not_id));

                    $this->mongo_db->where(array('$or' => $reset_where));
                }
            }

            $event_type[] = 'LOGIN';
        }else{
            if($reward_id){

                $reset = $this->getResetRewardEvent($site_id, $reward_id);

                if($reset){
                    $reset_time = array_values($reset);
                    $starttime = $reset_time[0];

                    $this->mongo_db->where('date_added', array('$gt' => $starttime));
                }

                $this->mongo_db->where('reward_id', $reward_id);
            }else{
                $reset = $this->getResetRewardEvent($site_id);

                if($reset){
                    $reset_where = array();
                    $reset_not_id = array();
                    foreach($reset as $k => $v){
                        $reset_not_id[] = new MongoId($k);
                        $reset_where[] = array('reward_id' => new MongoId($k), 'date_added' => array('$gte' => $v));
                    }
                    $reset_not_id[] = null;
                    $reset_where[] = array('reward_id' => array('$nin' => $reset_not_id));

                    $this->mongo_db->where(array('$or' => $reset_where));
                }else{
                    $this->mongo_db->where_ne('reward_id', null);
                }
            }

            $this->mongo_db->where_gt('value', 0);
        }

        if($show_redeem){
            $event_type[] = 'REDEEM';
        }

        $this->mongo_db->where_in('event_type', $event_type);

        if(!$show_quest){
            $this->mongo_db->where('quest_id', null);
        }

        if(!$show_quiz){
            $this->mongo_db->where('quiz_id', null);
        }

        if($pb_player_id){
            $this->mongo_db->where('pb_player_id', $pb_player_id);
        }

        $this->mongo_db->where('site_id', $site_id);

        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id', 'quest_id', 'mission_id', 'goods_id', 'event_type', 'quiz_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->order_by(array('date_added' => -1));

        $event_log = $this->mongo_db->get('playbasis_event_log');

        $events_output = array();

        foreach($event_log as $key => &$event){
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('_id', $event['pb_player_id']);
            $this->mongo_db->select(array(
                'cl_player_id',
                'username',
                'first_name',
                'last_name',
                'gender',
                'image',
                'exp',
                'level'));
            $this->mongo_db->select(array(), array('_id'));
            $player = $this->mongo_db->get('playbasis_player');

            $event['player'] = isset($player[0]) ? $player[0] : null;
            if(!$event['player']){
                unset($event_log[$key]);
                continue;
            }

            $action = $this->getActionLogDetail($event['action_log_id']);

            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
            if($action){
                $event['action_name'] = $action['action_name'];
                $event['string_filter'] = $action['url']."";
                $event['action_icon'] = $action['icon'];
            }
            if(isset($event['quest_id']) && $event['quest_id']){
                if(isset($event['mission_id']) && $event['mission_id']){
                    $event['action_name'] = 'mission_reward';
                }else{
                    $event['action_name'] = 'quest_reward';
                }
                $event['action_icon'] = 'fa-trophy';
            }
            if(isset($event['goods_id']) && $event['goods_id']){
                $event['action_name'] = 'redeem_goods';
                $event['action_icon'] = 'fa-gift';
            }
            if(isset($event['quiz_id']) && $event['quiz_id']){
                $event['action_name'] = 'quiz_reward';
                $event['action_icon'] = 'fa-bar-chart';
            }
            if($event['event_type'] == 'LOGIN'){
                $event['action_name'] = 'login';
                $event['action_icon'] = 'fa-sign-in';
            }
            unset($event['action_log_id']);
            unset($event['pb_player_id']);
            unset($event['quest_id']);
            unset($event['mission_id']);
            unset($event['goods_id']);
            unset($event['quiz_id']);
            unset($event['event_type']);

            $event['reward_id'] = $event['reward_id']."";

            if($event['reward_name'] == "badge"){

                $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
                $this->mongo_db->select(array(),array('_id'));
                $this->mongo_db->where(array(
                    'site_id' => $site_id,
                    'badge_id' => $event['item_id'],
                    'deleted' => false
                ));
                $this->mongo_db->limit(1);
                $result = $this->mongo_db->get('playbasis_badge_to_client');
                if(isset($result[0])){
                    $event['badge']['badge_id'] = $result[0]['badge_id']."";
                    $event['badge']['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
                    $event['badge']['name'] = $result[0]['name'];
                    $event['badge']['description'] = $result[0]['description'];
                    $event['badge']['hint'] = $result[0]['hint'];
                }

            }

            unset($event['item_id']);
            array_push($events_output, $event);
        }

        return $events_output;
    }

    public function getRecentActivities($site_id, $offset, $limit, $pb_player_id=null, $last_read_activity_id=null, $mode='all'){
        $this->set_site_mongodb($site_id);

        $last_read = $last_read_activity_id ? $this->getDateAddedOfEventById($site_id, new MongoId($last_read_activity_id)) : null;

        $reset = $this->getResetRewardEvent($site_id);
        if($reset){
            $reset_where = array();
            $reset_not_id = array();
            foreach($reset as $k => $v){
                $reset_not_id[] = new MongoId($k);
                $reset_where[] = array('reward_id' => new MongoId($k), 'date_added' => array('$gte' => $v));
            }
            $reset_where[] = array('reward_id' => array('$nin' => $reset_not_id));
            $this->mongo_db->where(array('$or' => $reset_where));
        }

        $event_type = array('REWARD', 'REDEEM', 'ACTION', 'LEVEL');
        if ($mode != 'all') {
            $event_type[] = 'SOCIAL';
        }
        $this->mongo_db->where_in('event_type', $event_type);

        if ($mode != 'all') {
            $this->mongo_db->where('pb_player_id', $pb_player_id);
        }

        $this->mongo_db->where('site_id', $site_id);

        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id', 'quest_id', 'mission_id', 'goods_id', 'quiz_id', 'action_name', 'url', 'event_type', 'event_id', 'from_pb_player_id'));
        $this->mongo_db->order_by(array('date_added' => -1));

        $event_log = $this->mongo_db->get('playbasis_event_log');

        $events_output = array();

        $ids = array();
        $date_added = null;
        foreach($event_log as $key => &$event){
            $ids[$event['_id'].""] = count($events_output);
            $date_added = $event['date_added'];
            if ($last_read) $event['have_read'] = $last_read->sec >= $date_added->sec;
            $event = $this->format($site_id, $event);
            if (isset($event['player']) && empty($event['player'])) unset($event_log[$key]);
            array_push($events_output, $event);
        }

        if($mode == 'all' && $ids){
            /* "socials" for counting number of like/comment for each livefeed event */
            $this->mongo_db->select(array('action_name', 'event_id'));
            $this->mongo_db->where('event_type', 'SOCIAL');
            $this->mongo_db->where_in('event_id', $this->getArrayKeysInMongoId($ids));
            $this->mongo_db->where_gte('date_added', $date_added);
            $results = $this->mongo_db->get('playbasis_event_log');
            $group = array();
            if ($results) foreach ($results as $event) {
                $key = $event['event_id']."";
                $event = $this->format($site_id, $event);
                if (!array_key_exists($key, $group)) $group[$key] = array();
                if (!array_key_exists($event['action_name'], $group[$key])) $group[$key][$event['action_name']] = 0;
                $group[$key][$event['action_name']]++;
            }
            foreach ($group as $key => $value) {
                $idx = $ids[$key];
                $events_output[$idx]['socials'] = $value;
            }

            /* count how many times a given player has like/comment each livefeed event */
            if($pb_player_id){
                $this->mongo_db->select(array('action_name', 'event_id'));
                $this->mongo_db->where('event_type', 'SOCIAL');
                $this->mongo_db->where_in('event_id', $this->getArrayKeysInMongoId($ids));
                $this->mongo_db->where('from_pb_player_id', $pb_player_id);
                $results = $this->mongo_db->get('playbasis_event_log');
                if ($results) foreach ($results as $event) {
                    $key = $event['event_id']."";
                    $idx = $ids[$key];
                    if (!array_key_exists($event['action_name'], $events_output[$idx])) $events_output[$idx][$event['action_name']] = 0;
                    $events_output[$idx][$event['action_name']]++;
                }
            }
        }

        return $events_output;
    }

    private function getArrayKeysInMongoId($arr) {
        $results = array();
        foreach (array_keys($arr) as $value) {
            array_push($results, new MongoId($value));
        }
        return $results;
    }

    private function format($site_id, $event) {
        if (isset($event['_id'])) {
            $event['id'] = $event['_id']."";
            unset($event['_id']);
        }

        if (isset($event['event_id'])) {
            $event['event_id'] = $event['event_id']."";
        }

        if (isset($event['date_added'])) {
            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
        }

        if (isset($event['pb_player_id'])) {
            $this->mongo_db->where('_id', $event['pb_player_id']);
            $this->mongo_db->select(array(
                'cl_player_id',
                'username',
                'first_name',
                'last_name',
                'gender',
                'image',
                'exp',
                'level'));
            $this->mongo_db->select(array(), array('_id'));
            $player = $this->mongo_db->get('playbasis_player');

            $event['player'] = isset($player[0]) ? $player[0] : null;
            unset($event['pb_player_id']);
        }

        if (isset($event['from_pb_player_id'])) {
            $this->mongo_db->where('_id', $event['from_pb_player_id']);
            $this->mongo_db->select(array(
                'cl_player_id',
                'username',
                'first_name',
                'last_name',
                'gender',
                'image',
                'exp',
                'level'));
            $this->mongo_db->select(array(), array('_id'));
            $player = $this->mongo_db->get('playbasis_player');

            $event['from_player'] = isset($player[0]) ? $player[0] : null;
            unset($event['from_pb_player_id']);
        }

        if (isset($event['action_log_id'])) {
            $actionAndStringFilter = $this->getActionLogDetail($event['action_log_id']);
            if($actionAndStringFilter){
                $event['action_name'] = $actionAndStringFilter['action_name'];
                $event['string_filter'] = $actionAndStringFilter['url']."";
                $event['action_icon'] = $actionAndStringFilter['icon'];
            }
            unset($event['action_log_id']);
        }

        if(isset($event['quest_id']) && $event['quest_id']){
            if(isset($event['mission_id']) && $event['mission_id']){
                $event['action_name'] = 'mission_reward';
            }else{
                $event['action_name'] = 'quest_reward';
            }
            $event['action_icon'] = 'fa-trophy';
            unset($event['quest_id']);
            unset($event['mission_id']);
        }
        if(isset($event['goods_id']) && $event['goods_id']){
            $event['action_name'] = 'redeem_goods';
            $event['action_icon'] = 'fa-gift';
            $event['goods'] = $this->getGoods(array('site_id' => $site_id, 'goods_id' => $event['goods_id']));
            unset($event['goods_id']);
        }
        if(isset($event['quiz_id']) && $event['quiz_id']){
            $event['action_name'] = 'quiz_reward';
            $event['action_icon'] = 'fa-bar-chart';
            unset($event['quiz_id']);
        }

        if(isset($event['url'])){
            switch ($event['action_name']) {
                case COMPLETE_QUEST_ACTION:
                    $quest_id = new MongoId($event['url']);
                    $event['quest'] = $this->getQuest(array('site_id' => $site_id, 'quest_id' => $quest_id));
                    unset($event['url']);
                    break;
                case COMPLETE_MISSION_ACTION:
                    $mission_id = new MongoId($event['url']);
                    $event['mission'] = $this->getMission(array('site_id' => $site_id, 'mission_id' => $mission_id));
                    $quest_id = $this->getQuestIdByMissionId(array('site_id' => $site_id, 'mission_id' => $mission_id));
                    $event['quest'] = $this->getQuest(array('site_id' => $site_id, 'quest_id' => $quest_id));
                    unset($event['url']);
                    break;
                case COMPLETE_QUIZ_ACTION:
                    $quiz_id = new MongoId($event['url']);
                    $event['quiz'] = $this->getQuiz($site_id, $quiz_id);
                    unset($event['url']);
                    break;
                default:
                    break;
            }
        }

        if (isset($event['reward_id'])) {
            $event['reward_id'] = $event['reward_id']."";

            if($event['reward_name'] == "badge"){
                $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
                $this->mongo_db->select(array(),array('_id'));
                $this->mongo_db->where(array(
                    'site_id' => $site_id,
                    'badge_id' => $event['item_id'],
                    'deleted' => false
                ));
                $this->mongo_db->limit(1);
                $result = $this->mongo_db->get('playbasis_badge_to_client');
                if(isset($result[0])){
                    $event['badge']['badge_id'] = $result[0]['badge_id']."";
                    $event['badge']['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
                    $event['badge']['name'] = $result[0]['name'];
                    $event['badge']['description'] = $result[0]['description'];
                    $event['badge']['hint'] = $result[0]['hint'];
                }
            }
        }

        unset($event['item_id']);
        //unset($event['event_type']);

        return $event;
    }

    public function getSocialActivitiesOfEventId($site_id, $event_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id', 'quest_id', 'mission_id', 'goods_id', 'event_type', 'quiz_id', 'action_name', 'url', 'from_pb_player_id'));
        $this->mongo_db->where('_id', $event_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_event_log');
        $resp = null;
        if ($results) {
            $resp = $this->format($site_id, $results[0]);
            $this->mongo_db->select(array('action_name', 'message', 'date_added', 'pb_player_id', 'from_pb_player_id'));
            $this->mongo_db->where('event_type', 'SOCIAL');
            $this->mongo_db->where('event_id', $event_id);
            $this->mongo_db->where_gte('date_added', $results[0]['date_added']);
            $results = $this->mongo_db->get('playbasis_event_log');
            if ($results) foreach ($results as &$event) {
                $event = $this->format($site_id, $event);
            }
            $resp['socials'] = $results;
        }
        return $resp;
    }

    private function getActionLogDetail($action_log_id){
        $this->mongo_db->select(array('action_name', 'url', 'client_id', 'site_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('_id', new MongoID($action_log_id));
        $returnThis = $this->mongo_db->get('playbasis_action_log');

        if($returnThis){
            $returnThis = $returnThis[0];

            $this->mongo_db->select(array('action_id', 'icon'));
            $this->mongo_db->where(array(
                'client_id' => $returnThis['client_id'],
                'site_id' => $returnThis['site_id'],
                'name' => $returnThis['action_name']
            ));
            $this->mongo_db->limit(1);
            $action = $this->mongo_db->get('playbasis_action_to_client');

            if($action){
                $returnThis['icon'] = $action[0]['icon'];
            }
        }else{
            return array();
        }

        return $returnThis;
    }

	public function findLatestAPIactivity($client_id, $site_id=0) {
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array('date_added'));
		$this->mongo_db->where(array('client_id' => $client_id));
		$this->mongo_db->order_by(array('date_added' => 'DESC'));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->get('playbasis_web_service_log');
		return $result ? $result[0]['date_added'] : null;
	}

    public function resetPlayerPoints($site_id, $client_id, $reward_id, $reward_name){
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('reward_id', $reward_id);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->set('claimed', 0);
        $this->mongo_db->set('redeemed', 0);
        $reward = $this->mongo_db->update_all('playbasis_reward_to_player');

        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> null,
            'client_id'		=> $client_id,
            'site_id'		=> $site_id,
            'event_type'	=> "RESET",
            'reward_id'		=> $reward_id,
            'reward_name'	=> $reward_name,
            /*'action_log_id' => null,
            'message'		=> null,
            'item_id'		=> null,
            'value'			=> null,
            'objective_id'	=> null,
            'objective_name'=> null,
            'goods_id'		=> null,
            'goods_name'	=> null,
            'goods_log_id'  => null,
            'quest_id'      => null,
            'mission_id'    => null,
            'quiz_id'       => null,*/
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }

    /*public function resetPlayerBadge($site_id, $client_id, $reward_id, $reward_name, $badge_id){
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('badge_id', $badge_id);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->set('claimed', 0);
        $this->mongo_db->set('redeemed', 0);
        $reward = $this->mongo_db->update_all('playbasis_reward_to_player');

        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> null,
            'client_id'		=> $client_id,
            'site_id'		=> $site_id,
            'event_type'	=> "RESET",
            'action_log_id' => null,
            'message'		=> null,
            'reward_id'		=> $reward_id,
            'reward_name'	=> $reward_name,
            'item_id'		=> $badge_id,
            'value'			=> null,
            'objective_id'	=> null,
            'objective_name'=> null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }*/

    public function listActiveClientsUsingAPI($days, $list_client_ids=null, $site_id=0) {
        $this->set_site_mongodb($site_id);
        $d = strtotime("-".$days." day");
        $this->mongo_db->where_gt('date_added', new MongoDate($d));
        if ($list_client_ids) $this->mongo_db->where_in('client_id', $list_client_ids);
        return $this->mongo_db->distinct('client_id', 'playbasis_web_service_log');
    }

    public function archive($m, $bucket, $folder, $pageSize=100) {
        $this->load->library('s3');

        $c = 0;

        /* find total "old" records to archive */
        $d = strtotime("-".$m." month");
        $this->mongo_db->where_lt('date_added', new MongoDate($d));
        $total = $this->mongo_db->count('playbasis_web_service_log');

        /* do paging over such records */
        while ($c < $total) {
            /* fetch the documents */
            $this->mongo_db->order_by(array('date_added' => 'ASC'));
            $this->mongo_db->limit($pageSize);
            $documents = $this->mongo_db->get('playbasis_web_service_log');

            /* upload to S3 */
            $_ids = array();
            foreach ($documents as $document) {
                $id = $document['_id'];
                $result = $this->s3->putObject(json_encode($document), $bucket, $folder.'/'.$id.'.json', S3::ACL_PRIVATE);
                if ($result) {
                    array_push($_ids, $id);
                }
            }

            /* remove the documents */
            $this->mongo_db->where_in('_id', $_ids);
            $this->mongo_db->delete_all_with_ids('playbasis_web_service_log');

            $c += count($_ids);

            print('> '.$c.'/'.$total."\n");
        }

        return $c;
    }

    public function getResetRewardEvent($site_id, $reward_id=null) {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array('reward_id','date_added'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('event_type', 'RESET');
        if ($reward_id) {
            $this->mongo_db->where('reward_id', $reward_id);
            $this->mongo_db->limit(1);
        }
        $this->mongo_db->order_by(array('date_added' => 'DESC')); // use 'date_added' instead of '_id'
        $results = $this->mongo_db->get('playbasis_event_log');
        $ret = array();
        if ($results){
            foreach ($results as $result) {
                $reward_id = $result['reward_id']->{'$id'};
                if (array_key_exists($reward_id, $ret)) continue;
                $ret[$reward_id] = $result['date_added'];
            }
        }

        return $ret;
    }

    /* copied from goods_model as model cannot call each other */
    public function getGoods($data)
    {
        //get goods id
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('goods_id','image','name','description','date_start','date_expire','sponsor'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            //'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'goods_id' => $data['goods_id'],
            //'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_goods_to_client');

        if(isset($result[0]['goods_id']))
        {
            $result[0]['goods_id'] = $result[0]['goods_id']."";
        }
        if(isset($result[0]['date_start']))
        {
            $result[0]['date_start'] = datetimeMongotoReadable($result[0]['date_start']);
        }
        if(isset($result[0]['date_expire']))
        {
            $result[0]['date_expire'] = datetimeMongotoReadable($result[0]['date_expire']);
        }
        if(isset($result[0]['image']))
        {
            $result[0]['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
        }
        return $result ? $result[0] : array();
    }

    /* copied from quest_model as model cannot call each other */
    public function getQuest($data, $test=NULL)
    {
        //get quest
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('quest_name', 'image', 'status', 'description', 'deleted'));
        $criteria = array(
            //'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['quest_id'],
            //'status' => true
        );

        $this->mongo_db->where($criteria);
        //$this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);

        $result = $this->mongo_db->get('playbasis_quest_to_client');

        $result = $result ? $result[0] : array();

        if ($result) {
            array_walk_recursive($result, array($this, "change_image_path"));
            $result['id'] = $result['_id']."";
            unset($result['_id']);
        }

        return $result;
    }

    /* copied from quest_model as model cannot call each other */
    public function getMission($data)
    {
        //get mission
        $this->set_site_mongodb($data['site_id']);

        //$this->mongo_db->select(array('missions.$.mission_id', 'missions.$.mission_name', 'missions.$.mission_number', 'missions.$.description', 'missions.$.image'));
        $this->mongo_db->select(array('missions.$'));
        $this->mongo_db->where(array(
            //'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            //'_id' => $data['quest_id'],
            'missions.mission_id' => $data['mission_id'],
            //'status' => true
        ));
        //$this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_quest_to_client');

        $result = $result ? $result[0] : array();

        if ($result && isset($result['missions'])) {
            $result = $result['missions'][0];
            array_walk_recursive($result, array($this, "change_image_path"));
            $result['mission_id'] = $result['mission_id']."";
            unset($result['hint']);
            unset($result['completion']);
            unset($result['rewards']);
        }

        return $result;
    }

    public function getQuestIdByMissionId($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array());
        $this->mongo_db->where(array(
            'site_id' => $data['site_id'],
            'missions.mission_id' => $data['mission_id'],
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_quest_to_client');
        return $result ? $result[0]['_id'] : array();
    }

    /* copied from quest_model as model cannot call each other */
    public function getQuestByMission($data)
    {
        //get mission
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('quest_name', 'image', 'status', 'description', 'deleted'));
        $this->mongo_db->where(array(
            //'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            //'_id' => $data['quest_id'],
            'missions.mission_id' => $data['mission_id'],
            //'status' => true
        ));
        //$this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_quest_to_client');

        $result = $result ? $result[0] : array();

        if ($result) {
            array_walk_recursive($result, array($this, "change_image_path"));
            $result['id'] = $result['_id']."";
            unset($result['_id']);
        }

        return $result;
    }

    /* copied from quiz_model (find_by_id) as model cannot call each other */
    public function getQuiz($site_id, $quiz_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('name', 'image', 'status', 'description', 'deleted'));
        $this->mongo_db->where('_id', $quiz_id);
        $this->mongo_db->where('site_id', $site_id);
        $results = $this->mongo_db->get('playbasis_quiz_to_client');

        $result = null;
        if ($results) {
            $result = $results[0];

            if(!empty($result['image'])){
                $pattern = '#^'.$this->config->item('IMG_PATH').'#';
                preg_match($pattern, $result['image'], $matches);
                if(!$matches){
                    $result['image'] = $this->config->item('IMG_PATH').$result['image'];
                }
            }else{
                $result['image'] = $this->config->item('IMG_PATH')."no_image.jpg";
            }

            $result['id'] = $result['_id']."";
            unset($result['_id']);
        }
        return $result;
    }

    public function insertCountries($countries) {
        return $this->mongo_db->batch_insert('countries', $countries);
    }

    public function findCountryByDialCode($dialCode) {
        if ($dialCode == '+1') return 'United States';
        $this->mongo_db->where('d_code', $dialCode);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('countries');
        return $results ? $results[0]['name'] : null;
    }

    public function countApiUsage($client_id, $from, $to=null) {
        $record = null;
        if ($from && $to) {
            $record = $this->findApiUsageStat($client_id, $from, $to);
            if ($record) return $record['n'];
        }
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where_gte('date_added', new MongoDate(strtotime($from.' 00:00:00')));
        if ($to) $this->mongo_db->where_lt('date_added', new MongoDate(strtotime($to.' 00:00:00')));
        $n = $this->mongo_db->count('playbasis_web_service_log');
        if ($from && $to) {
            $this->saveApiUsageStat($client_id, $from, $to, $n);
        }
        return $n;
    }

    public function findApiUsageStat($client_id, $from, $to) {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('from', $from);
        $this->mongo_db->where('to', $to);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_web_service_usage');
        return $results ? $results[0] : array();
    }

    public function saveApiUsageStat($client_id, $from, $to, $n) {
        return $this->mongo_db->insert('playbasis_web_service_usage', array(
            'client_id' => $client_id,
            'from' => $from,
            'to' => $to,
            'n' => $n,
        ));
    }

    private function change_image_path(&$item, $key)
    {
        if($key === "image"){
            if(!empty($item)){
                $item = $this->config->item('IMG_PATH').$item;
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }

        }
    }
}
?>