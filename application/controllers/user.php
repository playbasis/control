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
        $this->load->model('Player_model');
        $this->load->model('Client_model');
        $this->load->model('CMS_model');
        $this->load->model('App_model');
        $this->load->model('Plan_model');
//        $this->load->model('Domain_model');
        $this->load->model('Merchant_model');
        $this->load->model('Goods_model');
        $this->load->model('User_group_model');
        $this->load->model('User_group_to_client_model');
        $this->load->model('Setting_model');
        $this->load->model('Sms_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("user", $lang['folder']);
        $this->lang->load("login", $lang['folder']);
    }

    public function index()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);
    }

    public function getList($offset)
    {

        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');
        $config['base_url'] = site_url('user/page');
        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        if ($client_id) {
            $config['total_rows'] = $this->User_model->getTotalUserByClientId(array('client_id' => $client_id));
        } else {
            $config['total_rows'] = $this->User_model->getTotalNumUsers();
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
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        if ($client_id) {

            $filter['client_id'] = $client_id;
            $user_ids = $this->User_model->getUserByClientId($filter);

            $UsersInfoForClientId = array();

            $userGroups = $this->User_group_to_client_model->fetchAllUserGroups($client_id);
            foreach ($user_ids as $user_id) {
                $UsersInfo = $this->User_model->getUserInfo($user_id['user_id']);
                $UsersInfo['user_group'] = '';
                if(isset($UsersInfo['user_group_id']) && $UsersInfo['user_group_id']){
                    foreach ($userGroups as $userGroup){
                        if($userGroup['_id'] == $UsersInfo['user_group_id']){
                            $UsersInfo['user_group'] = $userGroup['name'];
                            break;
                        }
                    }
                }
                $UsersInfoForClientId[]= $UsersInfo;
            }

            $this->data['users'] = $UsersInfoForClientId;

        } else {
            $this->data['users'] = $this->User_model->fetchAllUsers($filter);
        }

        $this->data['main'] = 'user';
        $this->render_page('template');
    }

    public function update($user_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/update/' . $user_id;

        //Rules need to be set

        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'),
            'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        if ($this->input->post('password') != '') {
            $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                'trim|max_length[255]|xss_clean|check_space|min_length[5]');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
                'required|matches[password]');
        }
        $this->form_validation->set_rules('user_group', "", '');
        $this->form_validation->set_rules('status', "", '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('user/update/' . $user_id, 'refresh');
            }

            //Check to see if it passes the form validation

            if ($this->form_validation->run()) {
                $data = array();
                $data['email'] = $this->input->post('email');
                if (!$this->User_model->findEmail($data)) {
                    $this->User_model->editUser($user_id, $this->input->post());

                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/user', 'refresh');
                } else {
                    $user = $this->User_model->getUserInfo($user_id);
                    if ($user['username'] != $data['email']) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail'));
                        redirect('/user/update/' . $user_id);
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
    public function insert()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user/insert/';

        //Rules need to be set
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'),
            'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'),
            'trim|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
            'matches[password]');
        $this->form_validation->set_rules('user_group', "", '');
        $this->form_validation->set_rules('status', "", '');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('user/insert', 'refresh');
            }

            $client_id = $this->User_model->getClientId();
            $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

            if ($this->form_validation->run()) {
                // get Plan limit_others.user
                $user_limit = null;
                try {
                    $user_limit = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "user");
                } catch (Exception $e) {
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

                if ($user_id) {
                    if ($client_id) {
                        $data = array(
                            'client_id' => $client_id,
                            'user_id' => $user_id
                        );
                        $this->User_model->addUserToClient($data);
                    }

                    $this->session->data['success'] = $this->lang->line('text_success');

                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('user/', 'refresh');
                } else {
                    $this->session->set_flashdata('fail', $this->lang->line('text_fail'));
                    redirect('user/insert');
                }
            }
        }

        $this->getForm();

    }

    public function insert_ajax()
    {

        //Rules need to be set
        // $this->form_validation->set_rules('username', $this->lang->line('form_username'), 'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
        $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('form_email'),
            'trim|valid_email|xss_clean|required|check_space');
        $this->form_validation->set_rules('password', $this->lang->line('form_password'),
            'trim|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'),
            'matches[password]');
        //$this->form_validation->set_rules('user_group', $this->lang->line('form_user_group'), 'required');

        $json = array();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->data['message'] = null;

            /*if($this->checkLimitUser($this->input->post('client_id'))){
                $this->data['message'] = $this->lang->line('error_limit');
                $json['error'] = $this->data['message'];
            }*/

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $email = $this->input->post('email');
                $data['email'] = $email;
                $check_email = $this->User_model->findEmail($data);

                if (!$check_email) {
                    $user_id = $this->User_model->insertUser();

                    if ($user_id) {
                        $data = array(
                            'client_id' => $this->input->post('client_id'),
                            'user_id' => $user_id
                        );
                        $this->User_model->addUserToClient($data);
                    }

                    $this->session->data['success'] = $this->lang->line('text_success');
                    $json['success'] = $this->lang->line('text_success');
                } else {
                    $json['error'] = 'The Email provided already exists';
                }

            } else {
                $json['error'] = validation_errors('<div class="warning">', '</div>');
            }
        }

        $this->output->set_output(json_encode($json));
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }


        if ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            foreach ($selectedUsers as $selectedUser) {
                $this->User_model->deleteUser($selectedUser);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/user', 'refresh');
        }

        $this->getList(0);
    }

    public function delete_ajax()
    {

        $json = array();
        $this->error['warning'] = null;

        if ($this->input->post('user_id') && $this->error['warning'] == null) {

            if ($this->checkOwnerUser($this->input->post('user_id'))) {

                $this->User_model->deleteUser($this->input->post('user_id'));
            }

            $this->session->data['success'] = $this->lang->line('text_success_delete');

            $json['success'] = $this->lang->line('text_success_delete');
        }

        $this->output->set_output(json_encode($json));
    }

    public function getForm($user_id = 0)
    {

        if ((isset($user_id)) && $user_id != 0) {
            $user_info = $this->User_model->getUserInfo($user_id);
        }

        if (isset($user_info)) {
            $this->data['user'] = $user_info;
        }

        if (!$user_id) {
            if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                $this->data['is_admin_groups'] = true;
                $this->data['user_groups'] = $this->User_model->getUserGroups();
            } else {
                $client_id = $this->User_model->getClientId();
                $this->data['user_groups'] = $this->User_group_to_client_model->fetchAllUserGroups($client_id);
            }
        } else {
            $user_info = $this->User_model->getUserInfo($user_id);
            if ($user_info['user_group_id'] == $this->User_model->getAdminGroupID()) {
                $this->data['is_admin_groups'] = true;
                $this->data['user_groups'] = $this->User_model->getUserGroups();
            } else {
                $client_id = $this->User_model->getClientIdByUserId(new MongoId($user_id));
                $this->data['user_groups'] = $this->User_group_to_client_model->fetchAllUserGroups($client_id);
            }
        }

        $this->data['main'] = 'user_form';
        $this->render_page('template');

    }

    public function autocomplete()
    {
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

            if ($client_id) {

                $data['client_id'] = $client_id;
                $user_ids = $this->User_model->getUserByClientId($data);

                $UsersInfoForClientId = array();
                foreach ($user_ids as $user_id) {
                    $user_info = $this->User_model->getUserInfo($user_id['user_id']);
                    if (preg_match('/' . $filter_name . '/', $user_info['username'])) {
                        $UsersInfoForClientId[] = $user_info;
                    }
                }

                $results_user = $UsersInfoForClientId;


            } else {
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

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'user')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'user') && $this->Feature_model->getFeatureExistByClientId($client_id, 'user')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerUser($user_id)
    {

        $error = null;

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

            $users = $this->User_model->getUserByClientId($this->User_model->getClientId());

            $has = false;

            foreach ($users as $user) {
                if ($user['user_id'] . "" == $user_id . "") {
                    $has = true;
                }
            }

            if (!$has) {
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    private function checkLimitUser($client_id)
    {

        $data['client_id'] = $client_id;
        $users = $this->User_model->getTotalUserByClientId($data);

        if ($users > 10) {
            return true;
        } else {
            return false;
        }
    }

    public function block()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['main'] = 'block';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function jcryption()
    {
        require_once(APPPATH . '/libraries/jcryption/sqAES.php');
        require_once(APPPATH . '/libraries/jcryption/JCryption.php');
        $jc_obj = new JCryption(APPPATH.'third_party/keys/rsa_1024_pub.pem', APPPATH.'third_party/keys/rsa_1024_priv.pem');
        $jc_obj->go();

    }

    public function login()
    {
        if ($this->session->userdata('user_id')) {
            redirect('/', 'refresh');
        }

        if ($this->input->get('back')) {
            $this->session->set_userdata('redirect', $this->input->get('back'));
        }

        $this->form_validation->set_rules('username', $this->lang->line('entry_username'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        // $this->form_validation->set_rules('email', $this->lang->line('form_email'), 'trim|valid_email|xss_clean|required|cehck_space');
        $this->form_validation->set_rules('password', $this->lang->line('entry_password'), 'trim|required');

        $lang = get_lang($this->session, $this->config);

        $this->lang->load('login', $lang['folder']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once(APPPATH . '/libraries/jcryption/sqAES.php');
            require_once(APPPATH . '/libraries/jcryption/JCryption.php');
            JCryption::decrypt();
            $this->data['message'] = null;
            if ($this->form_validation->run()) {
                $this->load->model('User_model');
                $u = $this->input->post('username');
                $pw = $this->input->post('password');

                $is_lock = false;
                $this->User_model->login($u, $pw, $is_lock);
                $this->session->regenerate_id();

                if ($this->session->userdata('user_id')) {

                    if ($this->session->userdata('redirect')) {
                        $redirect = $this->session->userdata('redirect');
                        $this->session->unset_userdata('redirect');
                    } else {
                        $redirect = '/';
                    }
                    if ($_REQUEST['format'] == 'json') {
                        echo json_encode(array('status' => 'success', 'message' => ''));
                        exit();
                    }
                    redirect($redirect, 'refresh');
                }

                if($is_lock){
                    $msg_alert = $this->lang->line('error_account_locked');
                }else{
                    $msg_alert = $this->lang->line('error_login');
                }
                if ($_REQUEST['format'] == 'json') {
                    echo json_encode(array('status' => 'error', 'message' => $msg_alert));
                    exit();
                }
                $this->data['message'] = $msg_alert;
            } else {
                if ($_REQUEST['format'] == 'json') {
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

    public function logout()
    {
        $this->load->model('User_model');
        $this->User_model->logout();
        $this->input->set_cookie("client_id", null);
        $this->input->set_cookie("site_id", null);

        redirect('/', 'refresh');
    }

    public function register()
    {

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
        redirect('login' . ($this->input->get('plan') ? '?plan=' . $this->input->get('plan') : '') . '#register',
            'refresh');
    }

    /* new register flow (without captcha, plan and domain) */
    public function regis()
    {
        if (ALLOW_SIGN_UP) {
            $success = false;

            //Set rules for form registration
            $this->form_validation->set_rules('email', $this->lang->line('form_email'),
                'trim|valid_email|xss_clean|required|cehck_space');
            $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'),
                'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'),
                'trim|required|min_length[3]|max_length[40]|xss_clean');

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $_POST['password'] = DEFAULT_PASSWORD;
                $_POST['password_confirm'] = DEFAULT_PASSWORD;
                $plan_id = $this->input->get('plan');
                if (empty($plan_id)) {
                    $plan_id = FREE_PLAN;
                } // default is free plan
                $plan = $this->Plan_model->getPlanById(new MongoId($plan_id));

                if ($this->form_validation->run()) {

                    if ($user_id = $this->User_model->insertUser()) { // [1] firstly insert a user into "user"
                        $user_info = $this->User_model->getUserInfo($user_id);

                        $client_id = $this->Client_model->insertClient($this->input->post(),
                            $plan); // [2] then insert a new client into "playbasis_client"

                        $data = $this->input->post();
                        $data['client_id'] = $client_id;
                        $data['user_id'] = $user_info['_id'];
                        $this->User_model->addUserToClient($data); // [3] map the user to the client in "user_to_client"

                        $this->Client_model->addPlanToPermission(array( // [5] bind the client to the selected plan "playbasis_permission"
                            'client_id' => $client_id->{'$id'},
                            'plan_id' => $plan['_id']->{'$id'},
                            'site_id' => null,
                        ));

                        $success = true;
                        $message = $this->lang->line('text_email_sent');
                    } else {
                        $message = $this->lang->line('text_fail');
                    }
                } else {
                    $message = strip_tags(validation_errors());
                }
            } else {
                $message = "Unsupported HTTP method";
            }

            $res = array("response" => $success ? "success" : "fail", "message" => $message);
            if ($success) {
                $res = array_merge($res, array("data" => $user_id . ""));
            }
            echo json_encode($res);
            exit();
        }
        $res = array("response" => "fail", "message" => "Registration is closed!");
        echo json_encode($res);
    }

    public function signup_finish()
    {
        $user_id = $this->input->get('i');

        $user_info = $this->User_model->getUserInfo(new MongoId($user_id));

        $this->data['user_before_info'] = $user_info;
        $this->data['url_resend'] = site_url('user/resend_signup_email?i=' . $user_id . "");
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'account_activated';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');

        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function resend_signup_email()
    {
        $user_id = $this->input->get('i');

        $user_info = $this->User_model->getUserInfo(new MongoId($user_id));

        $this->load->library('parser');
        $this->load->library('email');
        $vars = array(
            'firstname' => $user_info['firstname'],
            'lastname' => $user_info['lastname'],
            'username' => $user_info['username'],
            'key' => $user_info['random_key'],
            'url' => site_url('enable_user?key='),
            'base_url' => site_url()
        );

        $htmlMessage = $this->parser->parse('emails/user_activated.html', $vars, true);

        $this->email($user_info['email'], '[Playbasis] Please activate your account', $htmlMessage);

        redirect('user/signup_finish?i=' . $user_id, 'refresh');
    }

    public function list_pending_users()
    {
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

    public function enable_users()
    {
        $this->load->library('parser');
        $this->error['warning'] = null;
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            if ($this->input->post('selected') && $this->error['warning'] == null) {
                foreach ($this->input->post('selected') as $user_id) {
                    $initial_password = get_random_password(8, 8);
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

    public function set_user_password()
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'set user password';
        $this->data['title'] = $this->lang->line('title');

        if (isset($_GET['key'])) {
            $random_key = $_GET['key'];
            $user = $this->User_model->checkRandomKey($random_key, false);
            if ($user != null) {
                redirect('user/update_password?random_key='.$random_key, 'refresh');
            } else {
                $this->data['topic_message'] = 'Your validation key is invalid,';
                $this->data['message'] = 'Please contact Playbasis.';
                $this->data['main'] = 'partial/something_wrong';
                $this->render_page('template_beforelogin');
            }
        } else {
            $this->data['topic_message'] = 'Your validation key is invalid,';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->render_page('template_beforelogin');
        }
    }
    public function update_password()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                'trim|required|min_length[5]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
                'required|matches[password]');

            if ($this->form_validation->run()) {
                $new_password = $this->input->post('password');
                $random_key = $this->input->post('random_key');
                $user = $this->User_model->checkRandomKey($random_key);
                $user_id = $user[0]['_id'];
                $this->User_model->insertNewPassword($user_id, $new_password);
                $this->User_model->logout();

                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'success', 'message' => 'Your password has been update!'));
                    exit();
                }

                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id
                );

                if ($client_id) {
                    $total = $this->App_model->getTotalAppsByClientId($data);
                } else {
                    $total = $this->App_model->getTotalApps($data);
                }

                if ($total == 0) {
                    $this->session->unset_userdata('site_id');
                    redirect('/first_app', 'refresh');
                } else {
                    redirect('/', 'refresh');
                }

            } else {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    exit();
                }
            }
        }

        $this->data['heading_title'] = $this->lang->line('add_site_title');
        $this->data['main'] = 'partial/completeprofile_partial';
        $this->data['form'] = 'user/update_password';
        $this->data['random_key'] = $this->input->get('random_key');
        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function enable_user()
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'enable user';
        $this->data['title'] = $this->lang->line('title');

        if (isset($_GET['key'])) {
            $random_key = $_GET['key'];
            $user = $this->User_model->checkRandomKey($random_key);
            if ($user != null) {
                $user_id = $user[0]['_id'];
                $this->User_model->force_login($user_id);

                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id
                );

                if ($client_id) {
                    $total = $this->App_model->getTotalAppsByClientId($data);
                } else {
                    $total = $this->App_model->getTotalApps($data);
                }

                if ($total == 0) {
                    $this->session->unset_userdata('site_id');
                    redirect('/first_app', 'refresh');
                } else {
                    redirect('/', 'refresh');
                }

            } else {
                $this->data['topic_message'] = 'Your validation key is invalid,';
                $this->data['message'] = 'Please contact Playbasis.';
                $this->data['main'] = 'partial/something_wrong';
                $this->render_page('template_beforelogin');
            }
        } else {
            $this->data['topic_message'] = 'Your validation key is invalid,';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->render_page('template_beforelogin');
        }
    }

    public function referral($code = '')
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['form'] = 'referral/' . $code;
        $this->data['title'] = $this->lang->line('title');

        if (!$code) {
            $this->data['topic_message'] = 'Referral code is required to access this page';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }

        $player = $this->Player_model->getPlayerByCode($code);
        if (!$player) {
            $this->data['topic_message'] = 'Your referral code is invalid.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }
        $this->data['by'] = $player;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();
            $this->_api = $this->playbasisapi;
            $platforms = $this->App_model->getPlatFormByAppId(array(
                'site_id' => $player['site_id'],
            ));
            $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
            if (!$platform) {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'fail', 'message' => 'Cannot find any active platform'));
                    exit();
                }
            }
            $this->_api->set_api_key($platform['api_key']);
            $this->_api->set_api_secret($platform['api_secret']);
            $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
            $this->_api->auth($pkg_name);
            $status = $this->_api->register($data['username'], $data['username'], $data['email'], array(
                'first_name' => $data['firstname'],
                'last_name' => $data['lastname'],
                'code' => $code,
            ));
            $error = $status && isset($status->success) && $status->success ? null : (isset($status->message) ? $status->message : 'Unknown reason');
            if ($this->input->post('format') == 'json') {
                echo json_encode(array(
                    'status' => !$error ? 'success' : 'fail',
                    'message' => !$error ? 'Your registration has been saved!' : $error
                ));
                exit();
            }
        }

        if ($player) {
            $app = $this->App_model->getApp($player['site_id']);
            $this->data['app_name'] = $app['site_name'];
            $this->data['thumb'] = "";
            if (isset($app['image']) && !empty($app['image'])) {
                $info = pathinfo($app['image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($app['image'], 0,
                            utf8_strrpos($app['image'], '.')) . '-80x80.' . $extension;
                    $this->data['thumb'] = S3_IMAGE . $new_image;
                }
            }
            $this->data['site_color'] =  (isset($site_info['app_color']) && !empty($site_info['app_color'])) ? $site_info['app_color'] : "#86559c";
            $this->data['referral_code'] = $code;
        }
        $this->data['main'] = 'partial/referral_partial';
        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function merchant()
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['form'] = 'merchant_verify';
        $this->data['title'] = $this->lang->line('title');

        $pin = $this->session->userdata('pin');
        if (!$pin) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = $this->input->post();
                $pin = isset($data['pin']) ? $data['pin'] : null;
                $error = $pin ? 'Your merchant PIN is invalid' : 'PIN is required';
                if ($pin && $this->Merchant_model->isValidPin($pin)) {
                    $error = false;
                    $this->session->set_userdata(array('pin' => $pin));
                }
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array(
                        'status' => !$error ? 'success' : 'fail',
                        'message' => !$error ? 'You successfully log in to merchant page!' : $error,
                        'login' => true
                    ));
                    exit();
                }
            }
            $this->data['main'] = 'partial/merchant_login';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }

        $branch = $this->Merchant_model->getBranchByPin($pin);
        if (!$branch) {
            $this->data['topic_message'] = 'Cannot find branch corresponding to the given PIN code.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }
        $branch_id = $branch['_id'];
        $client_id = $branch['client_id'];
        $site_id = $branch['site_id'];
        $merchant = $this->Merchant_model->findMerchantByBranchId($branch_id);
        $group_list = array_map('user_index_goods_group', $this->Merchant_model->findGoodsByBranchId($branch_id));
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $this->input->post();
            $group = isset($data['group']) ? $data['group'] : null;
            $coupon = isset($data['coupon']) ? $data['coupon'] : null;
            $mark = isset($data['mark']) && $data['mark'];
            if (!$group) {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'fail', 'message' => 'Goods group is required'));
                    exit();
                }
            }
            if (!$coupon) {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'fail', 'message' => 'Coupon code is required'));
                    exit();
                }
            }

            $goods_list = array_map('user_index_goods_id',
                $this->Goods_model->listGoodsByGroupAndCode($group, $coupon, array('goods_id')));
            if (!$goods_list) {
                if ($this->input->post('format') == 'json') {
                    /* invalid = FAIL */
                    echo json_encode(array(
                        'status' => 'fail',
                        'message' => 'Such coupon code cannot be found for the selected goods'
                    ));
                    exit();
                }
            }
            $redeemed_goods_list = $this->Goods_model->listRedeemedGoods($goods_list,
                array('goods_id', 'cl_player_id', 'pb_player_id'));
            $goods_list_redeemed = array_map('user_index_goods_id', $redeemed_goods_list);
            $verified_goods_list = $this->Goods_model->listVerifiedGoods($goods_list,
                array('goods_id', 'branch', 'date_added'));
            $goods_list_verified = array_map('user_index_goods_id', $verified_goods_list);
            $goods_list_ok = array_diff($goods_list_redeemed,
                $goods_list_verified); // coupon is redeemed but not yet exercised (found record in "playbasis_goods_to_player", not "playbasis_merchant_goodsgroup_redeem_log")
            if ($goods_list_ok) {
                if ($mark) {
                    $goods_id = $goods_list_ok[0];
                    $gp = $this->findGoodsToPlayerByGoodsId($goods_id, $redeemed_goods_list);
                    $this->Goods_model->markAsVerifiedGoods(array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'goods_id' => $goods_id,
                        'goods_group' => $group,
                        'cl_player_id' => $gp['cl_player_id'],
                        'pb_player_id' => $gp['pb_player_id'],
                        'branch' => array(
                            'b_id' => $branch_id,
                            'b_name' => $branch['branch_name'],
                        ),
                    ));
                }
                if ($this->input->post('format') == 'json') {
                    /* valid, redeemed, NOT used = SUCCESS */
                    echo json_encode(array('status' => 'success', 'message' => 'Coupon is valid'));
                    exit();
                }
            } else {
                if ($goods_list_verified) {
                    if ($this->input->post('format') == 'json') {
                        /* valid, redeemed, used = FAIL */
                        $verified_goods_list = $verified_goods_list[0];
                        echo json_encode(array(
                            'status' => 'fail',
                            'message' => 'Coupon is invalid as it has been used already',
                            'at' => $verified_goods_list['branch']['b_name'],
                            'when' => $this->datetimeMongotoReadable($verified_goods_list['date_added'])
                        ));
                        exit();
                    }
                } else {
                    if ($this->input->post('format') == 'json') {
                        /* valid, NOT redeemed = FAIL */
                        echo json_encode(array(
                            'status' => 'fail',
                            'message' => 'Coupon is invalid as it is not yet redeemed'
                        ));
                        exit();
                    }
                }
            }
        }
        $this->data['merchant'] = $merchant['name'];
        $this->data['branch'] = $branch['branch_name'];
        $this->data['group_list'] = $group_list;
        $this->data['main'] = 'partial/merchant';
        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function merchant_logout()
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['form'] = 'merchant_verify';
        $this->data['title'] = $this->lang->line('title');

        $this->session->unset_userdata('pin');

        if ($this->input->post('format') == 'json') {
            echo json_encode(array('status' => 'success', 'message' => 'You successfully log out of merchant page!'));
            exit();
        }

        $this->data['main'] = 'partial/merchant_login';
        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function player_reset_password($code = '')
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = 'Reset Password';

        if (!$code) {
            // TODO(Rook): This should render form for user to reset password manually by input username/email and reset code then redirect to change pwd form page

            $this->data['topic_message'] = 'Password reset code is required to access this page';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }

        $player = $this->Player_model->getPlayerByPasswordResetCode($code);
        if (!$player) {
            $this->data['topic_message'] = 'Your password reset code is invalid.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }

        $sess_data = array(
            'player' => $player
        );
        $this->session->set_userdata($sess_data);

        if ($this->session->userdata('player')) {


            $player_info = $this->Player_model->getPlayerById($player['pb_player_id']);
            $inhibited_str = $player_info['username'];
            $setting = $this->Setting_model->retrieveSetting($player_info);
            if (isset($setting['password_policy'])) {
                $password_policy = $setting['password_policy'];
                $rule = 'trim|required|xss_clean|check_space|max_length[40]';
                if ($password_policy['min_char'] && $password_policy['min_char'] > 0) {
                    $rule = $rule . '|' . 'min_length[' . $password_policy['min_char'] . ']';
                    $this->data['min_length'] = $password_policy['min_char'];
                }
                if ($password_policy['alphabet'] && $password_policy['numeric']) {
                    $rule = $rule . '|callback_require_at_least_number_and_alphabet';
                } elseif ($password_policy['alphabet']) {
                    $rule = $rule . '|callback_require_at_least_alphabet';
                } elseif ($password_policy['numeric']) {
                    $rule = $rule . '|callback_require_at_least_number';
                }

                if ($password_policy['user_in_password'] && ($inhibited_str != '')) {
                    $rule = $rule . '|callback_word_in_password[' . $inhibited_str . ']';
                }
                $this->form_validation->set_rules('password', $this->lang->line('form_password'), $rule);

            } else {
                $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                    'trim|required|min_length[8]|max_length[40]|xss_clean|check_space');
            }

            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
                'required|matches[password]');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->form_validation->run()) {
                    $new_password = $this->input->post('password');

                    $this->Player_model->verifyEmailByPlayerId($player['pb_player_id']);
                    $this->Player_model->setPlayerPasswordByPlayerId($player['pb_player_id'], $new_password);
                    $this->Player_model->deletePasswordResetCode($code);
                    $this->session->unset_userdata('user');

                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'You password has been changed. You can login again with new password.'
                        ));
                        exit();
                    }

                    $this->data['topic_message'] = 'Your password has been changed!';
                    $this->data['message'] = 'You password has been changed. You can login again with new password.';
                    $this->data['main'] = 'partial/something_wrong';
                    $this->render_page('template_beforelogin');
                } else {
                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                        exit();
                    }
                }
            }

            if ($player) {
                $player_info = $this->Player_model->getPlayerById($player['pb_player_id']);
                if($player_info){
                    $site_info = $this->App_model->getApp($player_info['site_id']);
                    $this->data['thumb'] = "";
                    if (isset($site_info['image']) && !empty($site_info['image'])) {
                        $info = pathinfo($site_info['image']);
                        if (isset($info['extension'])) {
                            $extension = $info['extension'];
                            $new_image = 'cache/' . utf8_substr($site_info['image'], 0,
                                    utf8_strrpos($site_info['image'], '.')) . '-80x80.' . $extension;
                            $this->data['thumb'] = S3_IMAGE . $new_image;
                        }
                    }
                    $this->data['site_color'] =  (isset($site_info['app_color']) && !empty($site_info['app_color'])) ? $site_info['app_color'] : "#86559c";
                }
                $this->data['player_info'] = $player_info;
                $this->data['password_recovery_code'] = $code;
            }
            $this->data['main'] = 'partial/playerresetpassword_partial';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
        }
    }

    public function player_reset_password_complete()
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = 'Reset Password';
        $this->data['topic_message'] = 'Completed Reset Password';
        $this->data['message'] = 'You password has been changed. You can now login again with new password.';
        $this->data['main'] = 'partial/something_wrong';
        $this->data['thumb'] = "";
        if($this->session->userdata('site_id')){
            $site_info = $this->App_model->getApp($this->session->userdata('site_id'));
            if (isset($site_info['image']) && !empty($site_info['image'])) {
                $info = pathinfo($site_info['image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($site_info['image'], 0,
                            utf8_strrpos($site_info['image'], '.')) . '-80x80.' . $extension;
                    $this->data['thumb'] = S3_IMAGE . $new_image;
                }
            }
            $this->data['site_color'] =  (isset($site_info['app_color']) && !empty($site_info['app_color'])) ? $site_info['app_color'] : "#86559c";
        }


        $this->load->vars($this->data);
        $this->render_page('template_beforelogin');
    }

    public function player_verify_email($code = '')
    {
        $this->load->library('parser');
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = 'Verify Email';

        if (!$code) {
            // TODO(Rook): This should render form for user to reset password manually by input username/email and reset code then redirect to change pwd form page

            $this->data['topic_message'] = 'Email verification code is required to access this page';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }

        $player = $this->Player_model->getPlayerByEmailVerifyCode($code);
        if (!$player) {
            $this->data['topic_message'] = 'Your email verification code is invalid.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }else{
            $this->Player_model->verifyEmailByPlayerId($player['pb_player_id']);
            $this->Player_model->deleteEmailVerifyCode($code);
            $player_info = $this->Player_model->getPlayerById($player['pb_player_id']);
            $this->data['thumb'] = "";
            if($player_info){
                $site_info = $this->App_model->getApp($player_info['site_id']);
                if (isset($site_info['image']) && !empty($site_info['image'])) {
                    $info = pathinfo($site_info['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($site_info['image'], 0,
                                utf8_strrpos($site_info['image'], '.')) . '-80x80.' . $extension;
                        $this->data['thumb'] = S3_IMAGE . $new_image;
                    }
                }
                $this->data['site_color'] =  (isset($site_info['app_color']) && !empty($site_info['app_color'])) ? $site_info['app_color'] : "#86559c";
            }
            $this->data['topic_message'] = 'Thanks. You have completed to verify your email.';
            $this->data['message'] = 'You email address has been verified. Let\'s have fun .';
            $this->data['main'] = 'partial/something_wrong';
            $this->load->vars($this->data);
            $this->render_page('template_beforelogin');
            return;
        }
    }

    private function findGoodsToPlayerByGoodsId($goods_id, $goods_list)
    {
        foreach ($goods_list as $goods) {
            if ($goods['goods_id'] == $goods_id) {
                return $goods;
            }
        }
        throw new Exception('Cannot find goods record given goods_id: ' . $goods_id);
    }

    private function email($to, $subject, $message)
    {
        $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');
        $this->amazon_ses->to($to);
        $this->amazon_ses->bcc(array(EMAIL_FROM));
        $this->amazon_ses->subject($subject);
        $this->amazon_ses->message($message);
        $this->amazon_ses->send();
    }

    public function edit_account()
    {
        if ($this->session->userdata('user_id')) {

            $this->data['message'] = null;
            $user_id = $this->session->userdata('user_id');

            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['title'] = $this->lang->line('text_edit_account');
            $this->data['form'] = 'user/edit_account';

            $client_id = $this->User_model->getClientId();
            $userGroups = $this->User_group_to_client_model->fetchAllUserGroups($client_id);
            $UsersInfo = $this->User_model->getUserInfo($user_id);
            $UsersInfo['user_group'] = '';
            if(isset($UsersInfo['user_group_id']) && $UsersInfo['user_group_id']){
                foreach ($userGroups as $userGroup){
                    if($userGroup['_id'] == $UsersInfo['user_group_id']){
                        $UsersInfo['user_group'] = $userGroup['name'];
                        break;
                    }
                }
            }
            $UsersInfo['phone_number'] = isset($UsersInfo['phone_number']) && $UsersInfo['phone_number'] ? $UsersInfo['phone_number'] : null;
            $UsersInfo['phone_status'] = isset($UsersInfo['phone_status']) && $UsersInfo['phone_status'] ? true : false;
            $this->data['user_info'] = $UsersInfo;

            if ($this->input->post('image')) {
                $this->data['image'] = $this->input->post('image');
            } elseif (!empty($this->data['user_info']) && isset($this->data['user_info']['image'])) {
                $this->data['image'] = $this->data['user_info']['image'];
            } else {
                $this->data['image'] = 'no_image.jpg';
            }

            if ($this->data['image']) {
                $info = pathinfo($this->data['image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($this->data['image'], 0,
                            utf8_strrpos($this->data['image'], '.')) . '-100x100.' . $extension;
                    $this->data['thumb'] = S3_IMAGE . $new_image;
                } else {
                    $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                }
            } else {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }

            $this->form_validation->set_rules('firstname', $this->lang->line('form_firstname'),
                'trim|required|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('lastname', $this->lang->line('form_lastname'),
                'trim|required|min_length[3]|max_length[40]|xss_clean');
            $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                'trim|min_length[3]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('form_confirm_password'),
                'matches[password]');

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = array(
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'password' => $this->input->post('password'),
                    'confirm_password' => $this->input->post('password_confirm'),
                    'edit_account' => true,
                );
                if ($this->input->post('image') != "no_image.jpg") {
                    $data['image'] = $this->input->post('image');
                }
                if ($this->input->post('image') == '') {
                    $data['image'] = '';
                }
                if ($this->form_validation->run()) {
                    if ($this->User_model->editUser($user_id, $data)) {
                        $this->data['success'] = $this->lang->line('text_success_update');
                    }
                }
            }

            $this->data['main'] = 'edit_account';
            $this->render_page('template');
        }
    }

    public function forgot_password()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'register';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_forgot_password'] = $this->lang->line('heading_forgot_password');
        $this->data['form'] = 'user/forgot_password';

        $this->form_validation->set_rules('email', $this->lang->line('form_email'),
            'trim|valid_email|xss_clean|required|check_space');

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->form_validation->run() && $this->data['message'] == null) {
                $check_email = $this->User_model->findEmail($this->input->post());

                if ($check_email) {
                    $random_key = get_random_password(8, 8);
                    $this->User_model->insertRandomPasswordKey($random_key, $check_email[0]['_id']);
                    $email = $check_email[0]['email'];

                    $this->load->library('email');
                    $this->load->library('parser');

                    $data = array(
                        'url' => site_url('reset_password?key=' . $random_key),
                        'base_url' => site_url()
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

                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'A link has been sent to your email, please click on it and change your password.'
                        ));
                        exit();
                    }

                    $this->data['topic_message'] = 'A link has been sent to your email,';
                    $this->data['message'] = 'Please click on it and change your password.';
                    $this->data['main'] = 'partial/something_wrong';
                    $this->render_page('template_beforelogin');
                } else {
                    // echo "<script>alert('The email was not found in our server, please make sure you have typed it correctly.');</script>";
                    // $this->data['message'] = $this->lang->line('error_no_email');
                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_no_email')));
                        exit();
                    }
//                    $this->session->set_flashdata('fail', $this->lang->line('error_no_email'));
//                    redirect('forgot_password', 'refresh');
                    redirect('login#forgotpassword', 'refresh');
                }
            } else {
                if ($this->input->post('format') == 'json') {
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    exit();
                }
            }


        }
//        $this->data['main'] = 'forgot_password';
//        $this->render_page('template');

        redirect('login#forgotpassword', 'refresh');
    }

    public function reset_password()
    {
        if (isset($_GET['key'])) {
            $random_key = $_GET['key'];
            $user = $this->User_model->checkRandomPasswordKey($random_key);
            $data = array(
                'user' => $user
            );
            $this->session->set_userdata($data);
        }

        if ($this->session->userdata('user')) {

            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['main'] = 'register';
            $this->data['title'] = $this->lang->line('title');
            $this->data['heading_forgot_password'] = $this->lang->line('heading_forgot_password');
            $this->data['form'] = 'user/reset_password';

            $this->form_validation->set_rules('password', $this->lang->line('form_password'),
                'trim|required|min_length[5]|max_length[40]|xss_clean|check_space');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('form_confirm_password'),
                'required|matches[password]');

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if ($this->form_validation->run()) {
                    $new_password = $this->input->post('password');
//                    $user_id = $this->session->userdata('user')[0]['_id'];
                    $user_id = $this->session->userdata('user');
                    $this->User_model->insertNewPassword($user_id[0]['_id'], $new_password);
                    $this->session->unset_userdata('user');

                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'Your password has been changed! We will redirect you to our login page.'
                        ));
                        exit();
                    }

                    $this->data['topic_message'] = 'Your password has been changed!';
                    $this->data['message'] = 'You will click <a href="' . site_url() . '">back</a> go to our login page.';
                    $this->data['main'] = 'partial/something_wrong';
                    $this->render_page('template_beforelogin');
                } else {
                    if ($this->input->post('format') == 'json') {
                        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                        exit();
                    }
                }

            }

            $this->data['main'] = 'reset_password_form';
            $this->render_page('template_beforelogin');
        } else {
            $this->data['topic_message'] = 'The link has already been used.';
            $this->data['message'] = 'Please contact Playbasis.';
            $this->data['main'] = 'partial/something_wrong';
            $this->render_page('template_beforelogin');
        }
    }

    public function cms_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $site_slug = $this->input->post('site_slug');
        $result = $this->User_model->cms_login($username, $password);
        if (isset($result)) {
            $user = $this->User_model->getUserInfo($result);
            $client = $this->User_model->getClientIdByUserId($user['_id']);
            $cms = $this->CMS_model->getCmsBySiteSlug($site_slug);
            $site_slug = $client == $cms['client_id'] ? $site_slug : false;

            $userGroup = $this->User_group_model->getUserGroupInfo($user['user_group_id']);
            $permission = $userGroup['permission'];
            $modify = $permission['modify'];

            $editor = array_search('cms', $modify) != -1 ? true : false;

            $role = $editor ? 'editor' : 'contributor';
            $response = array(
                'username' => $user['username'],
                'email' => $user['email'],
                'site_slug' => $site_slug,
                'role' => $role
            );
            echo json_encode(array('status' => 'success', 'message' => validation_errors(), 'response' => $response));
        } else {
            echo json_encode(array('status' => 'failed'));
        }
    }

    public function checksession()
    {
        if ($this->session->userdata('user_id')) {
            echo json_encode(array("status" => "login"));
        } else {
            echo json_encode(array("status" => "logout"));
        }
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    public function require_at_least_number_and_alphabet($str)
    {
        if (preg_match('#[0-9]#', $str) && preg_match('#[a-zA-Z]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_number_and_alphabet',
            'The %s field require at least one numeric character and one alphabet');
        return false;
    }

    public function require_at_least_number($str)
    {
        if (preg_match('#[0-9]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_number',
            'The %s field require at least one number');
        return false;
    }

    public function require_at_least_alphabet($str)
    {
        if (preg_match('#[a-zA-Z]#', $str)) {
            return true;
        }
        $this->form_validation->set_message('require_at_least_alphabet',
            'The %s field require at least one alphabet');
        return false;
    }

    public function word_in_password($str, $val)
    {
        if (strpos($str, $val) !== false) {
            $this->form_validation->set_message('word_in_password',
                'The %s field disallow to contain logon IDs');
            return false;
        }
        return true;
    }

    private function generateOTP($length){
        $selection = "1234567890";
        $OTP = "";
        for ($i = 0; $i < $length; $i++) {
            $OTP .= $selection[(rand() % strlen($selection))];
        }

        return $OTP;
    }
    
    public function requestOTP()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $user_id = $this->session->userdata('user_id');
        $phone_number = $this->input->post('phone_number');

        $check_phone_number = $this->User_model->checkPhoneNumber($phone_number);

        if(is_null($check_phone_number)) {
            $OTP_code = $this->generateOTP(6);

            // send SMS
            $this->config->load("twilio", true);
            $config = $this->Sms_model->getSMSClient($client_id, $site_id);
            $twilio = $this->config->item('twilio');
            $config['api_version'] = $twilio['api_version'];
            $this->load->library('twilio/twiliomini', $config);
            $from = "Playbasis";
            $to = $phone_number;
            $message = "Your OTP is " . $OTP_code . " to activate phone number " . $phone_number . "";

            $response = $this->twiliomini->sms($from, $to, $message);
            //$response = false;
            $this->Sms_model->log($client_id, $site_id, "admin", $from, $to, $message, $response);
            if ($response->IsError) {
            //if ($response) {
                echo json_encode(array('status' => 'fail', 'msg' => 'Error sending SMS, ' . $response->error_message));
            } else {
                $this->User_model->setupUserPhoneNumber($user_id, $phone_number, $OTP_code);
                echo json_encode(array('status' => 'success'));
            }
        }else{
            echo json_encode(array('status' => 'fail', 'msg' => 'This phone number is already used by other user'));
        }
    }

    public function verifyOTP()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $user_id = $this->session->userdata('user_id');
        $OTP_code = $this->input->post('otp_number');
        $user_info = $this->User_model->getUserInfo($user_id);

        if(isset($user_info['otp_code']) && ($OTP_code == $user_info['otp_code'])){
            $this->User_model->activateUserPhoneNumber($user_id);
            echo json_encode(array('status' => 'success'));
        }else{
            echo json_encode(array('status' => 'fail', 'msg' => 'The OTP is invalid'));
        }
        
    }
}

function user_index_goods_group($obj)
{
    return $obj['goods_group'];
}

function user_index_goods_id($obj)
{
    return $obj['goods_id'];
}
