<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Campaign extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Campaign_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("campaign", $lang['folder']);
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

        $this->getList(0);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'campaign/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['message'] = null;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $campaign_data = $this->input->post();
                log_message('error','campaign_data'. print_r($campaign_data,true));
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $data['name'] = $campaign_data['name'];
                $data['date_start'] = null;
                $data['date_end'] = null;
                $data['weight'] = isset($campaign_data['weight']) && $campaign_data['weight'] ? intval($campaign_data['weight']) : 0;
                $data['status'] = isseT($campaign_data['status']) && $campaign_data['status'] == "on" ? true : false;

                if (isset($campaign_data['date_start']) && !empty($campaign_data['date_start']) && isset($campaign_data['date_end']) && !empty($campaign_data['date_end'])) {
                    $date_start_another = strtotime($campaign_data['date_start']);
                    $date_end_another = strtotime($campaign_data['date_end']);
                    if ($date_start_another < $date_end_another) {
                        $data['date_start'] = new MongoDate($date_start_another);
                        $data['date_end'] = new MongoDate($date_end_another);
                    } else {
                        $this->data['message'] = $this->lang->line('error_date_time');
                    }
                } else {
                    if (isset($campaign_data['date_start']) && $campaign_data['date_start']) {
                        $date_start_another = strtotime($campaign_data['date_start']);
                        $data['date_start'] = new MongoDate($date_start_another);
                    }
                    if (isset($campaign_data['date_end']) && $campaign_data['date_end']) {
                        $date_end_another = strtotime($campaign_data['date_end']);
                        $data['date_end'] = new MongoDate($date_end_another);
                    }
                }
                if(empty($this->data['message'])){
                    $insert = $this->Campaign_model->insertCampaign($data);
                    if($insert){
                        redirect('/campaign', 'refresh');
                    }
                }
            }
        }
        $this->getForm();
    }

    public function update($campaign_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'campaign/update/' . $campaign_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $campaign_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['_id'] = $campaign_id;
                $data['name'] = $campaign_data['name'];
                $data['date_start'] = null;
                $data['date_end'] = null;
                $data['weight'] = isset($campaign_data['weight']) && $campaign_data['weight'] ? intval($campaign_data['weight']) : 0;
                $data['status'] = isseT($campaign_data['status']) && $campaign_data['status'] == "on" ? true : false;

                if (isset($campaign_data['date_start']) && $campaign_data['date_start'] && isset($campaign_data['date_end']) && $campaign_data['date_end']) {
                    $date_start_another = strtotime($campaign_data['date_start']);
                    $date_end_another = strtotime($campaign_data['date_end']);
                    if ($date_start_another < $date_end_another) {
                        $data['date_start'] = new MongoDate($date_start_another);
                        $data['date_end'] = new MongoDate($date_end_another);
                    } else {
                        $this->data['message'] = $this->lang->line('error_date_time');
                    }
                } else {
                    if (isset($campaign_data['date_start']) && $campaign_data['date_start']) {
                        $date_start_another = strtotime($campaign_data['date_start']);
                        $data['date_start'] = new MongoDate($date_start_another);
                    }
                    if (isset($campaign_data['date_end']) && $campaign_data['date_end']) {
                        $date_end_another = strtotime($campaign_data['date_end']);
                        $data['date_end'] = new MongoDate($date_end_another);
                    }
                }
                if (empty($this->data['message'])){
                    $update = $this->Campaign_model->updateCampaign($data);
                    if($update){
                        redirect('/campaign', 'refresh');
                    }
                }
            }
        }

        $this->getForm($campaign_id);
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if ($this->input->post('selected') && $this->error['message'] == null) {
            foreach ($this->input->post('selected') as $campaign_id) {
                $this->Campaign_model->deleteCampaign(new MongoId($campaign_id));
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/campaign', 'refresh');
        }

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

    private function getList($offset)
    {

        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;
        $config['base_url'] = site_url('campaign/page');

        if ($client_id) {
            $this->data['client_id'] = $client_id;
            $filter = array(
                'limit' => $config['per_page'],
                'offset' => $offset,
            );
            $campaigns  = $this->Campaign_model->getCampaign($client_id, $site_id, $filter);

            $this->data['campaigns'] = $campaigns;
            $config['total_rows'] = $this->Campaign_model->countCampaign($client_id, $site_id);
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

        $this->data['main'] = 'campaign';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getForm($campaign_id = null)
    {
        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();
        $this->data['main'] = 'campaign_form';

        if (isset($campaign_id) && ($campaign_id != 0)) {
            if ($this->User_model->getClientId()) {
                $campaign_info = $this->Campaign_model->getCampaign($client_id, $site_id, array('campaign_id' => new MongoId($campaign_id)));
                $campaign_info = $campaign_info ? $campaign_info[0] : null;
            }
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($campaign_info['name'])) {
            $this->data['name'] = $campaign_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (isset($campaign_info['date_start'])) {
            $this->data['date_start'] = datetimeMongotoReadable($campaign_info['date_start']);
        } else {
            $this->data['date_start'] = "";
        }

        if ($this->input->post('date_end')) {
            $this->data['date_end'] = $this->input->post('date_end');
        } elseif (isset($campaign_info['date_end'])) {
            $this->data['date_end'] = datetimeMongotoReadable($campaign_info['date_end']);
        } else {
            $this->data['date_end'] = "";
        }

        if ($this->input->post('weight')) {
            $this->data['weight'] = $this->input->post('weight');
        } elseif (isset($campaign_info['weight'])) {
            $this->data['weight'] = $campaign_info['weight'];
        } else {
            $this->data['weight'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($campaign_info['status'])) {
            $this->data['status'] = $campaign_info['status'];
        } else {
            $this->data['status'] = true;
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'campaign')) {
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

        if ($this->User_model->hasPermission('access', 'campaign') &&
            $this->Feature_model->getFeatureExistByClientId($client_id, 'campaign'))
        {
            return true;
        } else {
            return false;
        }
    }
}