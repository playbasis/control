<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('App_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }
        $this->_api = $this->playbasisapi;

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("data", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index() {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'data';
        $this->data['form'] = 'data/import';
        $this->getList();
    }

    public function insert() {

    }

    public function update() {

    }

    public function delete() {

    }
    public function import() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'data';
        $this->data['form'] = 'data/import/';
        $this->error['warning'] = null;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['message'] = null;
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name'])) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if(isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != '') {

                $maxsize    = 2097152;
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

                if(($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
                    $this->data['message'] = $this->lang->line('error_file_too_large');
                }

                if(!in_array($_FILES['file']['type'], $csv_mimetypes) && (!empty($_FILES["file"]["type"]))) {
                    $this->data['message'] = $this->lang->line('error_type_accepted');
                }

                $handle = fopen($_FILES['file']['tmp_name'], "r");
                if (!$handle) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }
            }
            if($this->form_validation->run() && $this->data['message'] == null) {

                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);

                    $data = array();
                    $params = explode(',',$line);
                    $date = "now";
                    foreach ($params as $param){
                        $keyAndValue =   explode(':',$param);
                        $key = $keyAndValue[0];
                        $value = $keyAndValue[1];
                        if (strtolower($key) == "player_id"){
                            $player_id = $value;
                        }
                        elseif (strtolower($key) == "action"){
                            $action = $value;
                        }
                        elseif (strtolower($key) == "date"){
                            $date = $value;
                        }
                        else{
                            $data = array_merge($data, array($key => $value) );
                        }
                    }
                    $result = $this->postEngineRule($player_id,$action, $data,$date);
                    if ($result !== "Success"){
                        $this->data['message'] = $result;
                        break;
                    }
                }
                if ($result == "Success"){
                    $this->data['success'] = $this->lang->line('text_success') ;
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/data','refresh');
                }
            }
        }

        $this->getList();
    }

    private function getList() {


        $config['base_url'] = site_url('data');
        if (!isset($this->data['success'])) {
            $this->data['success'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }



    private function validateModify() {
        if ($this->User_model->hasPermission('modify', 'data')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess() {
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'data') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'data')) {
            return true;
        } else {
            return false;
        }
    }

    private function getToken() {
        // get api key and secret

        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId()));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        $this->_api->set_api_key($platform['api_key']);
        $this->_api->set_api_secret($platform['api_secret']);

        $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
        $result = $this->_api->auth($pkg_name);
        return $result->response->token;
    }
    private function postEngineRule($player_id, $action, $param, $date = "now") {
        $this->getToken();
        if ($date != "now"){
            $this->_api->setHeader('Date',$date);
        }
        $result = $this->_api->engine($player_id,$action, $param);
        return $result->message;
    }

}
