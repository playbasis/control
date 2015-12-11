<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Workflow extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_model');
        $this->load->model('Player_model');
        $this->load->model('User_model');
        $this->load->model('Permission_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Workflow_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("workflow", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getPlayerList("approved");

    }

    public function rejected() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getPlayerList("rejected");

    }

    public function pending() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getPlayerList("pending");

    }

    private function getPlayerList($status) {
        $this->data['player_list'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['tab_status'] =  $status;
        $this->data['player_list'] = $this->Workflow_model->getPlayerByApprovalStatus($client_id,$site_id,$status);



        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['main'] = 'workflow';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function addaccount() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_approve');
        $this->data['form'] = 'workflow/addaccount/';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //$this->Workflow_model->approvePlayer($user_id);
            redirect('/workflow', 'refresh');
        }
        $this->getForm();
    }

    public function approveconfirm($user_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_approve');
        $this->data['form'] = 'workflow/approveconfirm/'.$user_id;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->Workflow_model->approvePlayer($user_id);
            redirect('/workflow', 'refresh');
        }
        $this->getForm($user_id);
    }

    public function rejectconfirm($user_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_reject');
        $this->data['form'] = 'workflow/rejectconfirm/'.$user_id;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->Workflow_model->rejectPlayer($user_id);
            redirect('/workflow/rejected', 'refresh');
        }
        $this->getForm($user_id);
    }

    public function getForm($user_id){

        $this->data['requester'] = $this->Player_model->getPlayerById($user_id);

        $this->data['main'] = 'workflow_form';
        $this->render_page('template');

    }



    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'goods') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'goods')) {
            return true;
        } else {
            return false;
        }
    }




}
