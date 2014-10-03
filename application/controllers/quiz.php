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

        $this->form_validation->set_rules('quiz_name', $this->lang->line('sms-mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quiz_description', $this->lang->line('sms-account_sid'), 'trim|required|xss_clean');

        if($this->input->post()){

            $data = $this->input->post();

            $quiz = array();

            foreach($data as $key => $value){
                if($key == "quiz"){
                    foreach($value as $qkey => $qvalue){
                        if($qkey == "grades"){
                            $grades = array();

                            foreach($qvalue as $ggkey => $ggvalue){
                                $grades['grade_id'] = $ggkey;
                                foreach($ggvalue as $gggkey => $gggvalue){

                                    if($gggkey == "rewards"){

                                        foreach($gggvalue as $rkey => $rvalue){
                                            if($rkey == "badge"){

                                                $badge = array();

                                                foreach($rvalue as $bkey => $bvalue){
                                                    if(!empty($bvalue)){
                                                        $badge["badge_id"] = $bkey;
                                                        $badge["badge_value"] = $bvalue;
                                                    }

                                                }

                                                if($badge){
                                                    $grades[$gggkey]["rewards"]["badge"] = $badge;
                                                }

                                            }
                                            if($rkey == "exp" && !empty($rvalue)){
                                                $grades[$gggkey]["rewards"]["exp"] = $rvalue;
                                            }
                                            if($rkey == "point" && !empty($rvalue)){
                                                $grades[$gggkey]["rewards"]["point"] = $rvalue;
                                            }
                                            if($rkey == "custom"){
                                                $custom = array();

                                                foreach($rvalue as $bkey => $bvalue){
                                                    var_dump($bkey);
                                                    var_dump($bvalue);
                                                    if(!empty($bvalue)){
                                                        $custom["custom_id"] = $bkey;
                                                        $custom["custom_value"] = $bvalue;
                                                    }

                                                }

                                                if($custom){
                                                    $grades[$gggkey]["rewards"]["custom"] = $custom;
                                                }

                                            }
                                        }

                                    }else{
                                        $grades[$gggkey] = $gggvalue;
                                    }

                                }
                                $quiz["grades"][] = $grades;
                            }

                        }

                    }
                }else{
                    $quiz[$key] = $value;
                }
            }
            echo "<pre>";
            var_dump($quiz);
            echo "</pre>";
            $this->data['quiz'] = $quiz;

            if($this->form_validation->run()){
                var_dump($this->input->post());
                exit();
            }
        }

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
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

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


        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        $this->data['badge_list'] = array();
        $this->data['badge_list'] = $this->Badge_model->getBadgeBySiteId(array("site_id" => $data['site_id'] ));

        $this->data['point_list'] = array();
        $this->data['point_list'] = $this->Reward_model->getAnotherRewardBySiteId($data['site_id']);

        $this->data['client_id'] = $data['client_id'];
        $this->data['site_id'] = $data['site_id'];

        $this->data['main'] = 'quiz_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }
}
?>