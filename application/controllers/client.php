<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Client extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Client_model');
        $this->load->model('User_group_to_client_model');
        $this->load->model('Permission_model');

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("client", $lang['folder']);
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
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    public function insert()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'client/insert';

        $this->form_validation->set_rules('company', $this->lang->line('entry_company_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('first_name', $this->lang->line('entry_firstname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('last_name', $this->lang->line('entry_lastname'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('email', $this->lang->line('entry_email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('plan_id', $this->lang->line('entry_plan'),
            'trim|required|min_length[1]|max_length[255]|xss_clean');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            // Check email is unique
            $clientEmail = $this->Client_model->getClients(null, array(
                'email' => $this->input->post('email')
            ));
            if (isset($clientEmail) && !empty($clientEmail)){
                $this->data['message'] = $this->lang->line('error_email_is_used');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $client_id = $this->Client_model->addClient($this->input->post());

                $this->Client_model->addPlanToPermission(array(
                    'client_id' => $client_id->{'$id'},
                    'plan_id' => $this->input->post('plan_id'),
                    'site_id' => null,
                ));

                $this->session->set_flashdata('success', $this->lang->line('text_success'));

                redirect('/client/update/' . $client_id, 'refresh');
            }
        }

        $this->getForm();
    }

    public function update($client_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'client/update/' . $client_id;

        $this->form_validation->set_rules('company', $this->lang->line('entry_company_name'),
            'trim|required|min_length[1]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('first_name', $this->lang->line('entry_firstname'),
            'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('last_name', $this->lang->line('entry_lastname'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('plan_id', $this->lang->line('entry_plan'),
            'trim|required|min_length[1]|max_length[255]|xss_clean');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerClient($client_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {

                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $this->Client_model->editClient($client_id, $this->input->post());

                if ($this->input->post('status') == false) {
                    $data = array('client_id' => $client_id);
                    $results = $this->User_model->getUserByClientId($data);
                    foreach ($results as $result) {
                        $user_id = $result['user_id'];
                        $this->User_model->disableUser($user_id);
                    }
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));

                redirect('/client', 'refresh');
            }
        }

        $this->getForm($client_id);
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

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $client_id) {
                if ($this->checkOwnerClient($client_id)) {
                    $this->Client_model->deleteClient($client_id);
                    $this->Client_model->deleteClientPersmission($client_id);
                    $site_id = $this->User_model->getSiteId();
                    $this->App_model->deleteAppByClientId($client_id);

                    $data = array('client_id' => $client_id);
                    $results = $this->User_model->getUserByClientId($data);
                    foreach ($results as $result) {
                        $user_id = $result['user_id'];
                        $this->User_model->deleteUser($user_id);
                    }
                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/client', 'refresh');
        }

        $this->getList(0, site_url('client'));
    }

    public function getList($offset)
    {

        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

//        $this->load->model('Domain_model');
        $this->load->model('App_model');
        $this->load->model('Image_model');
        $this->load->model('Plan_model');

        $this->load->library('pagination');

        $parameter_url = "?t=" . rand();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        if ($this->input->get('filter_name')) {
            $filter_name = $this->input->get('filter_name');
            $parameter_url .= "&filter_name=" . $filter_name;
        } else {
            $filter_name = null;
        }

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
            $parameter_url .= "&sort=" . $sort;
        } else {
            $sort = 'first_name';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
            $parameter_url .= "&order=" . $order;
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'filter_name' => $filter_name,
            'sort' => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        $total = $this->Client_model->getTotalClients($data);

        $results_client = $this->Client_model->getClients($data);

        if ($results_client) {

            foreach ($results_client as $result) {

                $plan_subscription = $this->Client_model->getPlanByClientId($result['_id']);
                $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);

                $data_client = array("client_id" => $result['_id'], 'site_id' => $site_id);
                $site_total = $this->App_model->getTotalAppsByClientId($data_client);

                if (isset($result['image'])) {
                    $info = pathinfo($result['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($result['image'], 0,
                                utf8_strrpos($result['image'], '.')) . '-140x140.' . $extension;
                        $image = S3_IMAGE . $new_image;
                    } else {
                        $image = S3_IMAGE . "cache/no_image-140x140.jpg";
                    }
                } else {
                    $image = S3_IMAGE . "cache/no_image-140x140.jpg";
                }

                /*if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 140, 140);
                }
                else {
                    $image = $this->Image_model->resize('no_image.jpg', 140, 140);
                }*/

                $this->data['clients'][] = array(
                    'client_id' => $result['_id'],
                    'company' => $result['company'],
                    'email' => $result['email'],
                    'first_name' => $result['first_name'],
                    'last_name' => $result['last_name'],
                    'plan_name' => $plan['name'],
                    'image' => $image,
                    'quantity' => $site_total,
                    'status' => $result['status'],
                    'selected' => is_array($this->input->post('selected')) && in_array($result['client_id'],
                            $this->input->post('selected')),
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

        $config['base_url'] = site_url('client/page') . $parameter_url;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;
        $config['page_query_string'] = true;

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
        $this->data['main'] = 'client';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getForm($client_id = null)
    {

        $this->load->model('Image_model');
        $this->load->model('Plan_model');

        if (isset($client_id) && ($client_id != 0)) {
            $client_info = $this->Client_model->getClient($client_id);
            $this->data['list_client_id'] = $client_id;
        } else {
            $this->data['list_client_id'] = null;
        }

        if ($this->input->post('company')) {
            $this->data['company'] = $this->input->post('company');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['company'] = $client_info['company'];
        } else {
            $this->data['company'] = '';
        }

        if ($this->input->post('first_name')) {
            $this->data['first_name'] = $this->input->post('first_name');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['first_name'] = $client_info['first_name'];
        } else {
            $this->data['first_name'] = '';
        }

        if ($this->input->post('last_name')) {
            $this->data['last_name'] = $this->input->post('last_name');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['last_name'] = $client_info['last_name'];
        } else {
            $this->data['last_name'] = '';
        }

        if ($this->input->post('mobile')) {
            $this->data['mobile'] = $this->input->post('mobile');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['mobile'] = $client_info['mobile'];
        } else {
            $this->data['mobile'] = '';
        }

        if ($this->input->post('email')) {
            $this->data['email'] = $this->input->post('email');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['email'] = $client_info['email'];
        } else {
            $this->data['email'] = '';
        }

        if ($this->input->post('plan_id')) {
            $this->data['plan_id'] = $this->input->post('plan_id');
        } elseif (isset($client_id) && ($client_id != 0)) {
            $plan_subscription = $this->Client_model->getPlanByClientId($client_info['_id']);
            $this->data['plan_id'] = $plan_subscription['plan_id'];
        } else {
            $this->data['plan_id'] = '';
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = date("Y-m-d", strtotime($this->input->post('date_start')));
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['date_start'] = array_key_exists('date_start',
                $client_info) && $client_info['date_start'] ? date("Y-m-d", $client_info['date_start']->sec) : null;
        } else {
            $this->data['date_start'] = '';
        }

        if ($this->input->post('date_expire')) {
            $this->data['date_expire'] = date("Y-m-d", strtotime($this->input->post('date_expire')));
        } elseif (isset($client_id) && ($client_id != 0)) {
            $this->data['date_expire'] = array_key_exists('date_expire',
                $client_info) && $client_info['date_expire'] ? date("Y-m-d", $client_info['date_expire']->sec) : null;
        } else {
            $this->data['date_expire'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($client_info)) {
            $this->data['image'] = $client_info['image'];
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

        /*if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($client_info) && $client_info['image'] && (S3_IMAGE . $client_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $client_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($client_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }*/

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($client_info)) {
            $this->data['status'] = $client_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['plan_data'] = $this->Plan_model->getAvailablePlans();

        //$this->data['groups'] = $this->User_model->getUserGroups();
        $this->data['groups'] = $this->User_group_to_client_model->fetchAllUserGroups($client_id);

        $this->data['main'] = 'client_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'client')) {
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
        if ($this->User_model->hasPermission('access', 'client')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerClient($clientId)
    {

//        $this->load->model('Domain_model');
        $this->load->model('App_model');

        $error = null;

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

            $theData = array(
                'client_id' => $this->User_model->getClientId(),
                'site_id' => $this->User_model->getSiteId()
            );

            $clients = $this->App_model->getAppsByClientId($theData);

            $has = false;

            foreach ($clients as $client) {
                if ($client['_id'] . "" == $clientId . "") {
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

    public function autocomplete()
    {
        $json = array();

        if ($this->input->get('filter_name')) {

            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'filter_name' => $filter_name
            );

            $results_client = $this->Client_model->getClients($data);

            foreach ($results_client as $result) {
                $json[] = array(
                    'email' => html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8'),
                );
            }

        }

        $this->output->set_output(json_encode($json));
    }
/*/
    public function domain($offset = 0)
    {

        $this->load->model('App_model');
        $this->load->model('Plan_model');

        $this->data['domains_data'] = array();

        $data = array(
            'client_id' => $this->input->get('client_id'),
            'site_id' => $this->User_model->getSiteId()
        );

        $results = $this->App_model->getAppsByClientId($data);

        if ($results) {
            foreach ($results as $result) {

                $plan_id = $this->Permission_model->getPermissionBySiteId($result['_id']);

                $this->data['domains_data'][] = array(
                    'site_id' => $result['_id'],
                    'client_id' => $result['client_id'],
                    'plan_id' => $plan_id,
                    'site_name' => $result['site_name'],
                    'keys' => $result['api_key'],
                    'secret' => $result['api_secret'],
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],
                    'date_modified' => $result['date_modified']
                );
            }
        }

        $data = array(
            'sort' => 'sort_order',
            'order' => 'ASC',
        );

        $this->data['plan_data'] = $this->Plan_model->getPlans($data);

        $this->load->vars($this->data);
        $this->render_page('client_domain');
    }
*/
    public function users($offset = 0)
    {

        $this->load->model('Plan_model');

        $this->data['users'] = array();

        $data = array(
            'client_id' => $this->input->get('client_id'),
        );

        $results = $this->User_model->getUserByClientId($data);

        if ($results) {
            foreach ($results as $result) {

                $user_data = $this->User_model->getUserInfo($result['user_id']);
                if ($user_data) {
                    $this->data['users'][] = array(
                        'user_id' => $result['user_id'],
                        'user_group_id' => $user_data['user_group_id'],
                        'client_id' => $result['client_id'],
                        'first_name' => $user_data['firstname'],
                        'last_name' => $user_data['lastname'],
                        'username' => $user_data['username'],
                        'status' => $user_data['status'],
                        'date_added' => $user_data['date_added'],
                    );
                }
            }
        }
        $this->data['list_client_id'] = $data['client_id'];
        //$this->data['groups'] = $this->User_model->getUserGroups();

        $this->data['groups'] = $this->User_group_to_client_model->fetchAllUserGroups($this->input->get('client_id'));


        $this->load->vars($this->data);
        $this->render_page('client_user');
    }
}

?>