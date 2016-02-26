<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report extends MY_Controller
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

        $this->getActionList(0, site_url('report/page'));
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

        $this->getActionList($offset, site_url('report/page'));
    }

    public function action()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList(0, site_url('report/action_page'));
    }

    public function action_page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList($offset, site_url('report/action_page'));
    }

    private function getActionList($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        $this->load->model('Action_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load("action", $lang['folder']);

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
            $filter_date_end = date("Y-m-d");

            //--> This will enable to search on the current day until the time 23:59:59
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end
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

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;

        $this->data['reports'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $filter_date_start,
            'date_expire' => $filter_date_end,
            'username' => $filter_username,
            'action_id' => $filter_action_id,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $results = array();

        if ($client_id) {
            $report_total = $this->Action_model->getTotalActionReport($data);

            $results = $this->Action_model->getActionReport($data);
        }

        foreach ($results as $result) {

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if (!empty($player['image'])) {
                $thumb = $player['image'];
            } else {
                $thumb = S3_IMAGE . "cache/no_image-40x40.jpg";
            }
            /*if (!empty($player['image']) && $player['image'] && ($player['image'] != 'HTTP/1.1 404 Not Found' && $player['image'] != 'HTTP/1.0 403 Forbidden')) {
                $thumb = $player['image'];
            } else {
                $thumb = $this->Image_model->resize('no_image.jpg', 40, 40);
            }*/

            $this->data['reports'][] = array(
                'cl_player_id' => $player['cl_player_id'],
                'username' => $player['username'],
                'image' => $thumb,
                'email' => $player['email'],
                // 'exp'               => $player['exp'],
                // 'level'             => $player['level'],
                'action_name' => $result['action_name'],
                'url' => $result['url'],
                'parameters' => ($filter_action_id != '') ? $result['parameters'] : null,
                'date_added' => $this->datetimeMongotoReadable($result['date_added'])
            );
        }

        $this->data['actions'] = array();

        if ($client_id) {
            $data_filter['client_id'] = $client_id;
            $data_filter['site_id'] = $site_id;
            $this->data['actions'] = $this->Action_model->getActionsSite($data_filter);
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

        $this->data['filter_date_start'] = $filter_date_start;

        // --> This will show only the date, not including the time
        $filter_date_end_exploded = explode(" ", $filter_date_end);
        $this->data['filter_date_end'] = $filter_date_end_exploded[0];
        // --> end

        $this->data['filter_username'] = $filter_username;
        $this->data['filter_action_id'] = $filter_action_id;

        $this->data['main'] = 'report_action';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function actionDownload()
    {
        $this->load->model('Action_model');
        $this->load->model('Player_model');

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
        } else {
            $filter_date_start = '';
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
        } else {
            $filter_date_end = '';
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
        } else {
            $filter_username = '';
        }

        if ($this->input->get('action_id')) {
            $filter_action_id = $this->input->get('action_id');
        } else {
            $filter_action_id = 0;
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $filter_date_start,
            'date_expire' => $filter_date_end,
            'username' => $filter_username,
            'action_id' => $filter_action_id
        );

        $this->load->helper('export_data');

        $results = $this->Action_model->getActionReport($data, true);

        $exporter = new ExportDataExcel('browser', "ActionReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $row_to_add = array(
            $this->lang->line('column_player_id'),
            $this->lang->line('column_username'),
            $this->lang->line('column_email'),
            $this->lang->line('column_level'),
            $this->lang->line('column_exp'),
            $this->lang->line('column_action_name'),
        );

        if ($filter_action_id != 0 && is_array($results[0]['parameters'])) {
            foreach ($results[0]['parameters'] as $key => $param) {
                array_push($row_to_add, $key);
            }
        }

        array_push($row_to_add, $this->lang->line('column_date_added'));
        $exporter->addRow($row_to_add);

        foreach ($results as $row) {
            $player = $this->Player_model->getPlayerById($row['pb_player_id'], $data['site_id']);
            $row_to_add = array(
                $player['cl_player_id'],
                $player['username'],
                $player['email'],
                $player['level'],
                $player['exp'],
                $row['action_name']
            );
            if ($filter_action_id != 0 && is_array($row['parameters'])) {
                foreach ($row['parameters'] as $param) {
                    array_push($row_to_add, $param);
                }
            }
            array_push($row_to_add, $this->datetimeMongotoReadable($row['date_added']));
            $exporter->addRow($row_to_add);
        }
        $exporter->finalize();
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
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
}

?>