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
    }

    public function QuestProcess($action_id, $pb_player_id, $validToken){

        $client_id = $validToken['client_id'];
        $site_id = $validToken['site_id'];
        $domain_name = $validToken['domain_name'];

        $quests = $this->player_model->getAllQuests($pb_player_id, $site_id, "join");

        foreach($quests as $q){
            $this->checkConditionQuest();
        }

    }

    private function checkConditionQuest(){

    }

    private function checkCompletionMission(){

    }

    private function updateRewardPlayer(){

    }

    private function trackQuest(){

    }
}
?>