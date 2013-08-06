<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Rule extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_Model');
        if(!$this->User_Model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Rule_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("rule", $lang['folder']);
    }

    public function index() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList();
    }

    private function getList() {
        include('action_data_log.php');

        $this->load->model('Badge_model');

        $s_siteId = $this->User_Model->getSiteId();
        $s_clientId = $this->User_Model->getClientId();

        $this->data['actionList'] = json_encode($this->Rule_model->getActionGigsawList($s_siteId,$s_clientId));
        $this->data['conditionList'] = json_encode($this->Rule_model->getConditionGigsawList($s_siteId,$s_clientId));
        $this->data['rewardList'] = json_encode($this->Rule_model->getRewardGigsawList($s_siteId,$s_clientId));

        $this->data['jsonConfig_siteId'] = $this->User_Model->getSiteId();
        $this->data['jsonConfig_clientId'] = $this->User_Model->getClientId();

        $this->data['jsonIcons'] = json_encode($icons);

        $this->data['ruleList'] = json_encode($this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId));

        $this->data['requestParams'] = '&siteId='.$s_siteId.'&clientId='.$s_clientId;

        $this->data['main'] = 'rule';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function jsonGetRules(){

        $s_siteId = $this->User_Model->getSiteId();
        $s_clientId = $this->User_Model->getClientId();

        $result = $this->Rule_model->getRulesByCombinationId($s_siteId,$s_clientId);
        $this->output->set_output(json_encode($result));
    }


    public function jsonSaveRule(){

        if(!$this->input->post('json')){
            $this->jsonErrorResponse();
            return ;
        }

        $input = json_decode(html_entity_decode($this->input->post('json')),true);

        $this->output->set_output(json_encode($this->Rule_model->saveRule($input)));
    }


    /*public function deleteRule(){

        /*start  : Wrap all of this to be reqireParam('post','rulesID')*/
        /*$params = $_POST;
        $this->load->model('admin/rules');

        if(!$this->paramsCleaned($params,array('ruleId','siteId','clientId'))){
            $this->jsonErrorResponse();
            return ;
        }

        $this->jsonResponse( $this->model_admin_rules->deleteRule($params['ruleId'],$this->user->getSiteId(),$this->user->getClientId()));
    }*/

    public function loadBadges() {
        $this->load->model('Badge_model');

        $badges = $this->Badge_model->getBadgeBySiteId($this->User_Model->getSiteId());

        if ($badges) {
            foreach ($badges as $badge) {
                $json['badges'][] = array(
                    'badge_id' => $badge['badge_id'],
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                    'image' => $badge['image']
                );
            }
        }

        $this->output->set_output(json_encode($json));

    }

    public function jsonGetRuleById(){

        if(!$this->input->get('ruleId')){
            $this->jsonErrorResponse();
            return ;
        }

        $json = $this->Rule_model->getRuleById($this->User_Model->getSiteId(),$this->User_Model->getClientId(),$this->input->get('ruleId'));

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
}