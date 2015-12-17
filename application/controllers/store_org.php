<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Store_org extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Store_org_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("store_org", $lang['folder']);
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
        $this->data['main'] = 'store_org_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function organize($organizeId = null)
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

                if (isset($organizeId)) {
                    if (MongoId::isValid($organizeId)) {
                        $result = $this->Store_org_model->retrieveOrganizeById($organizeId);
                        if(isset($result['_id'])){
                            $result['_id'] = $result['_id']."";
                        }
                        if(isset($result['parent'])){
                            $tmp = $result['parent']."";
                            $result['parent'] = array();
                            $result['parent']['_id'] = $tmp;

                            $org_res = $this->Store_org_model->retrieveOrganizeById($result['parent']['_id']);
                            if(isset($org_res))
                                $result['parent']['name'] = $org_res['name'];
                        }

                        $this->output->set_status_header('200');
                        $response = $result;
                    } else {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                    }
                } else {
                    $query_data = $this->input->get(null, true);

                    $result = $this->Store_org_model->retrieveOrganize($client_id, $site_id, $query_data);
                    foreach($result as &$document){
                        if(isset($document['_id'])){
                            $document['_id'] = $document['_id']."";
                        }
                        if(isset($document['parent'])){
                            $tmp = $document['parent']."";
                            $document['parent'] = array();
                            $document['parent']['_id'] = $tmp;

                            $org_res = $this->Store_org_model->retrieveOrganizeById($document['parent']['_id']);
                            if(isset($org_res))
                                $document['parent']['name'] = $org_res['name'];
                        }
                    }

                    $this->output->set_status_header('200');
                    $response = array(
                        'total' => count($result),
                        'rows' => $result
                    );
                }

                echo json_encode($response);
                die();

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                //todo: Add validation here
                $organize_data = $this->input->post();

                $name = !empty($organize_data['store-organize-name']) ? $organize_data['store-organize-name'] : null;
                $desc = !empty($organize_data['store-organize-desc']) ? $organize_data['store-organize-desc'] : null;
                $parent = !empty($organize_data['store-organize-parent']) ? $organize_data['store-organize-parent'] : null;
                $status = isset($organize_data['store-organize-status']) && $organize_data['store-organize-status'] == 'on' ? true : false;

                $result = null;
                if (!empty($organize_data) && !isset($organizeId)) {
                    if (isset($organize_data['action']) && $organize_data['action'] == 'delete' && isset($organize_data['id']) && !empty($organize_data['id'])) {
                        foreach ($organize_data['id'] as &$id_entry) {
                            if(!MongoId::isValid($id_entry)){
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'error'));
                                die;
                            }
                        }
                        $result = $this->Store_org_model->deleteOrganizeByIdArray($organize_data['id']);
                    } else {
                        $result = $this->Store_org_model->createOrganize($client_id, $site_id, $name, $desc, $parent,
                            $status);
                    }
                } else {
                    if (MongoId::isValid($organizeId)) {
                        if(isset($organize_data['action']) && $organize_data['action'] == 'delete' ){
                            $result = $this->Store_org_model->deleteOrganizeById($organizeId);
                        }else {
                            $result = $this->Store_org_model->updateOrganizeById($organizeId, array(
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'name' => $name,
                                'description' => $desc,
                                'parent' => $parent,
                                'status' => $status
                            ));
                        }
                    }
                }

                if (!$result) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                } elseif (!isset($organizeId) && !isset($organize_data['action'])) {
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

    public function node($nodeId = null)
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

                if (isset($nodeId)) {
                    if (MongoId::isValid($nodeId)) {
                        $result = $this->Store_org_model->retrieveNodeById($nodeId);
                        if(isset($result['_id'])){
                            $result['_id'] = $result['_id']."";
                        }
                        if(isset($result['organize'])){
                            $tmp = $result['organize']."";
                            $result['organize'] = array();
                            $result['organize']['_id'] = $tmp;

                            $org_res = $this->Store_org_model->retrieveOrganizeById($result['organize']['_id']);
                            if(isset($org_res))
                                $result['organize']['name'] = $org_res['name'];
                        }
                        if(isset($result['parent'])){
                            $tmp = $result['parent']."";
                            $result['parent'] = array();
                            $result['parent']['_id'] = $tmp;

                            $node_res = $this->Store_org_model->retrieveNodeById($result['parent']['_id']);
                            if(isset($node_res))
                                $result['parent']['name'] = $node_res['name'];
                        }

                        $this->output->set_status_header('200');
                        $response = $result;
                    } else {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                    }
                } else {
                    $query_data = $this->input->get(null, true);

                    $result = $this->Store_org_model->retrieveNode($client_id, $site_id, $query_data);
                    foreach($result as &$document){
                        if(isset($document['_id'])){
                            $document['_id'] = $document['_id']."";
                        }
                        if(isset($document['organize'])){
                            $tmp = $document['organize']."";
                            $document['organize'] = array();
                            $document['organize']['_id'] = $tmp;

                            $org_res = $this->Store_org_model->retrieveOrganizeById($document['organize']['_id']);
                            if(isset($org_res))
                                $document['organize']['name'] = $org_res['name'];
                        }
                        if(isset($document['parent'])){
                            $tmp = $document['parent']."";
                            $document['parent'] = array();
                            $document['parent']['_id'] = $tmp;

                            $node_res = $this->Store_org_model->retrieveNodeById($document['parent']['_id']);
                            if(isset($node_res))
                                $document['parent']['name'] = $node_res['name'];
                        }
                    }


                    $this->output->set_status_header('200');

                    $response = array(
                        'total' => count($result),
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
                $node_data = $this->input->post();

                $name = !empty($node_data['node-name']) ? $node_data['node-name'] : null; //null is use for delete
                $desc = !empty($node_data['node-desc']) ? $node_data['node-desc'] : null; //null is use for delete
                $organize = !empty($node_data['node-organize']) ? $node_data['node-organize'] : null;
                $parent = !empty($node_data['node-parent']) ? $node_data['node-parent'] : null;
                $status = isset($node_data['node-status']) && $node_data['node-status'] == 'on' ? true : false;

                $result = null;
                if (!empty($node_data) && !isset($nodeId)) {
                    if (isset($node_data['action']) && $node_data['action'] == 'delete' && isset($node_data['id']) && !empty($node_data['id'])) {
                        foreach ($node_data['id'] as &$id_entry) {
                            if(!MongoId::isValid($id_entry)){
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'error'));
                                die;
                            }
                        }
                        $result = $this->Store_org_model->deleteNodeByIdArray($node_data['id']);
                    } else {
                        $result = $this->Store_org_model->createNode($client_id, $site_id, $name, $desc,
                            $organize, $parent, $status);
                    }
                } else {
                    if (MongoId::isValid($nodeId)) {
                        if(isset($node_data['action']) && $node_data['action'] == 'delete' ){
                            $result = $this->Store_org_model->deleteNodeById($nodeId);
                        }else{
                            $result = $this->Store_org_model->updateNodeById($nodeId, array(
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'name' => $name,
                                'description' => $desc,
                                'organize' => $organize,
                                'parent' => $parent,
                                'status' => $status
                            ));
                        }
                    }
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
        if ($this->User_model->hasPermission('modify', 'store_org')) {
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
                'store_org') && $this->Feature_model->getFeatureExistByClientId($client_id, 'store_org')
        ) {
            return true;
        } else {
            return false;
        }
    }

}