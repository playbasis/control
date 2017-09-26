<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_gift extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Goods_model');
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

        $this->getGiftList(0, site_url('report_gift/page'));

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

        $this->getGiftList($offset, site_url('report_gift/page'));
    }

    public function gift_filter()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getGiftList(0, site_url('report_gift/page'));
    }

    public function getGiftList($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        $this->load->model('Report_gift_model');
        $this->load->model('Player_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $date = date("Y-m-d", strtotime("-30 days"));
            $previousDate = strtotime($date);
            $filter_date_start = date("Y-m-d H:i:s", $previousDate);
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=" . $filter_date_end;

            if(strpos($filter_date_end, '00:00:00')){
                //--> This will enable to search on the day until the time 23:59:59
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
                //--> end*/
            }
        } else {
            //--> This will enable to search on the current day until the time 23:59:59
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end
        }

        $UTC_7 = new DateTimeZone("Asia/Bangkok");

        if ($this->input->get('time_zone')){
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
            $newTZ = new DateTimeZone($filter_time_zone);
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }

        if ($this->input->get('status')) {
            $filter_gift_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_gift_status;
            if ($filter_gift_status === "all") $filter_gift_status = null;
        } else {
            $filter_gift_status = null;
        }

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'status' => $filter_gift_status,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $results = array();

        if ($client_id) {
            $report_total = $this->Report_gift_model->getTotalReportGift($data);
            $results = $this->Report_gift_model->getReportGift($data);
        }

        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $this->data['reports'] = array();

        foreach ($results as $result) {
            $sender = $this->Player_model->getPlayerById($result['sender'], $data['site_id']);
            $receiver = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if ($this->input->get('time_zone')){
                $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");
            }else{
                $date_added = datetimeMongotoReadable($result['date_added']);
            }
            
            if(strtolower($result['gift_type']) == 'goods'){
                $goods = $this->Goods_model->getGoodsOfClientPrivate($result['gift_id']);
            }

            $this->data['reports'][] = array(
                'sender' => $sender['username'],
                'receiver' => $receiver['username'],
                'type' => $result['gift_type'],
                'name' => $result['gift_name'] ,
                'code' => strtolower($result['gift_type']) == 'goods' && isset($goods['code']) ? $goods['code'] : null,
                'value' => $result['gift_value'],
                'date_gifted' => $date_added,
            );
        }

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
        $this->data['filter_status'] = $filter_gift_status;
        $this->data['filter_username'] = $filter_username;
        $this->data['main'] = 'report_gift';
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

        if ($this->User_model->hasPermission('access', 'report/action') && $this->Feature_model->getFeatureExistByClientId($client_id, 'report/action')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function giftDownload()
    {
        $parameter_url = "?t=" . rand();
        $this->load->model('Report_gift_model');
        $this->load->model('Player_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=" . $filter_date_start;
        } else {
            $date = date("Y-m-d", strtotime("-30 days"));
            $previousDate = strtotime($date);
            $filter_date_start = date("Y-m-d H:i:s", $previousDate);
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            if(strpos($filter_date_end, '00:00:00')){
                //--> This will enable to search on the day until the time 23:59:59
                $currentDate = strtotime($filter_date_end);
                $futureDate = $currentDate + ("86399");
                $filter_date_end = date("Y-m-d H:i:s", $futureDate);
                //--> end*/
            }
        } else {
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
        }

        $UTC_7 = new DateTimeZone("Asia/Bangkok");
        if ($this->input->get('time_zone')){

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
            $newTZ = new DateTimeZone($filter_time_zone);
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=" . $filter_username;
        } else {
            $filter_username = '';
        }

        if ($this->input->get('status')) {
            $filter_gift_status = $this->input->get('status');
            if ($filter_gift_status === "all") $filter_gift_status = null;
        } else {
            $filter_gift_status = null;
        }

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'status' => $filter_gift_status
        );

        $report_total = 0;
        if ($client_id) {
            $report_total = $this->Report_gift_model->getTotalReportGift($data);
        }
        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $this->load->helper('export_data');

        $exporter = new ExportDataExcel('browser', "GiftReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_sender'),
                $this->lang->line('column_receiver'),
                $this->lang->line('column_gift_type'),
                $this->lang->line('column_gift_name'),
                $this->lang->line('column_gift_code'),
                $this->lang->line('column_gift_amount'),
                $this->lang->line('column_date_gifted'),
            )
        );
        $data['limit'] = 10000;
        for ($i = 0; $i < $report_total/10000; $i++){
            $data['start'] = ($i * 10000);
            $results = $this->Report_gift_model->getReportGift($data);
            foreach ($results as $result) {

                $sender = $this->Player_model->getPlayerById($result['sender'], $data['site_id']);
                $receiver = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

                if ($this->input->get('time_zone')){
                    $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                    $date_added->setTimezone($newTZ);
                    $date_added = $date_added->format("Y-m-d H:i:s");
                }else{
                    $date_added = datetimeMongotoReadable($result['date_added']);
                }

                if(strtolower($result['gift_type']) == 'goods'){
                    $goods = $this->Goods_model->getGoodsOfClientPrivate($result['gift_id']);
                }

                $exporter->addRow(array(
                        $sender['username'],
                        $receiver['username'],
                        $result['gift_type'],
                        $result['gift_name'],
                        strtolower($result['gift_type']) == 'goods' && isset($goods['code']) ? $goods['code'] : null,
                        $result['gift_value'],
                        $date_added,
                    )
                );
            }
        }
        $exporter->finalize();

    }
}