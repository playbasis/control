<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Service_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
        $this->load->library('mongo_db');
    }
    public function getRecentPoint($site_id, $reward_id, $offset, $limit, $show_login=false, $show_quest=false){

        $this->mongo_db->where('site_id', $site_id);

        if($show_login){
            if($reward_id){
                $this->mongo_db->where('reward_id', $reward_id);
            }
            $this->mongo_db->where_in('event_type', array('REWARD', 'LOGIN'));
        }else{
            if($reward_id){
                $this->mongo_db->where('reward_id', $reward_id);
            }else{
                $this->mongo_db->where_ne('reward_id', null);
            }
            $this->mongo_db->where_in('event_type', array('REWARD'));
            $this->mongo_db->where_gt('value', 0);
        }

        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->order_by(array('date_added' => -1));
        $event_log = $this->mongo_db->get('playbasis_event_log');

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

            $actionAndStringFilter = $this->getActionNameAndStringFilter($event['action_log_id']);

            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
            if($actionAndStringFilter){
                $event['action_name'] = $actionAndStringFilter['action_name'];
                $event['string_filter'] = $actionAndStringFilter['url'];
                $event['action_icon'] = $actionAndStringFilter['icon'];
            }
            unset($event['action_log_id']);
            unset($event['pb_player_id']);

            $event['reward_id'] = $event['reward_id']."";

            if($event['reward_name'] == "badge"){

                $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
                $this->mongo_db->select(array(),array('_id'));
                $this->mongo_db->where(array(
                    'site_id' => $site_id,
                    'badge_id' => $event['item_id'],
                    'deleted' => false
                ));
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
        }

        if($show_quest){
            //Add quest mission logs to the live feed
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->limit((int)$limit);
            $this->mongo_db->offset((int)$offset);
            $this->mongo_db->select(array('reward_name', 'date_added', 'reward_value','pb_player_id', 'mission_id'));
            $this->mongo_db->select(array(), array('_id'));
            $quest_logs = $this->mongo_db->get('playbasis_quest_reward_log');

            foreach($quest_logs as &$quest){

                $this->mongo_db->where('site_id', $site_id);
                $this->mongo_db->where('_id', $quest['pb_player_id']);
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

                $quest['date_added'] = datetimeMongotoReadable($quest['date_added']);
                $quest['player'] = isset($player[0])?$player[0]:null;
                $quest['message'] = 'earned '. $quest['reward_value'].' '.$quest['reward_name'];
                if(($quest['mission_id'] != null)){
                    $quest['action_icon'] = 'fa-trophy';
                    $quest['action_name'] = 'mission_reward';
                }else{
                    $quest['action_icon'] = 'fa-trophy';
                    $quest['action_name'] = 'quest_reward';
                }
            }

            $result = array_merge($event_log, $quest_logs);

            usort($result, function($a1, $a2) {
                $v1 = strtotime($a1['date_added']);
                $v2 = strtotime($a2['date_added']);
                return $v1 - $v2;
            });

            //End add quest mission logs
            return $result;
        }

        // Comment out this because we use the $result krub
         return $event_log;
    }

    private function getActionNameAndStringFilter($action_log_id){
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
            $action = $this->mongo_db->get('playbasis_action_to_client');

            if($action){
                $returnThis['icon'] = $action[0]['icon'];
            }
        }else{
            return array();
        }

        return $returnThis;
    }
}
?>