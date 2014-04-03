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
    public function getPointHistory($site_id, $reward_id, $offset, $limit){

        if($reward_id){
            $this->mongo_db->where('reward_id', $reward_id);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('event_type', 'REWARD');
        $this->mongo_db->where_ne('reward_id', null);
        $this->mongo_db->where_gt('value', 0);
        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'value', 'message', 'date_added','action_log_id'));
        $this->mongo_db->select(array(), array('_id'));
        $event_log = $this->mongo_db->get('playbasis_event_log');


        foreach($event_log as &$event){
            $actionAndStringFilter = $this->getActionNameAndStringFilter($event['action_log_id']);

            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
            if($actionAndStringFilter){
                $event['action_name'] = $actionAndStringFilter['action_name'];
                $event['string_filter'] = $actionAndStringFilter['url'];
            }
            unset($event['action_log_id']);

            $event['reward_id'] = $event['reward_id']."";
        }


        return $event_log;
    }
}
?>