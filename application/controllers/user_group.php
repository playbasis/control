<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class User_group extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->load->model('User_group_model');
        $this->load->model('User_group_to_client_model');
        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("usergroup", $lang['folder']);
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

        $this->getList($offset);
    }

    public function getList($offset)
    {

        $this->load->library('pagination');
        $config['base_url'] = site_url('user_group/page');
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            $config['total_rows'] = $this->User_group_model->getTotalNumUsers();
        } else {
            $config['total_rows'] = $this->User_group_to_client_model->getTotalNumUsers($this->User_model->getClientId());
        }
        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

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

        if (isset($_GET['filter_name'])) {
            $filter = array(
                'filter_name' => $_GET['filter_name']
            );

            if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                $this->data['user_groups'] = $this->User_group_model->fetchAllUserGroups($filter);
            } else {
                $this->data['user_groups'] = $this->User_group_to_client_model->fetchAllUserGroups($this->User_model->getClientId(),
                    $filter);
            }
        } else {
            $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset
            );

            if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                $this->data['user_groups'] = $this->User_group_model->fetchAllUserGroups($filter);
            } else {
                $this->data['user_groups'] = $this->User_group_to_client_model->fetchAllUserGroups($this->User_model->getClientId(),
                    $filter);
            }
        }

        $this->data['main'] = 'user_group';
        $this->render_page('template');
    }

    public function update($user_group_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user_group/update/' . $user_group_id;

        //Rules need to be set
        $this->form_validation->set_rules('usergroup_name', $this->lang->line('form_usergroup_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        //-->

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($this->form_validation->run()) {

                if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                    $this->User_group_model->editUserGroup($user_group_id, $this->input->post());
                } else {
                    $this->User_group_to_client_model->editUserGroup($this->User_model->getClientId(), $user_group_id,
                        $this->input->post());
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                redirect('user_group/', 'refresh');
            } else {
                $this->data['temp_features'] = $this->input->post();
            }
        }
        $this->getForm($user_group_id);


    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user_group/insert/';

        //Rules need to be set
        $this->form_validation->set_rules('usergroup_name', $this->lang->line('form_usergroup_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        //-->

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($this->form_validation->run()) {
                $this->session->data['success'] = $this->lang->line('text_success');
                if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                    $this->User_group_model->insertUserGroup();
                } else {
                    $this->User_group_to_client_model->insertUserGroup($this->User_model->getClientId());
                }
                $this->session->set_flashdata('success', $this->lang->line('text_success'));
                redirect('user_group/', 'refresh');
            } else {
                $this->data['temp_features'] = $this->input->post();
            }
        }

        $this->getForm();
    }


    public function getForm($user_group_id = 0)
    {
        if ((isset($user_group_id) && $user_group_id != 0)) {

            if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                $user_group_info = $this->User_group_model->getUserGroupInfo($user_group_id);
            } else {
                $user_group_info = $this->User_group_to_client_model->getUserGroupInfo($this->User_model->getClientId(),
                    $user_group_id);
            }
        }

        if (isset($user_group_info)) {
            $this->data['user_group_info'] = $user_group_info;
        }

        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            $this->data['all_features'] = $this->User_group_model->getAllFeatures();
        } else {
            $this->data['all_features'] = $this->User_group_to_client_model->getAllFeatures($this->User_model->getClientId(),
                $this->User_model->getSiteId());
        }

        $this->data['main'] = 'user_group_form';
        $this->render_page('template');
    }

    public function delete()
    {
        $selectedUserGroups = $this->input->post('selected');

        foreach ($selectedUserGroups as $selectedUserGroup) {

            $check = $this->User_group_model->checkUsersInUserGroup($selectedUserGroup);

            if ($check == null) {
                if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                    $this->User_group_model->deleteUserGroup($selectedUserGroup);
                } else {
                    $this->User_group_to_client_model->deleteUserGroup($this->User_model->getClientId(),
                        $selectedUserGroup);
                }
            } else {
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_users_exists'));
                redirect('/user_group', 'refresh');
            }
        }
        $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
        redirect('user_group/');

    }

    public function autocomplete()
    {
        $json = array();

        if ($this->input->get('filter_name')) {

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'filter_name' => $filter_name
            );


            if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                $results_usergroup = $this->User_group_model->fetchAllUserGroups($data);
            } else {
                $results_usergroup = $this->User_group_to_client_model->fetchAllUserGroups($this->User_model->getClientId(),
                    $data);
            }

            foreach ($results_usergroup as $result) {
                $json[] = array(
                    'username' => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        if ($this->User_model->hasPermission('access', 'user_group')) {
            return true;
        } else {
            return false;
        }
    }

}