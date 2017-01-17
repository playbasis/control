<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getAllGoods($data, $nin = array())
    {
        $this->set_site_mongodb($data['site_id']);
        $select = isset($data['selected_field']) && is_array($data['selected_field']) && $data['selected_field'] ? $data['selected_field'] : array(
            'goods_id',
            'image',
            'name',
            'code',
            'description',
            'quantity',
            'per_user',
            'redeem',
            'group',
            'tags',
            'date_start',
            'date_expire',
            'sponsor',
            'sort_order',
            'organize_id',
            'organize_role'
        );
        $this->mongo_db->select($select);
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'deleted' => false,
            'status' => true,
        ));
        $this->mongo_db->order_by(array('sort_order' => 'asc'));
        if (!empty($nin)) {
            $this->mongo_db->where_not_in('_id', $nin);
        }
        if (!empty($data['tags'])){
            $this->mongo_db->where_in('tags', $data['tags']);
        }

        if(isset($data['date_start']) && !empty($data['date_start']) && isset($data['date_end']) && !empty($data['date_end'])){
            $this->mongo_db->where(array('$and' => array(array('$and' => array(array('$or' => array(array("date_start" => null), array("date_start" => array('$lte'=> $data['date_start'])))),
                                                                               array('$or' => array(array("date_expire" => null), array("date_expire" => array('$gte'=> $data['date_start'])))))),
                                                         array('$and' => array(array('$or' => array(array("date_start" => null), array("date_start" => array('$lte'=> $data['date_end'])))),
                                                                               array('$or' => array(array("date_expire" => null), array("date_expire" => array('$gte'=> $data['date_end'])))))))));
        } else {
            if(isset($data['date_start']) && !empty($data['date_start'])){
                $this->mongo_db->where(array('$and' => array( array('$or' => array(array("date_start" => null), array("date_start" => array('$lte'=> $data['date_start'])))),
                                                              array('$or' => array(array("date_expire" => null), array("date_expire" => array('$gte'=> $data['date_start'])))))));
            }

            if(isset($data['date_end']) && !empty($data['date_end'])){
                $this->mongo_db->where(array('$and' => array( array('$or' => array(array("date_start" => null), array("date_start" => array('$lte'=> $data['date_end'])))),
                                                              array('$or' => array(array("date_expire" => null), array("date_expire" => array('$gte'=> $data['date_end'])))))));
            }
        }




        if (isset($data['offset']) && !empty($data['offset'])) {
            if ($data['offset'] < 0) {
                $data['offset'] = 0;
            }
        } else {
            $data['offset'] = 0;
        }

        $this->mongo_db->offset((int)$data['offset']);
        
        if (isset($data['limit']) && !empty($data['limit'])) {
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $this->mongo_db->limit((int)$data['limit']);
        }

        $goods = $this->mongo_db->get('playbasis_goods_to_client');
        if ($goods) {
            foreach ($goods as &$g) {

                if (isset($g['redeem'])) {
                    if (isset($g['redeem']['badge'])) {
                        $redeem = array();
                        foreach ($g['redeem']['badge'] as $k => $v) {
                            $redeem_inside = array();
                            $redeem_inside["badge_id"] = $k;
                            $redeem_inside["badge_value"] = $v;
                            $redeem[] = $redeem_inside;
                        }
                        $g['redeem']['badge'] = $redeem;
                    }
                    if (isset($g['redeem']['custom'])) {
                        $redeem = array();
                        foreach ($g['redeem']['custom'] as $k => $v) {
                            $this->mongo_db->select(array('name'));
                            $this->mongo_db->select(array(), array('_id'));
                            $this->mongo_db->where(array(
                                'client_id' => $data['client_id'],
                                'site_id' => $data['site_id'],
                                'reward_id' => new MongoId($k),
                            ));
                            $custom = $this->mongo_db->get('playbasis_reward_to_client');
                            if (isset($custom[0]['name'])) {
                                $redeem_inside = array();
                                $redeem_inside["custom_id"] = $k;
                                $redeem_inside["custom_name"] = $custom[0]['name'];
                                $redeem_inside["custom_value"] = $v;
                                $redeem[] = $redeem_inside;
                            }
                        }
                        $g['redeem']['custom'] = $redeem;
                    }
                }

                if(isset($g['image'])) $g['image'] = $this->config->item('IMG_PATH') . $g['image'];
                if(isset($g['_id'])) $g['_id'] = $g['_id'] . "";
                if(isset($g['goods_id'])) $g['goods_id'] = $g['goods_id'] . "";
                if(isset($g['date_start'])) $g['date_start'] = $g['date_start'] ? datetimeMongotoReadable($g['date_start']) : null;
                if(isset($g['date_expire'])) $g['date_expire'] = $g['date_expire'] ? datetimeMongotoReadable($g['date_expire']) : null;
            }
        }
        return $goods;
    }

    public function getGoodsByGroupAndCode($client_id, $site_id, $group, $code, $fields = array(),$all=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('code', $code);

        $result = $this->mongo_db->get('playbasis_goods_to_client');

        if($all){
            return $result ? $result : null;
        }else{
            return $result ? $result[0] : null;
        }
    }

    public function getAllAvailableGoodsByGroupAndCode($client_id, $site_id, $group, $code, $quantity=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array(
            'goods_id',
            'image',
            'name',
            'code',
            'description',
            'quantity',
            'redeem',
            'group',
            'tags',
            'date_start',
            'date_expire',
            'sponsor',
            'sort_order',
            'organize_id',
            'organize_role'
        ));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('code', $code);
        $this->mongo_db->where('$or',  array(array('date_expired_coupon' => array('$exists' => false)), array('date_expired_coupon' => array('$gt' => new MongoDate()))));
        if($quantity){
            $this->mongo_db->where_gt('quantity', 0);
        }

        $result = $this->mongo_db->get('playbasis_goods_to_client');
        return $result;
    }

    public function checkPlayerGoodsGroupById($client_id, $site_id, $goods_list, $pb_player_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_in('goods_id', $goods_list);
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        
        $result = $this->mongo_db->get('playbasis_goods_to_player');
        return $result;
    }

    public function getGoods($data, $is_sponsor = false)
    {
        //get goods id
        $this->set_site_mongodb($data['site_id']);
        // $this->mongo_db->select(array('goods_id','image','name','description','quantity','redeem','date_start','date_expire','sponsor'));
        $this->mongo_db->select(array(
            'goods_id',
            'image',
            'name',
            'description',
            'quantity',
            'per_user',
            'redeem',
            'date_start',
            'date_expire',
            'days_expire',
            'date_expired_coupon',
            'sponsor',
            'sort_order',
            'group',
            'code',
            'tags',
            'organize_id',
            'organize_role'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $is_sponsor ? null : $data['client_id'],
            'site_id' => $is_sponsor ? null : $data['site_id'],
            'goods_id' => $data['goods_id'],
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_goods_to_client');

        if (isset($result[0]['redeem'])) {
            if (isset($result[0]['redeem']['badge'])) {
                $redeem = array();
                foreach ($result[0]['redeem']['badge'] as $k => $v) {
                    $redeem_inside = array();
                    $redeem_inside["badge_id"] = $k;
                    $redeem_inside["badge_value"] = $v;
                    $redeem[] = $redeem_inside;
                }
                $result[0]['redeem']['badge'] = $redeem;
            }
            if (isset($result[0]['redeem']['custom'])) {
                $redeem = array();
                foreach ($result[0]['redeem']['custom'] as $k => $v) {
                    $this->mongo_db->select(array('name'));
                    $this->mongo_db->select(array(), array('_id'));
                    $this->mongo_db->where(array(
                        'client_id' => $data['client_id'],
                        'site_id' => $data['site_id'],
                        'reward_id' => new MongoId($k),
                    ));
                    $this->mongo_db->limit(1);
                    $custom = $this->mongo_db->get('playbasis_reward_to_client');
                    if (isset($custom[0]['name'])) {
                        $redeem_inside = array();
                        $redeem_inside["custom_id"] = $k;
                        $redeem_inside["custom_name"] = $custom[0]['name'];
                        $redeem_inside["custom_value"] = $v;
                        $redeem[] = $redeem_inside;
                    }
                }
                $result[0]['redeem']['custom'] = $redeem;
            }
        }
        if (isset($result[0]['goods_id'])) {
            $result[0]['goods_id'] = $result[0]['goods_id'] . "";
        }
        if (isset($result[0]['date_start'])) {
            $result[0]['date_start'] = datetimeMongotoReadable($result[0]['date_start']);
        }
        if (isset($result[0]['date_expire'])) {
            $result[0]['date_expire'] = datetimeMongotoReadable($result[0]['date_expire']);
        }
        if (isset($result[0]['date_expired_coupon'])) {
            $result[0]['date_expired_coupon'] = datetimeMongotoReadable($result[0]['date_expired_coupon']);
        }
        if (isset($result[0]['image'])) {
            $result[0]['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
        }
        return $result ? $result[0] : array();
    }

    public function getGoodsIDByName($client_id, $site_id, $good_name, $good_group=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('goods_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('status', true);

        if($good_group){
            $this->mongo_db->where('group', $good_group);
        }else{
            $this->mongo_db->where('name', $good_name);
        }
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0]['goods_id']."" : null;
    }

    public function redeemLogDistinctPlayer($data, $goods_id, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'goods_id' => $goods_id->{'$id'}
        );
        if ($from || $to) {
            $query['date_added'] = array();
        }
        if ($from) {
            $query['date_added']['$gte'] = $this->new_mongo_date($from);
        }
        if ($to) {
            $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
        }
        $result = $this->mongo_db->command(array(
            'distinct' => 'playbasis_goods_log',
            'key' => 'pb_player_id',
            'query' => $query
        ));
        return $result['values'];
    }

    public function getTotalGoodsByGroup($client_id, $site_id, $group)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('goods_id', 'date_start', 'date_expire', 'quantity', 'per_user', 'redeem'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'group' => $group,
            'deleted' => false,
            'status' => true
        ));
        $this->mongo_db->where_gt('quantity', 0);
        $this->mongo_db->where('$or',  array(array('date_expired_coupon' => array('$exists' => false)), array('date_expired_coupon' => array('$gt' => new MongoDate()))));
        return $this->mongo_db->count('playbasis_goods_to_client');
    }
    
    public function getPlayerGoods($site_id, $goodsId, $pb_player_id)
    {
        $this->mongo_db->select(array('value'));
        $this->mongo_db->where(array(
            'site_id' => new MongoID($site_id),
            'goods_id' => new MongoID($goodsId),
            'pb_player_id' => new MongoID($pb_player_id)
        ));
        $this->mongo_db->limit(1);
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return isset($goods[0]) ? $goods[0]['value'] : null;
    }
    
    public function getPlayerGoodsGroup($site_id, $goods_group, $pb_player_id)
    {
        $this->mongo_db->where(array(
            'site_id' => new MongoID($site_id),
            'group' => $goods_group,
            'pb_player_id' => new MongoID($pb_player_id)
        ));
        $goods = $this->mongo_db->count('playbasis_goods_to_player');
        return $goods;
    }

    public function getGoodsByGroup($client_id, $site_id, $group, $offset = null, $limit = null , $quantity = null)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'goods_id',
            'name',
            'description',
            'image',
            'date_start',
            'date_expire',
            'quantity',
            'per_user',
            'redeem',
            'group',
            'code',
            'organize_id',
            'organize_role',
            'tags'
        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'group' => $group,
            'deleted' => false,
            'status' => true
        ));
        $this->mongo_db->where_gt('quantity', 0);
        if ($offset !== null) {
            $this->mongo_db->offset($offset);
        }
        if ($limit !== null) {
            $this->mongo_db->limit($limit);
        }
        if ($quantity != null){
            $this->mongo_db->where('quantity', (int)$quantity);
        }

        $this->mongo_db->where('$or',  array(array('date_expired_coupon' => array('$exists' => false)), array('date_expired_coupon' => array('$gt' => new MongoDate()))));
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function getGoodsByGroupAndPlayerId(
        $client_id,
        $site_id,
        $group,
        $pb_player_id,
        $amount,
        $is_sponsor = false
    ) {
        $msg = array();
        $goodsList = $this->getGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id, $group, 0, 1);
        if ($goodsList) {
            if ($this->checkGoods($client_id, $site_id, $goodsList[0], $pb_player_id, $amount , $msg)) {
                $total = $this->getTotalGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id, $group);
                $offset = rand(0, $total - 1); // randomly pick one
                $goodsList = $this->getGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id,
                    $group, $offset, 1);
                return $goodsList ? $goodsList[0] : null;
            }
        } else {
            $msg['error'] = 'GOODS_NOT_ENOUGH';
        }
        return $msg;
    }

    public function getGoodsFromGroup($client_id, $site_id, $group, $pb_player_id, $amount, $is_sponsor = false)
    {
        $total = $this->getTotalGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id, $group);
        $offset = rand(0, $total - 1); // randomly pick one
        $goodsList = $this->getGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id, $group,
            $offset, 1);
        return $goodsList ? $goodsList[0] : null;
    }

    public function countGoodsByGroup($client_id, $site_id, $group, $pb_player_id, $amount, $is_sponsor = false)
    {
        $goodsList = $this->getGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id, $group, 0,
            1);
        if ($goodsList) {
            if ($this->checkGoods($client_id, $site_id, $goodsList[0], $pb_player_id, $amount)) {
                return $this->getTotalGoodsByGroup($is_sponsor ? null : $client_id, $is_sponsor ? null : $site_id,
                    $group);
            }
        }
        return 0; // unavailable
    }

    /* Deprecated: use getGroupsAggregate instead */
    public function getGroups($site_id)
    {
        $this->mongo_db->select(array('goods_id', 'group', 'quantity'));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_exists('group', true);
        $results = $this->mongo_db->get("playbasis_goods_to_client");
        $groups = array();
        if ($results) {
            foreach ($results as $result) {
                $name = $result['group'];
                if (array_key_exists($name, $groups)) {
                    $groups[$name][] = array('goods_id' => $result['goods_id'], 'quantity' => $result['quantity']);
                } else {
                    $groups[$name] = array(array('goods_id' => $result['goods_id'], 'quantity' => $result['quantity']));
                }
            }
        }
        return $groups;
    }

    public function getGroupsAggregate($site_id)
    {
        $results = $this->mongo_db->aggregate('playbasis_goods_to_client', array(
            array(
                '$match' => array(
                    'deleted' => false,
                    'site_id' => $site_id,
                    'group' => array('$exists' => true)
                ),
            ),
            array(
                '$project' => array('group' => 1, 'quantity' => 1, 'date_expired_coupon' => 1)
            ),
            array(
                '$group' => array(
                    '_id' => array('group' => '$group'),
                    'quantity' => array('$sum' => array('$cond'=> array(array('$or' => array(array('$gt' => array('$date_expired_coupon', new MongoDate())) ,
                        array('$not' => array('$ifNull' => array('$date_expired_coupon', 0))))) , '$quantity', 0))),
                    'list' => array('$addToSet' => '$_id')
                )
            ),
        ));
        return $results ? $results['result'] : array();
    }

    private function checkGoods($client_id, $site_id, $goods, $pb_player_id, $amount, &$msg = array())
    {
        if (isset($goods['date_start'])) {
            $goods['date_start'] = datetimeMongotoReadable($goods['date_start']);
        }
        if (isset($goods['date_expire'])) {
            $goods['date_expire'] = datetimeMongotoReadable($goods['date_expire']);
        }
        $valid = $this->checkGoodsTime($goods);
        if (!$valid) {
            $msg['error'] = "GOODS_NOT_AVAILABLE";
            return false;
        }
        $valid = $this->checkGoodsAmount($goods, $amount);
        if (!$valid) {
            $msg['error'] = "GOODS_NOT_ENOUGH";
            return false;
        }
        $playerRecord = $this->getGoodsToPlayerRecord($goods['goods_id'], $pb_player_id);
        $valid = $this->checkGoodsPlayerPerUser($goods, $playerRecord);
        if (!$valid) {
            return false;
        }
        $valid = $this->checkGoodsPlayerPoint($goods, $pb_player_id, $amount, $client_id, $site_id);
        if (!$valid) {
            $msg['error'] = "POINT_NOT_ENOUGH";
            return false;
        }
        $valid = $this->checkGoodsPlayerBadge($goods, $pb_player_id, $amount);
        if (!$valid) {
            $msg['error'] = "BADGE_NOT_ENOUGH";
            return false;
        }
        $valid = $this->checkGoodsPlayerCustom($goods, $pb_player_id, $amount, $msg);
        if (!$valid) {
            $msg['error'] = "CUSTOM_POINT_NOT_ENOUGH";
            return false;
        }
        return true;
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

    private function checkGoodsPlayerPerUser($goods, $playerRecord)
    {
        if ($playerRecord && $goods['per_user'] != null) {
            return $playerRecord['value'] < $goods['per_user'];
        }
        return true;
    }

    private function checkGoodsPlayerPoint($goods, $pb_player_id, $amount, $client_id, $site_id)
    {
        if (isset($goods['redeem']['point']["point_value"]) && ($goods['redeem']['point']["point_value"] > 0)) {
            $reward_id = $this->getRewardIdByName($client_id, $site_id, 'point');
            $playerRecord = $this->getRewardToPlayerRecord($reward_id, $pb_player_id);
            $player_point = ($playerRecord && array_key_exists('value', $playerRecord) ? $playerRecord['value'] : 0);
            if ((int)($player_point * $amount) < (int)($goods['redeem']['point']["point_value"] * $amount)) {
                return false;
            }
        }
        return true;
    }

    private function checkGoodsPlayerBadge($goods, $pb_player_id, $amount)
    {
        if (isset($goods['redeem']['badge'])) {
            $redeem_current = 0;
            $redeem_at_least = count($goods['redeem']['badge']);

            $list_badge_id = array();
            foreach ($goods['redeem']['badge'] as $k => $v) {
                $list_badge_id[] = new MongoId($k);
            }
            $playerRecords = $this->getBadgesToPlayerRecords($list_badge_id, $pb_player_id);
            if ($playerRecords) {
                foreach ($playerRecords as $playerRecord) {
                    $badge_id = $playerRecord['badge_id'];
                    $value = (int)$playerRecord['value'];
                    if ($value * $amount >= $goods['redeem']['badge'][$badge_id->{'$id'}] * $amount) {
                        $redeem_current++;
                    }
                }
            }

            if ($redeem_current < $redeem_at_least) {
                return false;
            }
        }
        return true;
    }

    private function checkGoodsPlayerCustom($goods, $pb_player_id, $amount, &$msg = array())
    {
        if (isset($goods['redeem']['custom'])) {
            $redeem_current = 0;
            $redeem_at_least = count($goods['redeem']['custom']);

            $list_custom_id = array();
            foreach ($goods['redeem']['custom'] as $k => $v) {
                $list_custom_id[] = new MongoId($k);
            }
            $playerRecords = $this->getRewardsToPlayerRecords($list_custom_id, $pb_player_id);
            if ($playerRecords) {
                foreach ($playerRecords as $playerRecord) {
                    $reward_id = $playerRecord['reward_id'];
                    $value = (int)$playerRecord['value'];
                    if ($value * $amount >= $goods['redeem']['custom'][$reward_id->{'$id'}] * $amount) {
                        $redeem_current++;
                    } else {
                        $msg['custom_id'][] = $reward_id->{'$id'};
                    }
                }
            } else {
                $msg['custom_id'] = $list_custom_id;
            }

            if ($redeem_current < $redeem_at_least) {
                return false;
            }
        }
        return true;
    }

    private function getRewardIdByName($client_id, $site_id, $name)
    {
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => strtolower($name)
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0]['reward_id'] : array();
    }

    private function getGoodsToPlayerRecord($goods_id, $pb_player_id)
    {
        $this->mongo_db->where(array(
            'pb_player_id' => $pb_player_id,
            'goods_id' => $goods_id,
        ));
        $this->mongo_db->limit(1);
        $playerRecord = $this->mongo_db->get('playbasis_goods_to_player');
        return $playerRecord ? $playerRecord[0] : null;
    }

    private function getRewardToPlayerRecord($reward_id, $pb_player_id)
    {
        $this->mongo_db->where(array(
            'pb_player_id' => $pb_player_id,
            'reward_id' => $reward_id
        ));
        $this->mongo_db->limit(1);
        $playerRecord = $this->mongo_db->get('playbasis_reward_to_player');
        return $playerRecord ? $playerRecord[0] : null;
    }

    private function getRewardsToPlayerRecords($list_reward_id, $pb_player_id)
    {
        $this->mongo_db->where(array('pb_player_id' => $pb_player_id));
        $this->mongo_db->where_in('reward_id', $list_reward_id);
        return $this->mongo_db->get('playbasis_reward_to_player');
    }

    private function getBadgesToPlayerRecords($list_badge_id, $pb_player_id)
    {
        $this->mongo_db->where(array('pb_player_id' => $pb_player_id));
        $this->mongo_db->where_in('badge_id', $list_badge_id);
        return $this->mongo_db->get('playbasis_reward_to_player');
    }
}

?>