<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class spin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('player_model');
        $this->load->model('auth_model');
        $this->load->model('Spin_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("spin", $lang['folder']);
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
        $this->data['main'] = 'spin';
        $this->data['form'] = 'spin';
        $this->getList();
    }

    public function action()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'spin';
        $this->data['form'] = 'spin/action';
        $this->error['warning'] = null;

        $this->form_validation->set_rules('min_char', $this->lang->line('entry_min_char'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('max_retries', $this->lang->line('entry_max_retries'),  'trim|numeric|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $data_select = $this->input->post();
                
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                // call engine rule to log action "died"
                $platformData = $this->auth_model->getOnePlatform($client_id, $site_id);

                $data = array(
                    'api_key'    => isset($platformData['api_key'])?$platformData['api_key']:null,
                    'api_secret' => isset($platformData['api_secret'])?$platformData['api_secret']:null
                );
                $token = json_decode(json_encode($this->rest->post('Auth', $data)->response),true)['token'];

                $this->load->library('Rest');


                if(isset($data_select['selected']) && $data_select['selected']) foreach ($data_select['selected'] as $index => $player){
                    $action = explode(',',$player);
                    $pb_player_id = $this->player_model->getPlaybasisId(array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'cl_player_id' => $action[0]
                    ));
                    if ($pb_player_id) {
                        for($i=0; $i< intval($action[1]); $i++){
                            $this->rest->post('Engine/rule',
                                array(
                                    'token' => $token,
                                    'player_id' => $action[0],
                                    'action' => "manual-spin",
                                )
                            );
                        }
                    }
                }
            }
        }

        $this->getList();
    }

    private function getList()
    {
        $this->data['main'] = 'spin';

        $player_action_info =array();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name'])) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != '') {

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

                if(is_null($this->data['message'])){
                    $handle = fopen($_FILES['file']['tmp_name'], "r");
                    if (!$handle) {
                        $this->data['message'] = $this->lang->line('error_upload');
                    } else {
                        while (($line = fgets($handle)) !== false) {
                            $line = trim($line);
                            if (empty($line) || $line == ',') {
                                continue;
                            } // skip empty line
                            $obj = explode(',', $line);
                            $name = trim($obj[0]);
                            $n = trim(isset($obj[1]) ? $obj[1] : $name);
                            array_push($player_action_info, array('_id' => $name, 'manual_grant' => $n));
                        }
                    }
                    $_FILES = null;
                }
            }
        }
        /*
                $filter = array();
                if($this->input->get('date_start') && $this->input->get('date_end')){
                    $filter['date_start'] = $this->input->get('date_start') ;
                    $date = $this->input->get('date_end');
                    $currentDate = strtotime($date);
                    $futureDate = $currentDate + ("86399");
                    $filter_date_end = date("Y-m-d H:i:s", $futureDate);
                    $filter['date_end'] = $filter_date_end;
                }

                $player_action_info = $this->Spin_model->getPlayerAction($client_id, $site_id, $filter);
                $player_spin_info = $this->Spin_model->getPlayerSpin($client_id, $site_id, $filter);
                foreach ($player_action_info as $n => &$player){
                    $index = array_search($player['_id'], array_column($player_spin_info, '_id'));
                    if($index) {
                        $player['out_standing_spin'] = intval($player['n']) - intval($player_spin_info[$index]['n']);
                        if(intval($player['n']) - intval($player_spin_info[$index]['n']) <= 0){
                            unset($player_action_info[$n]);
                        }
                    } else {
                        $player['out_standing_spin'] = $player['n'];
                    }
                }

                if($this->input->get('date_start')){
                    $this->data['filter_date_start'] = $this->input->get('date_start');
                } else {
                    $this->data['filter_date_start'] = null;
                }

                if($this->input->get('date_end')){
                    $this->data['filter_date_end'] = $this->input->get('date_end');
                } else {
                    $this->data['filter_date_end'] = null;
                }

                $player_action_info = array_values($player_action_info);
                */
        $this->data['report'] = $player_action_info;
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'spin')) {
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
                'custompoints') && $this->Feature_model->getFeatureExistByClientId($client_id, 'spin')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
