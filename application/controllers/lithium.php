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

        $this->load->model('Jive_model');
        $this->load->model('Lithium_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("lithium", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->_api = $this->lithiumapi;
        $this->_api->initialize('http://community.stage.starhub.com/');
        $this->_api->setHttpAuth('basic', 'starhub', 'ewoPzus7*er');
        $this->_api->login('pascal', 'staging_123');
        $this->_api->logout();
    }

    public function index() {
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
            $jive = $this->Jive_model->getRegistration($this->User_model->getSiteId());
            $this->_api->initialize($jive['jive_url']);
            $token = $this->_api->newToken($jive['jive_client_id'], $jive['jive_client_secret'], $code);
            if ($token) $this->Jive_model->updateToken($this->User_model->getSiteId(), (array)$token);
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
                redirect('/jive/events'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->Jive_model->hasToken($this->User_model->getSiteId())) {
            $jive = $this->Jive_model->getRegistration($this->User_model->getSiteId());
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

            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $eventId) {
                    try {
                        $this->_api->subscribeEvent($this->Jive_model->getEventType($eventId), $eventId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_watch_event'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/jive/events'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['jive'] = $jive;
            $this->session->set_userdata('total_events', $this->Jive_model->totalEvents($this->User_model->getSiteId()));
            $this->getListEvents($offset);
        } else {
            $this->data['main'] = 'jive_event';
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
                redirect('/jive/webhooks'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->Jive_model->hasToken($this->User_model->getSiteId())) {
            $jive = $this->Jive_model->getRegistration($this->User_model->getSiteId());
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

            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $webhookId) {
                    try {
                        $this->_api->unsubscribeEvent($webhookId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/jive/webhooks'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['jive'] = $jive;
            $this->session->set_userdata('total_webhooks', $this->_api->totalSubscriptions());
            $this->getListSubscriptions($offset);
        } else {
            $this->data['main'] = 'jive_place';
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

        $jive = $this->Jive_model->getRegistration($this->User_model->getSiteId());
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
        $this->getListEvents($offset);
    }

    private function getListEvents($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('jive/events');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $events = $this->Jive_model->listEvents($this->User_model->getSiteId(), $per_page, $offset);
        if ($this->session->userdata('total_events') === false) $this->session->set_userdata('total_events', $this->Jive_model->totalEvents($this->User_model->getSiteId()));
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

        $this->data['main'] = 'jive_event';
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

        $jive = $this->Jive_model->getRegistration($this->User_model->getSiteId());
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
        $this->getListSubscriptions($offset);
    }

    private function getListSubscriptions($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('jive/webhooks');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $webhooks = $this->_api->listSubscriptions($per_page, $offset);
        if ($this->session->userdata('total_webhooks') === false) $this->session->set_userdata('total_webhooks', $this->_api->totalSubscriptions());
        $total = $this->session->userdata('total_webhooks');

        foreach ($webhooks->list as $webhook) {
            $this->data['webhooks'][] = array(
                'webhookID' => $webhook->id,
                'events' => isset($webhook->events) && !empty($webhook->events) ? $webhook->events : 'place',
                'object' => $webhook->object,
                'callback' => $webhook->callback,
                'selected' => ($this->input->post('selected') && in_array($webhook->webhookID, $this->input->post('selected'))),
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

        $this->data['main'] = 'jive_webhook';
        $this->data['setting_group_id'] = $setting_group_id;

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