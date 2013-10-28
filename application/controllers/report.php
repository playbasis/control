<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Report extends CI_Controller
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

        $per_page = 100;

        $this->load->library('pagination');

        $this->load->model('Action_model');

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        if ($this->input->get('date_start')) {
            $filter_date_start = $this->input->get('date_start');
        } else {
            $filter_date_start = date("Y-m-d", strtotime("-30 days")); ;
        }

        if ($this->input->get('date_expire')) {
            $filter_date_end = $this->input->get('date_expire');
        } else {
            $filter_date_end = date("Y-m-d"); ;
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

        $report_total = $this->Action_model->getTotalActionReport($data);

        $results = $this->Action_model->getActionReport($data);

        foreach ($results as $result) {

            if (!empty($result['image']) && $result['image'] && ($result['image'] != 'HTTP/1.1 404 Not Found' && $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                $thumb = $result['image'];
            } else {
                $thumb = $this->model_tool_image->resize('no_image.jpg', 40, 40);
            }

            $this->data['reports'][] = array(
                'cl_player_id'      => $result['cl_player_id'],
                'username'          => $result['username'],
                'image'             => $thumb,
                'email'             => $result['email'],
                'exp'               => $result['exp'],
                'level'             => $result['level'],
                'action_name'       => $result['action_name'],
                'url'               => $result['url'],
                'date_added'        => $result['date_added']
            );
        }

        $config['base_url'] = site_url('report/page');

        $config['total_rows'] = $report_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;
        $this->data['filter_username'] = $filter_username;
        $this->data['filter_action_id'] = $filter_action_id;

        $this->data['main'] = 'action';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function page($offset=0) {

    }

    private function xlsBOF()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
        return;
    }
    private function xlsEOF()
    {
        echo pack("ss", 0x0A, 0x00);
        return;
    }
    private function xlsWriteNumber($Row, $Col, $Value)
    {
        echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
        echo pack("d", $Value);
        return;
    }
    private function xlsWriteLabel($Row, $Col, $Value )
    {
        $L = strlen($Value);
        echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
        echo $Value;
        return;
    }

    private function array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
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

        $this->load->language('report/action');
        $this->load->model('report/action');

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

        $client_id = $this->user->getClientId();
        $site_id = $this->user->getSiteId();

        $data = array(
            'client_id'              => $client_id,
            'site_id'                => $site_id,
            'date_start'	         => $filter_date_start,
            'date_expire'	         => $filter_date_end,
            'username'               => $filter_username,
            'action_id'              => $filter_action_id
        );

        $filename = md5(date('YmdH').$filter_date_start.$site_id.$filter_date_end.$filter_username.$filter_action_id).".xlsx";

        $this->download_send_headers("ActionReport_" . date("YmdHis") . ".xls");

        $this->xlsBOF();

        $this->data['column_avatar'] = $this->language->get('column_avatar');
        $this->data['column_player_id'] = $this->language->get('column_player_id');
        $this->data['column_username'] = $this->language->get('column_username');
        $this->data['column_email'] = $this->language->get('column_email');
        $this->data['column_level'] = $this->language->get('column_level');
        $this->data['column_exp'] = $this->language->get('column_exp');
        $this->data['column_action_name'] = $this->language->get('column_action_name');
        $this->data['column_url'] = $this->language->get('column_url');
        $this->data['column_date_added'] = $this->language->get('column_date_added');

        $this->xlsWriteLabel(0,0,$this->language->get('column_player_id'));
        $this->xlsWriteLabel(0,1,$this->language->get('column_username'));
        $this->xlsWriteLabel(0,2,$this->language->get('column_email'));
        $this->xlsWriteLabel(0,3,$this->language->get('column_level'));
        $this->xlsWriteLabel(0,4,$this->language->get('column_exp'));
        $this->xlsWriteLabel(0,5,$this->language->get('column_action_name'));
        $this->xlsWriteLabel(0,6,$this->language->get('column_url'));
        $this->xlsWriteLabel(0,7,$this->language->get('column_date_added'));
        $xlsRow = 1;

        $results = $this->model_report_action->getActionReport($data, true);

        foreach($results as $row)
        {
            $this->xlsWriteNumber($xlsRow,0,$row['cl_player_id']);
            $this->xlsWriteLabel($xlsRow,1,$row['username']);
            $this->xlsWriteLabel($xlsRow,2,$row['email']);
            $this->xlsWriteLabel($xlsRow,3,$row['level']);
            $this->xlsWriteLabel($xlsRow,4,$row['exp']);
            $this->xlsWriteLabel($xlsRow,5,$row['action_name']);
            $this->xlsWriteLabel($xlsRow,6,$row['url']);
            $this->xlsWriteLabel($xlsRow,7,$row['date_added']);
            $xlsRow++;
        }
        $this->xlsEOF();

    }
}
?>