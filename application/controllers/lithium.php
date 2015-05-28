<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Lithium extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Lithium_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("lithium", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->_api = null;
        if ($this->Lithium_model->hasValidRegistration($this->User_model->getSiteId())) {
            $this->_api = $this->lithiumapi;
            $lithium = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
            if (isset($lithium['token']['access_token'])) { // $this->Lithium_model->hasToken($this->User_model->getSiteId())
                try {
                    $this->_api->initialize($lithium['lithium_url'], $lithium['token']['access_token']);
                } catch (Exception $e) {
                    if ($e->getMessage() == 'TOKEN_EXPIRED') {
                        $token = $this->_api->refreshToken($lithium['lithium_client_id'], $lithium['lithium_client_secret'], $lithium['token']['refresh_token']);
                        if ($token) {
                            $this->Lithium_model->updateToken($this->User_model->getSiteId(), (array)$token);
                            $this->_api->initialize($lithium['lithium_url'], $token->access_token); // re-initialize with new token
                        }
                    }
                }
            } else if (!empty($lithium['lithium_username'])) {
                $this->_api->initialize($lithium['lithium_url']);
                if (!empty($lithium['http_auth_username'])) $this->_api->setHttpAuth('basic', $lithium['http_auth_username'], $lithium['http_auth_password']);
                $this->_api->login($lithium['lithium_username'], $lithium['lithium_password']);
            }
        }
    }

    public function index() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'lithium/';

        $this->form_validation->set_rules('lithium_url', $this->lang->line('entry_lithium_url'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('lithium_username', $this->lang->line('entry_lithium_username'), 'trim|required|xss_clean|max_length[160]');
        $this->form_validation->set_rules('lithium_password', $this->lang->line('entry_lithium_password'), 'trim|required|xss_clean');

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                if (!$this->Lithium_model->hasValidRegistration($this->User_model->getSiteId())) {
                    $this->Lithium_model->insertRegistration($this->User_model->getSiteId(), $this->input->post());
                } else {
                    $this->Lithium_model->updateRegistration($this->User_model->getSiteId(), $this->input->post());
                }
            }
        }

        $this->getForm($this->Lithium_model->getRegistration($this->User_model->getSiteId()));
    }

    public function setup() {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        if ($this->Lithium_model->hasValidRegistration($this->User_model->getSiteId())) {
            $this->data['lithium'] = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
        }
        $this->data['main'] = 'lithium_setup';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function authorize() {
        $code = $this->input->get('code');
        if (!empty($code)) {
            $lithium = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
            $this->_api->initialize($lithium['lithium_url']);
            $token = $this->_api->newToken($lithium['lithium_client_id'], $lithium['lithium_client_secret'], $code);
            if ($token) $this->Lithium_model->updateToken($this->User_model->getSiteId(), (array)$token);
        }
        redirect('/lithium', 'refresh');
    }

    public function event($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        /* POST */
        if ($this->input->post('selected')) {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('/lithium/events'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->_api) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $eventId) {
                    try {
                        $this->_api->subscribeEvent($this->Lithium_model->getEventType($eventId), $eventId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_watch_event'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/lithium/events'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['lithium'] = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
            $this->session->set_userdata('total_events', $this->Lithium_model->totalEvents($this->User_model->getSiteId()));
            $this->getListEvents($offset);
        } else {
            $this->data['main'] = 'lithium_event';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function subscription($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        /* POST */
        if ($this->input->post('selected')) {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('/lithium/subscriptions'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->Lithium_model->hasToken($this->User_model->getSiteId())) {
            $lithium = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
            try {
                $this->_api->initialize($lithium['lithium_url'], $lithium['token']['access_token']);
            } catch (Exception $e) {
                if ($e->getMessage() == 'TOKEN_EXPIRED') {
                    $token = $this->_api->refreshToken($lithium['lithium_client_id'], $lithium['lithium_client_secret'], $lithium['token']['refresh_token']);
                    if ($token) {
                        $this->Lithium_model->updateToken($this->User_model->getSiteId(), (array)$token);
                        $this->_api->initialize($lithium['lithium_url'], $token->access_token); // re-initialize with new token
                    }
                }
            }

            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $subscriptionId) {
                    try {
                        $this->_api->unsubscribeEvent($subscriptionId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/lithium/subscriptions'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['lithium'] = $lithium;
            $this->session->set_userdata('total_subscriptions', $this->_api->totalSubscriptions());
            $this->getListSubscriptions($offset);
        } else {
            $this->data['main'] = 'lithium_subscription';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function events($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $lithium = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
        try {
            $this->_api->initialize($lithium['lithium_url'], $lithium['token']['access_token']);
        } catch (Exception $e) {
            if ($e->getMessage() == 'TOKEN_EXPIRED') {
                $token = $this->_api->refreshToken($lithium['lithium_client_id'], $lithium['lithium_client_secret'], $lithium['token']['refresh_token']);
                if ($token) {
                    $this->Lithium_model->updateToken($this->User_model->getSiteId(), (array)$token);
                    $this->_api->initialize($lithium['lithium_url'], $token->access_token); // re-initialize with new token
                }
            }
        }
        $this->data['lithium'] = $lithium;
        $this->getListEvents($offset);
    }

    private function getListEvents($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('lithium/events');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $events = $this->Lithium_model->listEvents($this->User_model->getSiteId(), $per_page, $offset);
        if ($this->session->userdata('total_events') === false) $this->session->set_userdata('total_events', $this->Lithium_model->totalEvents($this->User_model->getSiteId()));
        $total = $this->session->userdata('total_events');

        foreach ($events as $event) {
            $this->data['events'][] = array_merge($event, array('selected' => ($this->input->post('selected') && in_array($event['id'], $this->input->post('selected')))));
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

        $this->data['main'] = 'lithium_event';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function subscriptions($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $lithium = $this->Lithium_model->getRegistration($this->User_model->getSiteId());
        try {
            $this->_api->initialize($lithium['lithium_url'], $lithium['token']['access_token']);
        } catch (Exception $e) {
            if ($e->getMessage() == 'TOKEN_EXPIRED') {
                $token = $this->_api->refreshToken($lithium['lithium_client_id'], $lithium['lithium_client_secret'], $lithium['token']['refresh_token']);
                if ($token) {
                    $this->Lithium_model->updateToken($this->User_model->getSiteId(), (array)$token);
                    $this->_api->initialize($lithium['lithium_url'], $token->access_token); // re-initialize with new token
                }
            }
        }
        $this->data['lithium'] = $lithium;
        $this->getListSubscriptions($offset);
    }

    private function getListSubscriptions($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('lithium/subscriptions');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $subscriptions = $this->_api->listSubscriptions($per_page, $offset);
        if ($this->session->userdata('total_subscriptions') === false) $this->session->set_userdata('total_subscriptions', $this->_api->totalSubscriptions());
        $total = $this->session->userdata('total_subscriptions');

        foreach ($subscriptions->list as $subscription) {
            $this->data['subscriptions'][] = array(
                'subscriptionID' => $subscription->id,
                'events' => $subscription->events,
                'object' => $subscription->object,
                'callback' => $subscription->callback,
                'selected' => ($this->input->post('selected') && in_array($subscription->subscriptionID, $this->input->post('selected'))),
            );
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

        $this->data['main'] = 'lithium_subscription';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getForm($info = null) {
        if ($this->input->post('lithium_url')) {
            $this->data['lithium_url'] = $this->input->post('lithium_url');
        } elseif (!empty($info)) {
            $this->data['lithium_url'] = $info['lithium_url'];
        } else {
            $this->data['lithium_url'] = '';
        }

        if ($this->input->post('lithium_username')) {
            $this->data['lithium_username'] = $this->input->post('lithium_username');
        } elseif (!empty($info)) {
            $this->data['lithium_username'] = $info['lithium_username'];
        } else {
            $this->data['lithium_username'] = '';
        }

        if ($this->input->post('lithium_password')) {
            $this->data['lithium_password'] = $this->input->post('lithium_password');
        } elseif (!empty($info)) {
            $this->data['lithium_password'] = $info['lithium_password'];
        } else {
            $this->data['lithium_password'] = '';
        }

        if ($this->input->post('http_auth_username')) {
            $this->data['http_auth_username'] = $this->input->post('http_auth_username');
        } elseif (!empty($info)) {
            $this->data['http_auth_username'] = $info['http_auth_username'];
        } else {
            $this->data['http_auth_username'] = '';
        }

        if ($this->input->post('http_auth_password')) {
            $this->data['http_auth_password'] = $this->input->post('http_auth_password');
        } elseif (!empty($info)) {
            $this->data['http_auth_password'] = $info['http_auth_password'];
        } else {
            $this->data['http_auth_password'] = '';
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'lithium_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {
        if ($this->User_model->hasPermission('modify', 'lithium')) {
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

        if ($this->User_model->hasPermission('access', 'lithium') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'lithium')) {
            return true;
        } else {
            return false;
        }
    }
}
?>