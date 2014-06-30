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
    public function getRecentPoint($site_id, $reward_id, $offset, $limit){

        if($reward_id){
            $this->mongo_db->where('reward_id', $reward_id);
        }else{
            $this->mongo_db->where_ne('reward_id', null);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('event_type', 'REWARD');
        $this->mongo_db->where_gt('value', 0);
        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->order_by(array('date_added' => -1));
        $event_log = $this->mongo_db->get('playbasis_event_log');

        foreach($event_log as &$event){

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

            $event['player'] = isset($player) ? $player[0] : null;

            $actionAndStringFilter = $this->getActionNameAndStringFilter($event['action_log_id']);

            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
            if($actionAndStringFilter){
                $event['action_name'] = $actionAndStringFilter['action_name'];
                $event['string_filter'] = $actionAndStringFilter['url'];
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
                if($result){
                    $event['badge']['badge_id'] = $result[0]['badge_id']."";
                    $event['badge']['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
                    $event['badge']['name'] = $result[0]['name'];
                    $event['badge']['description'] = $result[0]['description'];
                    $event['badge']['hint'] = $result[0]['hint'];
                }

            }

            unset($event['item_id']);
        }


        return $event_log;
    }

    private function getActionNameAndStringFilter($action_log_id){
        $this->mongo_db->select(array('action_name', 'url'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('_id', new MongoID($action_log_id));
        $returnThis = $this->mongo_db->get('playbasis_action_log');
        return ($returnThis)?$returnThis[0]:array();
    }
}
?>