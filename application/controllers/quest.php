<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Quest extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('action_model');
        $this->load->model('player_model');
        $this->load->model('client_model');
        $this->load->model('tracker_model');
        $this->load->model('point_model');
        $this->load->model('social_model');
        $this->load->model('quest_model');
        $this->load->model('quiz_model');
        $this->load->model('reward_model');
        $this->load->model('email_model');
        $this->load->model('sms_model');
        $this->load->model('push_model');
        $this->load->model('store_org_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('webhook_model');
    }

    public function QuestProcess($pb_player_id, $validToken, $test_id = null)
    {
        $this->load->helper('vsort');

        $client_id = $validToken["client_id"];
        $site_id = $validToken["site_id"];

        if ($test_id) {
            $quests = $this->player_model->getQuestsByID($site_id, $test_id);
        } else {
            $quests = $this->player_model->getAllQuests($pb_player_id, $site_id, "join");
        }

        $questEvent = array();

        $questResult = array(
            'events_missions' => array(),
            'events_quests' => array()
        );

        /* [quest usage] check quest usage against the associated plan */
        if (!$test_id) {
            try {
                $this->client_model->permissionCheck(
                    $this->client_data,
                    $client_id,
                    $site_id,
                    "others",
                    "quest_usage"
                );
            } catch (Exception $e) {
                /* we have to suppress LIMIT_EXCEED exception here because this quest processing is just a part of /Engine/rule call */
                if ($e->getMessage() === "LIMIT_EXCEED") {
                    return $questResult;
                } // stop processing next quest as we already hit the plan usage
                throw $e; // otherwise, throw the error
            }
        }

        $badge_player_check = array();
        $player_badges = $this->player_model->getBadge($pb_player_id, $site_id);
        if ($player_badges) {
            foreach ($player_badges as $b) {
                $badge_player_check[$b["badge_id"]] = $b["amount"];
            }
        }

        $platform = $this->auth_model->getOnePlatform($this->client_id, $this->site_id);
        $request_array = array();
        foreach ($quests as $q) {

            $missionEvent = array();

            $data = array(
                "client_id" => $validToken['client_id'],
                "site_id" => $validToken['site_id'],
                "quest_id" => $q["quest_id"]
            );

            $quest = $this->quest_model->getQuest($data, $test_id);

            if (!$test_id) {
                $event_of_quest = $this->checkConditionQuest($quest, $pb_player_id, $validToken);
            } else {
                $event_of_quest = array();
            }

            $mission_count = count($quest["missions"]);
            $player_finish_count = 0;

            if (!(count($event_of_quest) > 0)) {
                $player_missions = array();
                if (isset($q['missions']) && is_array($q['missions'])) {
                    foreach ($q["missions"] as $pm) {
                        $player_missions[$pm["mission_id"] . ""] = isset($pm["status"]) ? $pm["status"] : "unjoin";
                    }
                }

                if ((bool)$quest["mission_order"]) {
                    $missions = vsort($quest["missions"], "mission_number");
                    $first_mission = key($missions);

                    //for check first mission of player that will be automatic join
                    $mission_status_check_unjoin = array("join", "finish");
                    if (isset($player_missions[$missions[$first_mission]["mission_id"] . ""])
                        && !in_array($player_missions[$missions[$first_mission]["mission_id"] . ""],
                            $mission_status_check_unjoin)
                    ) {
                        if (!$test_id) {
                            $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"],
                                $missions[$first_mission]["mission_id"], $validToken, "join");
                        }
                        $player_missions[$missions[$first_mission]["mission_id"] . ""] = "join";
                    }

                    $next_mission = false;

                    foreach ($missions as $m) {

                        //if player pass mission so next mission status will change to join
                        if ($next_mission && $player_missions[$m["mission_id"] . ""] == "unjoin") {
                            if (!$test_id) {
                                $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"],
                                    $validToken, "join");
                            }
                            $player_missions[$m["mission_id"] . ""] = "join";
                            $next_mission = false;
                        }

                        if (isset($player_missions[$m["mission_id"] . ""]) && $player_missions[$m["mission_id"] . ""] == "join") {
                            //echo "join";
                            if (!$test_id) {
                                $event_of_mission = $this->checkCompletionMission($quest, $m, $pb_player_id,
                                    $validToken, $badge_player_check);
                            } else {
                                $event_of_mission = array();
                            }

                            if (!(count($event_of_mission) > 0)) {

                                if (!$test_id) {
                                    $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"], $m["mission_id"],
                                        $validToken, "finish");
                                    try{
                                        $this->updateMissionRewardPlayer($pb_player_id, $q["quest_id"], $m["mission_id"],
                                            $validToken, $questResult);
                                    } catch (Exception $e){}
                                    
                                    /* process "feedbacks" */
                                    if (isset($m["feedbacks"]) && is_array($m["feedbacks"])) {
                                        foreach ($m["feedbacks"] as $feedback) {
                                            $this->processFeedback($feedback["feedback_type"], array(
                                                'client_id' => $client_id,
                                                'site_id' => $site_id,
                                                'pb_player_id' => $pb_player_id,
                                                'input' => array(
                                                    'template_id' => $feedback["template_id"],
                                                    'subject' => isset($feedback["subject"]) ? $feedback["subject"] : null,
                                                ),
                                            ));
                                        }
                                    }
                                    /* [quest usage] increase usage value on client's account */
                                    $this->quest_model->insertQuestUsage(
                                        $client_id,
                                        $site_id,
                                        $q["quest_id"],
                                        $m["mission_id"],
                                        $pb_player_id
                                    );
                                    /* fire complete-mission action */
                                    array_push($request_array,array('api_key' => $platform['api_key'],
                                                                    'pb_player_id' => $pb_player_id . '',
                                                                    'action' => ACTION_COMPLETE_MISSION ));

                                    /*$this->utility->request('engine', 'json', http_build_query(array(
                                        'api_key' => $platform['api_key'],
                                        'pb_player_id' => $pb_player_id . '',
                                        'action' => ACTION_COMPLETE_MISSION,
                                    )));*/
                                }

                                //for check total mission finish
                                $player_finish_count++;
                                $next_mission = true;
                            }
                        } else {
                            if (isset($player_missions[$m["mission_id"] . ""]) && $player_missions[$m["mission_id"] . ""] == "finish") {
                                //echo "finish";
                                //for check total mission finish
                                $player_finish_count++;
                                $next_mission = true;
                                continue;
                            } else {
                                //echo "unjoin";
                                break;
                            }
                        }

                        $event = array(
                            'mission_id' => $m["mission_id"],
                            'mission_status' => (count($event_of_mission) > 0 ? false : true),
                            'mission_events' => $event_of_mission
                        );
                        array_push($missionEvent, $event);
                    }
                } else {

                    if (isset($quest['missions']) && is_array($quest['missions'])) {
                        foreach ($quest["missions"] as $m) {
                            if (isset($player_missions[$m["mission_id"] . ""]) && $player_missions[$m["mission_id"] . ""] != "finish") {

                                if (isset($player_missions[$m["mission_id"] . ""]) && $player_missions[$m["mission_id"] . ""] == "unjoin") {
                                    if (!$test_id) {
                                        $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"],
                                            $m["mission_id"],
                                            $validToken, "join");
                                    }
                                }

                                if (!$test_id) {
                                    $event_of_mission = $this->checkCompletionMission($quest, $m, $pb_player_id,
                                        $validToken, $badge_player_check);
                                } else {
                                    $event_of_mission = array();
                                }

                                if (!(count($event_of_mission) > 0)) {
                                    if (!$test_id) {
                                        $this->updateMissionStatusOfPlayer($pb_player_id, $q["quest_id"],
                                            $m["mission_id"], $validToken, "finish");
                                        try {
                                            $this->updateMissionRewardPlayer($pb_player_id, $q["quest_id"],
                                                $m["mission_id"], $validToken, $questResult);
                                        } catch (Exception $e) {
                                        }

                                        /* process "feedbacks" */
                                        if (isset($m["feedbacks"]) && is_array($m["feedbacks"])) {
                                            foreach ($m["feedbacks"] as $feedback) {
                                                $this->processFeedback($feedback["feedback_type"], array(
                                                    'client_id' => $client_id,
                                                    'site_id' => $site_id,
                                                    'pb_player_id' => $pb_player_id,
                                                    'input' => array(
                                                        'template_id' => $feedback["template_id"],
                                                        'subject' => isset($feedback["subject"]) ? $feedback["subject"] : null,
                                                    ),
                                                ));
                                            }
                                        }
                                        /* [quest usage] increase usage value on client's account */
                                        $this->quest_model->insertQuestUsage(
                                            $client_id,
                                            $site_id,
                                            $q["quest_id"],
                                            $m["mission_id"],
                                            $pb_player_id
                                        );
                                        /* fire complete-mission action */

                                        array_push($request_array, array(
                                            'api_key' => $platform['api_key'],
                                            'pb_player_id' => $pb_player_id . '',
                                            'action' => ACTION_COMPLETE_MISSION
                                        ));
                                        /*$this->utility->request('engine', 'json', http_build_query(array(
                                            'api_key' => $platform['api_key'],
                                            'pb_player_id' => $pb_player_id . '',
                                            'action' => ACTION_COMPLETE_MISSION,
                                        )));*/
                                    }
                                    //for check total mission finish
                                    $player_finish_count++;
                                }
                            } else {
                                //for check total mission finish
                                $player_finish_count++;
                                continue;
                            }

                            $event = array(
                                'mission_id' => $m["mission_id"],
                                'mission_status' => (count($event_of_mission) > 0 ? false : true),
                                'mission_events' => $event_of_mission
                            );
                            array_push($missionEvent, $event);
                        }
                    }
                }
            }

            if ($mission_count == $player_finish_count) {
                //echo "finish all mission";
                if (!$test_id) {
                    $this->updateQuestStatusOfPlayer($pb_player_id, $q["quest_id"], $validToken, "finish");
                    $this->updateQuestRewardPlayer($pb_player_id, $q["quest_id"], $validToken, $questResult);
                    /* process "feedbacks" */
                    if (isset($quest["feedbacks"]) && is_array($quest["feedbacks"])) {
                        foreach ($quest["feedbacks"] as $feedback) {
                            $this->processFeedback($feedback["feedback_type"], array(
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'pb_player_id' => $pb_player_id,
                                'input' => array(
                                    'template_id' => $feedback["template_id"],
                                    'subject' => isset($feedback["subject"]) ? $feedback["subject"] : null,
                                ),
                            ));
                        }
                    }
                    /* [quest usage] increase usage value on client's account */
                    $this->quest_model->insertQuestUsage(
                        $client_id,
                        $site_id,
                        $q["quest_id"],
                        null,
                        $pb_player_id
                    );
                    /* fire complete-quest action */

                    array_push($request_array,array('api_key' => $platform['api_key'],
                        'pb_player_id' => $pb_player_id . '',
                        'action' => ACTION_COMPLETE_QUEST));

                    /*$this->utility->request('engine', 'json', http_build_query(array(
                        'api_key' => $platform['api_key'],
                        'pb_player_id' => $pb_player_id . '',
                        'action' => ACTION_COMPLETE_QUEST,
                    )));*/
                    try {
                        $this->client_model->permissionProcess(
                            $this->client_data,
                            $client_id,
                            $site_id,
                            "others",
                            "quest_usage"
                        );
                    } catch (Exception $e) {
                        /* we have to suppress LIMIT_EXCEED exception here because this quest processing is just a part of /Engine/rule call */
                        if ($e->getMessage() === "LIMIT_EXCEED") {
                            break;
                        } // stop processing next quest as we already hit the plan usage
                        throw $e; // otherwise, throw the error
                    }
                }
            }

            $event = array(
                'quest_id' => $q["quest_id"],
                'quest_status' => (count($event_of_quest) > 0 ? false : true),
                'quest_events' => $event_of_quest,
                'missions' => $missionEvent
            );
            array_push($questEvent, $event);
        }

        foreach ($request_array as $request){
            $this->utility->request('engine', 'json', http_build_query($request));
        }

        return $questResult;
    }

    private function checkConditionQuest($quest, $pb_player_id, $validToken , $type=null)
    {
        if (empty($quest)) {
            $event = array(
                'event_type' => 'QUEST_NOT_EXIT',
                'message' => 'quest not exit'
            );
            return $event;
        }
        // get organize information
        $org_list = $this->store_org_model->retrieveNodeByPBPlayerID($this->client_id, $this->site_id, $pb_player_id);
        $org_id_list = array();
        if (is_array($org_list)) {
            foreach ($org_list as $node) {
                $org_info = $this->store_org_model->getOrgInfoOfNode($this->client_id, $this->site_id,
                    $node['node_id']);
                $a = array((string)$org_info[0]['organize'] => isset($node['roles']) ? $node['roles'] : array());
                $org_id_list = array_merge($org_id_list, $a);
            }
        }
        if (isset($quest['organize_id'])) {
            if ((!array_key_exists((string)$quest['organize_id'], $org_id_list)
                || ((isset($quest['organize_role']) && $quest['organize_role'] != "")
                    && !array_key_exists($quest['organize_role'],
                        $org_id_list[(string)$quest['organize_id']])))
            ) {
                $event = array(
                    'event_type' => 'QUEST_NOT_EXIT',
                    'message' => 'quest not exit'
                );
                return $event;
            }
        }

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

        if ($player_badges) {
            $badge_player_check = array();
            foreach ($player_badges as $b) {
                $badge_player_check[$b["badge_id"]] = $b["amount"];
            }
        }

        $questEvent = array();

        if ($quest && isset($quest["condition"])) {

            foreach ($quest["condition"] as $c) {
                switch ($c["condition_type"]){
                    case "DATETIME_START":
                        if($type != "join"){
                            if (strtotime($c["condition_value"]) > time()) {
                                $event = array(
                                    'event_type' => 'QUEST_DID_NOT_START',
                                    'message' => 'quest did not start'
    
                                );
                                array_push($questEvent, $event);
                            }
                        }
                        break;
                    case "DATETIME_END":
                        if($type != "join"){
                            if (strtotime($c["condition_value"]) < time()) {
                                $event = array(
                                    'event_type' => 'QUEST_ALREADY_FINISHED',
                                    'message' => 'quest already finished'
                                );
                                array_push($questEvent, $event);
                            }
                        }
                        break;
                    case "DATEJOIN_START":
                        if (strtotime($c["condition_value"]) > time()) {
                            $event = array(
                                'event_type' => 'QUEST_DID_NOT_START',
                                'message' => 'quest did not start'

                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "DATEJOIN_END":
                        if (strtotime($c["condition_value"]) < time()) {
                            $event = array(
                                'event_type' => 'QUEST_ALREADY_FINISHED',
                                'message' => 'quest already finished'
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "LEVEL_START":
                        if ((int)$c["condition_value"] > (int)$player['level']) {
                            $event = array(
                                'event_type' => 'LEVEL_IS_LOWER',
                                'message' => 'Your level is under satisfied'
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "LEVEL_END":
                        if ((int)$c["condition_value"] < (int)$player['level']) {
                            $event = array(
                                'event_type' => 'LEVEL_IS_HIGHER',
                                'message' => 'Your level is abrove satisfied'
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "POINT":
                        $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["condition_id"],
                            $validToken['site_id']);

                        if (isset($point_a[0]['value'])) {
                            $point = $point_a[0]['value'];
                        } else {
                            $point = 0;
                        }
                        if ((int)$c["condition_value"] > (int)$point) {
                            $event = array(
                                'event_type' => 'POINT_NOT_ENOUGH',
                                'message' => 'Your point not enough',
                                'incomplete' => array($c["condition_id"] . "" => ((int)$c["condition_value"] - (int)$point))
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "CUSTOM_POINT":
                        $point_a = $this->player_model->getPlayerPoint($pb_player_id,
                            $c["condition_id"], $validToken['site_id']);

                        if (isset($point_a[0]['value'])) {
                            $custom_point = $point_a[0]['value'];
                        } else {
                            $custom_point = 0;
                        }
                        if ((int)$c["condition_value"] > (int)$custom_point) {
                            $event = array(
                                'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                                'message' => 'Your point not enough',
                                'incomplete' => array($c["condition_id"] . "" => ((int)$c["condition_value"] - (int)$custom_point))
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "QUIZ":
                        $complete_quiz = $this->action_model->actionLogByURL($validToken, ACTION_COMPLETE_QUIZ, $c['condition_id'], $pb_player_id);
                        if ($complete_quiz == null) {
                            $event = array(
                                'event_type' => 'QUIZ_NOT_ENOUGH',
                                'message' => 'user quiz not enough',
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    case "BADGE":
                        if (isset($badge_player_check[$c["condition_id"] . ""])) {
                            $badge = $badge_player_check[$c["condition_id"] . ""];
                        } else {
                            $badge = 0;
                        }
                        if ((int)$badge < (int)$c["condition_value"]) {
                            $event = array(
                                'event_type' => 'BADGE_NOT_ENOUGH',
                                'message' => 'user badge not enough',
                                'incomplete' => array($c["condition_id"] . "" => ((int)$c["condition_value"] - (int)$badge))
                            );
                            array_push($questEvent, $event);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        return $questEvent;
    }

    private function checkCompletionMission(
        $quest,
        $mission,
        $pb_player_id,
        $validToken,
        $badge_player_check = array(),
        $player_mission = array()
    ) {

        if (empty($quest)) {
            $event = array(
                'event_type' => 'QUEST_NOT_EXIT',
                'message' => 'quest not exit'
            );
            return $event;
        }

        if (isset($mission["status"]) && $mission["status"] == "finish") {
            return array();
        }

        if (!$badge_player_check) {
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            if ($player_badges) {
                $badge_player_check = array();
                foreach ($player_badges as $b) {
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }
            }
        }

        $missionEvent = array();

        if (!$player_mission) {
            $obj = $this->player_model->getMission($pb_player_id, $quest["_id"], $mission["mission_id"],
                $validToken['site_id']);
            $player_mission = $obj["missions"][0];
        }
        $datetime_check = (isset($player_mission["date_modified"])) ? $player_mission["date_modified"] : new MongoDate(time());

        if ($mission && isset($mission["completion"])) {

            foreach ($mission["completion"] as $c) {
                if ($c["completion_type"] == "ACTION") {
                    // default is count for compatibility
                    if (!isset($c["completion_op"])) {
                        $c["completion_op"] = "count";
                    }
                    if ($c["completion_op"] == "count") {
                        $action = $this->player_model->getActionCountFromDatetime($pb_player_id,
                            new MongoId($c["completion_id"]),
                            isset($c["completion_filter"]) ? $c["completion_filter"] : null,
                            isset($c["filtered_param"]) ? $c["filtered_param"] : null,
                            $validToken['site_id'],
                            $datetime_check);
                        $c["completion_filter"] = isset($action['action_name']) ? $action['action_name'] : null;
                    } else { // sum
                        $action = $this->player_model->getActionSumFromDatetime($pb_player_id, $c["completion_id"],
                            isset($c["completion_filter"]) ? $c["completion_filter"] : null,
                            isset($c["filtered_param"]) ? $c["filtered_param"] : null,
                            $validToken['site_id'],
                            $datetime_check);
                    }

                    if ((int)$c["completion_value"] > (int)$action[$c["completion_op"]]) {
                        $event = array(
                            'event_type' => 'ACTION_NOT_ENOUGH',
                            'message' => 'Your action not enough',
                            'incomplete' => array(
                                'incompletion_id' => $c["completion_id"] . "",
                                'incompletion_type' => "ACTION",
                                'incompletion_value' => ((int)$c["completion_value"] - (int)$action[$c["completion_op"]])
                            )
                        );
                        if (isset($c["completion_element_id"])) {
                            $event['incomplete']['incompletion_element_id'] = $c["completion_element_id"] . "";
                        }
                        if (isset($c["completion_filter"])) {
                            $event['incomplete']['incompletion_filter'] = $c["completion_filter"];
                        }

                        array_push($missionEvent, $event);
                    }
                }
                if ($c["completion_type"] == "POINT") {
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["completion_id"],
                        $validToken['site_id']);

                    if (isset($point_a[0]['value'])) {
                        $point = $point_a[0]['value'];
                    } else {
                        $point = 0;
                    }

                    if ((int)$c["completion_value"] > (int)$point) {
                        $event = array(
                            'event_type' => 'POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array(
                                'incompletion_id' => $c["completion_id"] . "",
                                'incompletion_type' => "POINT",
                                'incompletion_value' => ((int)$c["completion_value"] - (int)$point)
                            )
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if ($c["completion_type"] == "CUSTOM_POINT") {
                    $point_a = $this->player_model->getPlayerPoint($pb_player_id, $c["completion_id"],
                        $validToken['site_id']);

                    if (isset($point_a[0]['value'])) {
                        $custom_point = $point_a[0]['value'];
                    } else {
                        $custom_point = 0;
                    }

                    if ((int)$c["completion_value"] > (int)$custom_point) {
                        $event = array(
                            'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                            'message' => 'Your point not enough',
                            'incomplete' => array(
                                'incompletion_id' => $c["completion_id"] . "",
                                'incompletion_type' => "CUSTOM_POINT",
                                'incompletion_value' => ((int)$c["completion_value"] - (int)$custom_point)
                            )
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if ($c["completion_type"] == "QUIZ") {
                    $complete_quiz = $this->action_model->actionLogByURL($validToken, ACTION_COMPLETE_QUIZ,
                        $c['completion_id'], $pb_player_id);
                    if ($complete_quiz == null) {
                        $event = array(
                            'event_type' => 'QUIZ_NOT_ENOUGH',
                            'message' => 'user quiz not enough',
                        );
                        array_push($missionEvent, $event);
                    }
                }
                if ($c["completion_type"] == "BADGE") {
                    if (isset($badge_player_check[$c["completion_id"] . ""])) {
                        $badge = $badge_player_check[$c["completion_id"] . ""];
                    } else {
                        $badge = 0;
                    }
                    if ((int)$badge < (int)$c["completion_value"]) {
                        $event = array(
                            'event_type' => 'BADGE_NOT_ENOUGH',
                            'message' => 'user badge not enough',
                            'incomplete' => array(
                                'incompletion_id' => $c["completion_id"] . "",
                                'incompletion_type' => "BADGE",
                                'incompletion_value' => ((int)$c["completion_value"] - (int)$badge)
                            )
                        );
                        array_push($missionEvent, $event);
                    }
                }
            }
        }

        return $missionEvent;
    }

    private function updateMissionRewardPlayer($player_id, $quest_id, $mission_id, $validToken, &$questResult)
    {

        $data = array(
            "client_id" => $validToken['client_id'],
            "site_id" => $validToken['site_id'],
            "quest_id" => $quest_id,
            "mission_id" => $mission_id
        );

        $player = $this->player_model->readPlayer($player_id, $validToken['site_id'], 'anonymous');
        $anonymous = $player['anonymous'] != null ? $player['anonymous'] : false;

        $mission = $this->quest_model->getMission($data);

        $cl_player_id = $this->player_model->getClientPlayerId($player_id, $validToken['site_id']);

        $sub_events = array(
            "events" => array(),
            "mission_id" => $mission_id . "",
            "mission_number" => $mission["missions"][0]["mission_number"],
            "mission_name" => $mission["missions"][0]["mission_name"],
            "description" => $mission["missions"][0]["description"],
            "hint" => $mission["missions"][0]["hint"],
            "image" => $mission["missions"][0]["image"],
            "quest_id" => $quest_id . ""
        );

        if (isset($mission["missions"][0]["rewards"])) {
            foreach($mission["missions"][0]["rewards"] as $rewardkey => $reward ){
                if(isset($reward['reward_data']['group']) && ($reward['reward_type'] == 'GOODS') && ($reward['reward_value'] > 0))
                {
                    $goods_group_rewards = $this->goods_model->getGoodsByGroup($validToken['client_id'], $validToken['site_id'], $reward['reward_data']['group'] , null , null , 1 );
                    $rand_goods = array_rand($goods_group_rewards, (int)$reward['reward_value']);
                    if(!is_array($rand_goods)){
                        $rand_goods = array($rand_goods);
                    }
                    $i = 1;
                    foreach($rand_goods as $index){
                        $player_goods = $this->goods_model->getPlayerGoodsGroup($validToken['site_id'], $reward['reward_data']['group'] ,$player_id);
                        if(($goods_group_rewards[$index]['per_user'] >= ($player_goods + $i)) && ($i < $reward['reward_value'])){
                            $goods_data = array('reward_value' => "1",
                                'reward_id' => $goods_group_rewards[$index]['goods_id'],
                                'reward_type' => "GOODS",
                            );
                            $goods_data['reward_data'] = $goods_group_rewards[$index];
                            array_push($mission["missions"][0]["rewards"], $goods_data);
                        }
                        $i++;
                    }
                    unset($mission["missions"][0]["rewards"][$rewardkey]);
                }
            }

            $sub_events = $this->updateReward($mission["missions"][0]["rewards"], $sub_events, $player_id,
                $cl_player_id, $validToken, $anonymous);
        }

        array_push($questResult['events_missions'], $sub_events);

        return $questResult;
    }

    private function updateQuestRewardPlayer($player_id, $quest_id, $validToken, &$questResult)
    {
        $data = array(
            "client_id" => $validToken['client_id'],
            "site_id" => $validToken['site_id'],
            "quest_id" => $quest_id
        );

        $player = $this->player_model->readPlayer($player_id, $validToken['site_id'], 'anonymous');
        $anonymous = $player['anonymous'] != null ? $player['anonymous'] : false;

        $quest = $this->quest_model->getQuest($data);

        $cl_player_id = $this->player_model->getClientPlayerId($player_id, $validToken['site_id']);

        $sub_events = array(
            "events" => array(),
            "quest_id" => $quest_id . "",
            "quest_name" => $quest["quest_name"],
            "description" => $quest["description"],
            "hint" => $quest["hint"],
            "image" => $quest["image"],
        );

        if (isset($quest["rewards"])) {
            foreach($quest["rewards"] as $rewardkey => $reward){
                if(isset($reward['reward_data']['group']) && ($reward['reward_type'] == 'GOODS') && ($reward['reward_value'] > 0))
                {
                    $goods_group_rewards = $this->goods_model->getGoodsByGroup($validToken['client_id'], $validToken['site_id'], $reward['reward_data']['group'] , null , null , 1 );
                    if($goods_group_rewards) {
                        $rand_goods = array_rand($goods_group_rewards, (int)$reward['reward_value']);
                        if(!is_array($rand_goods)){
                            $rand_goods = array($rand_goods);
                        }
                        $i = 1;
                        foreach($rand_goods as $index){
                            $player_goods = $this->goods_model->getPlayerGoodsGroup($validToken['site_id'], $reward['reward_data']['group'] ,$player_id);
                            if(($goods_group_rewards[$index]['per_user'] >= ($player_goods + $i)) && ($i < $reward['reward_value'])){
                                $goods_data = array('reward_value' => "1",
                                    'reward_id' => $goods_group_rewards[$index]['goods_id'],
                                    'reward_type' => "GOODS",
                                );
                                $goods_data['reward_data'] = $goods_group_rewards[$index];
                                array_push($quest["rewards"], $goods_data);
                            }
                            $i++;
                        }
                    }
                }
            }
            $sub_events = $this->updateReward($quest["rewards"], $sub_events, $player_id, $cl_player_id, $validToken, $anonymous);
        }

        array_push($questResult['events_quests'], $sub_events);

        return $questResult;
    }

    private function updateReward($array_reward, $sub_events, $player_id, $cl_player_id, $validToken)
    {

        $update_config = array(
            "client_id" => $validToken['client_id'],
            "site_id" => $validToken['site_id'],
            "pb_player_id" => $player_id,
            "player_id" => $cl_player_id
        );

        $player = $this->player_model->readPlayer($player_id, $validToken['site_id'], 'anonymous');
        $anonymous = $player['anonymous'] != null ? $player['anonymous'] : false;

        foreach ($array_reward as $r) {

            if ($r["reward_type"] == "BADGE") {

                $this->client_model->updateplayerBadge($r["reward_id"], $r["reward_value"], $player_id, $cl_player_id,
                    $validToken['client_id'], $validToken['site_id']);
                $badgeData = $this->client_model->getBadgeById($r["reward_id"], $validToken['site_id']);

                if (!$badgeData) {
                    break;
                }
                $event = array(
                    'event_type' => 'REWARD_RECEIVED',
                    'reward_type' => 'badge',
                    'reward_data' => $badgeData,
                    'value' => $r["reward_value"]
                );
                array_push($sub_events['events'], $event);
                $eventMessage = $this->utility->getEventMessage('badge', '', '', $event['reward_data']['name']);
                //log event - reward, badge
                $data_reward = array(
                    'reward_type' => $r["reward_type"],
                    'reward_id' => $r["reward_id"],
                    'reward_name' => $event['reward_data']['name'],
                    'reward_value' => $r["reward_value"],
                );
                $this->trackQuest($player_id, $validToken, $data_reward, $sub_events["quest_id"],
                    isset($sub_events["mission_id"]) ? $sub_events["mission_id"] : null);

                //publish to node stream
                $this->node->publish(array_merge($update_config, array(
                    'action_name' => isset($sub_events["mission_id"]) ? 'mission_reward' : 'quest_reward',
                    'action_icon' => 'fa-trophy',
                    'message' => $eventMessage,
                    'badge' => $event['reward_data'],
                    'mission' => isset($sub_events["mission_id"]) ? $sub_events : null,
                    'quest' => (!isset($sub_events["mission_id"])) ? $sub_events : null
                )), $validToken['site_name'], $validToken['site_id']);
            } elseif ($r["reward_type"] == "GOODS") {
                $player_goods = $this->goods_model->getPlayerGoods($validToken['site_id'], $r["reward_id"], $player_id);
                if(isset($r["reward_data"]['per_user']) && (int)$r["reward_data"]['per_user'] > 0){
                    $quantity = ((int)$r["reward_value"] + (isset($player_goods) && !empty($player_goods) ? $player_goods : 0)) >
                                 (int)$r["reward_data"]['per_user'] ? (int)$r["reward_data"]['per_user'] - (int)$player_goods : $r["reward_value"];
                }
                else{
                    $quantity = $r["reward_value"];
                }
                try {
                    $this->client_model->updateplayerGoods($r["reward_id"], $quantity, $player_id, $cl_player_id,
                        $validToken['client_id'], $validToken['site_id']);
                } catch (Exception $e){}
                $goods = $this->goods_model->getGoods(array_merge($validToken, array(
                    'goods_id' => new MongoId($r["reward_id"])
                )));


                if (!$goods) {
                    break;
                }
                $event = array(
                    'event_type' => 'REWARD_RECEIVED',
                    'reward_type' => 'goods',
                    'reward_data' => $goods,
                    'value' => $r["reward_value"]
                );
                array_push($sub_events['events'], $event);
                $eventMessage = $this->utility->getEventMessage('goods', '', '', $event['reward_data']['name']);
                //log event - reward, badge
                $data_reward = array(
                    'reward_type' => $r["reward_type"],
                    'reward_id' => $r["reward_id"],
                    'reward_name' => $event['reward_data']['name'],
                    'reward_value' => $r["reward_value"],
                );
                $this->trackQuest($player_id, $validToken, $data_reward, $sub_events["quest_id"],
                    isset($sub_events["mission_id"]) ? $sub_events["mission_id"] : null);

                //publish to node stream
                $this->node->publish(array_merge($update_config, array(
                    'action_name' => isset($sub_events["mission_id"]) ? 'mission_reward' : 'quest_reward',
                    'action_icon' => 'fa-trophy',
                    'message' => $eventMessage,
                    'goods' => $event['reward_data'],
                    'mission' => isset($sub_events["mission_id"]) ? $sub_events : null,
                    'quest' => (!isset($sub_events["mission_id"])) ? $sub_events : null
                )), $validToken['site_name'], $validToken['site_id']);
            } else {
                // for POINT ,CUSTOM_POINT and EXP

                if ($r["reward_type"] == "EXP") {
                    //check if player level up
                    $lv = $this->client_model->updateExpAndLevel($r["reward_value"], $player_id, $cl_player_id, array(
                        'client_id' => $validToken['client_id'],
                        'site_id' => $validToken['site_id']
                    ));
                    if ($lv > 0) {
                        $eventMessage = $this->levelup($lv, $sub_events, $update_config);
                        //publish to node stream
                        $this->node->publish(array_merge($update_config, array(
                            'action_name' => isset($sub_events["mission_id"]) ? 'mission_reward' : 'quest_reward',
                            'action_icon' => 'fa-trophy',
                            'message' => $eventMessage,
                            'level' => $lv
                        )), $validToken['site_name'], $validToken['site_id']);
                    }

                    $reward_type_message = 'point';
                    $reward_type_name = 'exp';
                } else {
                    $reward_config = array(
                        "client_id" => $validToken['client_id'],
                        "site_id" => $validToken['site_id']
                    );
                    $reward_name = $this->reward_model->getRewardName($reward_config, $r["reward_id"]);

                    $return_data = array();
                    $reward_update = $this->client_model->updateCustomReward($reward_name, $r["reward_value"],
                        $update_config, $return_data, $anonymous);

                    $reward_type_message = 'point';
                    $reward_type_name = $return_data['reward_name'];
                }

                $event = array(
                    'event_type' => 'REWARD_RECEIVED',
                    'reward_type' => $reward_type_name,
                    'value' => $r["reward_value"]
                );
                array_push($sub_events['events'], $event);
                $eventMessage = $this->utility->getEventMessage($reward_type_message, $r["reward_value"],
                    $reward_type_name);
                //log event - reward, non-custom point
                $data_reward = array(
                    'reward_type' => $r["reward_type"],
                    'reward_id' => $r["reward_id"],
                    'reward_name' => $reward_type_name,
                    'reward_value' => $r["reward_value"],
                );
                $this->trackQuest($player_id, $validToken, $data_reward, $sub_events["quest_id"],
                    isset($sub_events["mission_id"]) ? $sub_events["mission_id"] : null);

                //publish to node stream
                $this->node->publish(array_merge($update_config, array(
                    'action_name' => isset($sub_events["mission_id"]) ? 'mission_reward' : 'quest_reward',
                    'action_icon' => 'fa-trophy',
                    'message' => $eventMessage,
                    'amount' => $r["reward_value"],
                    'point' => $reward_type_name,
                    'mission' => isset($sub_events["mission_id"]) ? $sub_events : null,
                    'quest' => (!isset($sub_events["mission_id"])) ? $sub_events : null
                )), $validToken['site_name'], $validToken['site_id']);
            }

        }

        return $sub_events;
    }

    private function trackQuest($player_id, $validToken, $data_reward, $quest_id, $mission_id = null)
    {
        if ($data_reward['reward_type'] == 'CUSTOM_POINT' || $data_reward['reward_type'] == 'EXP') {
            $reward_type = 'point';
        } else {
            $reward_type = strtolower($data_reward['reward_type']);
        }
        $eventMessage = $this->utility->getEventMessage($reward_type, $data_reward['reward_value'],
            $data_reward['reward_name'], $data_reward['reward_name']);
        $data = array(
            'pb_player_id' => $player_id,
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id'],
            'quest_id' => new MongoId($quest_id),
            'mission_id' => $mission_id ? new MongoId($mission_id) : null,
            'reward_type' => $data_reward['reward_type'],
            'reward_id' => $data_reward['reward_id'],
            'reward_name' => $data_reward['reward_name'],
            'amount' => $data_reward['reward_value'],
            'message' => $eventMessage
        );
        $this->tracker_model->trackQuest($data);
    }

    private function updateMissionStatusOfPlayer($player_id, $quest_id, $mission_id, $validToken, $status = "join")
    {
        $data = array(
            'site_id' => $validToken['site_id'],
            'pb_player_id' => $player_id,
            'quest_id' => $quest_id,
            'mission_id' => $mission_id
        );
        $this->quest_model->updateMissionStatus($data, $status);
    }

    private function updateQuestStatusOfPlayer($player_id, $quest_id, $validToken, $status = "join")
    {
        $data = array(
            'site_id' => $validToken['site_id'],
            'pb_player_id' => $player_id,
            'quest_id' => $quest_id
        );
        $this->quest_model->updateQuestStatus($data, $status);
    }

    private function levelup($lv, &$sub_events, $config)
    {
        $event = array(
            'event_type' => 'LEVEL_UP',
            'value' => $lv
        );
        array_push($sub_events['events'], $event);
        $eventMessage = $this->utility->getEventMessage('level', '', '', '', $lv);
        //log event - level
        $this->tracker_model->trackEvent('LEVEL', $eventMessage, array_merge($config, array(
            'action_log_id' => null,
            'amount' => $lv
        )));

        return $eventMessage;
    }

    /*public function testQuest_get(){
        //process regular data
        $required = $this->input->checkParam(array(
            'player_id'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        //get playbasis player id from client player id
        $cl_player_id = $this->input->get('player_id');
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        )));

//        $validToken = $this->validToken;
//
//        $this->load->model('service_model');
//        $res = $this->player_model->getPlayerPointFromDateTime($pb_player_id, new MongoId("53c551c69880409d6c8b45ac"), $validToken['site_id'], new MongoDate(strtotime("2014-07-01")));
//        $res = $this->player_model->getLastEventTime($pb_player_id, $validToken['site_id'], "REWARD");
//        $res = $this->player_model->getPointHistoryFromPlayerID($pb_player_id,  $validToken['site_id'], null, 0, 100);
//        $res = $this->player_model->getPointHistoryFromPlayerID($pb_player_id,  $validToken['site_id'], new MongoId("53bbecbb988040116c8b462f"), 0, 100);
//        $res = $this->service_model->getRecentPoint($validToken['site_id'], new MongoId("53c551c69880409d6c8b45ac"), 0, 100);
//        $res = $this->service_model->getRecentPoint($validToken['site_id'], null, 0, 100);

//        $site_id = new MongoId("53a9422f988040355a8b45d3");
//        $reward_id = new MongoId("53bbecbb988040116c8b462f");
//        $starttime = new MongoDate(strtotime("2014-01-01"));
//        $endtime = new MongoDate(strtotime("2014-10-15"));

        $apiResult = $this->QuestProcess($pb_player_id, $this->validToken);
//
        $this->response($this->resp->setRespond($apiResult), 200);
//        $this->response($this->resp->setRespond($res), 200);
    }*/

    /**
     * Check quest available for the player.
     * Quest available depends on quest condition and player information.
     * return empty array if any available quest for the player, otherwise array of quest(s).
     * @param string $quest_id
     * @param string $this ->input->get("api_key")
     * @param string $this ->input->get("player_id")
     * @return mixed array of quest(s)
     * @throws PARAMETER_MISSING if api_key or player_id is not given
     * @throws INVALID_API_KEY_OR_SECRET if apikey is invalid
     * @throws USER_NOT_EXIST if user not found
     */
    public function available_get($quest_id = 0)
    {
        // check user exists
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            "cl_player_id" => $this->input->get("player_id")
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);
        }

        $data = $this->validToken;

        // get specific available quest
        if ($quest_id) {
            try {
                $quest_id = new MongoId($quest_id);
            } catch (MongoException $ex) {
                $quest_id = null;
            }

            $data['quest_id'] = $quest_id;
            $quest = $this->quest_model->getQuest($data); // get quest detail

            if (!$this->checkConditionQuest($quest, $pb_player_id, $this->validToken)) {
//                array_walk_recursive($quest, array($this, "convert_mongo_object"));
//                $resp["quest"] = $quest;
//                $resp["quest"]["quest_id"] = $quest["_id"];
//                unset($resp["quest"]["_id"]);
//                $this->response($this->resp->setRespond($resp), 200);
                $event = array(
                    'event_type' => "QUEST_AVAILABLE",
                    'event_message' => "quest available",
                    'event_status' => true,
                );
                $this->response($this->resp->setRespond($event), 200);
            } else {
                $event = array(
                    'event_type' => "QUEST_NOT_AVAILABLE",
                    'event_message' => "quest not available",
                    'event_status' => false,
                );
                $this->response($this->resp->setRespond($event), 200);
            }
        } else {
            // get all available quests related to clients
            $resp["quests"] = array();
            $quests = $this->quest_model->getQuests($data);
            foreach ($quests as $key => $quest) {
                // condition failed
                if ($this->checkConditionQuest($quests[$key], $pb_player_id, $this->validToken)) {
                    continue;
                } else {
                    $quests[$key]["quest_id"] = $quests[$key]["_id"];
                    unset($quests[$key]["_id"]);
                    unset($quests[$key]['client_id']);
                    unset($quests[$key]['site_id']);
                    if(isset($quest['rewards']) && is_array($quest['rewards'])){
                        foreach ($quest['rewards'] as $index => $reward){
                            if(isset($reward['reward_data']['_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['_id']);
                            if(isset($reward['reward_data']['client_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['client_id']);
                            if(isset($reward['reward_data']['site_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['site_id']);
                            if($reward['reward_type'] == 'GOODS'){
                                if(isset($reward['reward_data']['group'])) {
                                    $quests[$key]['rewards'][$index]['reward_data']['name'] = $quests[$key]['rewards'][$index]['reward_data']['group'];
                                }
                                unset($quests[$key]['rewards'][$index]['reward_data']['code']);
                            }
                        }
                    }
                    array_push($resp["quests"], $quests[$key]);
                }
            }
            array_walk_recursive($resp["quests"], array($this, "convert_mongo_object"));
            $this->response($this->resp->setRespond($resp), 200);
        }
    }

    /**
     * Get quest information.
     * Get paticular quest if quest_id is given,
     * otherwise get all quests for the Client's quest.
     * @param string $quest_id
     * @param string $this ->input->get("api_key")
     * @return mixed array of quest(s)
     * @throws PARAMETER_MISSING if api_key is not given
     * @throws INVALID_API_KEY_OR_SECRET if apikey is invalid
     */
    public function index_get($quest_id = 0)
    {
        $data = $this->validToken;

        if ($quest_id) {
            // get specific quest
            try {
                $quest_id = new MongoId($quest_id);
            } catch (MongoException $ex) {
                $quest_id = null;
            }

            $data['quest_id'] = $quest_id;
            $quest = $this->quest_model->getQuest($data);
            if ($quest) {
                if(isset($quest['rewards']) && is_array($quest['rewards'])){
                    foreach ($quest['rewards'] as $index => $reward){
                        if(isset($reward['reward_data']['_id'])) unset($quest['rewards'][$index]['reward_data']['_id']);
                        if(isset($reward['reward_data']['client_id'])) unset($quest['rewards'][$index]['reward_data']['client_id']);
                        if(isset($reward['reward_data']['site_id'])) unset($quest['rewards'][$index]['reward_data']['site_id']);
                        if($reward['reward_type'] == 'GOODS'){
                            if(isset($reward['reward_data']['group'])) {
                                $quest['rewards'][$index]['reward_data']['name'] = $quest['rewards'][$index]['reward_data']['group'];
                            }
                            unset($quest['rewards'][$index]['reward_data']['code']);
                        }
                    }
                }
                array_walk_recursive($quest, array($this, "convert_mongo_object"));
                $resp['quest'] = $quest;
                $resp['quest']['quest_id'] = $quest['_id'];
                unset($resp['quest']['_id']);
                unset($resp['quest']['client_id']);
                unset($resp['quest']['site_id']);
            } else {
                $this->response($this->error->setError("QUEST_JOIN_OR_CANCEL_NOTFOUND"), 200);
            }
        } else {

            if ($this->input->get('tags')){
                $data = array_merge($data, array(
                    'tags' => explode(',', $this->input->get('tags'))
                ));
            }

            // get all questss related to clients
            $quests = $this->quest_model->getQuests($data);
            array_walk_recursive($quests, array($this, "convert_mongo_object"));
            foreach ($quests as $key => $quest) {
                if(isset($quest['rewards']) && is_array($quest['rewards'])){
                    foreach ($quest['rewards'] as $index => $reward){
                        if(isset($reward['reward_data']['_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['_id']);
                        if(isset($reward['reward_data']['client_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['client_id']);
                        if(isset($reward['reward_data']['site_id'])) unset($quests[$key]['rewards'][$index]['reward_data']['site_id']);
                        if($reward['reward_type'] == 'GOODS'){
                            if(isset($reward['reward_data']['group'])) {
                                $quests[$key]['rewards'][$index]['reward_data']['name'] = $quests[$key]['rewards'][$index]['reward_data']['group'];
                            }
                            unset($quests[$key]['rewards'][$index]['reward_data']['code']);
                        }
                    }
                }
                $quests[$key]['quest_id'] = $quests[$key]['_id'];
                unset($quests[$key]['_id']);
                unset($quests[$key]['client_id']);
                unset($quests[$key]['site_id']);
            }

            $resp['quests'] = $quests;
        }
        $this->response($this->resp->setRespond($resp), 200);
    }

    /**
     * Client join particular quest
     * @param string $quest_id
     * @param string $this ->input->post("token")
     * @param string $this ->input->post("player_id")
     * @return array()
     * @throws PARAMETER_MISSING if token, player_id or quest_id not given
     * @throws INVALID_API_KEY_OR_SECRET if token is invalid
     * @throws USER_NOT_EXIST if user not found
     * @throws QUEST_CONDITION if player did not meet the quest condition
     * @throws QUEST_JOINED if player joined the quest
     * @throws QUEST_FINISHED if player finished this quest
     */
    public function join_post($quest_id = 0)
    {
        // check put parameter
        $required = $this->input->checkParam(array("player_id"));
        if ($required) {
            $this->response($this->error->setError("PARAMETER_MISSING", $required), 200);
        }

        // check quest_id input
        if (!$quest_id) {
            $this->response($this->error->setError("PARAMETER_MISSING", array("quest_id")), 200);
        }

        // check user exists
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            "cl_player_id" => $this->input->post("player_id")
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);
        }

        try {
            $quest_id = new MongoId($quest_id);
        } catch (MongoException $ex) {
            $quest_id = null;
        }

        $data = array(
            "client_id" => $this->validToken["client_id"],
            "site_id" => $this->validToken["site_id"],
            "pb_player_id" => $pb_player_id,
            "quest_id" => $quest_id
        );

        // get quest detail
        $quest = $this->quest_model->getQuest($data);

        if (!$quest) {
            $this->response($this->error->setError("QUEST_JOIN_OR_CANCEL_NOTFOUND"), 200);
        }

        // check quest_to_client
        $player_quest = $this->quest_model->getPlayerQuest($data);

        // not join yet, let check condition
        if (!$player_quest) {
            $condition_quest = $this->checkConditionQuest($quest, $pb_player_id, $this->validToken, "join");
            // condition passed
            if (!$condition_quest) {
                $this->quest_model->joinQuest(array_merge($data, $quest));
            } else // condition failed
            {
                $this->response($this->error->setError("QUEST_CONDITION", $condition_quest[0]['message']), 200);
            }
        } else {
            // already join, let check quest_to_client status
            if ($player_quest["status"] == "join") // joined
            {
                $this->response($this->error->setError("QUEST_JOINED"), 200);
            } else {
                if ($player_quest["status"] == "finish") // finished
                {
                    $this->response($this->error->setError("QUEST_FINISHED"), 200);
                } else {
                    if ($player_quest["status"] == "unjoin") {
                        // unjoin, let him join again
                        $this->quest_model->updateQuestStatus($data, "join");
                    }
                }
            }
        }
        $this->response($this->resp->setRespond(
            (isset($condition_quest) && $condition_quest) ? $condition_quest : array(
                'events' => array(
                    'event_type' => 'QUEST_JOIN',
                    'quest_id' => $quest_id . ""
                )
            )), 200);
    }

    public function joinAll_post()
    {
        // check user exists
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            "cl_player_id" => $this->input->post("player_id")
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);
        }

        $data = $this->validToken;

        // get all available quests related to clients
        $resp["quests"] = array();
        $quests = $this->quest_model->getQuests($data);

        foreach ($quests as $index => $quest) {

            $data_sub = array(
                "client_id" => $this->validToken["client_id"],
                "site_id" => $this->validToken["site_id"],
                "pb_player_id" => $pb_player_id,
                "quest_id" => $quests[$index]["_id"]
            );

            // check quest_to_client
            $player_quest = $this->quest_model->getPlayerQuest($data_sub);

            // not join yet, let check condition
            if (!$player_quest) {
                $condition_quest = $this->checkConditionQuest($quest, $pb_player_id, $this->validToken, "join");
                // condition passed
                if (!$condition_quest) {
                    $this->quest_model->joinQuest(array_merge($data_sub, $quest));
                }

            } else {
                // already join, let check quest_to_client status
                if ($player_quest["status"] == "unjoin") {
                    // unjoin, let him join again
                    $this->quest_model->updateQuestStatus($data_sub, "join");
                }
            }
        }

        $this->response($this->resp->setRespond(array("join_all" => "finish")), 200);
    }

    /**
     * Client cancel particular quest
     * @param string $quest_id
     * @param string $this ->input->post("token")
     * @param string $this ->input->post("player_id")
     * @return array()
     * @throws PARAMETER_MISSING if token, player_id or quest_id not given
     * @throws INVALID_API_KEY_OR_SECRET if token is invalid
     * @throws USER_NOT_EXIST if user not found
     * @throws QUEST_CANCEL_FAILED if player not yet join the quest or
     *                             already join but cancelled
     * @throws QUEST_FINISHED if player finished this quest
     */
    public function cancel_post($quest_id = 0)
    {
        // check delete parameter
        $required = $this->input->checkParam(array("player_id"));
        if ($required) {
            $this->response($this->error->setError("PARAMETER_MISSING", $required), 200);
        }

        // check quest_id input
        if (!$quest_id) {
            $this->response($this->error->setError("PARAMETER_MISSING", array("quest_id")), 200);
        }

        // check user exists
        $pb_player_id = $this->player_model->getPlaybasisId(
            array_merge($this->validToken, array(
                "cl_player_id" => $this->input->post("player_id")
            )));
        if (!$pb_player_id) {
            $this->response($this->error->setError("USER_NOT_EXIST"), 200);
        }

        try {
            $quest_id = new MongoId($quest_id);
        } catch (MongoException $ex) {
            $quest_id = null;
        }

        $data = array(
            "client_id" => $this->validToken["client_id"],
            "site_id" => $this->validToken["site_id"],
            "pb_player_id" => $pb_player_id,
            "quest_id" => $quest_id
        );

        // check quest_to_client
        $player_quest = $this->quest_model->getPlayerQuest($data);

        // not join yet, cannot cancel
        if (!$player_quest) {
            $this->response($this->error->setError("QUEST_CANCEL_FAILED"), 200);
        } else {
            // already join, let check quest_to_client status
            if ($player_quest["status"] == "join") // joined, let unjoin
            {
                $this->quest_model->updateQuestStatus($data, "unjoin");
            } else {
                if ($player_quest["status"] == "finish") // finished
                {
                    $this->response($this->error->setError("QUEST_FINISHED"), 200);
                } else {
                    if ($player_quest["status"] == "unjoin") {
                        // unjoin
                        $this->response($this->error->setError("QUEST_CANCEL_FAILED"), 200);
                    }
                }
            }
        }
        $this->response($this->resp->setRespond(array(
            'events' => array(
                'event_type' => 'QUEST_UNJOIN',
                'quest_id' => $quest_id . ""
            )
        )), 200);
    }

    public function mission_get($quest_id = '', $mission_id = '')
    {
        $data = $this->validToken;

        if ($quest_id && $mission_id) {
            // get specific quest
            try {
                $quest_id = new MongoId($quest_id);
            } catch (MongoException $ex) {
                $quest_id = null;
            }

            try {
                $mission_id = new MongoId($mission_id);
            } catch (MongoException $ex) {
                $this->response($this->error->setError("MISSION_NOT_FOUND"), 200);
            }

            $data['quest_id'] = $quest_id;
            $quest = $this->quest_model->getQuest($data);
            if (!$quest) {
                $this->response($this->error->setError("QUEST_JOIN_OR_CANCEL_NOTFOUND"), 200);
            }

            $data['mission_id'] = $mission_id;
            $quest = $this->quest_model->getMission($data);
            if ($quest) {
                array_walk_recursive($quest, array($this, "convert_mongo_object"));
                $resp = $quest['missions'][0];
                $resp['quest_id'] = $quest['_id'];
                unset($resp['quest']['_id']);
            } else {
                $this->response($this->error->setError("MISSION_NOT_FOUND"), 200);
            }
        }
        $this->response($this->resp->setRespond($resp), 200);
    }

    public function questOfPlayer_get($quest_id = 0)
    {
        $required = $this->input->checkParam(array('player_id'));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $player_id = $this->input->get('player_id');
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $badge_player_check = array();
        $player_badges = $this->player_model->getBadge($pb_player_id, $this->validToken['site_id']);
        if ($player_badges) {
            foreach ($player_badges as $b) {
                $badge_player_check[$b["badge_id"]] = $b["amount"];
            }
        }

        $data = array(
            'client_id' => $this->validToken['client_id'],
            'site_id' => $this->validToken['site_id'],
            'pb_player_id' => $pb_player_id
        );

        $resp = array();
        if ($quest_id) {
            // get specific quest
            try {
                $quest_id = new MongoId($quest_id);
            } catch (MongoException $ex) {
                $quest_id = null;
            }

            $data['quest_id'] = $quest_id;
            $data['status'] = array("join", "finish");
            $quest_player = $this->quest_model->getPlayerQuest($data);

            $resp['quest'] = null;
            if ($quest_player) {
                $quest = $this->quest_model->getQuest(array_merge($data, array('quest_id' => $quest_player['quest_id'])));
                if($quest) {
                    $quest['num_missions'] = array(
                        'total' => count($quest["missions"]),
                        'join' => 0,
                        'unjoin' => 0,
                        'finish' => 0
                    );

                    foreach ($quest["missions"] as $k => $m) {
                        foreach ($quest_player["missions"] as $pk => $pm) {
                            if ($m['mission_id'] == $pm['mission_id']) {
                                $quest["missions"][$k]["date_modified"] = isset($m["date_modified"]) ? $m["date_modified"] : "";
                                $quest["missions"][$k]["status"] = isset($pm["status"]) ? $pm["status"] : "";
                                $quest["missions"][$k]["pending"] = $this->checkCompletionMission($quest, $m, $pb_player_id,
                                    $this->validToken, $badge_player_check, $m);
                                if ($quest["missions"][$k]["status"]) {
                                    $quest['num_missions'][$quest["missions"][$k]["status"]]++;
                                }
                            }
                        }
                    }

                    $quest['status'] = $quest_player['status'];
                    $quest['quest_id'] = $quest_player['quest_id'];
                    unset($quest['_id']);

                    if (isset($quest['rewards']) && is_array($quest['rewards'])) {
                        foreach ($quest['rewards'] as $index => $reward) {
                            if (isset($reward['reward_data']['_id'])) unset($quest['rewards'][$index]['reward_data']['_id']);
                            if (isset($reward['reward_data']['client_id'])) unset($quest['rewards'][$index]['reward_data']['client_id']);
                            if (isset($reward['reward_data']['site_id'])) unset($quest['rewards'][$index]['reward_data']['site_id']);
                            if ($reward['reward_type'] == 'GOODS') {
                                if (isset($reward['reward_data']['group'])) {
                                    $quest['rewards'][$index]['reward_data']['name'] = $quest['rewards'][$index]['reward_data']['group'];
                                }
                                unset($quest['rewards'][$index]['reward_data']['code']);
                            }
                        }
                    }

                    array_walk_recursive($quest, array($this, "convert_mongo_object"));
                    $resp['quest'] = $quest;
                }
            }
        } else {
            // get all quests related to clients
            $data['status'] = array("join", "finish");
            $quests_player = $this->quest_model->getPlayerQuests($data);

            $resp['quests'] = null;
            $quests = array();
            foreach ($quests_player as $q) {

                if ($this->input->get('tags')){
                    $data = array_merge($data, array(
                        'tags' => explode(',', $this->input->get('tags'))
                    ));
                }

                $quest = $this->quest_model->getQuest(array_merge($data, array('quest_id' => $q['quest_id'])));
                if($quest){
                    $quest['num_missions'] = array(
                        'total' => count($quest["missions"]),
                        'join' => 0,
                        'unjoin' => 0,
                        'finish' => 0
                    );

                    foreach ($quest["missions"] as $k => $m) {
                        foreach ($q["missions"] as $pk => $pm){
                            if($m['mission_id']== $pm['mission_id']){
                                $quest["missions"][$k]["date_modified"] = isset($m["date_modified"]) ? $m["date_modified"] : "";
                                $quest["missions"][$k]["status"] = isset($pm["status"]) ? $pm["status"] : "";
                                $quest["missions"][$k]["pending"] = $this->checkCompletionMission($quest, $m, $pb_player_id,
                                    $this->validToken, $badge_player_check, $m);
                                if ($quest["missions"][$k]["status"]) {
                                    $quest['num_missions'][$quest["missions"][$k]["status"]]++;
                                }
                            }
                        }
                    }

                    $quest['status'] = $q['status'];
                    $quest['quest_id'] = $q['quest_id'];
                    unset($quest['_id']);

                    if(isset($quest['rewards']) && is_array($quest['rewards'])){
                        foreach ($quest['rewards'] as $index => $reward){
                            if(isset($reward['reward_data']['_id'])) unset($quest['rewards'][$index]['reward_data']['_id']);
                            if(isset($reward['reward_data']['client_id'])) unset($quest['rewards'][$index]['reward_data']['client_id']);
                            if(isset($reward['reward_data']['site_id'])) unset($quest['rewards'][$index]['reward_data']['site_id']);
                            if($reward['reward_type'] == 'GOODS'){
                                if(isset($reward['reward_data']['group'])) {
                                    $quest['rewards'][$index]['reward_data']['name'] = $quest['rewards'][$index]['reward_data']['group'];
                                }
                                unset($quest['rewards'][$index]['reward_data']['code']);
                            }
                        }
                    }
                    $quests[] = $quest;
                }
            }

            array_walk_recursive($quests, array($this, "convert_mongo_object"));
            if ($quests) {
                $resp['quests'] = $quests;
            }
        }
        /* filter to include only requested fields */
        $filter = $this->input->get('filter');
        if ($filter) {
            $fields = explode(',', $filter);
            if (isset($resp['quest'])) {
                $resp['quest'] = $this->filterOnlyFields($fields, $resp['quest']);
            } else {
                if (isset($resp['quests'])) {
                    $filtered_quests = array();
                    if (is_array($resp['quests'])) {
                        foreach ($resp['quests'] as $q) {
                            array_push($filtered_quests, $this->filterOnlyFields($fields, $q));
                        }
                    }
                    if ($filtered_quests) {
                        $resp['quests'] = $filtered_quests;
                    }
                }
            }
        }
        $this->response($this->resp->setRespond($resp), 200);
    }

    public function questAll_get($player_id = 0)
    {
        $pb_player_id = null;
        $badge_player_check = null;
        if ($player_id) {
            $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
                'cl_player_id' => $player_id
            )));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }

            $badge_player_check = array();
            $player_badges = $this->player_model->getBadge($pb_player_id, $this->site_id);
            if ($player_badges) {
                foreach ($player_badges as $b) {
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }
            }
        }

        $quests = $this->quest_model->getQuests($this->validToken);
        array_walk_recursive($quests, array($this, "convert_mongo_object"));
        foreach ($quests as &$quest) {
            if ($pb_player_id) {
                $quest_player = $this->quest_model->getPlayerQuest(array(
                    'site_id' => $this->site_id,
                    'pb_player_id' => $pb_player_id,
                    'quest_id' => new MongoId($quest['_id'])
                ));
                if ($quest_player) {
                    $quest['player_status'] = $quest_player['status'];
                    foreach ($quest_player["missions"] as $k => $m) {
                        $quest["missions"][$k]["date_modified"] = isset($m["date_modified"]) ? $m["date_modified"] : "";
                        $quest["missions"][$k]["status"] = isset($m["status"]) ? $m["status"] : "";
                        $quest["missions"][$k]["pending"] = $this->checkCompletionMission($quest, $m, $pb_player_id,
                            $this->validToken, $badge_player_check, $m);
                    }
                }
            }
            if(isset($quest['rewards']) && is_array($quest['rewards'])){
                foreach ($quest['rewards'] as $index => $reward){
                    if(isset($reward['reward_data']['_id'])) unset($quest['rewards'][$index]['reward_data']['_id']);
                    if(isset($reward['reward_data']['client_id'])) unset($quest['rewards'][$index]['reward_data']['client_id']);
                    if(isset($reward['reward_data']['site_id'])) unset($quest['rewards'][$index]['reward_data']['site_id']);
                    if($reward['reward_type'] == 'GOODS'){
                        if(isset($reward['reward_data']['group'])) {
                            $quest['rewards'][$index]['reward_data']['name'] = $quest['rewards'][$index]['reward_data']['group'];
                        }
                        unset($quest['rewards'][$index]['reward_data']['code']);
                    }
                }
            }
            $quest['quest_id'] = $quest['_id'];
            unset($quest['_id']);
        }
        array_walk_recursive($quests, array($this, "convert_mongo_object"));
        $resp['quests'] = $quests;
        $this->response($this->resp->setRespond($resp), 200);
    }

    /*
     * reset quest
     *
     * @param player_id string player id of client
     * @param quest_id string (optional) id of quest
     * return array
     */
    public function reset_post()
    {
        $this->benchmark->mark('start');

        $player_id = $this->input->post('player_id') ? $this->input->post('player_id') : $this->response($this->error->setError('PARAMETER_MISSING',
            array('player_id')), 200);

        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $quest_id = $this->input->post('quest_id') ? new MongoId($this->input->post('quest_id')) : null;
        $results = $this->quest_model->delete($this->client_id, $this->site_id, $pb_player_id, $quest_id);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    public function leaderBoard_get(){
        $required = $this->input->checkParam(array(
            'quest_id',
        ));

        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $query_data = $this->input->get();
        $query_data['client_id'] = $this->validToken['client_id'];
        $query_data['site_id'] = $this->validToken['site_id'];
        try{
            $query_data['quest_id'] = new MongoId($query_data['quest_id']);
        }
        catch (Exception $e){
            $this->response($this->error->setError('PARAMETER_INVALID', array('quest_id')), 200);
        }

        if($this->input->get('player_id')){
            $cl_player_id = $this->input->get('player_id');
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $cl_player_id
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
            $query_data['pb_player_id'] = $pb_player_id;
        }

        $quest_data = $this->quest_model->getQuest($query_data);
        $select = array('cl_player_id', 'first_name', 'last_name', 'username', 'image');
        if (isset($quest_data['condition']) && is_array($quest_data['condition'])) foreach($quest_data['condition'] as $condition){
            if($condition['condition_type'] == 'DATETIME_START') $query_data['starttime'] = new MongoDate(strtotime($condition['condition_value']));
            elseif ($condition['condition_type'] == 'DATETIME_END') $query_data['endtime'] = new MongoDate(strtotime($condition['condition_value']));
        }

        $result = $player_data = array();
        if(isset($query_data['completion_element_id']) && !empty($query_data['completion_element_id'])){
            try{
                $query_data['completion_element_id'] = new MongoId($query_data['completion_element_id']);
            }
            catch (Exception $e){
                $this->response($this->error->setError('PARAMETER_INVALID', array('completion_element_id')), 200);
            }

            if (isset($quest_data['missions']) && is_array($quest_data['missions'])) foreach ($quest_data['missions'] as $mission_index => $mission){
                if (isset($mission['completion']) && is_array($mission['completion'])) foreach ($mission['completion'] as $quest){
                    if($quest['completion_element_id'] == $query_data['completion_element_id']){
                        $quest_data = $quest;
                        $filter_id = $query_players = $query_player = array();
                        
                        $player_join_quest = $this->quest_model->getAllPlayerByQuestId($query_data, null, isset($query_data['status']) ? $query_data['status'] : null);

                        if (isset($player_join_quest) && is_array($player_join_quest)) foreach ($player_join_quest as $player){
                            array_push($query_players, array('id' => new MongoId($player['pb_player_id']), 'date_join' =>  isset($player['missions'][$mission_index]['date_join']) ? $player['missions'][$mission_index]['date_join'] : $player['missions'][$mission_index]['date_modified']));
                        }
    
                        $Leader_data = $this->quest_model->getLeaderboardCompletion($quest_data['completion_data']['action_id'], strtolower($quest_data['completion_filter']), $query_data,
                            (isset($quest_data['completion_op']) && !empty($quest_data['completion_op'])) ? $quest_data['completion_op'] : "sum", $query_players);
    
                        if(isset($pb_player_id)){
                            $player_quest = $this->quest_model->getPlayerQuest(array('site_id' => $this->validToken['site_id'], 'quest_id' => new MongoId($query_data['quest_id']), 'pb_player_id' => $pb_player_id));
                            if($player_quest){
                                array_push($query_player, array('id' => new MongoId($pb_player_id), 'date_join' => isset($player_quest['missions'][$mission_index]['date_join']) ? $player_quest['missions'][$mission_index]['date_join'] : $player_quest['missions'][$mission_index]['date_modified']));
                                $query_rank = array();
                                $query_rank['starttime'] = (isset($query_data['starttime']) && !empty($query_data['starttime'])) ? $query_data['starttime'] : null;
                                $query_rank['endtime'] = (isset($query_data['endtime']) && !empty($query_data['endtime'])) ? $query_data['endtime'] : null;
                                $query_rank['site_id'] = $query_data['site_id'];
                                $player_data = $this->quest_model->getLeaderboardCompletion($quest_data['completion_data']['action_id'], strtolower($quest_data['completion_filter']), $query_rank,
                                    (isset($quest_data['completion_op']) && !empty($quest_data['completion_op'])) ? $quest_data['completion_op'] : "sum" ,$query_player);
                                if(!$player_data){
                                    $player_data['current'] = 0;
                                    $player_data['date_completed'] = null;
                                } else {
                                    $player_data = $player_data[0];
                                    $player_data['date_completed'] =  isset($player_quest['missions'][$mission_index]['date_finish']) ? $player_quest['missions'][$mission_index]['date_finish'] : null;
                                }

                                $player_data['date_join'] = isset($player_quest['missions'][$mission_index]['date_join']) ? $player_quest['missions'][$mission_index]['date_join'] : $player_quest['missions'][$mission_index]['date_modified'];
                                $player_data['player'] = $this->player_model->getPlayerByPlayer($query_data['site_id'], $pb_player_id, $select);
                                $player_data['goal'] = (int)$quest['completion_value'];
                                $player_data['rank'] = (isset($query_data['status']) && !empty($query_data['status']) && ($player_quest['status'] != $query_data['status'])) ? null : 1;
                                unset($player_data['_id']);
                                array_walk_recursive($player_data, array($this, "convert_mongo_object"));
                            } else {
                                $player_data['result'] = "Player did not join quest";
                            }
                        }
    
                        if($Leader_data) {
                            $result = $Leader_data;
                            if (isset($Leader_data) && is_array($Leader_data)) foreach ($Leader_data as $leader_index => $leader) {
                                if (isset($player_join_quest) && is_array($player_join_quest)) foreach ($player_join_quest as $player_index => $player) {
                                    if ($leader['_id'] == $player['pb_player_id']) {
                                        $result[$leader_index]['player'] = $this->player_model->getPlayerByPlayer($query_data['site_id'], $player['pb_player_id'], $select);
                                        $result[$leader_index]['goal'] = (int)$quest['completion_value'];
                                        $result[$leader_index]['date_completed'] =  isset($player['missions'][$mission_index]['date_finish']) ? $player['missions'][$mission_index]['date_finish'] : null;
                                        $result[$leader_index]['date_join'] = isset($player['missions'][$mission_index]['date_join']) ? $player['missions'][$mission_index]['date_join'] : $player['missions'][$mission_index]['date_modified'];
                                        array_push($filter_id, $result[$leader_index]['_id']);
                                        unset($result[$leader_index]['_id']);
                                        break;
                                    }
                                }
                                if(!isset($result[$leader_index]['player'])) unset($result[$leader_index]);
                            }
                        }

                        //adjust player
                        if(!isset($query_data['status']) || (isset($query_data['status']) && $query_data['status'] != 'finish')){
                            $adjust_player = array();
                            $player_quest = $this->quest_model->getAllPlayerByQuestId($query_data,$filter_id, isset($query_data['status']) ? $query_data['status'] : null);
                            if (isset($player_quest) && is_array($player_quest)) foreach ($player_quest as $player_index => $player){
                                array_push($adjust_player, array(
                                    'current' => 0,
                                    'date_completed' => null,
                                    'date_join' => isset($player['missions'][$mission_index]['date_join']) ? $player['missions'][$mission_index]['date_join'] : $player['missions'][$mission_index]['date_modified'],
                                    'player' => $this->player_model->getPlayerByPlayer($query_data['site_id'], $player['pb_player_id'], $select),
                                    'goal' => (int)$quest['completion_value'],
                                ));
                            }
                            foreach ($adjust_player as $key => $row) {
                                $cl_player[$key]  = $row['player']['cl_player_id'];
                            }
                            if(count($adjust_player) > 1) array_multisort($cl_player, SORT_ASC, $adjust_player);
                            $result = array_merge(array_values($result), array_values($adjust_player));
                        }
    
                        $check_adjust_rank = true;
                        if(isset($player_data['rank']) && !is_null($player_data['rank'])) {
                            foreach ($result as $res) {
                                if (($res['player']['cl_player_id'] != $cl_player_id) && $check_adjust_rank) {
                                    $player_data['rank'] += 1;
                                } else {
                                    $check_adjust_rank = false;
                                }
                            }
                        }
    
                        $offset = isset($query_data['offset']) && !empty($query_data['offset']) ? (int)$query_data['offset'] : 0;
                        $limit = isset($query_data['limit']) && !empty($query_data['limit']) && $query_data['limit'] <= count($result) - $offset ? (int)$query_data['limit']:null;
    
                        $result = array_slice($result, $offset, $limit);
                        array_walk_recursive($result, array($this, "convert_mongo_object"));
                        break;
                    }
                }
            }
            else $this->response($this->error->setError('MISSION_NOT_FOUND'), 200);
            if(!isset($Leader_data)) $this->response($this->error->setError('COMPLETION_ELEMENT_ID_NOT_FOUND'), 200);

            $response = isset($player_data) ? array('result' => $result , 'player_data' => $player_data) : array('result' => $result);
            $this->response($this->resp->setRespond($response), 200);
        } else {
            if(isset($pb_player_id)){
                $player_quest = $this->quest_model->getPlayerQuest(array('site_id' => $this->validToken['site_id'], 'quest_id' => new MongoId($query_data['quest_id']), 'pb_player_id' => $pb_player_id));
                if($player_quest){
                    $player_data['current'] = 0;
                    $player_data['date_completed'] = null;
                    $player_data['date_join'] = null;
                    if (isset($player_quest['missions']) && is_array($player_quest['missions'])) foreach ($player_quest['missions'] as $mission_index => $mission){
                        if($mission['status'] == 'finish') $player_data['current'] += 1;
                        $player_data['date_completed'] = !$player_data['date_completed'] ? (isset($mission['date_finish']) ? $mission['date_finish'] : null) :
                                                         max($player_data['date_completed'],(isset($mission['date_finish']) ? $mission['date_finish'] : null));
                        $player_data['date_join'] = !$player_data['date_join'] ? (isset($mission['date_join']) ? $mission['date_join'] : $mission['date_modified']) :
                                                     min($player_data['date_join'],(isset($mission['date_join']) ? $mission['date_join'] : $mission['date_modified']));
                        $player_data['player'] = $this->player_model->getPlayerByPlayer($query_data['site_id'], $pb_player_id, $select);
                        $player_data['goal'] = count($player_quest['missions']);
                        $player_data['rank'] = (isset($query_data['status']) && !empty($query_data['status']) && ($player_quest['status'] != $query_data['status'])) ? null : 1;
                    }
                    else $this->response($this->error->setError('MISSION_NOT_FOUND'), 200);
                    array_walk_recursive($player_data, array($this, "convert_mongo_object"));
                } else {
                    $player_data['result'] = "Player did not join quest";
                }
            }

            $player_join_quest = $this->quest_model->getAllPlayerByQuestId($query_data, null, isset($query_data['status']) ? $query_data['status'] : null);

            if (isset($player_join_quest) && is_array($player_join_quest)) foreach ($player_join_quest as $player_index => $player){
                $result[$player_index]['current'] = 0;
                $result[$player_index]['date_completed'] = null;
                $result[$player_index]['date_join'] = null;
                if (isset($player['missions']) && is_array($player['missions'])) foreach ($player['missions'] as $mission_index => $mission){
                    if($mission['status'] == 'finish') $result[$player_index]['current'] += 1;
                    $result[$player_index]['date_completed'] = !$result[$player_index]['date_completed'] ? (isset($mission['date_finish']) ? $mission['date_finish'] : null) :
                                                              max($result[$player_index]['date_completed'],(isset($mission['date_finish']) ? $mission['date_finish'] : null));
                    $result[$player_index]['date_join'] = !$result[$player_index]['date_join'] ? (isset($mission['date_join']) ? $mission['date_join'] : $mission['date_modified']) :
                                                         min($result[$player_index]['date_join'],(isset($mission['date_join']) ? $mission['date_join'] : $mission['date_modified']));
                }
                $result[$player_index]['player'] = $this->player_model->getPlayerByPlayer($query_data['site_id'], $player['pb_player_id'], $select);
                $result[$player_index]['goal'] = count($player['missions']);
            }
            if(isset($result) && !empty($result)){
                foreach ($result as $key => $row) {
                    $current[$key]  = $row['current'];
                    $date_completed[$key] = $row['date_completed'];
                }
                if(count($result) > 1) array_multisort($current, SORT_DESC, $date_completed, SORT_ASC, $result);
            }
            $check_adjust_rank = true;
            if(isset($player_data['rank']) && !is_null($player_data['rank'])) {
                foreach ($result as $res) {
                    if (($res['player']['cl_player_id'] != $cl_player_id) && $check_adjust_rank) {
                        $player_data['rank'] += 1;
                    } else {
                        $check_adjust_rank = false;
                    }
                }
            }
            
            $offset = isset($query_data['offset']) && !empty($query_data['offset']) ? (int)$query_data['offset'] : 0;
            $limit = isset($query_data['limit']) && !empty($query_data['limit']) && $query_data['limit'] <= count($result) - $offset ? (int)$query_data['limit']:null;

            $result = array_slice($result, $offset, $limit);
            array_walk_recursive($result, array($this, "convert_mongo_object"));

            $response = isset($player_data) ? array('result' => $result, 'player_data' => $player_data) : array('result' => $result);
            $this->response($this->resp->setRespond($response), 200);
        }
    }

    protected function processFeedback($type, $input)
    {
        switch (strtolower($type)) {
            case 'email':
                $this->processEmail($input);
                break;
            case 'sms':
                $this->processSms($input);
                break;
            case 'push':
                $this->processPushNotification($input);
                break;
            case 'webhook':
                $this->processWebhook($input);
                break;
            default:
                log_message('error', 'Unknown feedback type: ' . $type);
                break;
        }
    }

    protected function processEmail($input)
    {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "email"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $access = false;
            }
        }
        if (!$access) {
            return false;
        }

        /* get email */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $email = $player && isset($player['email']) ? $player['email'] : null;
        if (!$email) {
            return false;
        }

        /* check blacklist */
        $res = $this->email_model->isEmailInBlackList($email, $input['site_id']);
        if ($res) {
            return false;
        } // banned

        /* check valid template_id */
        $template = $this->email_model->getTemplateById($input['site_id'], $input['input']['template_id']);
        if (!$template) {
            return false;
        }

        /* player-2 */
        if (isset($input['player-2'])) {
            $player2 = $this->player_model->getById($input['site_id'], $input['player-2']);
            if ($player2) {
                $player['first_name-2'] = $player2['first_name'];
                $player['last_name-2'] = $player2['last_name'];
                $player['cl_player_id-2'] = $player2['cl_player_id'];
                $player['email-2'] = $player2['email'];
                $player['phone_number-2'] = $player2['phone_number'];
                if (!isset($player2['code']) && strpos($template['body'], '{{code-2}}') !== false) {
                    $player['code-2'] = $this->player_model->generateCode($input['player-2']);
                }
            }
        }

        /* send email */
        /* before send, check whether custom domain was set by user or not*/
        $from = get_verified_custom_domain($input['client_id'], $input['site_id']);
        $to = $email;
        $subject = $input['input']['subject'];
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) {
            $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        }
        if (isset($input['coupon'])) {
            $player['coupon'] = $input['coupon'];
        }
        $message = $this->utility->replace_template_vars($template['body'], $player);
        $response = $this->utility->email($from, $to, $subject, $message);
        $this->email_model->log(EMAIL_TYPE_USER, $input['client_id'], $input['site_id'], $response, $from, $to,
            $subject, $message);
        return $response != false;
    }

    protected function processSms($input)
    {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "sms"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $access = false;
            }
        }
        if (!$access) {
            return false;
        }

        /* get phone number */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $phone = $player && isset($player['phone_number']) ? $player['phone_number'] : null;
        if (!$phone) {
            return false;
        }

        /* check valid template_id */
        $template = $this->sms_model->getTemplateById($input['site_id'], $input['input']['template_id']);
        if (!$template) {
            return false;
        }

        /* player-2 */
        if (isset($input['player-2'])) {
            $player2 = $this->player_model->getById($input['site_id'], $input['player-2']);
            if ($player2) {
                $player['first_name-2'] = $player2['first_name'];
                $player['last_name-2'] = $player2['last_name'];
                $player['cl_player_id-2'] = $player2['cl_player_id'];
                $player['email-2'] = $player2['email'];
                $player['phone_number-2'] = $player2['phone_number'];
                if (!isset($player2['code']) && strpos($template['body'], '{{code-2}}') !== false) {
                    $player['code-2'] = $this->player_model->generateCode($input['player-2']);
                }
            }
        }

        /* send SMS */
        $this->config->load("twilio", true);
        $config = $this->sms_model->getSMSClient($input['client_id'], $input['site_id']);
        $twilio = $this->config->item('twilio');
        $config['api_version'] = $twilio['api_version'];
        $this->load->library('twilio/twiliomini', $config);
        $from = $config['number'];
        $to = $phone;
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) {
            $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        }
        if (isset($input['coupon'])) {
            $player['coupon'] = $input['coupon'];
        }
        $message = $this->utility->replace_template_vars($template['body'], $player);
        $response = $this->twiliomini->sms($from, $to, $message);
        $this->sms_model->log($input['client_id'], $input['site_id'], 'user', $from, $to, $message, $response);
        return $response->IsError;
    }

    protected function processWebhook($input)
    {
        /* check valid template_id */
        $template = $this->webhook_model->getTemplateById($input['site_id'], $input['input']['template_id']);
        if (!$template) {
            return false;
        }

        $posts = http_build_query($template['body']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $template['url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$posts);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // set timeout to 1 second cURL would not proceed next process immediately after 1 second
        curl_exec($ch);
        curl_close ($ch);

        $this->webhook_model->log($input['client_id'], $input['site_id'], array(
            'pb_player_id' => new MongoId($input['pb_player_id']),
            'template_id' => $input['input']['template_id'],
            'url' => isset($template['url']) ? $template['url'] : null,
            'body' => isset($template['body']) ? $template['body'] : null,
        ));

        return true;
    }

    protected function processPushNotification($input)
    {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "push"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $access = false;
            }
        }
        if (!$access) {
            return false;
        }

        /* get devices */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $devices = $this->player_model->listDevices($input['client_id'], $input['site_id'], $input['pb_player_id'],
            array('device_token', 'os_type'));
        if (!$devices) {
            return false;
        }

        /* check valid template_id */
        $template = $this->push_model->getTemplateById($input['site_id'], $input['input']['template_id']);
        if (!$template) {
            return false;
        }

        /* player-2 */
        if (isset($input['player-2'])) {
            $player2 = $this->player_model->getById($input['site_id'], $input['player-2']);
            if ($player2) {
                $player['first_name-2'] = $player2['first_name'];
                $player['last_name-2'] = $player2['last_name'];
                $player['cl_player_id-2'] = $player2['cl_player_id'];
                $player['email-2'] = $player2['email'];
                $player['phone_number-2'] = $player2['phone_number'];
                if (!isset($player2['code']) && strpos($template['body'], '{{code-2}}') !== false) {
                    $player['code-2'] = $this->player_model->generateCode($input['player-2']);
                }
            }
        }

        /* send push notification */
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) {
            $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        }
        if (isset($input['coupon'])) {
            $player['coupon'] = $input['coupon'];
        }
        $message = $this->utility->replace_template_vars($template['body'], $player);
        $site_name = $this->client_model->findSiteNameBySiteId($input['site_id']);
        foreach ($devices as $device) {
            $this->push_model->initial(array(
                'title' => $site_name,
                'device_token' => $device['device_token'],
                'messages' => $message,
                'badge_number' => 1,
                'data' => array(
                    'player_id_1' => $player['cl_player_id'],
                    'player_id_2' => $player['cl_player_id-2']
                ),
            ), $device['os_type']);
        }
        return true;
    }

    protected function processItemNotification($input, $message, $item_info)
    {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "push"
            );
        } catch (Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED") {
                $access = false;
            }
        }
        if (!$access) {
            return false;
        }

        /* get devices */
        $devices = $this->player_model->listDevices($input['client_id'], $input['site_id'], $input['pb_player_id'],
            array('device_token', 'os_type'));
        if (!$devices) {
            return false;
        }

        $site_name = $this->client_model->findSiteNameBySiteId($input['site_id']);
        foreach ($devices as $device) {
            $this->push_model->initial(array(
                'title' => $site_name,
                'device_token' => $device['device_token'],
                'messages' => $message,
                'badge_number' => 1,
                'data' => array('client_id'=>$input['client_id'], 'site_id'=>$input['site_id'], 'player_id' => $input['player_id'], 'item_info'=>$item_info),
            ), $device['os_type']);
        }
        return true;
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }
    }

    /**
     * Filter array elements to include only allowed fields
     */
    private function filterOnlyFields($fields, $arr)
    {
        if (!$fields) {
            return $arr;
        }
        $ret = array();
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (isset($arr[$field])) {
                    $ret[$field] = $arr[$field];
                }
            }
        }
        return $ret;
    }
}

?>