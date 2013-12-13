<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->load->model('User_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("user", $lang['folder']);

    }

    public function index(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        
        $this->getList(0);
    }

    public function page($offset = 0){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        
        $this->getList($offset);
    }

    public function getList($offset){

        //Pagination set up
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/page');
        $config['total_rows'] = $this->User_model->getTotalNumUsers();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);


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

        if(isset($_GET['filter_name'])){
            $filter = array(
                'filter_name' => $_GET['filter_name']
            );
            $this->data['users'] = $this->User_model->fetchAllUsers($filter);
        }else{
            $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset
            );
            $this->data['users'] = $this->User_model->fetchAllUsers($filter);
        }

        $this->data['main'] = 'user';
        $this->render_page('template');
        
    }

    public function update($user_id){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/update/'.$user_id;

        //Rules need to be set

        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        if($this->input->post('password')!=''){
            $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|max_length[255]|xss_clean|check_space');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            //Check to see if it passes the form validation
            if($this->form_validation->run()){
                $this->User_model->editUser($user_id, $this->input->post());

                redirect('user/','refresh');
            }
        }
        $this->getForm($user_id);

    }

    public function insert(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/insert/';

        //Rules need to be set
        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[255]|xss_clean|check_space|required');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run()){
                $this->User_model->insertUser();
                $this->session->data['success'] = $this->lang->line('text_success');
                redirect('user/','refresh');
            }
        }

        $this->getForm();

    }

    public function insert_ajax(){

        //Rules need to be set
        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[255]|xss_clean|check_space|required');

        $json = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run()){

                $user_id = $this->User_model->insertUser();

                if ($user_id) {
                    $data = array(
                        'client_id' => $this->input->post('client_id'),
                        'user_id' => $user_id
                    );
                    $this->User_model->addUserToClient($data);
                }

                $this->session->data['success'] = $this->lang->line('text_success');
                $json['success'] =  $this->lang->line('text_success');
            }
        }

        $this->output->set_output(json_encode($json));
    }

    public function delete(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;
        
        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            foreach ($selectedUsers as $selectedUser){
                $this->User_model->deleteUser($selectedUser);
            }

            $this->session->data['success'] = $this->lang->line('text_success');

            redirect('/user', 'refresh');
        }

        $this->getList(0);
    }

    public function delete_ajax(){

        $json = array();
        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('user_id') && $this->error['warning'] == null) {

            if($this->checkOwnerUser($this->input->post('user_id'))){

                $this->User_model->deleteUser($this->input->post('user_id'));
            }

            $this->session->data['success'] = $this->lang->line('text_success');

            $json['success'] =  $this->lang->line('text_success');
        }

        $this->output->set_output(json_encode($json));
    }

    public function getForm($user_id = 0){

        if((isset($user_id)) && $user_id !=0){
            $user_info = $this->User_model->getUserInfo($user_id);
        }

        if(isset($user_info)){
            $this->data['user'] = $user_info;    
        }

        $this->data['user_groups'] = $this->User_model->getUserGroups();

        $this->data['main'] = 'user_form';
        $this->render_page('template');

    }

    public function autocomplete(){
        $json = array();

        if ($this->input->get('filter_name')) {

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'filter_name' => $filter_name
            );

            $results_user = $this->User_model->fetchAllUsers($data);

            foreach ($results_user as $result) {
                $json[] = array(
                    'username' => html_entity_decode($result['username'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'user')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerUser($user_id){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $users = $this->User_model->getUserByClientId($this->User_model->getClientId());

            $has = false;

            foreach ($users as $user) {
                if($user['user_id']."" == $user_id.""){
                    $has = true;
                }
            }

            if(!$has){
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    public function login(){

        if($this->input->get('back')){
            $this->session->set_userdata('redirect', $this->input->get('back'));
        }

        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');

        $lang = get_lang($this->session, $this->config);

        $this->lang->load('login', $lang['folder']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $this->data['message'] = null;

            if($this->form_validation->run()){
                $this->load->model('User_model');
                $u = $this->input->post('username');
                $pw = $this->input->post('password');
                $this->User_model->login($u, $pw);

                if ($this->session->userdata('user_id')) {
                    if ($this->session->userdata('redirect')) {
                        $redirect = $this->session->userdata('redirect');
                        $this->session->unset_userdata('redirect');
                    } else {
                        $redirect = '/';
                    }
                    redirect($redirect, 'refresh');
                }

                $this->data['message'] = $this->lang->line('error_login');
            }
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'login';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function logout(){
        $this->load->model('User_model');
        $this->User_model->logout();
        redirect('/', 'refresh');
    }



    public function register(){

        $this->load->model('Image_model');

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'register';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_register'] = $this->lang->line('heading_title_register');
        $this->data['form'] = 'user/register';
        $this->data['user_groups'] = $this->User_model->getUserGroups();

        //Set rules for form regsitration
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'), 'required|matches[password]');
        $this->form_validation->set_rules('domain', $this->lang->line('form_domain'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('site', $this->lang->line('form_site'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');


        //Added
        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($client_info) && $client_info['image'] && (S3_IMAGE . $client_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $client_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($client_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($client_info)) {
            $this->data['image'] = $client_info['image'];
        } else {
            $this->data['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 100, 100);

        //End Added


        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run()){
                $this->User_model->insertRegistration();                
            }else{
                $this->data['temp_fields'] = $this->input->post();
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function doregister(){
        echo "<pre>";
        var_dump($this->input->post());
        echo "</pre>";
    }

}