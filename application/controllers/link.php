<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Link extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Permission_model');
        $this->load->model('App_model');
        $this->load->model('Link_model');

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
        $this->lang->load("link", $lang['folder']);


        /* initialize $this->api */
       /* $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(),
            $this->User_model->getSiteId());
        $this->_api = $this->playbasisapi;
        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId(),
        ));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        if ($platform) {
            $this->_api->set_api_key($result['api_key']);
            $this->_api->set_api_secret($result['api_secret']);
            $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
            $this->_api->auth($pkg_name);
        }*/
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
        $this->data['main'] = 'link';
        $this->data['form'] = 'link';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['error_warning'] = '';
        $this->data['success'] = '';

        $link_config = $this->Link_model->getConfig($client_id, $site_id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['error_warning'] = $this->lang->line('error_permission');
            } else {

                $data = $this->input->post();


                if($link_config){
                    $result = $this->Link_model->updateConfig($client_id, $site_id, $data['type'], $data['key']);

                }else{
                    $result = $this->Link_model->setConfig($client_id, $site_id, $data['type'], $data['key']);
                }

                if ($result) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/link', 'refresh');
                }else{
                    $this->data['error_warning'] = $this->lang->line('text_fail');
                    $link_config = array('type'=> $data['type'] , 'key'=> $data['key']);
                }
            }
        }

        $this->getConfig($link_config);
    }

    private function getConfig($link_config = null)
    {
        if($link_config){
            $this->data['link_type'] = $link_config['type'];
            $this->data['link_key'] = $link_config['key'];
        }else{
            $this->data['link_type'] = "";
            $this->data['link_key'] = "";
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'link')) {
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
                'link') && $this->Feature_model->getFeatureExistByClientId($client_id, 'link')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
