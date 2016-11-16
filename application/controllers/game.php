<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class game extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Game_model');
        $this->load->model('Badge_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("game", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['is_enable_campaign'] = $this->validateAccess('campaign');
        if ($this->data['is_enable_campaign']){
            $this->data['game'] = 'campaign';
            $this->getlist('campaign');
        } else {
            $this->data['game'] = 'farm';
            $this->getForm('farm');
        }
    }

    public function farm()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'farm';
        $this->getForm('farm');
    }

    public function campaign_index()
    {
        if (!$this->validateAccess('game') || !$this->validateAccess('campaign') ) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'campaign';
        $this->getlist('campaign');
    }

    public function bingo()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'bingo';
        $this->getForm('bingo');
    }

    public function egg()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'egg';
        $this->getForm('egg');
    }

    public function pairs()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'pairs';
        $this->getForm('pairs');
    }

    public function catch_item()
    {
        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->data['game'] = 'catch_item';
        $this->getForm('catch_item');
    }

    public function edit()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->error['warning'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateModify('game')) {
                $this->data['message'] = $this->lang->line('error_permission');
            } else {
                $this->data['message'] = null;
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $data = $this->input->post();
                $game_data['game_name'] = $data['name'];
                $game_data['image']     = $data['image'];
                $game_data['status']    = (isset($data['status']) && ($data['status'] == "on")) ? true : false;
                if($data['name'] == 'egg' || $data['name'] == 'pairs' || $data['name'] == 'catch_item') {
                    $game_data['duration'] = $data['duration'] ? $data['duration'] : 1;
                    if($data['name'] == 'egg') $game_data['action_time'] = $data['action_time'] ? $data['action_time'] : 1;
                }
                $game_id = $this->Game_model->updateGameSetting($client_id, $site_id, $game_data);
                $exist_world = array();
                if($game_id){
                    foreach($data['worlds'] as $index => $world){
                        if($data['name'] == 'farm' || $data['name'] == 'bingo'){
                            $item_array = array();
                            foreach($world['world_item'] as $row_index => $row){
                                foreach($row as $column_index => $column) {
                                    if(!empty($column['item_id'])){
                                        $item_data['item_id']                          = new MongoId($column['item_id']);
                                        $item_data['description']                      = $column['item_description'];
                                        $item_data['item_config']['row']               = $row_index;
                                        $item_data['item_config']['column']            = $column_index;
                                        $item_data['item_config']['rule_id']              = new MongoId($column['rule_id']);
                                        if($data['name'] == 'farm'){
                                            $item_data['item_config']['days_to_deduct']    = (int)$column['item_deduct'];
                                            $item_data['item_config']['amount_to_harvest'] = (int)$column['item_harvest'];
                                        }
                                        $this->Game_model->updateGameStageItem($client_id, $site_id, $game_id, $item_data);
                                        array_push($item_array, $item_data['item_id']);
                                    }

                                }
                            }

                            if(isset($world['world_id'])){
                                try{
                                    array_push($exist_world, new MongoId($world['world_id']));
                                } catch (Exception $e){

                                }
                            }
                            $stage_data['stage_name']             = $world['world_name'];
                            $stage_data['stage_level']            = (int)$world['world_level'];
                            $stage_data['image']                  = $world['world_image'];
                            $stage_data['category']               = isset($world['world_category']) && !empty($world['world_category']) ? new MongoId($world['world_category']): "";
                            $stage_data['description']            = $world['world_description'];
                            $stage_data['reset_enable']           = $world['reset_enable'];
                            if ($world['reset_enable'] == 'on'){
                                $stage_data['reset_date']         = !empty($world['reset_date']) ? new MongoDate(strtotime($world['reset_date'])) : new MongoDate();
                                $stage_data['reset_duration']     = !empty($world['reset_duration']) ? (int)$world['reset_duration'] : 30;
                            } else {
                                $stage_data['reset_date']         = null;
                                $stage_data['reset_duration']     = null;
                            }
                            $stage_data['item_list']              = $item_array;
                            $stage_data['stage_config']['width']  = (int)$world['world_width'];
                            $stage_data['stage_config']['height'] = (int)$world['world_height'];
                        }
                        if($data['name'] == 'egg' || $data['name'] == 'pairs' || $data['name'] == 'catch_item'){
                            $stage_data['stage_name']             = $world['world_name'];
                            $stage_data['stage_level']            = (int)$world['world_level'];
                            $stage_data['image']                  = $world['world_image'];
                            $stage_data['description']            = $world['world_description'];
                            $stage_data['range_low']              = (int)$world['world_low'];
                            $stage_data['range_high']             = (int)$world['world_high'];
                            if(isset($world['world_id'])){
                                try{
                                    array_push($exist_world, new MongoId($world['world_id']));
                                } catch (Exception $e){

                                }
                            }
                        }
                        if($stage_data){
                            $stage = $this->Game_model->updateGameStage($client_id, $site_id, $game_id, $stage_data, isset($world['world_id']) ? $world['world_id'] : null );
                            if($stage){
                                try{
                                    array_push($exist_world, new MongoId($stage));
                                } catch (Exception $e){

                                }
                            }
                            $stage_data = array();
                        }
                    }
                    if($exist_world){
                        $delete_stage_data['exclude_id'] = $exist_world;
                        $to_delete_stage = $this->Game_model->getGameStage($client_id, $site_id, $game_id, $delete_stage_data);
                        if($to_delete_stage){
                            foreach($to_delete_stage as $del_stage){
                                if(!empty($del_stage['item_list'])){
                                    $delete_item_data['del_items'] = $del_stage['item_list'];
                                    $this->Game_model->deleteGameStageItem($client_id, $site_id, $game_id, $delete_item_data);
                                }
                            }

                            $this->Game_model->deleteGameStage($client_id, $site_id, $game_id, $delete_stage_data);
                        }
                    }
                }
            }
        }
        if(isset($data['name'])){
            $this->getForm($data['name']);
        } else {
            $this->getForm();
        }
    }

    public function insert()
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify('game')) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                try {
                    $client_id = $this->User_model->getClientId();
                    $site_id = $this->User_model->getSiteId();
                    $data = $this->input->post();
                    $game_data['game_id'] = new MongoId($data['game_id']);
                    $game_name = $this->Game_model->getGameNameByID($client_id, $site_id, $data['game_id']);
                    $game_data['game_name'] = $game_name ? $game_name : "";
                    $game_data['status'] = $data['status'] && $data['status'] == "on" ? true : false;
                    $game_data['campaign_id'] = new MongoId($data['campaign_id']);

                    $game_campaign_id = $this->Game_model->insertGameCampaign($client_id, $site_id, $game_data);
                    $this->output->set_status_header('200');
                    $response = $game_campaign_id;
                } catch (Exception $e) {
                    $this->output->set_status_header('404');
                    $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_item'));
                }
            }

            echo json_encode($response);
            die();
        }
    }

    public function delete()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['game'] = 'campaign';
        $this->error['message'] = null;

        if (!$this->validateModify('game')) {
            $this->data['message'] = $this->lang->line('error_permission');
        } else {
            if ($this->input->post('selected')) {
                foreach ($this->input->post('selected') as $game_campaign_id) {
                    $this->Game_model->deleteGameCampaign($game_campaign_id);
                }
            }
        }

        $this->getList();
    }

    public function status()
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify('game')) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                try {
                    $data = $this->input->post();
                    $status = $data['status'] && $data['status'] == "true" ? true : false;

                    $game_campaign_id = $this->Game_model->updateStatusGameCampaign(new MongoId($data['_id']), $status);
                    $this->output->set_status_header('200');
                    $response = $game_campaign_id;
                } catch (Exception $e) {
                    $this->output->set_status_header('404');
                    $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_item'));
                }
            }

            echo json_encode($response);
            die();
        }
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess('game')) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['game'] = 'campaign';
        $this->getList($offset);
    }

    public function getForm($game_name=false)
    {
        $config['base_url'] = site_url('game');
        if (!isset($this->data['success'])) {
            $this->data['success'] = '';
        }
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        if ($game_name) {
            $this->data['game_name'] = $game_name;
        } else {
            $this->data['game_name'] = "farm";
        }

        $game_data = $this->Game_model->getGameSetting($client_id, $site_id, $this->data);
        $game_id = $game_data ? $game_data['_id'] : $this->Game_model->updateGameSetting($client_id, $site_id, array('game_name' => $this->data['game_name']));


        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($game_data['status'])) {
            $this->data['status'] = $game_data['status'];
        } else {
            $this->data['status'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (isset($game_data['image']) && !empty($game_data['image'])) {
            $this->data['image'] = $game_data['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->data['image']) {
            $info = pathinfo($this->data['image']);
            if (isset($info['extension'])) {
                $extension = $info['extension'];
                $new_image = 'cache/' . utf8_substr($this->data['image'], 0,
                        utf8_strrpos($this->data['image'], '.')) . '-100x100.' . $extension;
                $this->data['thumb'] = S3_IMAGE . $new_image;
            } else {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }
        } else {
            $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        }

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

        if($this->data['game_name'] == 'egg' || $this->data['game_name'] == 'pairs' || $this->data['game_name'] == 'catch_item'){
            if ($this->input->post('duration')) {
                $this->data['duration'] = $this->input->post('duration');
            } elseif (isset($game_data['duration'])) {
                $this->data['duration'] = $game_data['duration'];
            } else {
                $this->data['duration'] = 1;
            }
        }
        if($this->data['game_name'] == 'egg'){
            if ($this->input->post('action_time')) {
                $this->data['action_time'] = $this->input->post('action_time');
            } elseif (isset($game_data['action_time'])) {
                $this->data['action_time'] = $game_data['action_time'];
            } else {
                $this->data['action_time'] = 1;
            }
        }

        $game_stage = $this->Game_model->getGameStage($client_id, $site_id, $game_id);
        foreach ($game_stage as $index => $stage) {
            if (isset($stage['_id'])) {
                $this->data['worlds'][$index]['world_id'] = $stage['_id'];
            } else {
                $this->data['worlds'][$index]['world_id'] = "";
            }

            if (isset($stage['stage_name'])) {
                $this->data['worlds'][$index]['world_name'] = $stage['stage_name'];
            } else {
                $this->data['worlds'][$index]['world_name'] = "";
            }

            if (isset($stage['stage_level'])) {
                $this->data['worlds'][$index]['world_level'] = $stage['stage_level'];
            } else {
                $this->data['worlds'][$index]['world_level'] = "";
            }

            if (isset($stage['image'])) {
                $this->data['worlds'][$index]['world_image'] = $stage['image'];
            } else {
                $this->data['worlds'][$index]['world_image'] = "";
            }

            if ($this->data['worlds'][$index]['world_image']) {
                $info = pathinfo($this->data['worlds'][$index]['world_image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($this->data['worlds'][$index]['world_image'], 0,
                            utf8_strrpos($this->data['worlds'][$index]['world_image'], '.')) . '-100x100.' . $extension;
                    $this->data['worlds'][$index]['world_thumb'] = S3_IMAGE . $new_image;
                } elsE {
                    $this->data['worlds'][$index]['world_thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                }
            } else {
                $this->data['worlds'][$index]['world_thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }

            if (isset($stage['description'])) {
                $this->data['worlds'][$index]['world_description'] = $stage['description'];
            } else {
                $this->data['worlds'][$index]['world_description'] = "";
            }

            if($this->data['game_name'] == 'farm' || $this->data['game_name'] == 'bingo') {
                if (isset($stage['category'])) {
                    $this->data['worlds'][$index]['world_category'] = $stage['category'] . "";
                } else {
                    $this->data['worlds'][$index]['world_category'] = "";
                }

                if (isset($stage['reset_enable'])) {
                    $this->data['worlds'][$index]['world_reset_enable'] = $stage['reset_enable'] . "";
                } else {
                    $this->data['worlds'][$index]['world_reset_enable'] = "";
                }

                if (isset($stage['reset_date'])) {
                    $this->data['worlds'][$index]['world_reset_date'] = dateMongotoReadable($stage['reset_date']);
                } else {
                    $this->data['worlds'][$index]['world_reset_date'] = "";
                }
                if (isset($stage['reset_duration'])) {
                    $this->data['worlds'][$index]['world_reset_duration'] = $stage['reset_duration'] . "";
                } else {
                    $this->data['worlds'][$index]['world_reset_duration'] = "";
                }

                if (isset($stage['stage_config']['width'])) {
                    $this->data['worlds'][$index]['world_width'] = $stage['stage_config']['width'];
                } else {
                    $this->data['worlds'][$index]['world_width'] = "";
                }

                if (isset($stage['stage_config']['height'])) {
                    $this->data['worlds'][$index]['world_height'] = $stage['stage_config']['height'];
                } else {
                    $this->data['worlds'][$index]['world_height'] = "";
                }

                if (isset($stage['item_list'])) {
                    foreach ($stage['item_list'] as $item) {
                        $item_data['item_id'] = $item;
                        $badge_item = $this->Badge_model->getBadgeById($client_id, $site_id, $item);
                        $game_item = $this->Game_model->getGameStageItem($client_id, $site_id, $game_id, $item_data);
                        if ($game_item) {
                            $row = $game_item[0]['item_config']['row'];
                            $column = $game_item[0]['item_config']['column'];
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_id'] = $game_item[0]['item_id'] . "";
                            $this->data['worlds'][$index]['world_item'][$row][$column]['rule_id'] = isset($game_item[0]['item_config']['rule_id']) ? $game_item[0]['item_config']['rule_id'] . "" : "";
                            if ($this->data['game_name'] == 'farm') {
                                $this->data['worlds'][$index]['world_item'][$row][$column]['item_harvest'] = $game_item[0]['item_config']['amount_to_harvest'];
                                $this->data['worlds'][$index]['world_item'][$row][$column]['item_deduct'] = $game_item[0]['item_config']['days_to_deduct'];
                            }
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_description'] = $game_item[0]['description'];
                            if ($badge_item && isset($badge_item['image'])) {
                                $this->data['worlds'][$index]['world_item'][$row][$column]['item_image'] = $badge_item['image'];
                                $info = pathinfo($this->data['worlds'][$index]['world_item'][$row][$column]['item_image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($this->data['worlds'][$index]['world_item'][$row][$column]['item_image'], 0,
                                            utf8_strrpos($this->data['worlds'][$index]['world_item'][$row][$column]['item_image'], '.')) . '-100x100.' . $extension;
                                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_thumb'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            }
                        }
                    }
                } else {
                    $this->data['worlds'][$index]['world_item'] = "";
                }
            }
            if($this->data['game_name'] == 'egg' || $this->data['game_name'] == 'pairs' || $this->data['game_name'] == 'catch_item'){
                if (isset($stage['range_low'])) {
                    $this->data['worlds'][$index]['world_low'] = $stage['range_low'];
                } else {
                    $this->data['worlds'][$index]['world_low'] = "";
                }

                if (isset($stage['range_high'])) {
                    $this->data['worlds'][$index]['world_high'] = $stage['range_high'];
                } else {
                    $this->data['worlds'][$index]['world_high'] = "";
                }
            }
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getList($offset= 0)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $parameter_url = "?";

        $config['base_url'] = site_url('game/page');
        $config['suffix'] =  $parameter_url;
        $config['first_url'] = $config['base_url'].$parameter_url;
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;
        if ($client_id) {
            $this->data['client_id'] = $client_id;
            $filter = array(
                'limit' => $config['per_page'],
                'offset' => $offset,
            );
            $campaigns  = $this->Game_model->getGameCampaign($client_id, $site_id, $filter);
            foreach ($campaigns as &$campaign){
                $campaign_data = $this->Game_model->getCampaign($client_id, $site_id, $campaign['campaign_id']);
                $campaign_data = $campaign_data[0];
                $campaign_data['status'] = isset($campaign['status']) ? $campaign['status'] : false;
                $campaign_data['_id'] = $campaign['_id'];
                $campaign_data['game_name'] = $campaign['game_name'];
                $campaign = $campaign_data;
            }
            foreach ($campaigns as $key => $row) {
                $name[$key] = $row['game_name'];
                $weight[$key] = $row['weight'];
            }
            array_multisort($name, SORT_ASC, $weight, SORT_ASC, $campaigns);
            $this->data['campaigns'] = $campaigns;
            $config['total_rows'] = $this->Game_model->countGameCampaign($client_id, $site_id);
        }
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

        $this->data['main'] = 'game';
        $this->load->vars($this->data);
        $this->render_page('template');
        
    }

    public function rule($ruleId=null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess('game')) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                if (isset($ruleId)) {
                    try {
                        $result = $this->Game_model->getRules($client_id, $site_id, $ruleId);
                        $result = $result[0];
                        $result['_id'] = $result['_id'] ."";
                        $this->output->set_status_header('200');
                        $response = $result;
                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_item'));
                    }
                } else {
                    $result = $this->Game_model->getRules($client_id, $site_id);
                    foreach ($result as &$document) {
                        if (isset($document['_id'])) {
                            $document['_id'] = $document['_id'] . "";
                        }
                    }
                    $count_rules = $this->Game_model->countRules($client_id, $site_id);

                    $this->output->set_status_header('200');
                    $response = array(
                        'total' => $count_rules,
                        'rows' => $result
                    );
                }

            }

            echo json_encode($response);
            die();
        }
    }

    public function game_ajax($gameId=null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess('game')) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                if (isset($gameId)) {
                    try {
                        $result = $this->Game_model->getGameList($client_id, $site_id, $gameId);
                        $result = $result[0];
                        $result['_id'] = $result['_id'] ."";
                        $this->output->set_status_header('200');
                        $response = $result;
                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_item'));
                    }
                } else {
                    $result = $this->Game_model->getGameList($client_id, $site_id);
                    foreach ($result as $key => $row) {
                        $name[$key] = $row['game_name'];
                    }
                    array_multisort($name, SORT_ASC, $result);
                    foreach ($result as &$document) {
                        if (isset($document['_id'])) {
                            $document['_id'] = $document['_id'] . "";
                        }
                    }

                    $count_rules = $this->Game_model->countGameList($client_id, $site_id);

                    $this->output->set_status_header('200');
                    $response = array(
                        'total' => $count_rules,
                        'rows' => $result
                    );
                }

            }

            echo json_encode($response);
            die();
        }
    }

    public function campaign($campaignId=null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess('game') || !$this->validateAccess('campaign')) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                if (isset($campaignId)) {
                    try {
                        $result = $this->Game_model->getCampaign($client_id, $site_id, $campaignId);
                        $result = $result[0];
                        $result['_id'] = $result['_id'] ."";
                        $this->output->set_status_header('200');
                        $response = $result;
                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_item'));
                    }
                } else {
                    $result = $this->Game_model->getCampaign($client_id, $site_id);
                    foreach ($result as $key => $row) {
                        $name[$key] = $row['name'];
                    }
                    array_multisort($name, SORT_ASC, $result);
                    foreach ($result as &$document) {
                        if (isset($document['_id'])) {
                            $document['_id'] = $document['_id'] . "";
                        }
                    }
                    $count_rules = $this->Game_model->countCampaign($client_id, $site_id);

                    $this->output->set_status_header('200');
                    $response = array(
                        'total' => $count_rules,
                        'rows' => $result
                    );
                }

            }

            echo json_encode($response);
            die();
        }
    }

    private function validateModify($feature)
    {
        if ($this->User_model->hasPermission('modify', $feature)) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess($feature)
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', $feature) && $this->Feature_model->getFeatureExistByClientId($client_id, $feature)
        ) {
            return true;
        } else {
            return false;
        }
    }
}
