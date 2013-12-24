<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Action extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Action_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("action", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'action/insert';

        $this->form_validation->set_rules('exp', $this->lang->line('exp'), 'trim|required|numeric|xss_clean|check_space');
        $this->form_validation->set_rules('level', $this->lang->line('level'), 'trim|required|numeric|xss_clean|check_space');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                    $data = $this->input->post();
                    $data['client_id'] = $this->User_model->getClientId();
                    $data['site_id'] = $this->User_model->getSiteId();
                    $this->Action_model->addLevelSite($data);
                }else{
                    $this->Action_model->addLevel($this->input->post());
                }

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/action', 'refresh');
            }
        }

        $this->getForm();
    }

    public function update($action_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'action/update/'.$action_id;

        // if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        //     $this->data['message'] = null;

        //     if (!$this->validateModify()) {
        //         $this->data['message'] = $this->lang->line('error_permission');
        //     }

        //     if($this->form_validation->run() && $this->data['message'] == null){
        //         $this->Action_model->editLevel($level_id, $this->input->post());
        //         if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
        //             $this->Action_model->editLevelSite($level_id, $this->input->post());
        //         }else{
        //             $this->Action_model->editLevel($level_id, $this->input->post());
        //         }

        //         $this->session->data['success'] = $this->lang->line('text_success');

        //         redirect('/level', 'refresh');
        //     }
        // }

        $this->getForm($action_id);
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {

            if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                foreach ($this->input->post('selected') as $level_id) {
                    $this->Level_model->deleteLevelSite($level_id);
                }
            }else{
                foreach ($this->input->post('selected') as $level_id) {
                    $this->Level_model->deleteLevel($level_id);
                }
            }

            $this->session->data['success'] = $this->lang->line('text_success');

            redirect('/level', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset) {

        $this->load->library('pagination');
        $config['base_url'] = site_url('action/page');
        $config['per_page'] = 10;
        $config['total_rows'] = $this->Action_model->getTotalActions();
        // $config["uri_segment"] = 3;

        $this->pagination->initialize($config);

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset
            );

        $this->data['actions'] = $this->Action_model->getActions($filter);
        $this->data['main'] = 'action';
        $this->render_page('template');
        
    }

    private function getForm($action_id=null) {

        $this->load->model('Image_model');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = array();
        }

        if (isset($this->error['exp'])) {
            $this->data['error_warning'] = $this->error['exp'];
        } else {
            $this->data['error_warning'] = array();
        }

        if (isset($this->error['level'])) {
            $this->data['error_warning'] = $this->error['level'];
        } else {
            $this->data['error_warning'] = array();
        }

        // if (isset($level_id) && ($level_id != 0)) {
        //     if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
        //         $level_info = $this->Level_model->getLevelSite($level_id);
        //     }else{
        //         $level_info = $this->Level_model->getLevel($level_id);
        //     }

        // }

        if(isset($action_id)){
            $this->data['action_id'] = $action_id;
        } else {
            $this->data['action_id'] = null;
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($level_info)) {
            $this->data['image'] = $level_info['image'];
        } else {
            $this->data['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($level_info) && $level_info['image'] && (S3_IMAGE . $level_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $level_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($level_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 100, 100);

        if ($this->input->post('level_title')) {
            $this->data['level_title'] = $this->input->post('level_title');
        } elseif (!empty($level_info)) {
            $this->data['level_title'] = $level_info['level_title'];
        } else {
            $this->data['level_title'] = '';
        }

        if ($this->input->post('exp')) {
            $this->data['exp'] = $this->input->post('exp');
        } elseif (!empty($level_info)) {
            $this->data['exp'] = $level_info['exp'];
        } else {
            $this->data['exp'] = 0;
        }

        if ($this->input->post('level')) {
            $this->data['level'] = $this->input->post('level');
        } elseif (!empty($level_info)) {
            $this->data['level'] = $level_info['level'];
        } else {
            $this->data['level'] = 1;
        }

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($level_info)) {
            $this->data['sort_order'] = $level_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($level_info)) {
            $this->data['status'] = $level_info['status'];
        } else {
            $this->data['status'] = false;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'action_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'action')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'action')) {
            return true;
        } else {
            return false;
        }
    }
}
?>