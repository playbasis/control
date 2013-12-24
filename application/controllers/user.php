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
        $this->load->model('Client_model');
        $this->load->model('Plan_model');
        $this->load->model('Domain_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("user", $lang['folder']);

    }

    public function index(){

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

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

        //Added
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');        
        $config['base_url'] = site_url('user/page');
        $config['per_page'] = 10;

        if($client_id){

            $data = array(
                'client_id'=>$client_id
                );
            $config['total_rows'] = $this->User_model->getTotalUserByClientId($data);
        }else{
            $config['total_rows'] = $this->User_model->getTotalNumUsers();
        }

        //End Added 

        //Pagination set up
        // $this->load->library('pagination');
        // $config['base_url'] = site_url('user/page');
        // $config['total_rows'] = $this->User_model->getTotalNumUsers();
        // $config['per_page'] = 10;

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

            if($client_id){

                $filter['client_id'] = $client_id;
                $user_ids = $this->User_model->getUserByClientId($filter);

                $UsersInfoForClientId = array();
                foreach ($user_ids as $user_id){
                    $UsersInfoForClientId[] = $this->User_model->getUserInfo($user_id['user_id']);
                }

                $this->data['users'] = $UsersInfoForClientId;


            }else{
                $this->data['users'] = $this->User_model->fetchAllUsers($filter);    
            }
            
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

        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
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

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                redirect('/user', 'refresh');
                
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
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[255]|xss_clean|check_space|required');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');



        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $client_id = $this->User_model->getClientId();

            if($this->form_validation->run()){
                $user_id = $this->User_model->insertUser();

                if($user_id){
                    $this->User_model->insertUserToClient($client_id,$user_id);
                    $this->session->data['success'] = $this->lang->line('text_success');

                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('user/','refresh');    
                }else{
                    $this->session->set_flashdata('fail', $this->lang->line('text_fail'));
                    redirect('user/insert');
                }
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

            $this->data['message'] = null;

            if($this->checkLimitUser($this->input->post('client_id'))){
                $this->data['message'] = $this->lang->line('error_limit');
                $json['error'] = $this->data['message'];
            }

            if($this->form_validation->run() && $this->data['message'] == null){

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

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
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

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'user')) {
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

    private function checkLimitUser($client_id){

        $data['client_id'] = $client_id;
        $users = $this->User_model->getTotalUserByClientId($data);

        if ($users > 10) {
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
        $this->load->model('Permission_model');

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
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'), 'required|matches[password]');
        $this->form_validation->set_rules('domain_name', $this->lang->line('form_domain'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('site_name', $this->lang->line('form_site'), 'trim|required|min_length[3]|max_length[40]|xss_clean');


        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run()){
                $user_id = $this->User_model->insertUser();
                $user_info = $this->User_model->getUserInfo($user_id);

                $client_id = $this->Client_model->insertClient();//returns only client id

                $this->User_model->insertUserToClient($client_id, $user_info['_id']);//Does not return anything just inserts to 'user_to_client' table

                $data = $this->input->post();
                $data['client_id'] = $client_id;
                $data['limit_users'] = 1000;
                $data['date_start'] = date("Y-m-d H:i:s");
                $data['date_expire'] = date("Y-m-d H:i:s", strtotime("+1 year"));

                $site_info = $this->Domain_model->addDomain($data); //returns an array of client_site

                $plan_id = $this->Plan_model->getPlanID("BetaTest");//returns plan id

                $this->Client_model->whoandwhat($client_id, $site_info, $plan_id);

                $data = array();
                    $data['client_id'] = $client_id;
                    $data['plan_id'] = $plan_id;
                    $data['site_id'] = $site_info;
                    $this->Permission_model->addPlanToPermission($data);

                redirect('login');
            }else{
                $this->data['temp_fields'] = $this->input->post();
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function enable_user(){
        if($_GET['key']){
            $random_key = $_GET['key'];
            if($this->User_model->checkRandomKey($random_key)){
                echo "IT EXISTS!!!";
            }else{
                echo "IT DOES NOT EXIST";
            }
        }else{
            redirect('login');
        }
        
    }

    public function edit_account(){
        if($this->session->userdata('user_id')){

            $this->data['message'] = null;
            $user_id = $this->session->userdata('user_id');

            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['title'] = $this->lang->line('text_edit_account');
            $this->data['form'] = 'user/edit_account';

            $this->data['user_info'] = $this->User_model->getUserInfo($user_id);
            $this->data['usergroup_name'] = $this->User_model->getUserGroupNameForUser($user_id);

            $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'), 'matches[password]');

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $data = array(
                    'password'=>$this->input->post('password'),
                    'confirm_password' =>$this->input->post('password_confirm'),
                    'edit_account'=>true
                    );
                if($this->form_validation->run()){
                    $this->User_model->editUser($user_id, $data);
                }
            }

            $this->data['main'] = 'edit_account.php';
            $this->render_page('template');

        }
    }


}