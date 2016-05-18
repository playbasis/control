<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class App extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Client_model');
        $this->load->model('Plan_model');
        $this->load->model('Badge_model');
        $this->load->model('Rule_model');

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        if ($this->input->get('site_id')) {
            $this->User_model->updateSiteId($this->input->get('site_id'));
        }

        $this->load->model('App_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("app", $lang['folder']);
    }

    public function index()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

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
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    private function getList($offset)
    {

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('app/page');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        if ($this->input->get('filter_name')) {
            $filter_name = $this->input->get('filter_name');
        } else {
            $filter_name = null;
        }

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
        } else {
            $sort = 'site_name';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        if ($client_id) {
            $total = $this->App_model->getTotalAppsByClientId($data);

            $results_site = $this->App_model->getAppsByClientId($data);

            $total_platform = $this->App_model->getTotalPlatFormsByClientId($data);
        } else {
            $total = $this->App_model->getTotalApps($data);

            $results_site = $this->App_model->getApps($data);

            $total_platform = 0;
        }

        if ($total == 0) {
            $this->session->unset_userdata('site_id');
            redirect('/first_app', 'refresh');
        }

        if ($results_site) {
            foreach ($results_site as $result) {

                $data_filter_app = array(
                    'site_id' => $result['_id']
                );
                $app_data = $this->App_model->getPlatFormByAppId($data_filter_app);

                $this->data['site_list'][] = array(
                    'selected' => is_array($this->input->post('selected')) && in_array($result['_id'],
                            $this->input->post('selected')),
                    'site_id' => $result['_id'],
                    'client_id' => $result['client_id'],
                    'site_name' => $result['site_name'],
                    'apps' => $app_data,
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],
                    'date_modified' => $result['date_modified']
                );
            }
        }

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

        $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
        // get Plan limit_others.domain
        $this->data['plan_limit_app'] = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others",
            "app");
        $this->data['plan_limit_platform'] = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"],
            "others", "platform");

        $this->data['total_app'] = $total;
        $this->data['total_platform'] = $total_platform;

        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

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

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'app';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function reset()
    {
        $json = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $secret = $this->App_model->resetToken($this->input->post('platform_id'));

            $json['success'] = $this->lang->line('text_success');
            $json['secret'] = $secret;

            $this->session->set_flashdata('success', $this->lang->line('text_success'));

        }

        $this->output->set_output(json_encode($json));
    }

    public function add()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'app/add';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $client_id = $this->User_model->getClientId();

            $this->form_validation->set_rules('app_name', $this->lang->line('form_domain'),
                'trim|required|min_length[3]|max_length[100]|check_space|alpha_dash');
            $this->form_validation->set_rules('platform', $this->lang->line('form_site'),
                'trim|required|min_length[3]|max_length[100]|xss_clean');

            if (strtolower($this->input->post('platform')) == "ios") {
                $this->form_validation->set_rules('ios_bundle_id', $this->lang->line('form_ios_bundle_id'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');

            } elseif (strtolower($this->input->post('platform')) == "android") {
                $this->form_validation->set_rules('android_package_name', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');
            } else {
                $this->form_validation->set_rules('site_url', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean|url_exists_without_http|ip_is_public');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

                // get Plan limit_others.domain
                $limit_app = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "app");

                // Get current client app
                $data_filter_app = array('client_id' => $client_id);
                $usage_app = $this->App_model->getAppsByClientId($data_filter_app);
                $usage_app_conut = $usage_app ? count($usage_app) : 0;

                // compare
                if ($limit_app !== null && $usage_app_conut >= $limit_app) { // limit = null, means unlimited
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_limit_app"));
                    redirect("app");
                }

                // get Plan limit_others.domain
                $limit_platform = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others",
                    "platform");

                // Get current client site
                $data_filter_app = array('client_id' => $client_id);
                $usage_platform = $this->App_model->getPlatFormsByClientId($data_filter_app);
                $usage_platform_conut = $usage_platform ? count($usage_platform) : 0;

                // compare
                if ($limit_platform !== null && $usage_platform_conut >= $limit_platform) { // limit = null, means unlimited
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_limit_platform"));
                    redirect("app");
                }

                $c_data = array('site_name' => $this->input->post('app_name'));

                $site = $this->App_model->checkAppExists($c_data);

                if (!$site) {

                    $data_platform = array();
                    if (strtolower($this->input->post('platform')) == 'ios') {
                        if ($this->input->post('ios_bundle_id')) {
                            $data_platform["ios_bundle_id"] = $this->input->post('ios_bundle_id');
                        }
                        if ($this->input->post('ios_iphone_store_id')) {
                            $data_platform["ios_iphone_store_id"] = $this->input->post('ios_iphone_store_id');
                        }
                        if ($this->input->post('ios_ipad_store_id')) {
                            $data_platform["ios_ipad_store_id"] = $this->input->post('ios_ipad_store_id');
                        }
                    } elseif (strtolower($this->input->post('platform')) == 'android') {
                        if ($this->input->post('android_package_name')) {
                            $data_platform["android_package_name"] = $this->input->post('android_package_name');
                        }
                    } else {
                        if ($this->input->post('site_url')) {
                            $data_platform["site_url"] = $this->input->post('site_url');
                        }
                    }
                    $insert_data = array(
                        "app_name" => $this->input->post('app_name'),
                        "client_id" => $client_id,
                        "image" => $this->input->post('image'),
                        "platform" => strtolower($this->input->post('platform')),
                        "data" => $data_platform
                    );
                    list($site_id, $keySecret) = $this->App_model->addApp($insert_data);

                    /* switch to new app immediately */
                    $this->session->set_userdata('site_id', $site_id);

                    $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
                    $plan_id = $plan_subscription['plan_id'] . "";

                    /* bind plan to client in playbasis_permission */
                    $this->Client_model->addPlanToPermission(array(
                        'client_id' => $client_id->{'$id'},
                        'plan_id' => $plan_id,
                        'site_id' => $site_id . "",
                    ));

                    $another_data['site_value'] = array(
                        'site_id' => $site_id,
                        'status' => true
                    );
                    $this->Client_model->editClientPlan($client_id, $plan_id, $another_data);

                    /* preset badges */
                    $this->Badge_model->copyBadgesFromTemplate($client_id, $site_id);

                    /* preset rules*/
                    $this->Rule_model->copyRulesFromTemplate($client_id, $site_id);

                    /* pre-register test player */
                    $pkg_name = isset($data_platform['ios_bundle_id']) ? $data_platform['ios_bundle_id'] : (isset($data_platform["android_package_name"]) ? $data_platform["android_package_name"] : null);
                    $this->createTestPlayer($keySecret, array(
                        'cl_player_id' => TEST_PLAYER_ID,
                        'username' => 'test',
                        'email' => 'test@email.com',
                        'first_name' => 'Firstname',
                        'last_name' => 'Lastname',
                    ), $pkg_name);

                    $this->session->data['success'] = $this->lang->line('text_success');

                    redirect('app', 'refresh');
                } else {
                    if ($this->input->post('format') == 'json') {
                        echo json_encode($this->lang->line('text_fail_app_exists'));
                        exit();
                    }
                    $this->data['message'] = $this->lang->line('text_fail_app_exists');
                }
            }
        }

        $this->getForm();
    }

    private function createTestPlayer($keySecret, $data, $pkg_name)
    {
        $this->_api = $this->playbasisapi;
        $this->_api->set_api_key($keySecret['key']);
        $this->_api->set_api_secret($keySecret['secret']);
        $this->_api->auth($pkg_name);
        $status = $this->_api->register($data['cl_player_id'], $data['username'], $data['email'], array(
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ));
        return $status && isset($status->success) && $status->success;
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if (($this->input->post('app_selected') || $this->input->post('platform_selected')) && $this->error['warning'] == null) {

            if ($this->input->post('platform_selected')) {
                foreach ($this->input->post('platform_selected') as $platform_id) {
                    if ($this->checkOwnerPlatForm($platform_id)) {

                        $this->App_model->deletePlatform($platform_id);
                    }
                }
            }

            $client_id = $this->User_model->getClientId();
            $data_check = array('client_id' => $client_id);

            $check = $this->App_model->getTotalAppsByClientId($data_check);
            if ($check == 0) {
                $this->session->unset_userdata('site_id');
                redirect('/first_app', 'refresh');
            }

            $this->session->data['success'] = $this->lang->line('text_success_delete');
        }

        $this->getList(0);
    }

    public function platform_edit($platform_id)
    {
        $app = $this->App_model->getPlatform($platform_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('platform', $this->lang->line('form_site'),
                'trim|required|min_length[3]|max_length[100]|xss_clean');

            if (strtolower($this->input->post('platform')) == "ios") {
                $this->form_validation->set_rules('ios_bundle_id', $this->lang->line('form_ios_bundle_id'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');
            } elseif (strtolower($this->input->post('platform')) == "android") {
                $this->form_validation->set_rules('android_package_name', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');
            } else {
                $this->form_validation->set_rules('site_url', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean|url_exists_without_http|ip_is_public');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $data_platform = array();
                if (strtolower($this->input->post('platform')) == 'ios') {
                    if ($this->input->post('ios_bundle_id')) {
                        $data_platform["ios_bundle_id"] = $this->input->post('ios_bundle_id');
                    }
                    if ($this->input->post('ios_iphone_store_id')) {
                        $data_platform["ios_iphone_store_id"] = $this->input->post('ios_iphone_store_id');
                    }
                    if ($this->input->post('ios_ipad_store_id')) {
                        $data_platform["ios_ipad_store_id"] = $this->input->post('ios_ipad_store_id');
                    }
                } elseif (strtolower($this->input->post('platform')) == 'android') {
                    if ($this->input->post('android_package_name')) {
                        $data_platform["android_package_name"] = $this->input->post('android_package_name');
                    }
                } else {
                    if ($this->input->post('site_url')) {
                        $data_platform["site_url"] = $this->input->post('site_url');
                    }
                }

                $edit_data = array(
                    "platform" => strtolower($this->input->post('platform')),
                    "data" => $data_platform
                );
                $this->App_model->editApp($platform_id, $edit_data);

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('app', 'refresh');
            }
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['form'] = 'app/platform_edit/' . $platform_id;

        $this->getForm($app["site_id"] . "", $platform_id);
    }

    public function add_platform($app_id)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $client_id = $this->User_model->getClientId();

            $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

            // get Plan limit_others.domain
            $limit = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "platform");

            // Get current client site
            $data_filter_app = array('client_id' => $client_id);
            $usage = $this->App_model->getPlatFormsByClientId($data_filter_app);
            $usage_conut = $usage ? count($usage) : 0;

            // compare
            if ($limit !== null && $usage_conut >= $limit) { // limit = null, means unlimited
                $this->session->set_flashdata("fail", $this->lang->line("text_fail_limit_platform"));
                redirect("app");
            }

            $this->form_validation->set_rules('platform', $this->lang->line('form_site'),
                'trim|required|min_length[3]|max_length[100]|xss_clean');

            if (strtolower($this->input->post('platform')) == "ios") {
                $this->form_validation->set_rules('ios_bundle_id', $this->lang->line('form_ios_bundle_id'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');
            } elseif (strtolower($this->input->post('platform')) == "android") {
                $this->form_validation->set_rules('android_package_name', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean');
            } else {
                $this->form_validation->set_rules('site_url', $this->lang->line('form_site'),
                    'trim|required|min_length[3]|max_length[100]|xss_clean|url_exists_without_http|ip_is_public');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $data_platform = array();
                if (strtolower($this->input->post('platform')) == 'ios') {
                    if ($this->input->post('ios_bundle_id')) {
                        $data_platform["ios_bundle_id"] = $this->input->post('ios_bundle_id');
                    }
                    if ($this->input->post('ios_iphone_store_id')) {
                        $data_platform["ios_iphone_store_id"] = $this->input->post('ios_iphone_store_id');
                    }
                    if ($this->input->post('ios_ipad_store_id')) {
                        $data_platform["ios_ipad_store_id"] = $this->input->post('ios_ipad_store_id');
                    }
                } elseif (strtolower($this->input->post('platform')) == 'android') {
                    if ($this->input->post('android_package_name')) {
                        $data_platform["android_package_name"] = $this->input->post('android_package_name');
                    }
                } else {
                    if ($this->input->post('site_url')) {
                        $data_platform["site_url"] = $this->input->post('site_url');
                    }
                }

                $add_data = array(
                    "platform" => strtolower($this->input->post('platform')),
                    "data" => $data_platform
                );
                $this->App_model->addPlatform($app_id, $add_data);

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('app', 'refresh');
            }
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['form'] = 'app/add_platform/' . $app_id;

        $this->getForm($app_id);
    }

    private function getForm($app_id = null, $platform_id = null)
    {

        if (isset($app_id) && ($app_id != 0)) {
            $site_info = $this->App_model->getApp($app_id);
        }

        if (isset($platform_id) && ($platform_id != 0)) {
            $app_info = $this->App_model->getPlatform($platform_id);
        }

        if ($this->input->post('app_name')) {
            $this->data['app_name'] = $this->input->post('app_name');
        } elseif (isset($app_id) && ($app_id != 0)) {
            $this->data['app_name'] = $site_info['site_name'];
        } else {
            $this->data['app_name'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($site_info)) {
            $this->data['image'] = $site_info['image'];
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
            } elsE {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }
        } else {
            $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        }

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

        if ($this->input->post('platform')) {
            $this->data['platform'] = $this->input->post('platform');
        } elseif (isset($platform_id) && ($platform_id != 0)) {
            $this->data['platform'] = $app_info['platform'];
        } else {
            $this->data['platform'] = '';
        }

        if ($this->input->post('site_url')) {
            $this->data['site_url'] = $this->input->post('site_url');
        } elseif (isset($platform_id) && ($platform_id != 0) && isset($app_info['data']['site_url'])) {
            $this->data['site_url'] = $app_info['data']['site_url'];
        } else {
            $this->data['site_url'] = '';
        }

        if ($this->input->post('ios_bundle_id')) {
            $this->data['ios_bundle_id'] = $this->input->post('ios_bundle_id');
        } elseif (isset($platform_id) && ($platform_id != 0) && isset($app_info['data']['ios_bundle_id'])) {
            $this->data['ios_bundle_id'] = $app_info['data']['ios_bundle_id'];
        } else {
            $this->data['ios_bundle_id'] = '';
        }

        if ($this->input->post('ios_iphone_store_id')) {
            $this->data['ios_iphone_store_id'] = $this->input->post('ios_iphone_store_id');
        } elseif (isset($platform_id) && ($platform_id != 0) && isset($app_info['data']['ios_iphone_store_id'])) {
            $this->data['ios_iphone_store_id'] = $app_info['data']['ios_iphone_store_id'];
        } else {
            $this->data['ios_iphone_store_id'] = '';
        }

        if ($this->input->post('ios_ipad_store_id')) {
            $this->data['ios_ipad_store_id'] = $this->input->post('ios_ipad_store_id');
        } elseif (isset($platform_id) && ($platform_id != 0) && isset($app_info['data']['ios_ipad_store_id'])) {
            $this->data['ios_ipad_store_id'] = $app_info['data']['ios_ipad_store_id'];
        } else {
            $this->data['ios_ipad_store_id'] = '';
        }

        if ($this->input->post('android_package_name')) {
            $this->data['android_package_name'] = $this->input->post('android_package_name');
        } elseif (isset($platform_id) && ($platform_id != 0) && isset($app_info['data']['android_package_name'])) {
            $this->data['android_package_name'] = $app_info['data']['android_package_name'];
        } else {
            $this->data['android_package_name'] = '';
        }

        if (isset($app_id)) {
            $this->data['app_id'] = $app_id;
        } else {
            $this->data['app_id'] = null;
        }

        if (isset($platform_id)) {
            $this->data['platform_id'] = $platform_id;
        } else {
            $this->data['platform_id'] = null;
        }

        $data_filter_app = array(
            'site_id' => $app_id
        );
        $app_data = $this->App_model->getPlatFormByAppId($data_filter_app);

        $platform_already_have = array();
        foreach ($app_data as $ad) {
            $platform_already_have[] = $ad['platform'];
        }
        $this->data['platform_already_have'] = $platform_already_have;

        $this->data['main'] = 'app_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'app')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerApp($app_id)
    {

        $error = null;

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

            $theData = array('client_id' => $this->User_model->getClientId(), 'site_id' => $app_id);

            $sites = $this->App_model->getAppsByClientId($theData);

            $has = false;

            foreach ($sites as $site) {
                if ($site['_id'] . "" == $app_id . "") {
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

    private function checkOwnerPlatForm($platform_id)
    {
        $error = null;

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

            $theData = array('client_id' => $this->User_model->getClientId());

            $platforms = $this->App_model->getPlatFormsByClientId($theData);

            $has = false;

            foreach ($platforms as $platform) {
                if ($platform['_id'] . "" == $platform_id . "") {
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

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'app') && $this->Feature_model->getFeatureExistByClientId($client_id, 'app')
        ) {
            return true;
        } else {
            return false;
        }
    }

//    public function move_key_secret_to_platform(){
//        $this->load->model('App_model');
//        $this->load->model('Domain_model');
//
//        $domains = $this->Domain_model->getDomains(array());
//
//        foreach($domains as $d){
//
//            $domain = preg_replace("/http:\/\//", "", $d['domain_name']);
//            $domain = preg_replace("/https:\/\//", "", $domain);
//
//            $insert_data = array(
//                "client_id" => $d["client_id"],
//                "site_id" => $d["_id"],
//                "platform" => "web",
//                "api_key" => $d["api_key"],
//                "api_secret" => $d["api_secret"],
//                "data" => array("site_url" => $domain),
//                "status" => $d["status"],
//                "deleted" => $d["deleted"]
//            );
//            $this->App_model->moveOldtoNewSystem($insert_data);
//        }
//    }
}

?>
