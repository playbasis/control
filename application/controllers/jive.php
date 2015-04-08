<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Jive extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Jive_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("jive", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->_api = $this->jiveapi;
    }

    public function index() {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        if ($this->Jive_model->hasValidRegistration($this->User_model->getSiteId())) {
            $this->data['jive'] = $this->Jive_model->getJiveRegistration($this->User_model->getSiteId());
        }

        $this->data['main'] = 'jive_setup';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function download() {
        $this->load->library('zip');
        $this->zip->add_data('data/extension-16.png', fread(fopen('image/default-image-16.png', 'r'), filesize('image/default-image-16.png')));
        $this->zip->add_data('data/extension-48.png', fread(fopen('image/default-image-48.png', 'r'), filesize('image/default-image-48.png')));
        $this->zip->add_data('data/extension-128.png', fread(fopen('image/default-image-128.png', 'r'), filesize('image/default-image-128.png')));
        $this->zip->add_data('definition.json', json_encode(array('integrationUser' => array("systemAdmin" => false))));
        $this->zip->add_data('i18n/en.properties', '');
        $this->zip->add_data('meta.json', json_encode(array(
            "package_version" => "1.0",
            "minimum_version" => "0000",
            "id" => "1f6ff58b-b15b-43da-95fd-e7b34efc14d9",
            "uuid" => "1f6ff58b-b15b-43da-95fd-e7b34efc14d9",
            "type" => "client-app",
            "name" => "Playbasis Events Listener",
            "description" => "This add-on is to allow Playbasis to capture Jive events",

            "author" => "Thanakij Pechprasarn",
            "author_affiliation" => "Playbasis Inc",
            "author_email" => "pechpras@playbasis.com",

            "service_url" => API_SERVER,
            //"service_url" => 'https://api.pbapp.net',
            "redirect_url" => base_url()."/jive/authorize",
            "register_url" => "%serviceURL%/notification/".$this->User_model->getSiteId()->{'$id'},
            "unregister_url" => "%serviceURL%/notification/".$this->User_model->getSiteId()->{'$id'},

            "icon_16" => "extension-16.png",
            "icon_48" => "extension-48.png",
            "icon_128" => "extension-128.png",

            "website_url" => "http://playbasis.com",
            "info_email" => "info@playbasis.com",
            "status" => "available",
            "released_on" => date('Y-m-d\TH:i:s.000O')
        )));
        $this->zip->archive(tempnam(sys_get_temp_dir(), 'Jive'));
        $this->zip->download('Playbasis-events-listener.zip');
    }

    public function authorize() {
        $code = $this->input->get('code');
        if (!empty($code)) {
            $jive = $this->Jive_model->getJiveRegistration($this->User_model->getSiteId());
            $this->_api->initialize($jive['jive_url']);
            $token = $this->_api->newToken($jive['jive_client_id'], $jive['jive_client_secret'], $code);
            if ($token) $this->Jive_model->updateToken($this->User_model->getSiteId(), (array)$token);
        }
        redirect('/jive', 'refresh');
    }

    public function place($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        if ($this->Jive_model->hasToken($this->User_model->getSiteId())) {
            $jive = $this->Jive_model->getJiveRegistration($this->User_model->getSiteId());
            try {
                $this->_api->initialize($jive['jive_url'], $jive['token']['access_token']);
            } catch (Exception $e) {
                if ($e->getMessage() == 'TOKEN_EXPIRED') {
                    $token = $this->_api->refreshToken($jive['jive_client_id'], $jive['jive_client_secret'], $jive['token']['refresh_token']);
                    if ($token) {
                        $this->Jive_model->updateToken($this->User_model->getSiteId(), (array)$token);
                        $this->_api->initialize($jive['jive_url'], $token->access_token); // re-initialize with new token
                    }
                }
            }

            if ($this->input->post('selected')) {
                foreach ($this->input->post('selected') as $placeId) {
                    $this->_api->createWebhook($placeId);
                }
                $this->session->set_flashdata('success', $this->lang->line('text_success_watch'));
            }

            $this->data['jive'] = $jive;
            $this->session->set_userdata('total_places', $this->_api->totalPlaces());
            $this->getListPlaces($offset);
        } else {
            $this->data['main'] = 'jive_place';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function event() {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        if ($this->Jive_model->hasToken($this->User_model->getSiteId())) {
            $jive = $this->Jive_model->getJiveRegistration($this->User_model->getSiteId());
            $this->data['jive'] = $jive;
        }
        $this->data['events'] = array(
            array('id' => 'jive:user_account_created', 'type' => 'user_account', 'description' => 'User account has been created'),
            array('id' => 'jive:user_account_deleted', 'type' => 'user_account', 'description' => 'User account has been deleted'),
            array('id' => 'jive:user_account_disabled', 'type' => 'user_account', 'description' => 'User account has been disabled'),
            array('id' => 'jive:user_account_enabled', 'type' => 'user_account', 'description' => 'User account has been enabled'),
            array('id' => 'jive:user_account_invisible', 'type' => 'user_account', 'description' => 'User account has been invisible'),
            array('id' => 'jive:user_account_visible', 'type' => 'user_account', 'description' => 'User account has been visible'),
            array('id' => 'jive:user_profile_modified', 'type' => 'user_account', 'description' => 'User profile has been modified'),
            array('id' => 'jive:user_type_modified', 'type' => 'user_account', 'description' => 'User type has been modified'),
            array('id' => 'jive:user_session_login', 'type' => 'user_session', 'description' => 'User has logged in'),
            array('id' => 'jive:user_session_logout', 'type' => 'user_session', 'description' => 'User has logged out'),
            array('id' => 'jive:user_membership_added', 'type' => 'user_membership', 'description' => 'User membership has been added'),
            array('id' => 'jive:user_membership_removed', 'type' => 'user_membership', 'description' => 'User membership has been removed'),
            array('id' => 'jive:social_group_created', 'type' => 'social_group', 'description' => 'Social group has been created'),
            array('id' => 'jive:social_group_renamed', 'type' => 'social_group', 'description' => 'Social group has been renamed'),
            array('id' => 'jive:social_group_deleted', 'type' => 'social_group', 'description' => 'Social group has been deleted'),
            array('id' => 'jive:stream_config_created', 'type' => 'stream', 'description' => 'Stream config has been created'),
            array('id' => 'jive:stream_config_modified', 'type' => 'stream', 'description' => 'Stream config has been modified'),
            array('id' => 'jive:stream_config_deleted', 'type' => 'stream', 'description' => 'Stream config has been deleted'),
            array('id' => 'jive:stream_association_added', 'type' => 'stream', 'description' => 'Stream association has been added'),
            array('id' => 'jive:stream_association_removed', 'type' => 'stream', 'description' => 'Stream association has been removed'),
            array('id' => 'jive:webhook_created', 'type' => 'webhook', 'description' => 'Webhook has been created'),
            array('id' => 'jive:webhook_deleted', 'type' => 'webhook', 'description' => 'Webhook has been deleted'),
            array('id' => 'jive:webhook_enabled', 'type' => 'webhook', 'description' => 'Webhook has been enabled'),
            array('id' => 'jive:webhook_disabled', 'type' => 'webhook', 'description' => 'Webhook has been disabled'),
        );
        $this->data['main'] = 'jive_event';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function places($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $jive = $this->Jive_model->getJiveRegistration($this->User_model->getSiteId());
        try {
            $this->_api->initialize($jive['jive_url'], $jive['token']['access_token']);
        } catch (Exception $e) {
            if ($e->getMessage() == 'TOKEN_EXPIRED') {
                $token = $this->_api->refreshToken($jive['jive_client_id'], $jive['jive_client_secret'], $jive['token']['refresh_token']);
                if ($token) {
                    $this->Jive_model->updateToken($this->User_model->getSiteId(), (array)$token);
                    $this->_api->initialize($jive['jive_url'], $token->access_token); // re-initialize with new token
                }
            }
        }
        $this->data['jive'] = $jive;
        $this->data['offset'] = $offset;
        $this->getListPlaces($offset);
    }

    private function getListPlaces($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('jive/places');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['templates'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();

        $places = $this->_api->listPlaces($per_page, $offset);
        if ($this->session->userdata('total_places') === false) $this->session->set_userdata('total_places', $this->_api->totalPlaces());
        $total = $this->session->userdata('total_places');

        foreach ($places->list as $place) {
            $this->data['places'][] = array(
                'placeID' => $place->placeID,
                'name' => $place->name,
                'description' => isset($place->description) ? $place->description : '',
                'type' => $place->type,
                'followerCount' => $place->followerCount,
                'viewCount' => $place->viewCount,
                'creator' => isset($place->creator) ? (isset($place->creator->displayName) ? $place->creator->displayName : $place->creator->id) : '',
                'status' => $place->status,
                'selected' => ($this->input->post('selected') && in_array($place->placeID, $this->input->post('selected'))),
            );
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;

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

        $this->data['main'] = 'jive_place';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {
        if ($this->User_model->hasPermission('modify', 'jive')) {
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

        if ($this->User_model->hasPermission('access', 'jive') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'jive')) {
            return true;
        } else {
            return false;
        }
    }
}
?>