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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $merchant_data = $this->input->post();

                $postArr = array_map('array_filter', $merchant_data['branches']);
                foreach ($postArr as $key => $branch) {
                    if (!array_key_exists('branchName', $branch)) {
                        unset($postArr[$key]);
                    }
                }
                $merchant_data['branches'] = array_values($postArr);

                $batch_data = array();
                foreach ($merchant_data['branches'] as $branch) {
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

                $data['branches'] = array();

                if (!empty($batch_data)) {
                    $completed_flag = $this->Merchant_model->bulkInsertBranches($batch_data);

                    if ($completed_flag) {
                        foreach ($batch_data as $entry) {
                            array_push($data['branches'], array('b_id' => $entry['_id'], 'b_name' => $entry['branch_name']));
                        }
                    }
                }

                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $data['name'] = $merchant_data['merchant-name'];
                $data['desc'] = $merchant_data['merchant-desc'];
                $data['status'] = !empty($merchant_data['merchant-status']) ? true : false;

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

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->form_validation->set_rules('merchant-name', $this->lang->line('entry_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('merchant-desc', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('merchant-status', $this->lang->line('entry_status'), 'trim|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $merchant_data = $this->input->post();

                $data['_id'] = $merchant_id;
                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();

//                $postArr = array_map('array_filter', $merchant_data['branches']);
//                foreach ($postArr as $key => $branch) {
//                    if (!array_key_exists('branchName', $branch)) {
//                        unset($postArr[$key]);
//                    }
//                }
//                $merchant_data['branches'] = array_values($postArr);
//
//                $batch_data = array();
//                foreach ($merchant_data['branches'] as $branch) {
//                    array_push($batch_data, array(
//                        'client_id' => $client_id,
//                        'site_id' => $site_id,
//                        'branch_name' => $branch['branchName'],
//                        'pin_code' => $this->Merchant_model->generatePINCode($client_id, $site_id),
//                        'status' => !empty($branch['status']) ? true : false,
//                        'deleted' => false,
//                        'date_added' => new MongoDate(),
//                        'date_modified' => new MongoDate()
//                    ));
//                }
//
//                $data['branches'] = array();
//
//                if (!empty($batch_data)) {
//                    $completed_flag = $this->Merchant_model->bulkInsertBranches($batch_data);
//
//                    if ($completed_flag) {
//                        foreach ($batch_data as $entry) {
//                            array_push($data['branches'], array($entry['_id'], $entry['branch_name']));
//                        }
//                    }
//                }

                $data['name'] = $merchant_data['merchant-name'];
                $data['desc'] = $merchant_data['merchant-desc'];
                $data['status'] = !empty($merchant_data['merchant-status']) ? true : false;

                $update = $this->Merchant_model->updateMerchant($data);
                if ($update) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/merchant', 'refresh');
                }
            }
        }

        $this->getForm($merchant_id);
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
            if ($this->User_model->getClientId()) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $merchant_info = $this->Merchant_model->retrieveMerchant($merchant_id);

                if (!empty($merchant_info['branches'])) {
                    $tmpArrBranchID = array();
                    foreach ($merchant_info['branches'] as $branch) {
                        array_push($tmpArrBranchID, $branch['b_id']);
                    }

                    $branches = $this->Merchant_model->retrieveBranches($client_id, $site_id, $tmpArrBranchID);
                }

                $goodsgroup = $this->Goods_model->getGroupsAggregate($site_id);
            }
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

}