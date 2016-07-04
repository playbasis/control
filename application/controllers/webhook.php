<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Webhook extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Webhook_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("webhook", $lang['folder']);
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
        $this->data['form'] = 'webhook/insert';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('url', $this->lang->line('entry_url'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $postData = $this->input->post();
                if (isset($postData['body_key']) && isset($postData['body_value'])){
                    $body = array();
                    foreach ($postData['body_key'] as $key => $val){
                        if (!empty($val)) {
                            $body = array_merge($body, array(
                                $val => $postData['body_value'][$key]
                            ));
                        }
                    }
                    unset($postData['body_key']);
                    unset($postData['body_value']);
                    $postData = array_merge($postData, array(
                        'body' => $body
                    ));
                }

                if (!$this->Webhook_model->getTemplateByName($this->User_model->getSiteId(), $postData['name'])) {
                    $template_id = $this->Webhook_model->addTemplate(array_merge($postData, array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($template_id) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        redirect('/webhook', 'refresh');
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
        $this->data['form'] = 'webhook/update/' . $template_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('url', $this->lang->line('entry_url'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $postData = $this->input->post();
                if (isset($postData['body_key']) && isset($postData['body_value'])){
                    $body = array();
                    foreach ($postData['body_key'] as $key => $val){
                        if (!empty($val)) {
                            $body = array_merge($body, array(
                                $val => $postData['body_value'][$key]
                            ));
                        }
                    }
                    unset($postData['body_key']);
                    unset($postData['body_value']);
                    $postData = array_merge($postData, array(
                        'body' => $body
                    ));
                }

                $c = $this->Webhook_model->getTemplateByName($this->User_model->getSiteId(), $this->input->post('name'));
                $info = $this->Webhook_model->getTemplate($template_id);
                if ($c === 0 || ($c === 1 && $info && $info['name'] == $this->input->post('name'))) {
                    $success = $this->Webhook_model->editTemplate($template_id, array_merge($postData, array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($success) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                        redirect('/webhook', 'refresh');
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
                $this->Webhook_model->deleteTemplate($template_id);
            }
            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/webhook', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset, $ajax = false)
    {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('webhook/page');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['templates'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();

        $paging_data = array('limit' => $per_page, 'start' => $offset, 'sort' => 'sort_order');

        $templates = $this->Webhook_model->listTemplatesBySiteId($site_id, $paging_data);
        $total = $this->Webhook_model->getTotalTemplatesBySiteId($site_id, $paging_data);

        foreach ($templates as $template) {
            if (!$template['deleted']) {
                $this->data['templates'][] = array(
                    '_id' => $template['_id'],
                    'name' => $template['name'],
                    'url' => $template['url'],
                    'body' => (isset($template['body']) && is_array($template['body'])) ?
                        implode(',', array_map(function ($v, $k) {
                            return sprintf('%s=%s', $k, $v);
                        }, $template['body'], array_keys($template['body'])
                        )) : null,
                    'status' => $template['status'],
                    'sort_order' => $template['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($template['_id'],
                            $this->input->post('selected'))),
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

        $this->data['main'] = 'webhook';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page($ajax ? 'webhook_ajax' : 'template');
    }

    public function getListForAjax($offset)
    {
        $this->getList($offset, true);
    }

    private function getForm($template_id = null)
    {
        $info = null;
        if (isset($template_id) && $template_id) {
            $info = $this->Webhook_model->getTemplate($template_id);
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (!empty($info)) {
            $this->data['name'] = $info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('url')) {
            $this->data['url'] = $this->input->post('url');
        } elseif (!empty($info)) {
            $this->data['url'] = $info['url'];
        } else {
            $this->data['url'] = '';
        }

        if ($this->input->post('body')) {
            $this->data['body'] = $this->input->post('body');
        } elseif (!empty($info)) {
            $this->data['body'] = $info['body'];
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

        $this->data['main'] = 'webhook_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function increase_order($template_id)
    {
        $success = $this->Webhook_model->increaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }

    public function decrease_order($template_id)
    {
        $success = $this->Webhook_model->decreaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }


    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'sms')) {
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
                'webhook') && $this->Feature_model->getFeatureExistByClientId($client_id, 'webhook')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

?>