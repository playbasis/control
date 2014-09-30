<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Quiz extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("quiz", $lang['folder']);
    }

    public function index(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['main'] = 'quiz';
        $this->render_page('template');
    }

    public function insert(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm(0);
    }

    public function edit(){
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['main'] = 'quiz_form';
        $this->render_page('template');
    }

    private function getForm($quiz_id=null) {

        $this->load->model('Image_model');

        if (isset($quiz_id) && ($quiz_id != 0)) {
            if($this->User_model->getClientId()){
                $quiz_info = array();
            }else{
                $quiz_info = array();
            }

        }

        if ($this->input->post('quiz_name')) {
            $this->data['quiz_name'] = $this->input->post('quiz_name');
        } elseif (!empty($quiz_info)) {
            $this->data['quiz_name'] = $quiz_info['quiz_name'];
        } else {
            $this->data['quiz_name'] = '';
        }

        if ($this->input->post('quiz_description')) {
            $this->data['quiz_description'] = htmlentities($this->input->post('quiz_description'));
        } elseif (!empty($quiz_info)) {
            $this->data['quiz_description'] = htmlentities($quiz_info['quiz_description']);
        } else {
            $this->data['quiz_description'] = '';
        }

        if ($this->input->post('quiz_image')) {
            $this->data['quiz_image'] = $this->input->post('quiz_image');
        } elseif (!empty($quiz_info)) {
            $this->data['quiz_image'] = $quiz_info['quiz_image'];
        } else {
            $this->data['quiz_image'] = 'no_image.jpg';
        }

        if ($this->data['quiz_image']){
            $info = pathinfo($this->data['quiz_image']);
            if(isset($info['extension'])){
                $extension = $info['extension'];
                $new_image = 'cache/' . utf8_substr($this->data['quiz_image'], 0, utf8_strrpos($this->data['quiz_image'], '.')).'-100x100.'.$extension;
                $this->data['quiz_thumb'] = S3_IMAGE.$new_image;
            }elsE{
                $this->data['quiz_thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
            }
        }else{
            $this->data['quiz_thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
        }

        if ($this->input->post('quiz_description_image')) {
            $this->data['quiz_description_image'] = $this->input->post('quiz_description_image');
        } elseif (!empty($quiz_info)) {
            $this->data['quiz_description_image'] = $quiz_info['quiz_description_image'];
        } else {
            $this->data['quiz_description_image'] = 'no_image.jpg';
        }

        if ($this->data['quiz_description_image']){
            $info = pathinfo($this->data['quiz_description_image']);
            if(isset($info['extension'])){
                $extension = $info['extension'];
                $new_image = 'cache/' . utf8_substr($this->data['quiz_description_image'], 0, utf8_strrpos($this->data['quiz_description_image'], '.')).'-100x100.'.$extension;
                $this->data['quiz_description_thumb'] = S3_IMAGE.$new_image;
            }elsE{
                $this->data['quiz_description_thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
            }
        }else{
            $this->data['quiz_description_thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
        }

        $this->data['no_image'] = S3_IMAGE."cache/no_image-100x100.jpg";

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($quiz_info)) {
            $this->data['status'] = $quiz_info['status'];
        } else {
            $this->data['status'] = 'true';
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

        if ($this->input->post('claim')) {
            $this->data['claim'] = $this->input->post('claim');
        } elseif (!empty($badge_info) && isset($badge_info['claim'])) {
            $this->data['claim'] = $badge_info['claim'];
        } else {
            $this->data['claim'] = 0;
        }

        if ($this->input->post('redeem')) {
            $this->data['redeem'] = $this->input->post('redeem');
        } elseif (!empty($badge_info) && isset($badge_info['redeem'])) {
            $this->data['redeem'] = $badge_info['redeem'];
        } else {
            $this->data['redeem'] = 0;
        }

        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (!empty($badge_info)) {
            $this->data['quantity'] = $badge_info['quantity'];
        } else {
            $this->data['quantity'] = 1;
        }

        if ($this->input->post('sponsor')) {
            // echo $this->input->post('sponsor');
            $this->data['sponsor'] = $this->input->post('sponsor');
        } elseif (!empty($badge_info)) {
            $this->data['sponsor'] = isset($badge_info['sponsor'])?$badge_info['sponsor']:null;
        } else {
            $this->data['sponsor'] = false;
        }

        if (isset($badge_id)) {
            $this->data['badge_id'] = $badge_id;
        } else {
            $this->data['badge_id'] = null;
        }

        if($this->User_model->getClientId()){
            if($this->data['sponsor']){
                redirect('badge', 'refresh');
            }
        }

        $this->load->model('Client_model');
        $this->data['to_clients'] = $this->Client_model->getClients(array());
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'quiz_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }
}
?>