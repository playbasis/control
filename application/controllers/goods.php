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

                $maxsize = 2097152;
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

            if ($this->User_model->getClientId() && $this->User_model->getSiteId()) {
                if ($this->input->post('name') && $this->Goods_model->checkExists($this->User_model->getSiteId(),
                        $this->input->post('name'))
                ) {
                    $this->data['message'] = $this->lang->line('error_group_exists');
                }
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $data = array_merge($this->input->post(), array('quantity' => 1));

                if ($this->User_model->getClientId()) {

                    try {
                        $this->addGoods($handle, $data, $redeem, array($this->User_model->getClientId()),
                            array($this->User_model->getSiteId()));
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        fclose($handle);
                        redirect('/goods', 'refresh');
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
                            $this->addGoods($handle, $data, $redeem, array(new MongoId($goods_data['admin_client_id'])),
                                $list_site_id);
                            fclose($handle);
                            redirect('/goods', 'refresh');
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
                            $this->addGoods($handle, $data, $redeem, array_keys($hash_client_id), array($list_site_id));
                            fclose($handle);
                            redirect('/goods', 'refresh');
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

            $goods_data = $this->input->post();
            $goods_data['redeem'] = $redeem;

            if ($this->form_validation->run() && $this->data['message'] == null) {

                if ($this->User_model->getClientId()) {

                    $goods_id = $this->Goods_model->addGoods($goods_data);

                    $goods_data['goods_id'] = $goods_id;
                    $goods_data['client_id'] = $this->User_model->getClientId();
                    $goods_data['site_id'] = $this->User_model->getSiteId();

                    $this->Goods_model->addGoodsToClient($goods_data);

                    $this->session->set_flashdata('success', $this->lang->line('text_success'));

                    redirect('/goods', 'refresh');
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
                            $this->Goods_model->addGoodsToClient($goods_data);
                        }
                    } else {
                        $goods_data['goods_id'] = $this->Goods_model->addGoods($goods_data);

                        $all_sites_clients = $this->Client_model->getAllSitesFromAllClients();

                        foreach ($all_sites_clients as $site) {
                            $goods_data['site_id'] = $site['_id'];
                            $goods_data['client_id'] = $site['client_id'];
                            $this->Goods_model->addGoodsToClient($goods_data);
                        }
                    }
                    redirect('/goods', 'refresh');
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

            $goods_data = $this->input->post();
            if ($this->input->post('quantity') === false) {
                $goods_data = array_merge($goods_data, array('quantity' => 1));
            }
            $goods_data['redeem'] = $redeem;

            if ($this->User_model->hasPermission('access', 'store_org') &&
                $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
            ) {
                if ($this->input->post('global_goods')) {
                    $goods_data['organize_id'] = null;
                    $goods_data['organize_role'] = null;
                }
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                try {

                    if ($this->User_model->getClientId()) {

                        if (!$this->Goods_model->checkGoodsIsSponsor($goods_id)) {
                            $goods_data['client_id'] = $this->User_model->getClientId();
                            $goods_data['site_id'] = $this->User_model->getSiteId();
                            $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
                            if ($goods_info && array_key_exists('group', $goods_info)) {

                                /* if there is an uploaded file, then import it into the group */
                                if (!empty($_FILES) && isset($_FILES['file']['tmp_name']) && !empty($_FILES['file']['tmp_name'])) {
                                    $data = array_merge($this->input->post(), array('quantity' => 1));
                                    $handle = fopen($_FILES['file']['tmp_name'], "r");
                                    $this->addGoods($handle, $data, $redeem, array($this->User_model->getClientId()),
                                        array($this->User_model->getSiteId()));
                                    fclose($handle);
                                }

                                /* update all existing records in the group */
                                $this->Goods_model->editGoodsGroupToClient($goods_info['group'], $goods_data);
                            } else {
                                $this->Goods_model->editGoodsToClient($goods_id, $goods_data);
                            }
                        } else {
                            redirect('/goods', 'refresh');
                        }
                    } else {
                        $this->Goods_model->editGoods($goods_id, $goods_data);

                        $this->Goods_model->editGoodsToClientFromAdmin($goods_id, $goods_data);
                    }

                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));

                    redirect('/goods', 'refresh');

                } catch (Exception $e) {
                    $this->data['message'] = $e->getMessage();
                }
            }
        }

        $this->getForm($goods_id);
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
                                $this->Goods_model->deleteGoodsGroupClient($goods_info['group'],
                                    $this->User_model->getClientId(), $this->User_model->getSiteId());
                            } else {
                                $this->Goods_model->deleteGoodsClient($goods_id);
                            }
                        } else {
                            redirect('/goods', 'refresh');
                        }
                    } else {
                        $this->Goods_model->deleteGoods($goods_id);
                        $this->Goods_model->deleteGoodsClientFromAdmin($goods_id);
                    }

                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/goods', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset)
    {
        $this->_getList($offset);

        //todo: Node type should only shown when match with goods's organize.

        $_goods_list = array_values(array_unique(array_map(array($this, 'extract_goods_id'),
            $this->Goods_model->getAllRedeemedGoods(array('site_id' => $this->User_model->getSiteId())))));
        $redeemed_goods_list = $this->Goods_model->listRedeemedGoods($_goods_list,
            array('goods_id', 'cl_player_id', 'pb_player_id'));

        $this->load->model('Player_model');
        $_pb_player_id_list = array_values(array_unique(array_map(array($this, 'extract_pb_player_id'),
            $redeemed_goods_list)));
        $players_detail_list = $this->Player_model->listPlayers($_pb_player_id_list,
            array('first_name', 'last_name', 'cl_player_id'));

        $players_with_node_detail_list = $this->Player_model->listPlayersOrganize($_pb_player_id_list,
            array('node_id', 'pb_player_id'));
        $_node_list = array_values(array_unique(array_map(array($this, 'extract_node_id'),
            $players_with_node_detail_list)));
        $node_detail_list = $this->Store_org_model->listNodes($_node_list,
            array('name', 'description', 'organize'));

        $_organization_list = array_values(array_unique(array_map(array($this, 'extract_organize_id'),
            $node_detail_list)));
        $organization_detail_list = $this->Store_org_model->listOrganizations($_organization_list,
            array('name', 'description'));
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
                $player_index = $this->searchForId(new MongoId($redeemed_goods['pb_player_id']),
                    $players_detail_list);
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
                    $redeemed_goods['code'] = isset($goods[$goods_id]['code']) ? $goods[$goods_id]['code'] : null;
                }
            }
        }

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

                /*if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }*/
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
            $results = $this->Goods_model->getGroupsAggregate($this->session->userdata('site_id'));
            $ids = array();
            $group_name = array();
            foreach ($results as $i => $result) {
                $group = $result['_id']['group'];
                $quantity = $result['quantity'];
                $list = $result['list'];
                $first = array_shift($list); // skip first one
                $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
                $ids = array_merge($ids, $list);
            }
            $goods_total = $this->Goods_model->getTotalGoodsBySiteId(array(
                'site_id' => $site_id,
                'sort' => 'sort_order',
                '$nin' => $ids
            ));
            $goods_list = $this->Goods_model->getGoodsBySiteId(array(
                'site_id' => $site_id,
                'limit' => $per_page,
                'start' => $offset,
                'sort' => 'sort_order',
                '$nin' => $ids
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
                /*if ($goods['image'] && (S3_IMAGE . $goods['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $goods['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($goods['image'], 50, 50);
                }
                else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }*/

                $_id = $goods['_id']->{'$id'};
                $is_group = array_key_exists($_id, $group_name);
                $org_name = null;
                if ($this->data['org_status']) {
                    if (isset($goods['organize_id']) && !empty($goods['organize_id'])) {
                        $org = $this->Store_org_model->retrieveOrganizeById($goods['organize_id']);
                        $org_name = $org["name"];
                    }
                }

                $this->data['goods_list'][] = array(
                    'goods_id' => $goods['_id'],
                    'name' => $is_group ? $group_name[$_id]['group'] : $goods['name'],
                    'quantity' => $is_group ? $group_name[$_id]['quantity'] : $goods['quantity'],
                    'per_user' => $goods['per_user'],
                    'status' => $goods['status'],
                    'image' => $image,
                    'sort_order' => $goods['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($goods['_id'],
                            $this->input->post('selected'))),
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
        $this->data['setting_group_id'] = $setting_group_id;
    }

    private function getForm($goods_id = null, $import = false)
    {

        $this->load->model('Image_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        if (isset($goods_id) && ($goods_id != 0)) {
            if ($this->User_model->getClientId()) {
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
                'site_id' => $this->User_model->getSiteId(),
                'group' => $goods_info['group'],
                'limit' => $limit_group,
                'start' => 0
            );
            $this->data['members'] = $this->Goods_model->getAvailableGoodsByGroup($data);
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

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
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

        if (isset($goods_id)) {
            $this->data['goods_id'] = $goods_id;
        } else {
            $this->data['goods_id'] = null;
        }

        if ($this->User_model->getClientId()) {
            if ($this->data['sponsor']) {
                redirect('/goods', 'refresh');
            }
        }

        $this->load->model('Client_model');
        $this->data['to_clients'] = $this->Client_model->getClients(array());
        $this->data['client_id'] = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
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
                'start' => $offset * $limit
            );
            $this->data['members'] = $this->Goods_model->getAvailableGoodsByGroup($data);
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

            $goods_data = array('site_id' => $this->User_model->getSiteId());

            $goods_list = $this->Goods_model->getGoodsBySiteId($goods_data);
            $has = false;

            foreach ($goods_list as $goods) {
                if ($goods['_id'] . "" == $goodsId . "") {
                    $has = true;
                }
            }

            if (!$has) {
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
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
            $this->Goods_model->increaseOrderByOneClient($goods_id);
        } else {
            $this->Goods_model->increaseOrderByOne($goods_id);
        }

        $json = array('success' => 'Okay increase!');

        $this->output->set_output(json_encode($json));
    }

    public function decrease_order($goods_id)
    {

        if ($this->User_model->getClientId()) {
            $this->Goods_model->decreaseOrderByOneClient($goods_id);
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
            $tags = explode(',', $data['tags']);
        }
        $template = array(
            'description' => $data['description'] | '',
            'quantity' => (isset($data['quantity']) && !empty($data['quantity'])) ? (int)$data['quantity'] : null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user'])) ? (int)$data['per_user'] : null,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? $data['sponsor'] : false,
            'sort_order' => (int)$data['sort_order'] | 1,
            'language_id' => 1,
            'redeem' => $redeem,
            'tags' => isset($tags) ? $tags : null,
            'date_start' => null,
            'date_expire' => null,
            'days_expire' => isset($data['days_expire']) && !empty($data['days_expire']) ? $data['days_expire'] : null,
            'date_added' => $d,
            'date_modified' => $d,
        );
        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);
            if ($date_start_another < $date_expire_another) {
                $template['date_start'] = new MongoDate($date_start_another);
                $template['date_expire'] = new MongoDate($date_expire_another);
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $template['date_start'] = new MongoDate($date_start_another);
            }
            if (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $template['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        if (isset($data['organize_id'])) {
            $template['organize_id'] = new MongoID($data['organize_id']);
        }

        if (isset($data['organize_role'])) {
            $template['organize_role'] = $data['organize_role'];
        }

        /* loop insert into playbasis_goods */
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line) || $line == ',') {
                continue;
            } // skip empty line
            $obj = explode(',', $line);
            $name = trim($obj[0]);
            $code = trim(isset($obj[1]) ? $obj[1] : $name);
            $date_expired_coupon = trim(isset($obj[2]) && !empty($obj[2]) ? $obj[2] : null);
            $each = array_merge($template, array('name' => $name));
            if(!empty($date_expired_coupon)){
                $each = array_merge($each, array('date_expired_coupon' => new MongoDate(strtotime($date_expired_coupon))));
            }
            $goods_id = $this->Goods_model->addGoods($each);
            $each = array_merge($each, array('code' => $code));
            foreach ($list_client_id as $client_id) {
                foreach ($list_site_id as $site_id) {
                    array_push($list, array_merge($each, array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'goods_id' => $goods_id,
                        'group' => $data['name'],
                    )));
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

        /* bulk insert into playbasis_goods_to_client */
        return $this->Goods_model->addGoodsToClient_bulk($list);
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
}
