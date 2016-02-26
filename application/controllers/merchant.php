<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Merchant extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Merchant_model');
        $this->load->model('Goods_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("merchant", $lang['folder']);
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

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'merchant/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

//        $merchants = $this->Merchant_model->countMerchants($client_id, $site_id);

//        $this->load->model('Permission_model');
//        $this->load->model('Plan_model');

        // Get Limit
//        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
//        $limit_merchant = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'merchant');

//        $this->data['message'] = null;

//        if ($limit_merchant && $merchants >= $limit_merchant) {
//            $this->data['message'] = $this->lang->line('error_merchants_limit');
//        }

        $this->form_validation->set_rules('merchant-name', $this->lang->line('entry_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('merchant-desc', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('merchant-status', $this->lang->line('entry_status'), 'trim|xss_clean');

        $newBranches = $this->input->post('newBranches');

        if ($newBranches != false && !empty($newBranches)) {
            $i = 0;
            foreach ($newBranches as $branch) {
                if (!empty($branch['branchName'])) {
                    $this->form_validation->set_rules('newBranches[' . $i++ . '][branchName]',
                        $this->lang->line('entry_branch_name'),
                        'trim|xss_clean|min_length[3]|callback_alpha_dash_space');
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $merchant_data = $this->input->post();

                $batch_data = array();

                if (isset($merchant_data['newBranches']) && !empty($merchant_data['newBranches'])) {
                    $postArr = array_map('array_filter', $merchant_data['newBranches']);
                    foreach ($postArr as $key => $branch) {
                        if (!array_key_exists('branchName', $branch)) {
                            unset($postArr[$key]);
                        }
                    }
                    $merchant_data['newBranches'] = array_values($postArr);


                    foreach ($merchant_data['newBranches'] as $branch) {
                        array_push($batch_data, array(
                            'client_id' => $client_id,
                            'site_id' => $site_id,
                            'branch_name' => $branch['branchName'],
                            'pin_code' => $this->Merchant_model->generatePINCode($client_id, $site_id),
                            'status' => !empty($branch['status']) ? true : false,
                            'deleted' => false,
                            'date_added' => new MongoDate(),
                            'date_modified' => new MongoDate()
                        ));
                    }
                }
                $data['branches'] = array();

                if (!empty($batch_data)) {
                    $completed_flag = $this->Merchant_model->bulkInsertBranches($batch_data);

                    if ($completed_flag) {
                        foreach ($batch_data as $entry) {
                            array_push($data['branches'],
                                array('b_id' => $entry['_id'], 'b_name' => $entry['branch_name']));
                        }
                    }
                }

                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $data['name'] = $merchant_data['merchant-name'];
                $data['desc'] = $merchant_data['merchant-desc'];
                $data['status'] = !empty($merchant_data['merchant-status']) ? true : false;

                // TODO : Need to add goods group support for insert

                $createdMerchantId = $this->Merchant_model->createMerchant($data);
                if ($createdMerchantId) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/merchant', 'refresh');
                }
            }
        }
        $this->getForm();
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if ($this->input->post('selected') && $this->error['message'] == null) {
            foreach ($this->input->post('selected') as $merchant_id) {
                $this->Merchant_model->deleteMerchant($merchant_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/merchant', 'refresh');
        }

        $this->getList(0);
    }

    public function update($merchant_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'merchant/update/' . $merchant_id;
        $this->data['merchant_id'] = $merchant_id;

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->form_validation->set_rules('merchant-name', $this->lang->line('entry_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean|callback_alpha_dash_space');
        $this->form_validation->set_rules('merchant-desc', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('merchant-status', $this->lang->line('entry_status'), 'trim|xss_clean');

        // New Branch(es)
        $newBranches = $this->input->post('newBranches');
        if ($newBranches != false && !empty($newBranches)) {
            $i = 0;
            foreach ($newBranches as $branch) {
                if (!empty($branch['branchName'])) {
                    $this->form_validation->set_rules('newBranches[' . $i++ . '][branchName]',
                        $this->lang->line('entry_branch_name'), 'trim|xss_clean|alpha_dash');
                }
            }
        }

        // New Good Groups(es)
        $newGoodsGroups = $this->input->post('mc_goodsGroups');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $merchant_data = $this->input->post();

                $merchantGoodsGroups = $this->Merchant_model->retrieveMerchantGoodsGroups($client_id, $site_id,
                    $merchant_id);

                $data['_id'] = $merchant_id;
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;

                if ($newBranches != false && !empty($newBranches)) {

                    $postArr = array_map('array_filter', $merchant_data['newBranches']);
                    foreach ($postArr as $key => $branch) {
                        if (!array_key_exists('branchName', $branch)) {
                            unset($postArr[$key]);
                        }
                    }
                    $merchant_data['newBranches'] = array_values($postArr);

                    $batch_data = array();
                    foreach ($merchant_data['newBranches'] as $branch) {
                        array_push($batch_data, array(
                            'client_id' => $client_id,
                            'site_id' => $site_id,
                            'branch_name' => $branch['branchName'],
                            'pin_code' => $this->Merchant_model->generatePINCode($client_id, $site_id),
                            'status' => !empty($branch['status']) ? true : false,
                            'deleted' => false,
                            'date_added' => new MongoDate(),
                            'date_modified' => new MongoDate()
                        ));
                    }
                }

                $data['branches'] = array();

                if (!empty($batch_data)) {
                    $completed_flag = $this->Merchant_model->bulkInsertBranches($batch_data);

                    if ($completed_flag) {
                        foreach ($batch_data as $entry) {
                            array_push($data['branches'],
                                array('b_id' => $entry['_id'], 'b_name' => $entry['branch_name']));
                        }
                    }
                }

                $data['name'] = $merchant_data['merchant-name'];
                $data['desc'] = $merchant_data['merchant-desc'];
                $data['status'] = !empty($merchant_data['merchant-status']) ? true : false;

                if (!empty($merchant_data['mc_goodsGroups'])) {
                    $merchantGoodsGroupsId_processed = array();
                    foreach ($merchant_data['mc_goodsGroups'] as $ggkey => $ggvalue) {
                        $gg_data['client_id'] = $client_id;
                        $gg_data['site_id'] = $site_id;
                        $gg_data['merchant_id'] = $merchant_id;
                        $tmp_gg = preg_split("/(mc_gg_)/", $ggvalue['goodsGroup']);
                        $gg_data['goods_group'] = $tmp_gg[1];

                        $gg_data['newAllowBranches'] = array();
                        foreach ($ggvalue['allowBranches'] as &$allowBranch) {
                            $tmp_array = explode(':', $allowBranch);
                            $tmp_array['b_id'] = new MongoId($tmp_array[0]);
                            $tmp_array['b_name'] = $tmp_array[1];
                            unset($tmp_array[0]);
                            unset($tmp_array[1]);
                            array_push($gg_data['newAllowBranches'], $tmp_array);
                        }
                        $gg_data['branches_allow'] = $gg_data['newAllowBranches'];

                        $gg_data['status'] = !empty($ggvalue['status']) ? true : false;

                        // check if this id is exist in $merchantGoodsGroups then update
                        $isUpdated = false;
                        foreach ($merchantGoodsGroups as $merchantGoodsGroup) {
                            if ($ggkey === $merchantGoodsGroup['_id'] . "") {
                                $gg_data['_id'] = $ggkey;
                                $ggupdate = $this->Merchant_model->updateMerchantGoodsGroup($gg_data);
                                $isUpdated = true;
                                array_push($merchantGoodsGroupsId_processed, $ggkey . "");
                            }
                        }
                        // create if not existed
                        if (!$isUpdated) {
                            $ggcreate = $this->Merchant_model->createMerchantGoodsGroup($gg_data);
                            array_push($merchantGoodsGroupsId_processed, $ggcreate . "");
                        }
                    }

                    $merchantGoodsGroups = $this->Merchant_model->retrieveMerchantGoodsGroups($client_id, $site_id,
                        $merchant_id);

                    // check if there is any goodsgroupId get deleting

                    $merchantGoodsGroupsId = array();
                    foreach ($merchantGoodsGroups as $merchantGoodsGroup) {
                        array_push($merchantGoodsGroupsId, $merchantGoodsGroup['_id'] . "");
                    }

                    $deletedMerchantGoodsGroups = array_diff($merchantGoodsGroupsId, $merchantGoodsGroupsId_processed);
                    if (!empty($deletedMerchantGoodsGroups)) {
                        foreach ($deletedMerchantGoodsGroups as $deletedMerchantGoodsGroup) {
                            $ggdelete = $this->Merchant_model->deleteMerchantGoodsGroupById($deletedMerchantGoodsGroup);
                        }
                    }
                } else {
                    $ggdelete = $this->Merchant_model->deleteMerchantGoodsGroupByMerchantId($client_id, $site_id,
                        $merchant_id);
                }

                $update = $this->Merchant_model->updateMerchant($data);
                if ($update) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/merchant', 'refresh');
                }
            }
        }

        $this->getForm($merchant_id);
    }

    public function listBranch($merchant_id = null)
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if (!empty($merchant_id)) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                if ($this->User_model->getClientId()) {

                    $merchant_info = $this->Merchant_model->retrieveMerchant($merchant_id);

                    if (!empty($merchant_info['branches'])) {
                        $tmpArrBranchID = array();
                        foreach ($merchant_info['branches'] as $branch) {
                            array_push($tmpArrBranchID, $branch['b_id']);
                        }

                        $branches = $this->Merchant_model->retrieveBranches($client_id, $site_id, $tmpArrBranchID);

                        foreach ($branches as &$branch) {
                            $branch['_id'] = $branch['_id'] . "";
                        }

                        echo json_encode($branches);
                    }
                }
            }
        } else {
            $this->output->set_status_header('401');
            echo json_encode(array('status' => 'error'));
        }
    }

    public function updateBranch()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $update_data = $this->input->post();

            $result = null;
            if (!empty($update_data)) {
                if ($update_data['name'] == 'branch_name') {
                    $result = $this->Merchant_model->updateBranchById($update_data['pk'], $update_data['value']);
                } elseif ($update_data['name'] == 'status') {
                    $result = $this->Merchant_model->updateBranchById($update_data['pk'], null, $update_data['value']);
                }
            }

            if (!$result) {
                $this->output->set_status_header('401');
                echo json_encode(array('status' => 'error'));
            } else {
                echo json_encode(array('status' => 'success'));
            }
        }
    }

    public function removeBranch()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $remove_id = $this->input->post('id');

            $result = null;
            if (!empty($remove_id)) {
                if (is_array($remove_id)) {
                    foreach ($remove_id as &$id_entry) {
                        $id_entry = new MongoId($id_entry);
                    }
                    $result = $this->Merchant_model->removeBranchesByIdArray($remove_id);
                } elseif (is_string($remove_id)) {
                    $result = $this->Merchant_model->removeBranchById($remove_id);
                }
            }

            if (!$result) {
                $this->output->set_status_header('401');
                echo json_encode(array('status' => 'error'));
            } else {
                echo json_encode(array('status' => 'success'));
            }
        }
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function getList($offset)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'sort_order'
        );

        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('merchant/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $merchants = $this->Merchant_model->retrieveMerchants($filter);

            $this->data['merchants'] = $merchants;
            $config['total_rows'] = $this->Merchant_model->countMerchants($client_id, $site_id);
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

        $this->data['main'] = 'merchant';
        $this->render_page('template');
    }

    public function getForm($merchant_id = null)
    {
        $this->data['main'] = 'merchant_form';

        if (isset($merchant_id) && ($merchant_id != 0)) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($this->User_model->getClientId()) {

                $merchant_info = $this->Merchant_model->retrieveMerchant($merchant_id);

                if (!empty($merchant_info['branches'])) {
                    $tmpArrBranchID = array();
                    foreach ($merchant_info['branches'] as $branch) {
                        array_push($tmpArrBranchID, $branch['b_id']);
                    }

                    $branches = $this->Merchant_model->retrieveBranches($client_id, $site_id, $tmpArrBranchID);
                }
            }

            $this->data['goodsgroups'] = $this->Goods_model->getGroupsAggregate($site_id);

            $this->data['merchantGoodsGroupsJSON'] = $this->Merchant_model->retrieveMerchantGoodsGroupsJSON($client_id,
                $site_id,
                $merchant_id);
        }

        if ($this->input->post('merchant-name')) {
            $this->data['merchant_name'] = $this->input->post('merchant-name');
        } elseif (isset($merchant_info['name'])) {
            $this->data['merchant_name'] = $merchant_info['name'];
        } else {
            $this->data['merchant_name'] = '';
        }

        if ($this->input->post('merchant-desc')) {
            $this->data['merchant_desc'] = $this->input->post('merchant-desc');
        } elseif (isset($merchant_info['desc'])) {
            $this->data['merchant_desc'] = $merchant_info['desc'];
        } else {
            $this->data['merchant_desc'] = '';
        }

        if ($this->input->post('merchant-status')) {
            $this->data['merchant_status'] = $this->input->post('merchant-status');
        } elseif (isset($merchant_info['status'])) {
            $this->data['merchant_status'] = $merchant_info['status'];
        } else {
            $this->data['merchant-status'] = true;
        }

        if (isset($branches)) {
            $this->data['branches_list'] = $branches;
        } else {
            $this->data['branches_list'] = '';
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'merchant')) {
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
                'merchant') && $this->Feature_model->getFeatureExistByClientId($client_id, 'merchant')
        ) {
            return true;
        } else {
            return false;
        }
    }

    function alpha_dash_space($str)
    {
        if (!preg_match("/([ _-\d\w])+/i", $str)) {
            $this->form_validation->set_message('alpha_dash_space',
                'The %s field may only contain alpha-numeric characters, spaces, underscores, and dashes');
            return false;
        } else {
            return true;
        }
    }

}