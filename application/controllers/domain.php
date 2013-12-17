<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Domain extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Domain_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("domain", $lang['folder']);
    }

    public function index() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('domain/page');

        $this->load->model('Permission_model');

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
            $sort = 'domain_name';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page ;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort'  => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        if($client_id){
            $total = $this->Domain_model->getTotalDomainsByClientId($data);

            $results_site = $this->Domain_model->getDomainsByClientId($data);
        }else{
            $total = $this->Domain_model->getTotalDomains($data);

            $results_site = $this->Domain_model->getDomains($data);
        }

        if ($results_site) {
            foreach ($results_site as $result) {

                $plan_id = $this->Permission_model->getPermissionBySiteId($result['_id']);

                $this->data['domain_list'][] = array(
                    'selected'    => is_array($this->input->post('selected')) && in_array($result['_id'], $this->input->post('selected')),
                    'site_id' => $result['_id'],
                    'client_id' => $result['client_id'],
                    'plan_id' => $plan_id,
                    'domain_name' => $result['domain_name'],
                    'site_name' => $result['site_name'],
                    'keys' => $result['api_key'],
                    'secret' => $result['api_secret'],
                    'date_start' => $result['date_start'],
                    'date_expire' => $result['date_expire'],
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

        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'domain';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('domain');
    }

    public function reset() {
        $json = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->Domain_model->resetToken($this->input->post('site_id'));

            $json['success'] = $this->lang->line('text_success');

        }

        $this->output->set_output(json_encode($json));
    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'domain/insert';

        $this->form_validation->set_rules('domain_name', $this->lang->line('domain_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('plan_id', $this->lang->line('plan_id'), 'required');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $site_id = $this->Domain_model->addDomain($this->input->post());

                if ($site_id) {
                    $this->load->model('Permission_model');

                    $data = array();
                    $data['client_id'] = $this->input->post('client_id');
                    $data['plan_id'] = $this->input->post('plan_id');
                    $data['site_id'] = $site_id;
                    $this->Permission_model->addPlanToPermission($data);
                }

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/domain', 'refresh');
            }
        }

        $this->getForm();

    }

    public function insert_ajax() {

        $this->form_validation->set_rules('domain_name', $this->lang->line('domain_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('plan_id', $this->lang->line('plan_id'), 'required');

        $json = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
                $json['error'] = $this->data['message'];
            }

            if($this->checkLimitDomain($this->input->post('client_id'))){
                $this->data['message'] = $this->lang->line('error_limit');
                $json['error'] = $this->data['message'];
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $site_id = $this->Domain_model->addDomain($this->input->post());

                if ($site_id) {
                    $this->load->model('Permission_model');

                    $data = array();
                    $data['client_id'] = $this->input->post('client_id');
                    $data['plan_id'] = $this->input->post('plan_id');
                    $data['site_id'] = $site_id;
                    $this->Permission_model->addPlanToPermission($data);
                }

                $this->session->data['success'] = $this->lang->line('text_success');
                $json['success'] =  $this->lang->line('text_success');
            }
        }

        $this->output->set_output(json_encode($json));
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

        if ($this->input->post('site_id') && $this->error['warning'] == null) {

            if($this->checkOwnerDomain($this->input->post('site_id'))){

                $this->Domain_model->deleteDomain($this->input->post('site_id'));
            }

            $this->session->data['success'] = $this->lang->line('text_success');

            redirect('/domain', 'refresh');
        }

        $this->getList(0);
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'domain')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerDomain($site_id){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $sites = $this->Domain_model->getDomainsByClientId($this->User_model->getClientId());

            $has = false;

            foreach ($sites as $site) {
                if($site['_id']."" == $site_id.""){
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

    private function checkLimitDomain($client_id){
        $data['client_id'] = $client_id;
        $domains = $this->Domain_model->getTotalDomainsByClientId($data);

        if ($domains > 10) {
            return true;
        } else {
            return false;
        }
    }
}
?>