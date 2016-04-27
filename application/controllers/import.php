<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class import extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('App_model');
        $this->load->model('import_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $this->_api = $this->playbasisapi;

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("data", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("import", $lang['folder']);
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
        $this->data['form'] = 'import/insert';

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');

        $this->data['message'] = null;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $data = $this->input->post();

                $data['client_id']    = $this->User_model->getClientId();
                $data['site_id']      = $this->User_model->getSiteId();
                $data['name']         = $data['name'] != "" ? $data['name']: null;
                $data['host_type']    = $data['host_type'] != "" ? $data['host_type']: null;
                $data['host_name']    = $data['host_name'] != "" ? $data['host_name']: null;
                $data['file_name']    = $data['file_name'] != "" ? $data['file_name']: null;
                $data['port']         = $data['port'] != "" ? $data['port'] : null;
                $data['user_name']    = $data['user_name'] != "" ? $data['user_name'] : null;
                $data['password']     = $data['password'] != "" ? $data['password'] : null;
                $data['import_type']  = $data['import_type'] != "" ? $data['import_type'] : null;
                $data['routine']      = $data['routine'] != "" ? $data['routine'] : null;

                $insert = $this->import_model->addImportData($data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/import', 'refresh');
                }
            }
        }
        $this->getForm();
    }

    private function getForm($import_id = null)
    {
        $this->data['main'] = 'import_form';
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        if ($import_id) {
            $this->data = array_merge($this->data, $this->import_model->retrieveSingleImportData($import_id));
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function update($import_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'import/update/' . $import_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {

                $data = $this->input->post();

                $data['client_id']    = $this->User_model->getClientId();
                $data['site_id']      = $this->User_model->getSiteId();
                $data['_id']          = $import_id;
                $data['name']         = $data['name'] != "" ? $data['name']: null;
                $data['host_type']    = $data['host_type'] != "" ? $data['host_type']: null;
                $data['host_name']    = $data['host_name'] != "" ? $data['host_name']: null;
                $data['file_name']    = $data['file_name'] != "" ? $data['file_name']: null;
                $data['port']         = $data['port'] != "" ? $data['port'] : null;
                $data['user_name']    = $data['user_name'] != "" ? $data['user_name'] : null;
                $data['password']     = $data['password'] != "" ? $data['password'] : null;
                $data['import_type']  = $data['import_type'] != "" ? $data['import_type'] : null;
                $data['routine']      = $data['routine'] != "" ? $data['routine'] : null;

                $update = $this->import_model->updateImportData($data);
                if ($update) {
                    redirect('/import', 'refresh');
                }
            }
        }

        $this->getForm($import_id);
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if ($this->input->post('selected') && $this->error['message'] == null) {
            foreach ($this->input->post('selected') as $import_id) {
                $this->import_model->deleteImportData($import_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/import', 'refresh');
        }

        $this->getList(0);
    }

    private function getLog($import_id)
    {
        $this->data['main'] = 'import_log';
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days"));
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=" . $filter_date_end;

            //--> This will enable to search on the day until the time 23:59:59
            $date = $this->input->get('date_expire');
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end

        } else {

            //--> This will enable to search on the current day until the time 23:59:59
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end
        }

        $filter = array(
            'limit' => $config['per_page'],
            'start' => '0',
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'date_added',
            'import_id' => $import_id,
            'date_start' => $filter_date_start,
            'date_expire' => $filter_date_end
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $importData = $this->import_model->retrieveSingleImportData($import_id);
            $importResults = $this->import_model->retrieveImportResults($filter);

            $importData = array_merge($importData, array('logs'=>$importResults));

            $this->data['importData'] = $importData;
            $config['total_rows'] = $this->import_model->countImportData($client_id, $site_id);
        }

        $this->data['filter_date_start'] = $filter_date_start;

        // --> This will show only the date, not including the time
        $filter_date_end_exploded = explode(" ", $filter_date_end);
        $this->data['filter_date_end'] = $filter_date_end_exploded[0];

        $config['base_url'] = site_url('import/page') . $parameter_url;

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

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function displayLog($import_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'import/displayLog/' . $import_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
/*
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {

                $data = $this->input->post();

                $data['client_id']    = $this->User_model->getClientId();
                $data['site_id']      = $this->User_model->getSiteId();
                $data['_id']      = $import_id;
                $data['name']         = $data['name'] != "" ? $data['name']: null;
                $data['url']          = $data['url'] != "" ? $data['url']: null;
                $data['port']         = $data['port'] != "" ? $data['port'] : "80";
                $data['user_name']    = $data['user_name'] != "" ? $data['user_name'] : null;
                $data['password']     = $data['password'] != "" ? $data['password'] : null;
                $data['import_type']  = $data['import_type'] != "" ? $data['import_type'] : null;
                $data['routine']      = $data['routine'] != "" ? $data['routine'] : null;

                $update = $this->import_model->updateImportData($data);
                if ($update) {
                    redirect('/import', 'refresh');
                }
            }
        }
*/
        $this->getLog($import_id);
    }

    private function getList($offset)
    {

        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'date_added'
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('import/page');

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $importData = $this->import_model->retrieveImportData($filter);

            foreach ( $importData as $key => $val){
                $inputForResult = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'import_id' => $val['_id'].""
                );
                $importResults = $this->import_model->retrieveImportResults($inputForResult);
                $importData[$key] = array_merge($importData[$key], array('logs'=>$importResults));
            }

            $this->data['importData'] = $importData;
            $config['total_rows'] = $this->import_model->countImportData($client_id, $site_id);
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

        $this->data['main'] = 'import';

        $this->load->vars($this->data);
        $this->render_page('template');
    }


    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'data')) {
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
                'data') && $this->Feature_model->getFeatureExistByClientId($client_id, 'data')
        ) {
            return true;
        } else {
            return false;
        }
    }

}
