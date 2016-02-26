<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

define('MAX_REDEEM_TRIES', 5);

class Redeem extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('goods_model');
        $this->load->model('player_model');
        $this->load->model('redeem_model');
        $this->load->model('sms_model');
        $this->load->model('merchant_model');
        $this->load->model('store_org_model');
        $this->load->model('client_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('tracker_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function goods_post($option = 0)
    {
        $this->benchmark->mark('goods_redeem_start');

        $required = $this->input->checkParam(array(
            'player_id',
            'goods_id'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $org_list = $this->store_org_model->retrieveNodeByPBPlayerID($this->client_id, $this->site_id, $pb_player_id);

        $org_id_list = array();
        if (is_array($org_list)) {
            foreach ($org_list as $node) {
                $org_info = $this->store_org_model->getOrgInfoOfNode($this->client_id, $this->site_id,
                    $node['node_id']);
                $a = array((string)$org_info[0]['organize'] => isset($node['roles']) ? $node['roles'] : array());
                $org_id_list = array_merge($org_id_list, $a);
            }
        }

        $goods_id = $this->input->post('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => new MongoId($goods_id)
        )));

        if (isset($goods['organize_id'])) {
            if ((!array_key_exists((string)$goods['organize_id'], $org_id_list)
                || ((isset($goods['organize_role']) && $goods['organize_role'] != "")
                    && !array_key_exists($goods['organize_role'],
                        $org_id_list[(string)$goods['organize_id']])))
            ) {
                $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
            }
        }
        $amount = $this->input->post('amount') ? (int)$this->input->post('amount') : 1;

        $redeemResult = null;
        try {
            $redeemResult = $this->redeem($validToken['site_id'], $pb_player_id, $goods, $amount, $validToken);
            if (isset($redeemResult['events'][0]['event_type']) && ($redeemResult['events'][0]['event_type'] != 'GOODS_RECEIVED')) {
                $msg = $redeemResult['events'][0]['event_type'];
                switch ($msg) {
                    case 'GOODS_NOT_AVAILABLE':
                        $this->response($this->error->setError('REDEEM_GOODS_NOT_AVAILABLE'), 200);
                        break;
                    case 'GOODS_NOT_ENOUGH':
                        $this->response($this->error->setError('REDEEM_GOODS_NOT_ENOUGH'), 200);
                        break;
                    case 'POINT_NOT_ENOUGH':
                        $this->response($this->error->setError('REDEEM_POINT_NOT_ENOUGH'), 200);
                        break;
                    case 'BADGE_NOT_ENOUGH':
                        $this->response($this->error->setError('REDEEM_BADGE_NOT_ENOUGH'), 200);
                        break;
                    case 'CUSTOM_POINT_NOT_ENOUGH':
                        $reward_id = key($redeemResult['events'][0]['incomplete'][0]);
                        $reward_name = $this->client_model->getRewardName(array(
                            'client_id' => $validToken['client_id'],
                            'site_id' => $validToken['site_id'],
                            'reward_id' => $reward_id
                        ));
                        $this->response($this->error->setError('REDEEM_CUSTOM_POINT_NOT_ENOUGH', $reward_name), 200);
                        break;
                }

            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            switch ($msg) {
                case 'GOODS_NOT_FOUND':
                    $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
                    break;
                case 'OVER_LIMIT_REDEEM':
                    $this->response($this->error->setError('OVER_LIMIT_REDEEM'), 200);
                    break;
            }
        }

        $this->benchmark->mark('goods_redeem_end');
        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
        $this->response($this->resp->setRespond($redeemResult), 200);
    }

    public function sponsor_post($option = 0)
    {
        $this->benchmark->mark('goods_redeem_start');

        $required = $this->input->checkParam(array(
            'player_id',
            'goods_id'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $goods_id = $this->input->post('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => new MongoId($goods_id)
        )), true);

        $amount = $this->input->post('amount') ? (int)$this->input->post('amount') : 1;

        $redeemResult = null;
        try {
            $redeemResult = $this->redeem($validToken['site_id'], $pb_player_id, $goods, $amount, $validToken, true);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            switch ($msg) {
                case 'GOODS_NOT_FOUND':
                    $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
                    break;
                case 'OVER_LIMIT_REDEEM':
                    $this->response($this->error->setError('OVER_LIMIT_REDEEM'), 200);
                    break;
            }
        }

        $this->benchmark->mark('goods_redeem_end');
        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
        $this->response($this->resp->setRespond($redeemResult), 200);
    }

    public function goodsGroup_get()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'group'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->get('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $group = $this->input->get('group');

        $amount = $this->input->get('amount') ? (int)$this->input->get('amount') : 1;

        $n = $this->goods_model->countGoodsByGroup($this->validToken['client_id'], $this->validToken['site_id'], $group,
            $pb_player_id, $amount);

        $this->response($this->resp->setRespond($n), 200);
    }

    public function sponsorGroup_get()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'group'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->get('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $group = $this->input->get('group');

        $amount = $this->input->get('amount') ? (int)$this->input->get('amount') : 1;

        $n = $this->goods_model->countGoodsByGroup($this->validToken['client_id'], $this->validToken['site_id'], $group,
            $pb_player_id, $amount, true);

        $this->response($this->resp->setRespond($n), 200);
    }

    public function goodsGroup_post()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'group'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $group = $this->input->post('group');

        $amount = $this->input->post('amount') ? (int)$this->input->post('amount') : 1;

        $org_list = $this->store_org_model->retrieveNodeByPBPlayerID($this->client_id, $this->site_id, $pb_player_id);
        $org_id_list = array();
        if (is_array($org_list)) {
            foreach ($org_list as $node) {
                $org_info = $this->store_org_model->getOrgInfoOfNode($this->client_id, $this->site_id,
                    $node['node_id']);
                $a = array((string)$org_info[0]['organize'] => isset($node['roles']) ? $node['roles'] : array());
                $org_id_list = array_merge($org_id_list, $a);
            }
        }

        $goods = $this->goods_model->getGoodsByGroupAndPlayerId($this->validToken['client_id'],
            $this->validToken['site_id'], $group, $pb_player_id, $amount);
        if ($goods) {
            if (isset($goods['organize_id'])) {
                if ((!array_key_exists((string)$goods['organize_id'], $org_id_list)
                    || ((isset($goods['organize_role']) && $goods['organize_role'] != "")
                        && !array_key_exists($goods['organize_role'],
                            $org_id_list[(string)$goods['organize_id']])))
                ) {
                    $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
                }
            }
            for ($i = 0; $i < MAX_REDEEM_TRIES; $i++) { // try to redeem for a few times before giving up
                log_message('debug', 'random = ' . $goods['goods_id']);
                /* actual redemption */
                try {
                    $redeemResult = $this->redeem($validToken['site_id'], $pb_player_id, $goods, $amount, $validToken,
                        false);
                    $this->response($this->resp->setRespond($redeemResult), 200);
                } catch (Exception $e) {
                    if ($e->getMessage() == 'OVER_LIMIT_REDEEM') {
                        continue;
                    } // this goods_id has been assigned to this player too often!, try next one
                    else {
                        if ($e->getMessage() == 'GOODS_NOT_ENOUGH') {
                            continue;
                        } // there may be a collision, try next one
                        else {
                            if ($e->getMessage() == 'GOODS_NOT_FOUND') {
                                continue;
                            } // this should not happen, but if this is the case, then try next one
                            else {
                                $this->response($this->error->setError(
                                    "INTERNAL_ERROR", array()), 200);
                            }
                        }
                    }
                }
                $goods = $this->goods_model->getGoodsByGroupAndPlayerId($this->validToken['client_id'],
                    $this->validToken['site_id'], $group, $pb_player_id, $amount);
            }
        }
        $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
    }

    public function merchantGoodsGroup_post()
    {
        $required = $this->input->checkParam(array(
            'goods_group',
            'coupon_code',
            'pin_code'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $group = $this->input->post('goods_group');
        $code = $this->input->post('coupon_code');
        $goods_info = $this->goods_model->getGoodsByGroupAndCode($this->client_id, $this->site_id, $group, $code);

        if (!$goods_info) {
            $this->response($this->error->setError('REDEEM_INVALID_COUPON_CODE'), 200);
        }
        $goods_id = $goods_info['goods_id'];

        $merchantRedeem = $this->merchant_model->getMerchantRedeemLogByGoodsId($this->validToken['client_id'],
            $this->validToken['site_id'], new MongoId($goods_id));

        if (empty($merchantRedeem)) {

            $validToken = $this->validToken;
            $player_id = $this->player_model->getPbAndCilentIdByGoodsId($validToken, new MongoId($goods_id));

            $pb_player_id = $player_id['pb_player_id'];
            $cl_player_id = $player_id['cl_player_id'];

            if (!$pb_player_id) {
                // Goods is not yet redeemed
                $this->response($this->error->setError('GOODS_IS_NOT_REDEEMED'), 200);
            }

            $goods = $this->player_model->getGoodsByGoodsId($pb_player_id, $this->validToken['site_id'],
                new MongoId($goods_id));
            if (!empty($goods)) {
                // Get all branches that are allowed to verify the goods group
                $merchantGoodsGroups = $this->merchant_model->getMerchantGoodsGroups($this->validToken['client_id'],
                    $this->validToken['site_id'], $goods['group']);
                if (empty($merchantGoodsGroups)) {
                    $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                }
                $branches_allow = array();
                foreach ($merchantGoodsGroups as $merchantGoodsGroup) {
                    foreach ($merchantGoodsGroup['branches_allow'] as $branch) {
                        array_push($branches_allow, $branch['b_id']);
                    }
                }
                $pin_code = $this->input->post('pin_code');
                // Check whether the input pin_code is match to any branches in $branches_allow
                // if not found mean that the branch's pin_code cannot verify this goods.
                $branch = $this->merchant_model->getMerchantBranchByBranchesAndPinCode($this->validToken['client_id'],
                    $this->validToken['site_id'], $branches_allow, $pin_code);

                if (!empty($branch)) {
                    $branch_log_data['b_id'] = $branch['_id'];
                    $branch_log_data['b_name'] = $branch['branch_name'];

                    $log_result = $this->merchant_model->logMerchantRedeem($this->validToken['client_id'],
                        $this->validToken['site_id'], new MongoId($goods_id), $goods['group'], $cl_player_id,
                        $pb_player_id, $branch_log_data);

                    $result = $this->merchant_model->getMerchantRedeemLogByLogId($this->validToken['client_id'],
                        $this->validToken['site_id'], new MongoId($log_result));
                    if ($result) {
                        $this->player_model->deleteGoodsFromPlayer($this->validToken['client_id'],
                            $this->validToken['site_id'], $pb_player_id, new MongoId($goods_id));
                    }

                    $this->response($this->resp->setRespond(array("success" => true)), 200);
                } else {
                    $this->response($this->error->setError('BRANCH_IS_NOT_ALLOW_TO_VERIFY_GOODS'), 200);
                }
            } else {
                $this->response($this->error->setError('GOODS_IS_NOT_REDEEMED'), 200);
            }
        } else {
            $resp['success'] = false;

            $details['verified_at'] = $merchantRedeem['branch']['b_name'];
            $details['verified_date'] = date('r e', $merchantRedeem['date_added']->sec);
            $resp['details'] = $details;
            $this->response($this->resp->setRespond($resp), 200);
        }

    }

    public function sponsorGroup_post()
    {
        $required = $this->input->checkParam(array(
            'player_id',
            'group'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $group = $this->input->post('group');

        $amount = $this->input->post('amount') ? (int)$this->input->post('amount') : 1;

        $goods = $this->goods_model->getGoodsByGroupAndPlayerId($this->validToken['client_id'],
            $this->validToken['site_id'], $group, $pb_player_id, $amount);
        if ($goods) {
            for ($i = 0; $i < MAX_REDEEM_TRIES; $i++) { // try to redeem for a few times before giving up
                log_message('debug', 'random = ' . $goods['goods_id']);
                /* actual redemption */
                try {
                    $redeemResult = $this->redeem($validToken['site_id'], $pb_player_id, $goods, $amount, $validToken,
                        false, true);
                    $this->response($this->resp->setRespond($redeemResult), 200);
                } catch (Exception $e) {
                    if ($e->getMessage() == 'OVER_LIMIT_REDEEM') {
                        continue;
                    } // this goods_id has been assigned to this player too often!, try next one
                    else {
                        if ($e->getMessage() == 'GOODS_NOT_ENOUGH') {
                            continue;
                        } // there may be a collision, try next one
                        else {
                            if ($e->getMessage() == 'GOODS_NOT_FOUND') {
                                continue;
                            } // this should not happen, but if this is the case, then try next one
                            else {
                                $this->response($this->error->setError(
                                    "INTERNAL_ERROR", array()), 200);
                            }
                        }
                    }
                }
                $goods = $this->goods_model->getGoodsByGroupAndPlayerId($this->validToken['client_id'],
                    $this->validToken['site_id'], $group, $pb_player_id, $amount);
            }
        }
        $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
    }

    private function redeem(
        $site_id,
        $pb_player_id,
        $goods,
        $amount,
        $validToken,
        $validate = true,
        $is_sponsor = false
    ) {
        if (!$goods) {
            throw new Exception('GOODS_NOT_FOUND');
        }

        if ($goods['per_user'] !== null) {
            $get_player_goods = $this->player_model->getGoodsByGoodsId($pb_player_id, $is_sponsor ? null : $site_id,
                $goods['goods_id']);
            if (isset($get_player_goods['amount']) && $get_player_goods['amount'] >= $goods['per_user']) {
                throw new Exception('OVER_LIMIT_REDEEM');
            }
        }

        return $this->processRedeem($pb_player_id, $goods, $amount, $validToken, $validate, $is_sponsor);
    }

    private function processRedeem($pb_player_id, $goods, $amount, $validToken, $validate = true, $is_sponsor = false)
    {
        $redeemResult = array(
            'events' => array()
        );
        if ($validate && !$this->checkGoodsTime($goods)) {
            $event = array(
                'event_type' => 'GOODS_NOT_AVAILABLE',
                'message' => 'goods not available on now'
            );
            array_push($redeemResult['events'], $event);
        }
        if ($validate && !$this->checkGoodsAmount($goods, $amount)) {
            $event = array(
                'event_type' => 'GOODS_NOT_ENOUGH',
                'message' => 'goods not enough for redeem'
            );
            array_push($redeemResult['events'], $event);
        }

        if ($validate && isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)) {
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);
            $player_point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
            if (isset($player_point[0]['value'])) {
                $player_point = $player_point[0]['value'];
            } else {
                $player_point = 0;
            }

            if ((int)($player_point * $amount) < (int)($goods['redeem']['point']["point_value"] * $amount)) {
                $event = array(
                    'event_type' => 'POINT_NOT_ENOUGH',
                    'message' => 'user point not enough',
                    'incomplete' => (int)($goods['redeem']['point']["point_value"] * $amount) - (int)($player_point[0]['value'] * $amount)
                );
                array_push($redeemResult['events'], $event);
            }

        }

        if ($validate && isset($goods['redeem']['badge'])) {
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            $badge_redeem_check = count($goods['redeem']['badge']);
            $badge_can_redeem = 0;
            $badge_incomplete = array();

            if ($player_badges) {
                $badge_player_check = array();
                foreach ($player_badges as $b) {
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                foreach ($goods['redeem']['badge'] as $badgeobj) {
                    if (isset($badge_player_check[$badgeobj["badge_id"]]) && (int)($badge_player_check[$badgeobj["badge_id"]] * $amount) >= (int)($badgeobj["badge_value"] * $amount)) {
                        $badge_can_redeem++;
                    } else {
                        array_push($badge_incomplete,
                            array($badgeobj["badge_id"] . "" => (isset($badge_player_check[$badgeobj["badge_id"]])) ? ((int)($badgeobj["badge_value"] * $amount) - (int)($badge_player_check[$badgeobj["badge_id"]] * $amount)) : (int)($badgeobj["badge_value"] * $amount)));
                    }
                }
            }

            if ((int)$badge_redeem_check > (int)$badge_can_redeem) {
                $event = array(
                    'event_type' => 'BADGE_NOT_ENOUGH',
                    'message' => 'user badge not enough',
                    'incomplete' => $badge_incomplete
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if ($validate && isset($goods['redeem']['custom'])) {

            $custom_redeem_check = count($goods['redeem']['custom']);
            $custom_can_redeem = 0;
            $custom_incomplete = array();

            foreach ($goods['redeem']['custom'] as $customobj) {

                $customid = new MongoId($customobj["custom_id"]);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                if ($player_custom && (int)($player_custom[0]['value'] * $amount) >= (int)($customobj["custom_value"] * $amount)) {
                    $custom_can_redeem++;
                } else {
                    array_push($custom_incomplete,
                        array($customid . "" => ($player_custom) ? ((int)($customobj["custom_value"] * $amount) - (int)($player_custom[0]['value'] * $amount)) : (int)($customobj["custom_value"] * $amount)));
                }
            }

            if ((int)$custom_redeem_check > (int)$custom_can_redeem) {
                $event = array(
                    'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                    'message' => 'user custom point not enough',
                    'incomplete' => $custom_incomplete
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if (!(isset($redeemResult['events']) && count($redeemResult['events']) > 0)) {
            $validToken_ad = array('client_id' => null, 'site_id' => null);
            /* re-fetch the goods given goods_id */
            $goodsData = $this->goods_model->getGoods(array_merge($is_sponsor ? $validToken_ad : $validToken, array(
                    'goods_id' => new MongoId($goods['goods_id'])
                ))
            );
            if (!$goodsData) {
                log_message('error', 'Cannot find goods using goods_id = ' . $goods['goods_id']);
                return false;
            }

            try {
                /* check limit of redeem according to their plan */
                $this->client_model->permissionProcess(
                    $this->client_data,
                    $this->client_id,
                    $this->site_id,
                    "others",
                    "redeem",
                    $amount
                );

                /* give goods reward, if exists */
                $this->getRedeemGoods($pb_player_id, $goodsData, $amount, $validToken, $is_sponsor);
                $event = array(
                    'event_type' => 'GOODS_RECEIVED',
                    'goods_data' => $goodsData,
                    'value' => $amount
                );
                array_push($redeemResult['events'], $event);

                /* obtain coupon code */
                $log_id = $this->redeem_model->exerciseCode('goods', $validToken['client_id'], $validToken['site_id'],
                    $pb_player_id, array_key_exists('code', $goodsData) ? $goodsData['code'] : null);
                $redeemResult = array_merge($redeemResult, array('log_id' => $log_id->{'$id'}));

                // publish to node stream
                $eventMessage = $this->utility->getEventMessage('goods', '', '', '', '', '', $goodsData['name']);
                $validToken = array_merge($validToken, array(
                    'pb_player_id' => $pb_player_id,
                    'goods_id' => new MongoId($goodsData['goods_id']),
                    'goods_name' => $goodsData['name'],
                    'is_sponsor' => $is_sponsor,
                    'amount' => $amount,
                    'redeem' => $goodsData['redeem'],
                    'action_name' => 'redeem_goods',
                    'action_icon' => 'fa-icon-shopping-cart',
                    'message' => $eventMessage
                ));

                // log event - goods
                $this->tracker_model->trackGoods($validToken);

                $this->node->publish(array_merge($validToken, array(
                    'action_name' => 'redeem_goods',
                    'action_icon' => 'fa-gift',
                    'message' => $eventMessage,
                    'goods' => $event['goods_data']
                )), $validToken['domain_name'], $validToken['site_id']);
            } catch (Exception $e) {
                if ($e->getMessage() == "LIMIT_EXCEED") {
                    $this->response($this->error->setError(
                        "LIMIT_EXCEED", array()), 200);
                } else {
                    log_message('error', '[processRedeem] error = ' . $e->getMessage());
                    $this->response($this->error->setError(
                        "INTERNAL_ERROR", array()), 200);
                }
            }
        }

        return $redeemResult;
    }

    private function checkGoodsTime($goods)
    {
        if (isset($goods['date_start']) && $goods['date_start']) {
            $datetimecheck = new DateTime('now');
            $datetimestart = new DateTime($goods['date_start']);
            if ($datetimecheck < $datetimestart) {
                return false;
            }
        }
        if (isset($goods['date_expire']) && $goods['date_expire']) {
            $datetimecheck = new DateTime('now');
            $datetimeexpire = new DateTime($goods['date_expire']);
            if ($datetimecheck > $datetimeexpire) {
                return false;
            }
        }
        return true;
    }

    private function checkGoodsAmount($goods, $amount)
    {
        if (!isset($goods['quantity']) || is_null($goods['quantity'])) {
            return true;
        }
        return (int)$goods['quantity'] >= (int)$amount;
    }

    private function getRedeemGoods($pb_player_id, $goods, $amount, $validToken, $is_sponsor)
    {
        $this->load->model('client_model');

        $goods_id = new MongoId($goods['goods_id']);
        $this->client_model->updateplayerGoods($goods_id, $amount, $pb_player_id, $validToken['cl_player_id'],
            $validToken['client_id'], $validToken['site_id'], $is_sponsor);

        if (isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)) {
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);
            $reward_id = new MongoId($reward_id);
            $player_point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
            if ((int)$player_point[0]['value'] * $amount >= (int)$goods['redeem']['point']["point_value"] * $amount) {
                $this->client_model->updatePlayerPointReward($reward_id,
                    (-1 * $goods['redeem']['point']["point_value"] * $amount), $pb_player_id,
                    $validToken['cl_player_id'], $validToken['client_id'], $validToken['site_id']);
            }
        }

        if (isset($goods['redeem']['badge'])) {
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            if ($player_badges) {
                $badge_player_check = array();
                foreach ($player_badges as $b) {
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                foreach ($goods['redeem']['badge'] as $badgeobj) {
                    if (isset($badge_player_check[$badgeobj["badge_id"]]) && ($badge_player_check[$badgeobj["badge_id"]] * $amount) >= ($badgeobj["badge_value"] * $amount)) {
                        $badgeid = new MongoId($badgeobj["badge_id"]);
                        $this->client_model->updateplayerBadge($badgeid, (-1 * $badgeobj["badge_value"] * $amount),
                            $pb_player_id, $validToken['cl_player_id'], $validToken['client_id'],
                            $validToken['site_id']);
                    }
                }
            }
        }

        if (isset($goods['redeem']['custom'])) {
            foreach ($goods['redeem']['custom'] as $customobj) {

                $customid = new MongoId($customobj["custom_id"]);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                $custom_name = $this->client_model->getRewardName(array_merge($validToken,
                    array('reward_id' => $customid)));

                $customArray['reward_id'] = $customid;
                $customArray['reward_name'] = $custom_name;

                if ((int)($player_custom[0]['value'] * $amount) >= (int)($customobj["custom_value"] * $amount)) {
                    $this->client_model->updateCustomReward($custom_name, (-1 * $customobj["custom_value"] * $amount),
                        array_merge($validToken,
                            array('pb_player_id' => $pb_player_id, 'player_id' => $validToken['cl_player_id'])),
                        $customArray);
                }
            }
        }

        return true;
    }

    /*public function test_get()
    {
//        var_Dump($this->input->get('token'));
        $this->benchmark->mark('goods_redeem_start');
        $validToken = $this->auth_model->findToken($this->input->get('token'));

//        var_Dump($validToken);
        $goods_id = $this->input->get('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => new MongoId($goods_id)
        )));

//        var_dump($goods);
        $amount = 1;

        $cl_player_id = "1";
        $validToken = array_merge($validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);

        $redeemResult = $this->processRedeem($pb_player_id, $goods, $amount, $validToken);

        $this->benchmark->mark('goods_redeem_end');
        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');

        $this->response($this->resp->setRespond($redeemResult), 200);
    }*/
}

?>
