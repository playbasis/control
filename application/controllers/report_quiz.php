<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_quiz extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Quiz_model');
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

        $this->getQuizsList(0, site_url('report_quiz/page'));
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

        $this->getQuizsList($offset, site_url('report_quiz/page'));
    }

    public function quiz()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getQuizsList(0, site_url('report_quiz/page'));
    }

    public function quiz_page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getQuizsList($offset, site_url('report_quiz/page'));
    }

    public function getQuizsList($offset, $url)
    {
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;
        $parameter_url = "?t=" . rand();

        $this->load->library('pagination');

        $this->load->model('Report_quiz_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

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

        $limit = ($this->input->get('limit')) ? $this->input->get('limit') : $per_page;


        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'quiz_id' => $filter_action_id,
            'start' => $offset,
            'limit' => $limit
        );

        $report_total = 0;

        $results = array();
        if ($client_id) {
            $report_total = $this->Report_quiz_model->getTotalReportQuiz($data);

            $results = $this->Report_quiz_model->getReportQuiz($data);
        }

        $this->data['time_zone'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $this->data['reports'] = array();

        foreach ($results as $result) {

            $quiz_name = null;
            $question_name = null;
            $score = null;
            $max_score = null;
            $option = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            $is_multiple_choice = isset($result['is_multiple_choice']) ? $result['is_multiple_choice'] : false;
            $quiz_name = $result['quiz_name'];
            $question_name = isset($result['question']['question']) ? $result['question']['question'] : null;
            if($is_multiple_choice){
                $option_list = array();
                foreach ($result['option'] as $value){
                    array_push($option_list, empty($value['explanation']) ? $value['option'] : $value['explanation']." : ".$value['option']);
                }
                $option = implode(', ', $option_list);
            } else {
                $option = isset($result['option']['option']) ? $result['option']['option'] : null;
            }


            if (isset($result['grade'])) {
                if (isset($result['quiz_completed']) && $result['quiz_completed']) {
                    $score = $result['grade']['total_score'];
                    $max_score = $result['grade']['total_max_score'];
                } else {
                    $score = $result['grade']['score'];
                    $max_score = $result['grade']['max_score'];
                }
            }
            if ($this->input->get('time_zone')){
                $date_added = new DateTime( datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }

            $this->data['reports'][] = array(
                'cl_player_id' => $player['cl_player_id'],
                'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
                'quiz_name' => isset($quiz_name) ? $quiz_name : null,
                'question_name' => $question_name,
                'option' => $option,
                'score' => $score,
                'max_score' => $max_score,
                'quiz_completed' => isset($result['quiz_completed']) ? $result['quiz_completed'] : null,
            );
        }

        $this->data['quiz_list'] = $this->Quiz_model->getQuizs($data);


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

        $this->data['main'] = 'report_quiz';
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

        $this->load->model('Report_quiz_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

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

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date_start' => $this->input->get('time_zone') ? $filter_date_start2 : $filter_date_start,
            'date_expire' => $this->input->get('time_zone')? $filter_date_end2 : $filter_date_end,
            'username' => $filter_username,
            'quiz_id' => $filter_action_id
        );

        $results = array();
        if ($client_id) {
            $results = $this->Report_quiz_model->getReportQuiz($data);
        }

        $this->data['reports'] = array();

        foreach ($results as $result) {

            $quiz_name = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id']);

            $is_multiple_choice = isset($result['is_multiple_choice']) ? $result['is_multiple_choice'] : false;
            $quiz_name = $result['quiz_name'];
            $question_name = isset($result['question']['question']) ? $result['question']['question'] : null;
            if($is_multiple_choice){
                $option_list = array();
                foreach ($result['option'] as $value){
                    array_push($option_list, empty($value['explanation']) ? $value['option'] : $value['explanation']." : ".$value['option']);
                }
                $option = implode(', ', $option_list);
            } else {
                $option = isset($result['option']['option']) ? $result['option']['option'] : null;
            }

            if (isset($result['grade'])) {
                if (isset($result['quiz_completed']) && $result['quiz_completed']) {
                    $score = $result['grade']['total_score'];
                    $max_score = $result['grade']['total_max_score'];
                } else {
                    $score = $result['grade']['score'];
                    $max_score = $result['grade']['max_score'];
                }
            }
            if ($this->input->get('time_zone')){
                $date_added = new DateTime( datetimeMongotoReadable($result['date_added']), $UTC_7);
                $date_added->setTimezone($newTZ);
                $date_added = $date_added->format("Y-m-d H:i:s");;
            }
            $this->data['reports'][] = array(
                'cl_player_id' => $player['cl_player_id'],
                'date_added' => $this->input->get('time_zone') ? $date_added : datetimeMongotoReadable($result['date_added']),
                'quiz_name' => isset($quiz_name) ? $quiz_name : null,
                'question_name' => $question_name,
                'option' => $option,
                'score' => $score,
                'max_score' => $max_score,
                'quiz_completed' => isset($result['quiz_completed']) ? $result['quiz_completed'] : null,
            );
        }

        $results = $this->data['reports'];

        $this->load->helper('export_data');

        $exporter = new ExportDataExcel('browser', "quizReport_" . date("YmdHis") . ".xls");

        $exporter->initialize(); // starts streaming data to web browser

        $exporter->addRow(array(
                $this->lang->line('column_player_id'),
                $this->lang->line('column_quiz_name'),
                $this->lang->line('column_question_name'),
                $this->lang->line('column_option'),
                $this->lang->line('column_score'),
                $this->lang->line('column_max_score'),
                $this->lang->line('column_date_added')
            )
        );

        foreach ($results as $row) {
            $exporter->addRow(array(
                    $row['cl_player_id'],
                    $row['quiz_name'],
                    $row['question_name'],
                    $row['option'],
                    $row['score'],
                    $row['max_score'],
                    $row['date_added']
                )
            );
        }
        $exporter->finalize();
    }
}

?>