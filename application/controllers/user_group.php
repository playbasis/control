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

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("usergroup", $lang['folder']);
	}

	public function index(){
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
        		'limit' => $config['per_page'],
        		'start' => $offset
        	);
        }

		$this->data['main'] = 'user_group';
		$this->render_page('template');

	}

	public function insert(){
		$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'user_group/insert/';

        //Rules need to be set
        if($_SERVER['REQUEST_METHOD'] =='POST'){
        	if($this->form_validation->run()){
        		$this->User_group_model->insertUserGroup();
        		$this->session->data['success'] = $this->lang->line('text_success');
                redirect('user_group/','refresh');
        	}else{

        	}
        }

        $this->getForm();
	}

	public function getForm($user_group_id = 0){
		if((isset($user_group_id) && $user_group_id !=0)){
			$user_group_info = $this->User_group_model->getUserGroupInfo($user_group_id);
		}

		if(isset($user_group_info)){

		}
	}

}