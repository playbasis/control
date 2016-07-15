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
            $this->data['message'] = null;
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();
            $data = $this->input->post();

            $game_data['name']      = $data['name'];
            $game_data['image']     = $data['image'];
            $game_data['status']    = $data['status'] && $data['status'] == "on" ? true : false;
            $game_data['template']  = $data['template'];

            $game_id = $this->Game_model->updateGameSetting($client_id, $site_id, $game_data);
            if($game_id){
                foreach($data['worlds'] as $world){
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
                            }
                        }
                    }

                    $stage_data['id']                     = $world['world_id'];
                    $stage_data['name']                   = $world['world_name'];
                    $stage_data['level']                  = (int)$world['world_level'];
                    $stage_data['image']                  = $world['world_image'];
                    $stage_data['category']               = new MongoId($world['world_category']);
                    $stage_data['description']            = $world['world_description'];
                    $stage_data['item_list']              = $item_array;
                    $stage_data['stage_config']['width']  = (int)$world['world_width'];
                    $stage_data['stage_config']['height'] = (int)$world['world_height'];

                    $stage = $this->Game_model->updateGameStage($client_id, $site_id, $game_id, $stage_data);
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

                $game_id = $this->Game_model->getGameSetting($client_id, $site_id, array('name' => 'Farm'));
                $template = $this->Game_model->getGameTemplate($client_id, $site_id, $game_id['_id']);
                if(!$template){
                    $template_data['name']       = "default";
                    $template_data['weight']     = null;
                    $template_data['start'] = null;
                    $template_data['end']   = null;
                    $template_data['status']     = true;
                    $template_id = $this->Game_model->updateGameTemplate($client_id, $site_id, $game_id['_id'], $template_data);
                    $template = $this->Game_model->getGameTemplate($client_id, $site_id, $game_id['_id']);
                    log_message('error', print_r($template,true));
                }

                $count_template = $this->Game_model->countGameTemplate($client_id, $site_id, $game_id['_id']);
                log_message('error', $count_template);

                $this->output->set_status_header('200');
                $response = array(
                    'total' => $count_template,
                    'rows' => $template
                );
                echo json_encode($response);
                die();

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->input->post();
                log_message('error', "post");
                if ($template_id) $template_data['id']       = $template_id;
                $template_data['name']       = $data['template_name'];
                $template_data['weight']     = (int)$data['template_weight'];
                $template_data['date_start'] = $data['template_start'];
                $template_data['date_end']   = $data['template_end'];
                $template_data['status']     = (isset($data['template_status']) && ($data['template_status'] == "on")) ? true : false;

                $game_id = $this->Game_model->getGameSetting($client_id, $site_id, array('name' => 'Farm'));
                $template_id = $this->Game_model->updateGameTemplate($client_id, $site_id, $game_id['_id'], $template_data);
                log_message('error', $template_id);
                if (!$template_id) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                    die();
                } else {
                    $this->output->set_status_header('200');
                    // todo: should return update object
                    echo json_encode(array('status' => 'success'));
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
        $this->data['name'] = "Farm";

        $setting = $this->Game_model->getGameSetting($client_id, $site_id, $this->data);

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (isset($setting['image']) && !empty($setting['image'])) {
            $this->data['image'] = $setting['image'];
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

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'setting') && $this->Feature_model->getFeatureExistByClientId($client_id, 'setting')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
