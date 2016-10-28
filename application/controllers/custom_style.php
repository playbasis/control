<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Custom_style extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Custom_style_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("custom_style", $lang['folder']);
    }

    public function index()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        } elseif (!$this->validateModify()) {
            echo "<script>alert('" . $this->lang->line('error_permission') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm();
    }

    public function getForm()
    {
        $this->data['main'] = 'custom_style_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }


    public function style($styleId = null)
    {
        if ($this->session->userdata('user_id') /*&& $this->input->is_ajax_request()*/) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess()) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                if (isset($styleId)) {
                    try {
                        $styleId = new MongoId($styleId);
                        $result = $this->Custom_style_model->retrieveStyleById($client_id, $site_id, $styleId);
                        $this->output->set_status_header('200');
                        $response = $result;
                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                    }
                } else {
                    $query_data = $this->input->get(null, true);
                    $result = $this->Custom_style_model->retrieveStyle($client_id, $site_id, $query_data);
                    foreach ($result as &$document) {
                        if (isset($document['_id'])) {
                            $document['_id'] = $document['_id'] . "";
                        }
                    }

                    $this->output->set_status_header('200');
                    $count_nodes = $this->Custom_style_model->countStyles($client_id, $site_id);

                    $response = array(
                        'total' => $count_nodes,
                        'rows' => $result
                    );
                }

                echo json_encode($response);

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                //todo: Add validation here
                $post_data = $this->input->post();

                $name = !empty($post_data['style-name']) ? $post_data['style-name'] : null; //null is use for delete
                $key = !empty($post_data['style-key']) ? $post_data['style-key'] : null;
                $value = !empty($post_data['style-value']) ? $post_data['style-value'] : null; //null is use for delete


                $result = null;
                if (!empty($post_data) && !isset($styleId)) {
                    if (isset($post_data['action']) && $post_data['action'] == 'delete' && isset($post_data['id']) && !empty($post_data['id'])) {
                        foreach ($post_data['id'] as &$id_entry) {
                            try {
                                $id_entry = new MongoId($id_entry);
                            } catch (Exception $e) {
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'error'));
                                die;
                            };
                        }
                        $result = $this->Custom_style_model->deleteStyleByIdArray($post_data['id']);
                    } else {
                        if(!$name){
                            $this->output->set_status_header('400');
                            echo json_encode(array('status' => 'name require'));
                            die;
                        }
                        if(!$key){
                            $this->output->set_status_header('400');
                            echo json_encode(array('status' => 'key require'));
                            die;
                        }

                        $style_chk =$this->Custom_style_model->retrieveByNameAndKey($client_id, $site_id, $name, $key);
                        if($style_chk){
                            $this->output->set_status_header('400');
                            echo json_encode(array('status' => 'key duplicate'));
                            die;
                        }else {
                            $result = $this->Custom_style_model->createStyle($client_id, $site_id, $name, $key, $value);
                        }
                    }
                } else {
                    try {
                        $styleId = new MongoId($styleId);
                        if (isset($post_data['action']) && $post_data['action'] == 'delete') {
                            $result = $this->Custom_style_model->deleteStyleById($styleId);
                        } else {
                            if(!$name){
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'name require'));
                                die;
                            }
                            if(!$value){
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'key require'));
                                die;
                            }
                            $style_chk =$this->Custom_style_model->retrieveStyleByNameAndKeyButNotID($client_id, $site_id, $name, $key, $styleId);
                            if($style_chk) {
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'key duplicate'));
                                die;
                            }else{
                                $result = $this->Custom_style_model->updateStyleById($styleId, array(
                                    'client_id' => $client_id,
                                    'site_id' => $site_id,
                                    'name' => $name,
                                    'key' => $key,
                                    'value' => $value,
                                ));
                            }
                        }
                    } catch (Exception $e) {
                        $this->output->set_status_header('400');
                        echo json_encode(array('status' => 'error'));
                        die;
                    };
                }

                if (!$result) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                } elseif (!isset($nodeId) && !isset($node_data['action'])) {
                    $this->output->set_status_header('201');
                    // todo: should return newly create object
                    echo json_encode(array('status' => 'success', 'rows' => $result));
                } else {
                    $this->output->set_status_header('200');
                    // todo: should return update object
                    echo json_encode(array('status' => 'success'));
                }
            }
        }
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'custom_style')) {
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

        if ($this->User_model->hasPermission('access',
                'custom_style') && $this->Feature_model->getFeatureExistByClientId($client_id, 'custom_style')
        ) {
            return true;
        } else {
            return false;
        }
    }

}