<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Email extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Email_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("email", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
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
        $this->data['form'] = 'email/insert';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('body', $this->lang->line('entry_body'), 'trim|required|max_length[30000]');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                if (!$this->Email_model->getTemplateByName($this->User_model->getSiteId(),
                    $this->input->post('name'))
                ) {
                    $data = $this->input->post();
                    $data['body'] = $this->purify($this->input->post('body'));
                    $template_id = $this->Email_model->addTemplate(array_merge($data, array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($template_id) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        redirect('/email', 'refresh');
                    } else {
                        $this->session->set_flashdata('fail', $this->lang->line('error_insert'));
                        $this->data['message'] = $this->lang->line('error_insert');
                    }
                } else {
                    $this->session->set_flashdata('fail', $this->lang->line('error_name_is_used'));
                    $this->data['message'] = $this->lang->line('error_name_is_used');
                }
            }
        }

        $this->getForm();
    }

    public function update($template_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'email/update/' . $template_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('body', $this->lang->line('entry_body'), 'trim|required|max_length[30000]');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $c = $this->Email_model->getTemplateByName($this->User_model->getSiteId(), $this->input->post('name'));
                $info = $this->Email_model->getTemplate($template_id);
                if ($c === 0 || ($c === 1 && $info && $info['name'] == $this->input->post('name'))) {
                    $data = $this->input->post();
                    $data['body'] = $this->purify($this->input->post('body'));
                    $success = $this->Email_model->editTemplate($template_id, array_merge($data, array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($success) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                        redirect('/email', 'refresh');
                    } else {
                        $this->session->set_flashdata('fail', $this->lang->line('error_update'));
                        $this->data['message'] = $this->lang->line('error_update');
                    }
                } else {
                    $this->session->set_flashdata('fail', $this->lang->line('error_name_is_used'));
                    $this->data['message'] = $this->lang->line('error_name_is_used');
                }
            }
        }

        $this->getForm($template_id);
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
            foreach ($this->input->post('selected') as $template_id) {
                $this->Email_model->deleteTemplate($template_id);
            }
            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/email', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset, $ajax = false)
    {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('email/page');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['templates'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();

        $paging_data = array('limit' => $per_page, 'start' => $offset, 'sort' => 'sort_order');

        $templates = $this->Email_model->listTemplatesBySiteId($site_id, $paging_data);
        $total = $this->Email_model->getTotalTemplatesBySiteId($site_id, $paging_data);

        foreach ($templates as $template) {
            if (!$template['deleted']) {
                $this->data['templates'][] = array(
                    '_id' => $template['_id'],
                    'name' => $template['name'],
                    'body' => $template['body'],
                    'status' => $template['status'],
                    'sort_order' => $template['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($template['_id'],
                            $this->input->post('selected'))),
                );
            }
        }

        $domain = $this->Email_model->getClientDomain($client_id, $site_id, true);
        if($domain){
            $email = explode("@",$domain['email']);
            $domain_name = $email[1];

            // check domain's status from amazon ses
            $domain_verification = $this->amazon_ses->get_identity_verification($domain_name);

            $this->data['domain'] = array(
                'email' => (isset($domain['email']) && $domain['email'] ? $domain['email'] : ""),
                'verification_status' => (isset($domain_verification['VerificationStatus']) && $domain_verification['VerificationStatus'] ? $domain_verification['VerificationStatus'] : "Not verified"),
            );
        }else{
            $this->data['domain'] = array(
                'email' => EMAIL_FROM,
                'verification_status' => "Success",
            );
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

        $this->data['main'] = 'email';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page($ajax ? 'email_ajax' : 'template');
    }

    public function getListForAjax($offset)
    {
        $this->getList($offset, true);
    }

    private function getForm($template_id = null)
    {
        $info = null;
        if (isset($template_id) && $template_id) {
            $info = $this->Email_model->getTemplate($template_id);
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (!empty($info)) {
            $this->data['name'] = $info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('body')) {
            $this->data['body'] = $this->input->post('body');
        } elseif (!empty($info)) {
            $this->data['body'] = htmlentities($info['body']);
        } else {
            $this->data['body'] = '';
        }

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($info)) {
            $this->data['sort_order'] = $info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($info)) {
            $this->data['status'] = $info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if (isset($template_id)) {
            $this->data['template_id'] = $template_id;
        } else {
            $this->data['template_id'] = null;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'email_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function updateDomainToClient($client_id, $site_id, $data){
        $domain = $this->Email_model->getClientDomain($client_id, $site_id);
        if ($domain) {
            $this->Email_model->editDomain($data);
        } else {
            $this->Email_model->addDomain($data);
        }
    }

    private function sendEmailDomainVerification($to, $token){
        $this->load->library('email');
        $this->load->library('parser');

        $data = array(
            'verification_token' => $token,
            'base_url' => site_url()
        );

        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $subject = "[Playbasis] Domain verification token";
        $htmlMessage = $this->parser->parse('emails/user_domainverification.html', $data, true);

        $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');
        $this->amazon_ses->to($to);
        // $this->amazon_ses->bcc(EMAIL_FROM);
        $this->amazon_ses->subject($subject);
        $this->amazon_ses->message($htmlMessage);
        $this->amazon_ses->send();
    }

    public function setDomain()
    {
        if ($this->session->userdata('user_id') /*&& $this->input->is_ajax_request()*/) {


            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                $this->form_validation->set_rules('email', $this->lang->line('form_email'),
                    'trim|valid_email|xss_clean|required|check_space');

                if ($this->form_validation->run()){
                    $data = $this->input->post();
                    $data['client_id'] = $client_id;
                    $data['site_id'] = $site_id;

                    $email = explode("@",$data['email']);
                    $domain_name = $email[1];
                    $email_sent = false;

                    // check if the domain has been set by other site
                    $domain_verification = $this->amazon_ses->get_identity_verification($domain_name);
                    if($domain_verification) {
                        $data['verification_status'] = $domain_verification['VerificationStatus'];
                        $data['verification_token'] = $domain_verification['VerificationToken'];
                        if($data['verification_status'] != "Success") {
                            $this->sendEmailDomainVerification($data['email'], $domain_verification['VerificationToken']);
                            // set $email_sent after sent email to user in order to trigger warning response
                            $email_sent = EMAIL_FROM;
                        }
                    }else{
                        // set the domain
                        $result = $this->amazon_ses->verify_domain($domain_name);
                        if(isset($result['Error']) || $result == false){
                            echo json_encode(array('status' => 'error', 'message' => isset($response['Error']) ? $response['Error']['Message'] : $this->lang->line('error_setting_domain')));
                            die();
                        }else {
                            $this->sendEmailDomainVerification($data['email'], $result);
                            // set $email_sent after sent email to user in order to trigger warning response
                            $email_sent = EMAIL_FROM;
                            $data['verification_status'] = "Pending";
                            $data['verification_token'] = $result;
                        }
                    }

                    $this->updateDomainToClient($client_id, $site_id, $data);
                    echo json_encode(array('status' => 'success', 'data'=> array('email'=>$data['email'], 'status'=>$data['verification_status'], 'email_sent'=>$email_sent)));
                    exit();

                }else{
                    echo json_encode(array('status' => 'error', 'message' => validation_errors()));
                    die();
                }
            }
        }else {
            $this->output->set_status_header('403');
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
            die();
        }
    }

    public function setDefaultDomain(){
        if ($this->session->userdata('user_id') /*&& $this->input->is_ajax_request()*/) {

            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }
                $domain = $this->Email_model->getClientDomain($client_id, $site_id, true);
                if($domain){
                    $data = array('client_id'=>$client_id,
                                    'site_id'=>$site_id,
                                    'status'=>false);

                    $this->Email_model->editDomain($data);
                    echo json_encode(array('status' => 'success', 'data'=> array('email'=>EMAIL_FROM, 'status'=>"Success")));
                    exit();
                }else{
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_domain_already_default')));
                    die();
                }
            }
        }else {
            $this->output->set_status_header('403');
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
            die();
        }
    }

    public function increase_order($template_id)
    {
        $success = $this->Email_model->increaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }

    public function decrease_order($template_id)
    {
        $success = $this->Email_model->decreaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'email')) {
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
                'email') && $this->Feature_model->getFeatureExistByClientId($client_id, 'email')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function purify($html)
    {
        include_once('application/libraries/HTMLPurifier.auto.php');
        $config = HTMLPurifier_Config::createDefault();
        $filter = new HTMLPurifier($config);
        return $filter->purify($html);
    }
}
