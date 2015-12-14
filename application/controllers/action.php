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
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
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
        $this->form_validation->set_rules('description', $this->lang->line('form_description'), 'trim|xss_clean|max_length[1000]');
        $this->form_validation->set_rules('icon', $this->lang->line('form_icon'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('sort_order', $this->lang->line('form_sort'), 'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('color', $this->lang->line('form_color'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('status', "", '');

        $data_set = $this->input->post('init_dataset');
        if ($data_set != false && !empty($data_set)) {
            $i = 0;
            foreach ($data_set as $data) {
                if (!empty($data['param_name'])) {
                    $this->form_validation->set_rules('init_dataset[' . $i . '][param_name]',
                        $this->lang->line('form_param_name'), 'trim|required|xss_clean|check_space');
                    $this->form_validation->set_rules('init_dataset[' . $i++ . '][label]',
                        $this->lang->line('form_param_label'), 'trim|required|xss_clean|max_length[100]');
                }
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){

                $data = $this->input->post();
                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();

                if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

                    $exists = $this->Action_model->checkActionExists($data);
                    if($exists){
                        $data['action_id'] = $exists['_id'];

                        if($this->Action_model->checkActionClientExists($data)){
                            $this->Action_model->editActionToClient($data['action_id'], $data);
                        }else{
                            $this->Action_model->addActionToClient($data);
                        }
                    }else{
                        $data['action_id'] = $this->Action_model->addAction($data);
                        $this->Action_model->addActionToClient($data);
                    }

                }else{

                    $exists = $this->Action_model->checkActionExists($data);
                    if(!$exists){
                        
                        if($this->input->post('client_id') != 'admin_only'){
//                            $this->load->model('Domain_model');
                            $this->load->model('App_model');

                            $data_admin = $this->input->post();
                            $data_admin['action_id'] = $this->Action_model->addAction($data);
                            $data_admin['site_id'] = $data['site_id'];
                            $domains = $this->App_model->getAppsByClientId($data_admin);

                            foreach($domains as $domain){
                                $data_admin['site_id'] = $domain['_id'];
                                $this->Action_model->addActionToClient($data_admin);
                            }
                        }else{
                            $this->Action_model->addAction($data);
                        }
                    }else{
                        //Tell them that the action already exists
                    }

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
        $this->form_validation->set_rules('description', $this->lang->line('form_description'), 'trim|xss_clean|max_length[1000]');
        $this->form_validation->set_rules('icon', $this->lang->line('form_icon'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('color', $this->lang->line('form_color'), 'trim|required|xss_clean|check_space');
        $this->form_validation->set_rules('sort_order', $this->lang->line('form_sort'), 'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('status', "", '');

        $data_set = $this->input->post('init_dataset');
        if ($data_set != false && !empty($data_set)) {
            $i = 0;
            foreach ($data_set as $data) {
                if (!empty($data['param_name'])) {
                    $this->form_validation->set_rules('init_dataset[' . $i . '][param_name]',
                        $this->lang->line('form_param_name'), 'trim|required|xss_clean|check_space');
                    $this->form_validation->set_rules('init_dataset[' . $i++ . '][label]',
                        $this->lang->line('form_param_label'), 'trim|required|xss_clean|max_length[100]');
                }
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->data['message'] = null;

             if (!$this->validateModify()) {
                 $this->data['message'] = $this->lang->line('error_permission');
             }

            if($this->form_validation->run() && $this->data['message'] == null){

                $data = $this->input->post();

                if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                    $client_id = $this->User_model->getClientId();
                    $data['client_id'] = $client_id;
                    $site_id = $this->User_model->getSiteId();
                    $data['site_id'] = $site_id;
                    $this->Action_model->editActionToClient($action_id, $data);
                }else{
                    $this->Action_model->editAction($action_id, $data);
                    $this->Action_model->editActionToClient($action_id, $data);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
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
                    $this->Action_model->deleteActionClient($action_id);
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

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        
        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset,
                'client_id'=>$client_id,
                'site_id'=>$site_id,
                'sort'=>'sort_order'
            );
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('action/page');
        $config["uri_segment"] = 3;


        if($client_id){
            $this->data['actions'] = $this->Action_model->getActionsSite($filter);
            $config['total_rows'] = $this->Action_model->getTotalActionsSite($filter);
        }else{
            $this->data['actions'] = $this->Action_model->getActions($filter);
            $config['total_rows'] = $this->Action_model->getTotalActions();
        }

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = "<li class='page_index_nav next'>";
        $config['next_tag_close'] = "</li>";

        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = "<li class='page_index_nav prev'>";
        $config['prev_tag_close'] = "</li>";

        $config['num_tag_open'] = '<li class="page_index_number">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page_index_number active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page_index_nav next">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page_index_nav prev">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['pagination_total_pages'] = ceil(floatval($config["total_rows"]) / $config["per_page"]);
        $this->data['pagination_total_rows'] = $config["total_rows"];

        $this->data['main'] = 'action';
        $this->data['isAdmin'] = $this->User_model->isAdmin();
        $this->render_page('template');
    }

    public function getListForAjax($offset) {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        
        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset,
                'client_id'=>$client_id,
                'site_id'=>$site_id,
                'sort'=>'sort_order'
            );
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('action/page');
        $config["uri_segment"] = 3;

        if($client_id){
            $this->data['actions'] = $this->Action_model->getActionsSite($filter);
            $config['total_rows'] = $this->Action_model->getTotalActionsSite($filter);
        }else{
            $this->data['actions'] = $this->Action_model->getActions($filter);
            $config['total_rows'] = $this->Action_model->getTotalActions();
        }

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = "<li class='page_index_nav next'>";
        $config['next_tag_close'] = "</li>";

        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = "<li class='page_index_nav prev'>";
        $config['prev_tag_close'] = "</li>";

        $config['num_tag_open'] = '<li class="page_index_number">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page_index_number active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page_index_nav next">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page_index_nav prev">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['pagination_total_pages'] = ceil(floatval($config["total_rows"]) / $config["per_page"]);
        $this->data['pagination_total_rows'] = $config["total_rows"];

        $this->render_page('action_ajax');
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

        $site_id = $this->User_model->getSiteId();

        if($site_id){
            $this->data['action'] = $this->Action_model->getActionSiteInfo($action_id, $site_id);
        }else{
            $this->data['action'] = $this->Action_model->getAction($action_id);
        }

        $this->load->model('Client_model');

        $this->data['icons'] = $this->Action_model->getAllIcons();
        $this->data['colors'] = array('blue', 'orange','red', 'green', 'yellow','pink');
        $this->data['clients'] = $this->Client_model->getClients(array());

        $this->data['main'] = 'action_form';

        $this->load->vars($this->data);
        $this->data['isAdmin'] = $this->User_model->isAdmin();
        $this->render_page('template');
    }

    public function autocomplete(){
        $json = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

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
                $data['site_id'] = $site_id;
                $results_action = $this->Action_model->getActionsSite($data);
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
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'action') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'action')) {
            return true;
        } else {
            return false;
        }
    }

    public function increase_order($action_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Action_model->increaseOrderByOneClient($action_id, $client_id);
        }else{
            $this->Action_model->increaseOrderByOne($action_id);    
        }

        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));

    }



    public function decrease_order($action_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Action_model->decreaseOrderByOneClient($action_id, $client_id);
        }else{
            $this->Action_model->decreaseOrderByOne($action_id);    
        }
        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));
    }
}
?>