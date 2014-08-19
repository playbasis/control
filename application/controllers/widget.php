<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Widget extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Domain_model');
        $this->load->model('Custompoints_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("widget", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $site_data = $this->Domain_model->getDomainsBySiteId($site_id);
        $points_data = $this->Custompoints_model->getCustompoints($client_id, $site_id);

        $this->data['site_data'] = $site_data;
        $this->data['points_data'] = $points_data;
        $this->data['main'] = 'widget';
        $this->render_page('template');
    }

    public function preview(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $site_data = $this->Domain_model->getDomainsBySiteId($site_id);
        $points_data = $this->Custompoints_model->getCustompoints($client_id, $site_id);

        $this->data['site_data'] = $site_data;
        $this->data['points_data'] = $points_data;
        $this->render_page('widget_preview');
    }

    public function social_login(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $site_data = $this->Domain_model->getDomainsBySiteId($site_id);

        $this->data['site_data'] = $site_data;
//        $this->render_page('widget_social_login');
        $this->data['main'] = 'widget_social_login';
        $this->render_page('template');
    }
}
?>