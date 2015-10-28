<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class CMS extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('App_model');
        $this->load->model('Client_model');
        $this->load->model('Custompoints_model');
        $this->load->model('CMS_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("cms", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();


        $this->data['platform_data'] = array();
        $site_data = array();
        $points_data = array();
        $plan_widget = array();

        if($client_id){
            $list_site = $this->Client_model->getSitesByClientId($client_id);
            foreach($list_site as $site)
            {
                $lower_site_name = strtolower($site['site_name']);
                $site_name = str_replace("-", " ", $lower_site_name);
                $data = array(
                    "site_name" => $site_name,
                    "site_id" => $site_id
                );
            }


            $site_data = $this->App_model->getAppsBySiteId($site_id);
            $platform_data = $this->App_model->getPlatformWithType($site_id, 'web');

            //force use first web app platform
            $this->data['platform_data'] = $platform_data;

            $points_data = $this->Custompoints_model->getCustompoints(array('client_id' => $client_id, 'site_id' => $site_id));

            $this->load->model('Client_model');
            $this->load->model('Plan_model');
            //$plan_subscription = $this->Client_model->getPlanByClientId($client_id);
            // get Plan display
            //$plan_widget = $this->Plan_model-> getPlanDisplayWidget($plan_subscription["plan_id"]);
        }

        //$this->data['site_data'] = $site_data;
        $this->data['main'] = 'cms';
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
        $this->render_page('template');
    }
    public function getList($offset){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/page');
        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        if($client_id){
            $config['total_rows'] = $this->CMS_model->getTotalUserBySite($client_id,$site_id);
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
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        if($client_id){

            $filter['client_id'] = $client_id;
            $user_ids = $this->CMS_model->getUserByClientId($filter);

            $UsersInfoForClientId = array();
            foreach ($user_ids as $user_id){
                $UsersInfoForClientId[] = $this->User_model->getUserInfo($user_id['user_id']);
            }

            $this->data['users'] = $UsersInfoForClientId;

        }else{
            $this->data['users'] = $this->User_model->fetchAllUsers($filter);
        }

        //$this->data['main'] = 'user';
        $this->render_page('template');
    }

    public function preview(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $site_data = array();
        $points_data = array();

        if($client_id){
            $site_data = $this->App_model->getAppsBySiteId($site_id);
            $points_data = $this->Custompoints_model->getCustompoints(array('client_id' => $client_id, 'site_id' => $site_id));
        }

        $this->data['site_data'] = $site_data;
        $this->data['points_data'] = $points_data;
        $this->render_page('widget_preview');
    }

    public function createCMS()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $user_info = $this->User_model->getUserInfo($this->User_model->getId());
        $site_info = $this->Client_model->getSiteInfo($client_id,$site_id);
        $data = array(
            'client_id' => $this->User_model->getClientId(),
            'site_id' => $this->User_model->getSiteId(),
            'site_name' => $site_info['site_name'],
            'user_id' => $user_info['_id'],
            'user_name' => $user_info['username'],
            'user_email' => $user_info['email']

        );

        $this->CMS_model->createCMS($data);
    }
    public function login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $result = $this->User_model->cms_login($username,$password);
        if($result)
        {
            return $this->CMS_model->getUserRole($client_id,$site_id,$result);
        }
        else return false;
    }
    public function updateUserPermisison()
    {
        $editors = $this->input->post('editor');
        $contributors = $this->input->post('contributor');
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $cms = $this->CMS_model->getCmsInfo($client_id,$site_id);
        //$user_id = new MongoID("560a6597bfffced3120041a9");

        foreach ($editors as $editor) {
            $data_insert = array(
                'client_id' => $cms['client_id'],
                'site_id' => $cms['site_id'],
                'site_name' => $cms['site_name'],
                'user_id' => $editor['user_id'],
                'role' => "editor"
            );
            $this->CMS_model->updateUserPermission($data_insert);
        }

        foreach ($contributors as $contributor) {
            $data_insert = array(
                'client_id' => $cms['client_id'],
                'site_id' => $cms['site_id'],
                'site_name' => $cms['site_name'],
                'user_id' => $contributor['user_id'],
                'role' => "contributor"
            );
            $this->CMS_model->updateUserPermission($data_insert);
        }



        /*
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();

            if (!$this->validateModify()) {
                $this->data['message'][] = $this->lang->line('error_permission');
            }

            if (!$this->data['message']) {

            }
        }*/
    }
    public function listUser()
    {

    }


}
?>