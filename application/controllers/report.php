<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("report", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList(0, site_url('report/page'));
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList($offset, site_url('report/page'));
    }

    public function action() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList(0, site_url('report/action_page'));
    }

    public function action_page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getActionList($offset, site_url('report/action_page'));
    }

    private function getActionList($offset, $url){
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = 10;
        $parameter_url = "?t=".rand();

        $this->load->library('pagination');

        $this->load->model('Action_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load("action", $lang['folder']);

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=".$filter_date_start;
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days"));
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=".$filter_date_end;

            //--> This will enable to search on the day until the time 23:59:59
            $date = $this->input->get('date_expire');
            $currentDate = strtotime($date);
            $futureDate = $currentDate+("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end

        } else {
            $filter_date_end = date("Y-m-d");

            //--> This will enable to search on the current day until the time 23:59:59
            $date = date("Y-m-d");
            $currentDate = strtotime($date);
            $futureDate = $currentDate+("86399");
            $filter_date_end = date("Y-m-d H:i:s", $futureDate);
            //--> end
        }

        if ($this->input->get('username')) {
            $filter_username = $this->input->get('username');
            $parameter_url .= "&username=".$filter_username;
        } else {
            $filter_username = '';
        }

        if ($this->input->get('action_id')) {
            $filter_action_id = $this->input->get('action_id');
            $parameter_url .= "&action_id=".$filter_action_id;
        } else {
            $filter_action_id = '';
        }

        $limit =($this->input->get('limit')) ? $this->input->get('limit') : $per_page ;

        $this->data['reports'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id'              => $client_id,
            'site_id'                => $site_id,
            'date_start'	         => $filter_date_start,
            'date_expire'	         => $filter_date_end,
            'username'               => $filter_username,
            'action_id'              => $filter_action_id,
            'start'                  => $offset,
            'limit'                  => $limit
        );

        $report_total = 0;

        $results = array();

        if($client_id){
            $report_total = $this->Action_model->getTotalActionReport($data);

            $results = $this->Action_model->getActionReport($data);
        }

        foreach ($results as $result) {

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if (!empty($player['image']) && $player['image'] && ($player['image'] != 'HTTP/1.1 404 Not Found' && $player['image'] != 'HTTP/1.0 403 Forbidden')) {
                $thumb = $player['image'];
            } else {
                $thumb = $this->Image_model->resize('no_image.jpg', 40, 40);
            }

            $this->data['reports'][] = array(
                'cl_player_id'      => $player['cl_player_id'],
                'username'          => $player['username'],
                'image'             => $thumb,
                'email'             => $player['email'],
                // 'exp'               => $player['exp'],
                // 'level'             => $player['level'],
                'action_name'       => $result['action_name'],
                'url'               => $result['url'],
                'date_added'        => $this->datetimeMongotoReadable($result['date_added'])
            );
        }

        $this->data['actions'] = array();

        if($client_id){
            $data_filter['client_id'] = $client_id;
            $data_filter['site_id'] = $site_id;
            $this->data['actions'] = $this->Action_model->getActionsSite($data_filter);
        }

        $config['base_url'] = $url.$parameter_url;

        $config['total_rows'] = $report_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);
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

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['filter_date_start'] = $filter_date_start;

        // --> This will show only the date, not including the time
        $filter_date_end_exploded = explode(" ",$filter_date_end);
        $this->data['filter_date_end'] = $filter_date_end_exploded[0];
        // --> end

        $this->data['filter_username'] = $filter_username;
        $this->data['filter_action_id'] = $filter_action_id;

        $this->data['main'] = 'report_action';
        $this->load->vars($this->data);
        $this->render_page('template');
    }
    private function xlsBOF()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }
    private function xlsEOF()
    {
        echo pack("ss", 0x0A, 0x00);
    }
    private function xlsWriteNumber($Row, $Col, $Value)
    {
        echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);echo pack("d", $Value);
    }
    private function xlsWriteLabel($Row, $Col, $Value )
    {
        $L = strlen($Value);echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
        echo $Value;
    }

    function download_send_headers($filename) {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    public function actionDownload() {
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
            'client_id'              => $client_id,
            'site_id'                => $site_id,
            'date_start'	         => $filter_date_start,
            'date_expire'	         => $filter_date_end,
            'username'               => $filter_username,
            'action_id'              => $filter_action_id
        );
        $this->download_send_headers("ActionReport_" . date("YmdHis") . ".xls");
        $this->xlsBOF();
        $this->xlsWriteLabel(0,0,$this->lang->line('column_player_id'));
        $this->xlsWriteLabel(0,1,$this->lang->line('column_username'));
        $this->xlsWriteLabel(0,2,$this->lang->line('column_email'));
        $this->xlsWriteLabel(0,3,$this->lang->line('column_level'));
        $this->xlsWriteLabel(0,4,$this->lang->line('column_exp'));
        $this->xlsWriteLabel(0,5,$this->lang->line('column_action_name'));
        $this->xlsWriteLabel(0,6,$this->lang->line('column_url'));
        $this->xlsWriteLabel(0,7,$this->lang->line('column_date_added'));
        $xlsRow = 1;

        $results = $this->Action_model->getActionReport($data, true);

        foreach($results as $row)
        {
            $player = $this->Player_model->getPlayerById($row['pb_player_id'], $data['site_id']);
            $this->xlsWriteNumber($xlsRow,0,$player['cl_player_id']);
            $this->xlsWriteLabel($xlsRow,1,$player['username']);
            $this->xlsWriteLabel($xlsRow,2,$player['email']);
            $this->xlsWriteLabel($xlsRow,3,$player['level']);
            $this->xlsWriteLabel($xlsRow,4,$player['exp']);
            $this->xlsWriteLabel($xlsRow,5,$row['action_name']);
            $this->xlsWriteLabel($xlsRow,6,$row['url']);
            $this->xlsWriteLabel($xlsRow,7,$this->datetimeMongotoReadable($row['date_added']));
            $xlsRow++;
        }
        $this->xlsEOF();
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

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'report/action')) {
            return true;
        } else {
            return false;
        }
    }
    public function getActionsToDownload(){

        $this->load->helper('php-excel');
        
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
                'client_id' =>$client_id,
                'site_id' => $site_id,
                'username' => $filter_username,
                'date_expire' => $filter_date_end,
                'date_start' => $filter_date_start,
                'action_id' => $filter_action_id,
            );

        $this->load->model('Action_model');
        $this->load->model('Player_model');

        $results = $this->Action_model->getActionsForDownload($data);

        foreach ($results as $row){
            $player_info = $this->Player_model->getPlayerById($row['pb_player_id'], $data['site_id']);
            $data_array[] = array( $player_info['cl_player_id'], $player_info['username'], $player_info['email'],$row['action_name'], $row['url'], date("Y-m-d", $row['date_added']->sec), );
        }

        $xls = new Excel_XML;
        $xls->addArray ($data_array);
        $xls->generateXML ( "output_name" );

    }


}
?>