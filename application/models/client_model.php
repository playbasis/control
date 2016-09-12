<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define("CUSTOM_POINT_START_ID", 10000);

class Client_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getRuleSet($clientData)
    {
        assert($clientData);
        assert(is_array($clientData));
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('jigsaw_set'));
        $this->mongo_db->where($clientData);
        return $this->mongo_db->get('playbasis_rule');
    }

    public function getAction($clientData)
    {
        assert($clientData);
        assert(is_array($clientData));
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        assert(isset($clientData['action_name']));
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('action_id', 'icon'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            'name' => $clientData['action_name']
        ));
        $this->mongo_db->limit(1);
        $id = $this->mongo_db->get('playbasis_action_to_client');
        return ($id && $id[0]) ? $id[0] : array();
    }

    public function getActionId($clientData)
    {
        assert($clientData);
        assert(is_array($clientData));
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        assert(isset($clientData['action_name']));
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('action_id'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            'name' => $clientData['action_name']
        ));
        $this->mongo_db->limit(1);
        $id = $this->mongo_db->get('playbasis_action_to_client');
        return ($id && $id[0]) ? $id[0]['action_id'] : 0;
    }

    public function getActionName($clientData)
    {
        assert($clientData);
        assert(is_array($clientData));
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        assert(isset($clientData['action_id']));
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            'action_id' => $clientData['action_id']
        ));
        $this->mongo_db->limit(1);
        $id = $this->mongo_db->get('playbasis_action_to_client');
        return ($id && $id[0]) ? $id[0]['name'] : '';
    }

    public function getRuleDetail($clientData, $rule_id)
    {
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('name', 'description', 'action_id', 'jigsaw_set', 'active_status', 'tags'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            '_id' => new MongoId($rule_id)
        ));
        $id = $this->mongo_db->get('playbasis_rule');
        return $id ? $id[0] : '';
    }

    /*
     * get RuleSet by _id from plybasis_rule
     * @param string | MongoId $id
     * @return array
     */
    public function getRuleSetById($id)
    {
        try {
            $id = new MongoId($id);
        } catch (Exception $e) {
            return array();
        }

        $this->mongo_db->select(array("_id", "name", "jigsaw_set"));
        $rules = $this->mongo_db->get_where(
            "playbasis_rule",
            array("_id" => $id));

        if ($rules) {
            foreach ($rules as &$rule) {
                $rule["rule_id"] = $rule["_id"];
                unset($rule["_id"]);
            }
        }
        return $rules;
    }

    public function getRuleSetByClientSite($clientData)
    {
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        $clientData['active_status'] = true;
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array(
            '_id',
            'name',
            'jigsaw_set'
        ));
        $this->mongo_db->where($clientData);
        $rules = $this->mongo_db->get('playbasis_rule');
        if ($rules) {
            foreach ($rules as &$rule) {
                $rule['rule_id'] = $rule['_id'];
                unset($rule['_id']);
            }
        }
        return $rules;
    }

    public function getJigsawProcessor($jigsawId, $site_id)
    {
        assert($jigsawId);
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('class_path'));
        $this->mongo_db->where(array(
            'jigsaw_id' => $jigsawId
        ));
        $this->mongo_db->limit(1);
        $jigsawProcessor = $this->mongo_db->get('playbasis_game_jigsaw_to_client');
        if ($jigsawProcessor) {
            assert($jigsawProcessor);
            return $jigsawProcessor[0]['class_path'];
        } else {
            return null;
        }
    }

    public function getJigsawProcessorWithCache(&$cache, $jigsawId, $site_id)
    {
        $key = $jigsawId . '';
        if (!isset($cache[$key])) {
            $value = $this->getJigsawProcessor($jigsawId, $site_id);
            $cache[$key] = $value;
        }
        return $cache[$key];
    }

    public function updatePlayerPointReward(
        $rewardId,
        $quantity,
        $pbPlayerId,
        $clPlayerId,
        $clientId,
        $siteId,
        $overrideOldValue = false,
        $anonymous = false
    ) {
        assert(isset($rewardId));
        assert(isset($siteId));
        assert(isset($quantity));
        assert(isset($pbPlayerId));
        $this->set_site_mongodb($siteId);

        //update player reward table
        $this->mongo_db->where(array(
            'pb_player_id' => $pbPlayerId,
            'reward_id' => $rewardId
        ));
        $hasReward = $this->mongo_db->count('playbasis_reward_to_player');
        if ($hasReward) {
            $this->mongo_db->where(array(
                'pb_player_id' => $pbPlayerId,
                'reward_id' => $rewardId
            ));
            $this->mongo_db->set('date_modified', new MongoDate(time()));
            if ($overrideOldValue) {
                $this->mongo_db->set('value', intval($quantity));
            } else {
                $this->mongo_db->inc('value', intval($quantity));
            }
            $this->mongo_db->update('playbasis_reward_to_player');
        } else {
            $mongoDate = new MongoDate(time());
            $this->mongo_db->insert('playbasis_reward_to_player', array(
                'pb_player_id' => $pbPlayerId,
                'cl_player_id' => $clPlayerId,
                'client_id' => $clientId,
                'site_id' => $siteId,
                'reward_id' => $rewardId,
                'value' => intval($quantity),
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate
            ));
        }

        //update client reward limit
        if (!$anonymous) {
            $this->mongo_db->select(array('limit'));
            $this->mongo_db->where(array(
                'reward_id' => $rewardId,
                'site_id' => $siteId
            ));
            $this->mongo_db->limit(1);
            $result = $this->mongo_db->get('playbasis_reward_to_client');
            assert($result);
            $result = $result[0];
            if (is_null($result['limit'])) {
                return;
            }
            $this->mongo_db->where(array(
                'reward_id' => $rewardId,
                'site_id' => $siteId
            ));
            $this->mongo_db->dec('limit', intval($quantity));
            $this->mongo_db->update('playbasis_reward_to_client');
        }
    }

    public function updateCustomReward($rewardName, $quantity, $input, &$jigsawConfig, $anonymous = false)
    {
        //get reward id
        $this->set_site_mongodb($input['site_id']);
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where(array(
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'name' => strtolower($rewardName)
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        $customRewardId = null;
        if ($result && $result[0]) {
            $result = $result[0];
            $customRewardId = isset($result['reward_id']) ? $result['reward_id'] : null;
        }
        if (!$customRewardId) {
            return 0;
            //reward does not exist, add new custom point where id is max(reward_id)+1
            //$this->mongo_db->select(array('reward_id'));
            //$this->mongo_db->where(array(
            //	'client_id' => $input['client_id'],
            //	'site_id' => $input['site_id']
            //));
            //$this->mongo_db->order_by(array('reward_id' => 'desc'));
            //$this->mongo_db->limit(1);
            //$result = $this->mongo_db->get('playbasis_reward_to_client');
            //$result = $result[0];
            //$customRewardId = $result['reward_id'] + 1;
            //if($customRewardId < CUSTOM_POINT_START_ID)
            //	$customRewardId = CUSTOM_POINT_START_ID;

            /*$field1 = array(
                'field_type' => 'read_only',
                'label' => 'Name',
                'param_name' => 'reward_name',
                'placeholder' => '',
                'sortOrder' => '0',
                'value' => strtolower($rewardName)
            );

            $field2 = array(
                'field_type' => 'hidden',
                'label' => '',
                'param_name' => 'item_id',
                'placeholder' => '',
                'sortOrder' => '0',
                'value' => ''
            );

            $field3 = array(
                'field_type' => 'number',
                'label' => strtolower($rewardName),
                'param_name' => 'quantity',
                'placeholder' => 'How many ...',
                'sortOrder' => '0',
                'value' => '0'
            );

            $init_dataset = array($field1,$field2,$field3);

            $customRewardId = new MongoId();
            $mongoDate = new MongoDate(time());
            $this->mongo_db->insert('playbasis_reward_to_client', array(
                'reward_id' => $customRewardId,
                'client_id' => $input['client_id'],
                'site_id' => $input['site_id'],
                'group' => 'POINT',
                'name' => strtolower($rewardName),
                'limit' => null,
                'description' => null,
                'init_dataset' => $init_dataset,
                'sort_order' => 1,
                'status' => true,
                'is_custom' => true,
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate
            ));*/
        }
        $level = 0;
        if ($rewardName == 'exp') {
            $level = $this->updateExpAndLevel($quantity, $input['pb_player_id'], $input['player_id'], array(
                'client_id' => $input['client_id'],
                'site_id' => $input['site_id']
            ));
        } else {
            //update player reward
            $this->updatePlayerPointReward($customRewardId, $quantity, $input['pb_player_id'], $input['player_id'],
                $input['client_id'], $input['site_id'], $anonymous);
        }
        $jigsawConfig['reward_id'] = $customRewardId;
        $jigsawConfig['reward_name'] = $rewardName;
        $jigsawConfig['quantity'] = $quantity;
        return $level;
    }

    public function updateplayerBadge($badgeId, $quantity, $pbPlayerId, $clPlayerId, $client_id, $site_id)
    {
        assert(isset($badgeId));
        assert(isset($quantity));
        assert(isset($pbPlayerId));

        //update badge master table
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'substract',
            'quantity',
            'per_user'
        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'badge_id' => $badgeId,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_badge_to_client');
        if (!$result) {
            return;
        }

        $this->mongo_db->select(array(
            'value'
        ));
        $this->mongo_db->where(array(
            'pb_player_id' => $pbPlayerId,
            'badge_id' => $badgeId
        ));
        $this->mongo_db->limit(1);
        $rewardInfo = $this->mongo_db->get('playbasis_reward_to_player');

        $badgeInfo = $result[0];
        $mongoDate = new MongoDate(time());
        if (isset($badgeInfo['substract']) && $badgeInfo['substract']) {
            //Adjust quantity with per_user
            if (isset($rewardInfo[0])) {
                $rewardInfo = $rewardInfo[0];
                if((isset($badgeInfo['per_user'])&&!is_null($badgeInfo['per_user'])) && ($rewardInfo['value'] + $quantity) > $badgeInfo['per_user']){
                    $quantity = abs($badgeInfo['per_user'] - $rewardInfo['value']);
                }
            }
            else{
                if((isset($badgeInfo['per_user'])&&!is_null($badgeInfo['per_user'])) && ($quantity > $badgeInfo['per_user'])){
                    $quantity = $badgeInfo['per_user'];
                }
            }

            if(!is_null($badgeInfo['quantity'])){
                $remainingQuantity = (int)$badgeInfo['quantity'] - (int)$quantity;
                if ($remainingQuantity < 0) {
                    $remainingQuantity = 0;
                    $quantity = $badgeInfo['quantity'];
                }
                $this->mongo_db->set('quantity', $remainingQuantity);
            }

            $this->mongo_db->set('date_modified', $mongoDate);
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('badge_id', $badgeId);
            $this->mongo_db->update('playbasis_badge_to_client');
        }

        //update player badge table
        if ($rewardInfo) {
            $this->mongo_db->where(array(
                'pb_player_id' => $pbPlayerId,
                'badge_id' => $badgeId
            ));
            $this->mongo_db->set('date_modified', $mongoDate);
            $this->mongo_db->inc('value', intval($quantity));
            $this->mongo_db->update('playbasis_reward_to_player');
        } else {
            $data = array(
                'pb_player_id' => $pbPlayerId,
                'cl_player_id' => $clPlayerId,
                'client_id' => $client_id,
                'site_id' => $site_id,
                'badge_id' => $badgeId,
                'redeemed' => 0,
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate
            );
            $data['value'] = intval($quantity);
            $this->mongo_db->insert('playbasis_reward_to_player', $data);
        }
    }

    public function updateExpAndLevel($exp, $pb_player_id, $cl_player_id, $clientData)
    {
        //assert($exp);
        assert($pb_player_id);
        assert($clientData);
        assert(is_array($clientData));
        assert(isset($clientData['client_id']));
        assert(isset($clientData['site_id']));
        //get player exp
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array(
            'exp',
            'level'
        ));
        $this->mongo_db->where('_id', $pb_player_id);
        $result = $this->mongo_db->get('playbasis_player');
        $playerExp = $result[0]['exp'];
        $playerLevel = $result[0]['level'];
        $newExp = $exp + $playerExp;
        //check if client have their own exp table setup
        $this->mongo_db->select(array('level'));
        $this->mongo_db->where($clientData);
        $this->mongo_db->where_lte('exp', intval($newExp));
        $this->mongo_db->order_by(array('level' => 'desc'));
        $this->mongo_db->limit(1);
        $level = $this->mongo_db->get('playbasis_client_exp_table');
        if (!$level || !$level[0] || !$level[0]['level']) {
            //get level from default exp table instead
            $this->mongo_db->select(array('level'));
            $this->mongo_db->where_lte('exp', intval($newExp));
            $this->mongo_db->order_by(array('level' => 'desc'));
            $this->mongo_db->limit(1);
            $level = $this->mongo_db->get('playbasis_exp_table');
            assert($level);
        }
        $level = $level[0];
        if ($level['level'] && $level['level'] > $playerLevel) {
            $level = $level['level'];
        } else {
            $level = -1;
        }
        $this->mongo_db->where('_id', $pb_player_id);
        $this->mongo_db->set('date_modified', new MongoDate(time()));
        $this->mongo_db->inc('exp', intval($exp));
        if ($level > 0) {
            $this->mongo_db->set('level', intval($level));
        }
        $this->mongo_db->update('playbasis_player');
        //get reward id to update the reward to player table
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', 'exp');
        $result = $this->mongo_db->get('playbasis_reward');
        assert($result);
        $result = $result[0];
        $this->updatePlayerPointReward($result['_id'], $newExp, $pb_player_id, $cl_player_id, $clientData['client_id'],
            $clientData['site_id'], true);
        return $level;
    }

    public function log($logData, $jigsawOptionData = array())
    {
        assert($logData);
        assert(is_array($logData));
        assert($logData['pb_player_id']);
        assert($logData['action_id']);
        assert(is_string($logData['action_name']));
        assert($logData['client_id']);
        assert($logData['site_id']);
        assert(is_string($logData['site_name']));
        if (isset($logData['input'])) //			$logData['input'] = serialize(array_merge($logData['input'], $jigsawOptionData));
        {
            $logData['input'] = array_merge($logData['input'], $jigsawOptionData);
        } else {
            $logData['input'] = 'NO-INPUT';
        }
        $this->set_site_mongodb($logData['site_id']);
        $mongoDate = new MongoDate(time());
        $this->mongo_db->insert('jigsaw_log', array(
            'pb_player_id' => $logData['pb_player_id'],
            'input' => $logData['input'],
            'client_id' => $logData['client_id'],
            'site_id' => $logData['site_id'],
            'site_name' => $logData['site_name'],
            'action_log_id' => (isset($logData['action_log_id'])) ? $logData['action_log_id'] : 0,
            'action_id' => (isset($logData['action_id'])) ? $logData['action_id'] : 0,
            'action_name' => (isset($logData['action_name'])) ? $logData['action_name'] : '',
            'rule_id' => (isset($logData['rule_id'])) ? $logData['rule_id'] : 0,
            'rule_name' => (isset($logData['rule_name'])) ? $logData['rule_name'] : '',
            'jigsaw_id' => (isset($logData['jigsaw_id'])) ? $logData['jigsaw_id'] : 0,
            'jigsaw_name' => (isset($logData['jigsaw_name'])) ? $logData['jigsaw_name'] : '',
            'jigsaw_category' => (isset($logData['jigsaw_category'])) ? $logData['jigsaw_category'] : '',
            'jigsaw_index' => (isset($logData['jigsaw_index'])) ? $logData['jigsaw_index'] : '',
            'site_name' => (isset($logData['site_name'])) ? $logData['site_name'] : '',
            'date_added' => (isset($logData['rule_time'])) ? $logData['rule_time'] : $mongoDate,
            'date_modified' => $mongoDate
        ), array("w" => 0, "j" => false));
    }

    public function getBadgeById($badgeId, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'badge_id',
            'name',
            'description',
            'image',
            'hint',
            'tags'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'site_id' => $site_id,
            'badge_id' => $badgeId,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $badge = $this->mongo_db->get('playbasis_badge_to_client');
        if (!$badge) {
            return null;
        }
        $badge = $badge[0];
        $badge['badge_id'] = $badge['badge_id'] . "";
        $badge['image'] = $this->config->item('IMG_PATH') . $badge['image'];
        return $badge;
    }

    public function updateplayerGoods(
        $goodsId,
        $quantity,
        $pbPlayerId,
        $clPlayerId,
        $client_id,
        $site_id,
        $is_sponsor = false
    ) {
        assert(isset($goodsId));
        assert(isset($quantity));
        assert(isset($pbPlayerId));
        //update badge master table
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'group',
            'quantity',
            'date_expired_coupon',
            'days_expire'
        ));
        $this->mongo_db->where(array(
            //'client_id' => $is_sponsor ? null : $client_id,
            'site_id' => $is_sponsor ? null : new MongoId($site_id),
            'goods_id' => new MongoId($goodsId),
            'deleted' => false
        ));
        $this->mongo_db->where('$or',  array(array('date_expired_coupon' => array('$exists' => false)), array('date_expired_coupon' => array('$gt' => new MongoDate()))));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_goods_to_client');
        if (!$result) {
            return;
        }
        $goodsInfo = $result[0];
        $mongoDate = new MongoDate();

        if (!is_null($goodsInfo['quantity'])) {
            $remainingQuantity = $goodsInfo['quantity'] - $quantity;
        } else {
            $remainingQuantity = null;
        }

        /*
        if($remainingQuantity < 0)
        {
            $remainingQuantity = 0;
            $quantity = $goodsInfo['quantity'];
        }
        */

        // NEW -->
        if (is_null($remainingQuantity)) {
            $remainingQuantity = null;
            // $quantity = $goodsInfo['quantity'];
        } elseif ($remainingQuantity < 0) {
            throw new Exception('GOODS_NOT_ENOUGH');
        }
        // END NEW -->
        $this->mongo_db->set('quantity', $remainingQuantity);
        $this->mongo_db->set('date_modified', $mongoDate);
        $this->mongo_db->where('client_id', $is_sponsor ? null : new MongoId($client_id));
        $this->mongo_db->where('site_id', $is_sponsor ? null : new MongoId($site_id));
        $this->mongo_db->where('goods_id', new MongoId($goodsId));
        $this->mongo_db->where('$or',  array(array('date_expired_coupon' => array('$exists' => false)), array('date_expired_coupon' => array('$gt' => new MongoDate()))));
        $this->mongo_db->update('playbasis_goods_to_client', array("w" => 0, "j" => false));

        //update player badge table
        $this->mongo_db->where(array(
            'pb_player_id' => new MongoId($pbPlayerId),
            'goods_id' => new MongoId($goodsId)
        ));
        $hasBadge = $this->mongo_db->count('playbasis_goods_to_player');
        if ($hasBadge) {
            $this->mongo_db->where(array(
                'pb_player_id' => new MongoId($pbPlayerId),
                'goods_id' => new MongoId($goodsId)
            ));
            $this->mongo_db->set('date_modified', $mongoDate);
            $this->mongo_db->inc('value', intval($quantity));
            $this->mongo_db->update('playbasis_goods_to_player', array("w" => 0, "j" => false));
        } else {
            $data = array(
                'pb_player_id' => new MongoId($pbPlayerId),
                'cl_player_id' => $clPlayerId,
                'client_id' => new MongoId($client_id),
                'site_id' => new MongoId($site_id),
                'goods_id' => new MongoId($goodsId),
                'group' => isset($goodsInfo['group']) ? $goodsInfo['group'] : "",
                'is_sponsor' => $is_sponsor,
                'value' => intval($quantity),
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate
            );
            if(isset($goodsInfo['date_expired_coupon']) && !empty($goodsInfo['date_expired_coupon'])){
                $data['date_expire'] = ($goodsInfo['date_expired_coupon']);
            } elseif (isset($goodsInfo['days_expire']) && !empty($goodsInfo['days_expire'])) {
                $data['date_expire'] = new MongoDate(strtotime("+".$goodsInfo['days_expire']. ' day'));
            }
            $this->mongo_db->insert('playbasis_goods_to_player', $data, array("w" => 0, "j" => false));
        }
    }

    public function getRewardName($input)
    {
        $this->set_site_mongodb($input['site_id']);
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where(array(
            'client_id' => $input['client_id'],
            'site_id' => $input['site_id'],
            'reward_id' => new MongoId($input['reward_id'])
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0]['name'] : null;
    }

    public function getById($client_id)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array('first_name', 'last_name', 'email'));
        $this->mongo_db->where(array('_id' => $client_id));
        $ret = $this->mongo_db->get('playbasis_client');
        return is_array($ret) && count($ret) == 1 ? $ret[0] : $ret;
    }

    /*
     * Get Client date_start and date_expire
     * this is monthly billing date
     * @param client_id string
     * @return array of date_start and date_expire
     */
    public function getClientStartEndDate($client_id)
    {
        $this->mongo_db->select(array("date_start", "date_expire"));
        $this->mongo_db->where(array("_id" => new MongoID($client_id)));
        $result = $this->mongo_db->get("playbasis_client");

        if ($result) {
            $result = $result[0];
            if (!isset($result["date_start"])) {
                $result["date_start"] = null;
            }
            if (!isset($result["date_expire"])) {
                $result["date_expire"] = null;
            }
            return $result;
        } else {
            return array("date_start" => null, "date_expire" => null);
        }
    }

    public function getFreeClientStartEndDate($client_id)
    {
        $this->mongo_db->select(array("date_start"));
        $this->mongo_db->where(array("_id" => new MongoID($client_id)));
        $result = $this->mongo_db->get("playbasis_client");
        if (!$result || !isset($result[0]['date_start'])) {
            return array("date_start" => null, "date_expire" => null);
        }
        $init_date_start = $result[0]['date_start']->sec;
        $init_date_expire = strtotime("+1 month", $init_date_start);
        $curr = $init_date_expire;
        $today = time();
        while ($curr < $today) {
            $curr = strtotime("+1 month", $curr);
        }
        return array(
            'date_start' => new MongoDate(strtotime("-1 month", $curr)),
            'date_expire' => new MongoDate($curr)
        );
    }

    public function adjustCurrentUsageDate($date_start)
    {
        if (!$date_start) {
            return array("date_start" => null, "date_expire" => null);
        }
        $init_date_start = $date_start->sec;
        $init_date_expire = strtotime("+1 month", $init_date_start);
        $curr = $init_date_expire;
        $today = time();
        while ($curr < $today) {
            $curr = strtotime("+1 month", $curr);
        }
        return array(
            'date_start' => new MongoDate(strtotime("-1 month", $curr)),
            'date_expire' => new MongoDate($curr)
        );
    }

    /**
     * Return Permission limitation by Plan ID
     * in particular type and field
     * e.g. notifications email
     * @param site_id string
     * @param plan_id string
     * @param type notifications | requests | others
     * @param field string
     * @return integer | null
     */
    public function getPlanLimitById($plan, $type, $field = null)
    {
        $res = $plan;
        if ($res) {
            $limit = 'limit_' . $type;
            if (isset($res[$limit])) {
                if ($field) {
                    return isset($res[$limit][$field]) ? $res[$limit][$field] : null;
                } else {
                    return $res[$limit];
                }
            } else { // this plan does not set this limitation
                return null;
            }
        } else {
            throw new Exception("PLANID_NOTFOUND");
        }
    }

    /**
     * Return usage of service from client-site
     * in particular type and field
     * e.g. notifications email
     * If Client doesn't has Billing cyle return 0
     * @param type notifications | requests | others
     * @param field string
     * @param client_data array('usage' => , 'date' => array('date_start' => MongoDate(), 'date_expire' => MongoDate()))
     * @return array('plan_id' => string, 'value' => integer) | null
     */
    public function getPermissionUsage($type, $field, $client_data)
    {
        if (!in_array($type, array("notifications", "requests", "others"))) {
            throw new Exception("WRONG_TYPE");
        }

        $result = array();
        $res = $client_data['usage'];
        if ($res) {
            $result["plan_id"] = $res["plan_id"];

            // Sync current bill usage with Client bill
            try {
                $this->syncPermissionDate($client_data['date'], $res);
                // check this limitation on this client-site
                if (isset($res['usage'][$type]) && isset($res['usage'][$type][$field])) {
                    $result["value"] = $res['usage'][$type][$field];
                } else // this limitation is not found in database
                {
                    $result["value"] = 0;
                }
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if ($msg == "NOEXPIRE" || $msg == "NOTSYNC") {
                    $result["value"] = 0;
                }
            }

            return $result;
        } else { // client-site is not found
            throw new Exception("CLIENTSITE_NOTFOUND");
        }
    }

    /**
     * Update Permission service usage
     * in particular type and field
     * e.g. notifications email
     * @param client_id string
     * @param site_id string
     * @param type notifications | requests | others
     * @param field string
     */
    private function updatePermission(
        $client_id,
        $site_id,
        $type,
        $field,
        $inc = 1
    ) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $this->mongo_db->inc("usage." . $type . "." . $field, $inc);
        $this->mongo_db->update('playbasis_permission', array("w" => 0, "j" => false));
    }

    /*
     * Sync Permission billing date with Client billing date
     * @param array @clientDate
     * @param array @permissionDate
     * @throw NOTSYNC | NOEXPIRE
     */
    private function syncPermissionDate($clientDate, $permissionDate)
    {
        // Not has Date no limitation
        if ((!array_key_exists('date_start',
                    $clientDate) || !$clientDate["date_start"]) && (!array_key_exists('date_expire',
                    $clientDate) || !$clientDate["date_expire"])
        ) {
            throw new Exception("NOEXPIRE");
        }

        // Date is not sync
        if ((!array_key_exists('date_start',
                    $permissionDate) || $clientDate["date_start"] != $permissionDate["date_start"]) ||
            (!array_key_exists('date_expire',
                    $permissionDate) || $clientDate["date_expire"] != $permissionDate["date_expire"])
        ) {
            $this->mongo_db->where(array("_id" => $permissionDate["_id"]));

            // Update date & Reset usage
            $this->mongo_db->set(array(
                "date_start" => $clientDate["date_start"],
                "date_expire" => $clientDate["date_expire"]
            ));
            $this->mongo_db->unset_field("usage");
            $this->mongo_db->update("playbasis_permission");
            throw new Exception("NOTSYNC");
        }
    }

    /*
     * Check & Update permission usage
     * Always update if limit is not exceed
     * @param client_data array('usage' => , 'date' => array('date_start' => MongoDate(), 'date_expire' => MongoDate()), 'plan' => )
     * @param string $client_id
     * @param string $site_id
     * @param (notifications | requests | others) $type
     * @particular string $field
     */
    public function permissionProcess($client_data, $client_id, $site_id, $type, $field, $inc = 1)
    {
        // get current usage
        $usage = $this->getPermissionUsage($type, $field, $client_data);

        // get limit by plan
        $limit = $this->getPlanLimitById($client_data['plan'], $type, $field);

        // compare
        if ($limit !== null && ($usage["value"] >= $limit || $usage["value"] + $inc > $limit)) {
            // no permission to use this service
            throw new Exception("LIMIT_EXCEED");
        } else {  // increase service usage
            $this->updatePermission($client_id, $site_id, $type, $field, $inc);
        }
    }

    /*
     * Check permission usage
     * @param client_data array('usage' => , 'date' => array('date_start' => MongoDate(), 'date_expire' => MongoDate()), 'plan' => )
     * @param string $client_id
     * @param string $site_id
     * @param (notifications | requests | others) $type
     * @particular string $field
     */
    public function permissionCheck($client_data, $client_id, $site_id, $type, $field)
    {
        // get current usage
        $usage = $this->getPermissionUsage($type, $field, $client_data);

        // get limit by plan
        $limit = $this->getPlanLimitById($client_data['plan'], $type, $field);

        // compare
        if ($limit !== null && $usage["value"] >= $limit) {
            // no permission to use this service
            throw new Exception("LIMIT_EXCEED");
        }
    }

    public function getClientSiteUsage($client_id, $site_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_permission');
        if ($results) {
            return $results[0];
        }
        throw new Exception("CLIENTSITE_NOTFOUND");
    }

    public function insertRuleUsage($client_id, $site_id, $rule_id, $pb_player_id, $value)
    {
        $this->set_site_mongodb($site_id);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_rule_log', array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'rule_id' => $rule_id,
            'pb_player_id' => $pb_player_id,
            'value' => $value,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate,
        ), array("w" => 0, "j" => false));
    }

    public function hasSetUpMobile($client_id, $site_id = 0)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', $client_id);
        $this->mongo_db->where(array('status' => true, 'deleted' => false));
        $this->mongo_db->where('mobile', array('$regex' => new MongoRegex("/^\+[0-9]+/")));
        return $this->mongo_db->count('playbasis_client') > 0;
    }

    public function findActiveSites($client_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where(array('status' => true, 'deleted' => false));
        return $this->mongo_db->get('playbasis_client_site');
    }

    public function getPlanById($plan_id)
    {
        $this->mongo_db->where(array('_id' => $plan_id));
        $ret = $this->mongo_db->get('playbasis_plan');
        $plan = is_array($ret) && count($ret) == 1 ? $ret[0] : $ret;
        if ($plan && !array_key_exists('price', $plan)) {
            $plan['price'] = DEFAULT_PLAN_PRICE;
        }
        return $plan;
    }

    public function getPlanIdByClientId($client_id)
    {
        $permission = $this->getLatestPermissionByClientId($client_id);
        return $permission ? $permission['plan_id'] : null;
    }

    public function getLatestPermissionByClientId($client_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_permission');
        return $results ? $results[0] : null;
    }

    public function findBySiteId($site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', $site_id);
        $result = $this->mongo_db->get('playbasis_client_site');
        return $result ? $result[0] : array();
    }

    public function findSiteNameBySiteId($site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', $site_id);
        $result = $this->mongo_db->get('playbasis_client_site');
        return $result ? $result[0]['site_name'] : "";
    }

    public function checkFeatureByFeatureName($clientData, $featureName)
    {
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            'name' => $featureName
        ));
        $this->mongo_db->limit(1);
        $id = $this->mongo_db->get('playbasis_feature_to_client');

        if ($id) {
            return true;
        } else {
            return false;
        }
    }
}

?>
