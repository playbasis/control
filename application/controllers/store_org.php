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
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

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

        $this->getList(0);
    }

    public function getList($offset)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['main'] = 'store_org';
        $this->render_page('template');
    }

    public function getForm($store_org_id = null)
    {
        $this->data['main'] = 'store_org_form';

        if (isset($store_org_id) && ($store_org_id != 0)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($this->User_model->getClientId()) {

//                $merchant_info = $this->Merchant_model->retrieveMerchant($store_org_id);
//
//                if (!empty($merchant_info['branches'])) {
//                    $tmpArrBranchID = array();
//                    foreach ($merchant_info['branches'] as $branch) {
//                        array_push($tmpArrBranchID, $branch['b_id']);
//                    }
//
//                    $branches = $this->Merchant_model->retrieveBranches($client_id, $site_id, $tmpArrBranchID);
//                }
            }

//            $this->data['goodsgroups'] = $this->Goods_model->getGroupsAggregate($site_id);
//
//            $this->data['merchantGoodsGroupsJSON'] = $this->Merchant_model->retrieveMerchantGoodsGroupsJSON($client_id,
//                $site_id,
//                $store_org_id);
        }

        if ($this->input->post('store-name')) {
            $this->data['store_name_default'] = $this->input->post('store-name');
        } elseif (isset($store_info['name'])) {
            $this->data['store_name_default'] = $store_info['name'];
        } else {
            $this->data['store_name_default'] = '';
        }

        if ($this->input->post('store-id')) {
            $this->data['store_id_default'] = $this->input->post('store-id');
        } elseif (isset($store_info['id'])) {
            $this->data['store_id_default'] = $store_info['id'];
        } else {
            $this->data['store_id_default'] = '';
        }

        if ($this->input->post('store-desc')) {
            $this->data['store_desc_default'] = $this->input->post('store-desc');
        } elseif (isset($store_info['desc'])) {
            $this->data['store_desc_default'] = $store_info['desc'];
        } else {
            $this->data['store_desc_default'] = '';
        }

        if ($this->input->post('store-status')) {
            $this->data['store_status_default'] = $this->input->post('store-status');
        } elseif (isset($store_info['status'])) {
            $this->data['store_status_default'] = $store_info['status'];
        } else {
            $this->data['store_status_default'] = true;
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'store_org/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $store_data = $this->input->post();
            }
        }

        $this->getForm();
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

                if(isset($organizeId)){
                    if (MongoId::isValid($organizeId)) {
                        $result = $this->Store_org_model->retrieveOrganize($client_id, $site_id, ['id' => $organizeId]);

                        $this->output->set_status_header('200');
                        $response = $result;
                    }else{
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                    }
                }else{
                    $query_data = $this->input->get(null, true);

                    $result = $this->Store_org_model->retrieveOrganize($client_id, $site_id, $query_data);

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
                $brand_data = $this->input->post();

                $name = $brand_data['store-organize-name'];
                $desc = $brand_data['store-organize-desc'];
                $parent = !empty($brand_data['store-organize-parent']) ? $brand_data['store-organize-parent'] : null;
                $status = isset($brand_data['store-organize-status']) && $brand_data['store-organize-status'] == 'on' ? true : false;

                $result = null;
                if (!empty($brand_data) && !isset($organizeId)) {
                    $result = $this->Store_org_model->createOrganize($client_id, $site_id, $name, $desc, $parent, $status);
                }else{
                    if(MongoId::isValid($organizeId)){
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

                if (!$result) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                } elseif (!isset($organizeId)) {
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

    public function update($store_org_id)
    {

    }

    public function delete()
    {

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