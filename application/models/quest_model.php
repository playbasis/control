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

        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['quest_id'],
            'status' => true
        ));
        $this->mongo_db->where_ne('deleted', true);
        $result = $this->mongo_db->get('playbasis_quest_to_client');

        return $result ? $result[0] : array();
    }
    public function getQuests($data)
    {
        // get all quests related to client
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'status' => true
        ));
        $this->mongo_db->where_ne('deleted', true);
        $result = $this->mongo_db->get('playbasis_quest_to_client');
        return $result;
    }
    public function getMission($data)
    {
        //get mission
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->select(array('missions.$'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            '_id' => $data['quest_id'],
            'missions.mission_id' => $data['mission_id'],
            'status' => true
        ));
        $this->mongo_db->where_ne('deleted', true);
        $result = $this->mongo_db->get('playbasis_quest_to_client');
        return $result ? $result[0] : array();
    }

    public function joinQuest($data)
    {
        $this->set_site_mongodb($data["site_id"]);
        foreach($data["missions"] as &$m){
            $m["status"] = "unjoin";
        }
        $this->mongo_db->insert("playbasis_quest_to_player", array(
            "client_id" => $data["client_id"],
            "site_id" => $data["site_id"],
            "date_added" => new MongoDate(time()),
            "date_modified" => new MongoDate(time()),
            "missions" => $data["missions"],
            "pb_player_id" => $data["pb_player_id"],
            "quest_id" => $data["quest_id"],
            "status" => "join"
        ));
    }

    public function getPlayerQuest($data) {
        $this->set_site_mongodb($data["site_id"]);

        $this->mongo_db->where(array(
            "pb_player_id" => $data["pb_player_id"],
            "quest_id" => $data["quest_id"]
        ));
        if(isset($data['status'])){
            $this->mongo_db->where_in('status', $data['status']);
        }
        $this->mongo_db->where_ne('deleted', true);
        $result = $this->mongo_db->get('playbasis_quest_to_player');

        return $result ? $result[0] : array();
    }

    public function getPlayerQuests($data) {
        $this->set_site_mongodb($data["site_id"]);

        $this->mongo_db->where(array(
            "pb_player_id" => $data["pb_player_id"]
        ));
        if(isset($data['status'])){
            $this->mongo_db->where_in('status', $data['status']);
        }
        $this->mongo_db->where_ne('deleted', true);
        $result = $this->mongo_db->get('playbasis_quest_to_player');

        return $result;
    }

    public function updateQuestStatus($data, $status){
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->where(array(
            'site_id' => $data['site_id'],
            'pb_player_id' => $data['pb_player_id'],
            'quest_id' => $data['quest_id']
        ));
        $this->mongo_db->set(array('status' => $status));
        $this->mongo_db->set(array('date_modified' => new MongoDate(time())));
        $this->mongo_db->update('playbasis_quest_to_player');
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
            $this->mongo_db->set(array('missions.$.date_modified' => new MongoDate(time())));
            $this->mongo_db->update('playbasis_quest_to_player');
        }
    }

    private function change_image_path($item, $key)
    {
        if($key == "image"){
            $item = $this->config->item('IMG_PATH').$item;
        }
    }
}
?>
