<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Quest extends MY_Controller
{

	public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Quest_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("quest", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index(){
    	$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        /*
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }
        */

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function getList($offset){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = 10;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id'=>$client_id,
            'site_id'=>$site_id,
            'sort'=>'sort_order'
        );

        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('quest/page');
        $config["uri_segment"] = 3;

        if($client_id){
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);
            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
        }

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
    	
    	$this->data['main'] = 'quest';
        $this->render_page('template');

    }

    public function insert(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'quest/insert';

        // $this->form_validation->set_rules('name', $this->lang->line('form_action_name'), 'trim|required|xss_clean|max_length[100]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();

            echo "<pre>";
                var_dump($data);
            echo "</pre>";

        }else{
            $this->getForm();    
        }

        
    }

    public function getForm(){

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        $this->load->model('Image_model');
        $this->load->model('Level_model');

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($badge_info)) {
            $this->data['image'] = $badge_info['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($badge_info) && $badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($badge_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (!empty($goods_info)) {
            $this->data['date_start'] = $goods_info['date_start'];
        } else {
            $this->data['date_start'] = "-";
        }

        if ($this->input->post('date_end')) {
            $this->data['date_end'] = $this->input->post('date_end');
        } elseif (!empty($goods_info)) {
            $this->data['date_end'] = $goods_info['date_end'];
        } else {
            $this->data['date_end'] = "-";
        }

        $this->data['levels'] = $this->Level_model->getLevelsSite($data);

        $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($data);

        $this->data['customPoints'] = $this->Quest_model->getCustomPoints($data);

        $this->data['badges'] =$this->Quest_model->getBadgesByClientSiteId($data);


        $this->data['main'] = 'quest_form';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function autocomplete(){
        $json = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('filter_name')) {

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'filter_name' => $filter_name
            );

            if($client_id){
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $results_quest = $this->Quest_model->getQuestsByClientSiteId($data);
            }else{
                //For admins because there is no client id?
            }

            foreach ($results_quest as $result) {
                $json[] = array(
                    'name' => html_entity_decode($result['quest_name'], ENT_QUOTES, 'UTF-8'),
                    // 'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    // 'icon' => html_entity_decode($result['icon'], ENT_QUOTES, 'UTF-8'),
                    // 'color' => html_entity_decode($result['color'], ENT_QUOTES, 'UTF-8'),
                    // 'sort_order' => html_entity_decode($result['sort_order'], ENT_QUOTES, 'UTF-8'),
                    'status' => html_entity_decode($result['status'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
    }

    public function increase_order($quest_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->increaseOrderByOneClient($quest_id, $client_id);
        }else{
            $this->Quest_model->increaseOrderByOne($quest_id);    
        }

        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));

    }

    public function decrease_order($quest_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->decreaseOrderByOneClient($quest_id, $client_id);
        }else{
            $this->Quest_model->decreaseOrderByOne($quest_id);    
        }
        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));
    }

    public function getListForAjax($offset) {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        
        $this->load->library('pagination');

        $config['per_page'] = 10;

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset,
                'client_id'=>$client_id,
                'site_id'=>$site_id,
                'sort'=>'sort_order'
            );
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('action/page');
        $config["uri_segment"] = 3;

        if($client_id){
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);
            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
        }else{
            /*
            // $this->data['actions'] = $this->Action_model->getActions($filter);
            $allActions = $this->Action_model->getActions($filter);

            foreach ($allActions as &$action){
                $actionIsPublic = $this->checkActionIsPublic($action['_id']);
                $action['is_public'] =  $actionIsPublic;
            }

            $this->data['actions'] = $allActions;
            $config['total_rows'] = $this->Action_model->getTotalActions();
            */
        }

        $this->pagination->initialize($config);

        $this->render_page('quest_ajax');
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

            if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                foreach ($this->input->post('selected') as $quest_id) {
                    $this->Quest_model->deleteQuestClient($quest_id);
                }
            }else{
                /*
                foreach ($this->input->post('selected') as $action_id) {
                    $this->Action_model->delete($action_id);
                }
                */
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/quest', 'refresh');
        }

        $this->getList(0);
    }

     private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'action')) {
            return true;
        } else {
            return false;
        }
    }

}