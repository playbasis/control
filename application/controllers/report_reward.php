<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Report_reward extends MY_Controller{

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

    public function index(){
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList(0, site_url('report_reward/page'));
    }

    public function page($offset = 0){
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList($offset, site_url('report_reward/page'));
    }

    public function reward_badge() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList(0, site_url('report_reward/page'));
    }

    public function reward_badge_page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getRewardsList($offset, site_url('report_reward/page'));
    }

    public function getRewardsList($offset, $url){
        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = 100;
        $parameter_url = "?t=".rand();

        $this->load->library('pagination');

        $this->load->model('Report_reward_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load("action", $lang['folder']);

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=".$filter_date_start;
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days")); ;
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=".$filter_date_end;
        } else {
            $filter_date_end = date("Y-m-d"); ;
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

        

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id'              => $client_id,
            'site_id'                => $site_id,
            'date_start'             => $filter_date_start,
            'date_expire'            => $filter_date_end,
            'username'               => $filter_username,
            'action_id'              => $filter_action_id,
            'start'                  => $offset,
            'limit'                  => $limit
        );

        $report_total = 0;

        $results = array();
        if($client_id){
            $report_total = $this->Report_reward_model->getTotalReportReward($data);

            $results = $this->Report_reward_model->getReportReward($data);
        }

        $this->data['reports'] = array();

        foreach ($results as $result) {

            $budget_name = null;
            $reward_name = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if (!empty($player['image']) && $player['image'] && ($player['image'] != 'HTTP/1.1 404 Not Found' && $player['image'] != 'HTTP/1.0 403 Forbidden')) {
                $thumb = $player['image'];
            } else {
                $thumb = $this->Image_model->resize('no_image.jpg', 40, 40);
            }

            if($result['reward_id'] != null){
                $reward_name = $this->Report_reward_model->getRewardName($result['reward_id']);
            }else{
                $this->load->model('Badge_model');
                $badge_info = $this->Badge_model->getBadge($result['badge_id']);
                $badge_name = $badge_info['name'];
            }

            $this->data['reports'][] = array(
                'cl_player_id'      => $player['cl_player_id'],
                'username'          => $player['username'],
                'image'             => $thumb,
                'email'             => $player['email'],
                'date_added'        => datetimeMongotoReadable($result['date_added']),
                'reward_name'       => isset($reward_name)?$reward_name:null,
                'badge_name'        => isset($badge_name)?$badge_name:null,
                'value'             => $result['value']
            );
        }

        $this->data['actions'] = array();

        if($client_id){
            $data_filter['client_id'] = $client_id;
            $data_filter['site_id'] = $site_id;
            // $this->data['actions'] = $this->Action_model->getActionsSite($data_filter);

            $badges_reward = $this->Report_reward_model->getRewardsBadgesSite($data_filter);

            $all_badges_reward = array();
            foreach($badges_reward as $br){
                if(isset($br['reward_id']) && $br['reward_id']!=null){
                    $reward = $this->Report_reward_model->getRewardName($br['reward_id']);
                    if(!in_array($reward, $all_badges_reward)){
                        $all_badges_reward[] = $this->Report_reward_model->getRewardName($br['reward_id']);
                    }
                }

                if(isset($br['badge_id']) && $br['badge_id']!=null){
                    $this->load->model('Badge_model');
                    $badge_info = $this->Badge_model->getBadge($br['badge_id']);
                    if(!in_array($badge_info, $all_badges_reward)){
                        $all_badges_reward [] = $badge_info;    
                    }
                }
            }
            $this->data['badge_rewards'] = $all_badges_reward;

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
        $this->data['filter_date_end'] = $filter_date_end;
        $this->data['filter_username'] = $filter_username;
        $this->data['filter_action_id'] = $filter_action_id;

        $this->data['main'] = 'report_reward';
        $this->load->vars($this->data);
        $this->render_page('template');

    }

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'report')) {
            return true;
        } else {
            return false;
        }
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

        $parameter_url = "?t=".rand();
        $this->load->model('Report_reward_model');
        $this->load->model('Image_model');
        $this->load->model('Player_model');

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
            $parameter_url .= "&date_start=".$filter_date_start;
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days")); ;
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
            $parameter_url .= "&date_expire=".$filter_date_end;
        } else {
            $filter_date_end = date("Y-m-d"); ;
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

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id'              => $client_id,
            'site_id'                => $site_id,
            'date_start'             => $filter_date_start,
            'date_expire'            => $filter_date_end,
            'username'               => $filter_username,
            'action_id'              => $filter_action_id,
        );

        $report_total = 0;

        $results = array();

        if($client_id){
            $report_total = $this->Report_reward_model->getTotalReportReward($data);

            $results = $this->Report_reward_model->getReportReward($data);
        }

        $this->data['reports'] = array();

        foreach ($results as $result) {

            $budget_name = null;
            $reward_name = null;

            $player = $this->Player_model->getPlayerById($result['pb_player_id'], $data['site_id']);

            if (!empty($player['image']) && $player['image'] && ($player['image'] != 'HTTP/1.1 404 Not Found' && $player['image'] != 'HTTP/1.0 403 Forbidden')) {
                $thumb = $player['image'];
            } else {
                $thumb = $this->Image_model->resize('no_image.jpg', 40, 40);
            }

            if($result['reward_id'] != null){
                $reward_name = $this->Report_reward_model->getRewardName($result['reward_id']);
            }else{
                $this->load->model('Badge_model');
                $badge_info = $this->Badge_model->getBadge($result['badge_id']);
                $badge_name = $badge_info['name'];
            }

            $this->data['reports'][] = array(
                'cl_player_id'      => $player['cl_player_id'],
                'username'          => $player['username'],
                'image'             => $thumb,
                'email'             => $player['email'],
                'date_added'        => datetimeMongotoReadable($result['date_added']),
                'reward_name'       => isset($reward_name)?$reward_name:null,
                'badge_name'        => isset($badge_name)?$badge_name:null,
                'value'             => $result['value']
            );
        }
        $results = $this->data['reports'];
       
        $this->download_send_headers("ActionReport_" . date("YmdHis") . ".xls");
        $this->xlsBOF();
        $this->xlsWriteLabel(0,0,$this->lang->line('column_player_id'));
        $this->xlsWriteLabel(0,1,$this->lang->line('column_username'));
        $this->xlsWriteLabel(0,2,$this->lang->line('column_email'));
        $this->xlsWriteLabel(0,3,$this->lang->line('column_reward_name'));
        $this->xlsWriteLabel(0,4,$this->lang->line('column_reward_value'));
        $this->xlsWriteLabel(0,5,$this->lang->line('column_date_added'));
        $xlsRow = 1;
        
        foreach($results as $row)
        {

            if($row['badge_name'] != null){
                $badge_name = $row['badge_name'];
            }else{
                $reward_name = $row['reward_name']['name'];
            }

            $this->xlsWriteNumber($xlsRow,0,$row['cl_player_id']);
            $this->xlsWriteLabel($xlsRow,1,$row['username']);
            $this->xlsWriteLabel($xlsRow,2,$row['email']);
            $this->xlsWriteLabel($xlsRow,3,isset($badge_name)?$badge_name:$reward_name);
            $this->xlsWriteLabel($xlsRow,4,$row['value']);
            $this->xlsWriteLabel($xlsRow,5,$row['date_added']);
            $xlsRow++;
        }

        $this->xlsEOF();
    }



}

?>