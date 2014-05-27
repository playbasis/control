<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quest_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }
    public function getQuest($data)
    {
        //get quest
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['quest_id'],
            'status' => true
        ));
        $result = $this->mongo_db->get('playbasis_quest_to_client');

        return $result ? $result[0] : array();
    }
    public function updateMissionStatus($data, $status){
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->where(array(
            'site_id' => $data['site_id'],
            'pb_player_id' => $data['pb_player_id'],
            'quest_id' => $data['quest_id'],
            'missions.mission_id' => $data['mission_id'],
        ));
        $qp = $this->mongo_db->get('playbasis_quest_to_player');

        if($qp && isset($qp[0])){
            $this->mongo_db->where(array(
                'site_id' => $data['site_id'],
                'pb_player_id' => $data['pb_player_id'],
                'missions.mission_id' => $data['mission_id'],
            ));
            $this->mongo_db->set(array('missions.$.status' => $status));
            $this->mongo_db->set(array('missions.$.date_modifield' => new MongoDate(time())));
            $this->mongo_db->update('playbasis_quest_to_player');
        }
    }
}
?>