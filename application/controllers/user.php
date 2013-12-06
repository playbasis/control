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
            $this->data['users'] = $this->User_model->fetchAUser($_GET['filter_name']);
        }else{
            $this->data['users'] = $this->User_model->fetchAllUsers($config['per_page'], $offset);    
        }

        $this->data['get_all_users'] = $this->User_model->fetchEntireUsers(); 
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
        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'trim|max_length[255]|xss_clean|check_space');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            //Check to see if it passes the form validation
            if($this->form_validation->run()){
                $this->User_model->editUser($user_id, $this->input->post());
            }else{
                
            }
        }else{
            $this->getForm($user_id);
        }
        
    }

    public function insert(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/insert/';

        //Rules need to be set
        $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|max_length[255]|xss_clean|check_space|required');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'trim|max_length[255]|xss_clean|check_space|required');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run()){
                $this->User_model->insertUser();
                $this->session->data['success'] = $this->lang->line('text_success');
                redirect('user/','refresh');
            }else{
                
            }
        }

        $this->getForm();

    }

    public function delete(){

        $selectedUsers = $this->input->post('selected');

        foreach ($selectedUsers as $selectedUser){
            $this->User_model->deleteUser($selectedUser);
        }

        redirect('user/');

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

}