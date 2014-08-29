<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Domain extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Client_model');
        $this->load->model('Plan_model');

        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Domain_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("domain", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

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

                $this->data['domain_list'][] = array(
                    'selected'    => is_array($this->input->post('selected')) && in_array($result['_id'], $this->input->post('selected')),
                    'site_id' => $result['_id'],
                    'client_id' => $result['client_id'],
                    'domain_name' => $result['domain_name'],
                    'site_name' => $result['site_name'],
                    'keys' => $result['api_key'],
                    'secret' => $result['api_secret'],
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

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'domain';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function reset() {
        $json = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->Domain_model->resetToken($this->input->post('site_id'));

            $json['success'] = $this->lang->line('text_success');

            $this->session->set_flashdata('success', $this->lang->line('text_success'));

        }

        $this->output->set_output(json_encode($json));
    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'domain/insert';

        $this->form_validation->set_rules('domain_domain_name', $this->lang->line('form_domain'), 'trim|required|min_length[3]|max_length[100]|xss_clean|check_space|valid_url_format|url_exists');
        $this->form_validation->set_rules('domain_site_name', $this->lang->line('form_site'), 'trim|required|min_length[3]|max_length[100]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $client_id = $this->User_model->getClientId();
                $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

                // get Plan limit_others.domain
                $limit = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "domain");
                if (!isset($limit["value"]) || !$limit["value"])
                    $limit["value"] = 3; // default

                // Get current client site
                $usage = $this->Client_model->getSitesByClientId($client_id);

                // compare
                if (sizeof($usage) >= $limit["value"]) {
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_limit_domain"));
                    redirect("domain/");
                }

                $c_data = array('domain_name' => $this->input->post('domain_domain_name'));

                $domain = $this->Domain_model->checkDomainExists($c_data);

                if(!$domain){

                    $d_data = array();
                    $d_data['client_id'] = $client_id;
                    $d_data['domain_name'] = $this->input->post('domain_domain_name');
                    $d_data['site_name'] = $this->input->post('domain_site_name');
                    $d_data['user_id'] =  $this->User_model->getId();
                    $d_data['limit_users'] = 1000;

                    $site_id = $this->Domain_model->addDomain($d_data);

                    if ($site_id) {
                        $this->load->model('Plan_model');
                        $this->load->model('Permission_model');
                        $this->load->model('Client_model');

                        $plan_subscription = $this->Client_model->getPlanByClientId($client_id);

                        $another_data['domain_value'] = array(
                            'site_id' => $site_id,
                            'status' => true
                        );

                        $this->Client_model->editClientPlan($client_id, $plan_subscription['plan_id'], $another_data);
                    }

                    $this->session->data['success'] = $this->lang->line('text_success');

                    redirect('/domain', 'refresh');
                }else{
                    if($this->input->post('format') == 'json'){
                        echo json_encode($this->data['fail_domain_exists']);
                        exit();
                    }
                }

            }
        }

        $this->getForm();

    }

    /*public function insert() {

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

    }*/

    public function insert_ajax() {

        $this->form_validation->set_rules('domain_name', $this->lang->line('entry_domain_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        $this->form_validation->set_rules('site_name', $this->lang->line('entry_site_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('limit_users', $this->lang->line('limit_users'), 'trim|xss_clean|check_space|numeric');

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

                $data['domain_name']= $this->input->post('domain_name');
                $data['site_id'] = $this->User_model->getSiteId();
                $check_domain_exists = $this->Domain_model->checkDomainExists($data);

                if(!$check_domain_exists){
                    $site_id = $this->Domain_model->addDomain($this->input->post());

                    if ($site_id) {
                        $this->load->model('Permission_model');

                        $plan_subscription = $this->Client_model->getPlanByClientId(new MongoID($this->input->post('client_id')));

                        $data = array();
                        $data['client_id'] = $this->input->post('client_id');
                        $data['plan_id'] = $plan_subscription['plan_id']->{'$id'};
                        $data['site_id'] = $site_id;
                        $this->Permission_model->addPlanToPermission($data);
                    }

                    $this->session->data['success'] = $this->lang->line('text_success');
                    $json['success'] =  $this->lang->line('text_success_insert');    
                }else{
                    $json['error'] = "The domain already exists!";    
                }

                
            }else{
                $json['error'] = "Please provide the neccessary fields below or check if there are any errors.";
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

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $site_id) {
                if($this->checkOwnerDomain($site_id)){

                    $this->Domain_model->deleteDomain($site_id);
                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/domain', 'refresh');
        }

        $this->getList(0);
    }

    public function deleteAjax() {

        $json = array();
        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('site_id') && $this->error['warning'] == null) {

            if($this->checkOwnerDomain($this->input->post('site_id'))){

                $this->Domain_model->deleteDomain($this->input->post('site_id'));
            }

            $this->session->data['success'] = $this->lang->line('text_success_delete');

            $json['success'] = $this->lang->line('text_success_delete');
        }

        $this->output->set_output(json_encode($json));
    }

    private function getForm($domain_id=null) {

        if (isset($domain_id) && ($domain_id != 0)) {
            if($this->User_model->getClientId()){
                $domain_info = $this->Domain_model->getDomain($domain_id);
            }else{
                $domain_info = $this->Domain_model->getDomain($domain_id);
            }
        }

        if ($this->input->post('domain_domain_name')) {
            $this->data['domain_domain_name'] = $this->input->post('domain_domain_name');
        } elseif (isset($domain_id) && ($domain_id != 0)) {
            $this->data['domain_domain_name'] = $domain_info['domain_name'];
        } else {
            $this->data['domain_domain_name'] = '';
        }

        if ($this->input->post('domain_site_name')) {
            $this->data['domain_site_name'] = $this->input->post('domain_site_name');
        } elseif (isset($domain_id) && ($domain_id != 0)) {
            $this->data['domain_site_name'] = $domain_info['site_name'];
        } else {
            $this->data['domain_site_name'] = '';
        }

        if (isset($domain_id)) {
            $this->data['domain_id'] = $domain_id;
        } else {
            $this->data['domain_id'] = null;
        }

        $this->data['main'] = 'domain_form';

        $this->load->vars($this->data);
        $this->render_page('template');
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

            $theData = array('client_id' => $this->User_model->getClientId(), 'site_id' =>$site_id);

            $sites = $this->Domain_model->getDomainsByClientId($theData);

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

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'domain')) {
            return true;
        } else {
            return false;
        }
    }
}
?>
