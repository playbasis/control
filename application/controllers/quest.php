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

    public function QuestProcess($action_id, $pb_player_id, $validToken){

        $client_id = $validToken['client_id'];
        $site_id = $validToken['site_id'];
        $domain_name = $validToken['domain_name'];

        $quests = $this->player_model->getAllQuests($pb_player_id, $site_id, "join");

        foreach($quests as $q){
            $this->checkConditionQuest($q["quest_id"], $pb_player_id, $validToken);
        }

    }

    private function checkConditionQuest($quest_id, $pb_player_id, $validToken){

        $data = array(
            "client_id" => $validToken['client_id'],
            "site_id" => $validToken['site_id'],
            "quest_id" => $quest_id
        );

        $quest = $this->quest_model->getQuest($data);

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

        if($quest && isset($quest["condition"])){
            $questResult['events'] = array();
            foreach($quest["condition"] as $c){
                if($c["condition_type"] == "DATETIME_START"){
                    if($c["condition_value"]->sec > time()){
                        $event = array(
                            'event_type' => 'QUEST_DID_NOT_START',
                            'message' => 'quest did not start'
                        );
                        array_push($questResult['events'], $event);
                    }
                }
                if($c["condition_type"] == "DATETIME_END"){
                    if($c["condition_value"]->sec < time()){
                        $event = array(
                            'event_type' => 'QUEST_ALREADY_FINISHED',
                            'message' => 'quest already finished'
                        );
                        array_push($questResult['events'], $event);
                    }
                }
                if($c["condition_type"] == "LEVEL_START"){
                    if($c["condition_value"] > $player['level']){
                        $event = array(
                            'event_type' => 'LEVEL_IS_LOWER',
                            'message' => 'Your level is under satisfied'
                        );
                        array_push($questResult['events'], $event);
                    }
                }
                if($c["condition_type"] == "LEVEL_END"){
                    if($c["condition_value"] < $player['level']){
                        $event = array(
                            'event_type' => 'LEVEL_IS_HIGHER',
                            'message' => 'Your level is abrove satisfied'
                        );
                        array_push($questResult['events'], $event);
                    }
                }
                if($c["condition_type"] == "POINT"){
                    $reward_id = $this->point_model->findPoint(array_merge($validToken, array('reward_name'=>'point')));
                    $point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
                    echo "reward";
                    var_Dump($reward_id);
                    echo "point";
                    var_Dump($point);
                    if(isset($player_point[0]['value']) && isset($goods['redeem']['point']["point_value"])){
                    if($c["condition_value"] > $point[0]["value"]){
                        $event = array(
                            'event_type' => 'POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough'
                        );
                        array_push($questResult['events'], $event);
                    }
                }
                if($c["condition_type"] == "CUSTOM_POINT"){

                }
                if($c["condition_type"] == "BADGE"){

                }
            }

            var_Dump($questResult);
        }

    }

    private function checkCompletionMission(){

    }

    private function updateRewardPlayer(){

    }

    private function trackQuest(){

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
        //get action id by action name
        $actionName = $this->input->post('action');
        $action_id = $this->client_model->getActionId(array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id'],
            'action_name' => $actionName
        ));
        if(!$action_id)
            $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
        $this->QuestProcess($action_id, $pb_player_id, $validToken);
    }
}
?>