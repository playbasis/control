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
        $this->lang->load("form_validation", $lang['folder']);
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

        $this->form_validation->set_rules('name', $this->lang->line('form_action_name'), 'trim|required|xss_clean|max_length[100]');
        $this->form_validation->set_rules('icon', $this->lang->line('form_icon'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('color', $this->lang->line('form_color'), 'trim|required|xss_clean|check_space');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){

                $data = $this->input->post();

                if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                    $data['client_id'] = $this->User_model->getClientId();
                    $data['site_id'] = $this->User_model->getSiteId();
                    $data['action_id'] = $this->Action_model->addAction($data);
                    $this->Action_model->addActionToClient($data);
                }else{
                    $this->Action_model->addAction($data);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success'));

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

        $this->form_validation->set_rules('name', $this->lang->line('form_action_name'), 'trim|required|xss_clean|max_length[100]');
        $this->form_validation->set_rules('icon', $this->lang->line('form_icon'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('color', $this->lang->line('form_color'), 'trim|required|xss_clean|check_space');

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->data['message'] = null;

             if (!$this->validateModify()) {
                 $this->data['message'] = $this->lang->line('error_permission');
             }

            if($this->form_validation->run() && $this->data['message'] == null){

                $this->Action_model->editAction($action_id, $this->input->post());

                if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                    $this->Action_model->editActionToClient($action_id, $this->input->post());
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success'));
                redirect('action', 'refresh');
            }
        }

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
                foreach ($this->input->post('selected') as $action_id) {
                    $this->Action_model->delete($action_id);
                }
            }else{
                foreach ($this->input->post('selected') as $action_id) {
                    $this->Action_model->delete($action_id);
                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/action', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset) {

        if($this->User_model->getClientId()){
            echo "Hello";
        }


        $this->load->library('pagination');
        $config['base_url'] = site_url('action/page');
        $config['per_page'] = 10;
        $config['total_rows'] = $this->Action_model->getTotalActions();
        $config["uri_segment"] = 3;

        $this->pagination->initialize($config);

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset
            );

        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

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

        if(isset($action_id)){
            $this->data['action_id'] = $action_id;
        } else {
            $this->data['action_id'] = null;
        }

        $this->data['action'] = $this->Action_model->getAction($action_id);
        $this->data['icons'] = $this->Action_model->getAllIcons();
        $this->data['colors'] = array('blue', 'orange','red', 'green', 'yellow','pink');

        $this->data['main'] = 'action_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function autocomplete(){
        $json = array();

        $client_id = $this->User_model->getClientId();

        if ($this->input->get('filter_name')) {

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'filter_name' => $filter_name
            );

            if($client_id){
                $data['client_id'] = $client_id;
                $results_action = $this->Action_model->getActionClient($data);
            }else{
                $results_action = $this->Action_model->getActions($data);
            }

            foreach ($results_action as $result) {
                $json[] = array(
                    'name' => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
                    'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    'icon' => html_entity_decode($result['icon'], ENT_QUOTES, 'UTF-8'),
                    'color' => html_entity_decode($result['color'], ENT_QUOTES, 'UTF-8'),
                    'sort_order' => html_entity_decode($result['sort_order'], ENT_QUOTES, 'UTF-8'),
                    'status' => html_entity_decode($result['status'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
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