<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST2_Controller.php';
define('PLAN_ID_FOR_CALL_US', '5409409daf6072480f0001ae');

class Playbasis extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('plan_model');
        $this->load->model('payment_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function plans_get()
    {
        $plans = $this->plan_model->listDisplayPlans(array('site_id' => 0));
        $plan_call_us = $this->plan_model->getPlanById(new MongoId(PLAN_ID_FOR_CALL_US));
        if (is_array($plans)) {
            if ($plan_call_us) {
                array_push($plans, $plan_call_us);
            }
        } else {
            if ($plan_call_us) {
                $plans = array($plan_call_us);
            }
        }
        $cache = array();
        if (is_array($plans)) {
            foreach ($plans as &$plan) {
                $plan['_id'] = $plan['_id']->{'$id'};
                $plan['price'] = array_key_exists('price', $plan) ? intval($plan['price']) : DEFAULT_PLAN_PRICE;
                $plan['free_flag'] = $plan['price'] <= 0;
                $plan['custom_flag'] = ($plan['_id'] == PLAN_ID_FOR_CALL_US);
                $plan['date_added'] = $plan['date_added']->sec;
                $plan['date_modified'] = $plan['date_modified']->sec;
                if (array_key_exists('feature_to_plan', $plan)) {
                    foreach ($plan['feature_to_plan'] as $i => $feature_id) {
                        $plan['feature_to_plan'][$i] = $this->getSytemFeatureById($feature_id, $cache, 'name');
                    }
                }
                if (array_key_exists('action_to_plan', $plan)) {
                    foreach ($plan['action_to_plan'] as $i => $action_id) {
                        $plan['action_to_plan'][$i] = $this->getSytemActionById($action_id, $cache, 'name');
                    }
                }
                if (array_key_exists('reward_to_plan', $plan)) {
                    foreach ($plan['reward_to_plan'] as $i => $reward) {
                        $plan['reward_to_plan'][$i]['name'] = $this->getSytemRewardById($reward['reward_id'], $cache,
                            'name');
                        unset($plan['reward_to_plan'][$i]['reward_id']);
                    }
                }
                if (array_key_exists('jigsaw_to_plan', $plan)) {
                    foreach ($plan['jigsaw_to_plan'] as $i => $jigsaw_id) {
                        $plan['jigsaw_to_plan'][$i] = $this->getSytemJigsawById($jigsaw_id, $cache, 'name');;
                    }
                }
                if (array_key_exists('limit_requests', $plan)) {
                    foreach ($plan['limit_requests'] as $key => $value) {
                        $plan['limit_requests'][str_replace('/', '', $key)] = $value;
                        unset($plan['limit_requests'][$key]);
                    }
                }
            }
        }
        $this->response($this->resp->setRespond($plans), 200);
    }

    private function getSytemFeatureById($feature_id, $cache, $field)
    {
        $key = $feature_id->{'$id'};
        if (!array_key_exists($key, $cache)) {
            $value = $this->payment_model->getSytemFeatureById($feature_id);
            $cache[$key] = $value;
        }
        return $cache[$key] && array_key_exists($field, $cache[$key]) ? $cache[$key][$field] : null;
    }

    private function getSytemActionById($action_id, $cache, $field)
    {
        $key = $action_id->{'$id'};
        if (!array_key_exists($key, $cache)) {
            $value = $this->payment_model->getSytemActionById($action_id);
            $cache[$key] = $value;
        }
        return $cache[$key] && array_key_exists($field, $cache[$key]) ? $cache[$key][$field] : null;
    }

    private function getSytemRewardById($reward_id, $cache, $field)
    {
        $key = $reward_id->{'$id'};
        if (!array_key_exists($key, $cache)) {
            $value = $this->payment_model->getSytemRewardById($reward_id);
            $cache[$key] = $value;
        }
        return $cache[$key] && array_key_exists($field, $cache[$key]) ? $cache[$key][$field] : null;
    }

    private function getSytemJigsawById($jigsaw_id, $cache, $field)
    {
        $key = $jigsaw_id->{'$id'};
        if (!array_key_exists($key, $cache)) {
            $value = $this->payment_model->getSytemJigsawById($jigsaw_id);
            $cache[$key] = $value;
        }
        return $cache[$key] && array_key_exists($field, $cache[$key]) ? $cache[$key][$field] : null;
    }

    public function test()
    {
        $this->load->view('playbasis/apitest');
    }

    public function fb()
    {
        $this->load->view('playbasis/fb');
    }

    public function login()
    {
        $this->load->view('playbasis/login');
    }

    /*
    public function memtest()
    {
        $this->load->model('auth_model');
        $this->load->model('action_model');
        $this->load->model('badge_model');
        $this->load->model('client_model');
        $this->load->model('player_model');
        $this->load->model('point_model');

        $validToken = $this->auth_model->createTokenFromAPIKey('abc');
        $hasAction = $this->action_model->findAction(array_merge($validToken, array(
            'action_name' => 'read'
        )));

        echo "findAction model return : ".$hasAction."<br><br>";

        $API['key'] = "abc";
        $API['secret'] = "abcde";
        $clientInfo = $this->auth_model->getApiInfo($API);

        echo "getApiInfo model return : ".var_dump($clientInfo)."<br><br>";

        $token = $this->auth_model->generateToken(array_merge($clientInfo, $API));

        echo "generateToken model return : ".var_dump($token)."<br><br>";

        $findToken = $this->auth_model->findToken($token['token']);

        echo "findToken model return : ".var_dump($findToken)."<br><br>";

//        $createToken = $this->auth_model->createToken("1", "1");
//
//        echo "createToken model return : ".var_dump($createToken)."<br><br>";
//
//        $createTokenFromAPIKey = $this->auth_model->createTokenFromAPIKey("abc");
//
//        echo "createTokenFromAPIKey model return : ".var_dump($createTokenFromAPIKey)."<br><br>";

        $badge = $this->badge_model->getBadge(array_merge($validToken, array(
            'badge_id' => 1
        )));

        echo "getBadge model return : ".var_dump($badge)."<br><br>";

        $badges = $this->badge_model->getAllBadges(array_merge($validToken));

        echo "getBadgegetAllBadges model return : ".var_dump($badges)."<br><br>";

        $collection = $this->badge_model->getCollection(array_merge($validToken, array(
            'collection_id' => 1
        )));

        echo "getCollection model return : ".var_dump($collection)."<br><br>";

        $collections = $this->badge_model->getAllCollection(array_merge($validToken));

        echo "getAllCollection model return : ".var_dump($collections)."<br><br>";

        $ruleSet = $this->client_model->getRuleSet(array(
            'client_id' => 1,
            'site_id' => 1
        ));

        echo "getRuleSet model return : ".var_dump($ruleSet)."<br><br>";

        $getAcion = $this->client_model->getActionId(array(
            'client_id' => 1,
            'site_id' => 1,
            'action_name' => 'like'
        ));

        echo "getActionId model return : ".$getAcion."<br><br>";

        $ruleSet = $this->client_model->getRuleSetByActionId(array(
            'client_id' => 1,
            'site_id' => 1,
            'action_id' => 1
        ));

        echo "getRuleSetByActionId model return : ".var_dump($ruleSet)."<br><br>";

        $processor = $this->client_model->getJigsawProcessor(1,1);

        echo "getJigsawProcessor model return : ".$processor."<br><br>";

        $badgePlayer = $this->client_model->updateplayerBadge(1, 1, 1, 1);

        echo "updateplayerBadge model return : ".$badgePlayer."<br><br>";

        $lv = $this->client_model->updateExpAndLevel(1, 1, 1, array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id']
        ));

        echo "updateExpAndLevel model return : ".$lv."<br><br>";

        $badge = $this->client_model->getBadgeById(1,1);

        echo "getBadgeById model return : ".var_dump($badge)."<br><br>";

        $player = $this->player_model->readPlayer(1, 1, array(
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

        echo "readPlayer model return : ".var_dump($player)."<br><br>";

        //$players = $this->player_model->readPlayers(1, array(
        //    'username',
        //    'first_name',
        //    'last_name',
        //    'gender',
        //    'image',
        //    'exp',
        //    'level',
        //    'date_added',
        //    'birth_date'
        //), 0, 10);

        //echo "readPlayers model return : ".var_dump($players)."<br><br>";

        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
            'cl_player_id' => 1
        )));

        echo "getPlaybasisId model return : ".$pb_player_id."<br><br>";

        $cl_player_id = $this->player_model->getClientPlayerId(1,1);

        echo "getClientPlayerId model return : ".$cl_player_id."<br><br>";

        $points = $this->player_model->getPlayerPoints(1,1);

        echo "getPlayerPoints model return : ".var_dump($points)."<br><br>";

        $points = $this->player_model->getPlayerPoint(1,1,1);

        echo "getPlayerPoint model return : ".var_dump($points)."<br><br>";

        $actions = $this->player_model->getLastActionPerform(1,1);

        echo "getLastActionPerform model return : ".var_dump($actions)."<br><br>";

        $actions = $this->player_model->getActionPerform(1,1,1);

        echo "getActionPerform model return : ".var_dump($actions)."<br><br>";

        $actions = $this->player_model->getActionCount(1,1,1);

        echo "getActionCount model return : ".var_dump($actions)."<br><br>";

        $badge = $this->badge_model->getBadge(array_merge($validToken, array(
            'badge_id' => 1
        )));

        echo "getBadge model return : ".var_dump($badge)."<br><br>";

        $player = $this->player_model->getLastEventTime(1,1, 'LOGIN');

        echo "getLastEventTime model return : ".$player."<br><br>";

        $leaderboard = $this->player_model->getLeaderboard("point", 10, $validToken['client_id'], $validToken['site_id']);

        echo "getLeaderboard model return : ".var_dump($leaderboard)."<br><br>";

        $input = array_merge($validToken, array(
            'pb_player_id' => 1
        ));
        $point = $this->point_model->getRewardNameById(array_merge($input, array(
            'reward_id' => 1
        )));

        echo "getRewardNameById model return : ".$point."<br><br>";

        $input = array_merge($validToken, array(
            'reward_name' => 'point'
        ));
        $haspoint = $this->point_model->findPoint($input);

        echo "findPoint model return : ".$haspoint."<br><br>";

    }
    */
}

?>