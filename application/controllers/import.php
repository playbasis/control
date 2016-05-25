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
        $this->load->model('Player_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $this->_api = $this->playbasisapi;

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("import", $lang['folder']);
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
        $this->data['tab_status'] = "cron";

        $this->getList(0);
    }

    public function page($tab_status,$offset = 0)
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if($tab_status == "cron") {
            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['title'] = $this->lang->line('title');
            $this->data['heading_title'] = $this->lang->line('heading_title');
            $this->data['text_no_results'] = $this->lang->line('text_no_results');
            $this->data['tab_status'] = "cron";

            $this->getList($offset);

        }elseif($tab_status == "log") {
            $this->data['meta_description'] = $this->lang->line('meta_description');
            $this->data['title'] = $this->lang->line('title');
            $this->data['heading_title'] = $this->lang->line('heading_title');
            $this->data['text_no_results'] = $this->lang->line('text_no_results');
            $this->data['tab_status'] = "log";
            $this->data['main'] = 'import';
            $this->data['form'] = 'import/log';

            $this->getLog($offset);
        }
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
            'site_id' => $site_id
        );
        if (isset($_GET['filter_import_type'])) {
            $filter['filter_import_type'] = $_GET['filter_import_type'];
        }

        $importData = $this->import_model->retrieveImportData($filter);

        $this->data['importData'] = $importData;

        $config['total_rows'] = $this->import_model->countImportData($client_id, $site_id);

        $config['base_url'] = site_url('import/page/cron');
        $config["uri_segment"] = 4;

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

        $this->form_validation->set_rules('host_name', $this->lang->line('entry_hostname'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        $this->form_validation->set_rules('file_name', $this->lang->line('entry_filename'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');

        $this->form_validation->set_rules('routine', $this->lang->line('entry_occur'),
            'trim|required');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $data = $this->input->post();

                $data['client_id']      = $this->User_model->getClientId();
                $data['site_id']        = $this->User_model->getSiteId();
                $data['name']           = (isset($data['name']) && !empty($data['name'])) ? $data['name']: null;
                $data['host_type']      = (isset($data['host_type']) && !empty($data['name'])) ? $data['host_type']: null;
                $data['host_name']      = (isset($data['host_name']) && !empty($data['host_name'])) ? $data['host_name']: null;
                $data['port']           = (isset($data['port']) && !empty($data['port'])) ? $data['port'] : null;
                $data['user_name']      = (isset($data['user_name']) && !empty($data['user_name'])) ? $data['user_name'] : null;
                $data['password']       = (isset($data['password']) && !empty($data['password'])) ? $data['password'] : null;
                $data['file_name']      = (isset($data['file_name']) && !empty($data['file_name'])) ? $data['file_name']: null;
                $data['directory']      = (isset($data['directory']) && !empty($data['directory'])) ? $data['directory']: null;
                $data['import_type']    = (isset($data['import_type']) && !empty($data['import_type'])) ? $data['import_type'] : null;
                $data['routine']        = (isset($data['routine']) && !empty($data['routine'])) ? $data['routine'] : null;
                $data['execution_time'] = (isset($data['execution_time']) && !empty($data['execution_time'])) ? $data['execution_time'] : null;

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
                $data['name']           = (isset($data['name']) && !empty($data['name'])) ? $data['name']: null;
                $data['host_type']      = (isset($data['host_type']) && !empty($data['name'])) ? $data['host_type']: null;
                $data['host_name']      = (isset($data['host_name']) && !empty($data['host_name'])) ? $data['host_name']: null;
                $data['port']           = (isset($data['port']) && !empty($data['port'])) ? $data['port'] : null;
                $data['user_name']      = (isset($data['user_name']) && !empty($data['user_name'])) ? $data['user_name'] : null;
                $data['password']       = (isset($data['password']) && !empty($data['password'])) ? $data['password'] : null;
                $data['file_name']      = (isset($data['file_name']) && !empty($data['file_name'])) ? $data['file_name']: null;
                $data['directory']      = (isset($data['directory']) && !empty($data['directory'])) ? $data['directory']: null;
                $data['import_type']    = (isset($data['import_type']) && !empty($data['import_type'])) ? $data['import_type'] : null;
                $data['routine']        = (isset($data['routine']) && !empty($data['routine'])) ? $data['routine'] : null;
                $data['execution_time'] = (isset($data['execution_time']) && !empty($data['execution_time'])) ? $data['execution_time'] : null;

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

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'import')) {
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
                'import') && $this->Feature_model->getFeatureExistByClientId($client_id, 'import')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function adhoc()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['tab_status'] = "adhoc";
        $this->data['main'] = 'import';
        $this->data['form'] = 'import/adhoc';

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
            $this->data['message'] = null;

            $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|min_length[2]|max_length[255]|xss_clean');

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ((empty($_FILES) || !isset($_FILES['file']['tmp_name']) || $_FILES['file']['tmp_name'] == '') && $this->data['message'] == null) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != '' && $this->data['message'] == null) {

                $maxsize = 2097152;
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
            }


            if ($this->form_validation->run() && $this->data['message'] == null) {

                $import_type = $this->input->post('import_type');
                $import_name = $this->input->post('name');
                $returnImportActivities = array();

                $keys = array();
                $parameter_set = null;
                while (($line = fgets($handle)) !== false ) {
                    if(!$parameter_set){

                        $line = trim($line);
                        $parameter_set = $line;
                        $keys = explode(',', $line);
                        continue;
                    }
                    $line = trim($line);
                    $params = explode(',', $line);
                    if(count($params)<count($keys)){
                        $returnImportActivities[] = array( "input" => $line,"result" => $this->lang->line('error_format'));
                    }else {
                        $data = array();
                        foreach ($keys as $index => $key) {
                            $data += array($key => $params[$index]);
                        }

                        if($import_type == "player") {
                            $result = $this->postRegisterPlayer($data);
                        }elseif($import_type == "transaction") {
                            $result = $this->postEngineRule($data);
                        }elseif($import_type == "storeorg") {
                            $result = $this->postAddPlayerToNodeByName($data);
                        }elseif($import_type == "content") {
                            $result = $this->postAddContent($data);
                        }
                        $returnImportActivities[] = array( "input" => $line,"result" => $result);
                    }
                }

                $this->import_model->updateCompleteImport($this->User_model->getClientId(), $this->User_model->getSiteId(), $import_name, array('results' => $returnImportActivities), $parameter_set, $import_type);
                $this->session->set_flashdata('success', $this->lang->line('text_success_import'));
                redirect('/import/adhoc', 'refresh');
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getToken()
    {
        // get api key and secret

        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId()
        ));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        $this->_api->set_api_key($platform['api_key']);
        $this->_api->set_api_secret($platform['api_secret']);

        $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
        $result = $this->_api->auth($pkg_name);
        return $result->response->token;
    }

    private function postEngineRule($param)
    {
        $this->getToken();

        $player_id = isset($param['player_id']) ? $param['player_id'] : null;
        $action = isset($param['action']) ? $param['action'] : null;
        $pb_player_id = $this->Player_model->getPlaybasisId(array(
            'client_id' => $this->User_model->getClientId(),
            'site_id' => $this->User_model->getSiteId(),
            'cl_player_id' => $player_id
        ));
        if (!$pb_player_id) {
            return $this->lang->line('error_user_not_found');
        }
        if (isset($param['date']) && !is_null($param['date'])) {
            $this->_api->setHeader('Date', $param['date']);
        }
        $customs = array();
        if (isset($param['customs']) && !is_null($param['customs'])) {
            $custom_params = explode('|', $param['customs']);
            foreach ($custom_params as $custom_param)  {
                $keyAndValue = explode('=', $custom_param);
                if(count($keyAndValue)!=2){
                    return $this->lang->line('error_custom_format');
                }

                $customs = array_merge($customs, array($keyAndValue[0] => $keyAndValue[1]));
            }
        }
        $result = $this->_api->engine($player_id, $action, $customs);
        return $result->message;
    }

    private function postRegisterPlayer( $param)
    {
        $this->getToken();
        
        $player_id = isset($param['player_id']) ? $param['player_id'] : null;
        $username = isset($param['username']) ? $param['username'] : null;
        $email = isset($param['email']) ? $param['email'] : null;
        $result = $this->_api->register($player_id, $username, $email, $param);
        return $result->message;
    }

    private function postAddPlayerToNodeByName($param) {
        $this->getToken();

        $player_id = isset($param['player_id']) ? $param['player_id'] : null;
        $node_name = isset($param['node_name']) ? $param['node_name'] : null;
        $organize_type = isset($param['organize_type']) ? $param['organize_type'] : null;
        $result = $this->_api->addPlayerToNodeByName($player_id, $node_name, $organize_type);
        if($result->message == "Success" && isset($param['role']) && !is_null($param['role'])){
            $roles = explode('|', $param['role']);
            foreach($roles as $role) {
                $this->postSetPlayerRole($player_id, $result->response->node_id, $role);
            }
        }
        return $result->message;
    }

    private function postSetPlayerRole($player_id, $node_id, $role) {
        $this->getToken();

        $result = $this->_api->setPlayerRole($player_id, $node_id->{'$id'}, array('role' => $role));
        return $result->message;
    }

    private function postAddContent($param) {
        $this->getToken();

        $result = $this->_api->addContent($param);
        return $result->message;
    }

    public function log(){
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['tab_status'] = "log";
        $this->data['main'] = 'import';
        $this->data['form'] = 'import/log';

        $this->getLog(0);
    }

    private function getLog($offset)
    {
        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?";

        if ($this->input->get('filter_date_start')) {
            $filter_date_start = $this->input->get('filter_date_start');
            $parameter_url .= "&filter_date_start=" . $filter_date_start;
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days"));
        }

        if ($this->input->get('filter_date_end')) {

            //--> This will enable to search on the day until the time 23:59:59
            $date = $this->input->get('filter_date_end');
            $parameter_url .= "&filter_date_end=" . $date;
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
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'date_added',
            'date_start' => $filter_date_start,
            'date_end' => $filter_date_end
        );


        $filteredImportDataByName = array();
        $filteredImportDataByRoutine = array();

        if (isset($_GET['filter_name'])) {
            $filteredImportDataByName = $this->import_model->retrieveImportDataByName($client_id, $site_id, $_GET['filter_name']);
            foreach($filteredImportDataByName as &$data){
                $data = $data['_id']."";
            }
            $filter['import_name'] = $_GET['filter_name'];
            $parameter_url .= "&filter_name=" . $_GET['filter_name'];
        }
        if (isset($_GET['filter_import_method'])) {
            $filter['filter_import_method'] = $_GET['filter_import_method'];
            $parameter_url .= "&filter_import_method=" . $_GET['filter_import_method'];
        }
        if (isset($_GET['filter_import_type'])) {
            $filter['filter_import_type'] = $_GET['filter_import_type'];
            $parameter_url .= "&filter_import_type=" . $_GET['filter_import_type'];
        }
        if (isset($_GET['filter_occur'])) {
            $filteredImportDataByRoutine = $this->import_model->retrieveImportDataByRoutine($client_id, $site_id, $_GET['filter_occur']);
            foreach($filteredImportDataByRoutine as &$data){
                $data = $data['_id']."";
            }
            $parameter_url .= "&filter_occur=" . $_GET['filter_occur'];
        }

        if(isset($_GET['filter_occur']) && isset($_GET['filter_name']) && $_GET['filter_occur'] && $_GET['filter_name']){
            $filter['import_id'] = array_intersect($filteredImportDataByName,$filteredImportDataByRoutine);
        }elseif(isset($_GET['filter_name']) && $_GET['filter_name']){
            $filter['import_id'] = $filteredImportDataByName;
        }elseif(isset($_GET['filter_occur']) && $_GET['filter_occur']){
            $filter['import_id'] = $filteredImportDataByRoutine;
        }

        $logDatas = array();
        $importLogsResults = $this->import_model->retrieveImportResults($filter);
        $importLogsResultsCount =$this->import_model->countImportResults($filter);

        foreach ($importLogsResults as $importLogsResult) {

            $total_result = "";
            $data_result = array();
            if($importLogsResult['results'] == "Duplicate"){
                $total_result = $this->lang->line('entry_duplicate');
            }else{
                $success_count = 0;
                foreach ($importLogsResult['results'] as $index => $log_result){
                    $temp_data = array($index+1);
                    array_push($temp_data , array($log_result['input']));
                    array_push($temp_data , $log_result['result']);

                    $data_result[]=$temp_data;
                    if($log_result['result']=="Success"){
                        $success_count++;
                    }
                }
                $total_result = $success_count." / ".count($importLogsResult['results']);
            }

            if($importLogsResult['import_id']){
                $importData = $this->import_model->retrieveSingleImportData($importLogsResult['import_id']);
                $logDatas[]=array(
                    'log_id' => $importLogsResult['_id'],
                    'name' => $importData['name'],
                    'import_method' => "cron",
                    'import_type' => $importLogsResult['import_type'],
                    'routine' => $importData['routine'],
                    'date_added' => datetimeMongotoReadable($importLogsResult['date_added']),
                    'result' => $total_result,
                    'import_key' => $importLogsResult['import_key'],
                    'log_results' => $data_result
                );

            }else{
                $logDatas[]=array(
                    'log_id' => $importLogsResult['_id'],
                    'name' => isset($importLogsResult['import_name']) ? $importLogsResult['import_name'] : "",
                    'import_method' => "adhoc",
                    'import_type' => $importLogsResult['import_type'],
                    'routine' => "",
                    'date_added' => datetimeMongotoReadable($importLogsResult['date_added']),
                    'result' => $total_result,
                    'import_key' => $importLogsResult['import_key'],
                    'log_results' => $data_result
                );
            }
        }

        $this->data['logDatas'] = $logDatas;

        $this->data['filter_date_start'] = $filter_date_start;

        // --> This will show only the date, not including the time
        $filter_date_end_exploded = explode(" ", $filter_date_end);
        $this->data['filter_date_end'] = $filter_date_end_exploded[0];

        $config['total_rows'] = $importLogsResultsCount;
        //$config['total_rows'] = count($importLogsResults);

        $config['base_url'] = site_url('import/page/log');
        $config['suffix'] =  $parameter_url;
        $config["uri_segment"] = 4;

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

}
