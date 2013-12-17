<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class User_group extends MY_Controller{


	public function __construct(){
		parent::__construct();

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);

        $this->load->model('User_group_model');
        $this->load->model('User_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("usergroup", $lang['folder']);
	}

	public function index(){

        if(!$this->validateAccess()){
            echo $this->lang->line('error_access');
            return false;
        }

		$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);

	}

	public function page($offset = 0){
		$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);
	}

	public function getList($offset){

		//Pagination set up
		$this->load->library('pagination');
		$config['base_url'] = site_url('user_group/page');
        $config['total_rows'] = $this->User_group_model->getTotalNumUsers();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

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


        if(isset($_GET['filter_name'])){
        	$filter = array(
                    'filter_name' => $_GET['filter_name']
                );

            $this->data['user_groups'] = $this->User_group_model->fetchAllUserGroups($filter);
        }else{
            $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset
            );
            $this->data['user_groups'] = $this->User_group_model->fetchAllUserGroups($filter);
        }

		$this->data['main'] = 'user_group';
		$this->render_page('template');

	}


    public function update($user_group_id){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user_group/update/'.$user_group_id;

        //Rules need to be set
        $this->form_validation->set_rules('usergroup_name', $this->lang->line('form_usergroup_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');
        //-->

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            if($this->form_validation->run()){
                $this->User_group_model->editUserGroup($user_group_id, $this->input->post());  

                redirect('user_group/','refresh');
            }else{
                $this->data['temp_features'] = $this->input->post();
            }            
        }
        $this->getForm($user_group_id);
        

    }

	public function insert(){
		$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user_group/insert/';

        //Rules need to be set
        $this->form_validation->set_rules('usergroup_name', $this->lang->line('form_usergroup_name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');
        //-->

        if($_SERVER['REQUEST_METHOD'] =='POST'){
            
        	if($this->form_validation->run()){
        		$this->session->data['success'] = $this->lang->line('text_success');
                $this->User_group_model->insertUserGroup();
                redirect('user_group/','refresh');
        	}else{
                $this->data['temp_features'] = $this->input->post();
        	}
        }

        $this->getForm();
	}



	public function getForm($user_group_id = 0){
		if((isset($user_group_id) && $user_group_id !=0)){
			$user_group_info = $this->User_group_model->getUserGroupInfo($user_group_id);
		}

		if(isset($user_group_info)){
            $this->data['user_group_info'] = $user_group_info;
		}
        
        $this->data['all_features'] = $this->User_group_model->getAllFeatures();
        $this->data['main'] = 'user_group_form';
        $this->render_page('template');
	}

    public function delete(){
        $selectedUserGroups = $this->input->post('selected');

        foreach($selectedUserGroups as $selectedUserGroup){
            $this->User_group_model->deleteUserGroup($selectedUserGroup);
        }

        redirect('user_group/');

    }

    public function autocomplete(){
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

            $results_usergroup = $this->User_group_model->fetchAllUserGroups($data);

            foreach ($results_usergroup as $result) {
                $json[] = array(
                    'username' => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
    }

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'user_group')) {
            return true;
        } else {
            return false;
        }
    }

}