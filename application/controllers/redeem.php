<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Redeem extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('goods_model');
        $this->load->model('player_model');
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
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $validToken = array_merge($this->validToken, array(
            'cl_player_id' => $cl_player_id
        ));
        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $goods_id = $this->input->post('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => new MongoId($goods_id)
        )));

        // if(!$goods)
        //     $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);

        //-->NEW
        if(!$goods){
            $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);
        }else{
            $per_user = $goods['per_user'];

            $get_player_goods = $this->player_model->getGoods(new MongoId($pb_player_id), $validToken['site_id']);

            $overLimit = false;

            if($goods['per_user'] != null){
                foreach ($get_player_goods as $a_good){
                    if($a_good['goods_id'] == $goods['goods_id']){
                        if ($a_good['amount']>=$per_user){
                            $overLimit = true;
                            break;
                        }    
                    }
                }    
            }
            
            if ($overLimit){
                $this->response($this->error->setError('OVER_LIMIT_REDEEM'), 200);
            }else{
                $amount = 1;
                if($this->input->post('amount'))
                    $amount = (int)$this->input->post('amount');

                $pb_player_id = new MongoId($pb_player_id);
                $redeemResult = $this->processRedeem($pb_player_id, $goods, $amount, $validToken);

                $this->benchmark->mark('goods_redeem_end');
                $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
                $this->response($this->resp->setRespond($redeemResult), 200);              
            }
        }
        //-->END NEW

        /*
        $amount = 1;
        if($this->input->post('amount'))
            $amount = (int)$this->input->post('amount');

        $pb_player_id = new MongoId($pb_player_id);
        $redeemResult = $this->processRedeem($pb_player_id, $goods, $amount, $validToken);

        $this->benchmark->mark('goods_redeem_end');
        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
        $this->response($this->resp->setRespond($redeemResult), 200);
        */
    }

    private function processRedeem($pb_player_id, $goods, $amount, $validToken)
    {   
        $redeemResult = array(
            'events' => array()
        );
        if(!$this->checkGoodsTime($goods)){
            $event = array(
                'event_type' => 'GOODS_NOT_AVAILABLE',
                'message' => 'goods not available on now'
            );
            array_push($redeemResult['events'], $event);
        }
        if(!$this->checkGoodsAmount($goods, $amount)){            
            $event = array(
                'event_type' => 'GOODS_NOT_ENOUGH',
                'message' => 'goods not enough for redeem'
            );
            array_push($redeemResult['events'], $event);
        }
        
        if(isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)){
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);
            $player_point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
            if(isset($player_point[0]['value'])){
                $player_point = $player_point[0]['value'];
            }else{
                $player_point = 0;
            }

            if((int)$player_point < (int)$goods['redeem']['point']["point_value"]){
                $event = array(
                    'event_type' => 'POINT_NOT_ENOUGH',
                    'message' => 'user point not enough',
                    'incomplete' => (int)$goods['redeem']['point']["point_value"] - (int)$player_point[0]['value']
                );
                array_push($redeemResult['events'], $event);
            }

        }

        if(isset($goods['redeem']['badge'])){
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            $badge_redeem_check = count($goods['redeem']['badge']);
            $badge_can_redeem = 0;
            $badge_incomplete = array();

            if($player_badges){
                $badge_player_check = array();
                foreach($player_badges as $b){
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                foreach($goods['redeem']['badge'] as $badgeobj){
                    if(isset($badge_player_check[$badgeobj["badge_id"]]) && (int)$badge_player_check[$badgeobj["badge_id"]] >= (int)$badgeobj["badge_value"]){
                        $badge_can_redeem++;
                    }else{
                        array_push($badge_incomplete, array($badgeobj["badge_id"]."" => (isset($badge_player_check[$badgeobj["badge_id"]])) ? ((int)$badgeobj["badge_value"] - (int)$badge_player_check[$badgeobj["badge_id"]]) : (int)$badgeobj["badge_value"]));
                    }
                }
            }

            if((int)$badge_redeem_check > (int)$badge_can_redeem){
                $event = array(
                    'event_type' => 'BADGE_NOT_ENOUGH',
                    'message' => 'user badge not enough',
                    'incomplete' => $badge_incomplete
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if(isset($goods['redeem']['custom'])){

            $custom_redeem_check = count($goods['redeem']['custom']);
            $custom_can_redeem = 0;
            $custom_incomplete = array();

            foreach($goods['redeem']['custom'] as $customobj){

                $customid =new MongoId($customobj["custom_id"]);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                if($player_custom && (int)$player_custom[0]['value'] >= (int)$customobj["custom_value"]){
                    $custom_can_redeem++;
                }else{
                    array_push($custom_incomplete, array($customid."" => ($player_custom) ? ((int)$customobj["custom_value"] - (int)$player_custom[0]['value']) : (int)$customobj["custom_value"]));
                }
            }

            if((int)$custom_redeem_check > (int)$custom_can_redeem){
                $event = array(
                    'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                    'message' => 'user custom point not enough',
                    'incomplete' => $custom_incomplete
                );
                array_push($redeemResult['events'], $event);
            }            
        }

        if(!(isset($redeemResult['events']) && count($redeemResult['events']) > 0)){
            $this->getRedeemGoods($pb_player_id, $goods, $amount, $validToken);

            $goodsData = $this->goods_model->getGoods(array_merge($validToken, array(
                'goods_id' => new MongoId($goods['goods_id']))));

            if(!$goodsData)
                return;
            $event = array(
                'event_type' => 'GOODS_RECEIVED',
                'goods_data' => $goodsData,
                'value' => $amount
            );

            array_push($redeemResult['events'], $event);

            $eventMessage = $this->utility->getEventMessage('goods', '', '', '', '', '', $goodsData['name']);

            $validToken = array_merge($validToken, array(
                'pb_player_id' => $pb_player_id,
                // 'goods_id' => $goodsData['goods_id'],
                'goods_id' => new MongoId($goodsData['goods_id']),
                'goods_name' => $goodsData['name'],
                'amount' => $amount,
                'redeem' => $goodsData['redeem'],
                'action_name' => 'redeem_goods'
            ));
            //log event - goods
            $this->tracker_model->trackGoods($validToken);
            //publish to node stream
            $this->node->publish(array_merge($validToken, array(
                'message' => $eventMessage,
                'goods' => $event['goods_data']
            )), $validToken['domain_name'], $validToken['site_id']);
            //publish to facebook notification
//            if($fbData)
//                $this->social_model->sendFacebookNotification($validToken['client_id'], $validToken['site_id'], $fbData['facebook_id'], $eventMessage, '');
        }

        return $redeemResult;
    }

    private function checkGoodsTime($goods)
    {
        if(isset($goods['date_start']) && $goods['date_start']){
            $datetimecheck = new DateTime('now');
            $datetimestart = new DateTime($goods['date_start']);
            if($datetimecheck < $datetimestart)
                return false;
        }
        if(isset($goods['date_expire']) && $goods['date_expire'] ){
            $datetimecheck = new DateTime('now');
            $datetimeexpire = new DateTime($goods['date_expire']);
            if($datetimecheck > $datetimeexpire)
                return false;
        }
        return true;
    }

    private function checkGoodsAmount($goods, $amount)
    {
        /*
        if(isset($goods['quantity']) && $goods['quantity']){
            if((int)$goods['quantity'] >= (int)$amount)
                return true;
        }
        return false;
        */

        // NEW -->
        if(isset($goods['quantity']) && !is_null($goods['quantity'])){
            if((int)$goods['quantity'] >= (int)$amount)
                return true;
        }elseif(is_null($goods['quantity'])){
            return true;
        }else{
            return false;    
        }
        // END NEW -->
    }

    private function getRedeemGoods($pb_player_id, $goods, $amount, $validToken){
        $this->load->model('client_model');

        $goods_id = new MongoId($goods['goods_id']);
        $this->client_model->updateplayerGoods($goods_id, $amount, $pb_player_id, $validToken['cl_player_id'], $validToken['client_id'], $validToken['site_id']);

        if(isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)){
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);
            $reward_id =new MongoId($reward_id);
            $player_point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
            if((int)$player_point[0]['value'] >= (int)$goods['redeem']['point']["point_value"]){
                $this->client_model->updatePlayerPointReward($reward_id, (-1*$goods['redeem']['point']["point_value"]), $pb_player_id, $validToken['cl_player_id'], $validToken['client_id'], $validToken['site_id']);
            }
        }

        if(isset($goods['redeem']['badge'])){
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            if($player_badges){
                $badge_player_check = array();
                foreach($player_badges as $b){
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                foreach($goods['redeem']['badge'] as $badgeobj){
                    if(isset($badge_player_check[$badgeobj["badge_id"]]) && $badge_player_check[$badgeobj["badge_id"]] >= $badgeobj["badge_value"]){
                        $badgeid =new MongoId($badgeobj["badge_id"]);
                        $this->client_model->updateplayerBadge($badgeid, (-1*$badgeobj["badge_value"]), $pb_player_id, $validToken['cl_player_id'], $validToken['client_id'], $validToken['site_id']);
                    }
                }
            }
        }

        if(isset($goods['redeem']['custom'])){
            foreach($goods['redeem']['custom'] as $customobj){

                $customid =new MongoId($customobj["custom_id"]);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                $custom_name = $this->client_model->getRewardName(array_merge($validToken, array('reward_id' => $customid)));

                $customArray['reward_id'] = $customid;
                $customArray['reward_name'] = $custom_name;

                if((int)$player_custom[0]['value'] >= (int)$customobj["custom_value"]){
                    $this->client_model->updateCustomReward($custom_name, (-1*$customobj["custom_value"]), array_merge($validToken, array('pb_player_id' => $pb_player_id, 'player_id' => $validToken['cl_player_id'])), $customArray);
                }
            }
        }

        return true;
    }

    public function test_get()
    {
        var_Dump($this->input->get('token'));
//        $this->benchmark->mark('goods_redeem_start');
        $validToken = $this->auth_model->test($this->input->get('token'));

        var_Dump($validToken);
//        $goods_id = $this->input->get('goods_id');
//        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
//            'goods_id' => new MongoId($goods_id)
//        )));
//
//        $amount = 1;
//
//        $cl_player_id = "1";
//        $validToken = array_merge($validToken, array(
//            'cl_player_id' => $cl_player_id
//        ));
//        $pb_player_id = $this->player_model->getPlaybasisId($validToken);
//
//        $redeemResult = $this->processRedeem($pb_player_id, $goods, $amount, $validToken);
//
//        $this->benchmark->mark('goods_redeem_end');
//        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
//        $this->response($this->resp->setRespond($redeemResult), 200);
    }
}
?>