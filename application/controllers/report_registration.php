<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_registration extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("report", $lang['folder']);
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

        $this->getRegisteredPlayers(0, site_url('report_registration/page'));

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

        $this->getRegisteredPlayers($offset, site_url('report_registration/page'));
    }

    public function registration_filter()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRegisteredPlayers(0, site_url('report_registration/page'));
    }

    private function getDefaultRegistrationReportDateStart()
    {
        return date("Y-m-d H:i:s", strtotime(date("Y-m-d", strtotime("-30 days"))));
    }

    private function getDefaultRegistrationReportDateEnd()
    {
        return date("Y-m-d H:i:s", strtotime(date("Y-m-d")) + 86399);
    }

    private function getRegistrationReportDateFilter($value, $fallback, $end_of_day = false)
    {
        if (!is_string($value)) {
            return $fallback;
        }

        $value = trim($value);
        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $format = 'Y-m-d';
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
            $format = 'Y-m-d H:i:s';
        } else {
            return $fallback;
        }

        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if (!$date || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return $fallback;
        }

        if ($date->format($format) !== $value) {
            return $fallback;
        }

        if ($end_of_day && $format === 'Y-m-d H:i:s' && strpos($value, '00:00:00') !== false) {
            return $date->modify('+86399 seconds')->format("Y-m-d H:i:s");
        }

        return $value;
    }

    public function getRegisteredPlayers($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        // $this->load->model('Report_goods_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');


        if ($this->input->get('date_start')) {
            $filter_date_start = $this->getRegistrationReportDateFilter(
                $this->input->get('date_start'),
                $this->getDefaultRegistrationReportDateStart()
            );
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $filter_date_start = $this->getDefaultRegistrationReportDateStart();
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->getRegistrationReportDateFilter(
                $this->input->get('date_expire'),
                $this->getDefaultRegistrationReportDateEnd(),
                true
            );
            $parameter_url .= "&date_expire=" . $filter_date_end;
        } else {
            $filter_date_end = $this->getDefaultRegistrationReportDateEnd();
        }

        if ($this->input->get('time_zone')){
            $UTC_7 = new DateTimeZone("Asia/Bangkok");

            $filter_time_zone = $this->input->get('time_zone');
            $parameter_url .= "&time_zone=" . urlencode($filter_time_zone);
            $newTZ = new DateTimeZone($filter_time_zone);
            $date_start = new DateTime( $filter_date_start, $newTZ);
            $date_start->setTimezone($UTC_7);
            $filter_date_start2 = $date_start->format("Y-m-d H:i:s");;

            $date_end = new DateTime( $filter_date_end, $newTZ);
            $date_end->setTimezone($UTC_7);
            $filter_date_end2 = $date_end->format("Y-m-d H:i:s");
        } else {
            $filter_time_zone = "Asia/Bangkok";
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }

        // --> NEW INPUT!

        if ($this->input->get('filter_site_id')) {
            $filter_site_id = $this->input->get('filter_site_id');
            $parameter_url .= "&site_id=" . $filter_site_id;
        } else {
            $filter_site_id = $this->User_model->getSiteId();
        }

        // --> END NEW INPUT!

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'filter_site_id' => $filter_site_id,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $result = array();

        if ($client_id) {

            $this->load->model('Report_player_model');

            $report_total = $this->Report_player_model->getTotalPlayers($data);

            $results = $this->Report_player_model->getReportPlayers($data);

        }

        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $this->data['reports'] = array();

        foreach ($results as $result) {
            if ($this->input->get('time_zone')){
                $date_added = new DateTime( datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }

            $this->data['reports'][] = array(
                'cl_player_id' => $result['cl_player_id'],
                'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
            );
        }

        $this->data['sites_available'] = $this->Report_player_model->getAllSitesFromClient($client_id);


        $config['base_url'] = $url . $parameter_url;

        $config['total_rows'] = $report_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;
        $config['page_query_string'] = true;

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

        $this->data['filter_time_zone'] = $filter_time_zone;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;
        // --> end
        $this->data['filter_username'] = $filter_username;
        $this->data['filter_site_id'] = $filter_site_id;

        $this->data['main'] = 'report_registration';
        $this->load->vars($this->data);
        $this->render_page('template');


    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'report/action') && $this->Feature_model->getFeatureExistByClientId($client_id, 'report/action')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function actionDownload()
    {

        $parameter_url = "?t=" . rand();
        $this->load->model('Report_goods_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->getRegistrationReportDateFilter(
                $this->input->get('date_start'),
                $this->getDefaultRegistrationReportDateStart()
            );
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $filter_date_start = $this->getDefaultRegistrationReportDateStart();
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->getRegistrationReportDateFilter(
                $this->input->get('date_expire'),
                $this->getDefaultRegistrationReportDateEnd(),
                true
            );
        } else {
            $filter_date_end = $this->getDefaultRegistrationReportDateEnd();
        }

        if ($this->input->get('time_zone')){
            $UTC_7 = new DateTimeZone("Asia/Bangkok");

            $filter_time_zone = $this->input->get('time_zone');
            $newTZ = new DateTimeZone($filter_time_zone);
            $date_start = new DateTime( $filter_date_start, $newTZ);
            $date_start->setTimezone($UTC_7);
            $filter_date_start2 = $date_start->format("Y-m-d H:i:s");;

            $date_end = new DateTime( $filter_date_end, $newTZ);
            $date_end->setTimezone($UTC_7);
            $filter_date_end2 = $date_end->format("Y-m-d H:i:s");
        } else {
            $filter_time_zone = "Asia/Bangkok";
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
        );

        $results = array();

        if ($client_id) {

            $this->load->model('Report_player_model');

            $results = $this->Report_player_model->getReportPlayers($data);
        }

        $this->data['reports'] = array();

        foreach ($results as $result) {

            if ($this->input->get('time_zone')){
                $date_added = new DateTime( datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }

            $this->data['reports'][] = array(
                'cl_player_id' => $result['cl_player_id'],
                'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
            );
        }
        $results = $this->data['reports'];

        $this->load->helper('export_data');

        $exporter = new ExportDataExcel('browser', "RegisterReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_player_id'),
                $this->lang->line('column_date_registered')
            )
        );

        foreach ($results as $row) {
            $exporter->addRow(array(
                    $row['cl_player_id'],
                    $row['date_added']
                )
            );
        }
        $exporter->finalize();
    }

}
