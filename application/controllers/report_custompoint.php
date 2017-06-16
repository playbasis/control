<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_custompoint extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Custompoints_model');
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

        $this->getRewardsList(0, site_url('report_custompoint/page'));
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

        $this->getRewardsList($offset, site_url('report_custompoint/page'));
    }

    public function reward_custompoint()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList(0, site_url('report_custompoint/page'));
    }

    public function reward_badge_page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList($offset, site_url('report_custompoint/page'));
    }

    public function getRewardsList($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        $this->load->model('Report_custompoint_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');
        $this->load->model('Badge_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load("action", $lang['folder']);

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

        if ($this->input->get('action_id')) {
            $filter_action_id = $this->input->get('action_id');
            $parameter_url .= "&action_id=" . $filter_action_id;
        } else {
            $filter_action_id = '';
        }

        if ($this->input->get('status')) {
            $filter_point_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_point_status;
            if ($filter_point_status === "all" ) $filter_point_status = null;
        } else {
            $filter_point_status = null;
        }

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;


        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'action_id' => $filter_action_id,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $results = array();
        if ($client_id) {
            if($filter_point_status == "pending"){
                $in_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['in_id'] = is_array($in_id) ? array_column($in_id, '_id') : array();
            } elseif ($filter_point_status == "approve"){
                $ex_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['ex_id'] = is_array($ex_id) ? array_column($ex_id, '_id') : array();
            } elseif ($filter_point_status == "reject"){
                $in_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['in_id'] = is_array($in_id) ? array_column($in_id, '_id') : array();
            }

            $report_total = $this->Report_custompoint_model->getTotalReportPoint($data);

            $results = $this->Report_custompoint_model->getReportPoint($data);
        }

        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $this->data['reports'] = array();

        foreach ($results as $result) {
            $status = null;
            $reward_name = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if (!empty($player['image'])) {
                $thumb = $player['image'];
            } else {
                $thumb = S3_IMAGE . "cache/no_image-40x40.jpg";
            }


            $reward_name = $this->Report_custompoint_model->getPointName($result['reward_id']);
            

            if ($this->input->get('time_zone')){
                $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }

            if(isset($result['transaction_id']) && $result['transaction_id']){
                $status = $this->Custompoints_model->getRewardStatus($result['transaction_id']);

            }

            if (is_null($filter_point_status) || $filter_point_status === $status) {
                $this->data['reports'][] = array(
                    'cl_player_id' => $player['cl_player_id'],
                    'username' => $player['username'],
                    'image' => $thumb,
                    'email' => $player['email'],
                    'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
                    'reward_name' => isset($reward_name) ? $reward_name : null,
                    'value' => $result['value'],
                    'status' => isset($status) ? $status : null
                );
            }
        }

        $this->data['point_rewards'] = array();

        if ($client_id) {
            $data_filter['client_id'] = $client_id;
            $data_filter['site_id'] = $site_id;
            // $this->data['actions'] = $this->Action_model->getActionsSite($data_filter);

            $all_point_reward = $this->Report_custompoint_model->getPointBySite($data_filter);

            $this->data['point_rewards'] = $all_point_reward;
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
        // --> end
        $this->data['filter_username'] = $filter_username;
        $this->data['filter_action_id'] = $filter_action_id;
        $this->data['filter_status'] = $filter_point_status;

        $this->data['main'] = 'report_custompoint';
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

        $this->load->library('pagination');

        $this->load->model('Report_custompoint_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');
        $this->load->model('Badge_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load("action", $lang['folder']);

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

        if ($this->input->get('action_id')) {
            $filter_action_id = $this->input->get('action_id');
            $parameter_url .= "&action_id=" . $filter_action_id;
        } else {
            $filter_action_id = '';
        }

        if ($this->input->get('status')) {
            $filter_point_status = $this->input->get('status');
            $parameter_url .= "&status=" . $filter_point_status;
            if ($filter_point_status === "all" ) $filter_point_status = null;
        } else {
            $filter_point_status = null;
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'action_id' => $filter_action_id
        );

        $results = array();
        if ($client_id) {
            if($filter_point_status == "pending"){
                $in_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['in_id'] = is_array($in_id) ? array_column($in_id, '_id') : array();
            } elseif ($filter_point_status == "approve"){
                $ex_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['ex_id'] = is_array($ex_id) ? array_column($ex_id, '_id') : array();
            } elseif ($filter_point_status == "reject"){
                $in_id = $this->Report_custompoint_model->getListRewardStatusToPlayer($data,$filter_point_status);
                $data['in_id'] = is_array($in_id) ? array_column($in_id, '_id') : array();
            }
            
            $results = $this->Report_custompoint_model->getReportPoint($data);
        }

        $this->data['reports'] = array();

        $this->load->helper('export_data');

        $exporter = new ExportDataExcel('browser', "RewardReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_player_id'),
                $this->lang->line('column_username'),
                $this->lang->line('column_email'),
                $this->lang->line('column_reward_name'),
                $this->lang->line('column_reward_value'),
                $this->lang->line('column_reward_status'),
                $this->lang->line('column_date_added')
            )
        );

        foreach ($results as $result) {
            $status = null;
            $badge_name = null;
            $reward_name = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);
            if (!empty($player['image'])) {
                $thumb = $player['image'];
            } else {
                $thumb = S3_IMAGE . "cache/no_image-40x40.jpg";
            }

            $reward_name = $this->Report_custompoint_model->getPointName($result['reward_id']);

            if ($this->input->get('time_zone')){
                $date_added = new DateTime(datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }

            if(isset($result['transaction_id']) && $result['transaction_id']){
                $status = $this->Custompoints_model->getRewardStatus($result['transaction_id']);
            }

            $this->data['reports'][] = array(
                'cl_player_id' => $player['cl_player_id'],
                'username' => $player['username'],
                'image' => $thumb,
                'email' => $player['email'],
                'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
                'reward_name' => isset($reward_name) ? $reward_name : null,
                'badge_name' => isset($badge_name) ? $badge_name : null,
                'value' => $result['value'],
                'status' => isset($status) ? $status : null
            );

            $exporter->addRow(array(
                    $player['cl_player_id'],
                    $player['username'],
                    $player['email'],
                    $reward_name['name'],
                    $result['value'],
                    isset($status) ? $status : null,
                    $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added'])
                )
            );
        }

        $exporter->finalize();

    }
}

?>