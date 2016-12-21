<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Sequence extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Sequence_model');
        $this->load->model('Location_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("sequence", $lang['folder']);
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
        $this->data['form'] = 'sequence/';

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['warning'] = $this->lang->line('error_permission');
            }else{
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $selectedSequences = $this->input->post('selected');

                    foreach ($selectedSequences as $selectedSequence) {
                        $result = $this->Sequence_model->deleteSequence($client_id,$site_id,$selectedSequence);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                    redirect('/sequence', 'refresh');
            }
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
        $this->data['form'] = 'sequence/';

        $this->getList($offset);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'sequence/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name']) || $_FILES['file']['tmp_name'] == '') {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if ( $this->data['message'] == null && $this->form_validation->run() ) {

                $maxsize = 4194304;
                $csv_mimetypes = array(
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'text/comma-separated-values',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'text/anytext',
                    'application/octet-stream',
                    'application/txt',
                );

                if (($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
                    $this->data['message'] = $this->lang->line('error_file_too_large');
                }

                if (!in_array($_FILES['file']['type'], $csv_mimetypes) && (!empty($_FILES["file"]["type"]))) {
                    $this->data['message'] = $this->lang->line('error_type_accepted');
                }

                $handle = fopen($_FILES['file']['tmp_name'], "r");
                if (!$handle) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }

                if ( $this->data['message'] == null){
                    $data = $this->input->post();
                    $data['client_id'] = $client_id;
                    $data['site_id'] = $site_id;
                    $data['file_name'] = $_FILES['file']['name'];

                    // prepare data of sequence number
                    if($this->generateSequenceData($handle,$data)) {

                        $insert_file = $this->Sequence_model->insertSequence($data);
                        if ($insert_file) {
                            $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                            redirect('/sequence', 'refresh');
                        } else {
                            $this->data['message'] = $this->lang->line('text_fail_insert');
                        }
                    }else{
                        $this->data['message'] = $this->lang->line('error_non_numeric');
                    }
                }
            }
        }
        $this->getForm();
    }

    public function update($sequence_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'sequence/update/' . $sequence_id;

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if (!empty($_FILES) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != '') {

                $maxsize = 4194304;
                $csv_mimetypes = array(
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'text/comma-separated-values',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'text/anytext',
                    'application/octet-stream',
                    'application/txt',
                );

                if (($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
                    $this->data['message'] = $this->lang->line('error_file_too_large');
                }

                if (!in_array($_FILES['file']['type'], $csv_mimetypes) && (!empty($_FILES["file"]["type"]))) {
                    $this->data['message'] = $this->lang->line('error_type_accepted');
                }

                $handle = fopen($_FILES['file']['tmp_name'], "r");
                if (!$handle) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }

                $data['file_name'] = $_FILES['file']['name'];

                if ( $this->data['message'] == null) {
                    // prepare data of sequence number
                    if(!$this->generateSequenceData($handle, $data))
                    {
                        $this->data['message'] = $this->lang->line('error_non_numeric');
                    }
                }
            }

            if ( $this->data['message'] == null && $this->form_validation->run() ) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $insert = $this->Sequence_model->updateSequence($client_id, $site_id, $sequence_id, $data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/sequence', 'refresh');
                }else{
                    $this->data['message'] = $this->lang->line('text_fail_update');
                }
            }
        }

        $this->getForm($sequence_id);
    }

    private function generateSequenceData($handle,&$data){
        $result = true;
        $sequence_number = array();
        $file_content = array();
        while ((($line = fgets($handle)) !== false) && $result ) {
            $file_content[]= $line;
            $line = str_replace(' ', '', trim($line));
            if (empty($line)) {
                // skip empty line
                continue;
            }
            $obj = explode(',', $line);
            foreach($obj as $number){
                if($number){
                    if(is_numeric($number)){
                        $sequence_number[]= (int)$number;
                    }else{
                        $result = false;
                        break;
                    }
                }
            }
        }
        $data['file_content'] = $file_content;
        $data['sequence_list'] = $sequence_number;
        return $result;
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
            'sort' => 'name'
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('sequence/page');

        $this->data['sequences'] = $this->Sequence_model->retrieveSequence($filter);

        $config['total_rows'] = $this->Sequence_model->getTotalSequence($filter);

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

        $this->data['main'] = 'sequence';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getForm($sequence_id = null)
    {
        $this->data['main'] = 'sequence_form';

        if (!is_null($sequence_id)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();
            $sequence_info = $this->Sequence_model->retrieveSequenceByID($client_id, $site_id, $sequence_id);
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($sequence_info['name'])) {
            $this->data['name'] = $sequence_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($sequence_info['file_name'])) {
            $this->data['file_name'] = $sequence_info['file_name'];
        } else {
            $this->data['file_name'] = '';
        }

        if (isset($sequence_info['file_id'])) {
            $this->data['file_id'] = $sequence_info['file_id'].'';
        } else {
            $this->data['file_id'] = '';
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = explode(',', $this->input->post('tags'));
        } elseif (isset($sequence_info['tags'])) {
            $this->data['tags'] = $sequence_info['tags'];
        } else {
            $this->data['tags'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getSequenceFile()
    {
        $json = array();

        if ($this->input->get('file_id')) {
            $sequence_file = $this->Sequence_model->retrieveSequenceFileByID($this->User_model->getClientId(),$this->User_model->getSiteId(),$this->input->get('file_id'));
        }

        if ($this->input->get('file_name')) {
            $file_name = $this->input->get('file_name');
        }else{
            $file_name = "sequence.csv";
        }

        if(isset($sequence_file['file_content'])){
            $this->load->helper('export_data');

            $exporter = new ExportDataCSVSequence('browser', $file_name);

            $exporter->initialize(); // starts streaming data to web browser

            foreach ($sequence_file['file_content'] as $row) {
                $exporter->addRow(array($row) );
            }
            $exporter->finalize();
        }

    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'sequence')) {
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

        if ($this->User_model->hasPermission('access', 'sequence') && $this->Feature_model->getFeatureExistByClientId($client_id, 'sequence')
        ) {
            return true;
        } else {
            return false;
        }
    }

}