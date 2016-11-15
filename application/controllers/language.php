<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Language extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Language_model');
        $this->load->model('Custompoints_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("language", $lang['folder']);
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
        $this->data['form'] = 'language/';

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
                $selectedLanguages = $this->input->post('selected');

                foreach ($selectedLanguages as $selectedLanguage) {
                    $result = $this->Language_model->deleteLanguage($client_id,$site_id,$selectedLanguage);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/language', 'refresh');
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
        $this->data['form'] = 'language/';

        $this->getList($offset);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'language/insert';

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('language', $this->lang->line('entry_language'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('abbreviation', $this->lang->line('entry_abbreviation'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if ($this->form_validation->run()) {
                $data = $this->input->post();
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;

                $chk_name = $this->Language_model->retrieveLanguageByName($client_id, $site_id, $data['language']);
                if($chk_name){
                    $this->data['message'] = $this->lang->line('text_error_duplicate_language');
                }else {
                    $chk_abbr = $this->Language_model->retrieveLanguageByAbbr($client_id, $site_id, $data['abbreviation']);
                    if($chk_abbr){
                        $this->data['message'] = $this->lang->line('text_error_duplicate_abbr');
                    }else {
                        $insert = $this->Language_model->insertLanguage($data);
                        if ($insert) {
                            $this->session->set_flashdata('success', $this->lang->line('text_success_insert'));
                            redirect('/language', 'refresh');
                        }
                    }
                }
            }
        }
        $this->getForm();
    }

    public function update($language_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'language/update/' . $language_id;

        $this->data['message'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $this->form_validation->set_rules('language', $this->lang->line('entry_language'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');
            $this->form_validation->set_rules('abbreviation', $this->lang->line('entry_abbreviation'),
                'trim|required|min_length[2]|max_length[255]|xss_clean');

            if ( $this->data['message'] == null && $this->form_validation->run() ) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $data = $this->input->post();

                $chk_name = $this->Language_model->retrieveLanguageByNameButNotID($client_id, $site_id, $data['language'],$language_id);
                if($chk_name){
                    $this->data['message'] = $this->lang->line('text_error_duplicate_language');
                }else{
                    $chk_abbr = $this->Language_model->retrieveLanguageByAbbrButNotID($client_id, $site_id, $data['abbreviation'],$language_id);
                    if($chk_abbr){
                        $this->data['message'] = $this->lang->line('text_error_duplicate_abbr');
                    }else {
                        $insert = $this->Language_model->updateLanguage($client_id, $site_id, $language_id, $data);
                        if ($insert) {
                            $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                            redirect('/language', 'refresh');
                        }
                    }
                }
            }
        }

        $this->getForm($language_id);
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

        $config['base_url'] = site_url('language/page');


        $this->data['languages'] = $this->Language_model->retrieveLanguage($filter);
        $config['total_rows'] = $this->Language_model->getTotalLanguage($filter);

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

        $this->data['main'] = 'language';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getForm($language_id = null)
    {
        $this->data['main'] = 'language_form';

        if (!is_null($language_id)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();
            $language_info = $this->Language_model->retrieveLanguageByID($client_id, $site_id, $language_id);
        }

        if ($this->input->post('language')) {
            $this->data['language'] = $this->input->post('language');
        } elseif (isset($language_info['language'])) {
            $this->data['language'] = $language_info['language'];
        } else {
            $this->data['language'] = '';
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($language_info['status'])) {
            $this->data['status'] = $language_info['status'];
        } else {
            $this->data['status'] = '';
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = explode(',', $this->input->post('tags'));
        } elseif (isset($language_info['tags'])) {
            $this->data['tags'] = $language_info['tags'];
        } else {
            $this->data['tags'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'language')) {
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
                'language') && $this->Feature_model->getFeatureExistByClientId($client_id, 'language')
        ) {
            return true;
        } else {
            return false;
        }
    }
}