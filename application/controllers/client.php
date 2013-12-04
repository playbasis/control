<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Client extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Client_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("client", $lang['folder']);
    }

    public function index() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0, site_url('client/page'));

    }

    public function page($offset=0) {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset, site_url('client/page'));

    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'client/insert';

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $this->Client_model->addClient($this->input->post());

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/client', 'refresh');
            }
        }

        $this->getForm();
    }

    public function update($client_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'client/update/'.$client_id;

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerClient($client_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $this->Client_model->editClient($client_id, $this->input->post());

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/client', 'refresh');
            }
        }

        $this->getForm($client_id);
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $client_id) {
                if($this->checkOwnerClient($client_id)){
                    $this->Client_model->deleteClient($client_id);
                }
            }

            $this->session->data['success'] = $this->lang->line('text_success');

            redirect('/client', 'refresh');
        }

        $this->getList(0, site_url('client'));
    }

    public function getList($offset, $url) {

        $offset = $this->input->get('per_page') ? $this->input->get('per_page') : $offset;

        $per_page = 10;

        $this->load->model('Domain_model');
        $this->load->model('Image_model');

        $this->load->library('pagination');

        $this->load->model('Permission_model');

        $parameter_url = "?t=".rand();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        if ($this->input->get('filter_name')) {
            $filter_name = $this->input->get('filter_name');
            $parameter_url .= "&filter_name=".$filter_name;
        } else {
            $filter_name = null;
        }

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
            $parameter_url .= "&sort=".$sort;
        } else {
            $sort = 'domain_name';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
            $parameter_url .= "&order=".$order;
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page ;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'filter_name' => $filter_name,
            'sort'  => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        $total = $this->Client_model->getTotalClients($data);

        $results_client = $this->Client_model->getClients($data);

        if ($results_client) {
            foreach ($results_client as $result) {

                $data_client = array("client_id" => $result['_id']);
                $domain_total = $this->Domain_model->getTotalDomainsByClientId($data_client);

                if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 140, 140);
                }
                else {
                    $image = $this->Image_model->resize('no_image.jpg', 140, 140);
                }

                $this->data['clients'][] = array(
                    'client_id' => $result['_id'],
                    'first_name' => $result['first_name'],
                    'last_name' => $result['last_name'],
                    'image' => $image,
                    'quantity' => $domain_total,
                    'status' => $result['status'],
                    'selected'    => in_array($result['client_id'], $this->input->post('selected')
                )
            }
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

        $config['base_url'] = $url.$parameter_url;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);
        $config['page_query_string'] = true;

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'client';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getForm($client_id=null) {

        $this->load->model('Image_model');

        if (isset($badge_id) && ($badge_id != 0)) {
            $badge_info = $this->Badge_model->getBadge($badge_id);
            $badge_info = $badge_info[0];
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['name'] = $badge_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('description')) {
            $this->data['description'] = $this->input->post('description');
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['description'] = $badge_info['description'];
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('hint')) {
            $this->data['hint'] = $this->input->post('hint');
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['hint'] = $badge_info['hint'];
        } else {
            $this->data['hint'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($badge_info)) {
            $this->data['image'] = $badge_info['image'];
        } else {
            $this->data['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);;
        }

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($badge_info) && $badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($badge_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 100, 100);

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($badge_info)) {
            $this->data['sort_order'] = $badge_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($badge_info)) {
            $this->data['status'] = $badge_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if ($this->input->post('stackable')) {
            $this->data['stackable'] = $this->input->post('stackable');
        } elseif (!empty($badge_info)) {
            $this->data['stackable'] = $badge_info['stackable'];
        } else {
            $this->data['stackable'] = 1;
        }

        if ($this->input->post('substract')) {
            $this->data['substract'] = $this->input->post('substract');
        } elseif (!empty($badge_info)) {
            $this->data['substract'] = $badge_info['substract'];
        } else {
            $this->data['substract'] = 1;
        }

        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (!empty($badge_info)) {
            $this->data['quantity'] = $badge_info['quantity'];
        } else {
            $this->data['quantity'] = 1;
        }

        if (isset($badge_id)) {
            $this->data['badge_id'] = $badge_id;
        } else {
            $this->data['badge_id'] = null;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'client_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function checkOwnerClient($clientId){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $clients = $this->Client_model->getDomainsByClientId($this->User_model->getClientId());

            $has = false;

            foreach ($clients as $client) {
                if($client['_id']."" == $clientId.""){
                    $has = true;
                }
            }

            if(!$has){
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    public function autocomplete(){
        $json = array();

        if ($this->input->get('filter_name')) {

            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'filter_name' => $filter_name
            );

            $results_client = $this->Client_model->getClients($data);

            foreach ($results_client as $result) {
                $json[] = array(
                    'name' => html_entity_decode($result['first_name'], ENT_QUOTES, 'UTF-8'),
                    'fullname' => html_entity_decode($result['first_name'] . ' ' . $result['last_name'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }

        $this->output->set_output(json_encode($json));
    }
}
?>