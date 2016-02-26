<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Sms extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Sms_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("sms", $lang['folder']);
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
        $this->data['form'] = 'sms/insert';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('body', $this->lang->line('entry_body'),
            'trim|required|xss_clean|max_length[160]');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                if (!$this->Sms_model->getTemplateByName($this->User_model->getSiteId(), $this->input->post('name'))) {
                    $template_id = $this->Sms_model->addTemplate(array_merge($this->input->post(), array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($template_id) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        redirect('/sms', 'refresh');
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
        $this->data['form'] = 'sms/update/' . $template_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('body', $this->lang->line('entry_body'),
            'trim|required|xss_clean|max_length[160]');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $c = $this->Sms_model->getTemplateByName($this->User_model->getSiteId(), $this->input->post('name'));
                $info = $this->Sms_model->getTemplate($template_id);
                if ($c === 0 || ($c === 1 && $info && $info['name'] == $this->input->post('name'))) {
                    $success = $this->Sms_model->editTemplate($template_id, array_merge($this->input->post(), array(
                        'client_id' => $this->User_model->getClientId(),
                        'site_id' => $this->User_model->getSiteId(),
                    )));

                    if ($success) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                        redirect('/sms', 'refresh');
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
                $this->Sms_model->deleteTemplate($template_id);
            }
            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/sms', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset, $ajax = false)
    {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('sms/page');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['templates'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();

        $paging_data = array('limit' => $per_page, 'start' => $offset, 'sort' => 'sort_order');

        $templates = $this->Sms_model->listTemplatesBySiteId($site_id, $paging_data);
        $total = $this->Sms_model->getTotalTemplatesBySiteId($site_id, $paging_data);

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

        $this->data['main'] = 'sms';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page($ajax ? 'sms_ajax' : 'template');
    }

    public function getListForAjax($offset)
    {
        $this->getList($offset, true);
    }

    private function getForm($template_id = null)
    {
        $info = null;
        if (isset($template_id) && $template_id) {
            $info = $this->Sms_model->getTemplate($template_id);
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (!empty($info)) {
            $this->data['name'] = $info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('body')) {
            $this->data['body'] = htmlentities($this->input->post('body'));
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

        $this->data['main'] = 'sms_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function increase_order($template_id)
    {
        $success = $this->Sms_model->increaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }

    public function decrease_order($template_id)
    {
        $success = $this->Sms_model->decreaseSortOrder($template_id);
        $this->output->set_output(json_encode(array('success' => $success)));
    }

    public function setup()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->form_validation->set_rules('sms-mode', $this->lang->line('sms-mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-account_sid', $this->lang->line('sms-account_sid'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-auth_token', $this->lang->line('sms-auth_token'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-number', $this->lang->line('sms-number'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-name', $this->lang->line('sms-name'), 'trim|required|xss_clean');

        $setting_group_id = $this->User_model->getAdminGroupID();
        if ($this->User_model->getUserGroupId() != $setting_group_id) {
            $this->data['sms'] = $this->Sms_model->getSMSClient($this->User_model->getClientId());
        } else {
            $this->data['sms'] = $this->Sms_model->getSMSMaster();
        }

        if ($this->input->post()) {
            if ($this->form_validation->run()) {

                $sms_data = $this->input->post();
                if ($this->User_model->getClientId()) {

                    $sms_data['client_id'] = $this->User_model->getClientId();
                    $sms_data['site_id'] = $this->User_model->getSiteId();
                    $this->Sms_model->updateSMS($sms_data);

                } else {
                    $this->Sms_model->updateSMS($sms_data);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
            }
        }

        $this->data['main'] = 'sms_setup';
        $this->load->vars($this->data);
        $this->render_page('template');
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
                'sms') && $this->Feature_model->getFeatureExistByClientId($client_id, 'sms')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

?>