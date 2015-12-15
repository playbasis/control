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
        $this->load->model('App_model');

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
        $this->data['form'] = 'workflow/';

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $result = $this->Workflow_model->deletePlayer($this->input->post('user_id'));
            if($result){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->deletePlayer($selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

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
        $this->data['form'] = 'workflow/rejected/';

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $result = $this->Workflow_model->deletePlayer($this->input->post('user_id'));
            if($result){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/rejected', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->deletePlayer($selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow/rejected', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

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
        $this->data['form'] = 'workflow/pending/';

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $result = $this->Workflow_model->deletePlayer($this->input->post('user_id'));
            if($result){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/pending', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action')=="approve"){
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->approvePlayer($selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_approve'));
                redirect('/workflow/pending', 'refresh');
            }
            elseif($this->input->post('action')=="reject") {
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->rejectPlayer($selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_reject'));
                redirect('/workflow/pending', 'refresh');
            }
            elseif($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->deletePlayer($selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow/pending', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->getPlayerList("pending");

    }

    private function getPlayerList($status) {
        $this->data['player_list'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['tab_status'] =  $status;
        $this->data['player_list'] = $this->Workflow_model->getPlayerByApprovalStatus($client_id,$site_id,$status);

        $pending_count = count($this->Workflow_model->getPlayerByApprovalStatus($client_id,$site_id,"pending"));
        $this->data['pending_count'] =$pending_count;


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

    public function edit_account($user_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_edit');
        $this->data['form'] = 'workflow/edit_account/'.$user_id;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->Player_model->editPlayer($user_id,$_POST);
            $this->session->set_flashdata('success', $this->lang->line('text_success_edit'));
            redirect('/workflow', 'refresh');
        }
        $this->getForm($user_id);
    }

    public function create_account() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_create');
        $this->data['form'] = 'workflow/create_account/';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $this->input->post();
            $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
            $this->_api = $this->playbasisapi;

            $platforms = $this->App_model->getPlatFormByAppId(array(
                'site_id' => $this->User_model->getSiteId(),
            ));
            $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
            if (!$platform) {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'fail', 'message' => 'Cannot find any active platform'));
                    exit();
                }
            }
            $this->_api->set_api_key($result['api_key']);
            $this->_api->set_api_secret($result['api_secret']);
            $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
            $this->_api->auth($pkg_name);

            $status = $this->_api->register($data['cl_player_id'], $data['username'], $data['email'], $data);

            if($status->success) {
                $this->session->set_flashdata('success', $this->lang->line('text_success_create'));
                //redirect($this->session->userdata('previous_page'), 'refresh');
                redirect('/workflow', 'refresh');
            }else{
                $this->data['message'] = $status->message;
            }
        }

        $this->getForm();
    }

    public function getForm($user_id=0){

        $this->data['requester'] = array();
        if (isset($_POST['username'])) {
            $this->data['requester'] = $_POST;
        }elseif($user_id !=0){
            $this->data['requester'] = $this->Player_model->getPlayerById($user_id);
        }else{
            $this->data['requester'] = array('approve_status'=>'approved');
        }

        $this->data['main'] = 'workflow_form';
        $this->render_page('template');

    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'user')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'goods') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'workflow')) {
            return true;
        } else {
            return false;
        }
    }




}
