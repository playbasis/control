<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Rule extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Rule_model');
        $this->load->model('Push_model');
        $this->load->model('Badge_model');
        $this->load->model('Goods_model');
        $this->load->model('Reward_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');
        $this->load->model('Game_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("rule", $lang['folder']);
    }

    public function index()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList();
    }

    private function getList()
    {
        include('action_data_log.php');

        $this->load->model('Badge_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');
        $this->load->model('Level_model');
        $this->load->model('Webhook_model');

        $isAdmin = $this->User_model->isAdmin();
        $client_id = $isAdmin ? null : $this->User_model->getClientId();
        $site_id = $isAdmin ? null : $this->User_model->getSiteId();
        $s_clientId = '' . $client_id;
        $s_siteId = '' . $site_id;
        $this->data["isAdmin"] = $isAdmin;

        $this->data['jsonConfig_siteId'] = $s_siteId;
        $this->data['jsonConfig_clientId'] = $s_clientId;

        $this->data['actionList'] = array();
        $this->data['conditionList'] = array();
        $this->data['levelConditionList'] = array();
        $this->data['rewardList'] = array();
        $this->data['feedbackList'] = array();
        $this->data['groupList'] = array();
        $this->data['emailList'] = array();
        $this->data['smsList'] = array();
        $this->data['pushList'] = array();
        $this->data['webhookList'] = array();

        //if($s_clientId){
        $actionList = $this->Rule_model->getActionJigsawList($site_id, $client_id);
        $conditionList = $this->Rule_model->getConditionJigsawList($site_id, $client_id);
        $rewardList = $this->Rule_model->getRewardJigsawList($site_id, $client_id);
        $emailList = $this->Email_model->listTemplatesBySiteId($site_id);
        $smsList = $this->Sms_model->listTemplatesBySiteId($site_id);
        $pushList = $this->Push_model->listTemplatesBySiteId($site_id);
        $webhookList = $this->Webhook_model->listTemplatesBySiteId($site_id);
        $feedbackList = $this->Rule_model->getFeedbackJigsawList($site_id, $client_id, $emailList, $smsList, $pushList, $webhookList);
        $conditionGroupList = $this->Rule_model->getConditionGroupJigsawList($site_id, $client_id);
        $groupList = $this->Rule_model->getGroupJigsawList($site_id, $client_id);
        $levelConditionList = $this->Level_model->getLevelConditions();
        $gameList = $this->Game_model->getGameList($client_id, $site_id);
        if($gameList){
            foreach ($gameList as &$game) {
                $game['name'] = $game['game_name'];
            }
        }

        if (is_array($rewardList)) {
            $reward_list = array("badge","customPointReward","goods");
            foreach ($rewardList as &$reward) {
                if (is_array($reward['dataSet'])) {
                    foreach ($reward['dataSet'] as &$dataset) {
                        if (strtolower($dataset['param_name']) == "quantity") {
                            $dataset['field_type'] = 'text';
                        }
                    }
                    if(!in_array($reward["name"],$reward_list)) {
                        $reward['dataSet'][] = array(
                            'field_type' => "text",
                            'label' => "custom Log (optional)",
                            'param_name' => "custom_log",
                            'placeholder' => "",
                            'sortOrder' => "0",
                            'value' => "",

                        );
                    }
                }
            }
        }

        $this->data['actionList'] = $actionList;
        $this->data['conditionList'] = $conditionList;
        $this->data['levelConditionList'] = $levelConditionList;
        $this->data['rewardList'] = $rewardList;
        $this->data['feedbackList'] = array_merge($rewardList, $feedbackList);
        $this->data['groupList'] = $groupList;
        $this->data['conditionGroupList'] = $conditionGroupList;
        $this->data['emailList'] = $emailList;
        $this->data['smsList'] = $smsList;
        $this->data['pushList'] = $pushList;
        $this->data['webhookList'] = $webhookList;
        $this->data['gameList'] = $gameList;

        //}

        $this->data['jsonIcons'] = array();
        $this->data['requestParams'] = '&siteId=' . $s_siteId . '&clientId=' . $s_clientId;
        $this->data['main'] = 'rule';

        // Template
        $templates = $this->Rule_model->getRulesByCombinationId(null, null);
        $this->data["ruleTemplate"] = array();
        if (!isset($templates["error"])) {
            foreach ($templates as $template) {
                $name = $template["name"];
                $this->data["ruleTemplate"][$name] = $template["rule_id"];
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function jsonGetRules()
    {
        if ($this->User_model->isAdmin()) {
            $siteId = null;
            $clientId = null;
        } else {
            $siteId = $this->User_model->getSiteId();
            $clientId = $this->User_model->getClientId();
        }

        $actionList = $this->Rule_model->getActionJigsawList($siteId, $clientId);
        $conditionList = $this->Rule_model->getConditionJigsawList($siteId, $clientId);
        $rewardList = $this->Rule_model->getRewardJigsawList($siteId, $clientId);
        $result = $this->Rule_model->getRulesByCombinationId($siteId, $clientId, array(
            'actionList' => $this->makeListOfId($actionList, 'specific_id'),
            'actionNameDict' => $this->makeDict($actionList, 'specific_id', 'name'),
            'conditionList' => $this->makeListOfId($conditionList, 'id'),
            'rewardList' => $this->makeListOfId($rewardList, 'specific_id'),
        ));
        $this->output->set_output(json_encode($result));
    }

    public function setRuleState()
    {
        if ($this->User_model->isAdmin()) {
            $siteId = null;
            $clientId = null;
        } else {
            $siteId = $this->User_model->getSiteId();
            $clientId = $this->User_model->getClientId();
        }

        if (!$this->input->post('ruleId') &&
            !$this->input->post('state')
        ) {
            $this->jsonErrorResponse();
            return;
        }

        //after clean channge disable to be 0 because 0-string is lost in network
        if ($this->input->post('state') == 'disable') {
            $state = '0';
        } else {
            $state = '1';
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        // $this->jsonResponse($params);
        $this->output->set_output(json_encode(
            $this->Rule_model->changeRuleState(
                $this->input->post('ruleId'),
                $state,
                $siteId,
                $clientId)));
    }

    public function jsonSaveRule()
    {
        if (!$this->input->post('json')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        $input = json_decode(html_entity_decode($this->input->post('json')), true);
        $this->output->set_output(json_encode($this->Rule_model->saveRule($input)));
    }

    private function setRewards($client_id, $site_id, &$jigsaw){
        if($jigsaw['name']=="exp" || $jigsaw['name']=="point" || $jigsaw['name']=="specialReward") {
            // do nothing
        }elseif($jigsaw['name']=="badge") {
            if($jigsaw['dataSet'][1]['value']){
                $badge_name = $this->Badge_model->getNameOfBadgeID($client_id, $site_id,$jigsaw['dataSet'][1]['value']);
                //$jigsaw['dataSet'][1]['value'] this value must be changed when import (badge_name -> badge_id)
                //$jigsaw['config']['item_id'] this value must be changed when import (badge_name -> badge_id)
                $jigsaw['config']['badge_name'] = $badge_name;
            }else{
                $jigsaw['config']['badge_name'] = null;
            }
        }elseif($jigsaw['name']=="goods" ) {
            if($jigsaw['dataSet'][1]['value']) {
                $goods_private = $this->Goods_model->getGoodsOfClientPrivate($jigsaw['dataSet'][1]['value']);
                //$jigsaw['dataSet'][1]['value'] this value must be changed when import (goods_name,good_group -> goods_id)
                //$jigsaw['config']['item_id'] this value must be changed when import (goods_name,good_group -> goods_id)
                $jigsaw['config']['good_name'] = $goods_private['name'];
                $jigsaw['config']['good_group'] = isset($goods_private['group']) ? $goods_private['group'] : null;
            }else{
                $jigsaw['config']['good_name'] = null;
                $jigsaw['config']['good_group'] =  null;
            }
        }else{//customPoint
            // validate customPoint when import
        }
    }

    public function jsonExportRule()
    {
        if (!$this->input->post('array_rules')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        $client_id = $this->input->post("client_id");
        $site_id = $this->input->post("site_id");

        $array_rules = array();
        foreach($this->input->post('array_rules') as $rule){
            $rule_info = $this->Rule_model->getRuleForExport($client_id, $site_id,  $rule);
            $rule_info['client_id'] = null;
            $rule_info['site_id'] = null;
            $rule_info['action_id'] = $rule_info['action_id']."";
            foreach($rule_info['jigsaw_set'] as &$jigsaw){
                if( $jigsaw['category']=="REWARD"){
                    $this->setRewards($client_id, $site_id, $jigsaw);
                }elseif( $jigsaw['category']=="GROUP"){// reward group
                    foreach($jigsaw['dataSet'][0]['value'] as &$dataSet){
                        $this->setRewards($client_id, $site_id, $dataSet);
                    }
                }elseif( $jigsaw['category']=="CONDITION"){
                    if($jigsaw['name']=="badge"){
                        $this->setRewards($client_id, $site_id, $jigsaw);
                    }elseif($jigsaw['name']=="redeem"){
                        foreach($jigsaw['dataSet'][0]['value'] as &$dataSet){
                            $this->setRewards($client_id, $site_id, $dataSet);
                        }
                    }

                }elseif( $jigsaw['category']=="CONDITION_GROUP"){
                    foreach($jigsaw['dataSet'][0]['value'] as &$dataSet){
                        if($dataSet['name']=="badge"){
                            $this->setRewards($client_id, $site_id, $dataSet);
                        }elseif($dataSet['name']=="redeem"){
                            // TODO : there is a bug when adding redeem reward in condition group, need to investigate and fix first
                            /*foreach($dataSet['dataSet'][0]['value'] as $index => &$dataSet2){
                                $this->setRewards($client_id, $site_id, $dataSet);
                            }*/
                        }
                    }
                }elseif( $jigsaw['category']=="FEEDBACK"){
                    if($jigsaw['name']=="email"){
                        $info = $this->Email_model->getTemplate($jigsaw['dataSet'][1]['value']);
                        //$jigsaw['dataSet'][1]['value'] this value must be changed when import (template_name -> template_id)
                        //$jigsaw['config']['template_id'] this value must be changed when import (template_name -> template_id)
                        $jigsaw['config']['template_name'] = $info['name'];
                    }elseif($jigsaw['name']=="sms"){
                        $info = $this->Sms_model->getTemplate($jigsaw['dataSet'][1]['value']);
                        //$jigsaw['dataSet'][1]['value'] this value must be changed when import (template_name -> template_id)
                        //$jigsaw['config']['template_id'] this value must be changed when import (template_name -> template_id)
                        $jigsaw['config']['template_name'] = $info['name'];
                    }elseif($jigsaw['name']=="push"){
                        $info = $this->Push_model->getTemplate($jigsaw['dataSet'][1]['value']);
                        //$jigsaw['dataSet'][1]['value'] this value must be changed when import (template_name -> template_id)
                        //$jigsaw['config']['template_id'] this value must be changed when import (template_name -> template_id)
                        $jigsaw['config']['template_name'] = $info['name'];
                    }
                }
            }

            $array_rules[] = $rule_info;
        }

        $this->output->set_output(json_encode($array_rules));
    }

    private function validateRewards($client_id, $site_id, &$jigsaw){
        $result = null;
        if($jigsaw['name']=="exp" || $jigsaw['name']=="point" || $jigsaw['name']=="specialReward") {
            // do nothing
        }elseif($jigsaw['name']=="badge") {
            if($jigsaw['config']['badge_name']){
                $badge_id = $this->Badge_model->getBadgeIDByName($client_id, $site_id,$jigsaw['config']['badge_name']);
                if($badge_id){
                    unset($jigsaw['config']['badge_name']);
                    $jigsaw['dataSet'][1]['value'] = $badge_id;
                    $jigsaw['config']['item_id'] = $badge_id;
                }else{
                    $result = array('jigsaw'=>'badge','name'=>$jigsaw['config']['badge_name']);
                }
            }
        }elseif($jigsaw['name']=="goods" ) {
            if($jigsaw['config']['good_name'] || $jigsaw['config']['good_group']) {
                $goods_id = $this->Goods_model->getGoodsIDByName($client_id, $site_id, $jigsaw['config']['good_name'],$jigsaw['config']['good_group']);
                if($goods_id){
                    $jigsaw['dataSet'][1]['value'] = $goods_id;
                    $jigsaw['config']['item_id'] = $goods_id;
                    unset($jigsaw['config']['good_name']);
                    unset($jigsaw['config']['good_group']);
                }else{
                    $result = array('jigsaw'=>'goods','name'=>$jigsaw['config']['good_group'] ? $jigsaw['config']['good_group'] : $jigsaw['config']['good_name']);
                }
            }
        }else{//customPoint
            // validate customPoint whether exist
            $customPoint_id = $this->Reward_model->getClientRewardIDByName($client_id, $site_id, $jigsaw['name']);
            if($customPoint_id){
                $jigsaw['specific_id'] = $customPoint_id;
                $jigsaw['config']['reward_id'] = $customPoint_id;

            }else{
                $result = array('jigsaw'=>'customPoint','name'=>$jigsaw['name']);
            }
        }
        return $result;
    }

    private function push_validation_error(&$array,$key,$value){
        if(isset($array[$key])){
            $array[$key] = array_keys(array_flip(array_merge($array[$key],array($value))));
        }else{
            $array[$key] = array($value);
        }

    }

    public function jsonImportRule()
    {
        if (!$this->input->post('array_rules')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        $array_rules = json_decode($this->input->post('array_rules'),true);
        $client_id = $this->input->post("client_id");
        $site_id = $this->input->post("site_id");

        $validation_result = array();
        foreach($array_rules as &$rule_info){
            $rule_info['rule_id'] = 'undefined';
            $rule_info['client_id'] = $client_id;
            $rule_info['site_id'] = $site_id;
            $rule_info['date_added'] = "";
            $rule_info['date_modified'] = "";
            //$rule_info['active_status'] = $rule_info['active_status'] ? 1 : 0;
            foreach($rule_info['jigsaw_set'] as &$jigsaw){
                if( $jigsaw['category']=="REWARD"){
                    $vResult = $this->validateRewards($client_id, $site_id, $jigsaw);
                    if($vResult){ // if $vResult is not NULL then mean that the validation got error
                        $this->push_validation_error($validation_result, $vResult['jigsaw'], $vResult['name']);
                    }

                }elseif( $jigsaw['category']=="GROUP"){// reward group
                    foreach($jigsaw['dataSet'][0]['value'] as $index => &$dataSet){
                        $vResult = $this->validateRewards($client_id, $site_id, $dataSet);
                        if($vResult){ // if $vResult is not NULL then mean that the validation got error
                            $this->push_validation_error($validation_result, $vResult['jigsaw'], $vResult['name']);
                        }else{
                            $jigsaw['config']['group_container'][$index]['item_id']=$dataSet['config']['item_id'];
                        }
                    }
                }elseif( $jigsaw['category']=="CONDITION"){
                    if($jigsaw['name']=="badge"){
                        if($jigsaw['config']['badge_name']) {
                            $badge_id = $this->Badge_model->getBadgeIDByName($client_id, $site_id,$jigsaw['config']['badge_name']);
                            if($badge_id){
                                unset($jigsaw['config']['badge_name']);
                                $jigsaw['dataSet'][1]['value'] = $badge_id;
                                $jigsaw['config']['badge_id'] = $badge_id;
                            }else{
                                $this->push_validation_error($validation_result, "badge", $jigsaw['config']['badge_name']);
                            }
                        }
                    }elseif($jigsaw['name']=="redeem"){
                        foreach($jigsaw['dataSet'][0]['value'] as $index => &$dataSet){
                            $vResult = $this->validateRewards($client_id, $site_id, $dataSet);
                            if($vResult){ // if $vResult is not NULL, mean that the validation got error
                                $this->push_validation_error($validation_result, $vResult['jigsaw'], $vResult['name']);
                            }else{
                                $jigsaw['config']['group_container'][$index]['item_id']=$dataSet['config']['item_id'];
                            }
                        }
                    }
                }elseif( $jigsaw['category']=="CONDITION_GROUP"){
                    foreach($jigsaw['dataSet'][0]['value'] as $index => &$dataSet){
                        if($dataSet['name']=="badge"){
                            if($dataSet['config']['badge_name']) {
                                $badge_id = $this->Badge_model->getBadgeIDByName($client_id, $site_id,$dataSet['config']['badge_name']);
                                if($badge_id){
                                    unset($dataSet['config']['badge_name']);
                                    $dataSet['dataSet'][1]['value'] = $badge_id;
                                    $dataSet['config']['badge_id'] = $badge_id;
                                    $jigsaw['config']['condition_group_container'][$index]['badge_id']=$badge_id;
                                }else{
                                    $this->push_validation_error($validation_result,"badge",$dataSet['config']['badge_name']);
                                }
                            }
                        }elseif($dataSet['name']=="redeem"){
                            // TODO : there is a bug when adding redeem reward in condition group, need to investigate and fix first
                            /*foreach($dataSet['dataSet'][0]['value'] as $index => &$dataSet2){
                                $vResult = $this->validateRewards($client_id, $site_id, $dataSet2);
                                if($vResult){ // if $vResult is not NULL, mean that the validation got error
                                    //$validation_result[] = array('rule_name'=>$rule_info['name'], 'message'=>$vResult);
                                    $this->push_validation_error($validation_result, $vResult['jigsaw'], $vResult['name']);
                                }else{
                                    $dataSet['config']['group_container'][$index]['item_id']=$dataSet2['config']['item_id'];
                                }
                            }*/
                        }
                    }
                }elseif( $jigsaw['category']=="FEEDBACK"){

                    if($jigsaw['name']=="email"){
                        $email_template_id = $this->Email_model->getTemplateIDByName($site_id, $jigsaw['config']['template_name']);
                        if($email_template_id){
                            unset($jigsaw['config']['template_name']);
                            $jigsaw['dataSet'][1]['value'] = $email_template_id;
                            $jigsaw['config']['template_id'] = $email_template_id;
                        }else{
                            $this->push_validation_error($validation_result,"email_template",$jigsaw['config']['template_name']);
                        }

                    }elseif($jigsaw['name']=="sms"){
                        $sms_template_id = $this->Sms_model->getTemplateIDByName($site_id, $jigsaw['config']['template_name']);
                        if($sms_template_id){
                            unset($jigsaw['config']['template_name']);
                            $jigsaw['dataSet'][1]['value'] = $sms_template_id;
                            $jigsaw['config']['template_id'] = $sms_template_id;
                        }else{
                            $this->push_validation_error($validation_result,"sms_template",$jigsaw['config']['template_name']);
                        }

                    }elseif($jigsaw['name']=="push"){
                        $push_template_id = $this->Push_model->getTemplateIDByName($site_id, $jigsaw['config']['template_name']);
                        if($push_template_id){
                            unset($jigsaw['config']['template_name']);
                            $jigsaw['dataSet'][1]['value'] = $push_template_id;
                            $jigsaw['config']['template_id'] = $push_template_id;
                        }else{
                            $this->push_validation_error($validation_result,"push_template",$jigsaw['config']['template_name']);
                        }
                    }
                }
            }
        }

        if(!$validation_result){ // passed data validation
            foreach($array_rules as $rule) {
                $import_result = $this->Rule_model->saveRule($rule);
            }
            $this->output->set_output(json_encode(array('status'=>'success')));
        }else{ // failed data validation
            $this->output->set_output(json_encode(array('status'=>'fail','results'=>$validation_result)));
        }



    }

    public function jsonCloneRule()
    {
        $id = $this->input->post("id");
        if (!$id) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        if ($this->User_model->isAdmin()) {
            $siteId = null;
            $clientId = null;
        } else {
            $siteId = $this->User_model->getSiteId();
            $clientId = $this->User_model->getClientId();
        }

        $this->output->set_output(json_encode(
            $this->Rule_model->cloneRule(
                $id, $clientId, $siteId)));
    }

    /*
     * Play Action according to the rule
     * does not edit anything in database
     */
    public function jsonPlayRule()
    {
        $id = $this->input->post("id");
        $client_id = $this->input->post("client_id");
        $site_id = $this->input->post("site_id");
        $rule = $this->Rule_model->getById($id);
        $result = $this->User_model->get_api_key_secret($client_id, $site_id);

        // extract the rule parameters
        $action = null;
        $params = array();
        foreach ($rule["jigsaw_set"] as $jigsaw) {
            if ($jigsaw["category"] == "ACTION") {
                $action = $jigsaw["name"];
            } else {
                if ($jigsaw["category"] == "CONDITION") {
                    if ($jigsaw["name"] == "customParameter") {
                        $params[$jigsaw["config"]["param_name"]] = $jigsaw["config"]["param_value"];
                    }
                }
            }
        }

        $data = array_merge(array(
            "api_key" => $result["api_key"],
            "rule_id" => strval($rule["_id"]),
            "action" => $action,
            "client_id" => $client_id,
            "site_id" => $site_id,
            "test" => true
        ), $params);

        $this->_api = $this->playbasisapi;
        $result = $this->_api->testRule($data);
        $result = json_encode($result);
        $this->output->set_output($result);
    }

    public function deleteRule()
    {
        if ($this->User_model->isAdmin()) {
            $siteId = null;
            $clientId = null;
        } else {
            $siteId = $this->User_model->getSiteId();
            $clientId = $this->User_model->getClientId();
        }
        /*start  : Wrap all of this to be reqireParam('post','rulesID')*/
        if (!$this->input->post('ruleId')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission'));
            return;
        }

        $this->output->set_output(json_encode(
            $this->Rule_model->deleteRule(
                $this->input->post('ruleId'),
                $siteId,
                $clientId)));
    }

    public function loadBadges()
    {
        $this->load->model('Badge_model');

        $client_id = $this->User_model->isAdmin() ? null : $this->User_model->getClientId();
        $site_id = $this->User_model->isAdmin() ? null : $this->User_model->getSiteId();
        //if ($site_id) {
        $badge_data = array(
            'site_id' => $site_id,
            'sort' => 'sort_order'
        );

        $itemCategory = $this->Badge_model->retrieveItemCategoryName($client_id, $site_id);
        $categoryName = array();
        foreach ($itemCategory as &$c) {
            $categoryName[$c['_id'] . ""] = $c['name'];
        }

        $badges = $site_id ? $this->Badge_model->getBadgeBySiteId($badge_data) : $this->Badge_model->listBadgesTemplate();
        $badgeToCategory[''] = array();

        foreach ($badges as &$b) {
            $b['_id'] = $b['_id'] . "";
            $b['badge_id'] = isset($b['badge_id']) ? $b['badge_id'] . "" : $b['_id'];
            $b['client_id'] = isset($b['client_id']) ? $b['client_id'] . "" : null;
            $b['site_id'] = isset($b['site_id']) ? $b['site_id'] . "" : null;
            if(isset($b['category']) && array_key_exists($b['category']."",$categoryName)){
                $badgeToCategory[$categoryName[$b['category'].""]][] = $b;
            }else{
                $badgeToCategory[''][] = $b;
            }

        }

        $this->output->set_output(json_encode($badgeToCategory));
        //}
    }

    public function loadGoods()
    {
        $this->load->model('Badge_model');
        $this->load->model('Goods_model');

        $results = $this->Goods_model->getGroupsAggregate($this->User_model->getSiteId());
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }

        $goods_list = $this->Goods_model->getGoodsBySiteId(array(
            'site_id' => $this->User_model->getSiteId(),
            'sort' => 'sort_order',
            '$nin' => $ids
        ));
        foreach ($goods_list as &$g) {
            $g['_id'] = $g['_id'] . "";
            $g['goods_id'] = $g['goods_id'] . "";
            $g['client_id'] = $g['client_id'] . "";
            $g['site_id'] = $g['site_id'] . "";
        }
        $json['goods'] = $goods_list;

        $this->output->set_output(json_encode($json));
    }

    public function loadGoodsById()
    {
        $this->load->model('Badge_model');
        $this->load->model('Goods_model');

        $siteId = $this->User_model->getSiteId();
        $clientId = $this->User_model->getClientId();


        if (!$this->input->get('goodsID')) {
            $this->jsonErrorResponse();
            return;
        }

        $goods_info = $this->Goods_model->getGoodsOfClientPrivate($this->input->get('goodsID'));
        $goods_info['_id'] = $goods_info['_id'] . "";
        $goods_info['goods_id'] = $goods_info['goods_id'] . "";
        $goods_info['client_id'] = $goods_info['client_id'] . "";
        $goods_info['site_id'] = $goods_info['site_id'] . "";

        $json['goods_info'] = $goods_info;
        $this->output->set_output(json_encode($json));
    }

    public function jsonGetRuleById()
    {
        if ($this->User_model->isAdmin()) {
            $siteId = null;
            $clientId = null;
        } else {
            $siteId = $this->User_model->getSiteId();
            $clientId = $this->User_model->getClientId();
        }

        if (!$this->input->get('ruleId')) {
            $this->jsonErrorResponse();
            return;
        }

        $rule = $this->Rule_model->getRuleById(
            $siteId,
            $clientId,
            $this->input->get('ruleId'));

        if ($rule) {
            $this->output->set_output(
                $this->input->get('callback') . "(" . json_encode($rule) . ")");
        } else {
            $this->jsonErrorResponse();
        }
    }

    function jsonErrorResponse($msg = 'Error, invalid request format or missing parameter')
    {
        echo json_encode(
            array(
                'error' => 1,
                'success' => false,
                'msg' => $msg
            )
        );
    }

    private function makeListOfId($arr, $field)
    {
        if (!$arr || !is_array($arr)) {
            return null;
        }
        $ret = array();
        foreach ($arr as $each) {
            $ret[] = $each[$field];
        }
        return $ret;
    }

    private function makeDict($arr, $keyField, $valField)
    {
        if (!$arr || !is_array($arr)) {
            return array();
        }
        $ret = array();
        foreach ($arr as $each) {
            $ret[$each[$keyField]] = $each[$valField];
        }
        return $ret;
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'rule')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'rule') && $this->Feature_model->getFeatureExistByClientId($client_id, 'rule')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function curl($url, $data)
    {
        $data = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }
}
