<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Goods extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Goods_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("goods", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);

    }

    public function page($offset=0) {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'goods/insert';

        //I took out the check_space because some goods may have spaces? - Joe
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('stackable', "", '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if($this->checkLimitGoods()){
                $this->data['message'] = null;

                if (!$this->validateModify()) {
                    $this->data['message'] = $this->lang->line('error_permission');
                }

                if($this->form_validation->run() && $this->data['message'] == null){

                    if($this->User_model->getClientId()){

                        $goods_data = $this->input->post();

                        $goods_id = $this->Goods_model->addGoods($goods_data);

                        $goods_data['goods_id'] = $goods_id;

                        $this->Goods_model->addGoodsToClient($goods_data);

                        $this->session->set_flashdata('success', $this->lang->line('text_success'));

                        redirect('/goods', 'refresh');
                    }else{

                        $goods_data = $this->input->post();

                        if($goods_data['client_id'] != 'all_clients'){
                            $this->load->model('Client_model');
                            $clients_sites = $this->Client_model->getSitesByClientId($goods_data['client_id']);

                            $goods_data['goods_id'] = $this->Goods_model->addGoods($goods_data);

                            foreach ($clients_sites as $client){
                                $goods_data['site_id'] = $client['_id'];
                                $this->Goods_model->addGoodsToClient($goods_data);
                            }
                        }elseif ($goods_data['client_id'] == 'all_clients'){
                            $goods_data['goods_id'] = $this->Goods_model->addGoods($goods_data);

                            $this->load->model('Client_model');
                            $all_sites_clients = $this->Client_model->getAllSitesFromAllClients();

                            foreach($all_sites_clients as $site){
                                $goods_data['site_id'] = $site['_id'];
                                $goods_data['client_id'] = $site['client_id'];
                                $this->Goods_model->addGoodsToClient($goods_data);
                            }
                        }
                        redirect('/goods', 'refresh');
                    }

                }
            }else{
                $this->session->set_flashdata('limit_reached', $this->lang->line('text_reach_limit_goods'));
                redirect('/goods/insert', 'refresh');
            }
        }
        $this->getForm();
    }

    public function update($goods_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'goods/update/'.$goods_id;

        //I took out the check_space because some goods may have spaces? - Joe
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerGoods($goods_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                if($this->User_model->getClientId()){
                    $this->Goods_model->editGoodsToClient($goods_id, $this->input->post());
                }else{
                    $this->Goods_model->editGoods($goods_id, $this->input->post());

                    $this->Goods_model->editGoodsToClientFromAdmin($goods_id, $this->input->post());
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));

                redirect('/goods', 'refresh');
            }
        }

        $this->getForm($goods_id);
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $goods_id) {
                if($this->checkOwnerGoods($goods_id)){

                    if($this->User_model->getClientId()){
                        $this->Goods_model->deleteGoodsClient($goods_id);
                    }else{
                        $this->Goods_model->deleteGoods($goods_id);
                    }

                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/goods', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('goods/page');


        $this->load->model('Goods_model');
        $this->load->model('Image_model');

        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['goods_list'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->getUserGroupId() == $setting_group_id) {
            $data['limit'] = $per_page;
            $data['start'] = $offset;
            $data['sort'] = 'sort_order';

            $results = $this->Goods_model->getGoodsList($data);

            $goods_total = $this->Goods_model->getTotalGoods($data);

            foreach ($results as $result) {

                if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }
                $goodsIsPublic = $this->checkGoodsIsPublic($result['_id']);
                $this->data['goods_list'][] = array(
                    'goods_id' => $result['_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order'  => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'], $this->input->post('selected'))),
                    'is_public'=>$goodsIsPublic
                );
            }
        }else{
            $this->load->model('Reward_model');

            $goods_data = array('site_id'=> $site_id, 'limit'=> $per_page, 'start' =>$offset, 'sort'=>'sort_order');

            $goods_list = $this->Goods_model->getGoodsBySiteId($goods_data);

            $reward_limit_data = $this->Reward_model->getGoodsRewardBySiteId($site_id);

            $goods_total = $this->Goods_model->getTotalGoodsBySiteId($goods_data);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data[0]['limit'] - $goods_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 50, 50);

                foreach ($goods_list as $goods) {

                    $goods_info = $this->Goods_model->getGoodsToClient($goods['_id']);

                    if($goods_info){

                        if ($goods_info['image'] && (S3_IMAGE . $goods_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $goods_info['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $image = $this->Image_model->resize($goods_info['image'], 50, 50);
                        }
                        else {
                            $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                        }

                        if(!$goods_info['deleted']){
                            $this->data['goods_list'][] = array(
                                'goods_id' => $goods_info['_id'],
                                'name' => $goods_info['name'],
                                'hint' => $goods_info['hint'],
                                'quantity' => $goods_info['quantity'],
                                'status' => $goods_info['status'],
                                'image' => $image,
                                'sort_order'  => $goods_info['sort_order'],
                                'selected' => ($this->input->post('selected') && in_array($goods_info['_id'], $this->input->post('selected'))),
                            );
                        }

                    }
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

        $config['total_rows'] = $goods_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['main'] = 'goods';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getListForAjax($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('goods/page');


        $this->load->model('Goods_model');
        $this->load->model('Image_model');

        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['goods_list'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->getUserGroupId() == $setting_group_id) {

            $data['limit'] = $per_page;
            $data['start'] = $offset;
            $data['sort'] = 'sort_order';

            $results = $this->Goods_model->getGoods($data);

            $goods_total = $this->Goods_model->getTotalGoods($data);

            foreach ($results as $result) {

                if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }
                $goodsIsPublic = $this->checkGoodsIsPublic($result['_id']);
                $this->data['goods_list'][] = array(
                    'goods_id' => $result['_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order'  => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'], $this->input->post('selected'))),
                    'is_public' => $goodsIsPublic
                );
            }
        }
        else {

            $this->load->model('Reward_model');

            $goods_data = array('site_id'=>$site_id, 'limit'=>$per_page, 'start' => $offset, 'sort'=>'sort_order');

            $goods_list = $this->Goods_model->getGoodsBySiteId($goods_data);

            $reward_limit_data = $this->Reward_model->getGoodsRewardBySiteId($site_id);

            $goods_total = $this->Goods_model->getTotalGoodsBySiteId($goods_data);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data[0]['limit'] - $goods_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 50, 50);

                foreach ($goods_list as $goods) {

                    $goods_info = $this->Goods_model->getGoodsToClient($goods['_id']);

                    if($goods_info){

                        if ($goods_info['image'] && (S3_IMAGE . $goods_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $goods_info['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $image = $this->Image_model->resize($goods_info['image'], 50, 50);
                        }
                        else {
                            $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                        }

                        if(!$goods_info['deleted']){
                            $this->data['goods_list'][] = array(
                                'goods_id' => $goods_info['_id'],
                                'name' => $goods_info['name'],
                                'hint' => $goods_info['hint'],
                                'quantity' => $goods_info['quantity'],
                                'status' => $goods_info['status'],
                                'image' => $image,
                                'sort_order'  => $goods_info['sort_order'],
                                'selected' => ($this->input->post('selected') && in_array($goods_info['_id'], $this->input->post('selected'))),
                            );
                        }
                    }
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

        $config['total_rows'] = $goods_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['main'] = 'goods';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('goods_ajax');
    }

    private function getForm($goods_id=null) {

        $this->load->model('Image_model');

        if (isset($goods_id) && ($goods_id != 0)) {
            if($this->User_model->getClientId()){
                $goods_info = $this->Goods_model->getGoodsToClient($goods_id);
            }else{
                $goods_info = $this->Goods_model->getGoods($goods_id);
            }

        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($goods_id) && ($goods_id != 0)) {
            $this->data['name'] = $goods_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('description')) {
            $this->data['description'] = $this->input->post('description');
        } elseif (isset($goods_id) && ($goods_id != 0)) {
            $this->data['description'] = $goods_info['description'];
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('hint')) {
            $this->data['hint'] = $this->input->post('hint');
        } elseif (isset($goods_id) && ($goods_id != 0)) {
            $this->data['hint'] = $goods_info['hint'];
        } else {
            $this->data['hint'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($goods_info)) {
            $this->data['image'] = $goods_info['image'];
        } else {
            $this->data['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($goods_info) && $goods_info['image'] && (S3_IMAGE . $goods_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $goods_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($goods_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 100, 100);

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($goods_info)) {
            $this->data['sort_order'] = $goods_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($goods_info)) {
            $this->data['status'] = $goods_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if ($this->input->post('stackable')) {
            $this->data['stackable'] = $this->input->post('stackable');
        } elseif (!empty($goods_info)) {
            $this->data['stackable'] = $goods_info['stackable'];
        } else {
            $this->data['stackable'] = 1;
        }

        if ($this->input->post('substract')) {
            $this->data['substract'] = $this->input->post('substract');
        } elseif (!empty($goods_info)) {
            $this->data['substract'] = $goods_info['substract'];
        } else {
            $this->data['substract'] = 1;
        }

        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (!empty($goods_info)) {
            $this->data['quantity'] = $goods_info['quantity'];
        } else {
            $this->data['quantity'] = 1;
        }

        if (isset($goods_id)) {
            $this->data['goods_id'] = $goods_id;
        } else {
            $this->data['goods_id'] = null;
        }

        $this->load->model('Client_model');
        $this->data['to_clients'] = $this->Client_model->getClients($data = array());
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'goods_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'goods')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkLimitGoods(){

        if(isset($client)){

            $error = null;

            if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

                $this->load->model('Reward_model');

                $plan_limit = $this->Reward_model->getRewardByClientId($this->User_model->getClientId());

                $goods_list_count = $this->Goods_model->getTotalGoodsBySiteId($this->User_model->getSiteId());

                foreach ($plan_limit as $plan) {
                    if($plan['site_id'] == $this->input->post('site_id')){
                        if($plan['name'] == 'goods'){
                            if($plan['limit']){
                                $limit_goods =  $plan['limit'];
                            }
                        }
                    }
                }

                if(isset($limit_goods)){
                    if($goods_list_count >= $limit_goods){
                        $over_limit = true;
                    }else{
                        $over_limit = false;
                    }
                }else{
                    $over_limit = false;
                }
            }

            if(!$over_limit){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }

    }

    private function checkOwnerGoods($goodsId){

        $error = null;

        if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){

            $goods_data = array('site_id'=>$this->User_model->getSiteId());

            $goods_list = $this->Goods_model->getGoodsBySiteId($goods_data);
            $has = false;

            foreach ($goods_list as $goods) {
                if($goods['_id']."" == $goodsId.""){
                    $has = true;
                }
            }

            if(!$has){
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess(){
        if ($this->User_model->hasPermission('access', 'goods')) {
            return true;
        } else {
            return false;
        }
    }

    public function increase_order($goods_id){

        if($this->User_model->getClientId()){
            $this->Goods_model->increaseOrderByOneClient($goods_id);
        }else{
            $this->Goods_model->increaseOrderByOne($goods_id);
        }

        $json = array('success'=>'Okay increase!');

        $this->output->set_output(json_encode($json));
    }

    public function decrease_order($goods_id){

        if($this->User_model->getClientId()){
            $this->Goods_model->decreaseOrderByOneClient($goods_id);
        }else{
            $this->Goods_model->decreaseOrderByOne($goods_id);
        }

        $json = array('success'=>'Okay decrease!');

        $this->output->set_output(json_encode($json));
    }

    private function vsort (&$array, $key, $order='asc') {
        $res=array();
        $sort=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sort[$ii]=$va[$key];
        }
        if(strtolower($order) == 'asc'){
            asort($sort);
        }else{
            arsort($sort);
        }
        foreach ($sort as $ii => $va) {
            $res[$ii]=$array[$ii];
        }
        $array=$res;
    }

    public function checkGoodsIsPublic($goods_id){
        $allGoodsFromClients = $this->Goods_model->checkGoodsIsPublic($goods_id);

        if(isset($allGoodsFromClients[0]['client_id'])){
            $firstGoods = $allGoodsFromClients[0]['client_id'];
            foreach($allGoodsFromClients as $goods){
                if($goods['client_id'] != $firstGoods){
                    return true;
                }
            }
            return false;
        }else{
            return true;
        }
    }

}