<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Rule extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Rule_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("rule", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList();
    }

    private function getList() {
        include('action_data_log.php');

        $this->load->model('Badge_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');

        $s_siteId = $this->User_model->getSiteId();
        $s_clientId = $this->User_model->getClientId();
        $this->data["isAdmin"] = false;

        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            $s_siteId = $adminGroup;
            $s_clientId = $adminGroup;
            $this->data["isAdmin"] = true;
            // for query mongodb purpose
            $client_id = "";
            $site_id = "";
        } else {
            // for query mongodb purpose
            $client_id = $s_clientId;
            $site_id = $s_siteId;
        }

        $this->data['jsonConfig_siteId'] = $s_siteId;
        $this->data['jsonConfig_clientId'] = $s_clientId;

        $this->data['actionList'] = array();
        $this->data['conditionList'] = array();
        $this->data['rewardList'] = array();
        $this->data['feedbackList'] = array();
        $this->data['emailList'] = array();
        $this->data['smsList'] = array();

        if($s_clientId){
            $actionList = $this->Rule_model->getActionJigsawList($site_id, $client_id);
            $conditionList = $this->Rule_model->getConditionJigsawList($site_id, $client_id);
            $rewardList = $this->Rule_model->getRewardJigsawList($site_id, $client_id);
            $emailList = $this->Email_model->listTemplatesBySiteId($site_id);
            $smsList = $this->Sms_model->listTemplatesBySiteId($site_id);
            $feedbackList = $this->Rule_model->getFeedbackJigsawList($site_id, $client_id, $emailList, $smsList);

            $this->data['actionList'] = $actionList;
            $this->data['conditionList'] = $conditionList;
            $this->data['rewardList'] = $rewardList;
            $this->data['feedbackList'] = array_merge($rewardList, $feedbackList);
            $this->data['emailList'] = $emailList;
            $this->data['smsList'] = $smsList;
        }

        $this->data['jsonIcons'] = array();
        $this->data['requestParams'] = '&siteId='.$s_siteId.'&clientId='.$s_clientId;
        $this->data['main'] = 'rule';

        // Template
        $templates = $this->Rule_model->getRulesByCombinationId(
            $adminGroup,
            $adminGroup
        );
        $this->data["ruleTemplate"] = array();

        if(!isset($templates["error"])){
            foreach ($templates as $template) {
                $name = $template["name"];
                $this->data["ruleTemplate"][$name] = $template["rule_id"];
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function jsonGetRules(){
        $adminGroup = $this->User_model->getAdminGroupID();
        $userGroup = $this->User_model->getUserGroupId();
        if ($this->User_model->isAdmin()) {
            $s_siteId = $adminGroup;
            $s_clientId = $adminGroup;
        } else {
            $s_siteId = $this->User_model->getSiteId();
            $s_clientId = $this->User_model->getClientId();
        }

        $actionList = $this->Rule_model->getActionJigsawList($s_siteId, $s_clientId);
        $conditionList = $this->Rule_model->getConditionJigsawList($s_siteId, $s_clientId);
        $rewardList = $this->Rule_model->getRewardJigsawList($s_siteId, $s_clientId);

        $result = $this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId, array(
            'actionList' => $this->makeListOfId($actionList, 'specific_id'),
            'actionNameDict' => $this->makeDict($actionList, 'specific_id', 'name'),
            'conditionList' => $this->makeListOfId($conditionList, 'id'),
            'rewardList' => $this->makeListOfId($rewardList, 'specific_id'),
        ));
        $this->output->set_output(json_encode($result));
    }

    public function setRuleState() {
        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            $s_siteId = $adminGroup;
            $s_clientId = $adminGroup;
        } else {
            $s_siteId = $this->User_model->getSiteId();
            $s_clientId = $this->User_model->getClientId();
        }

        if(!$this->input->post('ruleId') &&
           !$this->input->post('siteId') &&
           !$this->input->post('clientId') &&
           !$this->input->post('state')){
            $this->jsonErrorResponse();
            return ;
        }

        //after clean channge disable to be 0 because 0-string is lost in network
        if($this->input->post('state')=='disable')
            $state='0';
        else
            $state='1';

        // $this->jsonResponse($params);
        $this->output->set_output(json_encode(
            $this->Rule_model->changeRuleState(
                $this->input->post('ruleId'),
                $state,
                $s_siteId,
                $s_clientId)));
    }

    public function jsonSaveRule(){
        if(!$this->input->post('json')){
            $this->jsonErrorResponse();
            return ;
        }
        $input = json_decode(html_entity_decode($this->input->post('json')),true);
        $this->output->set_output(json_encode($this->Rule_model->saveRule($input)));
    }

    public function jsonCloneRule(){
        $id = $this->input->post("id");
        $client_id = $this->input->post("client_id");
        $site_id = $this->input->post("site_id");
        if(!$id){
            $this->jsonErrorResponse();
            return ;
        }
        $this->output->set_output(json_encode(
            $this->Rule_model->cloneRule(
                $id, $client_id, $site_id)));
    }

    /*
     * Play Action according to the rule
     * does not edit anything in database
     */
    public function jsonPlayRule() {
        $id = $this->input->post("id");
        $client_id = $this->input->post("client_id");
        $site_id = $this->input->post("site_id");
        $rule = $this->Rule_model->getById($id);

        // process each rule
        $result = array();
        foreach ($rule["jigsaw_set"] as $jigsaw) {
            if ($jigsaw["category"] == "ACTION") {
                foreach ($jigsaw["dataSet"] as $dataSet) {
                    if ($dataSet["param_name"] == "url")
                        $result = $this->curl(
                            $this->config->item("server") . "Engine/rule",
                            array("rule_id" => strval($rule["_id"]),
                            "action" => $jigsaw["name"],
                            "url" => $dataSet["value"],
                            "client_id" => $client_id,
                            "site_id" => $site_id,
                            "test" => true));
                }
            }
        }

        $this->output->set_output($result);
    }

    public function deleteRule(){
        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            $s_siteId = $adminGroup;
            $s_clientId = $adminGroup;
        } else {
            $s_siteId = $this->User_model->getSiteId();
            $s_clientId = $this->User_model->getClientId();
        }
        /*start  : Wrap all of this to be reqireParam('post','rulesID')*/
        if(!$this->input->post('ruleId') &&
           !$this->input->post('siteId') &&
           !$this->input->post('clientId')){
            $this->jsonErrorResponse();
            return ;
        }

        $this->output->set_output(json_encode(
            $this->Rule_model->deleteRule(
                $this->input->post('ruleId'),
                $s_siteId,
                $s_clientId)));
    }

    public function loadBadges() {
        $this->load->model('Badge_model');

        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            $site_id = $adminGroup;
        } else {
            $site_id  = $this->User_model->getSiteId();
        }

        if ($site_id) {
            $badge_data = array (
                'site_id'=> $site_id,
                'sort'=>'sort_order'
            );

            $badges = $this->Badge_model->getBadgeBySiteId($badge_data);

            foreach($badges as &$b){
                $b['_id'] = $b['_id']."";
                $b['badge_id'] = $b['badge_id']."";
                $b['client_id'] = $b['client_id']."";
                $b['site_id'] = $b['site_id']."";
            }
            $json['badges'] = $badges;

            $this->output->set_output(json_encode($json));
        }
    }

    public function jsonGetRuleById(){
        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            $s_siteId = $adminGroup;
            $s_clientId = $adminGroup;
        } else {
            $s_siteId = $this->User_model->getSiteId();
            $s_clientId = $this->User_model->getClientId();
        }

        if(!$this->input->get('ruleId')){
            $this->jsonErrorResponse();
            return ;
        }

        $json = $this->Rule_model->getRuleById(
            $s_siteId,
            $s_clientId,
            $this->input->get('ruleId'));

        if($json){
            $this->output->set_output(
                $this->input->get('callback')."(".json_encode($json[0]).")");
        }else{
            $this->jsonErrorResponse();
        }
    }

    function jsonErrorResponse(){
        echo json_encode(
            array(
                'error'=>1,
                'success'=>false,
                'msg'=>'Error , invalid request format or missing parameter'
            )
        );
    }

    private function makeListOfId($arr, $field) {
        if (!$arr || !is_array($arr)) return null;
        $ret = array();
        foreach ($arr as $each) {
            $ret[] = $each[$field];
        }
        return $ret;
    }

    private function makeDict($arr, $keyField, $valField) {
        if (!$arr || !is_array($arr)) return null;
        $ret = array();
        foreach ($arr as $each) {
            $ret[$each[$keyField]] = $each[$valField];
        }
        return $ret;
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'rule') &&  $this->Feature_model->getFeatureExitsByClientId($client_id, 'rule')) {
            return true;
        } else {
            return false;
        }
    }

    private function curl($url, $data) {
        $data = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }
}
