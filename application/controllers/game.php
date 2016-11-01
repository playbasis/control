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
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'game';
        $this->data['form'] = 'game/edit/';
        $this->getList();
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
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            } else {
                $this->data['message'] = null;
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $data = $this->input->post();
                $game_data['game_name'] = $data['name'];
                $game_data['image']     = $data['image'];
                $game_data['status']    = (isset($data['status']) && ($data['status'] == "on")) ? true : false;

                $game_id = $this->Game_model->updateGameSetting($client_id, $site_id, $game_data);
                $exist_world = array();
                if($game_id){
                    foreach($data['worlds'] as $index => $world){
                        $item_array = array();
                        foreach($world['world_item'] as $row_index => $row){
                            foreach($row as $column_index => $column) {
                                if(!empty($column['item_id'])){
                                    $item_data['item_id']                          = new MongoId($column['item_id']);
                                    $item_data['description']                      = $column['item_description'];
                                    $item_data['item_config']['row']               = $row_index;
                                    $item_data['item_config']['column']            = $column_index;
                                    $item_data['item_config']['days_to_deduct']    = (int)$column['item_deduct'];
                                    $item_data['item_config']['amount_to_harvest'] = (int)$column['item_harvest'];
                                    $this->Game_model->updateGameStageItem($client_id, $site_id, $game_id, $item_data);
                                    array_push($item_array, $item_data['item_id']);
                                    if(isset($column['item_image'])){
                                        foreach ($column['item_image'] as $template_index => $template){
                                            $item_template_data['template_id'] = new MongoId($template_index);
                                            $item_template_data['item_id']     = new MongoId($column['item_id']);
                                            $item_template_data['images']      = $template;
                                            $item_template_data['thumb']       = $column['item_thumb'][$template_index];
                                            $this->Game_model->updateGameItemTemplate($client_id, $site_id, $game_id, $item_template_data);
                                        }
                                    }
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

                        $stage = $this->Game_model->updateGameStage($client_id, $site_id, $game_id, $stage_data, isset($world['world_id']) ? $world['world_id'] : null );
                        if($stage){
                            try{
                                array_push($exist_world, new MongoId($stage));
                            } catch (Exception $e){

                            }
                        }
                        $stage_data = array();
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

        $this->getList();
    }

    public function template($template_id = null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess()) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                $game_id = $this->Game_model->getGameSetting($client_id, $site_id, array('game_name' => 'farm'));
                $template = $this->Game_model->getGameTemplate($client_id, $site_id, $game_id['_id']);
                if(!$template){
                    $template_data['template_name']     = "default";
                    $template_data['weight']            = null;
                    $template_data['date_start']        = null;
                    $template_data['date_added']        = null;
                    $template_data['status']            = true;
                    $template_id = $this->Game_model->updateGameTemplate($client_id, $site_id, $game_id['_id'], $template_data);
                    $template = $this->Game_model->getGameTemplate($client_id, $site_id, $game_id['_id']);
                }

                foreach ($template as &$document) {
                    if (isset($document['_id'])) {
                        $document['_id'] = $document['_id'] . "";
                    }
                }

                $count_template = $this->Game_model->countGameTemplate($client_id, $site_id, $game_id['_id']);

                $this->output->set_status_header('200');
                $response = array(
                    'total' => $count_template,
                    'rows' => $template
                );
                echo json_encode($response);
                die();

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                $data = $this->input->post();
                if ($template_id) $template_data['id']  = $template_id;
                $template_data['template_name']         = $data['template_name'];
                $template_data['weight']                = (int)$data['template_weight'];
                $template_data['date_start']            = $data['template_start'];
                $template_data['date_end']              = $data['template_end'];
                $template_data['status']                = (isset($data['template_status']) && ($data['template_status'] == "on")) ? true : false;

                $game_id = $this->Game_model->getGameSetting($client_id, $site_id, array('game_name' => 'farm'));
                $template = $this->Game_model->updateGameTemplate($client_id, $site_id, $game_id['_id'], $template_data);
                if (!$template) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                    die();
                } else {
                    $this->output->set_status_header('200');
                    // todo: should return update object
                    echo json_encode(array('status' => 'success'));
                    die();
                }
            }  elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                if ($template_id) {
                    try {
                        $game_id = $this->Game_model->getGameSetting($client_id, $site_id, array('game_name' => 'farm'));
                        $result = $this->Game_model->deleteGameTemplate($client_id, $site_id, $game_id['_id'], $template_id);
                        if (!$result) {
                            $this->output->set_status_header('400');
                            echo json_encode(array('status' => 'error', 'message' => $this->lang->line('')));
                            die();
                        } else {
                            $this->output->set_status_header('200');
                            echo json_encode(array('status' => 'success'));
                            die();
                        }
                    } catch (Exception $e) {
                        $this->output->set_status_header('400');
                        echo json_encode(array('status' => 'error', 'message' => $this->lang->line('')));
                        die();
                    }
                } else {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('')));
                    die();
                }
            }
        }
    }

    private function getList()
    {
        $config['base_url'] = site_url('game');
        if (!isset($this->data['success'])) {
            $this->data['success'] = '';
        }
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $this->data['game_name'] = "farm";

        $game_data = $this->Game_model->getGameSetting($client_id, $site_id, $this->data);
        $game_id = $game_data ? $game_data['_id'] : $this->Game_model->updateGameSetting($client_id, $site_id, array('game_name' => 'farm'));
        $game_stage = $this->Game_model->getGameStage($client_id, $site_id, $game_id);
        $count_template = $this->Game_model->countGameTemplate($client_id, $site_id, $game_id);

        if (isset($count_template)) {
            $this->data['total_template'] = $count_template;
        } else {
            $this->data['total_template'] = 0;
        }
        
        foreach ($game_stage as $index => $stage){
            if(isset($stage['_id'])) {
                $this->data['worlds'][$index]['world_id'] = $stage['_id'];
            } else {
                $this->data['worlds'][$index]['world_id'] = "";
            }

            if(isset($stage['stage_name'])) {
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

            if (isset($stage['description'])) {
                $this->data['worlds'][$index]['world_description'] = $stage['description'];
            } else {
                $this->data['worlds'][$index]['world_description'] = "";
            }

            if (isset($stage['item_list'])) {
                foreach($stage['item_list'] as $item){
                    $item_data['item_id'] = $item;
                    $game_item = $this->Game_model->getGameStageItem($client_id, $site_id, $game_id, $item_data);
                    $row = $game_item[0]['item_config']['row'];
                    $column = $game_item[0]['item_config']['column'];
                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_id'] = $game_item[0]['item_id'] . "";
                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_harvest'] = $game_item[0]['item_config']['amount_to_harvest'];
                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_deduct'] = $game_item[0]['item_config']['days_to_deduct'];
                    $this->data['worlds'][$index]['world_item'][$row][$column]['item_description'] = $game_item[0]['description'];

                    $game_item_template = $this->Game_model->getGameItemTemplate($client_id, $site_id, $game_id, $item_data);
                    if($game_item_template && is_array($game_item_template)) foreach ($game_item_template as $template){
                        if(isset($template['images'])){
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_image'][$template['template_id'].""] = $template['images'];
                        } else {
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_image'][$template['template_id'].""] = 'no_image.jpg';
                        }
                        if(isset($template['thumb'])){
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_thumb'][$template['template_id'].""] = $template['thumb'];
                        } else {
                            $this->data['worlds'][$index]['world_item'][$row][$column]['item_thumb'][$template['template_id'].""] = S3_IMAGE . "cache/no_image-100x100.jpg";
                        }
                    } else {
                        $this->data['worlds'][$index]['world_item'][$row][$column]['item_image'] = array();
                        $this->data['worlds'][$index]['world_item'][$row][$column]['item_thumb'] = array();
                    }
                }
            } else {
                $this->data['worlds'][$index]['world_item'] = "";
            }
        }

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
            } elsE {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }
        } else {
            $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        }

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'game')) {
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

        if ($this->User_model->hasPermission('access', 'game') && $this->Feature_model->getFeatureExistByClientId($client_id, 'game')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
