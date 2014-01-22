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

        $this->data['jsonConfig_siteId'] = $s_siteId;
        $this->data['jsonConfig_clientId'] = $s_clientId;

        $this->data['actionList'] = json_encode(array());
        $this->data['conditionList'] = json_encode(array());
        $this->data['rewardList'] = json_encode(array());
        $this->data['ruleList'] = json_encode(array());

        if($s_clientId){
            $this->data['actionList'] = json_encode($this->Rule_model->getActionGigsawList($s_siteId,$s_clientId));
            $this->data['conditionList'] = json_encode($this->Rule_model->getConditionGigsawList($s_siteId,$s_clientId));
            $this->data['rewardList'] = json_encode($this->Rule_model->getRewardGigsawList($s_siteId,$s_clientId));
            $this->data['ruleList'] = json_encode($this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId));
        }

        $this->data['jsonIcons'] = json_encode($icons);


        $this->data['requestParams'] = '&siteId='.$s_siteId.'&clientId='.$s_clientId;

        $this->data['main'] = 'rule';

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('rule');
    }

    public function jsonGetRules(){

        $s_siteId = $this->User_model->getSiteId();
        $s_clientId = $this->User_model->getClientId();

        $result = $this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId);
        $this->output->set_output(json_encode($result));
    }

    public function setRuleState(){

        if(!$this->input->post('ruleId') && !$this->input->post('siteId') && !$this->input->post('clientId') && !$this->input->post('state')){
            $this->jsonErrorResponse();
            return ;
        }

        //after clean channge disable to be 0 because 0-string is lost in network
        if($this->input->post('state')=='disable')
            $state='0';
        else
            $state='1';

        // $this->jsonResponse($params);
        $this->output->set_output(json_encode($this->Rule_model->changeRuleState($this->input->post('ruleId'),$state,$this->User_model->getSiteId(),$this->User_model->getClientId())));
    }

    public function jsonSaveRule(){

        if(!$this->input->post('json')){
            $this->jsonErrorResponse();
            return ;
        }

        $input = json_decode(html_entity_decode($this->input->post('json')),true);

        $this->output->set_output(json_encode($this->Rule_model->saveRule($input)));
    }


    public function deleteRule(){

        /*start  : Wrap all of this to be reqireParam('post','rulesID')*/
        if(!$this->input->post('ruleId') && !$this->input->post('siteId') && !$this->input->post('clientId')){
            $this->jsonErrorResponse();
            return ;
        }

        $this->output->set_output(json_encode($this->Rule_model->deleteRule($this->input->post('ruleId'),$this->User_model->getSiteId(),$this->User_model->getClientId())));
    }

    public function loadBadges() {
        $this->load->model('Badge_model');

        $badge_data = array ('site_id'=>$this->User_model->getSiteId(), 'sort'=>'sort_order');

        $badges = $this->Badge_model->getBadgeBySiteId($badge_data);

        /*$json =array();

        if ($badges) {

            foreach ($badges as $badge) {

                $badge_detail = $this->Badge_model->getBadge($badge['badge_id']);

                if(!$badge_detail['deleted']){
                    $json['badges'][] = array(
                        'badge_id' => $badge['badge_id'],
                        'name' => $badge_detail['name'],
                        'description' => $badge_detail['description'],
                        'image' => $badge_detail['image']
                    );    
                }
            }
        }*/

        $json['badges'] = $badges;

        $this->output->set_output(json_encode($json));

    }

    public function jsonGetRuleById(){

        if(!$this->input->get('ruleId')){
            $this->jsonErrorResponse();
            return ;
        }

        $json = $this->Rule_model->getRuleById($this->User_model->getSiteId(),$this->User_model->getClientId(),$this->input->get('ruleId'));

        if($json){
            $this->output->set_output($this->input->get('callback')."(".json_encode($json[0]).")");
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
        if ($this->User_model->hasPermission('access', 'rule_set')) {
            return true;
        } else {
            return false;
        }
    }
}