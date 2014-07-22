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
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList();
    }

    private function getList() {
        include('action_data_log.php');

        $this->load->model('Badge_model');

        $s_siteId = $this->User_model->getSiteId();
        $s_clientId = $this->User_model->getClientId();
        $this->data["isAdmin"] = false;

        $adminGroup = $this->User_model->getAdminGroupID();
        if ($this->User_model->isAdmin()) {
            error_log("I am admin");
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

        $this->data['actionList'] = json_encode(array());
        $this->data['conditionList'] = json_encode(array());
        $this->data['rewardList'] = json_encode(array());
        $this->data['ruleList'] = json_encode(array());

        if($s_clientId){
            $this->data['actionList'] = json_encode(
                $this->Rule_model->getActionGigsawList($site_id, $client_id)
            );
            $this->data['conditionList'] = json_encode(
                $this->Rule_model->getConditionGigsawList($site_id, $client_id)
            );
            $this->data['rewardList'] = json_encode(
                $this->Rule_model->getRewardGigsawList($site_id, $client_id)
            );
            $this->data['ruleList'] = json_encode(
                $this->Rule_model->getRulesByCombinationId($site_id, $client_id)
            );
        }

        $this->data['jsonIcons'] = json_encode($icons);
        $this->data['requestParams'] = '&siteId='.$s_siteId.'&clientId='.$s_clientId;
        $this->data['main'] = 'rule';

        // Template
        $templates = $this->Rule_model->getRulesByCombinationId(
            $adminGroup,
            $adminGroup
        );
        $this->data["ruleTemplate"] = array();
        foreach ($templates as $template) {
            $name = $template["name"];
            $this->data["ruleTemplate"][$name] = $template["rule_id"];
        }

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('rule');
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

        $result = $this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId);
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

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'rule')) {
            return true;
        } else {
            return false;
        }
    }
}
