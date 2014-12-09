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
//        $this->load->model('Domain_model');
        $this->load->model('App_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("user", $lang['folder']);
        $this->lang->load("login", $lang['folder']);

    }

    public function index(){

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        
        $this->getList(0);
    }

    public function page($offset = 0){

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
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

        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

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

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset
        );
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

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
            


        $this->data['main'] = 'user';
        $this->render_page('template');
        
    }

    public function update($user_id){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/update/'.$user_id;

        //Rules need to be set

        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean');
        if($this->input->post('password')!=''){
            $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|max_length[255]|xss_clean|check_space|min_length[5]');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');
        }
        $this->form_validation->set_rules('user_group', "", '');
        $this->form_validation->set_rules('status', "", '');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            //Check to see if it passes the form validation

            if($this->form_validation->run()){
                $data = array();
                $data['email'] = $this->input->post('email');
                if(!$this->User_model->findEmail($data)){
                    $this->User_model->editUser($user_id, $this->input->post());

                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/user', 'refresh');
                }else{
                    $user = $this->User_model->getUserInfo($user_id);
                    if($user['username'] != $data['email']){
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail'));
                        redirect('/user/update/'.$user_id);    
                    }
                    $this->User_model->editUser($user_id, $this->input->post());
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/user', 'refresh');
                }
            }
            
        }
        $this->getForm($user_id);

    }

    /*
     * Add Other user to use the same Client-Site
     * Each Client-Site has limit according to plan
     * ** Compatible-purpose ** default is 3
     */
    public function insert(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/insert/';

        //Rules need to be set
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[255]|xss_clean|check_space|required');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');
        $this->form_validation->set_rules('user_group', "", '');
        $this->form_validation->set_rules('status', "", '');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $client_id = $this->User_model->getClientId();
            $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

            if($this->form_validation->run()){
                // get Plan limit_others.user
                $user_limit = null;
                try {
                    $user_limit = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "user");
                } catch(Exception $e) {
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_internal"));
                    redirect("user/");
                }

                // get current user usage from this client
                $user_usage = $this->User_model->getTotalUserByClientId(
                    array("client_id" => $client_id));

                // compute
                if ($user_limit !== null && $user_usage >= $user_limit) {
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_limit_user"));
                    redirect("user/");
                }

                $user_id = $this->User_model->insertUser();

                if($user_id){
                    if($client_id){
                        $data = array(
                            'client_id' => $client_id,
                            'user_id' => $user_id
                        );
                        $this->User_model->addUserToClient($data);
                    }

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
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[255]|xss_clean|check_space|required');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('form_password'), 'required|matches[password]');
        $this->form_validation->set_rules('user_group', $this->lang->line('form_user_group'), 'required');

        $json = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $this->data['message'] = null;

            /*if($this->checkLimitUser($this->input->post('client_id'))){
                $this->data['message'] = $this->lang->line('error_limit');
                $json['error'] = $this->data['message'];
            }*/

            if($this->form_validation->run() && $this->data['message'] == null){

                $email = $this->input->post('email');
                $data['email'] = $email;
                $check_email = $this->User_model->findEmail($data);

                if(!$check_email){
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
                }else{
                    $json['error'] = 'The Email provided already exists';
                }
                
            }else{
                $json['error'] = "Please provide the necessary fields below or check if there are any errors: ".$this->data['message'];
            }
        }

        $this->output->set_output(json_encode($json));
    }

    public function delete(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
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
                $user_ids = $this->User_model->getUserByClientId($data);

                $UsersInfoForClientId = array();
                foreach ($user_ids as $user_id){
                    $user_info = $this->User_model->getUserInfo($user_id['user_id']);
                    if(preg_match('/'.$filter_name.'/', $user_info['username'])){
                        $UsersInfoForClientId[] = $user_info;
                    }
                }

                $results_user = $UsersInfoForClientId;


            }else{
                $results_user = $this->User_model->fetchAllUsers($data);
            }

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
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'user') &&  $this->Feature_model->getFeatureExitsByClientId($client_id, 'user')) {
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

        if ($this->session->userdata('user_id')) {
            redirect('/', 'refresh');
        }

        if($this->input->get('back')){
            $this->session->set_userdata('redirect', $this->input->get('back'));
        }

        $this->form_validation->set_rules('username', $this->lang->line('entry_username'), 'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        // $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('entry_password'), 'trim|required');

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
                    if($this->input->post('format') == 'json'){
                        echo json_encode(array('status' => 'success', 'message' => ''));
                        exit();
                    }
                    redirect($redirect, 'refresh');
                }
                if($this->input->post('format') == 'json'){
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_login')));
                    exit();
                }
                $this->data['message'] = $this->lang->line('error_login');
            }else{
                if($this->input->post('format') == 'json'){
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    exit();
                }
            }
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'login';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');

        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function logout(){
        $this->load->model('User_model');
        $this->User_model->logout();
        setcookie("client_id", null);
        setcookie("site_id", null);

        redirect('/', 'refresh');
    }

    public function register(){
        
//        $this->data['meta_description'] = $this->lang->line('meta_description');
//        $this->data['main'] = 'register';
//        $this->data['title'] = $this->lang->line('title');
//        $this->data['heading_title_register'] = $this->lang->line('heading_title_register');
//        $this->data['form'] = 'user/register?plan='.$this->input->get('plan');
//        $this->data['user_groups'] = $this->User_model->getUserGroups();
//
//        $plan_id = null;
//        $plan = null;
//        try {
//            //$plan_id = new MongoId($this->input->get('plan'));
//            //$plan = $this->Plan_model->getPlanById($plan_id);
//
//            $plan = $this->Plan_model->getPlanById(new MongoId(FREE_PLAN));
//
//            if (!$plan) throw new Exception('Cannot find plan '.$plan_id);
//            if (!array_key_exists('price', $plan)) {
//                $plan['price'] = DEFAULT_PLAN_PRICE;
//            }
//        } catch (Exception $e) {
//            header('Location: http://www.playbasis.com/plans.html');
//            echo 'Invalid plan: '.$e->getMessage();
//            exit();
//        }
//
//        $this->data['plan'] = $plan;
//
//        //Set rules for form registration
//        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
//        $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|required|min_length[5]|max_length[40]|xss_clean|check_space');
//        $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'), 'required|matches[password]');
//        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
//        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[40]|xss_clean');
//        $this->form_validation->set_rules('company_name', $this->lang->line('form_company_name'), 'trim|required|max_length[100]|xss_clean');
//        // $this->form_validation->set_rules('domain_name', $this->lang->line('form_domain'), 'trim|required|min_length[3]|max_length[100]|xss_clean|check_space|valid_url_format|url_exists');
//        $this->form_validation->set_rules('domain_name', $this->lang->line('form_domain'), 'trim|required|min_length[3]|max_length[100]|xss_clean|check_space|url_exists_without_http');
//        $this->form_validation->set_rules('site_name', $this->lang->line('form_site'), 'trim|required|min_length[3]|max_length[100]|xss_clean');
//
//        //ReCaptcha stuff
//        $this->load->helper('recaptchalib');
//        $publicKey = CAPTCHA_PUBLIC_KEY;
//        $this->data['recaptcha'] = recaptcha_get_html($publicKey);
//
//        if($_SERVER['REQUEST_METHOD'] == 'POST'){
//
//            //ReCaptcha stuff
//            $privateKey = CAPTCHA_PRIVATE_KEY;
//
//            if($this->input->post('format') == 'json' || $this->input->post('version') == 'new'){
//                $_POST['password'] = DEFAULT_PASSWORD;
//                $_POST['password_confirm'] = DEFAULT_PASSWORD;
//                $_POST['site_name'] = $_POST['domain_name'];
//            }
//
//            $recaptcha_challenge_field = isset($_POST["recaptcha_challenge_field"])?$_POST["recaptcha_challenge_field"]:null;
//            $recaptcha_response_field = isset($_POST["recaptcha_response_field"])?$_POST["recaptcha_response_field"]:null;
//
//            $resp = recaptcha_check_answer($privateKey,$_SERVER["REMOTE_ADDR"],$recaptcha_challenge_field,$recaptcha_response_field);
//
//            if($this->form_validation->run()){
//                // $user_id = $this->User_model->insertUser();
//                $domain = $this->Domain_model->checkDomainExists($this->input->post());
//
//                // if($user_id){
//                if(!$domain){
//                    if (isset($resp) && !$resp->is_valid) {
//                    // What happens when the CAPTCHA was entered incorrectly
//                        if($this->input->post('format') == 'json'){
//                            echo json_encode('Incorrect captcha code');
//                            exit();
//                        }
//                        $this->data['incorrect_captcha'] = $this->lang->line('text_incorrect_captcha');
//                        $this->data['temp_fields'] = $this->input->post();
//                    }else{
//                        if($user_id = $this->User_model->insertUser()){ // [1] firstly insert a user into "user"
//                            $user_info = $this->User_model->getUserInfo($user_id);
//
//                            $client_id = $this->Client_model->insertClient($this->input->post(), $plan); // [2] then insert a new client into "playbasis_client"
//
//                            $data = $this->input->post();
//                            $data['client_id'] = $client_id;
//                            $data['user_id'] =  $user_info['_id'];
//                            $this->User_model->addUserToClient($data); // [3] map the user to the client in "user_to_client"
//
//                            $site_id = $this->Domain_model->addDomain($data); // [4] then insert a new domain into "playbasis_client_site"
//
//                            $this->Client_model->addPlanToPermission(array( // [5] bind the client to the selected plan "playbasis_permission"
//                                'client_id' => $client_id->{'$id'},
//                                'plan_id' => $plan_id->{'$id'},
//                                'site_id' => $site_id->{'$id'},
//                            ));
//
//                            $another_data['domain_value'] = array(
//                                'site_id' => $site_id,
//                                'status' => true
//                            );
//
//                            $this->Client_model->editClientPlan($client_id, $plan_id, $another_data); // [6] finally, populate 'feature', 'action', 'reward', 'jigsaw' into playbasis_xxx_to_client
//
//                            if($this->input->post('format') == 'json'){
//                                echo json_encode(array("response"=>"success"));
//                                exit();
//                            }
//                            // echo "<script>alert('We have sent you an email, please click the link provided to activate your account.');</script>";
//                            // echo "<script>window.location.href = '".site_url()."';</script>";
////                            $this->session->set_flashdata('email_sent', $this->lang->line('text_email_sent'));
////                            redirect('login', 'refresh');
//                            redirect('login#register', 'refresh');
//                        }else{
//                            $this->data['fail_email_exists'] = $this->lang->line('text_fail');
//
//                            if($this->input->post('format') == 'json'){
//                                echo json_encode($this->data['fail_email_exists']);
//                                exit();
//                            }
//                        }
//                    }
//                }else{
//
//                    $data = array('email' => $this->input->post('email'));
//                    if($this->User_model->findEmail($data)){
//                        $this->data['fail_email_exists'] = $this->lang->line('text_fail');
//                    }
//                    $this->data['fail_domain_exists'] = $this->lang->line('text_fail_domain_exists');
//
//                    if($this->input->post('format') == 'json'){
//                        echo json_encode($this->data['fail_domain_exists']);
//                        exit();
//                    }
//                }
//            }else{
//                if($this->input->post('format') == 'json'){
//                    echo json_encode(strip_tags(validation_errors()));
//                    exit();
//                }
//            }
//            $this->data['temp_fields'] = $this->input->post();
//        }

//        $this->load->vars($this->data);
//        $this->render_page('template');
        redirect('login#register', 'refresh');
    }

    /* new register flow (without captcha, plan and domain) */
    public function regis(){

        $success = false;

        //Set rules for form registration
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[40]|xss_clean');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $_POST['password'] = DEFAULT_PASSWORD;
            $_POST['password_confirm'] = DEFAULT_PASSWORD;

            if($this->form_validation->run()){
                if($user_id = $this->User_model->insertUser()){ // [1] firstly insert a user into "user"
                    $user_info = $this->User_model->getUserInfo($user_id);

                    $plan = $this->Plan_model->getPlanById(new MongoId(FREE_PLAN));

                    $client_id = $this->Client_model->insertClient($this->input->post(), $plan); // [2] then insert a new client into "playbasis_client"

                    $data = $this->input->post();
                    $data['client_id'] = $client_id;
                    $data['user_id'] =  $user_info['_id'];
                    $this->User_model->addUserToClient($data); // [3] map the user to the client in "user_to_client"

                    $this->Client_model->addPlanToPermission(array( // [5] bind the client to the selected plan "playbasis_permission"
                        'client_id' => $client_id->{'$id'},
                        'plan_id' => $plan['_id']->{'$id'},
                        'site_id' => null,
                    ));

                    $success = true;
                    $message = $this->lang->line('text_email_sent');
                }else{
                    $message = $this->lang->line('text_fail');
                }
            }else{
                $message = strip_tags(validation_errors());
            }
        } else {
            $message = "Unsupported HTTP method";
        }

        $res = array("response" => $success ? "success" : "fail", "message" => $message);
        if($success){
            $res = array_merge($res, array("data" => $user_id.""));
        }
        echo json_encode($res);
        exit();
    }

    public function signup_finish(){
        $user_id = $this->input->get('i');

        $user_info = $this->User_model->getUserInfo(new MongoId($user_id));

        $this->data['user_before_info'] = $user_info;
        $this->data['url_resend'] = site_url('user/resend_signup_email?i='.$user_id."");
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'account_activated';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');

        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function resend_signup_email(){
        $user_id = $this->input->get('i');

        $user_info = $this->User_model->getUserInfo(new MongoId($user_id));

        $this->load->library('parser');
        $this->load->library('email');
        $vars = array(
            'firstname' => $user_info['firstname'],
            'lastname' => $user_info['lastname'],
            'username' => $user_info['username'],
            'key' => $user_info['random_key'],
            'url'=> site_url('enable_user?key='),
            'base_url' =>site_url()
        );

        $htmlMessage = $this->parser->parse('emails/user_activated.html', $vars, true);

        $this->email($user_info['email'], '[Playbasis] Please activate your account', $htmlMessage);

        redirect('user/signup_finish?i='.$user_id, 'refresh');
    }

    public function list_pending_users() {
        $this->data['users'] = array();
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            $results = $this->User_model->listPendingUsers();
            if ($results) {
                foreach ($results as $result) {
                    $this->data['users'][] = array(
                        '_id' => $result['_id'],
                        'user_group_id' => $result['user_group_id'],
                        'first_name' => $result['firstname'],
                        'last_name' => $result['lastname'],
                        'email' => $result['email'],
                        'username' => $result['username'],
                        'status' => $result['status'],
                        'date_added' => $result['date_added'],
                        'random_key' => isset($result['random_key']) ? $result['random_key'] : null,
                    );
                }
            }
        } else {
	        $this->session->set_flashdata('error', $this->lang->line('error_access'));
        }
        $this->data['main'] = 'user_pending';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function enable_users() {
        $this->load->library('parser');
        $this->error['warning'] = null;
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            if ($this->input->post('selected') && $this->error['warning'] == null) {
                foreach ($this->input->post('selected') as $user_id) {
                    $initial_password = get_random_password(8,8);
                    $this->User_model->insertNewPassword($user_id, $initial_password);
                    $this->User_model->enableUser($user_id);
                    $user = $this->User_model->getById($user_id);
                    if ($user) {
                        /* find plan of this user */
                        $client_id = $this->User_model->getClientIdByUserId($user_id);
                        $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
                        $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
                        if (!array_key_exists('price', $plan)) {
                            $plan['price'] = DEFAULT_PLAN_PRICE;
                        }
                        $price = $plan['price'];
                        $free_flag = $price <= 0;
                        $paid_flag = !$free_flag;
                        /* proceed by sending email */
                        $vars = array(
                            'firstname' => $user['firstname'],
                            'lastname' => $user['lastname'],
                            'username' => $user['username'],
                            'password' => $initial_password,
                            'paid_flag' => $paid_flag ? array(array('force' => 1)) : array(),
                        );
                        $htmlMessage = $this->parser->parse('user_guide.html', $vars, true);
                        $this->email($user['email'], '[Playbasis] Getting started with Playbasis', $htmlMessage);
                    }
                }
                $this->session->set_flashdata('success', $this->lang->line('text_success_enable'));
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('error_access'));
        }
        redirect('/pending_users', 'refresh');
    }

    public function enable_user(){
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'enable user';
        $this->data['title'] = $this->lang->line('title');

        if(isset($_GET['key'])){
            $random_key = $_GET['key'];
            $user = $this->User_model->checkRandomKey($random_key);
            if($user != null){
                $user_id = $user[0]['_id'];

                /* generate initial password */
//                if(dohash(DEFAULT_PASSWORD,$user[0]['salt']) == $user[0]['password']){
//                    $initial_password = get_random_password(8,8);
//                    $this->User_model->insertNewPassword($user_id, $initial_password);
//                }else{
//                    $initial_password = '******';
//                }

                /* check free/paid */
//                $client_id = $this->User_model->getClientIdByUserId($user_id);
//                $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
//                $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
//                if (!array_key_exists('price', $plan)) {
//                    $plan['price'] = DEFAULT_PLAN_PRICE;
//                }
//                $price = $plan['price'];
//                $free_flag = $price <= 0;
//                $paid_flag = !$free_flag;

                /* send email */
                /*$user = $this->User_model->getById($user_id);
                $vars = array(
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'username' => $user['username'],
                    'password' => $initial_password,
                    'paid_flag' => $paid_flag ? array(array('force' => 1)) : array(),
                );
                $htmlMessage = $this->parser->parse('user_guide.html', $vars, true);
                $this->email($user['email'], '[Playbasis] Getting started with Playbasis', $htmlMessage);*/

                /* force login, so that user doesn't have to type email and password (obtained by email) */
                $this->User_model->force_login($user_id);
                redirect('account/update_profile', 'refresh');
            }else{
                $this->data['topic_message'] = 'Your validation key is invalid,';
                $this->data['message'] = 'Please contact Playbasis.';
                $this->data['main'] = 'partial/something_wrong';
                $this->render_page('template_beforelogin');
            }
        }else{
            $this->data['topic_message'] = 'Your validation key is invalid,';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->render_page('template_beforelogin');
        }
    }

    private function email($to, $subject, $message) {
        $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');
        $this->amazon_ses->to($to);
        $this->amazon_ses->bcc(array(EMAIL_FROM,'pascal@playbasis.com'));
        $this->amazon_ses->subject($subject);
        $this->amazon_ses->message($message);
        $this->amazon_ses->send();
    }

    public function edit_account(){
        if($this->session->userdata('user_id')){

            $this->data['message'] = null;
            $user_id = $this->session->userdata('user_id');

            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['title'] = $this->lang->line('text_edit_account');
            $this->data['form'] = 'user/edit_account';

            $this->data['user_info'] = $this->User_model->getUserInfo($user_id);            

            if ($this->input->post('image')) {
                $this->data['image'] = $this->input->post('image');
            } elseif (!empty($this->data['user_info']) && isset($this->data['user_info']['image'])) {
                $this->data['image'] = $this->data['user_info']['image'];
            } else {
                $this->data['image'] = 'no_image.jpg';
            }

            if ($this->data['image']){
                $info = pathinfo($this->data['image']);
                if(isset($info['extension'])){
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($this->data['image'], 0, utf8_strrpos($this->data['image'], '.')).'-100x100.'.$extension;
                    $this->data['thumb'] = S3_IMAGE.$new_image;
                }else{
                    $this->data['thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
                }
            }else{
                $this->data['thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
            }

            $this->data['usergroup_name'] = $this->User_model->getUserGroupNameForUser($user_id);

            $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'), 'trim|required|min_length[3]|max_length[40]|xss_clean');
            $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'), 'matches[password]');

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $data = array(
                    'firstname'=>$this->input->post('firstname'),
                    'lastname'=>$this->input->post('lastname'),
                    'password'=>$this->input->post('password'),
                    'confirm_password' =>$this->input->post('password_confirm'),
                    'edit_account'=>true,
                );
                if($this->input->post('image') != "no_image.jpg"){
                    $data['image'] =$this->input->post('image');
                }
                if($this->input->post('image') == ''){
                    $data['image'] = '';
                }
                if($this->form_validation->run()){
                    if($this->User_model->editUser($user_id, $data)){
                        $this->data['success'] = $this->lang->line('text_success_update');
                    }
                }
            }

            $this->data['main'] = 'edit_account';
            $this->render_page('template');
        }
    }

    public function forgot_password(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'register';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_forgot_password'] = $this->lang->line('heading_forgot_password');
        $this->data['form'] = 'user/forgot_password';
        
        $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|check_space');

        $this->data['message'] = null;

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->form_validation->run() && $this->data['message'] == null){
                $check_email = $this->User_model->findEmail($this->input->post());

                if($check_email){
                    $random_key = get_random_password(8,8);
                    $this->User_model->insertRandomPasswordKey($random_key, $check_email[0]['_id']);
                    $email = $check_email[0]['email'];

                    $this->load->library('email');
                    $this->load->library('parser');
               
                    $data = array(
                        'url' => site_url('reset_password?key='.$random_key),
                        'base_url' =>site_url()
                        );

                    $config['mailtype'] = 'html';
                    $config['charset'] = 'utf-8';
                    $subject = "[Playbasis] Reset Your Password";
                    $htmlMessage = $this->parser->parse('emails/user_forgotpassword.html', $data, true);

                    $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');
                    $this->amazon_ses->to($email);
                    // $this->amazon_ses->bcc(EMAIL_FROM);
                    $this->amazon_ses->subject($subject);
                    $this->amazon_ses->message($htmlMessage);
                    $this->amazon_ses->send();

                    if($this->input->post('format') == 'json'){
                        echo json_encode(array('status' => 'success', 'message' => 'A link has been sent to your email, please click on it and change your password.'));
                        exit();
                    }

                    $this->data['topic_message'] = 'A link has been sent to your email,';
                    $this->data['message'] = 'Please click on it and change your password.';
                    $this->data['main'] = 'partial/something_wrong';
                    $this->render_page('template_beforelogin');
                }else{
                    // echo "<script>alert('The email was not found in our server, please make sure you have typed it correctly.');</script>";
                    // $this->data['message'] = $this->lang->line('error_no_email');
                    if($this->input->post('format') == 'json'){
                        echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_no_email')));
                        exit();
                    }
//                    $this->session->set_flashdata('fail', $this->lang->line('error_no_email'));
//                    redirect('forgot_password', 'refresh');
                    redirect('login#forgotpassword', 'refresh');
                }
            }else{
                if($this->input->post('format') == 'json'){
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    exit();
                }
            }


        }
//        $this->data['main'] = 'forgot_password';
//        $this->render_page('template');

        redirect('login#forgotpassword', 'refresh');
    }

    public function reset_password(){
        if(isset($_GET['key'])){
            $random_key = $_GET['key'];
            $user = $this->User_model->checkRandomPasswordKey($random_key);
            $data = array(
                'user'=>$user
                );
            $this->session->set_userdata($data);
        }

        if($this->session->userdata('user')){

            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['main'] = 'register';
            $this->data['title'] = $this->lang->line('title');
            $this->data['heading_forgot_password'] = $this->lang->line('heading_forgot_password');
            $this->data['form'] = 'user/reset_password';

            $this->form_validation->set_rules('password', $this->lang->line('form_password'), 'trim|required|min_length[5]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'), 'required|matches[password]');

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                if($this->form_validation->run()){
                    $new_password = $this->input->post('password');
//                    $user_id = $this->session->userdata('user')[0]['_id'];
                    $user_id = $this->session->userdata('user');
                    $this->User_model->insertNewPassword($user_id[0]['_id'], $new_password);
                    $this->session->unset_userdata('user');

                    if($this->input->post('format') == 'json'){
                        echo json_encode(array('status' => 'success', 'message' => 'Your password has been changed! We will redirect you to our login page.'));
                        exit();
                    }

                    $this->data['topic_message'] = 'Your password has been changed!';
                    $this->data['message'] = 'You will click <a href="'.site_url().'">back</a> go to our login page.';
                    $this->data['main'] = 'partial/something_wrong';
                    $this->render_page('template_beforelogin');
                }else{
                    if($this->input->post('format') == 'json'){
                        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                        exit();
                    }
                }

            }

            $this->data['main'] = 'reset_password_form';
            $this->render_page('template_beforelogin');
        }else{
            $this->data['topic_message'] = 'The link has already been used.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->render_page('template_beforelogin');
        }
    }

    public function checksession(){
        if($this->session->userdata('user_id')){
            echo json_encode(array("status" => "login"));
        }else{
            echo json_encode(array("status" => "logout"));
        }
    }

}
