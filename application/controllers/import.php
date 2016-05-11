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
                $returnImportActivities = array();
                $row = 0;
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);

                    $data = array();
                    $params = explode(',', $line);
                    $date = "now";
                    foreach ($params as $param) {
                        $keyAndValue = explode(':', $param);
                        $key = $keyAndValue[0];
                        $value = $keyAndValue[1];
                        if (strtolower($key) == "player_id") {
                            $player_id = $value;
                        } elseif (strtolower($key) == "action") {
                            $action = $value;
                        } elseif (strtolower($key) == "date") {
                            $date = $value;
                        } else {
                            $data = array_merge($data, array($key => $value));
                        }
                    }

                    $result = $this->postEngineRule($player_id, $action, $data, $date);

                    $returnImportActivities = array_merge($returnImportActivities, array($row => $result));
                    $row++;
                }
                $this->import_model->updateCompleteImport($this->User_model->getClientId(), $this->User_model->getSiteId(), array('results' => $returnImportActivities));

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

    private function postEngineRule($player_id, $action, $param, $date = "now")
    {
        $this->getToken();
        if ($date != "now") {
            $this->_api->setHeader('Date', $date);
        }
        $result = $this->_api->engine($player_id, $action, $param);
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

        if ($this->input->get('filter_date_start')) {
            $filter_date_start = $this->input->get('filter_date_start');
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days"));
        }

        if ($this->input->get('filter_date_end')) {

            //--> This will enable to search on the day until the time 23:59:59
            $date = $this->input->get('filter_date_end');
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
            //'import_id' => $import_id,
            'date_start' => $filter_date_start,
            'date_expire' => $filter_date_end
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $importDatas = array();
        $importLogsResults = $this->import_model->retrieveImportResults($filter);
        foreach ($importLogsResults as $importLogsResult) {
            if($importLogsResult['import_id']){
                $importData = $this->import_model->retrieveSingleImportData($importLogsResult['import_id']);
                $importDatas[]=array(
                    'name' => $importData['name'],
                    'import_type' => $importData['import_type'],
                    'routine' => $importData['routine'],
                    'date_added' => $importLogsResult['date_added'],
                    'results' => $importLogsResult['results']
                );

            }else{
                $importDatas[]=array(
                    'name' => null,
                    'import_type' => null,
                    'routine' => null,
                    'date_added' => $importLogsResult['date_added'],
                    'results' => $importLogsResult['results']
                );
            }
        }

        $this->data['importDatas'] = $importDatas;

        $this->data['filter_date_start'] = $filter_date_start;

        // --> This will show only the date, not including the time
        $filter_date_end_exploded = explode(" ", $filter_date_end);
        $this->data['filter_date_end'] = $filter_date_end_exploded[0];

        $config['total_rows'] = $this->import_model->countImportResults($filter);

        $config['base_url'] = site_url('import/page/log'); //. $parameter_url;
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
