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

            log_message('error', print_r($data,true));

            $game_data['name']      = $data['name'];
            $game_data['image']     = $data['image'];
            $game_data['status']    = $data['status'] && $data['status'] == "on" ? true : false;
            $game_data['template']  = $data['template'];

            $game_id = $this->Game_model->updateGameSetting($client_id, $site_id, $game_data);
            log_message('error', 'game');
            log_message('error', print_r($game_id,true));
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

    public function template()
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

                $category = $this->input->get('filter_category');

                $result = $this->Badge_model->getAllBadgeByCategory($client_id, $site_id, $category);
                foreach ($result as &$document) {
                    if (isset($document['_id']) && isset($document['badge_id'])) {
                        $document['_id'] = $document['badge_id'] . "";
                        unset($document['badge_id']);
                    }
                }

                $count_category = $this->Badge_model->countAllBadgeByCategory($client_id, $site_id, $category);

                $this->output->set_status_header('200');
                $response = array(
                    'total' => $count_category,
                    'rows' => $result
                );


            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                //todo: Add validation here
                $category_data = $this->input->post();

                $name = !empty($category_data['category-name']) ? $category_data['category-name'] : null;

                $result = null;
                if (!empty($category_data) && !isset($categoryId)) {
                    if (isset($category_data['action']) && $category_data['action'] == 'delete' && isset($category_data['id']) && !empty($category_data['id'])) {
                        foreach ($category_data['id'] as &$id_entry) {
                            try {
                                $id_entry = new MongoId($id_entry);
                            } catch (Exception $e) {
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'error'));
                                die;
                            }
                        }
                        $result = $this->Badge_model->deleteItemCategoryByIdArray($client_id, $site_id, $category_data['id']);
                    } else {
                        $chk_name = $this->Badge_model->retrieveItemCategoryByName($client_id, $site_id, $name);
                        if($chk_name){
                            $this->output->set_status_header('400');
                            echo json_encode(array('status' => 'name duplicate'));
                            die;
                        }else {
                            $result = $this->Badge_model->createItemCategory($client_id, $site_id, $name);
                        }
                    }
                } else {
                    try {
                        $categoryId = new MongoId($categoryId);
                        if (isset($category_data['action']) && $category_data['action'] == 'delete') {
                            $result = $this->Badge_model->deleteItemCategory($client_id, $site_id, $categoryId);
                        } else {
                            $chk_name = $this->Badge_model->retrieveItemCategoryByNameButNotID($client_id, $site_id, $name, $categoryId);
                            if($chk_name){
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'name duplicate'));
                                die;
                            }else {
                                $result = $this->Badge_model->updateItemCategory($categoryId, array(
                                    'client_id' => $client_id,
                                    'site_id' => $site_id,
                                    'name' => $name
                                ));
                            }
                        }
                    } catch (Exception $e) {
                        $this->output->set_status_header('400');
                        echo json_encode(array('status' => 'error'));
                        die;
                    }
                }

                if (!$result) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                } elseif (!isset($categoryId) && !isset($category_data['action'])) {
                    $this->output->set_status_header('201');
                    // todo: should return newly create object
                    $category_result = $this->Badge_model->retrieveItemCategoryById($result);
                    if (isset($category_result['_id'])) {
                        $category_result['_id'] = $category_result['_id'] . "";
                    }
                    echo json_encode(array('status' => 'success', 'rows' => $category_result));
                } else {
                    $this->output->set_status_header('200');
                    // todo: should return update object
                    echo json_encode(array('status' => 'success'));
                }
            }

            echo json_encode($response);
            die();
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
