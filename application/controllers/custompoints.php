<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Custompoints extends MY_Controller
{
    

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');
        $this->load->model('Custompoints_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("custompoints", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

    public function insert(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'custompoints/insert';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

           if($this->form_validation->run()){
                $custompoints_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['name'] = $custompoints_data['name'];
                $data['status'] = true;

                $insert = $this->Custompoints_model->insertCustompoints($data);
                if($insert){
                    redirect('/custompoints', 'refresh');
                }
            }      
        }
        $this->getForm();
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('custompoints/page');

        $site_id = $this->User_model->getSiteId();
        $client_id = $this->User_model->getClientId();
        $this->data['client_id'] = $client_id;

        $custompoints = $this->Custompoints_model->getCustompoints($client_id, $site_id);

        $this->data['custompoints'] = $custompoints;
        

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

        /*
        $config['total_rows'] = $badge_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

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

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        */

        $this->data['main'] = 'custompoints';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'badge')) {
            return true;
        } else {
            return false;
        }
    }

    public function getForm($custompoints_id = null){
        $this->data['main'] = 'custompoints_form';

        if (isset($custompoints_id) && ($custompoints_id != 0)) {
            if($this->User_model->getClientId()){
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

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function update($custompoints_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'custompoints/update/'.$custompoints_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if($this->form_validation->run()){
                $custompoints_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['reward_id'] = $custompoints_id;
                $data['name'] = $custompoints_data['name'];

                $update = $this->Custompoints_model->updateCustompoints($data);
                if($update){
                    redirect('/custompoints', 'refresh');
                }
            } 
        }       

        $this->getForm($custompoints_id);
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $reward_id) {
                    $this->Custompoints_model->deleteCustompoints($reward_id); 
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/custompoints', 'refresh');
        }

        $this->getList(0);
    }

}