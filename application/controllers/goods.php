<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
class Goods extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('goods_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function index_get($goodsId = 0)
    {
        $required = $this->input->checkParam(array(
            'api_key'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $validToken = $this->auth_model->createTokenFromAPIKey($this->input->get('api_key'));
        if(!$validToken)
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
        if($goodsId)
        {
            try {
                $goodsId = new MongoId($goodsId);
            }catch (MongoException $ex) {
                $goodsId = null;
            }
            //get goods by specific id
            $badge['goods'] = $this->goods_model->getGoods(array_merge($validToken, array(
                'goods_id' => new MongoId($goodsId)
            )));
            $this->response($this->resp->setRespond($badge), 200);
        }
        else
        {
            //get all goods relate to clients
            $badgesList['goods_list'] = $this->goods_model->getAllGoods($validToken);
            $this->response($this->resp->setRespond($badgesList), 200);
        }
    }

    public function redeem_post()
    {
        $this->benchmark->mark('goods_redeem_start');

        //process regular data
        $required = $this->input->checkParam(array(
            'token'
        ));
        if($required)
            $this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
        $required = $this->input->checkParam(array(
            'player_id',
            'goods_id'
        ));
        if($required)
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        $validToken = $this->auth_model->findToken($this->input->post('token'));
        if(!$validToken)
            $this->response($this->error->setError('INVALID_TOKEN'), 200);
        //get playbasis player id from client player id
        $cl_player_id = $this->input->post('player_id');
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
            'cl_player_id' => $cl_player_id
        )));
        if(!$pb_player_id)
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $goods_id = $this->input->post('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => $goods_id
        )));
        if(!$goods)
            $this->response($this->error->setError('GOODS_NOT_FOUND'), 200);

        $amount = 1;
        if($this->input->post('amount'))
            $amount = (int)$this->input->post('amount');

        $pb_player_id = new MongoId($pb_player_id);
        $redeemResult = $this->processRedeem($pb_player_id, $goods, $amount, $validToken);

        $this->benchmark->mark('goods_redeem_end');
        $redeemResult['processing_time'] = $this->benchmark->elapsed_time('goods_redeem_start', 'goods_redeem_end');
        $this->response($this->resp->setRespond($redeemResult), 200);
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

        $this->load->model('player_model');

        if(isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)){
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);
            $player_point = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);
            if((int)$player_point[0]['value']["point_value"] < (int)$goods['redeem']['point']["point_value"]){
                $event = array(
                    'event_type' => 'POINT_NOT_ENOUGH',
                    'message' => 'user point not enough'
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if(isset($goods['redeem']['badge'])){
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            $badge_redeem_check = count($goods['redeem']['badge']);
            $badge_can_redeem = 0;

            if($player_badges){
                $badge_player_check = array();
                foreach($player_badges as $b){
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                foreach($goods['redeem']['badge'] as $badgeid => $badgevalue){
                    if(isset($badge_player_check[$badgeid]) && $badge_player_check[$badgeid] >= $badgevalue){
                        $badge_can_redeem++;
                    }
                }
            }

            if((int)$badge_redeem_check > (int)$badge_can_redeem){
                $event = array(
                    'event_type' => 'BADGE_NOT_ENOUGH',
                    'message' => 'user badge not enough'
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if(isset($goods['redeem']['custom'])){

            $custom_redeem_check = count($goods['redeem']['custom']);
            $custom_can_redeem = 0;

            foreach($goods['redeem']['custom'] as $customid => $customvalue){

                $customid =new MongoId($customid);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                if((int)$player_custom[0]['value'] >= (int)$customvalue){
                    $custom_can_redeem++;
                }
            }

            if((int)$custom_redeem_check > (int)$custom_can_redeem){
                $event = array(
                    'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                    'message' => 'user custom point not enough'
                );
                array_push($redeemResult['events'], $event);
            }
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
        if(isset($goods['quantity']) && $goods['quantity']){
            if((int)$goods['quantity'] >= (int)$amount)
                return true;
        }
        return false;
    }

    public function test_get()
    {
        $validToken = $this->auth_model->findToken($this->input->get('token'));

        $goods_id = $this->input->get('goods_id');
        $goods = $this->goods_model->getGoods(array_merge($validToken, array(
            'goods_id' => new MongoId($goods_id)
        )));

        $amount = 1;

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
                'event_type' => 'RESOUCRE_NOT_ENOUGH',
                'message' => 'resource not enough for redeem'
            );
            array_push($redeemResult['events'], $event);
        }
        echo "<pre>";
        var_dump($goods);
        echo "</pre>";

        $pb_player_id = new MongoId("52d910ed8d8c89002d0002ff");
        $this->load->model('player_model');

        if(isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)){
            $input = array_merge($validToken, array(
                'reward_name' => "point"
            ));
            $this->load->model('point_model');
            $reward_id = $this->point_model->findPoint($input);

            $player_point= $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $validToken['site_id']);

            if((int)$player_point[0]['value'] < (int)$goods['redeem']['point']["point_value"]){
                $event = array(
                    'event_type' => 'POINT_NOT_ENOUGH',
                    'message' => 'user point not enough'
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if(isset($goods['redeem']['badge'])){
            $player_badges = $this->player_model->getBadge($pb_player_id, $validToken['site_id']);

            $badge_redeem_check = count($goods['redeem']['badge']);
            $badge_can_redeem = 0;

            if($player_badges){
                $badge_player_check = array();
                foreach($player_badges as $b){
                    $badge_player_check[$b["badge_id"]] = $b["amount"];
                }

                echo "<pre>";
                var_dump($badge_player_check);
                echo "</pre>";

                echo "Check : ".$badge_redeem_check;
                echo "</br>";

                foreach($goods['redeem']['badge'] as $badgeid => $badgevalue){
                    var_dump($badgeid);
                    var_dump($badgevalue);
                    echo "</br>";
                    if(isset($badge_player_check[$badgeid]) && $badge_player_check[$badgeid] >= $badgevalue){
                        $badge_can_redeem++;
                    }
                }

                echo "User have : ".$badge_can_redeem;
                echo "</br>";
            }

            if((int)$badge_redeem_check > (int)$badge_can_redeem){
                $event = array(
                    'event_type' => 'BADGE_NOT_ENOUGH',
                    'message' => 'user badge not enough'
                );
                array_push($redeemResult['events'], $event);
            }
        }

        if(isset($goods['redeem']['custom'])){

            $custom_redeem_check = count($goods['redeem']['custom']);
            $custom_can_redeem = 0;

            echo "Check : ".$custom_redeem_check;
            echo "</br>";

            foreach($goods['redeem']['custom'] as $customid => $customvalue){

                var_dump($customid);
                var_dump($customvalue);
                echo "</br>";

                $customid =new MongoId($customid);
                $player_custom = $this->player_model->getPlayerPoint($pb_player_id, $customid, $validToken['site_id']);

                if((int)$player_custom[0]['value'] >= (int)$customvalue){
                    $custom_can_redeem++;
                }
            }

            echo "User have : ".$custom_can_redeem;
            echo "</br>";

            if((int)$custom_redeem_check > (int)$custom_can_redeem){
                $event = array(
                    'event_type' => 'CUSTOM_POINT_NOT_ENOUGH',
                    'message' => 'user custom point not enough'
                );
                array_push($redeemResult['events'], $event);
            }
        }

        echo "<pre>";
        var_dump($redeemResult);
        echo "</pre>";

        
    }
}
?>