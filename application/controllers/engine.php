<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once(APPPATH . 'controllers/quest.php');
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

//class Engine extends REST2_Controller
class Engine extends Quest
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('push_model');
        $this->load->model('player_model');
        $this->load->model('action_model');
        $this->load->model('engine/jigsaw', 'jigsaw_model');
        $this->load->model('client_model');
        $this->load->model('tracker_model');
        $this->load->model('point_model');
        $this->load->model('goods_model');
        $this->load->model('redeem_model');
        $this->load->model('social_model');
        $this->load->model('email_model');
        $this->load->model('sms_model');
        $this->load->model('store_org_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('energy_model');
        $this->load->model('level_model');
        $this->load->model('game_model');
        $this->load->model('badge_model');
        $this->load->model('reward_model');
        $this->load->model('location_model');
    }

    public function getActionConfig_get()
    {
        $required = $this->input->checkParam(array(
            'api_key'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
        if (!$validToken) {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
        }
        $ruleSet = $this->client_model->getRuleSet(array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id']
        ));
        $actionConfig = array();
        foreach ($ruleSet as $rule) {
            $jigsawSet = $rule['jigsaw_set'];
            $actionId = $jigsawSet[0]['config']['action_id'];
            $actionInput = $jigsawSet[0]['config'];
            if (isset($actionConfig[$actionId])) {
                $config = array(
//              'url' => $actionInput['url'],
//              'regex' => $actionInput['regex']
                );
                $found = false;
                foreach ($actionConfig[$actionId]['config'] as $configElement) {
//              if($config['url'] != $configElement['url'] || $config['regex'] != $configElement['regex'])
                    if (isset($config['url']) && isset($configElement['url']) && ($config['url'] != $configElement['url'])) {
                        continue;
                    }
                    $found = true;
                    break;
                }
                if (!$found) {
                    array_push($actionConfig[$actionId]['config'], $config);
                }
            } else {
                $actionName = $this->client_model->getActionName(array(
                    'client_id' => $validToken['client_id'],
                    'site_id' => $validToken['site_id'],
                    'action_id' => new MongoId($jigsawSet[0]['config']['action_id'])
                ));
                $actionConfig[$actionId] = array(
                    'name' => $actionName,
                    'config' => array(
                        array(
//                      'url' => $actionInput['url'],
//                      'regex' => $actionInput['regex']
                        )
                    )
                );
            }
        }
        $this->response($this->resp->setRespond($actionConfig), 200);
    }

    public function rules_get()
    {
        /* check parameters */
        $clientData = array('client_id' => $this->client_id, 'site_id' => $this->site_id);
        $actionName = $this->input->get('action');
        if ($actionName) {
            $action = $this->client_model->getAction(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'action_name' => $actionName
            ));
            if (!$action) {
                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            }
            $clientData['action_id'] = $action['action_id'];
        }
        $pb_player_id = null;
        $player_id = $this->input->get('player_id');
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        $rules = null;
        $ruleSet = $this->client_model->getRuleSetByClientSite($clientData);
        if ($ruleSet) {
            $rules = array();
            foreach ($ruleSet as $r) {
                /* get rule detail */
                $rule = $this->client_model->getRuleDetail($this->validToken, $r['rule_id']);

                /* format output */
                $rule['id'] = $rule['_id'] . '';
                unset($rule['_id']);
                $rule['action'] = $actionName;
                unset($rule['action_id']);

                /* find current state of rule execution of the player */
                if (isset($rule['jigsaw_set'])) {
                    $input = array(
                        'site_id' => $this->site_id,
                        'rule_id' => $r['rule_id'],
                    );
                    if ($pb_player_id) {
                        $input = array_merge($input, array('pb_player_id' => $pb_player_id));
                    }
                    foreach ($rule['jigsaw_set'] as &$jigsaw) {
                        if ($pb_player_id) {
                            try {
                                $jigsaw_id = new MongoId($jigsaw['id']);
                            } catch (MongoException $ex) {
                                $jigsaw_id = "";
                            }
                            $input['jigsaw_id'] = $jigsaw_id;
                            $input['jigsaw_name'] = $jigsaw['name'];
                            $input['jigsaw_category'] = $jigsaw['category'];
                            $input['jigsaw_index'] = $jigsaw['jigsaw_index'];
                            $jigsaw['state'] = $this->jigsaw_model->getMostRecentJigsaw($input, array(
                                'input',
                                'date_added'
                            ));
                            $jigsaw['state'] = $jigsaw['state'] ? $jigsaw['state'] : null;
                        }
                        try {
                            $this->applyBadgeObjGoodsObj($this->client_id, $this->site_id, $jigsaw['config']);
                        } catch (MongoException $ex) {
                            log_message('error', 'Error in applyBadgeObjGoodsObj(), client_id = ' . $this->client_id);
                            log_message('error', 'Error in applyBadgeObjGoodsObj(), site_id = ' . $this->site_id);
                            log_message('error',
                                'Error in applyBadgeObjGoodsObj(), jigsawConfig = ' . print_r($jigsaw['config'], true));
                            $jigsaw['config']['data'] = null;
                        }
                        array_walk_recursive($jigsaw, array($this, "convert_mongo_object"));
                    }
                }
                array_push($rules, $rule);
            }
        }

        $this->response($this->resp->setRespond($rules), 200);
    }

    public function rule_get($rule_id = 0)
    {
        /* check parameters */
        if (!$rule_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('rule_id')), 200);
        }
        $pb_player_id = null;
        $player_id = $this->input->get('player_id');
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        /* get rule detail */
        $rule = $this->client_model->getRuleDetail($this->validToken, $rule_id);
        if (!$rule) {
            $this->response($this->error->setError('RULE_NOT_FOUND'), 200);
        }

        /* format output */
        $rule['id'] = $rule['_id'] . '';
        unset($rule['_id']);
        $rule['action'] = $this->action_model->findActionName($this->site_id, $rule['action_id']);
        unset($rule['action_id']);

        /* find current state of rule execution of the player */
        if (isset($rule['jigsaw_set'])) {
            $input = array(
                'site_id' => $this->site_id,
                'rule_id' => new MongoId($rule['id']),
            );
            if ($pb_player_id) {
                $input = array_merge($input, array('pb_player_id' => $pb_player_id));
            }
            foreach ($rule['jigsaw_set'] as &$jigsaw) {
                if ($pb_player_id) {
                    try {
                        $jigsaw_id = new MongoId($jigsaw['id']);
                    } catch (MongoException $ex) {
                        $jigsaw_id = "";
                    }
                    $input['jigsaw_id'] = $jigsaw_id;
                    $input['jigsaw_name'] = $jigsaw['name'];
                    $input['jigsaw_category'] = $jigsaw['category'];
                    $input['jigsaw_index'] = $jigsaw['jigsaw_index'];
                    $jigsaw['state'] = $this->jigsaw_model->getMostRecentJigsaw($input, array(
                        'input',
                        'date_added'
                    ));
                    $jigsaw['state'] = $jigsaw['state'] ? $jigsaw['state'] : null;
                }
                try {
                    $this->applyBadgeObjGoodsObj($this->client_id, $this->site_id, $jigsaw['config']);
                } catch (MongoException $ex) {
                    log_message('error', 'Error in applyBadgeObjGoodsObj(), client_id = ' . $this->client_id);
                    log_message('error', 'Error in applyBadgeObjGoodsObj(), site_id = ' . $this->site_id);
                    log_message('error',
                        'Error in applyBadgeObjGoodsObj(), jigsawConfig = ' . print_r($jigsaw['config'], true));
                    $jigsaw['config']['data'] = null;
                }
                array_walk_recursive($jigsaw, array($this, "convert_mongo_object"));
            }
        }

        $this->response($this->resp->setRespond($rule), 200);
    }

    private function applyBadgeObjGoodsObj($client_id, $site_id, &$config)
    {
        if (isset($config['group_container'])) {
            foreach ($config['group_container'] as &$each) {
                $this->applyBadgeObjGoodsObj($client_id, $site_id, $each);
            }
            return;
        }
        if (!isset($config['reward_name'])) {
            return;
        }
        switch ($config['reward_name']) {
            case 'badge':
                $config['data'] = $this->findBadgeDetail($client_id, $site_id, new MongoId($config['item_id']));
                break;
            case 'goods':
                $config['data'] = $this->findGoodsDetail($client_id, $site_id, new MongoId($config['item_id']));
                break;
            default:
                break;
        }
    }

    private function findBadgeDetail($client_id, $site_id, $item_id)
    {
        return $this->client_model->getBadgeById($item_id, $site_id);
    }

    private function findGoodsDetail($client_id, $site_id, $item_id)
    {
        $goodsData = $this->goods_model->getGoods(array('client_id'=>$client_id , 'site_id'=>$site_id ,'goods_id'=>$item_id));
        if (!$goodsData) {
            return null;
        }

        unset($goodsData['_id']);
        unset($goodsData['redeem']);
        unset($goodsData['quantity']);
        unset($goodsData['days_expire']);
        unset($goodsData['date_expired_coupon']);
        unset($goodsData['sponsor']);
        unset($goodsData['sort_order']);

        return $goodsData;
    }

    /*
     * Get response from Quest but not edit
     * any database
     */
    public function quest_post()
    {
        $token = array(
            'client_id' => $this->validToken['client_id'],
            'site_id' => $this->validToken['site_id'],
            'site_name' => null
        );
        $quest_id = $this->input->post('quest_id');

        $response = $this->QuestProcess(null, $token, $quest_id);
        $this->response($this->resp->setRespond($response), 200);
    }

    public function json_get($json = '')
    {
        $this->benchmark->mark('engine_rule_start');

        if (!$json) {
            $data = $this->input->get();
        }else{
            $json = urldecode($json);
            $data = json_decode($json, true);
            if (!$data) {
                $this->response($this->resp->setRespond('Cannot convert JSON to data'), 200);
            }
        }



        $test = isset($data['test'])?$data['test']:false;

        $required = array();
        if (!isset($data['api_key'])) {
            array_push($required, 'api_key');
        }
        if (!isset($data['action'])) {
            array_push($required, 'action');
        }

        if (!$test) {
            if (!isset($data['pb_player_id'])) {
                array_push($required, 'pb_player_id');
            }
        }

        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $api_key = $data['api_key'];

        if (!$test) {
            $pb_player_id = new MongoId($data['pb_player_id']);
        } else {
            $pb_player_id = null;
        }

        $actionName = $data['action'];

        $this->_early_checks(null, $api_key);

        //$validToken = $this->auth_model->createTokenFromAPIKey($api_key);
        $validToken = $this->validToken;
        if (!$validToken) {
            $this->response($this->error->setError('INVALID_TOKEN'), 200);
        }

        if (!$pb_player_id && !$test) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        //get playbasis player id from client player id
        $anonymous = $this->player_model->isAnonymous($validToken['client_id'], $validToken["site_id"], null,
            $pb_player_id);
        if ($pb_player_id && $anonymous) {
            /* List all active sessions of the anonymous player */
            $sessions = $this->player_model->listSessions($validToken['client_id'], $validToken["site_id"],
                $pb_player_id);
            if (count($sessions) == 0) {
                $this->response($this->error->setError('ANONYMOUS_SESSION_NOT_VALID'), 200);
            }
        }

        //get action id by action name
        $action = $this->client_model->getAction(array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id'],
            'action_name' => $actionName
        ));
        if (!$action) {
            $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
        }
        $actionId = $action['action_id'];
        $actionIcon = $action['icon'];

        $input = array_merge($data, $validToken, array(
            'pb_player_id' => $pb_player_id,
            'action_id' => $actionId,
            'action_name' => $actionName,
            'action_icon' => $actionIcon,
        ));

        if (!$test) {
            $input["test"] = false;
        }

        if (isset($data['pb_player_id-2'])) {
            $input['pb_player_id-2'] = new MongoId($data['pb_player_id-2']);
        }

        try {
            $apiResult = $this->processRule($input, $validToken, null, null);
        } catch (Exception $e) {
            if ($e->getPrevious()) {
                $prev_e = $e->getPrevious();
                $this->response($this->error->setError('PARAMETER_MISSING', array($prev_e->getMessage())), 200);
            } else {
                $this->response($this->error->setError($e->getMessage()), 200);
            }
        }

        if (!$test) {
            //Log validated action
            $action_dataset = $this->jigsaw_model->getActionDatasetInfo($input['action_name']);
            $input['parameters'] = array();
            if (is_array($action_dataset)) {
                foreach ($action_dataset as $dataset) {
                    if (isset($input[$dataset['param_name']])) {
                        $input['parameters'][$dataset['param_name']] = $input[$dataset['param_name']];
                    }
                }
            }
            $headers = $this->input->request_headers();
            $action_time = array_key_exists('Date', $headers) ? strtotime($headers['Date']) : null;
            $time = $action_time;
            if (!isset($input['node_id'])) {
                $node = $this->store_org_model->retrieveNodeByPBPlayerID($validToken['client_id'],
                    $validToken['site_id'], $pb_player_id);
                $input['node_id'] = isset($node[0]['node_id']) ? $node[0]['node_id'] : null;
            }
            $this->tracker_model->trackValidatedAction($input, $time);

            //Quest Process
            $apiQuestResult = $this->QuestProcess($pb_player_id, $validToken);
            $apiResult = array_merge($apiResult, $apiQuestResult);
        }

        $this->benchmark->mark('engine_rule_end');
        $apiResult['processing_time'] = $this->benchmark->elapsed_time('engine_rule_start', 'engine_rule_end');
        $this->response($this->resp->setRespond($apiResult), 200);
    }

    public function rule_post($option = 0)
    {
        $this->benchmark->mark('engine_rule_start');
        $fbData = null;
        $twData = null;
        if ($option == 'facebook') {
            $fbData = $this->social_model->processFacebookData($this->request->body);
            if (!$fbData['pb_player_id']) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
            if (!$fbData['action']) {
                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            }
        } else {
            if ($option == 'twitter') {
                $twData = $this->social_model->processTwitterData($this->request->body);
            }
        }
        if ($fbData) {
            //process facebook data
            $validToken = $this->auth_model->createToken($fbData['client_id'], $fbData['site_id']);
            if (!$validToken) {
                $this->response($this->error->setError('INVALID_TOKEN'), 200);
            }
            $pb_player_id = $fbData['pb_player_id'];
            if ($pb_player_id < 0) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
            $actionName = $fbData['action'];
            $action = $this->client_model->getAction(array(
                'client_id' => $validToken['client_id'],
                'site_id' => $validToken['site_id'],
                'action_name' => $actionName
            ));
            if (!$action) {
                $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
            }
            $actionId = $action['action_id'];
            $actionIcon = $action['icon'];
            $input = array_merge($validToken, array(
                'pb_player_id' => $pb_player_id,
                'action_id' => $actionId,
                'action_name' => $actionName,
                'action_icon' => $actionIcon
            ));
            try {
                $apiResult = $this->processRule($input, $validToken, $fbData, $twData);
            } catch (Exception $e) {
                if ($e->getPrevious()) {
                    $prev_e = $e->getPrevious();
                    $this->response($this->error->setError('PARAMETER_MISSING', array($prev_e->getMessage())), 200);
                } else {
                    $this->response($this->error->setError($e->getMessage()), 200);
                }
            }
        } else {
            if ($twData) {
                //proces twitter data
                $apiResult = null;
                foreach ($twData['sites'] as $site) {
                    $validToken = $this->auth_model->createToken($site['client_id'], $site['site_id']);
                    if (!$validToken) {
                        continue;
                    }
                    $pb_player_id = $site['pb_player_id'];
                    if ($pb_player_id < 0) {
                        continue;
                    }
                    $actionName = $twData['action'];
                    $action = $this->client_model->getAction(array(
                        'client_id' => $validToken['client_id'],
                        'site_id' => $validToken['site_id'],
                        'action_name' => $actionName
                    ));
                    if (!$action) {
                        continue;
                    }
                    $actionId = $action['action_id'];
                    $actionIcon = $action['icon'];
                    $input = array_merge($validToken, array(
                        'pb_player_id' => $pb_player_id,
                        'action_id' => $actionId,
                        'action_name' => $actionName,
                        'action_icon' => $actionIcon
                    ));
                    try {
                        $apiResult = $this->processRule($input, $validToken, $fbData, $twData);
                    } catch (Exception $e) {
                        if ($e->getPrevious()) {
                            $prev_e = $e->getPrevious();
                            $this->response($this->error->setError('PARAMETER_MISSING', array($prev_e->getMessage())),
                                200);
                        } else {
                            $this->response($this->error->setError($e->getMessage()), 200);
                        }
                    }
                }
            } else {
                $test = $this->input->post("test");
                $required = null;

                //process regular data
                if (!$test) {
                    $required = $this->input->checkParam(array('action', 'player_id'));
                }
                if ($required) {
                    $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
                }

                if (!$test) {
                    $validToken = $this->validToken;
                } else {
                    $validToken = array(
                        "client_id" => new MongoId($this->input->post("client_id")),
                        "site_id" => new MongoId($this->input->post("site_id")),
                        "site_name" => null
                    );
                }
                if (!$validToken) {
                    $this->response($this->error->setError('INVALID_TOKEN'), 200);
                }

                //get action id by action name
                $actionName = $this->input->post('action');
                $action = $this->client_model->getAction(array(
                    'client_id' => $validToken['client_id'],
                    'site_id' => $validToken['site_id'],
                    'action_name' => $actionName
                ));
                if (!$action) {
                    $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);
                }
                $actionId = $action['action_id'];
                $actionIcon = $action['icon'];

                //get playbasis player id from client player id
                $pb_player_id = array();
                if (!$test) {
                    $cl_player_id = $this->input->post('player_id');
                    $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array('cl_player_id' => $cl_player_id)));
                    if(!$pb_player_id){
                        usleep(500000);
                        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array('cl_player_id' => $cl_player_id)));
                    }
                    $anonymous = $this->player_model->isAnonymous($validToken['client_id'], $validToken["site_id"], $cl_player_id);
                    if ($pb_player_id && $anonymous) {
                        /* List all active sessions of the anonymous player */
                        $sessions = $this->player_model->listSessions($validToken['client_id'], $validToken["site_id"],
                            $pb_player_id);
                        if (count($sessions) == 0) {
                            $this->response($this->error->setError('ANONYMOUS_SESSION_NOT_VALID'), 200);
                        }
                    }
                }

                if (!$pb_player_id && !$test) {
                    log_message('error',
                        '[debug-case-pbapp_auto_user] player_id = ' . $this->input->post('player_id') . ', action = ' . $this->input->post('action'));
                    //$this->response($this->error->setError('USER_NOT_EXIST'), 200);
                    //create user with tmp data for this id
                    $pb_player_id = $this->player_model->createPlayer(array_merge($validToken, array(
                        'player_id' => $cl_player_id,
                        'image' => $this->config->item('DEFAULT_PROFILE_IMAGE'),
                        'email' => 'qa1+'.$cl_player_id.'@playbasis.com',
                        'username' => $cl_player_id,
                        'first_name' => $cl_player_id,
                        'nickname' => $cl_player_id,
                    )));
                    /* Automatically energy initialization after creating a new player*/
                    $energys = $this->energy_model->findActiveEnergyRewardsById($this->validToken['client_id'],
                        $this->validToken['site_id']);
                    foreach ($energys as $energy) {
                        $energy_reward_id = $energy['reward_id'];
                        $energy_max = (int)$energy['energy_props']['maximum'];
                        $batch_data = array();
                        if ($energy['type'] == 'gain') {
                            array_push($batch_data, array(
                                'pb_player_id' => $pb_player_id,
                                'cl_player_id' => $cl_player_id,
                                'client_id' => $this->validToken['client_id'],
                                'site_id' => $this->validToken['site_id'],
                                'reward_id' => $energy_reward_id,
                                'value' => $energy_max,
                                'date_cron_modified' => new MongoDate(),
                                'date_added' => new MongoDate(),
                                'date_modified' => new MongoDate()
                            ));
                        } elseif ($energy['type'] == 'loss') {
                            array_push($batch_data, array(
                                'pb_player_id' => $pb_player_id,
                                'cl_player_id' => $cl_player_id,
                                'client_id' => $this->validToken['client_id'],
                                'site_id' => $this->validToken['site_id'],
                                'reward_id' => $energy_reward_id,
                                'value' => 0,
                                'date_cron_modified' => new MongoDate(),
                                'date_added' => new MongoDate(),
                                'date_modified' => new MongoDate()
                            ));
                        }
                        if (!empty($batch_data)) {
                            $this->energy_model->bulkInsertInitialValue($batch_data);
                        }
                    }
                }

                $postData = $this->input->post();
                $input = array_merge($postData, $validToken, array(
                    'pb_player_id' => $pb_player_id,
                    'action_id' => $actionId,
                    'action_name' => $actionName,
                    'action_icon' => $actionIcon
                ));
                if (!$test) {
                    $input["test"] = false;
                }

                try {
                    $apiResult = $this->processRule($input, $validToken, $fbData, $twData);
                } catch (Exception $e) {
                    if ($e->getPrevious()) {
                        $prev_e = $e->getPrevious();
                        $this->response($this->error->setError('PARAMETER_MISSING', array($prev_e->getMessage())), 200);
                    } else {
                        $this->response($this->error->setError($e->getMessage()), 200);
                    }
                }
            }
        }
        //Log validated action
        if (!$test) {
            // populate input parameter of the action
            $action_dataset = $this->jigsaw_model->getActionDatasetInfo($input['action_name']);
            $input['parameters'] = array();
            if (is_array($action_dataset)) {
                foreach ($action_dataset as $dataset) {
                    if (isset($input[$dataset['param_name']])) {
                        $input['parameters'][$dataset['param_name']] = $input[$dataset['param_name']];
                    }
                }
            }
            $headers = $this->input->request_headers();
            $action_time = array_key_exists('Date', $headers) ? strtotime($headers['Date']) : null;
            $time = $action_time;

            if (!isset($input['node_id'])) {
                $node = $this->store_org_model->retrieveNodeByPBPlayerID($validToken['client_id'],
                    $validToken['site_id'], $pb_player_id);
                $input['node_id'] = isset($node[0]['node_id']) ? $node[0]['node_id'] : null;
            }

            // track validated action in the log
            $this->tracker_model->trackValidatedAction($input, $time);
        }
        //Quest Process
        if (!$test) {
            $apiQuestResult = $this->QuestProcess($pb_player_id, $validToken);
            $apiResult = array_merge($apiResult, $apiQuestResult);
        }

        $this->benchmark->mark('engine_rule_end');
        $apiResult['processing_time'] = $this->benchmark->elapsed_time('engine_rule_start', 'engine_rule_end');
        array_walk_recursive($apiResult, array($this, "convert_mongo_object"));
        $this->response($this->resp->setRespond($apiResult), 200);
    }

    private function levelup($lv, &$apiResult, $input)
    {
        $event = array(
            'event_type' => 'LEVEL_UP',
            'value' => $lv
        );
        array_push($apiResult['events'], $event);
        $eventMessage = $this->utility->getEventMessage('level', '', '', '', $lv);
        //log event - level
        $this->tracker_model->trackEvent('LEVEL', $eventMessage, array_merge($input, array(
            'amount' => $lv
        )));
        return $eventMessage;
    }

    public function processRule(&$input, $validToken, $fbData, $twData, $time = null)
    {
        if (!isset($input['player_id']) || !$input['player_id']) {
            if (!$input["test"]) {
                $input['player_id'] = $this->player_model->getClientPlayerId(
                    $input['pb_player_id'], $validToken['site_id']);
            }
        }
        if (!$input["test"] && !isset($input['node_id'])) {
            $node = $this->store_org_model->retrieveNodeByPBPlayerID($validToken['client_id'], $validToken['site_id'],
                $input['pb_player_id']);
            $input['node_id'] = isset($node[0]['node_id']) ? $node[0]['node_id'] : null;
        }

        if (!$input["test"]) {
            $anonymousUser = $this->player_model->isAnonymous($validToken['client_id'], $validToken['site_id'], null,
                $input['pb_player_id']);
        } else {
            $anonymousUser = false;
        }

        $headers = $this->input->request_headers();

        $action_time = array_key_exists('Date', $headers) ? strtotime($headers['Date']) : null;
        if ($time == null) {
            $time = $action_time;
        }
        if (!$input["test"]) {

            // populate input parameter of the action
            $action_dataset = $this->jigsaw_model->getActionDatasetInfo($input['action_name']);
            $input['parameters'] = array();
            if (is_array($action_dataset)) {
                foreach ($action_dataset as $dataset) {
                    if (isset($input[$dataset['param_name']])) {
                        $input['parameters'][$dataset['param_name']] = $input[$dataset['param_name']];
                    }
                }
            }
            $input['action_log_id'] = $this->tracker_model->trackAction($input, $time); //track action
            $input['action_log_time'] = $this->action_model->findActionLogTime($validToken['site_id'],
                $input['action_log_id']);
        }

        $client_id = $validToken['client_id'];
        $site_id = $validToken['site_id'];
        $site_name = $validToken['site_name'];

        if (!$input["test"] && isset($input['session_id'])) {
            $setting = $this->player_model->getSecuritySetting($validToken['client_id'], $validToken['site_id']);
            $session = $this->player_model->findBySessionId($client_id, $site_id, $input['session_id']);
            if (!$session) {
                throw new Exception('SESSION_IS_EXPIRED');
            } elseif (isset($setting['timeout']) && $setting['timeout'] > 0) {
                $session_expires_in = $setting['timeout'];
                $this->player_model->login($client_id, $site_id, $input['pb_player_id'], $input['session_id'],
                    $session_expires_in);
            }
        }
        if (!isset($input['site_id']) || !$input['site_id']) {
            $input['site_id'] = $site_id;
        }

        if (isset($input["rule_id"])) {
            $ruleSet = $this->client_model->getRuleSetById($input["rule_id"]);
        } else {
            $ruleSet = $this->client_model->getRuleSetByClientSite(array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'action_id' => $input['action_id']
            ));
        }

        $apiResult = array(
            'events' => array()
        );

        /* if no matched rules then log empty info in jigsaw_log */
        if (!$ruleSet) {
            if (!$input["test"]) {
                $this->client_model->log($input);
            }
            return $apiResult;
        }

        /* [rule usage] check rule usage against the associated plan */
        if (!$input["test"]) {
            $this->client_model->permissionCheck(
                $this->client_data,
                $client_id,
                $site_id,
                "others",
                "rule"
            );
        }

        $cache_jigsaw = array();
        foreach ($ruleSet as $rule) {
            /* [rule usage] init */
            $count = 0;
            $last_jigsaw = null;
            $last_coupon = array();

            $input['rule_id'] = new MongoId($rule['rule_id']);
            $input['rule_name'] = $rule['name'];
            $input['rule_time'] = new MongoDate(time());
            $jigsawSet = (isset($rule['jigsaw_set']) && !empty($rule['jigsaw_set'])) ? $rule['jigsaw_set'] : array();

            foreach ($jigsawSet as $jigsaw) {
                try {
                    $jigsaw_id = new MongoId($jigsaw['id']);
                } catch (MongoException $ex) {
                    $jigsaw_id = "";
                }

                $input['jigsaw_id'] = $jigsaw_id;
                $input['jigsaw_name'] = $jigsaw['name'];
                $input['jigsaw_category'] = $jigsaw['category'];
                $input['jigsaw_index'] = $jigsaw['jigsaw_index'];
                $jigsaw['config'] = $this->normalize_jigsawConfig($jigsaw['config']);
                $input['input'] = $jigsaw['config'];
                $exInfo = array();
                $jigsawConfig = $jigsaw['config'];
                $jigsawCategory = $jigsaw['category'];

                // Level condition
                if (($input['jigsaw_name']) == 'level') {
                    //read player information
                    $player['player'] = $this->player_model->readPlayer($input['pb_player_id'], $this->site_id, array(
                        'exp',
                    ));

                    $level = $this->level_model->getLevelByExp($player['player']['exp'], $this->validToken['client_id'],
                        $this->validToken['site_id']);
                    $input['level'] = $level['level'];
                }

                // Level condition
                if (($input['jigsaw_name']) == 'locationArea') {
                    //read player information
                    $location_info = $this->location_model->getLocation($client_id, $site_id, array('location_id' => $jigsawConfig['location_id']));
                    $jigsawConfig['latitude'] = isset($location_info[0]['latitude']) ? $location_info[0]['latitude'] : null;
                    $jigsawConfig['longitude'] = isset($location_info[0]['longitude']) ? $location_info[0]['longitude'] : null;
                }

                // User profile condition
                if (($input['jigsaw_name']) == 'userProfile') {
                    //read player information
                    $player_profile = $this->player_model->readPlayer($input['pb_player_id'], $this->site_id, array(
                        'exp','gender','birth_date'
                    ));

                    $level = $this->level_model->getLevelByExp($player_profile['exp'], $this->validToken['client_id'],
                        $this->validToken['site_id']);

                    $player_profile['level'] = isset($level['level']) ? $level['level'] : null;
                    $input['user_profile'] = $player_profile;
                }

                // Game level condition
                if (($input['jigsaw_name']) == 'gameLevel') {
                    //get current game stage of player
                    $stage_to_player = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $jigsawConfig['game_id'], $input['pb_player_id'], array('is_current' => true));
                    if ($stage_to_player) {
                        $input['game_current_stage'] = $stage_to_player['stage_level'];
                    } else {
                        $input['game_current_stage'] = 1;
                    }

                }

                // Badge condition
                if (($input['jigsaw_name']) == 'badge') {
                    //read player badge information
                    $badge = $this->player_model->getBadge($input['pb_player_id'], $this->site_id);

                    $input['player_badge'] = $badge;
                }

                // support formula-based quantity
                foreach ( array('quantity', 'param_value') as $config_key){
                    if (isset($jigsawConfig[$config_key]) && strpos($jigsawConfig[$config_key], '{') !== false) {

                        $f = $jigsawConfig[$config_key];
                        foreach ($input as $key => $value) {
                            if (!is_string($value)) {
                                continue;
                            }
                            $f = str_replace('{' . $key . '}', $value, $f);
                        }


                        if($config_key == 'quantity'){
                            require_once APPPATH . '/libraries/ipsum/Parser.class.php';
                            $parser = new Parser($f . '\0');
                            try {
                                $jigsawConfig[$config_key] = intval($parser->run());
                            } catch (Exception $e) {
                                log_message('error', 'Error during evaluation (formula = ' . $f . '), e = ' . $e->getMessage());
                                $jigsawConfig[$config_key] = 0;
                            }
                        }else{
                            $jigsawConfig[$config_key] = $f;
                        }
                    }
                }
                if ((($input['jigsaw_name']) == 'redeem') && isset($jigsawConfig['group_container']) && is_array($jigsawConfig['group_container'])) {
                    foreach ($jigsawConfig['group_container'] as &$jigsawConfigGroup) {
                        if (isset($jigsawConfigGroup['quantity']) && strpos($jigsawConfigGroup['quantity'], '{') !== false) {

                            $f = $jigsawConfigGroup['quantity'];
                            foreach ($input as $key => $value) {
                                if (!is_string($value)) {
                                    continue;
                                }
                                $f = str_replace('{' . $key . '}', $value, $f);
                            }

                            require_once APPPATH . '/libraries/ipsum/Parser.class.php';
                            $parser = new Parser($f . '\0');
                            try {
                                $jigsawConfigGroup['quantity'] = intval($parser->run());
                            } catch (Exception $e) {
                                log_message('error', 'Error during evaluation (formula = ' . $f . '), e = ' . $e->getMessage());
                                $jigsawConfigGroup['quantity'] = 0;
                            }
                        }
                    }
                }

                //get class path to process jigsaw
                $processor = ($jigsaw_id ? $this->client_model->getJigsawProcessorWithCache($cache_jigsaw, $jigsaw_id,
                    $site_id) : $jigsaw['id']);
                if ($processor == 'goods') {
                    $processor = 'reward';
                }

                if($processor=="groupNot" || $processor=="groupOr"){
                    // check if condition group containing item group
                    if(array_search("badge", array_column($jigsawConfig['condition_group_container'], 'param_name')) !== false){
                        //read player badge information
                        $badge = $this->player_model->getBadge($input['pb_player_id'], $this->site_id);
                        $input['player_badge'] = $badge;
                    }
                }

                if (!$input["test"]) {
                    $jigsaw_model = $this->jigsaw_model->$processor($jigsawConfig, $input, $exInfo);
                } else {
                    $jigsaw_model = true;
                }

                /* [rule usage] increase rule usage counter if a reward is given on chunk-based basis */
                if ($this->is_reward($jigsaw['category']) && !$this->is_reward($last_jigsaw)) {
                    $count++;
                }

                if ($jigsaw_model) {

                    /* pre-processing in case of 'GROUP' */
                    $break = false;
                    $jigsawName = $input['jigsaw_name'];
                    $isGroup = false;
                    if ($jigsawCategory == 'GROUP') {
                        $isGroup = true;
                        $break = $exInfo['break'];
                        $conf = $jigsawConfig['group_container'][$exInfo['index']];
                        $jigsawConfig = $this->normalize_jigsawConfig(array_merge($jigsawConfig, $conf));
                        if (array_key_exists('reward_name', $conf)) {
                            $jigsawCategory = 'REWARD';
                        } else {
                            if (array_key_exists('feedback_name', $conf)) {
                                $jigsawCategory = 'FEEDBACK';
                                foreach (array('feedback_name', 'template_id', 'subject') as $field) {
                                    if (array_key_exists($field, $conf)) {
                                        $input['input'][$field] = $conf[$field];
                                    }
                                }
                                $jigsawName = $conf['feedback_name'];
                            }
                        }
                    }

                    /* process 'REWARD' or 'FEEDBACK' */
                    if ($jigsawCategory == 'REWARD') {
                        if (isset($exInfo['dynamic'])) {
                            //reward is a custom point
                            assert('$exInfo["dynamic"]["reward_name"]');
                            assert('$exInfo["dynamic"]["quantity"]');

                            if (!$input["test"] && !$anonymousUser) {
                                $lv = $this->client_model->updateCustomReward(
                                    $exInfo['dynamic']['reward_name'],
                                    $exInfo['dynamic']['quantity'],
                                    $input,
                                    $jigsawConfig,
                                    $anonymousUser);
                            }

                            $event = array(
                                'event_type' => isset($jigsawConfig['reward_status']) && !empty($jigsawConfig['reward_status']) ? $jigsawConfig['reward_status'] : 'REWARD_RECEIVED',
                                'reward_type' => $jigsawConfig['reward_name']
                            );
                            $event['value'] = $event['event_type'] == "REWARD_NOT_AVAILABLE" ? "0" : $jigsawConfig['quantity']."";

                            if (isset($jigsawConfig['transaction_id']) && !empty($jigsawConfig['transaction_id'])){
                                $event['transaction_id'] = $jigsawConfig['transaction_id'];
                            }

                            array_push($apiResult['events'],
                                $isGroup ? array_merge($event, array('index' => $exInfo['index'])) : $event);

                            if (!$input["test"] && !$anonymousUser) {
                                $eventMessage = $this->utility->getEventMessage(
                                    'point',
                                    $jigsawConfig['quantity'],
                                    $jigsawConfig['reward_name']);

                                //log event - reward, custom point
                                $this->tracker_model->trackEvent(
                                    'REWARD',
                                    $eventMessage,
                                    array_merge($input, array(
                                        'reward_id' => $jigsawConfig['reward_id'],
                                        'reward_name' => $jigsawConfig['reward_name'],
                                        'amount' => $jigsawConfig['quantity']
                                    )));

                                //publish to node stream
                                $this->node->publish(array_merge($input, array(
                                    'message' => $eventMessage,
                                    'amount' => $jigsawConfig['quantity'],
                                    'point' => $jigsawConfig['reward_name']
                                )), $site_name, $site_id);

                                //publish to facebook notification
                                if ($fbData) {
                                    $this->social_model->sendFacebookNotification(
                                        $client_id,
                                        $site_id,
                                        $fbData['facebook_id'],
                                        $eventMessage,
                                        '');
                                }

                                if ($lv > 0) {
                                    $eventMessage = $this->levelup($lv, $apiResult, $input);
                                    //publish to node stream
                                    $this->node->publish(array_merge($input, array(
                                        'message' => $eventMessage,
                                        'level' => $lv
                                    )), $site_name, $site_id);
                                    //publish to facebook notification
                                    if ($fbData) {
                                        $this->social_model->sendFacebookNotification(
                                            $client_id,
                                            $site_id,
                                            $fbData['facebook_id'],
                                            $eventMessage,
                                            '');
                                    }
                                }
                            }  // close if (!$input["test"])
                        } else {
                            if (is_null($jigsawConfig['item_id']) || $jigsawConfig['item_id'] == '') {
                                //item_id is null, process standard point-based rewards (exp, point)
                                if ($jigsawConfig['reward_name'] == 'exp') {
                                    //check if player level up
                                    if (!$input["test"]) {
                                        $lv = $this->client_model->updateExpAndLevel(
                                            $jigsawConfig['quantity'],
                                            $input['pb_player_id'],
                                            $input['player_id'],
                                            array(
                                                'client_id' => $validToken['client_id'],
                                                'site_id' => $validToken['site_id']
                                            ));
                                        if ($lv > 0) {
                                            $eventMessage = $this->levelup($lv, $apiResult, $input);
                                            //publish to node stream
                                            $this->node->publish(array_merge($input, array(
                                                'message' => $eventMessage,
                                                'level' => $lv
                                            )), $site_name, $site_id);
                                            //publish to facebook notification
                                            if ($fbData) {
                                                $this->social_model->sendFacebookNotification(
                                                    $client_id,
                                                    $site_id,
                                                    $fbData['facebook_id'],
                                                    $eventMessage,
                                                    '');
                                            }
                                        }
                                    }  // close if (!$input["test"])
                                } else {
                                    //update point-based reward
                                    if (!$input["test"]) {
                                        $reward = $this->client_model->updatePlayerPointReward(
                                            $jigsawConfig['reward_id'],
                                            $jigsawConfig['quantity'],
                                            $input['pb_player_id'],
                                            $input['player_id'],
                                            $input['client_id'],
                                            $input['site_id'],
                                            $anonymousUser);
                                    }
                                }  // close if ($jigsawConfig["reward_name"] == 'exp')

                                $event = array(
                                    'event_type' => isset($reward['reward_status']) && !empty($reward['reward_status']) ? $reward['reward_status'] : 'REWARD_RECEIVED',
                                    'reward_type' => $jigsawConfig['reward_name'],
                                );
                                $event['value'] = $event['event_type'] == "REWARD_NOT_AVAILABLE" ? "0" : $jigsawConfig['quantity']."";

                                if (isset($reward['transaction_id']) && !empty($reward['transaction_id'])) {
                                    $event['transaction_id'] = $reward['transaction_id'];
                                }
                                array_push($apiResult['events'],
                                    $isGroup ? array_merge($event, array('index' => $exInfo['index'])) : $event);

                                if (!$input["test"]) {
                                    $eventMessage = $this->utility->getEventMessage(
                                        'point',
                                        $jigsawConfig['quantity'],
                                        $jigsawConfig['reward_name']);
                                    //log event - reward, non-custom point
                                    $this->tracker_model->trackEvent(
                                        'REWARD',
                                        $eventMessage,
                                        array_merge($input, array(
                                            'reward_id' => $jigsawConfig['reward_id'],
                                            'reward_name' => $jigsawConfig['reward_name'],
                                            'amount' => $jigsawConfig['quantity']
                                        )));
                                    //publish to node stream
                                    $this->node->publish(array_merge($input, array(
                                        'message' => $eventMessage,
                                        'amount' => $jigsawConfig['quantity'],
                                        'point' => $jigsawConfig['reward_name']
                                    )), $site_name, $site_id);
                                    //publish to facebook notification
                                    if ($fbData) {
                                        $this->social_model->sendFacebookNotification(
                                            $client_id,
                                            $site_id,
                                            $fbData['facebook_id'],
                                            $eventMessage,
                                            '');
                                    }

                                    if(isset($jigsawConfig['custom_log']) && $jigsawConfig['custom_log']){
                                        if(isset($input[$jigsawConfig['custom_log']]) && $input[$jigsawConfig['custom_log']]){
                                            $this->reward_model->addCustomLog($client_id, $site_id, $input['player_id'],
                                                $input['pb_player_id'], $jigsawConfig['reward_id'], $jigsawConfig['custom_log'], $input[$jigsawConfig['custom_log']]);
                                        }
                                    }

                                }  // close if (!$input["test"])
                            } else {
                                switch ($jigsawConfig['reward_name']) {
                                    case 'badge':
                                        if (!$input["test"]) {
                                            $this->client_model->updateplayerBadge(
                                                $jigsawConfig['item_id'],
                                                $jigsawConfig['quantity'],
                                                $input['pb_player_id'],
                                                $input['player_id'],
                                                $client_id, $site_id);
                                        }

                                        $badgeData = $this->client_model->getBadgeById(
                                            $jigsawConfig['item_id'],
                                            $site_id);
                                        if (!$badgeData) {
                                            break;
                                        }

                                        $event = array(
                                            'event_type' => 'REWARD_RECEIVED',
                                            'reward_type' => $jigsawConfig['reward_name'],
                                            'reward_data' => $badgeData,
                                            'value' => $jigsawConfig['quantity']
                                        );
                                        array_push($apiResult['events'], $isGroup ? array_merge($event,
                                            array('index' => $exInfo['index'])) : $event);

                                        if (!$input["test"]) {
                                            $eventMessage = $this->utility->getEventMessage(
                                                $jigsawConfig['reward_name'],
                                                '',
                                                '',
                                                $event['reward_data']['name']);
                                            //log event - reward, badge
                                            $this->tracker_model->trackEvent('REWARD', $eventMessage,
                                                array_merge($input, array(
                                                    'reward_id' => $jigsawConfig['reward_id'],
                                                    'reward_name' => $jigsawConfig['reward_name'],
                                                    'item_id' => $jigsawConfig['item_id'],
                                                    'amount' => $jigsawConfig['quantity']
                                                )));
                                            //publish to node stream
                                            $this->node->publish(array_merge($input, array(
                                                'message' => $eventMessage,
                                                'badge' => $event['reward_data']
                                            )), $site_name, $site_id);
                                            //publish to facebook notification
                                            if ($fbData) {
                                                $this->social_model->sendFacebookNotification(
                                                    $client_id,
                                                    $site_id,
                                                    $fbData['facebook_id'],
                                                    $eventMessage,
                                                    '');
                                            }

                                            $auto_notify = $this->badge_model->getBadgeNotificationFlag($client_id, $site_id, $jigsawConfig['item_id']);
                                            if ($auto_notify){
                                                $item_info = array(
                                                    'item_name' => $badgeData['name'],
                                                    'item_id' => $badgeData['badge_id'],
                                                    'amount' => $jigsawConfig['quantity']
                                                );
                                                $this->processItemNotification($input,'item received', $item_info);
                                            }
                                            break;
                                        }  // close if (!$input["test"])
                                        break;
                                    case 'goods':
                                        $goodsData = $this->jigsaw_model->getGoods($site_id, $jigsawConfig['item_id']);
                                        if (!$goodsData) {
                                            break;
                                        }

                                        if (isset($goodsData['group'])) {
                                            $goodsData = $this->goods_model->getGoodsFromGroup($validToken['client_id'],
                                                $validToken['site_id'], $goodsData['group'], $input['pb_player_id'],
                                                $jigsawConfig['quantity']);
                                            $goodsData['name'] = $goodsData['group'];
                                            if (!$goodsData) {
                                                break;
                                            }
                                            $jigsawConfig['item_id'] = $goodsData['goods_id'];
                                        }

                                        unset($goodsData['_id']);
                                        unset($goodsData['redeem']);
                                        unset($goodsData['quantity']);
                                        $goodsData['goods_id'] = $goodsData['goods_id'] . '';
                                        $goodsData['image'] = $this->config->item('IMG_PATH') . $goodsData['image'];
                                        $event = array(
                                            'event_type' => 'REWARD_RECEIVED',
                                            'reward_type' => $jigsawConfig['reward_name'],
                                            'reward_data' => $goodsData,
                                            'value' => $jigsawConfig['quantity']
                                        );

                                        if (!$input["test"] && !$anonymousUser) {
                                            $this->giveGoods($jigsawConfig, $input, $validToken, $event, $fbData, $goodsData);
                                        }
                                        if(is_array($event['reward_data']['code'])){
                                            foreach ($event['reward_data']['code'] as $code){
                                                array_push($last_coupon, $code);
                                            }
                                        } else {
                                            array_push($last_coupon, $event['reward_data']['code']);
                                        }
                                        array_push($apiResult['events'], $isGroup ? array_merge($event, array('index' => $exInfo['index'])) : $event);

                                        break;
                                    default:
                                        log_message('error', 'Unknown reward: ' . $jigsawConfig['reward_name']);
                                        break;
                                }  // close switch($jigsawConfig['reward_name'])

                            }
                        }  // close if(isset($exInfo['dynamic']))
                    } elseif ($jigsawCategory == 'FEEDBACK') {
                        if (!$input["test"]) {
                            $this->processFeedback($jigsawName, array_merge($input, array('coupon' => $last_coupon)));
                        }
                    } else {
                        //check for completed objective
                        /*if(isset($exInfo['objective_complete'])) {
                            $objId = $exInfo['objective_complete']['id'];
                            $objName = $exInfo['objective_complete']['name'];

                            if (!$input["test"])
                                $this->player_model->completeObjective(
                                    $input['pb_player_id'],
                                    new MongoId($objId),
                                    $client_id,
                                    $site_id);

                            $event = array(
                                'event_type' => 'OBJECTIVE_COMPLETE',
                                'objective_id' => $objId,
                                'objective_name' => $objName
                            );
                            array_push($apiResult['events'], $isGroup ? array_merge($event, array('index' => $exInfo['index'])) : $event);

                            if (!$input["test"]) {
                                $eventMessage = $this->utility->getEventMessage(
                                    'objective', '', '', '', '', $objName);

                                $this->tracker_model->trackEvent('OBJECTIVE_COMPLETE', $eventMessage, array_merge($input, array(
                                    'objective_id' => $objId,
                                    'objective_name' => $objName,
                                )));

                                //publish to node stream
                                $this->node->publish(array_merge($input, array(
                                    'message' => $eventMessage,
                                    'objective' => array(
                                        'id' => $objId,
                                        'name' => $objName
                                    )
                                )), $site_name, $site_id);
                                //publish to facebook notification
                                if($fbData)
                                    $this->social_model->sendFacebookNotification(
                                        $client_id,
                                        $site_id,
                                        $fbData['facebook_id'],
                                        $eventMessage,
                                        '');
                            }  // close if (!$input["test"])
                        }  // close if(isset($exInfo['objective_complete']))*/
                    }  // close if($jigsaw['category'] == 'REWARD')
                    // success, log jigsaw - ACTION, CONDITION, REWARD, or FEEDBACK
                    if (!$input["test"]) {
                        $this->client_model->log($input, $exInfo);
                    }

                    if ($break) {
                        break;
                    } // break early, do not process next jigsaw
                } else {  // jigsaw return false
                    if ($this->is_reward($jigsawCategory)) { // REWARD, FEEDBACK
                        if (isset($exInfo['break']) && $exInfo['break']) {
                            break;
                        }
                    } else {
                        // fail, log jigsaw - ACTION or CONDITION
                        if (!$input["test"]) {
                            $this->client_model->log($input, $exInfo);
                        }
                        break;
                    }
                }  // close if($jigsaw_model)

                /* [rule usage] set last_jigsaw */
                $last_jigsaw = $jigsawCategory;
            }  // close foreach($jigsawSet as $jigsaw)

            /* [rule usage] increase usage value on client's account */
            if (!$input["test"] && $count > 0) {
                $this->client_model->insertRuleUsage(
                    $client_id,
                    $site_id,
                    $input['rule_id'],
                    $input['pb_player_id'],
                    $count
                );
                $this->client_model->permissionProcess(
                    $this->client_data,
                    $client_id,
                    $site_id,
                    "others",
                    "rule",
                    $count
                );
            }
        }  // close foreach($ruleSet as $rule)
        return $apiResult;
    }

    private function normalize_jigsawConfig($jigsawConfig)
    {
        if (isset($jigsawConfig['action_id']) && !empty($jigsawConfig['action_id'])) {
            $jigsawConfig['action_id'] = new MongoId($jigsawConfig['action_id']);
        }
        if (isset($jigsawConfig['reward_id']) && !empty($jigsawConfig['reward_id'])) {
            $jigsawConfig['reward_id'] = $jigsawConfig['reward_id'] != 'goods' ? new MongoId($jigsawConfig['reward_id']) : $jigsawConfig['reward_id'];
        }
        if (isset($jigsawConfig['item_id']) && !empty($jigsawConfig['item_id'])) {
            $jigsawConfig['item_id'] = new MongoId($jigsawConfig['item_id']);
        }
        return $jigsawConfig;
    }

    private function is_reward($category)
    {
        return in_array($category, array('REWARD', 'FEEDBACK'));
    }

    private function giveGoods($jigsawConfig, $input, $validToken, &$event, $fbData, $goodsData)
    {
        $site_name = $validToken['site_name'];
        $eventMessage = $this->utility->getEventMessage($jigsawConfig['reward_name'], '', '', '', '', '', $goodsData['name']);
        if(isset($goodsData['group']) && !empty($goodsData['group'])){
            $goods_group_rewards = $this->goods_model->getGoodsByGroup($validToken['client_id'], $validToken['site_id'], $goodsData['group'] , 0 , $jigsawConfig['quantity']*10 , 1 );
            if($jigsawConfig['quantity'] > 0 && $goods_group_rewards){
                $rand_goods = array_rand($goods_group_rewards, sizeof($goods_group_rewards) < $jigsawConfig['quantity'] ? sizeof($goods_group_rewards) : (int)$jigsawConfig['quantity']);
                if(!is_array($rand_goods)){
                    $rand_goods = array($rand_goods);
                }
                $log_array = array();
                $id_array = array();
                $code_array = array();
                foreach($rand_goods as $index){
                    $player_goods = $this->goods_model->getPlayerGoodsGroup($validToken['site_id'], $goodsData['group'] , $input['pb_player_id']);
                    if(($goods_group_rewards[$index]['per_user'] > $player_goods) || ($goods_group_rewards[$index]['per_user'] == null)) {
                        try {
                            $return_data = $this->client_model->updateplayerGoods($goods_group_rewards[$index]['goods_id'], 1,
                                $input['pb_player_id'], $input['player_id'], $validToken['client_id'], $validToken['site_id'], false);
                        } catch (Exception $e){}
                        $this->tracker_model->trackGoods(array_merge($validToken, array(
                            'pb_player_id' => $input['pb_player_id'],
                            'goods_id' => new MongoId($goods_group_rewards[$index]['goods_id']),
                            'goods_name' => $goods_group_rewards[$index]['name'],
                            'group' => $goods_group_rewards[$index]['group'],
                            'date_expire' => isset($return_data['date_expire']) ? $return_data['date_expire'] : null,
                            'is_sponsor' => false,
                            'amount' => $goods_group_rewards[$index]['quantity'],
                            'redeem' => null, // cannot pull from goodsData, should pull from "redeem" condition for rule context
                            'action_name' => 'redeem_goods',
                            'action_icon' => 'fa-icon-shopping-cart',
                            'message' => $eventMessage
                        )));
                        $log_id = $this->redeem_model->exerciseCode('goods', $validToken['client_id'], $validToken['site_id'],
                            $input['pb_player_id'], array_key_exists('code', $goods_group_rewards[$index]) ? $goods_group_rewards[$index]['code'] : null);
                        array_push($log_array, $log_id."" );
                        array_push($id_array, $goods_group_rewards[$index]['goods_id']."");
                        array_push($code_array, array_key_exists('code', $goods_group_rewards[$index]) ? $goods_group_rewards[$index]['code'] : null);
                    }
                }
                $event['value'] = sizeof($rand_goods);
                $event['reward_data']['code'] = sizeof($rand_goods) == 1 ? $code_array[0]:$code_array;
                $event['reward_data']['goods_id'] = sizeof($rand_goods) == 1 ? $id_array[0]:$id_array;
                $event['log_id'] = sizeof($rand_goods) == 1 ? $log_array[0]:$log_array;
            } else {
                $event['value'] = 0;
                $event['log_id'] = null;
            }
        } else {
            $player_goods = $this->goods_model->getPlayerGoods($validToken['site_id'], $goodsData['goods_id'], $input['pb_player_id']);
            if(isset($goodsData['per_user']) && (int)$goodsData['per_user'] > 0){
                $quantity = ((int)$jigsawConfig['quantity'] + (int)$player_goods) > (int)$goodsData['per_user'] ? (int)$goodsData['per_user'] - (int)$player_goods : $jigsawConfig['quantity'];
            }
            else{
                $quantity = $jigsawConfig['quantity'];
            }
            try {
                $this->client_model->updateplayerGoods($goodsData['goods_id'], $quantity,
                    $input['pb_player_id'], $input['player_id'], $validToken['client_id'], $validToken['site_id'], false);
            } catch (Exception $e){}
            // log event - goods
            $this->tracker_model->trackGoods(array_merge($validToken, array(
                'pb_player_id' => $input['pb_player_id'],
                'goods_id' => new MongoId($goodsData['goods_id']),
                'goods_name' => $goodsData['name'],
                'is_sponsor' => false,
                'amount' => $jigsawConfig['quantity'],
                'redeem' => null, // cannot pull from goodsData, should pull from "redeem" condition for rule context
                'action_name' => 'redeem_goods',
                'action_icon' => 'fa-icon-shopping-cart',
                'message' => $eventMessage
            )));
            $log_id = $this->redeem_model->exerciseCode('goods', $validToken['client_id'], $validToken['site_id'],
                $input['pb_player_id'], array_key_exists('code', $goodsData) ? $goodsData['code'] : null);
            $event['log_id'] = $log_id."";
            $event['value'] = $quantity;
        }

        // publish - node stream
        $this->node->publish(array_merge($input, array(
            'message' => $eventMessage,
            'goods' => $event['reward_data']
        )), $site_name, $validToken['site_id']);

        // publish - facebook notification
        if ($fbData) {
            $this->social_model->sendFacebookNotification($validToken['client_id'], $validToken['site_id'],
                $fbData['facebook_id'], $eventMessage, '');
        }
    }

    public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        $token = $this->auth_model->getApiInfo($credential);
        //echo '<br>getPlayerInfo (node_stream)<br>';
        //$pb_player_id = $this->player_model->getPlaybasisId(array(
        //	'site_id' => $token['site_id'],
        //	'client_id' => $token['client_id'],
        //	'cl_player_id' => '1'
        //));
        //$result = $this->node->getPlayerInfo($pb_player_id, $token['site_id']);
        //print_r($result);
        //echo '<br>getMostRecentJigsaw (jigsaw)<br>';
        //$rule_id = new MongoId('51f1b3506d6cfb64170e81cd');
        //$jigsaw_id = new MongoId('51f120906d6cfb64170000b4');
        //$result = $this->jigsaw_model->getMostRecentJigsaw(array(
        //	'site_id' => $token['site_id'],
        //	'pb_player_id' => $pb_player_id,
        //	'rule_id' => $rule_id,
        //	'jigsaw_id' => $jigsaw_id
        //), array(
        //	'input',
        //	'date_added'
        //));
        //print_r($result);
        //echo '<br>checkBadge (jigsaw)<br>';
        //$badge_id = new MongoId('51f120906d6cfb641700001f');
        //$result = $this->jigsaw_model->checkBadge($badge_id, $pb_player_id, $token['site_id']);
        //var_dump($result);
        //echo '<br>checkReward (jigsaw)<br>';
        //$reward_id = $this->point_model->findPoint(array_merge($token, array('reward_name'=>'point')));
        //$result = $this->jigsaw_model->checkReward($reward_id, $token['site_id']);
        //var_dump($result);
        echo '</pre>';
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
}

?>
