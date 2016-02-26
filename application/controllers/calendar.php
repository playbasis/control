<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Calendar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Googles_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("calendar", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->load->library('GoogleApi');
        $this->record = $this->Googles_model->getRegistration();
        $this->_client = null;
        $this->_gcal = null;
        if ($this->record) {
            $this->_client = $this->googleapi->initialize($this->record['google_client_id'],
                $this->record['google_client_secret'], base_url() . 'calendar/authorize');
            if (isset($this->record['token'])) {
                try {
                    $this->_gcal = $this->_client->setAccessToken($this->record['token'])->calendar();
                } catch (Exception $e) {
                    $this->data['message'] = $this->lang->line('text_fail_initialize_access_token') . ': ' . $e->getMessage();
                }
            }
        }
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

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->data['message'] = null;

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name'])) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if (isset($_FILES['file']['name']) && empty($_FILES['file']['name'])) {
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

                $json = file_get_contents($_FILES['file']['tmp_name']);
                if (!$json) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }
                $data = json_decode($json);
                if (!$data || !isset($data->web) || !isset($data->web->auth_uri) || !isset($data->web->client_id) || !isset($data->web->client_secret)) {
                    $this->data['message'] = $this->lang->line('error_json');
                }

                if (/*$this->form_validation->run() &&*/
                    $this->data['message'] == null
                ) {
                    $this->Googles_model->insertRegistration($data->web->auth_uri, $data->web->client_id,
                        $data->web->client_secret);
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

    public function authorize()
    {
        $code = $this->input->get('code');
        if (!empty($code)) {
            try {
                $accessToken = $this->_client->authenticate($code);
                if ($accessToken) {
                    $this->Googles_model->updateToken($this->User_model->getSiteId(), (array)$accessToken);
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('fail',
                    $this->lang->line('text_fail_authorized_code') . ': ' . $e->getMessage());
            }
        }
        redirect('/calendar', 'refresh');
    }

    public function place()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        /* POST */
        if ($this->input->post('selected')) {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('/calendar/place', 'refresh');
            }
        }

        if ($this->_gcal) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $placeId) {
                    $callback_url = API_SERVER . '/notification';
                    try {
                        $channel_id = get_random_code(12, true, true, true);
                        $this->_client->watchCalendar($this->_gcal, $placeId, $channel_id,
                            array('site_id' => $this->User_model->getSiteId() . '', 'callback_url' => $callback_url));
                        $this->Googles_model->insertWebhook($placeId, $channel_id, $callback_url);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = ' . $e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_watch_place'));
                }
                if ($fail) {
                    $this->session->set_flashdata('fail', $fail);
                }
                redirect('/calendar/place', 'refresh');
            }

            $this->data['calendar'] = $this->record;
            $this->getListPlaces();
        } else {
            $this->data['main'] = 'calendar_place';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    public function webhook()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        /* POST */
        if ($this->input->post('selected')) {
            if (!$this->validateModify()) {
                $this->session->set_flashdata('fail', $this->lang->line('error_permission'));
                redirect('/calendar/webhook', 'refresh');
            }
        }

        if ($this->_gcal) {
            /* POST */
            if ($this->input->post('selected')) {
                $success = false;
                $fail = false;
                foreach ($this->input->post('selected') as $resource_id) {
                    $subscription = $this->Googles_model->getSubscription($resource_id);
                    $channel_id = $subscription['channel_id'];
                    try {
                        $this->_client->unwatchCalendar($this->_gcal, $channel_id, $resource_id);
                        $this->Googles_model->removeWebhook($channel_id, $resource_id);
                        $success = true;
                    } catch (Exception $e) {
                        log_message('error', 'ERROR = ' . $e->getMessage());
                        $fail = $e->getMessage();
                    }
                }
                if ($success) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                }
                if ($fail) {
                    $this->session->set_flashdata('fail', $fail);
                }
                redirect('/calendar/webhook', 'refresh');
            }

            $this->data['calendar'] = $this->record;
            $this->getListWebhooks();
        } else {
            $this->data['main'] = 'calendar_webhook';
            $this->load->vars($this->data);
            $this->render_page('template');
        }
    }

    private function getListPlaces()
    {
        $places = $this->_client->listCalendars($this->_gcal);

        foreach ($places as $place) {
            $this->data['places'][] = array(
                'placeID' => $place['calendar_id'],
                'name' => $place['summary'],
                'description' => $place['description'],
                'selected' => ($this->input->post('selected') && in_array($place['id'],
                        $this->input->post('selected'))),
            );
        }

        $this->data['main'] = 'calendar_place';
        $this->data['form'] = 'calendar/place';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getListWebhooks()
    {
        $webhooks = $this->Googles_model->listWebhooks();

        foreach ($webhooks as $webhook) {
            $this->data['webhooks'][] = array(
                'calendar_id' => $webhook['calendar_id'],
                'channel_id' => $webhook['channel_id'],
                'resource_id' => isset($webhook['resource_id']) ? $webhook['resource_id'] : null,
                'resource_uri' => isset($webhook['resource_uri']) ? $webhook['resource_uri'] : null,
                'callback_url' => $webhook['callback_url'],
                'date_expire' => isset($webhook['date_expire']) && $webhook['date_expire'] ? date('d M Y H:m:s',
                    $webhook['date_expire']->sec) : null,
                'selected' => ($this->input->post('selected') && in_array($webhook['calendar_id'],
                        $this->input->post('selected'))),
            );
        }

        $this->data['main'] = 'calendar_webhook';
        $this->data['form'] = 'calendar/webhook';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'calendar')) {
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
                'calendar') && $this->Feature_model->getFeatureExistByClientId($client_id, 'calendar')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

?>