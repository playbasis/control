<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Custompoints extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');
        $this->load->model('Custompoints_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("custompoints", $lang['folder']);
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

        $this->getList(0);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'custompoints/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $custom_points = $this->Custompoints_model->countCustompoints($client_id, $site_id);

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');
        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit_custompoints = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'custompoint');

        $this->data['message'] = null;

        if ($limit_custompoints && $custom_points >= $limit_custompoints) {
            $this->data['message'] = $this->lang->line('error_currency_limit');
        }

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('type_custompoint', $this->lang->line('entry_type'), 'required');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->input->post('type_custompoint') != "normal") {
                $this->form_validation->set_rules('energy_maximum', $this->lang->line('entry_energy_maximum'),
                    'required|numeric|is_natural_no_zero|xss_clean');
                $this->form_validation->set_rules('energy_changing_period',
                    $this->lang->line('entry_energy_changing_period'),
                    'required|xss_clean');
                $this->form_validation->set_rules('energy_changing_per_period',
                    $this->lang->line('entry_energy_changing_per_period'),
                    'required|numeric|is_natural_no_zero|xss_clean');
            }else {
                $this->form_validation->set_rules('quantity', $this->lang->line('entry_quantity'),
                    'numeric|xss_clean');
            }

            $this->form_validation->set_rules('per_user',  $this->lang->line('entry_per_user'), 'numeric|xss_clean');
            $this->form_validation->set_rules('limit_per_day',  $this->lang->line('entry_limit_per_day'), 'numeric|xss_clean');

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $custompoints_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['name'] = $custompoints_data['name'];
                $data['quantity'] = isset($custompoints_data['quantity']) && !is_null($custompoints_data['quantity']) && $custompoints_data['quantity'] !== "" ? intval($custompoints_data['quantity']) : null;
                $data['per_user'] = isset($custompoints_data['per_user']) && !is_null($custompoints_data['per_user']) && $custompoints_data['per_user'] !== "" ? intval($custompoints_data['per_user']) : null;
                $data['limit_per_day'] = isset($custompoints_data['limit_per_day']) && !is_null($custompoints_data['limit_per_day']) && $custompoints_data['limit_per_day'] !== "" ? intval($custompoints_data['limit_per_day']) : null;
                $limit_start_time = isset($custompoints_data['limit_start_time']) && !is_null($custompoints_data['limit_start_time']) && $custompoints_data['limit_start_time'] !== "" ? $custompoints_data['limit_start_time'] : "00:00";
                $data['limit_start_time'] = is_null($data['limit_per_day']) || $data['limit_per_day'] == 0 ? "00:00" : $limit_start_time;
                $data['pending'] = isset($custompoints_data['pending']) && !empty($custompoints_data['pending']) ? $custompoints_data['pending'] : false;
                $data['status'] = true;
                $data['type'] = $custompoints_data['type_custompoint'];
                if ($custompoints_data['type_custompoint'] != "normal") {
                    $data['maximum'] = $custompoints_data['energy_maximum'];
                    $data['changing_period'] = $custompoints_data['energy_changing_period'];
                    $data['changing_per_period'] = $custompoints_data['energy_changing_per_period'];
                }
                $data['tags'] = $custompoints_data['tags'];

                $insert = $this->Custompoints_model->insertCustompoints($data);
                $custompoints_data = $this->Custompoints_model->getCustompointById($insert);
                $this->Custompoints_model->auditAfterCustomPoint('insert', $custompoints_data['reward_id'], $this->User_model->getId());
                if ($insert) {
                    if($this->initPlayerPoint($data) || $data['type'] == "normal"){
                        redirect('/custompoints', 'refresh');
                    }
                }
            }
        }
        $this->getForm();
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);
    }

    private function getList($offset)
    {

        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'name'
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('custompoints/page');

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $custompoints = $this->Custompoints_model->getCustompoints($filter);

            $this->data['custompoints'] = $custompoints;
            $config['total_rows'] = $this->Custompoints_model->countCustompoints($client_id, $site_id);
        }

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

        $this->data['main'] = 'custompoints';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'custompoints')) {
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
                'custompoints') && $this->Feature_model->getFeatureExistByClientId($client_id, 'custompoints')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getForm($custompoints_id = null)
    {
        $this->data['main'] = 'custompoints_form';

        if (isset($custompoints_id) && ($custompoints_id != 0)) {
            if ($this->User_model->getClientId()) {
                $custompoints_info = $this->Custompoints_model->getCustompoint($custompoints_id);
            }
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($custompoints_id) && ($custompoints_id != 0)) {
            $this->data['name'] = $custompoints_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($custompoints_info)) {
            $this->data['status'] = $custompoints_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if (isset($custompoints_info['type'])) {
            $this->data['type'] = $custompoints_info['type'];
        } else {
            $this->data['type'] = "normal";
        }

        if ($this->input->post('pending')) {
            $this->data['pending'] = $this->input->post('pending');
        } elseif (isset($custompoints_info['pending'])) {
            $this->data['pending'] = $custompoints_info['pending'];
        } else {
            $this->data['pending'] = false;
        }

        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (!empty($custompoints_info)) {
            $this->data['quantity'] = $custompoints_info['quantity'];
        } else {
            $this->data['quantity'] = "";
        }

        if ($this->input->post('per_user')) {
            $this->data['per_user'] = $this->input->post('per_user');
        } elseif (isset($custompoints_info['per_user'])) {
            $this->data['per_user'] = $custompoints_info['per_user'];
        } else {
            $this->data['per_user'] = "";
        }

        if ($this->input->post('limit_per_day')) {
            $this->data['limit_per_day'] = $this->input->post('limit_per_day');
        } elseif (isset($custompoints_info['limit_per_day'])) {
            $this->data['limit_per_day'] = $custompoints_info['limit_per_day'] ;
        } else {
            $this->data['limit_per_day'] = "";
        }

        if ($this->input->post('limit_start_time')) {
            $this->data['limit_start_time'] = $this->input->post('limit_start_time');
        } elseif (isset($custompoints_info['limit_start_time'])) {
            $this->data['limit_start_time'] = $custompoints_info['limit_start_time'] ;
        } else {
            $this->data['limit_start_time'] = "";
        }

        if (isset($custompoints_info['energy_props'])) {
            $this->data['maximum'] = $custompoints_info['energy_props']['maximum'];
        }

        if (isset($custompoints_info['energy_props'])) {
            $this->data['changing_period'] = $custompoints_info['energy_props']['changing_period'];
        }

        if (isset($custompoints_info['energy_props'])) {
            $this->data['changing_per_period'] = $custompoints_info['energy_props']['changing_per_period'];
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = explode(',', $this->input->post('tags'));
        } elseif (isset($custompoints_info['tags'])) {
            $this->data['tags'] = $custompoints_info['tags'];
        } else {
            $this->data['tags'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function update($custompoints_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'custompoints/update/' . $custompoints_id;
        $this->data['message'] = null;
        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('type_custompoint', $this->lang->line('entry_type'), 'required');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->data['message'] = null;

            if ($this->input->post('type_custompoint') != "normal") {
                $this->form_validation->set_rules('energy_maximum', $this->lang->line('entry_energy_maximum'),
                    'required|numeric|is_natural_no_zero|xss_clean');
                $this->form_validation->set_rules('energy_changing_period',
                    $this->lang->line('entry_energy_changing_period'),
                    'required|xss_clean');
                $this->form_validation->set_rules('energy_changing_per_period',
                    $this->lang->line('entry_energy_changing_per_period'),
                    'required|numeric|is_natural_no_zero|xss_clean');
            } else {
                $this->form_validation->set_rules('quantity', $this->lang->line('entry_quantity'),
                    'numeric|xss_clean');
            }

            $this->form_validation->set_rules('per_user',  $this->lang->line('entry_per_user'), 'numeric|xss_clean');
            $this->form_validation->set_rules('limit_per_day',  $this->lang->line('entry_limit_per_day'), 'numeric|xss_clean');

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $custompoints_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['reward_id'] = $custompoints_id;
                $data['name'] = $custompoints_data['name'];
                $data['quantity'] = isset($custompoints_data['quantity']) && !is_null($custompoints_data['quantity']) && $custompoints_data['quantity'] !== "" ? intval($custompoints_data['quantity']) : null;
                $data['per_user'] = isset($custompoints_data['per_user']) && !is_null($custompoints_data['per_user']) && $custompoints_data['per_user'] !== "" ? intval($custompoints_data['per_user']) : null;
                $data['limit_per_day'] = isset($custompoints_data['limit_per_day']) && !is_null($custompoints_data['limit_per_day']) && $custompoints_data['limit_per_day'] !== "" ? intval($custompoints_data['limit_per_day']) : null;
                $limit_start_time = isset($custompoints_data['limit_start_time']) && !is_null($custompoints_data['limit_start_time']) && $custompoints_data['limit_start_time'] !== "" ? $custompoints_data['limit_start_time'] : "00:00";
                $data['limit_start_time'] = is_null($data['limit_per_day']) || $data['limit_per_day'] == 0 ? null : $limit_start_time;
                $data['pending'] = isset($custompoints_data['pending']) && !empty($custompoints_data['pending']) ? $custompoints_data['pending'] : false;
                $data['type'] = $custompoints_data['type_custompoint'];
                if ($custompoints_data['type_custompoint'] != "normal") {
                    $data['maximum'] = $custompoints_data['energy_maximum'];
                    $data['changing_period'] = $custompoints_data['energy_changing_period'];
                    $data['changing_per_period'] = $custompoints_data['energy_changing_per_period'];
                }
                $data['tags'] = $custompoints_data['tags'];

                $audit_id = $this->Custompoints_model->auditBeforeCustomPoint('update', $custompoints_id, $this->User_model->getId());
                $update = $this->Custompoints_model->updateCustompoints($data);
                $this->Custompoints_model->auditAfterCustomPoint('update', $custompoints_id, $this->User_model->getId(), $audit_id);
                if ($update) {
                    redirect('/custompoints', 'refresh');
                }
            }
        }

        $this->getForm($custompoints_id);
    }

    public function delete()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $reward_id) {
                $audit_id = $this->Custompoints_model->auditBeforeCustomPoint('delete', $reward_id, $this->User_model->getId());
                $this->Custompoints_model->deleteCustompoints($reward_id);
                $this->Custompoints_model->auditAfterCustomPoint('delete', $reward_id, $this->User_model->getId(), $audit_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/custompoints', 'refresh');
        }

        $this->getList(0);
    }

    public function initPlayerPoint($data)
    {

        $client_id = $data['client_id'];
        $site_id = $data['site_id'];
        $reward_id = $this->Custompoints_model->getRewardId($data);
        $total = $this->Custompoints_model->findPlayersToInsert($client_id, $site_id, true);
        $completed_flag = false;
        if (isset($reward_id)) {
            for ($i = 0; $i <= round($total / LIMIT_PLAYERS_QUERY); $i++) {
                $offset = LIMIT_PLAYERS_QUERY * $i;
                $players_without_energy = $this->Custompoints_model->findPlayersToInsert($client_id, $site_id, false,
                    $offset, LIMIT_PLAYERS_QUERY);

                $batch_data = array();
                foreach ($players_without_energy as $player) {
                    // Note: $player here is from player table
                    if ($data['type'] == 'gain') {
                        array_push($batch_data, array(
                            'pb_player_id' => $player['_id'],
                            'cl_player_id' => $player['cl_player_id'],
                            'client_id' => $client_id,
                            'site_id' => $site_id,
                            'reward_id' => $reward_id,
                            'value' => $data['maximum'],
                            'date_cron_modified' => new MongoDate(),
                            'date_added' => new MongoDate(),
                            'date_modified' => new MongoDate()
                        ));
                    } elseif ($data['type'] == 'loss') {
                        array_push($batch_data, array(
                            'pb_player_id' => $player['_id'],
                            'cl_player_id' => $player['cl_player_id'],
                            'client_id' => $client_id,
                            'site_id' => $site_id,
                            'reward_id' => $reward_id,
                            'value' => 0,
                            'date_cron_modified' => new MongoDate(),
                            'date_added' => new MongoDate(),
                            'date_modified' => new MongoDate()
                        ));
                    }
                }
                if (!empty($batch_data)) {
                    $completed_flag = $this->Custompoints_model->bulkInsertInitialValue($batch_data);
                }

            }
        }
        return $completed_flag;
    }

}