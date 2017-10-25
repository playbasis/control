<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Reward_control extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Sequence_model');
        $this->load->model('Custom_reward_model');
        $this->load->model('Custom_param_condition_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("reward_control", $lang['folder']);
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
        $this->data['tab_status'] = "sequence_reward";
        $this->data['main'] = 'reward_control';
        $this->data['form'] = 'reward_control/';

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
                if($selectedSequences && is_array($selectedSequences)) {
                    foreach ($selectedSequences as $selectedSequence) {
                        $result = $this->Sequence_model->deleteSequence($client_id,$site_id,$selectedSequence);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                    redirect('/reward_control', 'refresh');
                } else {
                    $this->session->set_flashdata('warning', $this->lang->line('text_fail_delete'));
                    redirect('/reward_control', 'refresh');
                }
            }
        }

        $this->getList(0);
    }

    public function custom_reward()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['tab_status'] = "custom_reward";
        $this->data['main'] = 'reward_control';
        $this->data['form'] = 'reward_control/custom_reward';

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
                $selectedCustomRewards = $this->input->post('selected');

                if($selectedCustomRewards && is_array($selectedCustomRewards)) {
                    foreach ($selectedCustomRewards as $selectedCustomReward) {
                        $result = $this->Custom_reward_model->deleteCustomReward($client_id, $site_id, $selectedCustomReward);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                    redirect('/reward_control/custom_reward', 'refresh');
                } else {
                    $this->session->set_flashdata('warning', $this->lang->line('text_fail_delete'));
                    redirect('/reward_control/custom_reward', 'refresh');
                }
            }
        }

        $this->getList(0);
    }

    public function custom_param_condition()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['tab_status'] = "custom_param_condition";
        $this->data['main'] = 'reward_control';
        $this->data['form'] = 'reward_control/custom_param_condition';

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
                $selectedCustomParamConditions = $this->input->post('selected');

                if($selectedCustomParamConditions && is_array($selectedCustomParamConditions)) {
                    foreach ($selectedCustomParamConditions as $selectedCustomParamCondition) {
                            $result = $this->Custom_param_condition_model->deleteCustomParamCondition($client_id, $site_id, $selectedCustomParamCondition);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                    redirect('/reward_control/custom_param_condition', 'refresh');
                } else {
                    $this->session->set_flashdata('warning', $this->lang->line('text_fail_delete'));
                    redirect('/reward_control/custom_param_condition', 'refresh');
                }
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
        $this->data['form'] = 'reward_control/';

        $this->getList($offset);
    }

    public function insert($type)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'reward_control/insert/'.$type;
        if($type == 'sequence_reward'){
            $this->data['main'] = 'reward_control_sequence_form';
        } elseif($type == 'custom_reward') {
            $this->data['main'] = 'reward_control_custom_reward_form';
        }  elseif($type == 'custom_param_condition') {
            $this->data['main'] = 'reward_control_custom_param_condition_form';
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }
            
            $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

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

                    if($type == 'sequence_reward'){
                        // prepare data of sequence number
                        if($this->generateSequenceData($handle,$data)) {

                            $insert_file = $this->Sequence_model->insertSequence($data);
                            if ($insert_file) {
                                $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                                redirect('/reward_control', 'refresh');
                            } else {
                                $this->data['message'] = $this->lang->line('text_fail_insert');
                            }
                        }else{
                            $this->data['message'] = $this->lang->line('error_non_numeric');
                        }
                    } elseif($type == 'custom_reward') {
                        if($this->generateCustomRewardData($handle,$data)) {

                            $insert_file = $this->Custom_reward_model->insertCustomReward($data);
                            if ($insert_file) {
                                $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                                redirect('/reward_control', 'refresh');
                            } else {
                                $this->data['message'] = $this->lang->line('text_fail_insert');
                            }
                        }else{
                            $this->data['message'] = $this->lang->line('error_reward_type');
                        }
                    } elseif($type == 'custom_param_condition') {
                        if($this->generateCustomParamConditionData($handle,$data)) {

                            $insert_file = $this->Custom_param_condition_model->insertCustomParamCondition($data);
                            if ($insert_file) {
                                $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                                redirect('/reward_control', 'refresh');
                            } else {
                                $this->data['message'] = $this->lang->line('text_fail_insert');
                            }
                        }else{
                            $this->data['message'] = $this->lang->line('error_reward_type');
                        }
                    }
                }
            }
        }
        $this->getForm($type);
    }

    public function update($type, $item_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        if($type == 'sequence_reward') {
            $this->data['main'] = 'reward_control_sequence_form';
        } elseif ($type == 'custom_reward'){
            $this->data['main'] = 'reward_control_custom_reward_form';
        } elseif ($type == 'custom_param_condition'){
            $this->data['main'] = 'reward_control_custom_param_condition_form';
        }

        $this->data['form'] = 'reward_control/update/'.$type .'/'. $item_id;

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }
            
            $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

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
                    if($type == 'sequence_reward') {
                        if(!$this->generateSequenceData($handle, $data))
                        {
                            $this->data['message'] = $this->lang->line('error_non_numeric');
                        }
                    } elseif ($type == 'custom_reward') {
                        if(!$this->generateCustomRewardData($handle,$data))
                        {
                            $this->data['message'] = $this->lang->line('error_reward_type');
                        }
                    } elseif ($type == 'custom_param_condition') {
                        if(!$this->generateCustomParamConditionData($handle,$data))
                        {
                            $this->data['message'] = $this->lang->line('error_reward_type');
                        }
                    }
                }
            }

            if ( $this->data['message'] == null && $this->form_validation->run() ) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                if($type == 'sequence_reward') {
                    $insert = $this->Sequence_model->updateSequence($client_id, $site_id, $item_id, $data);
                } elseif ($type == 'custom_reward') {
                    $insert = $this->Custom_reward_model->updateCustomReward($client_id, $site_id, $item_id, $data);
                }  elseif ($type == 'custom_param_condition') {
                    $insert = $this->Custom_param_condition_model->updateCustomParamCondition($client_id, $site_id, $item_id, $data);
                }

                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    if($type == 'sequence_reward') {
                        redirect('/reward_control', 'refresh');
                    } elseif ($type == 'custom_reward') {
                        redirect('/reward_control/custom_reward', 'refresh');
                    }
                }else{
                    $this->data['message'] = $this->lang->line('text_fail_update');
                }
            }
        }

        $this->getForm($type, $item_id);
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

    private function generateCustomRewardData($handle,&$data){
        $result = true;
        $custom_reward_data = array();
        $file_content = array();
        $parameter_set = null;
        while ((($line = fgets($handle)) !== false) && $result ) {
            $file_content[]= $line;
            $line = trim($line);
            if (empty($line) || $line == ',' || (!$parameter_set && strpos($line,'reward_type'))) {
                $line = trim($line);
                $parameter_set = $line;
                continue;
            } // skip empty line
            $obj = explode(',', $line);
            $custom_param = trim($obj[0]);
            $reward_type = strtolower(trim($obj[1]));
            $reward_name = trim($obj[2]);
            if(!in_array($reward_type, array('goods','goods_group','badge','custom_point'))){
                $result = false;
                break;
            }
            if(!isset($custom_reward_data[$custom_param])) {
                $custom_reward_data[$custom_param] = array();
            }
            array_push($custom_reward_data[$custom_param] , array('reward_type' => $reward_type, 'reward_name' => $reward_name));
        }
        $data['file_content'] = $file_content;
        $data['custom_reward_data'] = $custom_reward_data;
        return $result;
    }

    private function generateCustomParamConditionData($handle,&$data){
        $result = true;
        $custom_param_condition_data = array();
        $file_content = array();
        $parameter_set = null;
        while ((($line = fgets($handle)) !== false) && $result ) {
            $file_content[]= $line;
            $line = trim($line);
            if (empty($line) || $line == ',' || (!$parameter_set && strpos($line,'reward_type'))) {
                $line = trim($line);
                $parameter_set = $line;
                continue;
            } // skip empty line
            $obj = explode(',', $line);
            foreach($obj as $value){
                $custom_param_condition_data[] = $value;
            }
        }
        $data['file_content'] = $file_content;
        $data['custom_param_condition_data'] = $custom_param_condition_data;
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

        $config['base_url'] = site_url('reward_control/page');

        if($this->data['tab_status'] == 'sequence_reward'){
            $this->data['data_list'] = $this->Sequence_model->retrieveSequence($filter);
            $config['total_rows'] = $this->Sequence_model->getTotalSequence($filter);
        } elseif ($this->data['tab_status'] == 'custom_reward') {
            $this->data['data_list'] = $this->Custom_reward_model->retrieveCustomReward($filter);
            $config['total_rows'] = $this->Custom_reward_model->getTotalCustomReward($filter);
        } elseif ($this->data['tab_status'] == 'custom_param_condition') {
            $this->data['data_list'] = $this->Custom_param_condition_model->retrieveCustomParamCondition($filter);
            $config['total_rows'] = $this->Custom_param_condition_model->getTotalCustomParamCondition($filter);
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

        $this->data['main'] = 'reward_control';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getForm($type,$item_id = null)
    {
        if (!is_null($item_id)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();
            if($type == 'sequence_reward'){
                $data_info = $this->Sequence_model->retrieveSequenceByID($client_id, $site_id, $item_id);
            } elseif ($type == 'custom_reward'){
                $data_info = $this->Custom_reward_model->retrieveCustomRewardByID($client_id, $site_id, $item_id);
            } elseif ($type == 'custom_param_condition'){
                $data_info = $this->Custom_param_condition_model->retrieveCustomParamConditionByID($client_id, $site_id, $item_id);
            }
            
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($data_info['name'])) {
            $this->data['name'] = $data_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($data_info['file_name'])) {
            $this->data['file_name'] = $data_info['file_name'];
        } else {
            $this->data['file_name'] = '';
        }

        if (isset($data_info['file_id'])) {
            $this->data['file_id'] = $data_info['file_id'].'';
        } else {
            $this->data['file_id'] = '';
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = explode(',', $this->input->post('tags'));
        } elseif (isset($data_info['tags'])) {
            $this->data['tags'] = $data_info['tags'];
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

    public function getCustomRewardFile()
    {
        if ($this->input->get('file_id')) {
            $sequence_file = $this->Custom_reward_model->retrieveCustomRewardFileByID($this->User_model->getClientId(),$this->User_model->getSiteId(),$this->input->get('file_id'));
        }

        if ($this->input->get('file_name')) {
            $file_name = $this->input->get('file_name');
        }else{
            $file_name = "custom_reward.csv";
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

    public function getCustomParamConditionFile()
    {
        if ($this->input->get('file_id')) {
            $sequence_file = $this->Custom_param_condition_model->retrieveCustomParamConditionFileByID($this->User_model->getClientId(),$this->User_model->getSiteId(),$this->input->get('file_id'));
        }

        if ($this->input->get('file_name')) {
            $file_name = $this->input->get('file_name');
        }else{
            $file_name = "custom_param_condition.csv";
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
        if ($this->User_model->hasPermission('modify', 'reward_control')) {
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

        if ($this->User_model->hasPermission('access', 'reward_control') && $this->Feature_model->getFeatureExistByClientId($client_id, 'reward_control')
        ) {
            return true;
        } else {
            return false;
        }
    }

}