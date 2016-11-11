<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Location extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');
        $this->load->model('Merchant_model');
        $this->load->model('Location_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("location", $lang['folder']);
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
        $this->data['form'] = 'location/';

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['warning'] = $this->lang->line('error_permission');
            }else{
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $selectedLocations = $this->input->post('selected');


                    foreach ($selectedLocations as $selectedLocation) {
                        //$player = $this->Player_model->getPlayerById($selectedLocation, $site_id);
                        $result = $this->Location_model->deleteLocation($client_id,$site_id,$selectedLocation);
                        /*if (!$result->success) {
                            $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                            redirect('/workflow', 'refresh');
                        }*/
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                    redirect('/location', 'refresh');


            }
        }

        $this->getList(0);
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
        $this->data['form'] = 'location/';

        $this->getList($offset);
    }

    public function object($object_type)
    {

        if (!$this->validateAccess()) {
            $this->output->set_status_header('401');
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
            die();
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if($object_type == "item"){
            $item_filter = array(
                'site_id' => $site_id,
                'sort' => 'name',
                'order' => 'desc'
            );

            $items = $this->Badge_model->getBadgeBySiteId($item_filter);
            foreach ($items as &$item) {
                if (isset($item['badge_id'])) {
                    $item['_id'] = $item['badge_id'] . "";
                }
            }

            $this->output->set_status_header('200');
            $response = $items;

        }elseif($object_type == "store"){
            $stores = $this->Merchant_model->retrieveAllBranches($client_id, $site_id);
            foreach ($stores as &$store) {
                if (isset($store['_id'])) {
                    $store['_id'] = $store['_id'] . "";
                }
                if (isset($store['branch_name'])) {
                    $store['name'] = $store['branch_name'];
                }
            }

            $this->output->set_status_header('200');
            $response = $stores;
        }

        echo json_encode($response);
        die();
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'location/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('latitude', $this->lang->line('column_latitude'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('longitude', $this->lang->line('column_longitude'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('object_type', $this->lang->line('object_type'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('object_id', $this->lang->line('object_id'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if ( $this->data['message'] == null && $this->form_validation->run() ) {
                $data = $this->input->post();
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;

                $insert = $this->Location_model->insertLocation($data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                    redirect('/location', 'refresh');
                }
            }
        }
        $this->getForm();
    }

    public function update($location_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'location/update/' . $location_id;

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('latitude', $this->lang->line('column_latitude'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('longitude', $this->lang->line('column_longitude'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('object_type', $this->lang->line('object_type'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('object_id', $this->lang->line('object_id'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if ( $this->data['message'] == null && $this->form_validation->run() ) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $data = $this->input->post();

                $insert = $this->Location_model->updateLocation($client_id, $site_id, $location_id, $data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/location', 'refresh');
                }
            }
        }

        $this->getForm($location_id);
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

        $config['base_url'] = site_url('location/page');

        $this->data['locations'] = $this->Location_model->retrieveLocation($filter);
        foreach($this->data['locations'] as &$location){
            if($location['object_type'] == "item"){
                $location['object_name'] = $this->Badge_model->getNameOfBadgeID($client_id, $site_id, $location['object_id']);
            }else if($location['object_type'] == "store"){
                $location['object_name'] = $this->Merchant_model->getBranchNameByID($client_id, $site_id, $location['object_id']);
            }
        }

        $config['total_rows'] = $this->Location_model->getTotalLocation($filter);

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

        $this->data['main'] = 'location';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getForm($location_id = null)
    {
        $this->data['main'] = 'location_form';

        if (!is_null($location_id)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();
            $location_info = $this->Location_model->retrieveLocationByID($client_id, $site_id, $location_id);
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($location_info['name'])) {
            $this->data['name'] = $location_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('latitude')) {
            $this->data['latitude'] = $this->input->post('latitude');
        } elseif (isset($location_info['latitude'])) {
            $this->data['latitude'] = $location_info['latitude'];
        } else {
            $this->data['latitude'] = '';
        }

        if ($this->input->post('longitude')) {
            $this->data['longitude'] = $this->input->post('longitude');
        } elseif (isset($location_info['longitude'])) {
            $this->data['longitude'] = $location_info['longitude'];
        } else {
            $this->data['longitude'] = '';
        }

        if ($this->input->post('object_type')) {
            $this->data['object_type'] = $this->input->post('object_type');
        } elseif (isset($location_info['object_type'])) {
            $this->data['object_type'] = $location_info['object_type'];
        } else {
            $this->data['object_type'] = '';
        }

        if ($this->input->post('object_id')) {
            $this->data['object_id'] = $this->input->post('object_id');
        } elseif (isset($location_info['object_id'])) {
            $this->data['object_id'] = $location_info['object_id'];
        } else {
            $this->data['object_id'] = '';
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($location_info['status'])) {
            $this->data['status'] = $location_info['status'];
        } else {
            $this->data['status'] = '';
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = explode(',', $this->input->post('tags'));
        } elseif (isset($location_info['tags'])) {
            $this->data['tags'] = $location_info['tags'];
        } else {
            $this->data['tags'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if ($this->input->post('selected') && $this->error['message'] == null) {
            foreach ($this->input->post('selected') as $reward_id) {
                $this->Custompoints_model->deleteCustompoints($reward_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/custompoints', 'refresh');
        }

        $this->getList(0);
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'location')) {
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
                'location') && $this->Feature_model->getFeatureExistByClientId($client_id, 'location')
        ) {
            return true;
        } else {
            return false;
        }
    }

}