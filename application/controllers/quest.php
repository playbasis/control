<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Quest extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('player_model');
        $this->load->model('client_model');
        $this->load->model('tracker_model');
        $this->load->model('point_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('social_model');
        $this->load->model('quest_model');
    }

    public function QuestProcess($pb_player_id, $validToken){

        $this->load->helper('vsort');

        $client_id = $validToken['client_id'];
        $site_id = $validToken['site_id'];
        $domain_name = $validToken['domain_name'];

        $quests = $this->player_model->getAllQuests($pb_player_id, $site_id, "join");

        $questEvent = array();

        foreach($quests as $q){

            $missionEvent = array();

            $data = array(
                "client_id" => $validToken['client_id'],
                "site_id" => $validToken['site_id'],
                "quest_id" => $q["quest_id"]
            );

            $quest = $this->quest_model->getQuest($data);

            $event_of_quest = $this->checkConditionQuest($quest, $pb_player_id, $validToken);

            $mission_count = count($quest["missions"]);
            $player_finish_count = 0;

            if(!(count($event_of_quest) > 0)){

                $player_missions = array();
                foreach($q["missions"] as $pm){
                    $player_missions[$pm["mission_id"].""] = $pm["status"];
                }

                if((bool)$quest["mission_order"]){
                    $missions = vsort($quest["missions"], "mission_number");
                    $first_mission = key($missions);

                    //for check first mission of player that will be automatic join
                    $mission_status_check_unjoin = array("join", "finish");
                    if(!in_array($player_missions[$missions[$first_mission]["mission_id"].""], $mission_status_check_unjoin)){
                        $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $missions[$first_mission]["mission_id"], $validToken, "join");
                        $player_missions[$missions[$first_mission]["mission_id"].""] = "join";
                    }

                    $next_mission = false;

                    foreach($missions as $m){
                        //if player pass mission so next mission status will change to join
                        if($next_mission){
                            $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken, "join");
                            $player_missions[$m["mission_id"].""] = "join";
                            $next_mission = false;
                        }

                        if($player_missions[$m["mission_id"].""] == "join"){
                            //echo "join";
                            $event_of_mission = $this->checkCompletionMission($quest, $m, $pb_player_id, $validToken);
                            if(!(count($event_of_mission) > 0)){

                                $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken, "finish");

                                $this->updateMissionRewardPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken);
                                //for check total mission finish
                                $player_finish_count++;
                                $next_mission = true;
                            }
                        }else if($player_missions[$m["mission_id"].""] == "finish"){
                            //echo "finish";
                            //for check total mission finish
                            $player_finish_count++;
                            $next_mission = true;
                            continue;
                        }else{
                            //echo "unjoin";
                            break;
                        }

                        $event = array(
                            'mission_id' => $m["mission_id"],
                            'mission_status' => (count($event_of_mission)>0 ? false : true),
                            'mission_detail' => $event_of_mission
                        );
                        array_push($missionEvent, $event);
                    }
                }else{

                    foreach($quest["missions"] as $m){

                        if($player_missions[$m["mission_id"].""] != "finish"){

                            $event_of_mission = $this->checkCompletionMission($quest, $m, $pb_player_id, $validToken);

                            if(!(count($event_of_mission) > 0)){
                                $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken,"finish");

                                $this->updateMissionRewardPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken);
                                //for check total mission finish
                                $player_finish_count++;
                            }else{
                                $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"], $validToken,"join");
                            }
                        }else{
                            //for check total mission finish
                            $player_finish_count++;
                        }

                        $event = array(
                            'mission_id' => $m["mission_id"],
                            'mission_status' => (count($event_of_mission)>0 ? false : true),
                            'mission_detail' => $event_of_mission
                        );
                        array_push($missionEvent, $event);
                    }
                }
            }

            if($mission_count == $player_finish_count){
                echo "finish all mission";
                $this->updateQuestRewardPlayer($pb_player_id, $q["quest_id"], $validToken);
            }

            $event = array(
                'quest_id' => $q["quest_id"],
                'quest_status' => (count($event_of_quest)>0 ? false : true),
                'quest_detail' => $event_of_quest,
                'missions' => $missionEvent
            );
            array_push($questEvent, $event);
        }

        return $questEvent;

    }

    private function checkConditionQuest($quest, $pb_player_id, $validToken){

        //read player information
        $player = $this->player_model->readPlayer($pb_player_id, $validToken['site_id'], array(
            'username',
            'first_name',
            'last_name',
            'gender',
            'image',
            'exp',
            'level',
            'date_added',
            'birth_date'
        ));

        $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

        if($player_badges){
            $badge_player_check = array();
            foreach($player_badges as $b){
                $badge_player_check[$b["badge_id"]] = $b["amount"];
            }
        }

        $questEvent = array();

        if($quest && isset($quest["condition"])){

            foreach($quest["condition"] as $c){
                if($c["condition_type"] == "DATETIME_START"){
                    if($c["condition_value"]->sec > time()){
                        $event = array(
                            'event_type' => 'QUEST_DID_NOT_START',
                            'message' => 'quest did not start'
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "DATETIME_END"){
                    if($c["condition_value"]->sec < time()){
                        $event = array(
                            'event_type' => 'QUEST_ALREADY_FINISHED',
                            'message' => 'quest already finished'
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "LEVEL_START"){
                    if($c["condition_value"] > $player['level']){
                        $event = array(
                            'event_type' => 'LEVEL_IS_LOWER',
                            'message' => 'Your level is under satisfied'
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "LEVEL_END"){
                    if($c["condition_value"] < $player['level']){
                        $event = array(
                            'event_type' => 'LEVEL_IS_HIGHER',
                            'message' => 'Your level is abrove satisfied'
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "POINT"){
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["condition_id"], $validToken['site_id']);

                    if(isset($point_a[0]['value'])){
                        $point = $point_a[0]['value'];
                    }else{
                        $point = 0;
                    }
                    if($c["condition_value"] > $point){
                        $event = array(
                            'event_type' => 'POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array($c["condition_id"]."" => ((int)$c["condition_value"] - (int)$point))
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "CUSTOM_POINT"){
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["condition_id"], $validToken['site_id']);

                    if(isset($point_a[0]['value'])){
                        $custom_point = $point_a[0]['value'];
                    }else{
                        $custom_point = 0;
                    }
                    if($c["condition_value"] > $custom_point){
                        $event = array(
                            'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array($c["condition_id"]."" => ((int)$c["condition_value"] - (int)$custom_point))
                        );
                        array_push($questEvent, $event);
                    }
                }
                if($c["condition_type"] == "BADGE"){
                    if(isset($badge_player_check[$c["condition_id"].""])){
                        $badge = $badge_player_check[$c["condition_id"].""];
                    }else{
                        $badge = 0;
                    }
                    if($badge < $c["condition_value"]){
                        $event = array(
                            'event_type' => 'BADGE_NOT_ENOUGH',
                            'message' => 'user badge not enough',
                            'incomplete' => array($c["condition_id"]."" => ((int)$c["condition_value"] - (int)$badge))
                        );
                        array_push($questEvent, $event);
                    }
                }
            }
        }

        return $questEvent;

    }

    private function checkCompletionMission($quest, $mission, $pb_player_id, $validToken){

        $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

        if($player_badges){
            $badge_player_check = array();
            foreach($player_badges as $b){
                $badge_player_check[$b["badge_id"]] = $b["amount"];
            }
        }

        $missionEvent = array();

        $player_mission = $this->player_model->getMission($pb_player_id, $quest["_id"], $mission["mission_id"], $validToken['site_id']);

        if($mission && isset($mission["completion"])){

            foreach($mission["completion"] as $c){
                if($c["completion_type"] == "ACTION"){
                    $datetime_check = (isset($player_mission["missions"][0]["date_modifield"]))?datetimeMongotoReadable($player_mission["missions"][0]["date_modifield"]):date("Y-m-d H:i:s");
                    $action = $this->player_model->getActionCountFromDatetime($pb_player_id, $c["completion_id"], isset($c["completion_filter"])?$c["completion_filter"]:null, $validToken['site_id'], $datetime_check);

                    if((int)$c["completion_value"] > (int)$action["count"]){
                        $event = array(
                            'event_type' => 'ACTION_NOT_ENOUGH',
                            'message' => 'Your action not enough',
                            'incomplete' => array($c["completion_id"]."" => ((int)$c["completion_value"] - (int)$action["count"]))
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if($c["completion_type"] == "POINT"){
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["completion_id"], $validToken['site_id']);

                    if(isset($point_a[0]['value'])){
                        $point = $point_a[0]['value'];
                    }else{
                        $point = 0;
                    }
                    if($c["completion_value"] > $point){
                        $event = array(
                            'event_type' => 'POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array($c["completion_id"]."" => ((int)$c["completion_value"] - (int)$point))
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if($c["completion_type"] == "CUSTOM_POINT"){
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["completion_id"], $validToken['site_id']);

                    if(isset($point_a[0]['value'])){
                        $custom_point = $point_a[0]['value'];
                    }else{
                        $custom_point = 0;
                    }
                    if($c["completion_value"] > $custom_point){
                        $event = array(
                            'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array($c["completion_id"]."" => ((int)$c["completion_value"] - (int)$custom_point))
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if($c["completion_type"] == "BADGE"){
                    if(isset($badge_player_check[$c["completion_id"].""])){
                        $badge = $badge_player_check[$c["completion_id"].""];
                    }else{
                        $badge = 0;
                    }
                    if($badge < $c["completion_value"]){
                        $event = array(
                            'event_type' => 'BADGE_NOT_ENOUGH',
                            'message' => 'user badge not enough',
                            'incomplete' => array($c["completion_id"]."" => ((int)$c["completion_value"] - (int)$badge))
                        );
                        array_push($missionEvent, $event);
                    }
                }
            }
        }

        return $missionEvent;
    }

    private function updateMissionRewardPlayer($player_id, $quest_id, $mission_id, $validToken){

    }

    private function updateQuestRewardPlayer($player_id, $quest_id, $validToken){

    }

    private function trackQuest(){

    }

    private function updateMissionStatusOfPlayer($player_id, $quest_id, $mission_id, $validToken, $status="join"){
        $data = array(
            'site_id' => $validToken['site_id'],
            'pb_player_id' => $player_id,
            'quest_id' => $quest_id,
            'mission_id' => $mission_id
        );
        $this->quest_model->updateMissionStatus($data, $status);
    }

    public function testQuest_post(){
        //process regular data
        $required = $this->input->checkParam(array(
            'token'
        ));
        if($required)
            $this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
        $required = $this->input->checkParam(array(
            'action',
            'player_id'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $validToken = $this->auth_model->findToken($this->input->post('token'));
        if(!$validToken)
            $this->response($this->error->setError('INVALID_TOKEN'), 200);
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
            'cl_player_id' => $cl_player_id
        )));

        $this->QuestProcess($pb_player_id, $validToken);
    }

    public function index_get($quest_id = 0) {
        $required = $this->input->checkParam(array('api_key'));
        if ($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
        if (!$validToken)
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);

        $data = array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id']
        );

        if ($quest_id) {
            // get specific quest
            try {
                $quest_id = new MongoId($quest_id);
            } catch(MongoException $ex) {
                $quest_id = null;
            }

            $data['quest_id'] = $quest_id;
            $quest = $this->quest_model->getQuest($data);
            array_walk_recursive($quest, array($this, "convert_mongo_object"));
            $resp['quest'] = $quest;
            $resp['quest']['quest_id'] = $quest['_id'];
            unset($resp['quest']['_id']);
        } else {
            // get all questss related to clients
            $quest = $this->quest_model->getQuests($data);
            array_walk_recursive($quest, array($this, "convert_mongo_object"));
            foreach ($quest as $key => $value) {
                $quest[$key]['quest_id'] = $quest[$key]['_id'];
                unset($quest[$key]['_id']);
            }
            $resp['quests'] = $quest;
        }
        $this->response($this->resp->setRespond($resp), 200);
    }

    public function index_put($quest_id = 0) {
        // check put parameter
        $required = $this->input->checkParamPut(array("token", "player_id"), $this->_put_args);
        if ($required)
            $this->response($this->error->setError("PARAMETER_MISSING", $required), 200);

        // check quest_id input
		if (!$quest_id)
            $this->response($this->error->setError("PARAMETER_MISSING", array("quest_id"
			)), 200);

        // validate token
        $validToken = $this->auth_model->findToken($this->_put_args["token"]);
        if (!$validToken)
            $this->response($this->error->setError("INVALID_API_KEY_OR_SECRET"), 200);

        // check user exists
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
			"cl_player_id" => $this->_put_args["player_id"]
		)));
		if (!$pb_player_id)
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);

        try {
            $quest_id = new MongoId($quest_id);
        } catch(MongoException $ex) {
            $quest_id = null;
        }

        $data = array(
            "client_id" => $validToken["client_id"],
            "site_id" => $validToken["site_id"],
            "pb_player_id" => $pb_player_id,
            "quest_id" => $quest_id
        );

        // get quest detail
        $quest = $this->quest_model->getQuest($data);

        // check quest_to_client
        $player_quest = $this->quest_model->getPlayerQuest($data);

        // not join yet, let check condition
        if (!$player_quest) {
            $condition_quest = $this->checkConditionQuest($quest, $pb_player_id, $validToken);
            print_r($condition_quest);
            // condition passed
            if (!$condition_quest)
                $this->quest_model->joinQuest(array_merge($data, $quest));
            else
                // condition failed
                $this->response($this->error->setError("QUEST_CONDITION"), 200);
        } else {
            // already join, let check quest_to_client status
            if ($player_quest["status"] == "join")
                // joined
                $this->response($this->error->setError("QUEST_JOINED"), 200);
            else if ($player_quest["status"] == "finish")
                // finished
                $this->response($this->error->setError("QUEST_FINISHED"), 200);
            else if ($player_quest["status"] == "unjoin") {
                // unjoin, let him join again
                $this->quest_model->updateQuestStatus($data, "join");
            }
        }
        $this->response($this->resp->setRespond(
            isset($condition_quest) ? $condition_quest : array()), 200);

    }

    public function index_delete($quest_id = 0)
    {
        // check delete parameter
        $required = $this->input->checkParamPut(array("token", "player_id"), $this->_delete_args);
        if ($required)
            $this->response($this->error->setError("PARAMETER_MISSING", $required), 200);

        // check quest_id input
		if (!$quest_id)
            $this->response($this->error->setError("PARAMETER_MISSING", array("quest_id"
			)), 200);

        // validate token
        $validToken = $this->auth_model->findToken($this->_delete_args["token"]);
        if (!$validToken)
            $this->response($this->error->setError("INVALID_API_KEY_OR_SECRET"), 200);

        // check user exists
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
			"cl_player_id" => $this->_delete_args["player_id"]
		)));
		if (!$pb_player_id)
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);

        try {
            $quest_id = new MongoId($quest_id);
        } catch(MongoException $ex) {
            $quest_id = null;
        }

        $data = array(
            "client_id" => $validToken["client_id"],
            "site_id" => $validToken["site_id"],
            "pb_player_id" => $pb_player_id,
            "quest_id" => $quest_id
        );

        // check quest_to_client
        $player_quest = $this->quest_model->getPlayerQuest($data);

        // not join yet, cannot join
        if (!$player_quest) {
                $this->response($this->error->setError("QUEST_CANCEL_FAILED"), 200);
        } else {
            // already join, let check quest_to_client status
            if ($player_quest["status"] == "join")
                // joined, let unjoin
                $this->quest_model->updateQuestStatus($data, "unjoin");
            else if ($player_quest["status"] == "finish")
                // finished
                $this->response($this->error->setError("QUEST_FINISHED"), 200);
            else if ($player_quest["status"] == "unjoin") {
                // unjoin
                $this->response($this->error->setError("QUEST_CANCEL_FAILED"), 200);
            }
        }
        $this->response($this->resp->setRespond(array()), 200);
    }

    private function convert_mongo_object(& $item,$key) {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else if (get_class($item) === 'MongoDate') {
                $item =  datetimeMongotoReadable($item);
            }
        }
    }

}
?>
