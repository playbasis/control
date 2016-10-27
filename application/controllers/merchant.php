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

        $result = $this->merchant_model->getAvailableBranchByGoodsGroup($client_id,$site_id,$goods_group);
        if($result && is_array($result)) foreach ($result as $index_m => $merchant){
            $result[$index_m]['branch'] =  $merchant['branch'][0];
            $merchant_data = $this->merchant_model->getMerchantById($client_id, $site_id, $merchant['_id']);
            $result[$index_m]['merchant']['id'] = $merchant['_id']."";
            $result[$index_m]['merchant']['name'] = $merchant_data['name'];
            unset($result[$index_m]['_id']);
        }
        if($result && is_array($result)) foreach ($result as $index_m => $merchant){
            foreach ($merchant['branch'] as $index_r => $res) {
                $result[$index_m]['branch'][$index_r]['b_id'] = $res['b_id'] . "";
            }
        }

        $this->response($this->resp->setRespond($result), 200);
    }

    public function GoodsGroupVerify_get()
    {
        $required = $this->input->checkParam(array(
            'goods_group',
            'coupon_code',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $group = $this->input->get('goods_group');
        $code = $this->input->get('coupon_code');
        $pin_code = $this->input->get('pin_code');
        $cl_player_id = $this->input->get('player_id');

        $goods_info = $this->goods_model->getGoodsByGroupAndCode($client_id, $site_id, $group, $code);

        if (!$goods_info) {
            $this->response($this->error->setError('REDEEM_INVALID_COUPON_CODE'), 200);
        }
        $goods_id = new MongoId($goods_info['goods_id']);

        if (!$cl_player_id){
            $player_id = $this->player_model->getPbAndCilentIdByGoodsId($this->validToken, $goods_id);
            $pb_player_id = $player_id['pb_player_id'];
            $cl_player_id = $player_id['cl_player_id'];
        } else {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'cl_player_id' => $cl_player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }
        $player_goods = $this->player_model->getGoodsByGoodsId($pb_player_id, $site_id, $goods_id);

        if (!empty($player_goods)) {
            if($player_goods['amount'] < 1){
                $this->response($this->error->setError('REDEEM_COUPON_CODE_USED'), 200);
            } else {
                if($pin_code){
                    // Get all branches that are allowed to verify the goods group
                    $merchantGoodsGroups = $this->merchant_model->getMerchantGoodsGroups($client_id, $site_id, $player_goods['group']);
                    if (empty($merchantGoodsGroups)) {
                        $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                    }
                    $branches_allow = array();
                    foreach ($merchantGoodsGroups as $merchantGoodsGroup) {
                        foreach ($merchantGoodsGroup['branches_allow'] as $branch) {
                            array_push($branches_allow, $branch['b_id']);
                        }
                    }

                    // Check whether the input pin_code is match to any branches in $branches_allow
                    // if not found mean that the branch's pin_code cannot verify this goods.
                    $branch = $this->merchant_model->getMerchantBranchByBranchesAndPinCode($client_id, $site_id, $branches_allow, $pin_code);

                    if (!empty($branch)) {
                        $branch_log_data['b_id'] = $branch['_id'];
                        $branch_log_data['b_name'] = $branch['branch_name'];
                        $this->response($this->resp->setRespond('REDEEM_GOODS_IS_AVAILABLE_FROM_THIS_BRANCH'), 200);
                    } else {
                        $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                    }
                } else {
                    $this->response($this->resp->setRespond('REDEEM_GOODS_IS_AVAILABLE'), 200);
                }
            }
        } else {
            $this->response($this->error->setError('REDEEM_GOODS_NOT_AVAILABLE'), 200);
        }
    }

    public function GoodsGroupRedeem_post()
    {
        $required = $this->input->checkParam(array(
            'goods_group',
            'coupon_code',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $group = $this->input->post('goods_group');
        $code = $this->input->post('coupon_code');
        $pin_code = $this->input->post('pin_code');
        $cl_player_id = $this->input->post('player_id');

        $goods_info = $this->goods_model->getGoodsByGroupAndCode($client_id, $site_id, $group, $code, array('goods_id'), true);

        if (!$goods_info) {
            $this->response($this->error->setError('REDEEM_INVALID_COUPON_CODE'), 200);
        }

        $goods_list = array();
        if(is_array($goods_info)) foreach ($goods_info as $good){
            array_push($goods_list, new MongoId($good['goods_id']));
        }

        if (!$cl_player_id){
            //workaround for case same coupon code in group
            $player_id = $this->player_model->getPbAndCilentIdByGoodsId($this->validToken, false, $goods_list,true);
            if(!$player_id){
                $player_id = $this->player_model->getPbAndCilentIdByGoodsId($this->validToken, false, $goods_list);
            }
            $pb_player_id = $player_id['pb_player_id'];
            $cl_player_id = $player_id['cl_player_id'];
        } else {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'cl_player_id' => $cl_player_id,
            ));
            if (!$pb_player_id) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        //workaround for case same coupon code in group
        $player_goods = $this->player_model->getGoodsByGoodsId($pb_player_id, $site_id, false, $goods_list, true);
        if (!$player_goods) {
            $player_goods = $this->player_model->getGoodsByGoodsId($pb_player_id, $site_id, false, $goods_list);
        }

        if (!empty($player_goods)) {
            if($player_goods['amount'] < 1){
                $this->response($this->error->setError('REDEEM_COUPON_CODE_USED'), 200);
            } else {
                if($pin_code){
                    // Get all branches that are allowed to verify the goods group
                    $merchantGoodsGroups = $this->merchant_model->getMerchantGoodsGroups($client_id, $site_id, $player_goods['group']);
                    if (empty($merchantGoodsGroups)) {
                        $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                    }
                    $branches_allow = array();
                    foreach ($merchantGoodsGroups as $merchantGoodsGroup) {
                        foreach ($merchantGoodsGroup['branches_allow'] as $branch) {
                            array_push($branches_allow, $branch['b_id']);
                        }
                    }

                    // Check whether the input pin_code is match to any branches in $branches_allow
                    // if not found mean that the branch's pin_code cannot verify this goods.
                    $branch = $this->merchant_model->getMerchantBranchByBranchesAndPinCode($client_id, $site_id, $branches_allow, $pin_code);

                    if (!empty($branch)) {
                        $branch_log_data['b_id'] = $branch['_id'];
                        $branch_log_data['b_name'] = $branch['branch_name'];

                        $log_result = $this->merchant_model->logMerchantRedeem($client_id, $site_id, new MongoId($player_goods['goods_id']), $player_goods['group'], $cl_player_id, $pb_player_id, $branch_log_data);
                        $result = $this->merchant_model->getMerchantRedeemLogByLogId($client_id, $site_id, new MongoId($log_result));
                        if ($result) {
                            $this->player_model->markUsedGoodsFromPlayer($client_id, $site_id, $pb_player_id, new MongoId($player_goods['goods_id']));
                        }
                        $this->response($this->resp->setRespond(array("success" => true)), 200);
                    } else {
                        $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                    }
                } else {
                    $this->player_model->markUsedGoodsFromPlayer($client_id, $site_id, $pb_player_id, new MongoId($player_goods['goods_id']));
                    $this->response($this->resp->setRespond(array("success" => true)), 200);
                }
            }
        } else {
            $this->response($this->error->setError('REDEEM_GOODS_NOT_AVAILABLE'), 200);
        }
    }
}