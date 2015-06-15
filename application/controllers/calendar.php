<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Calendar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Googles_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("calendar", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->load->library('GoogleApi');
        $this->record = $this->Googles_model->getRegistration($this->User_model->getSiteId());
        $this->_client = null;
        $this->_gcal = null;
        if ($this->record) {
            $this->_client = $this->googleapi->initialize($this->record['google_client_id'], $this->record['google_client_secret'], base_url().'/calendar/authorize');
            if (isset($this->record['token'])) {
                $this->_gcal = $this->_client->setAccessToken($this->record['token'])->calendar();
                /*// Print the next 10 events on the user's calendar.
                $calendarId = 'primary';
                $optParams = array(
                    'maxResults' => 10,
                    'orderBy' => 'startTime',
                    'singleEvents' => TRUE,
                    'timeMin' => date('c'),
                );
                $results = $this->_gcal->events->listEvents($calendarId, $optParams);*/
            }
        }

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

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->data['message'] = null;

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name'])) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if (isset($_FILES['file']['name']) && empty($_FILES['file']['name'])) {
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

                $json = file_get_contents($_FILES['file']['tmp_name']);
                if (!$json) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }
                $data = json_decode($json);
                if (!$data || !isset($data->installed) || !isset($data->installed->auth_uri) || !isset($data->installed->client_id) || !isset($data->installed->client_secret)) {
                    $this->data['message'] = $this->lang->line('error_json');
                }

                if(/*$this->form_validation->run() &&*/ $this->data['message'] == null){
                    $this->Googles_model->insertRegistration($data->installed->auth_uri, $data->installed->client_id, $data->installed->client_secret);
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/calendar', 'refresh');
                }
            }
        }

        $this->data['form'] = 'calendar/';
        $this->data['calendar'] = $this->record;
        $this->data['main'] = 'calendar_setup';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function authorize() {
        $code = $this->input->get('code');
        if (!empty($code)) {
            try {
                $accessToken = $this->_client->authenticate($code);
                if ($accessToken) $this->Googles_model->updateToken($this->User_model->getSiteId(), (array)$accessToken);
            } catch (Exception $e) {
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_authorized_code').': '.$e->getMessage());
            }
        }
        redirect('/calendar', 'refresh');
    }

    public function place($offset=0) {
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
                redirect('/calendar/places'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->_gcal) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $placeId) {
                        try {
                            $this->_api->createContentWebhook($placeId);
                            $success = true;
                        } catch (Exception $e) {
                            log_message('error', 'ERROR = '.$e->getMessage());
                            $fail = $e->getMessage();
                        }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_watch_place'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/calendar/places'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['calendar'] = $this->record;
            $this->session->set_userdata('total_places', $this->_api->totalPlaces());
            $this->getListPlaces($offset);
        } else {
            $this->data['main'] = 'calendar_place';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
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
                redirect('/calendar/events'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->_gcal) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $eventId) {
                    try {
                        $this->_api->createSystemWebhook($this->Googles_model->getEventType($eventId), $eventId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_watch_event'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/calendar/events'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['calendar'] = $this->record;
            $this->session->set_userdata('total_events', $this->Googles_model->totalEvents($this->User_model->getSiteId()));
            $this->getListEvents($offset);
        } else {
            $this->data['main'] = 'calendar_event';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function webhook($offset=0) {
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
                redirect('/calendar/webhooks'.($offset ? '/'.$offset : ''), 'refresh');
            }
        }

        if ($this->_gcal) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $webhookId) {
                    try {
                        $this->_api->deleteWebhook($webhookId);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = '.$e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                if ($fail) $this->session->set_flashdata('fail', $fail);
                redirect('/calendar/webhooks'.($offset ? '/'.$offset : ''), 'refresh');
            }

            $this->data['calendar'] = $this->record;
            $this->session->set_userdata('total_webhooks', $this->_api->totalWebhooks());
            $this->getListWebhooks($offset);
        } else {
            $this->data['main'] = 'calendar_place';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function places($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['calendar'] = $this->record;
        $this->getListPlaces($offset);
    }

    private function getListPlaces($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('calendar/places');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

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

        $this->data['main'] = 'calendar_place';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function events($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['calendar'] = $this->record;
        $this->getListEvents($offset);
    }

    private function getListEvents($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('calendar/events');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $events = $this->Googles_model->listEvents($this->User_model->getSiteId(), $per_page, $offset);
        if ($this->session->userdata('total_events') === false) $this->session->set_userdata('total_events', $this->Googles_model->totalEvents($this->User_model->getSiteId()));
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

        $this->data['main'] = 'calendar_event';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function webhooks($offset=0) {
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['calendar'] = $this->record;
        $this->getListWebhooks($offset);
    }

    private function getListWebhooks($offset) {
        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('calendar/webhooks');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['offset'] = $offset;

        $webhooks = $this->_api->listWebhooks($per_page, $offset);
        if ($this->session->userdata('total_webhooks') === false) $this->session->set_userdata('total_webhooks', $this->_api->totalWebhooks());
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

        $this->data['main'] = 'calendar_webhook';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {
        if ($this->User_model->hasPermission('modify', 'calendar')) {
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

        if ($this->User_model->hasPermission('access', 'calendar') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'calendar')) {
            return true;
        } else {
            return false;
        }
    }
}
?>