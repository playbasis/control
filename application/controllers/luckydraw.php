<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class LuckyDraw extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('LuckyDraw_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("luckydraw", $lang['folder']);
    }

    public function index(){

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function getList($offset){
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

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

        $config['base_url'] = site_url('luckydraw/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;


        $this->data['luckydraws'] = $this->LuckyDraw_model->getLuckyDraws($filter);
        $config['total_rows'] = $this->LuckyDraw_model->getTotalLuckyDraws($filter);

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

        $this->data['main'] = 'luckydraw';
        $this->render_page('template');
    }

    public function insert(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm(0);
    }

    public function edit($luckydraw_id){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm($luckydraw_id);
    }

    private function getForm($luckydraw_id=null) {

        $this->load->model('Image_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        $luckydraw_info = array();

        if (isset($luckydraw_id) && ($luckydraw_id != 0)) {
            if ($this->User_model->getClientId()) { // isAdmin?
                $luckydraw_info = $this->LuckyDraw_model->getLuckyDraw($luckydraw_id);
            } else {
                $luckydraw_info = $this->LuckyDraw_model->getLuckyDraw($luckydraw_id);
            }
        }

        $site_id = $this->User_model->getSiteId();

        $luckydraws = $this->LuckyDraw_model->getTotalLuckyDraws(array(
            'site_id' => $site_id
        ));

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');
        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit_luckydraw = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'luckydraw');

        $this->data['message'] = null;
        if ($limit_luckydraw && $luckydraws >= $limit_luckydraw) {
            $this->data['message'] = $this->lang->line('error_luckydraw_limit');
        }

        if ($this->input->post()) {
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $data = $this->input->post();

            $luckydraw = $data;

            $this->form_validation->set_rules('name', 'lang:entry_name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_start', 'lang:entry_date_start', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_end', 'lang:entry_date_end', 'trim|required|xss_clean');
            $this->form_validation->set_rules('participate_method', 'lang:entry_part_method',
                'trim|required|xss_clean');

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $luckydraw['client_id'] = $this->User_model->getClientId();
                $luckydraw['site_id'] = $this->User_model->getSiteId();

                if ($luckydraw_info) {
                    $this->LuckyDraw_model->editLuckyDrawToClient($luckydraw_id, $luckydraw);
                } else {
                    $this->LuckyDraw_model->addLuckyDrawToClient($luckydraw);
                }
                redirect('/luckydraw', 'refresh');
            }

            $luckydraw_info = array_merge($luckydraw_info, $luckydraw);
        }

        $this->data['luckydraw'] = $luckydraw_info;

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        $this->data['badge_list'] = array();
        $this->data['badge_list'] = $this->Badge_model->getBadgeBySiteId(array("site_id" => $data['site_id']));

        $this->data['point_list'] = array();
        $this->data['point_list'] = $this->Reward_model->getAnotherRewardBySiteId($data['site_id']);

        $this->load->model('Feature_model');

        $this->data['client_id'] = $data['client_id'];
        $this->data['site_id'] = $data['site_id'];

        $this->data['main'] = 'luckydraw_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {

            foreach ($this->input->post('selected') as $luckydraw_id) {
                $this->LuckyDraw_model->delete($luckydraw_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/luckydraw', 'refresh');
        }

        $this->getList(0);
    }

    private function validateModify() {
        if ($this->User_model->hasPermission('modify', 'luckydraw')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'luckydraw') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'luckydraw')) {
            return true;
        } else {
            return false;
        }
    }

}

function index_badge_id($obj) {
    return $obj['badge_id'];
}

function index_reward_id($obj) {
    return $obj['reward_id'];
}
?>