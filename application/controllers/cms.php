<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class CMS extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('App_model');
        $this->load->model('Client_model');
        $this->load->model('Custompoints_model');
        $this->load->model('CMS_model');
        $this->load->model('User_model');
        $this->load->model('User_group_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("cms", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }


    public function index()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $cms = $this->CMS_model->getCmsInfo($client_id, $site_id);

        $create = isset($cms) != null ? false : true;
        if (!$create) {
            $this->data['link'] = 'https://cms.pbapp.net/' . $cms['site_slug'] . '/login';
        }

        $user_info = $this->User_model->getUserInfo($this->User_model->getId());
        $userGroup = $this->User_group_model->getUserGroupInfo($user_info['user_group_id']);
        $permission = $userGroup['permission'];
        $access = $permission['access'];
        $modify = $permission['modify'];

        $editor = array_search('cms', $modify) != -1 ? true : false;
        $contributor = array_search('cms', $access) != -1 ? true : false;

        if ($editor || $contributor) {
            $this->data['role'] = $editor ? 'editor' : 'contributor';
        }


        $this->data['main'] = 'cms';
        $this->data['create'] = $create;
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->render_page('template');
    }

    public function createCMS()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $user_info = $this->User_model->getUserInfo($this->User_model->getId());
        $site_info = $this->Client_model->getSiteInfo($client_id, $site_id);
        $data = array(
            'client_id' => $this->User_model->getClientId(),
            'site_id' => $this->User_model->getSiteId(),
            'site_name' => $site_info['site_name'],
            'user_id' => $user_info['_id'],
            'user_name' => $user_info['username'],
            'firstname' => $user_info['firstname'],
            'lastname' => $user_info['lastname'],
            'user_email' => $user_info['email']

        );

        $this->CMS_model->createCMS($data);
    }

}

?>