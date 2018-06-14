<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class setting extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Setting_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("setting", $lang['folder']);
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
        $this->data['main'] = 'setting';
        $this->data['form'] = 'setting/edit';
        $this->getList();
    }

    public function insert()
    {

    }

    public function delete()
    {

    }

    public function edit()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'setting';
        $this->data['form'] = 'setting/edit/';
        $this->error['warning'] = null;

        $this->form_validation->set_rules('min_char', $this->lang->line('entry_min_char'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('max_retries', $this->lang->line('entry_max_retries'),  'trim|numeric|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['app_status'] = (isset($data['app_status']) && $data['app_status'] == "true") ? true : false;
                if(isset($data['app_period']['date_start']) && $data['app_period']['date_start'] && isset($data['app_period']['date_end']) && $data['app_period']['date_end']){
                    if($data['app_period']['date_start'] > $data['app_period']['date_end']){
                        $this->data['message'] = $this->lang->line('error_date');
                    }
                    $data['app_period']['date_start'] = new MongoDate(strtotime($data['app_period']['date_start']));
                    $data['app_period']['date_end'] = new MongoDate(strtotime($data['app_period']['date_end']));
                }else{
                    if((isset($data['app_period']['date_start']) && $data['app_period']['date_start']) ||
                       (isset($data['app_period']['date_end']) && $data['app_period']['date_end'])){
                        $this->data['message'] = $this->lang->line('error_date');
                    }
                    $data['app_period'] = null;
                }

                $data['password_policy_enable'] = (isset($data['password_policy_enable']) && $data['password_policy_enable'] == "true") ? true : false;

                $data['password_policy']['alphabet'] = (isset($data['password_policy']['alphabet']) && $data['password_policy']['alphabet'] == "on") ? true : false;
                $data['password_policy']['numeric'] = (isset($data['password_policy']['numeric']) && $data['password_policy']['numeric'] == "on") ? true : false;
                $data['password_policy']['user_in_password'] = (isset($data['password_policy']['user_in_password']) && $data['password_policy']['user_in_password'] == "on") ? true : false;
                $data['password_policy']['min_char'] = isset($data['password_policy']['min_char']) ? intval($data['password_policy']['min_char']) : 0;
                $data['max_retries'] = isset($data['max_retries']) ? intval($data['max_retries']) : 0;
                $data['email_verification_enable'] = (isset($data['email_verification_enable']) && $data['email_verification_enable'] == "on") ? true : false;
                $data['player_authentication_enable'] = (isset($data['player_authentication_enable']) && $data['player_authentication_enable'] == "on") ? true : false;
                $data['goods_alert_enabled'] = (isset($data['goods_alert_enabled']) && $data['goods_alert_enabled'] == "true") ? true : false;
                $data['timeout'] = $this->wordToTime($data['timeout']);
                if(is_null($this->data['message'])) {
                    if($data['goods_alert_enabled'] == true && !isset($data['goods_alert_users'])){
                        $this->data['message'] = $this->lang->line('error_alert_set_users');
                    }
                }

                if(is_null($this->data['message'])) {
                    $insert = $this->Setting_model->updateSetting($data);
                    if ($insert) {
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        redirect('/setting', 'refresh');
                    }
                }
            }
        }

        $this->getList();
    }

    private function getList()
    {
        $config['base_url'] = site_url('setting');
        if (!isset($this->data['success'])) {
            $this->data['success'] = '';
        }
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['client_id'] = $client_id;
        $this->data['site_id'] = $site_id;
        $setting = $this->Setting_model->retrieveSetting($this->data);
        if (is_array($setting)) {
            $this->data = array_merge($this->data, $setting);
        } else {
            $this->data['password_policy_enable'] = true;
        }

        if(!isset($this->data['app_status'])){
            $this->data['app_status'] = true;
        }
        $this->data['timeout'] = isset($setting['timeout']) ? $this->timeToWord($setting['timeout']) : null;
        $timeout_list = array(60, 300, 900, 3600, 7200, 86400, 604800, 1209600, -1);
        $this->data['timeout_list'] = array();
        foreach ($timeout_list as $key => $time) {
            $this->data['timeout_list'][$key] = $this->timeToWord($time);
        }

        $this->data['goods_alert_enabled'] = isset($setting['goods_alert_enabled']) ? $setting['goods_alert_enabled'] : false;
        $this->data['goods_alert_users'] = array();
        if(isset($setting['goods_alert_users']) && $setting['goods_alert_users']){
            $user_ids = $this->User_model->getUserByClientId(array('client_id' => $client_id));
            foreach ($user_ids as $user_id){
                $user_info = $this->User_model->getById($user_id['user_id']);
                $user_info['alert_active'] = (in_array($user_info['_id']."", $setting['goods_alert_users'])) ? true : false;
                $this->data['goods_alert_users'][] = $user_info;
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }


    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'setting')) {
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
                'setting') && $this->Feature_model->getFeatureExistByClientId($client_id, 'setting')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function timeToWord($time)
    {
        if ($time == -1) {
            return "Forever";
        }
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($time / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
        return $print;
    }

    private function wordToTime($word)
    {
        if ($word == "Forever") {
            return -1;
        }
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        $word_array = explode(" ", $word);
        $unit = (substr($word_array[1], -1) == 's') ? rtrim($word_array[1], 's') : $word_array[1];
        $value = intval($word_array[0]);
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if ($name == strtolower($unit)) {
                break;
            }
        }

        $time = $value * $seconds;
        return $time;
    }
}
