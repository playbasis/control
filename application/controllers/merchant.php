<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Merchant extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('goods_model');
        $this->load->model('merchant_model');
        $this->load->model('reward_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function availableBranchGoodsGroup_get()
    {
        $required = $this->input->checkParam(array(
            'goods_group',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $goods_group = $this->input->get('goods_group');

        $result = $this->merchant_model->getMerchantGoodsGroups($client_id,$site_id,$goods_group);
        if(isset($result[0]['branches_allow'])) $result = $result[0]['branches_allow'];

        $this->response($this->resp->setRespond($result), 200);
    }

}