<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Goods extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_model');
        $this->load->model('User_model');
        $this->load->model('Plan_model');
        $this->load->model('Permission_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Goods_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("goods", $lang['folder']);

        $this->load->model('Store_org_model');
        $this->load->model('Feature_model');
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

        $this->getList(0);

    }

    public function markAsUsed()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->getListAsUsed(0);
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
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);
    }

    public function import()
    {

        // Get Usage
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $usage = $this->Goods_model->getTotalGoodsBySiteId(
            array('site_id' => $site_id));
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);

        // Get Limit
        $limit = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'goods');

        if ($limit && $usage >= $limit) {
            $this->data['message'] = $this->lang->line('error_limit');
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'goods/import';
        if(isset($_SERVER['HTTP_REFERER'])){
            $referred_page = strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME']) ?
                explode($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/', $_SERVER['HTTP_REFERER']) :
                explode($_SERVER['HTTP_HOST'].'/', $_SERVER['HTTP_REFERER']);
        }
        $this->data['refer_page'] = isset($referred_page[1]) && strpos( $referred_page[1], 'page') ?  $referred_page[1] : 'goods';

        $this->form_validation->set_rules('name', $this->lang->line('entry_group'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('reward_point', $this->lang->line('entry_point'),
            'is_numeric|trim|xss_clean|greater_than[-1]|less_than[2147483647]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($limit && $usage >= $limit) {
                $this->data['message'] = $this->lang->line('error_limit');
            }

            if (empty($_FILES) || !isset($_FILES['file']['tmp_name'])) {
                $this->data['message'] = $this->lang->line('error_file');
            }

            if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != '') {

                $maxsize = 4194304;
                $csv_mimetypes = array(
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'text/comma-separated-values',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'text/anytext',
                    'application/octet-stream',
                    'application/txt',
                );

                if (($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
                    $this->data['message'] = $this->lang->line('error_file_too_large');
                }

                if (!in_array($_FILES['file']['type'], $csv_mimetypes) && (!empty($_FILES["file"]["type"]))) {
                    $this->data['message'] = $this->lang->line('error_type_accepted');
                }

                $handle = fopen($_FILES['file']['tmp_name'], "r");
                if (!$handle) {
                    $this->data['message'] = $this->lang->line('error_upload');
                }
            }

            $point_empty = true;
            $badge_empty = true;
            $custom_empty = true;
            $redeem = array();

            if ($this->input->post('reward_point') != '' || (int)$this->input->post('reward_point') != 0) {
                $point_empty = false;
                $redeem['point'] = array('point_value' => (int)$this->input->post('reward_point'));
            }

            if ($this->input->post('reward_badge')) {
                foreach ($this->input->post('reward_badge') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $badge_empty = false;
                        $redeem['badge'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($this->input->post('reward_reward')) {
                foreach ($this->input->post('reward_reward') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $custom_empty = false;
                        $redeem['custom'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($point_empty && $badge_empty && $custom_empty) {
                $this->data['message'] = $this->lang->line('error_redeem');
            }

            if ($client_id && $site_id) {
                if ($this->input->post('name') && $this->Goods_model->checkExists($site_id, $this->input->post('name')))
                {
                    $this->data['message'] = $this->lang->line('error_goods_exists');
                }
            }

            $whitelist_data = array();
            $whitelist_enable = $this->input->post('whitelist_enable') ? true : false;
            if ($this->data['message'] == null && $whitelist_enable) {
                if (empty($_FILES) || !isset($_FILES['whitelist_file']['tmp_name']) || $_FILES['whitelist_file']['tmp_name'] == '') {
                    $this->data['message'] = $this->lang->line('error_file_whitelist');
                }

                if ( $this->data['message'] == null ) {

                    $maxsize = 4194304;
                    $csv_mimetypes = array(
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt',
                    );

                    if (($_FILES['whitelist_file']['size'] >= $maxsize) || ($_FILES["whitelist_file"]["size"] == 0)) {
                        $this->data['message'] = $this->lang->line('error_file_too_large_whitelist');
                    }

                    if (!in_array($_FILES['whitelist_file']['type'], $csv_mimetypes) && (!empty($_FILES["whitelist_file"]["type"]))) {
                        $this->data['message'] = $this->lang->line('error_type_accepted_whitelist');
                    }

                    $handle_whitelist = fopen($_FILES['whitelist_file']['tmp_name'], "r");
                    if (!$handle_whitelist) {
                        $this->data['message'] = $this->lang->line('error_upload_whitelist');
                    }

                    if ( $this->data['message'] == null){
                        // prepare data of user white list
                        $this->generateWhiteListData($handle_whitelist,$whitelist_data);
                        $whitelist_data['file_name'] = $_FILES['whitelist_file']['name'];

                    }

                }
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $data = array_merge($this->input->post(), array('quantity' => 1));
                $data['per_user_include_inactive'] = $this->input->post('per_user_include_inactive') ? true : false;

                if (isset($data['custom_param'])){
                    if(is_array($data['custom_param'])){
                        $custom_param = array();
                        foreach ($data['custom_param'] as $param){
                            if(!is_null($param['key']) && !is_null($param['value'])){
                                array_push($custom_param, $param);
                                if(is_numeric($param['value'])){
                                    array_push($custom_param, array('key' => $param['key'].'_numeric', 'value' => floatval($param['value'])));
                                }
                            }
                        }
                        $data['custom_param'] = $custom_param;
                    } else {
                        $data['custom_param'] = array();
                    }
                }

                if ($client_id) {

                    try {
                        $distinct_id = $this->Goods_model->addGoodsDistinct(array_merge($data, array('client_id' => $client_id, 'site_id' => $site_id,
                                                                                        'redeem' => $redeem, 'whitelist_enable' => $whitelist_enable)), true);
                        $data['distinct_id'] = $distinct_id;
                        $data = $this->addGoods($handle, $data, $redeem, array($this->User_model->getClientId()), array($this->User_model->getSiteId()));
                        //insert whitelist
                        if($whitelist_enable) {
                            $whitelist_data['cl_player_id_list'] = $this->generateWhiteListBatch($whitelist_data['cl_player_id_list'], $client_id, $site_id, $distinct_id);
                            $this->Goods_model->setGoodsWhiteList($client_id, $site_id, $distinct_id, $whitelist_data);
                        }
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        fclose($handle);
                        redirect('/goods/update/'.$data['goods_id'], 'refresh');
                    } catch (Exception $e) {
                        $this->data['message'] = $e->getMessage();
                    }
                } else {

                    $this->load->model('Client_model');
                    $goods_data = $this->input->post();

                    if (isset($goods_data['admin_client_id']) && $goods_data['admin_client_id'] != 'all_clients') {

                        $clients_sites = $this->Client_model->getSitesByClientId($goods_data['admin_client_id']);
                        $list_site_id = array();
                        foreach ($clients_sites as $client) {
                            array_push($list_site_id, $client['_id']);
                        }

                        try {
                            //$this->Goods_model->addGoodsDistinct(array_merge($data, array('client_id'=> $client_id, 'site_id' => $site_id, 'redeem' => $redeem)), true);
                            $data = $this->addGoods($handle, $data, $redeem, array(new MongoId($goods_data['admin_client_id'])), $list_site_id);
                            fclose($handle);
                            redirect('/goods/update/'.$data['goods_id'], 'refresh');
                        } catch (Exception $e) {
                            $this->data['message'] = $e->getMessage();
                        }
                    } else {
                        $all_sites_clients = $this->Client_model->getAllSitesFromAllClients();
                        $hash_client_id = array();
                        $list_site_id = array();
                        foreach ($all_sites_clients as $site) {
                            $hash_client_id[$site['client_id']] = true;
                            array_push($list_site_id, $site['_id']);
                        }

                        try {
                            //$this->Goods_model->addGoodsDistinct(array_merge($data, array('client_id'=> $client_id, 'site_id' => $site_id, 'redeem' => $redeem)), true);
                            $data = $this->addGoods($handle, $data, $redeem, array_keys($hash_client_id), array($list_site_id));
                            fclose($handle);
                            redirect('/goods/update/'.$data['goods_id'], 'refresh');
                        } catch (Exception $e) {
                            $this->data['message'] = $e->getMessage();
                        }
                    }
                }
            }
        }
        $this->getForm(null, true);
    }

    public function insert()
    {
        // Get Usage
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $usage = $this->Goods_model->getTotalGoodsBySiteId(
            array('site_id' => $site_id));
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);

        // Get Limit
        $limit = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'goods');

        if ($limit && $usage >= $limit) {
            $this->data['message'] = $this->lang->line('error_limit');
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'goods/insert';
        if(isset($_SERVER['HTTP_REFERER'])){
            $referred_page = strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME']) ?
                explode($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/', $_SERVER['HTTP_REFERER']) :
                explode($_SERVER['HTTP_HOST'].'/', $_SERVER['HTTP_REFERER']);
        }
        $this->data['refer_page'] = isset($referred_page[1]) && strpos( $referred_page[1], 'page') ?  $referred_page[1] : 'goods';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('reward_point', $this->lang->line('entry_point'),
            'is_numeric|trim|xss_clean|greater_than[-1]|less_than[2147483647]');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($limit && $usage >= $limit) {
                $this->data['message'] = $this->lang->line('error_limit');
            }

            $point_empty = true;
            $badge_empty = true;
            $custom_empty = true;
            $redeem = array();

            if ($this->input->post('reward_point') != '' || (int)$this->input->post('reward_point') != 0) {
                $point_empty = false;
                $redeem['point'] = array('point_value' => (int)$this->input->post('reward_point'));
            }

            if ($this->input->post('reward_badge')) {
                foreach ($this->input->post('reward_badge') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $badge_empty = false;
                        $redeem['badge'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($this->input->post('reward_reward')) {
                foreach ($this->input->post('reward_reward') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $custom_empty = false;
                        $redeem['custom'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($point_empty && $badge_empty && $custom_empty) {
                $this->data['message'] = $this->lang->line('error_redeem');
            }

            if ($this->User_model->getClientId() && $this->User_model->getSiteId()) {
                if ($this->input->post('name') && $this->Goods_model->checkExists($this->User_model->getSiteId(), $this->input->post('name')))
                {
                    $this->data['message'] = $this->lang->line('error_goods_exists');
                }
            }

            $whitelist_data = array();
            $whitelist_enable = $this->input->post('whitelist_enable') ? true : false;
            if ($this->data['message'] == null && $whitelist_enable) {
                if (empty($_FILES) || !isset($_FILES['whitelist_file']['tmp_name']) || $_FILES['whitelist_file']['tmp_name'] == '') {
                    $this->data['message'] = $this->lang->line('error_file_whitelist');
                }

                if ( $this->data['message'] == null ) {

                    $maxsize = 4194304;
                    $csv_mimetypes = array(
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt',
                    );

                    if (($_FILES['whitelist_file']['size'] >= $maxsize) || ($_FILES["whitelist_file"]["size"] == 0)) {
                        $this->data['message'] = $this->lang->line('error_file_too_large_whitelist');
                    }

                    if (!in_array($_FILES['whitelist_file']['type'], $csv_mimetypes) && (!empty($_FILES["whitelist_file"]["type"]))) {
                        $this->data['message'] = $this->lang->line('error_type_accepted_whitelist');
                    }

                    $handle = fopen($_FILES['whitelist_file']['tmp_name'], "r");
                    if (!$handle) {
                        $this->data['message'] = $this->lang->line('error_upload_whitelist');
                    }

                    if ( $this->data['message'] == null){
                        // prepare data of user white list
                        $this->generateWhiteListData($handle,$whitelist_data);
                        $whitelist_data['file_name'] = $_FILES['whitelist_file']['name'];
                    }

                }
            }

            $goods_data = $this->input->post();
            $goods_data['redeem'] = $redeem;
            $goods_data['whitelist_enable'] = $whitelist_enable;
            $goods_data['per_user_include_inactive'] = $this->input->post('per_user_include_inactive') ? true : false;

            if ($this->form_validation->run() && $this->data['message'] == null) {

                if (isset($goods_data['custom_param'])){
                    if(is_array($goods_data['custom_param'])){
                        $custom_param = array();
                        foreach ($goods_data['custom_param'] as $param){
                            if(!is_null($param['key']) && !is_null($param['value'])){
                                array_push($custom_param, $param);
                                if(is_numeric($param['value'])){
                                    array_push($custom_param, array('key' => $param['key'].'_numeric', 'value' => floatval($param['value'])));
                                }
                            }
                        }
                        $goods_data['custom_param'] = $custom_param;
                    } else {
                        $goods_data['custom_param'] = array();
                    }
                }

                if ($client_id) {
                    $goods_id = $this->Goods_model->addGoods($goods_data);

                    $goods_data['goods_id'] = $goods_id;
                    $goods_data['client_id'] = $client_id;
                    $goods_data['site_id'] = $site_id;

                    $distinct_id = $this->Goods_model->addGoodsDistinct($goods_data, false);
                    $goods_data['distinct_id'] = $distinct_id;
                    $goods_client_id = $this->Goods_model->addGoodsToClient($goods_data);
                    $this->Goods_model->auditAfterGoods('insert', $goods_client_id, $this->User_model->getId());

                    //insert whitelist
                    if(isset($goods_data['whitelist_enable']) && $goods_data['whitelist_enable'] == true) {
                        $whitelist_data['cl_player_id_list'] = $this->generateWhiteListBatch($whitelist_data['cl_player_id_list'], $client_id, $site_id, $distinct_id);
                        $this->Goods_model->setGoodsWhiteList($client_id, $site_id, $distinct_id, $whitelist_data);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    $this->session->set_flashdata('refer_page', $goods_data['refer_page']);
                    redirect('/goods/update/'.$goods_client_id, 'refresh');
                } else {

                    $this->load->model('Client_model');

                    if (isset($goods_data['sponsor'])) {
                        $goods_data['sponsor'] = true;
                    } else {
                        $goods_data['sponsor'] = false;
                    }

                    if (isset($goods_data['admin_client_id']) && $goods_data['admin_client_id'] != 'all_clients') {

                        $clients_sites = $this->Client_model->getSitesByClientId($goods_data['admin_client_id']);

                        $goods_data['goods_id'] = $this->Goods_model->addGoods($goods_data);
                        $goods_data['client_id'] = $goods_data['admin_client_id'];

                        foreach ($clients_sites as $client) {
                            $goods_data['site_id'] = $client['_id'];
                            $goods_client_id = $this->Goods_model->addGoodsToClient($goods_data);
                            $this->Goods_model->auditAfterGoods('insert', $goods_client_id, $this->User_model->getId());
                        }

                    } else {
                        $goods_data['goods_id'] = $this->Goods_model->addGoods($goods_data);
                        $all_sites_clients = $this->Client_model->getAllSitesFromAllClients();
                        foreach ($all_sites_clients as $site) {
                            $goods_data['site_id'] = $site['_id'];
                            $goods_data['client_id'] = $site['client_id'];
                            $goods_client_id = $this->Goods_model->addGoodsToClient($goods_data);
                            $this->Goods_model->auditAfterGoods('insert', $goods_client_id, $this->User_model->getId());
                        }

                    }
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    $this->session->set_flashdata('refer_page', $goods_data['refer_page']);
                    redirect('/goods/update/'.$goods_data['goods_id'], 'refresh');
                }
            }
        }
        $this->getForm();
    }

    public function update($goods_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'goods/update/' . $goods_id;
        if(isset($_SERVER['HTTP_REFERER'])){
            $referred_page = strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME']) ?
                explode($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/', $_SERVER['HTTP_REFERER']) :
                explode($_SERVER['HTTP_HOST'].'/', $_SERVER['HTTP_REFERER']);
        }

        $this->data['refer_page'] = isset($referred_page[1]) && strpos( $referred_page[1], 'page') ?  $referred_page[1] : 'goods';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
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
        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('reward_point', $this->lang->line('entry_point'),
            'is_numeric|trim|xss_clean|greater_than[-1]|less_than[2147483647]|');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerGoods($goods_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $point_empty = true;
            $badge_empty = true;
            $custom_empty = true;
            $redeem = array();

            if ($this->input->post('reward_point') != '' || (int)$this->input->post('reward_point') != 0) {
                $point_empty = false;
                $redeem['point'] = array('point_value' => (int)$this->input->post('reward_point'));
            }

            if ($this->input->post('reward_badge')) {
                foreach ($this->input->post('reward_badge') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $badge_empty = false;
                        $redeem['badge'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($this->input->post('reward_reward')) {
                foreach ($this->input->post('reward_reward') as $rbk => $rb) {
                    if ($rb != '' || $rb != 0) {
                        $custom_empty = false;
                        $redeem['custom'][$rbk] = (int)$rb;
                    }
                }
            }

            if ($point_empty && $badge_empty && $custom_empty) {
                $this->data['message'] = $this->lang->line('error_redeem');
            }

            $whitelist_data = array();
            $whitelist_enable = $this->input->post('whitelist_enable') ? true : false;
            $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            $distinct_id = $goods_info['distinct_id'];

            if ($this->User_model->getClientId() && $this->User_model->getSiteId()) {
                if ($this->input->post('name') && $this->Goods_model->checkExists($this->User_model->getSiteId(), $this->input->post('name'),$distinct_id)
                ) {
                    $this->data['message'] = $this->lang->line('error_goods_exists');
                }
            }

            if ($this->data['message'] == null && $whitelist_enable) {

                $distinct_info = $this->Goods_model->getGoodsDistinctByID($site_id,$distinct_id);
                if ( (!isset($distinct_info['whitelist_enable']) || $distinct_info['whitelist_enable'] == false) &&
                     (empty($_FILES) || !isset($_FILES['whitelist_file']['tmp_name']) || $_FILES['whitelist_file']['tmp_name'] == '')) {
                    $this->data['message'] = $this->lang->line('error_file_whitelist');
                }

                if ( $this->data['message'] == null && (isset($_FILES['whitelist_file']['tmp_name']) && $_FILES['whitelist_file']['tmp_name'] != '') ) {

                    $maxsize = 4194304;
                    $csv_mimetypes = array(
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt',
                    );

                    if (($_FILES['whitelist_file']['size'] >= $maxsize) || ($_FILES["whitelist_file"]["size"] == 0)) {
                        $this->data['message'] = $this->lang->line('error_file_too_large_whitelist');
                    }

                    if (!in_array($_FILES['whitelist_file']['type'], $csv_mimetypes) && (!empty($_FILES["whitelist_file"]["type"]))) {
                        $this->data['message'] = $this->lang->line('error_type_accepted_whitelist');
                    }

                    $handle = fopen($_FILES['whitelist_file']['tmp_name'], "r");
                    if (!$handle) {
                        $this->data['message'] = $this->lang->line('error_upload_whitelist');
                    }

                    if ( $this->data['message'] == null){
                        // prepare data of user white list
                        $this->generateWhiteListData($handle,$whitelist_data);
                        $whitelist_data['file_name'] = $_FILES['whitelist_file']['name'];
                    }

                }
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                try {
                    $goods_data = $this->input->post();
                    if ($this->input->post('quantity') === false) {
                        $goods_data = array_merge($goods_data, array('quantity' => 1));
                    }
                    $goods_data['redeem'] = $redeem;
                    $goods_data['whitelist_enable'] = $whitelist_enable;
                    $goods_data['per_user_include_inactive'] = $this->input->post('per_user_include_inactive') ? true : false;

                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($client_id, 'store_org')
                    ) {
                        if ($this->input->post('global_goods')) {
                            $goods_data['organize_id'] = null;
                            $goods_data['organize_role'] = null;
                        }
                    }

                    if (isset($goods_data['custom_param'])){
                        if(is_array($goods_data['custom_param'])){
                            $custom_param = array();
                            foreach ($goods_data['custom_param'] as $param){
                                if(!is_null($param['key']) && !is_null($param['value'])){
                                    array_push($custom_param, $param);
                                    if(is_numeric($param['value'])){
                                        array_push($custom_param, array('key' => $param['key'].'_numeric', 'value' => floatval($param['value'])));
                                    }
                                }
                            }
                            $goods_data['custom_param'] = $custom_param;
                        } else {
                            $goods_data['custom_param'] = array();
                        }
                    }

                    if ($client_id) {
                        if (!$this->Goods_model->checkGoodsIsSponsor($goods_id)) {
                            $goods_data['client_id'] = $client_id;
                            $goods_data['site_id'] = $site_id;
                            $goods_data['goods_id'] = $goods_info['goods_id'];
                            $goods_data['distinct_id'] = $goods_info['distinct_id'];

                            $audit_id = $this->Goods_model->auditBeforeGoods('update', $goods_id, $this->User_model->getId());
                            $this->Goods_model->editGoodsDistinct($site_id,$goods_info['name'],$goods_data);
                            if ($goods_info && array_key_exists('group', $goods_info)) {
                                $this->Goods_model->editGoodsGroupToClient($goods_info['group'], $goods_data);
                                if ($goods_info['group'] != $goods_data['name']){
                                    $this->Goods_model->editGoodsGroupLog($goods_info['group'], $goods_data);
                                    $this->Goods_model->editGoodsGroupPLayer($goods_info['group'], $goods_data);
                                }
                            } else {
                                $this->Goods_model->editGoodsToClient($goods_id, $goods_data);
                            }
                            $this->Goods_model->auditAfterGoods('update', $goods_id, $this->User_model->getId(), $audit_id);

                            //update whitelist
                            if(isset($goods_data['whitelist_enable']) && ($goods_data['whitelist_enable'] == true) && (isset($_FILES['whitelist_file']['tmp_name']) && $_FILES['whitelist_file']['tmp_name'] != '')) {
                                $this->Goods_model->deleteGoodsWhiteList($client_id, $site_id, $distinct_id);
                                $whitelist_data['cl_player_id_list'] = $this->generateWhiteListBatch($whitelist_data['cl_player_id_list'], $client_id, $site_id, $distinct_id);
                                $this->Goods_model->setGoodsWhiteList($client_id, $site_id, $distinct_id, $whitelist_data);
                            }elseif(!isset($goods_data['whitelist_enable']) || ($goods_data['whitelist_enable'] == false)){
                                $this->Goods_model->deleteGoodsWhiteList($client_id, $site_id, $distinct_id);
                            }

                        } else {
                            redirect($_SERVER['HTTP_REFERER'], 'refresh');
                        }
                    } else {
                        $this->Goods_model->editGoods($goods_id, $goods_data);
                        $audit_id = $this->Goods_model->auditBeforeGoods('update', $goods_id, $this->User_model->getId());
                        $this->Goods_model->editGoodsToClientFromAdmin($goods_id, $goods_data);
                        $this->Goods_model->auditAfterGoods('update', $goods_id, $this->User_model->getId(), $audit_id);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    $this->session->set_flashdata('refer_page', $goods_data['refer_page']);
                    redirect('/goods/update/'.$goods_id, 'refresh');

                } catch (Exception $e) {
                    $this->data['message'] = $e->getMessage();
                }
            }
        }

        $this->getForm($goods_id);
    }

    public function updateGoodsFromAjax($goods_id)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($goods_id && $_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }
                $goods_info = $this->Goods_model->getGoodsOfClientPrivate($goods_id);
                $group = isset($goods_info['group']) ? $goods_info['group'] : null;
                $goods_data = $this->input->post();
                $filter_data = $this->input->get();
                $goods_data['client_id'] = $client_id;
                $goods_data['site_id'] = $site_id;
                if($group){
                    $goods_data['coupon_batch_name'] = isset($goods_data['coupon_batch_name']) && $goods_data['coupon_batch_name'] ? $goods_data['coupon_batch_name'] : 'default';
                    $audit_id = $this->Goods_model->auditBeforeCoupon('update_coupon', $goods_id, $this->User_model->getId());
                    $this->Goods_model->editGoodsGroupCoupon($group, $goods_id, $goods_data, $filter_data);
                    $this->Goods_model->auditAfterCoupon('update_coupon', $goods_id, $this->User_model->getId(), $audit_id);
                    $n = $this->Goods_model->checkBatchNameExistInDistinct($group,array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_data['coupon_batch_name']));
                    if(!$n) {
                        $this->Goods_model->addBatchNameInDistinct($group, array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_data['coupon_batch_name']));
                    }
                    $d = $this->Goods_model->checkBatchNameExistInClient($group,array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_info['batch_name']));
                    if(!$d) {
                        $this->Goods_model->removeBatchNameInDistinct($group, array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_info['batch_name']));
                    }
                    echo json_encode(array('status' => 'success'));
                } else {
                    echo json_encode(array("status" => 'error'));
                }
            }
        }
    }

    public function deleteGoodsFromAjax($goods_id)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($goods_id && $_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }
                $goods_info = $this->Goods_model->getGoodsOfClientPrivate($goods_id);
                $group = isset($goods_info['group']) ? $goods_info['group'] : null;
                $filter_data = $this->input->get();
                $goods_data['client_id'] = $client_id;
                $goods_data['site_id'] = $site_id;
                if($group){
                    $audit_id = $this->Goods_model->auditBeforeCoupon('delete_coupon', $goods_id, $this->User_model->getId());
                    $this->Goods_model->deleteGoodsGroupCoupon($group, $goods_id, $goods_data,$filter_data);
                    $this->Goods_model->auditAfterCoupon('delete_coupon', $goods_id, $this->User_model->getId(), $audit_id);
                    $d = $this->Goods_model->checkBatchNameExistInClient($group,array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_info['batch_name']));
                    if(!$d) {
                        $this->Goods_model->removeBatchNameInDistinct($group, array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $goods_info['batch_name']));
                    }
                    $n = $this->Goods_model->checkGoodsGroupQuantity($site_id, $group);
                    if(!$n){
                        $this->Goods_model->deleteGoodsDistinct($site_id, $group);
                        echo json_encode(array('status' => 'deleted'));
                    } else {
                        echo json_encode(array('status' => 'success'));
                    }
                } else {
                    echo json_encode(array("status" => 'error'));
                }
            }
        }
    }

    public function upload($goods_id)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($goods_id && $_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }
  
                if (!empty($_FILES) && isset($_FILES['file']['tmp_name']) && !empty($_FILES['file']['tmp_name'])) {
                    $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
                    $goods_data = array(
                        'name' => $goods_info['group'],
                        'quantity' => 1,
                        'description' => $goods_info['description'],
                        'image' => $goods_info['image'],
                        'status' => $goods_info['status'],
                        'per_user' => $goods_info['per_user'],
                        'custom_param' => $goods_info['custom_param'],
                        'tags' => $goods_info['tags'],
                        'days_expire' => $goods_info['days_expire'],
                        'distinct_id' => $goods_info['distinct_id'],
                        'per_user_include_inactive' => $goods_info['per_user_include_inactive']
                    );
                    if(isset($goods_info['organize_id']) && isset($goods_info['organize_role'])){
                        $goods_data['organize_id'] = $goods_info['organize_id'];
                        $goods_data['organize_role'] = $goods_info['organize_role'];
                    }
                    $handle = fopen($_FILES['file']['tmp_name'], "r");
                    $audit_id = $this->Goods_model->auditBeforeGoods('upload', $goods_id, $this->User_model->getId());
                    $this->addGoods($handle, $goods_data, $goods_info['redeem'], array($client_id), array($site_id));
                    $this->Goods_model->auditAfterGoods('upload', $goods_id, $this->User_model->getId(), $audit_id);
                    fclose($handle);
                    echo json_encode(array('status' => 'success'));
                }
            }
        }
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $goods_id) {
                if ($this->checkOwnerGoods($goods_id)) {

                    if ($this->User_model->getClientId()) {
                        if (!$this->Goods_model->checkGoodsIsSponsor($goods_id)) {
                            $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
                            if ($goods_info && array_key_exists('group', $goods_info)) {
                                $this->Goods_model->deleteGoodsDistinct($this->User_model->getSiteId(), $goods_info['group']);
                                $audit_id = $this->Goods_model->auditBeforeGoods('delete', $goods_id, $this->User_model->getId());
                                $this->Goods_model->deleteGoodsGroupClient($goods_info['group'], $this->User_model->getClientId(), $this->User_model->getSiteId());
                                $this->Goods_model->auditAfterGoods('delete', $goods_id, $this->User_model->getId(), $audit_id);
                            } else {
                                $this->Goods_model->deleteGoodsDistinct($this->User_model->getSiteId(), $goods_info['name']);
                                $audit_id = $this->Goods_model->auditBeforeGoods('delete', $goods_id, $this->User_model->getId());
                                $this->Goods_model->deleteGoodsClient($goods_id);
                                $this->Goods_model->auditAfterGoods('delete', $goods_id, $this->User_model->getId(), $audit_id);
                            }
                        } else {
                            redirect('/goods', 'refresh');
                        }
                    } else {
                        $this->Goods_model->deleteGoods($goods_id);
                        $audit_id = $this->Goods_model->auditBeforeGoods('delete', $goods_id, $this->User_model->getId());
                        $this->Goods_model->deleteGoodsClientFromAdmin($goods_id);
                        $this->Goods_model->auditAfterGoods('delete', $goods_id, $this->User_model->getId(), $audit_id);
                    }

                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

        $this->getList(0);
    }

    public function pageAsUsed($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getListAsUsed($offset);
    }

    public function getListAsUsed($offset, $per_page = NUMBER_OF_RECORDS_PER_PAGE){
        $this->load->library('pagination');

        $config['base_url'] = site_url('goods/pageAsUsed');

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        } else {
            $this->data['org_status'] = false;
        }
        //todo: Node type should only shown when match with goods's organize.
        $redeemed_goods_count = $this->Goods_model->countRedeemedGoodsBySite($this->User_model->getSiteId(), array('goods_id', 'cl_player_id', 'pb_player_id'));
        $redeemed_goods_list = $this->Goods_model->listRedeemedGoodsBySite($this->User_model->getSiteId(), array('goods_id', 'cl_player_id', 'pb_player_id'),array('start' => $offset , 'limit' => $per_page));

        $this->load->model('Player_model');
        $_pb_player_id_list = array_values(array_unique(array_map(array($this, 'extract_pb_player_id'), $redeemed_goods_list)));
        $players_detail_list = $this->Player_model->listPlayers($_pb_player_id_list, array('first_name', 'last_name', 'cl_player_id'));

        $players_with_node_detail_list = $this->Player_model->listPlayersOrganize($_pb_player_id_list, array('node_id', 'pb_player_id'));
        $_node_list = array_values(array_unique(array_map(array($this, 'extract_node_id'), $players_with_node_detail_list)));
        $node_detail_list = $this->Store_org_model->listNodes($_node_list, array('name', 'description', 'organize'));

        $_organization_list = array_values(array_unique(array_map(array($this, 'extract_organize_id'), $node_detail_list)));
        $organization_detail_list = $this->Store_org_model->listOrganizations($_organization_list, array('name', 'description'));
        $goods_list_data = array();
        foreach ($redeemed_goods_list as $redeemed_goods) {
            if (isset($redeemed_goods['goods_id'])) {
                array_push($goods_list_data, new MongoId($redeemed_goods['goods_id']));
            }
        }
        $goods = $this->Goods_model->getGoodslistOfClientPrivate($goods_list_data);

        foreach ($redeemed_goods_list as &$redeemed_goods) {
            if (isset($redeemed_goods['pb_player_id'])) {
                // set player info
                $player_index = $this->searchForId(new MongoId($redeemed_goods['pb_player_id']), $players_detail_list);
                if (isset($player_index)) {
                    $redeemed_goods['player_info'] = $players_detail_list[$player_index];
                }

                // set player node info
                $player_node_info_index_array = $this->searchForPBPlayerId(new MongoId($redeemed_goods['pb_player_id']),
                    $players_with_node_detail_list);
                if (isset($player_node_info_index_array)) {
                    $node_info_array = array();
                    $organize_info_array = array();
                    foreach ($player_node_info_index_array as $player_node_info_index) {
                        $node_info_index = $this->searchForId(new MongoId($players_with_node_detail_list[$player_node_info_index]['node_id']),
                            $node_detail_list);
                        if (isset($node_info_index)) {
                            array_push($node_info_array, $node_detail_list[$node_info_index]);

                            $organize_info_index = $this->searchForId(new MongoId($node_detail_list[$node_info_index]['organize']),
                                $organization_detail_list);
                            if (isset($organize_info_index)) {
                                array_push($organize_info_array, $organization_detail_list[$organize_info_index]);
                            }
                        }
                    }

                    $redeemed_goods['player_node_info'] = $node_info_array;
                    $redeemed_goods['player_organize_info'] = $organize_info_array;
                }
            }

            if (isset($redeemed_goods['goods_id'])) {
                $goods_id = array_search($redeemed_goods['goods_id'], array_column($goods, 'goods_id'));
                if ($goods_id !== false){
                    $redeemed_goods['name'] = isset($goods[$goods_id]['group']) ? $goods[$goods_id]['group'] : $goods[$goods_id]['name'];
                    $redeemed_goods['code'] = isset($goods[$goods_id]['code']) ? $goods[$goods_id]['code'] : null;
                }
            }
        }

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

        $config['total_rows'] = $redeemed_goods_count;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

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

        $this->data['main'] = 'goods';
        $this->data['tabs'] = $this->lang->line('heading_title_mark_as_used');
        $this->data['redeemed_goods_list'] = $redeemed_goods_list;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getListForAjax($offset)
    {
        $this->_getList($offset);
        $this->load->vars($this->data);
        $this->render_page('goods_ajax');
    }

    private function _getList($offset, $per_page = NUMBER_OF_RECORDS_PER_PAGE)
    {

        $this->load->library('pagination');

        $config['base_url'] = site_url('goods/page');
        $parameter_url = "?";
        $this->load->model('Image_model');

        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['goods_list'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        } else {
            $this->data['org_status'] = false;
        }

        if ($this->User_model->getUserGroupId() == $setting_group_id) {
            $data['limit'] = $per_page;
            $data['start'] = $offset;
            $data['sort'] = 'sort_order';

            $results = $this->Goods_model->getGoodsList($data);

            $goods_total = $this->Goods_model->getTotalGoods($data);

            foreach ($results as $result) {

                if (isset($result['image'])) {
                    $info = pathinfo($result['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($result['image'], 0,
                                utf8_strrpos($result['image'], '.')) . '-50x50.' . $extension;
                        $image = S3_IMAGE . $new_image;
                    } else {
                        $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                    }
                } else {
                    $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                }

                $goodsIsPublic = $this->checkGoodsIsPublic($result['_id']);
                $org_name = null;
                if ($this->data['org_status']) {
                    if (isset($goods['organize_id']) && !empty($goods['organize_id'])) {
                        $org = $this->Store_org_model->retrieveOrganizeById($goods['organize_id']);
                        $org_name = $org["name"];
                    }
                }

                $this->data['goods_list'][] = array(
                    'goods_id' => $result['_id'],
                    'name' => $result['name'],
                    'quantity' => $result['quantity'],
                    'per_user' => $result['per_user'],
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order' => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'],
                            $this->input->post('selected'))),
                    'is_public' => $goodsIsPublic,
                    'organize_name' => $org_name
                );
            }
        } else {
            $filter_array = array();
            if (isset($_GET['filter_goods'])) {
                $parameter_url .= "&filter_goods=" . $_GET['filter_goods'];
                $filter_array['filter_goods'] = $_GET['filter_goods'];
            }
            if (isset($_GET['filter_group'])) {
                $parameter_url .= "&filter_group=" . $_GET['filter_group'];
                $filter_array['filter_group'] = $_GET['filter_group'] == "yes" ? true: false;
            }
            if (isset($_GET['filter_status'])) {
                $parameter_url .= "&filter_status=" . $_GET['filter_status'];
                $filter_array['filter_status'] = $_GET['filter_status'] == "enable" ? true : false;
            }
            if (isset($_GET['filter_tags'])) {
                $parameter_url .= "&filter_tags=" . $_GET['filter_tags'];
                $filter_array['filter_tags'] = $_GET['filter_tags'];
            }

            $good_list = $this->Goods_model->getGroupsList($this->session->userdata('site_id'), $filter_array);
            $in_goods = array();
            foreach ($good_list as $good_name){
                if($good_name['is_group']){
                    $goods_id =  $this->Goods_model->getGoodsIDByName($this->session->userdata('client_id'), $this->session->userdata('site_id'), "", $good_name['name'],false);
                } else {
                    $goods_id =  $this->Goods_model->getGoodsIDByName($this->session->userdata('client_id'), $this->session->userdata('site_id'), $good_name['name'],"",false);
                }
                array_push($in_goods, new MongoId($goods_id));
            }
            $goods_total = $this->Goods_model->getTotalGoodsBySiteId(array(
                'site_id' => $site_id,
                'sort' => 'sort_order',
                'specific' => array("goods_id" => array('$in' => $in_goods )),
            ));
            $goods_list = $this->Goods_model->getGoodsBySiteId(array(
                'site_id' => $site_id,
                'limit' => $per_page,
                'start' => $offset,
                'sort' => 'sort_order',
                'specific' => array("goods_id" => array('$in' => $in_goods )),
            ));

            $this->data['no_image'] = S3_IMAGE . "cache/no_image-50x50.jpg";

            foreach ($goods_list as $goods) {

                if (isset($goods['image'])) {
                    $info = pathinfo($goods['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($goods['image'], 0,
                                utf8_strrpos($goods['image'], '.')) . '-50x50.' . $extension;
                        $image = S3_IMAGE . $new_image;
                    } else {
                        $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                    }
                } else {
                    $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                }

                $is_group = array_key_exists('group', $goods);
                $org_name = null;
                if ($this->data['org_status']) {
                    if (isset($goods['organize_id']) && !empty($goods['organize_id'])) {
                        $org = $this->Store_org_model->retrieveOrganizeById($goods['organize_id']);
                        $org_name = $org["name"];
                    }
                }
                if(isset($goods['custom_param'])){
                    $param_array = array();
                    foreach ($goods['custom_param'] as $param){
                        if(strpos( $param['key'], POSTFIX_NUMERIC_PARAM ) == false){
                            array_push($param_array, implode(' : ', $param));
                        }
                    }
                }

                $this->data['goods_list'][] = array(
                    'goods_id' => $goods['_id'],
                    'name' => $is_group ? $goods['group'] : $goods['name'],
                    'quantity' => $is_group ? $this->Goods_model->checkGoodsGroupQuantity($this->session->userdata('site_id'), $goods['group']) : $goods['quantity'],
                    'per_user' => $goods['per_user'],
                    'status' => $goods['status'],
                    'image' => $image,
                    'sort_order' => $goods['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($goods['_id'], $this->input->post('selected'))),
                    'white_list' => isset($goods['distinct_id']) ? $this->Goods_model->checkGoodsWhiteList($site_id, $goods['distinct_id']) : false,
                    'custom_param' => isset($goods['custom_param']) && $param_array? $param_array : null,
                    'sponsor' => isset($goods['sponsor']) ? $goods['sponsor'] : null,
                    'is_group' => $is_group,
                    'organize_name' => $org_name,
                    'tags' => isset($goods['tags']) ? $goods['tags'] : null
                );
            }
        }

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

        $config['suffix'] =  $parameter_url == "?" ? "" : $parameter_url;
        $config['first_url'] = $parameter_url == "?" ? $config['base_url'] : $config['base_url'].$parameter_url;

        $config['total_rows'] = $goods_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

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

        $this->data['main'] = 'goods';
        $this->data['tabs'] = $this->lang->line('heading_title_goods_list');
        $this->data['setting_group_id'] = $setting_group_id;
    }

    private function getList($offset)
    {
        $this->_getList($offset);
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getForm($goods_id = null, $import = false)
    {

        $this->load->model('Image_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if (isset($goods_id) && ($goods_id != 0)) {
            if ($client_id) {
                $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            } else {
                $goods_info = $this->Goods_model->getGoods($goods_id);
            }
        }

        $this->data['is_import'] = $import;
        $this->data['is_group'] = $import || (!empty($goods_info) && array_key_exists('group', $goods_info));
        if (!empty($goods_info) && array_key_exists('group', $goods_info)) {
            $this->data['group'] = $goods_info['group'];
        }

        if (!empty($goods_info) && array_key_exists('group', $goods_info)) {

            $limit_group = 10;

            $data = array(
                'site_id' => $site_id,
                'group' => $goods_info['group'],
                'limit' => $limit_group,
                'start' => 0,
                'order' => 'asc',
                'sort' => 'date_start'
            );
            $this->data['members'] = $this->Goods_model->getAvailableGoodsByGroup($data);
            $this->data['members_batch'] = $this->Goods_model->getGoodBatchByDistinctID($this->User_model->getClientId(), $this->User_model->getSiteId(),$goods_info['distinct_id']);
            $this->data['members_total'] = $this->Goods_model->getTotalAvailableGoodsByGroup($data);
            $this->data['members_current_total_page'] = $this->data['members_total'] > $limit_group ? $limit_group : $this->data['members_total'];

            $total_page = $this->data['members_total'] > 0 ?  ceil($this->data['members_total'] / $limit_group) : 0;

            $this->data['total_page'] = $this->create(1, $total_page, 1,
                '<a class="paginate_button" data-page="%d">%d</a>',
                '<a class="paginate_button current" data-page="%d">%d</a>');
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($goods_info['name']) && !empty($goods_info['name'])) {
            $this->data['name'] = $goods_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('description')) {
            $this->data['description'] = htmlentities($this->input->post('description'));
        } elseif (isset($goods_info['description']) && !empty($goods_info['description'])) {
            $this->data['description'] = htmlentities($goods_info['description']);
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('code')) {
            $this->data['code'] = $this->input->post('code');
        } elseif (isset($goods_info['code']) && !empty($goods_info['code'])) {
            $this->data['code'] = $goods_info['code'];
        } else {
            $this->data['code'] = '';
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = $this->input->post('tags');
        } elseif (isset($goods_info['tags']) && !empty($goods_info['tags'])) {
            $this->data['tags'] = $goods_info['tags'];
        } else {
            $this->data['tags'] = null;
        }

        if ($this->input->post('custom_param')) {
            $this->data['custom_param'] = $this->input->post('custom_param');
        } elseif (isset($goods_info['custom_param']) && !empty($goods_info['custom_param'])) {
            $this->data['custom_param'] = $goods_info['custom_param'];
        } else {
            $this->data['custom_param'] = null;
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (isset($goods_info['image']) && !empty($goods_info['image'])) {
            $this->data['image'] = $goods_info['image'];
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

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (isset($goods_info['sort_order']) && !empty($goods_info['sort_order'])) {
            $this->data['sort_order'] = $goods_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($goods_info['status'])) {
            $this->data['status'] = $goods_info['status'] ? true : false;
        } else {
            $this->data['status'] = 1;
        }

        if ($this->input->post('whitelist_enable')) {
            $this->data['whitelist_enable'] = $this->input->post('whitelist_enable');
        } elseif (isset($goods_info['distinct_id'])) {
            $this->data['distinct_id']  = $goods_info['distinct_id'];
            $distinct_id = $goods_info['distinct_id'];
            $distinct_info = $this->Goods_model->getGoodsDistinctByID($site_id,$distinct_id);
            $whitelist_info = $this->Goods_model->getGoodsWhiteList($client_id, $site_id, $distinct_id);
            $this->data['whitelist_enable'] = isset($distinct_info['whitelist_enable']) ? $distinct_info['whitelist_enable'] : false;
            if($this->data['whitelist_enable'] == true) {
                $this->data['whitelist_file_name'] = isset($whitelist_info['file_name']) ? $whitelist_info['file_name'] : "";
            }
        } else {
            $this->data['whitelist_enable'] = false;
        }

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($client_id, 'store_org')
        ) {
            $this->data['org_status'] = true;
            if ($this->input->post('organize_id')) {
                $this->data['organize_id'] = $this->input->post('organize_id');
            } elseif (isset($goods_info['organize_id']) && !empty($goods_info['organize_id'])) {
                $this->data['organize_id'] = $goods_info['organize_id'];
            } else {
                $this->data['organize_id'] = null;
            }

            if ($this->input->post('organize_role')) {
                $this->data['organize_role'] = $this->input->post('organize_role');
            } elseif (isset($goods_info['organize_role']) && !empty($goods_info['organize_role'])) {
                $this->data['organize_role'] = $goods_info['organize_role'];
            } else {
                $this->data['organize_role'] = null;
            }
        } else {
            $this->data['org_status'] = false;
        }


        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (isset($goods_info['quantity']) && !empty($goods_info['quantity'])) {
            $this->data['quantity'] = $goods_info['quantity'];
        } else {
            $this->data['quantity'] = null;
        }

        if ($this->input->post('per_user')) {
            $this->data['per_user'] = $this->input->post('per_user');
        } elseif (isset($goods_info['per_user']) && !empty($goods_info['per_user'])) {
            $this->data['per_user'] = $goods_info['per_user'];
        } else {
            $this->data['per_user'] = null;
        }

        if ($this->input->post('per_user_include_inactive')) {
            $this->data['per_user_include_inactive'] = $this->input->post('per_user_include_inactive');
        } elseif (isset($goods_info['per_user_include_inactive']) && !empty($goods_info['per_user_include_inactive'])) {
            $this->data['per_user_include_inactive'] = $goods_info['per_user_include_inactive'];
        } else {
            $this->data['per_user_include_inactive'] = null;
        }

        if ($this->input->post('reward_point')) {
            $this->data['reward_point'] = $this->input->post('reward_point');
        } elseif (isset($goods_info['redeem']['point']) && !empty($goods_info['redeem']['point'])) {
            $this->data['reward_point'] = isset($goods_info['redeem']['point']) ? $goods_info['redeem']['point']['point_value'] : 0;
        } else {
            $this->data['reward_point'] = 0;
        }

        if ($this->input->post('reward_badge')) {
            $this->data['reward_badge'] = $this->input->post('reward_badge');
        } elseif (isset($goods_info['redeem']['badge']) && !empty($goods_info['redeem']['badge'])) {
            $this->data['reward_badge'] = isset($goods_info['redeem']['badge']) ? $goods_info['redeem']['badge'] : array();
        } else {
            $this->data['reward_badge'] = array();
        }

        if ($this->input->post('reward_reward')) {
            $this->data['reward_reward'] = $this->input->post('reward_reward');
        } elseif (isset($goods_info['redeem']['custom']) && !empty($goods_info['redeem']['custom'])) {
            $this->data['reward_reward'] = isset($goods_info['redeem']['custom']) ? $goods_info['redeem']['custom'] : array();
        } else {
            $this->data['reward_reward'] = array();
        }

        if ($this->input->post('sponsor')) {
            $this->data['sponsor'] = $this->input->post('sponsor');
        } elseif (isset($goods_info['sponsor']) && !empty($goods_info['sponsor'])) {
            $this->data['sponsor'] = isset($goods_info['sponsor']) ? $goods_info['sponsor'] : null;
        } else {
            $this->data['sponsor'] = false;
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (isset($goods_info['date_start']) && !empty($goods_info['date_start'])) {
            $this->data['date_start'] = $goods_info['date_start'];
        } else {
            $this->data['date_start'] = "";
        }

        if ($this->input->post('date_expire')) {
            $this->data['date_expire'] = $this->input->post('date_expire');
        } elseif (isset($goods_info['date_expire']) && !empty($goods_info['date_expire'])) {
            $this->data['date_expire'] = $goods_info['date_expire'];
        } else {
            $this->data['date_expire'] = "";
        }

        if ($this->input->post('days_expire')) {
            $this->data['days_expire'] = $this->input->post('days_expire');
        } elseif (isset($goods_info['days_expire']) && !empty($goods_info['days_expire'])) {
            $this->data['days_expire'] = $goods_info['days_expire'];
        } else {
            $this->data['days_expire'] = "";
        }

        if (!empty($goods_info) && !array_key_exists('group', $goods_info)) {
            if ($this->input->post('date_expired_coupon')) {
                $this->data['date_expired_coupon'] = $this->input->post('date_expired_coupon');
            } elseif (isset($goods_info['date_expired_coupon']) && !empty($goods_info['date_expired_coupon'])) {
                $this->data['date_expired_coupon'] = $goods_info['date_expired_coupon'];
            } else {
                $this->data['date_expired_coupon'] = "";
            }
        }

        if (isset($goods_id)) {
            $this->data['goods_id'] = $goods_id;
        } else {
            $this->data['goods_id'] = null;
        }

        if ($client_id) {
            if ($this->data['sponsor']) {
                redirect('/goods', 'refresh');
            }
        }

        $this->load->model('Client_model');
        $this->data['to_clients'] = $this->Client_model->getClients(array());
        $this->data['client_id'] = $client_id;
        $this->data['site_id'] = $site_id;

        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['badge_list'] = array();
        if ($this->User_model->getUserGroupId() != $setting_group_id) {
            $this->data['badge_list'] = $this->Badge_model->getBadgeBySiteId(array("site_id" => $site_id));
        }
        if (!empty($goods_info)) {
            $goods_private = $this->Goods_model->getGoodsOfClientPrivate($goods_id);
            if (!$this->checkGoodsIsPublic($goods_private['goods_id'])) {
                $this->data['badge_list'] = $this->Badge_model->getBadgeBySiteId(array("site_id" => $goods_private['site_id']));
            }
        }

        $this->data['point_list'] = array();
        if ($this->User_model->getUserGroupId() != $setting_group_id) {
            $this->data['point_list'] = $this->Reward_model->getAnotherRewardBySiteId($site_id);
        }
        if (!empty($goods_info)) {
            $goods_private = $this->Goods_model->getGoodsOfClientPrivate($goods_id);
            if (!$this->checkGoodsIsPublic($goods_private['goods_id'])) {
                $this->data['point_list'] = $this->Reward_model->getAnotherRewardBySiteId($goods_private['site_id']);
            }
        }

        $this->data['main'] = 'goods_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getGoodsGroupAjax($goods_id = null)
    {

        $offset = $this->input->get('page') ? $this->input->get('page') - 1 : 0;
        $limit = 10;

        if (isset($goods_id) && ($goods_id != 0)) {
            if ($this->User_model->getClientId()) {
                $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            } else {
                $goods_info = $this->Goods_model->getGoods($goods_id);
            }
        }
        if (!empty($goods_info) && array_key_exists('group', $goods_info)) {
            $data = array(
                'site_id' => $this->User_model->getSiteId(),
                'group' => $goods_info['group'],
                'limit' => $limit,
                'start' => $offset * $limit,
                'order' => 'asc',
                'sort' => 'date_start'
            );

            $this->data['filter'] = array();
            if (isset($_GET['filter_goods'])) {
                $this->data['filter']['filter_goods'] = $_GET['filter_goods'];
                $data['filter_goods'] = $_GET['filter_goods'];
            }
            if (isset($_GET['filter_batch'])) {
                $this->data['filter']['filter_batch'] = $_GET['filter_batch'];
                $data['filter_batch'] = $_GET['filter_batch'];
            }
            if (isset($_GET['filter_coupon_name'])) {
                $this->data['filter']['filter_coupon_name'] =  $_GET['filter_coupon_name'];
                $data['filter_name'] = $_GET['filter_coupon_name'];
            }
            if (isset($_GET['filter_voucher_code'])) {
                $this->data['filter']['filter_voucher_code'] =  $_GET['filter_voucher_code'];
                $data['filter_voucher_code'] = $_GET['filter_voucher_code'];
            }

            $this->data['members'] = $this->Goods_model->getAvailableGoodsByGroup($data);
            $this->data['members_batch'] = $this->Goods_model->getGoodBatchByDistinctID($this->User_model->getClientId(), $this->User_model->getSiteId(),$goods_info['distinct_id']);
            $this->data['members_total'] = $this->Goods_model->getTotalAvailableGoodsByGroup($data);
            $this->data['members_current_total_page'] = ($limit * ($offset + 1)) >= $limit ? ($limit * ($offset + 1)) : $this->data['members_total'];
            $this->data['members_current_start_page'] = (($limit * $offset) + 1) >= 1 ? (($limit * $offset) + 1) : 1;

            $total_page = $this->data['members_total'] > 0 ? ceil($this->data['members_total'] / $limit) : 0;

            $this->data['total_page'] = $this->create(($offset + 1), $total_page, 1,
                '<a class="paginate_button" data-page="%d">%d</a>',
                '<a class="paginate_button current" data-page="%d">%d</a>');
        }

        $this->load->vars($this->data);
        $this->load->view('goods_group_ajax');
    }

    public function getBadgeForGoods()
    {
        if ($this->input->get('client_id')) {
            $this->load->model('Badge_model');
            $this->data['badge_list'] = $this->Badge_model->getBadgeByClientId(array("client_id" => $this->input->get('client_id')));

            $this->load->vars($this->data);
            $this->render_page('goods_badge_list_ajax');
        } else {
            $this->output->set_status_header('404');
        }
    }

    public function getCustomForGoods()
    {
        if ($this->input->get('client_id')) {
            $this->load->model('Reward_model');

            $this->data['point_list'] = $this->Reward_model->getAnotherRewardByClientId($this->input->get('client_id'));

            $this->load->vars($this->data);
            $this->render_page('goods_reward_list_ajax');
        } else {
            $this->output->set_status_header('404');
        }
    }

    public function markUsed($goods_to_player_id)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                try {
                    $goods_to_player = $this->Goods_model->getGoodsToPlayer($goods_to_player_id);
                } catch (Exception $e) {
                    $this->output->set_status_header('404');
                    echo json_encode(array('status' => 'error'));
                    die();
                }

                if (isset($goods_to_player)) {
                    $goods_info = $this->Goods_model->getGoodsOfClientPrivate($goods_to_player['goods_id']);
                    $this->Goods_model->markAsVerifiedGoods(array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'goods_id' => $goods_to_player['goods_id'],
                        'goods_group' => $goods_info['group'],
                        'cl_player_id' => $goods_to_player['cl_player_id'],
                        'pb_player_id' => $goods_to_player['pb_player_id'],
                    ));

                    $this->output->set_status_header('200');
                    echo json_encode(array('status' => 'success'));
                } else {
                    $this->output->set_status_header('404');
                    echo json_encode(array('status' => 'error'));
                    die();
                }
            }
        }
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'goods')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkOwnerGoods($goodsId)
    {
        $error = null;
        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
            $c = $this->Goods_model->getTotalGoodsBySiteId(array(
                'site_id' => $this->User_model->getSiteId(),
                '$in' => array(new MongoId($goodsId)),
            ));
            $has = ($c > 0);
            if (!$has) {
                $error = $this->lang->line('error_permission');
            }
        }
        return !$error;
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'goods') && $this->Feature_model->getFeatureExistByClientId($client_id, 'goods')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function increase_order($goods_id)
    {

        if ($this->User_model->getClientId()) {
            $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            if ($goods_info && array_key_exists('group', $goods_info)) {
                $goods_data['client_id'] = $this->User_model->getClientId();
                $goods_data['site_id'] = $this->User_model->getSiteId();
                $this->Goods_model->increaseOrderOfGroupByOneClient($goods_id, $goods_info['group'], $goods_data);
            }else{
                $this->Goods_model->increaseOrderByOneClient($goods_id);
            }

        } else {
            $this->Goods_model->increaseOrderByOne($goods_id);
        }

        $json = array('success' => 'Okay increase!');

        $this->output->set_output(json_encode($json));
    }

    public function decrease_order($goods_id)
    {

        if ($this->User_model->getClientId()) {
            $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            if ($goods_info && array_key_exists('group', $goods_info)) {
                $goods_data['client_id'] = $this->User_model->getClientId();
                $goods_data['site_id'] = $this->User_model->getSiteId();
                $this->Goods_model->decreaseOrderOfGroupByOneClient($goods_id, $goods_info['group'], $goods_data);
            }else {
                $this->Goods_model->decreaseOrderByOneClient($goods_id);
            }
        } else {
            $this->Goods_model->decreaseOrderByOne($goods_id);
        }

        $json = array('success' => 'Okay decrease!');

        $this->output->set_output(json_encode($json));
    }

    public function checkGoodsIsPublic($goods_id)
    {
        $allGoodsFromClients = $this->Goods_model->checkGoodsIsPublic($goods_id);

        if (isset($allGoodsFromClients[0]['client_id'])) {
            $firstGoods = $allGoodsFromClients[0]['client_id'];
            foreach ($allGoodsFromClients as $goods) {
                if ($goods['client_id'] != $firstGoods) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    private function addGoods($handle, $data, $redeem, $list_client_id, $list_site_id)
    {
        $list = array();

        /* build template */
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        if (!empty($data['tags'])){
            if(!is_array($data['tags'])) {
                $tags = explode(',', $data['tags']);
            } else {
                $tags = $data['tags'];
            }
        }
        $template = array(
            'description' => $data['description'] | '',
            'quantity' => (isset($data['quantity']) && !empty($data['quantity'])) ? (int)$data['quantity'] : null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user'])) ? (int)$data['per_user'] : null,
            'per_user_include_inactive' => (isset($data['per_user_include_inactive']) && !empty($data['per_user_include_inactive'])) ? (bool)$data['per_user_include_inactive'] : false,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? $data['sponsor'] : false,
            'sort_order' => (int)$data['sort_order'] | 1,
            'language_id' => 1,
            'redeem' => $redeem,
            'custom_param' => isset($data['custom_param']) ? $data['custom_param'] : array(),
            'distinct_id' => isset($data['distinct_id']) ? $data['distinct_id'] : null,
            'tags' => isset($tags) ? $tags : null,
            'date_start' => null,
            'date_expire' => null,
            'days_expire' => isset($data['days_expire']) && !empty($data['days_expire']) ? $data['days_expire'] : null,
            'date_added' => $d,
            'date_modified' => $d,

        );

        if (isset($data['organize_id'])) {
            $template['organize_id'] = new MongoID($data['organize_id']);
        }

        if (isset($data['organize_role'])) {
            $template['organize_role'] = $data['organize_role'];
        }
        /* loop insert into playbasis_goods */
        $parameter_set = null;
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line) || $line == ',' || (!$parameter_set && strpos($line,'date_start'))) {
                $line = trim($line);
                $parameter_set = $line;
                continue;
            } // skip empty line
            $obj = explode(',', $line);
            $name = trim($obj[0]);
            $code = trim(isset($obj[1]) ? $obj[1] : $name);
            $date_start = trim(isset($obj[2]) && !empty($obj[2]) ? $obj[2] : null);
            $date_end = trim(isset($obj[3]) && !empty($obj[3]) ? $obj[3] : null);
            $date_expired_coupon = trim(isset($obj[4]) && !empty($obj[4]) ? $obj[4] : null);
            $batch_name = trim(isset($obj[5]) && !empty($obj[5]) ? $obj[5] : 'default');
            $each = array_merge($template, array('name' => $name));
            if(!empty($date_start) && !empty($date_end)){
                $date_start_another = strtotime($date_start);
                $date_expire_another = strtotime($date_end);
                if ($date_start_another < $date_expire_another) {
                    $each['date_start'] = new MongoDate($date_start_another);
                    $each['date_expire'] = new MongoDate($date_expire_another);
                }
            } else {
                if (!empty($date_start)) {
                    $date_start_another = strtotime($date_start);
                    $each['date_start'] = new MongoDate($date_start_another);
                }
                if (!empty($date_end)) {
                    $date_expire_another = strtotime($date_end);
                    $each['date_expire'] = new MongoDate($date_expire_another);
                }
            }
            if(!empty($date_expired_coupon)){
                $each = array_merge($each, array('date_expired_coupon' => new MongoDate(strtotime($date_expired_coupon))));
            }
            $goods_id = $this->Goods_model->addGoods($each);
            $each = array_merge($each, array('code' => $code, 'batch_name' => $batch_name));
            foreach ($list_client_id as $client_id) {
                foreach ($list_site_id as $site_id) {
                    array_push($list, array_merge($each, array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'goods_id' => $goods_id,
                        'group' => $data['name'],
                    )));
                    $n = $this->Goods_model->checkBatchNameExistInDistinct($data['name'],array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $batch_name));
                    if($n == 0){
                        $this->Goods_model->addBatchNameInDistinct($data['name'], array('client_id' => $client_id, 'site_id' => $site_id, 'batch_name' => $batch_name));
                    }
                }
            }
        }


        /* check limit for goods group */
        $site_id = $this->User_model->getSiteId();
        $usage = $this->Goods_model->getTotalGoodsBySiteId(array('site_id' => $site_id));
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'goods');
        if ($limit !== null && $usage + count($list) > $limit) {
            throw new Exception('Cannot process your request because of uploaded goods will go over the limit');
        }
        $this->Goods_model->addGoodsToClient_bulk($list);
        $goods_data = $this->Goods_model->getGoodsOfClientPrivate($goods_id);
        $data['goods_id'] = $goods_data['_id'];
        $this->Goods_model->auditAfterGoods('insert', $goods_data['_id'], $this->User_model->getId());
        /* bulk insert into playbasis_goods_to_client */
        return $data;
    }

    /* Returns a set of pagination links. The parameters are:
   *
   * $page          - the current page number
   * $numberOfPages - the total number of pages
   * $context       - the amount of context to show around page links - this
   *                  optional parameter defauls to 1
   * $linkFormat    - the format to be used for links to other pages - this
   *                  parameter is passed to sprintf, with the page number as a
   *                  second and third parameter. This optional parameter
   *                  defaults to creating an HTML link with the page number as
   *                  a GET parameter.
   * $pageFormat    - the format to be used for the current page - this
   *                  parameter is passed to sprintf, with the page number as a
   *                  second and third parameter. This optional parameter
   *                  defaults to creating an HTML span containing the page
   *                  number.
   * $ellipsis      - the text to be used where pages are omitted - this
   *                  optional parameter defaults to an ellipsis ('...')
   */
    private function create(
        $page,
        $numberOfPages,
        $context = 1,
        $linkFormat = '<a href="?page=%d">%d</a>',
        $pageFormat = '<span>%d</span>',
        $ellipsis = '&hellip;'
    ) {

        // create the list of ranges
        $ranges = array(array(1, 1 + $context));
        self::mergeRanges($ranges, $page - $context, $page + $context);
        self::mergeRanges($ranges, $numberOfPages - $context, $numberOfPages);

        // initialise the list of links
        $links = array();

        // loop over the ranges
        foreach ($ranges as $range) {

            // if there are preceeding links, append the ellipsis
            if (count($links) > 0) {
                $links[] = $ellipsis;
            }

            // merge in the new links
            $links =
                array_merge(
                    $links,
                    self::createLinks($range, $page, $linkFormat, $pageFormat));

        }

        // return the links
        return implode(' ', $links);

    }

    /* Merges a new range into a list of ranges, combining neighbouring ranges.
     * The parameters are:
     *
     * $ranges - the list of ranges
     * $start  - the start of the new range
     * $end    - the end of the new range
     */
    private function mergeRanges(&$ranges, $start, $end)
    {

        // determine the end of the previous range
        $endOfPreviousRange =& $ranges[count($ranges) - 1][1];

        // extend the previous range or add a new range as necessary
        if ($start <= $endOfPreviousRange + 1) {
            $endOfPreviousRange = $end;
        } else {
            $ranges[] = array($start, $end);
        }

    }

    /* Create the links for a range. The parameters are:
     *
     * $range      - the range
     * $page       - the current page
     * $linkFormat - the format for links
     * $pageFormat - the format for the current page
     */
    private function createLinks($range, $page, $linkFormat, $pageFormat)
    {

        // initialise the list of links
        $links = array();

        // loop over the pages, adding their links to the list of links
        for ($index = $range[0]; $index <= $range[1]; $index++) {
            $links[] =
                sprintf(
                    ($index == $page ? $pageFormat : $linkFormat),
                    $index,
                    $index);
        }

        // return the array of links
        return $links;

    }

    private function extract_goods_id($obj)
    {
        return $obj['goods_id'];
    }

    private function extract_pb_player_id($obj)
    {
        return $obj['pb_player_id'];
    }

    private function extract_node_id($obj)
    {
        return $obj['node_id'];
    }

    private function extract_organize_id($obj)
    {
        return $obj['organize'];
    }

    private function searchForId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['_id'] == $id) {
                return $key;
            }
        }
        return null;
    }

    private function searchForGoodsId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['goods_id'] == $id) {
                return $key;
            }
        }
        return null;
    }

    private function searchForPBPlayerId($id, $array)
    {
        $indexes = array();
        foreach ($array as $key => $val) {
            if ($val['pb_player_id'] == $id) {
                array_push($indexes, $key);
            }
        }
        return !empty($indexes) ? $indexes : null;
    }

    private function generateWhiteListData($handle,&$data){
        $result = true;
        $cl_player_id_list = array();
        $file_content = array();
        while ((($line = fgets($handle)) !== false) && $result ) {
            $file_content[]= $line;
            $line = str_replace(' ', '', trim($line));
            if (empty($line)) {
                // skip empty line
                continue;
            }
            $obj = explode(',', $line);
            foreach($obj as $cl_player_id){
                if($cl_player_id){
                    $cl_player_id_list[]= $cl_player_id;
                }
            }
        }
        $data['file_content'] = $file_content;
        $data['cl_player_id_list'] = $cl_player_id_list;
        return $result;
    }

    private function generateWhiteListBatch($cl_player_id_list, $client_id, $site_id, $distinct_id){
        $result = array();
        $date_added = new MongoDate();
        foreach ($cl_player_id_list as $cl_player_id){
            $result[] = array(
                'client_id' => new MongoId($client_id),
                'site_id' => new MongoId($site_id),
                'distinct_id' => new MongoId($distinct_id),
                'cl_player_id' => $cl_player_id,
                'date_added' => $date_added
            );
        }

        return $result;
    }

    public function getWhitelistFile()
    {
        $whitelist_file = $this->Goods_model->retrieveWhiteListFile($this->User_model->getClientId(),$this->User_model->getSiteId(),$this->input->get('distinct_id'));

        if(isset($whitelist_file['file_content'])){
            $this->load->helper('export_data');

            $exporter = new ExportDataCSVSequence('browser', $whitelist_file['file_name']);

            $exporter->initialize(); // starts streaming data to web browser

            foreach ($whitelist_file['file_content'] as $row) {
                $exporter->addRow(array($row) );
            }
            $exporter->finalize();
        }

    }
}
