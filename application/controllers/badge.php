<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Badge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("badge", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
        
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

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'badge/insert';

        //I took out the check_space because some badges may have spaces? - Joe
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if($this->checkLimitBadge()){
                $this->data['message'] = null;

                if (!$this->validateModify()) {
                    $this->data['message'] = $this->lang->line('error_permission');
                }

                if($this->form_validation->run() && $this->data['message'] == null){
                    $this->Badge_model->addBadge($this->input->post());

                    $this->session->set_flashdata('success', $this->lang->line('text_success'));

                    redirect('/badge', 'refresh');
                }    
            }else{
                $this->session->set_flashdata('limit_reached', $this->lang->line('text_reach_limit_badge'));
                redirect('/badge/insert', 'refresh');
            }
        }

        $this->getForm();
    }

    public function update($badge_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'badge/update/'.$badge_id;

        //I took out the check_space because some badges may have spaces? - Joe
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerBadge($badge_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $this->Badge_model->editBadge($badge_id, $this->input->post());

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));

                redirect('/badge', 'refresh');
            }
        }

        $this->getForm($badge_id);
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
            foreach ($this->input->post('selected') as $badge_id) {
                if($this->checkOwnerBadge($badge_id)){
                    $this->Badge_model->deleteBadge($badge_id);
                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/badge', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('badge/page');


        $this->load->model('Badge_model');
        $this->load->model('Image_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['badges'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->getUserGroupId() == $setting_group_id) {

            $data['limit'] = $per_page;
            $data['start'] = $offset;

            $results = $this->Badge_model->getBadges($data);

            $badge_total = $this->Badge_model->getTotalBadges($data);

            foreach ($results as $result) {

                if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }

                $this->data['badges'][] = array(
                    'badge_id' => $result['_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order'  => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'], $this->input->post('selected'))),
                );
            }
        }
        else {

            $this->load->model('Reward_model');

            $badges = $this->Badge_model->getBadgeBySiteId($site_id, $per_page, $offset);

            $reward_limit_data = $this->Reward_model->getBadgeRewardBySiteId($site_id);

            $badge_total = $this->Badge_model->getTotalBadgeBySiteId($site_id);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data[0]['limit'] - $badge_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 50, 50);

                foreach ($badges as $badge) {

                    $badge_info = $this->Badge_model->getBadge($badge['badge_id']);

                    if($badge_info){

                        if ($badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $image = $this->Image_model->resize($badge_info['image'], 50, 50);
                        }
                        else {
                            $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                        }

                        $this->data['badges'][] = array(
                            'badge_id' => $badge_info['_id'],
                            'name' => $badge_info['name'],
                            'hint' => $badge_info['hint'],
                            'quantity' => $badge_info['quantity'],
                            'status' => $badge_info['status'],
                            'image' => $image,
                            'sort_order'  => $badge_info['sort_order'],
                            'selected' => ($this->input->post('selected') && in_array($badge_info['_id'], $this->input->post('selected'))),
                        );

                    }
                }

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

        $config['total_rows'] = $badge_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['main'] = 'badge';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('badge');
    }

    private function getForm($badge_id=null) {

        $this->load->model('Image_model');

        if (isset($badge_id) && ($badge_id != 0)) {
            $badge_info = $this->Badge_model->getBadge($badge_id);
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
            $this->data['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
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

        $this->data['main'] = 'badge_form';

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('badge_form');
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'badge')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkLimitBadge(){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $this->load->model('Reward_model');

            $plan_limit = $this->Reward_model->getRewardByClientId($this->User_model->getClientId());

            $badges_count = $this->Badge_model->getTotalBadgeBySiteId($this->User_model->getSiteId());

            foreach ($plan_limit as $plan) {
                if($plan['site_id'] == $this->input->post('site_id')){
                    if($plan['name'] == 'badge'){
                        if($plan['limit']){
                            $limit_badge =  $plan['limit'];
                        }
                    }
                }
            }

            if(isset($limit_badge) && $limit_badge <= $badges_count){
                $error = $this->lang->line('error_limit');
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerBadge($badgeId){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $badges = $this->Badge_model->getBadgeBySiteId($this->User_model->getSiteId());

            $has = false;
    
            foreach ($badges as $badge) {
                if($badge['badge_id']."" == $badgeId.""){
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

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'badge')) {
            return true;
        } else {
            return false;
        }
    }

    public function increase_order($badge_id){

        $this->Badge_model->increaseOrderByOne($badge_id);

        redirect('badge', 'refresh');

    }

    public function decrease_order($badge_id){
        $this->Badge_model->decreaseOrderByOne($badge_id);

        redirect('badge', 'refresh');
    }

}