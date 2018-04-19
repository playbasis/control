<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_model extends MY_Model
{
    public function getGoods($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods");

        return $results ? $results[0] : null;
    }

    public function getGoodsToClient($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0] : null;
    }

    public function getGoodsToPlayer($_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($_id));
        $results = $this->mongo_db->get("playbasis_goods_to_player");

        return $results ? $results[0] : null;
    }

    public function getPlayerGoodsById($site_id, $goodsId, $pb_player_id)
    {
        $this->mongo_db->select(array('value','gifted'));
        $this->mongo_db->where(array(
            'site_id' => $site_id,
            'goods_id' => $goodsId,
            'pb_player_id' => $pb_player_id
        ));
        $this->mongo_db->limit(1);
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return isset($goods[0]) ? $goods[0] : null;
    }

    public function getPlayerGoodsModifiedDateById($site_id, $goodsId, $pb_player_id)
    {
        $this->mongo_db->select(array('date_modified'));
        $this->mongo_db->where(array(
            'site_id' => $site_id,
            'goods_id' => $goodsId,
            'pb_player_id' => $pb_player_id
        ));
        $this->mongo_db->limit(1);
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return isset($goods[0]) ? $goods[0]['date_modified'] : null;
    }


    public function getPlayerGoods($site_id, $date_start, $date_end, $data=array())
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getPlayerGoodsUsed($site_id, $date_start, $date_end, $data=array())
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where('value',0);
        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getPlayerGoodsGifted($site_id, $date_start, $date_end, $data=array())
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where('gifted',true);
        $this->mongo_db->where('value',0);
        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getPlayerGoodsActive($site_id, $date_start, $date_end, $data=array())
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where_gt('value',0);
        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getGoodsOfClientPrivate($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id', new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0] : null;
    }

    public function getGoodslistOfClientPrivate($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where_in('goods_id', $goods_id);
        return $this->mongo_db->get("playbasis_goods_to_client");
    }
    
    public function getGoodsIDByName($client_id, $site_id, $good_name, $good_group=null, $check_status=true)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('goods_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        
        $this->mongo_db->where('deleted', false);
        if($check_status){
            $this->mongo_db->where('status', true);
        }

        if($good_group){
            $this->mongo_db->where('group', $good_group);
        }else{
            $this->mongo_db->where('name', $good_name);
        }
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0]['goods_id']."" : null;
    }

    public function getGoodsByName($client_id, $site_id, $good_name, $good_group=null, $check_status=true)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('deleted', false);
        if($check_status){
            $this->mongo_db->where('status', true);
        }

        if($good_group){
            $this->mongo_db->where('group', $good_group);
        }else{
            $this->mongo_db->where('name', $good_name);
            $this->mongo_db->where('group', array('$exists' => false));
        }
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0] : null;
    }

    public function getGoodsList($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_goods");
    }

    public function getTotalGoods($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->count("playbasis_goods");
    }

    public function getGoodsBySiteId($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where(array('$or' => array(array('group', array('$exists' => false) , 'name', $regex),
                                                        array('group', array('$exists' => true), 'group', $regex))));
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', new MongoId($data['site_id']));

        if(array_key_exists('specific', $data)){
            $this->mongo_db->where($data['specific']);
        }
        if (array_key_exists('$nin', $data)) {
            $this->mongo_db->where_not_in('_id', $data['$nin']);
        }
        if (array_key_exists('$in', $data)) {
            $this->mongo_db->where_in('_id', $data['$in']);
        }
        return $this->mongo_db->get("playbasis_goods_to_client");
    }

    public function getTotalGoodsBySiteId($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where(array('$or' => array(array('group', array('$exists' => false) , 'name', $regex),
                                                        array('group', array('$exists' => true), 'group', $regex))));
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id'] ? new MongoId($data['site_id']) : null);
        
        if(array_key_exists('specific', $data)){
            $this->mongo_db->where($data['specific']);
        }
        
        if (array_key_exists('$nin', $data)) {
            $this->mongo_db->where_not_in('_id', $data['$nin']);
        }
        if (array_key_exists('$in', $data)) {
            $this->mongo_db->where_in('_id', $data['$in']);
        }
        return $this->mongo_db->count("playbasis_goods_to_client");
    }

    /* Deprecated: use getGroupsAggregate instead */
    public function getGroups($site_id)
    {
        $this->mongo_db->select(array('group', 'quantity'));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_exists('group', true);
        $results = $this->mongo_db->get("playbasis_goods_to_client");
        $groups = array();
        if ($results) {
            foreach ($results as $result) {
                $name = $result['group'];
                if (array_key_exists($name, $groups)) {
                    $groups[$name][] = array('_id' => $result['_id'], 'quantity' => $result['quantity']);
                } else {
                    $groups[$name] = array(array('_id' => $result['_id'], 'quantity' => $result['quantity']));
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
            )
        );
        return $results ? $results['result'] : array();
    }

    public function getGroupsList($site_id, $data=array())
    {
        $this->mongo_db->select(array('name','is_group'));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        
        if(isset($data['filter_goods']) && $data['filter_goods']){
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_goods'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if(isset($data['filter_status']) && !is_null($data['filter_status'])){
            $this->mongo_db->where('status', $data['filter_status']);
        }
        if(isset($data['filter_tags']) && $data['filter_tags']){
            $tags = explode(',', $data['filter_tags']);
            $this->mongo_db->where_in('tags', $tags);
        }
        if(isset($data['filter_custom_key']) && $data['filter_custom_key']){
            if(isset($data['filter_custom_val']) && isset($data['filter_custom_key'])){
                $this->mongo_db->where('custom_param', array('$elemMatch' => array('key' => $data['filter_custom_key'] , 'value' => $data['filter_custom_val'])));
            } else {
                $this->mongo_db->where('custom_param', array('$elemMatch' => array('key' => $data['filter_custom_key'])));
            }

        }
        if(isset($data['filter_group']) && !is_null($data['filter_group'])){
            $this->mongo_db->where('is_group', $data['filter_group']);
        }

        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get('playbasis_goods_distinct_to_client');
    }

    public function getGoodsDistinctByName($site_id, $goods_name, $is_group=null)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('name', $goods_name);
        $this->mongo_db->where('deleted', false);
        if(!is_null($is_group)){
            $this->mongo_db->where('is_group', $is_group);
        }
        return $this->mongo_db->get('playbasis_goods_distinct_to_client');
    }

    public function getGoodsDistinctByID($site_id, $distinct_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($distinct_id));

        $result =  $this->mongo_db->get('playbasis_goods_distinct_to_client');
        return isset($result[0]) ? $result[0] : null;
    }

    public function getGoodsDistinctID($site_id, $goods_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($goods_id));

        $result = $this->mongo_db->get('playbasis_goods_to_client');
        return isset($result[0]['distinct_id']) ? $result[0]['distinct_id'] : null;
    }

    public function checkGoodsWhiteList($site_id, $distinct_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($distinct_id));
        
        $result = $this->mongo_db->get('playbasis_goods_distinct_to_client');
        return isset($result[0]['whitelist_enable']) ? $result[0]['whitelist_enable'] : false;
    }

    public function addGoodsDistinct($data, $is_group)
    {
        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $data_insert = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'name' => $data['name'],
            'is_group' => $is_group,
            'deleted' => false,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user'])) ? (int)$data['per_user'] : null,
            'per_user_include_inactive'=> isset($data['per_user_include_inactive']) ? $data['per_user_include_inactive'] : false,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'description' => $data['description'] | '',
            'language_id' => (int)1,
            'redeem' => $data['redeem'],
            'tags' => isset($tags) ? $tags : null,
            'sponsor' => isset($data['sponsor']) ? $data['sponsor'] : false,
            'date_start' => null,
            'date_expire' => null,
            'custom_param' => isset($data['custom_param']) ? $data['custom_param'] : array(),
            'batch_name' => array(),
            'whitelist_enable' => isset($data['whitelist_enable']) ? $data['whitelist_enable'] : false,
        );
        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $data_insert['date_start'] = new MongoDate($date_start_another);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $data_insert['date_start'] = new MongoDate($date_start_another);
            }
            if (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        if (isset($data['date_expired_coupon']) && $data['date_expired_coupon']){
            $data_insert['date_expired_coupon'] = new MongoDate(strtotime($data['date_expired_coupon']));
        }

        if (isset($data['organize_id'])) {
            $data_insert['organize_id'] = new MongoID($data['organize_id']);
        }

        if (isset($data['organize_role'])) {
            $data_insert['organize_role'] = $data['organize_role'];
        }
        return $this->mongo_db->insert('playbasis_goods_distinct_to_client', $data_insert);
    }

    public function editGoodsDistinct($site_id, $goods_name, $data)
    {
        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }
        
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('name', $goods_name);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('per_user_include_inactive', isset($data['per_user_include_inactive']) ? $data['per_user_include_inactive'] : false);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
        $this->mongo_db->set('custom_param', isset($data['custom_param']) ? $data['custom_param'] : 'off');
        $this->mongo_db->set('whitelist_enable', isset($data['whitelist_enable']) ? $data['whitelist_enable'] : false);

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', null);
            } elseif (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            } else {
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', null);
            }
        }

        if (isset($data['days_expire'])){
            $this->mongo_db->set('days_expire', $data['days_expire'] ? $data['days_expire'] : null);
        }

        if (isset($data['date_expired_coupon']) && $data['date_expired_coupon']){
            $this->mongo_db->set('date_expired_coupon', new MongoDate(strtotime($data['date_expired_coupon'])) );
        }else{
            $this->mongo_db->unset_field('date_expired_coupon');
        }

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }

        $this->mongo_db->set('organize_id', isset($data['organize_id']) ? new MongoID($data['organize_id']) : null);
        $this->mongo_db->set('organize_role', isset($data['organize_role']) ? $data['organize_role'] : null);

        $this->mongo_db->update('playbasis_goods_distinct_to_client');
    }

    public function deleteGoodsDistinct($site_id, $goods_name)
    {
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('name', $goods_name);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_goods_distinct_to_client');
    }

    public function setGoodsWhiteList($client_id, $site_id, $distinct_id, $whitelist_data)
    {
        $result = $this->mongo_db->batch_insert('playbasis_goods_white_list_to_player', $whitelist_data['cl_player_id_list']);

        $data_insert = array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'distinct_id' => new MongoId($distinct_id),
            'deleted' => false,
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'file_name' => $whitelist_data['file_name'],
            'file_content' => $whitelist_data['file_content']
        );

        return $this->mongo_db->insert('playbasis_goods_white_list', $data_insert);
    }

    public function getGoodsWhiteList($client_id, $site_id, $distinct_id)
    {
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'distinct_id' => new MongoId($distinct_id),
            'deleted' => false
        ));

        $this->mongo_db->limit(1);

        $result = $this->mongo_db->get('playbasis_goods_white_list');

        return isset($result[0]) ? $result[0] : null;
    }

    public function deleteGoodsWhiteList($client_id, $site_id, $distinct_id)
    {
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'distinct_id' => new MongoId($distinct_id),
        ));
        $this->mongo_db->delete_all("playbasis_goods_white_list_to_player");

        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'distinct_id' => new MongoId($distinct_id),
            'deleted' => false
        ));

        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        return $this->mongo_db->update('playbasis_goods_white_list');
    }

    public function retrieveWhiteListFile($client_id, $site_id, $distinct_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('distinct_id', new MongoId($distinct_id));
        $this->mongo_db->where('deleted', false);

        $result = $this->mongo_db->get('playbasis_goods_white_list');

        return $result ? $result[0] : null;
    }

    public function checkExists($site_id, $goods, $distinct_id=null)
    {
        if(!is_null($distinct_id)){
            $this->mongo_db->where_ne('_id', new MongoId($distinct_id));
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('name', $goods);
        return $this->mongo_db->count("playbasis_goods_distinct_to_client") > 0;
    }

    public function getGoodBatchByDistinctID($client_id, $site_id, $distinct_id)
    {
        $this->mongo_db->where('_id', new MongoId($distinct_id));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $result = $this->mongo_db->get("playbasis_goods_distinct_to_client");
        return isset($result[0]['batch_name']) ? $result[0]['batch_name'] : array();
    }

    public function checkGoodsGroupQuantity($site_id, $group)
    {
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('quantity', 1);
        return $this->mongo_db->count("playbasis_goods_to_client");
    }

    public function getAvailableGoodsByGroup($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name', 'code', 'goods_id', 'date_start', 'date_expire', 'date_expired_coupon','batch_name'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if(isset($data['filter_goods'])){
            $this->mongo_db->where('goods_id', new MongoId($data['filter_goods']));
        }

        if(isset($data['filter_batch'])){
            $this->mongo_db->where('batch_name', $data['filter_batch']);
        }

        if(isset($data['filter_voucher_code'])){
            $regex_code = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_voucher_code'])) . "/i");
            $this->mongo_db->where('code', $regex_code);
        }

        if (isset($data['filter_date_start']) && !is_null($data['filter_date_start'])) {
            $this->mongo_db->where('date_start', new MongoDate(strtotime($data['filter_date_start'])));
        }

        if(isset($data['filter_date_end']) && !is_null($data['filter_date_end'])){
            $this->mongo_db->where('date_expire', new MongoDate(strtotime($data['filter_date_end'])));
        }

        if(isset($data['filter_date_expire']) && !is_null($data['filter_date_expire'])){
            $this->mongo_db->where('date_expired_coupon', new MongoDate(strtotime($data['filter_date_expire'])));
        }

        $sort_data = array(
            '_id',
            'date_start',
            'date_expire',
            'name',
            'code',
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order, 'name' => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('group', $data['group']);
        $this->mongo_db->where_gt('quantity', 0);

        $results = $this->mongo_db->get("playbasis_goods_to_client");
        if(is_array($results)) foreach ($results as $index => $goods){
            if(isset($goods['date_start'])) $results[$index]['date_start'] = datetimeMongotoReadable($goods['date_start']);
            if(isset($goods['date_expire'])) $results[$index]['date_expire'] = datetimeMongotoReadable($goods['date_expire']);
            if(isset($goods['date_expired_coupon'])) $results[$index]['date_expired_coupon'] = datetimeMongotoReadable($goods['date_expired_coupon']);
        }

        return $results;
    }

    public function getTotalAvailableGoodsByGroup($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if(isset($data['filter_goods'])){
            $this->mongo_db->where('goods_id', new MongoId($data['filter_goods']));
        }
        if(isset($data['filter_batch'])){
            $this->mongo_db->where('batch_name', $data['filter_batch']);
        }
        if(isset($data['filter_voucher_code'])){
            $regex_code = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_voucher_code'])) . "/i");
            $this->mongo_db->where('code', $regex_code);
        }

        if (isset($data['filter_date_start']) && !is_null($data['filter_date_start'])) {
            $this->mongo_db->where('date_start', new MongoDate(strtotime($data['filter_date_start'])));
        }

        if(isset($data['filter_date_end']) && !is_null($data['filter_date_end'])){
            $this->mongo_db->where('date_expire', new MongoDate(strtotime($data['filter_date_end'])));
        }

        if(isset($data['filter_date_expire']) && !is_null($data['filter_date_expire'])){
            $this->mongo_db->where('date_expired_coupon', new MongoDate(strtotime($data['filter_date_expire'])));
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('group', $data['group']);
        $this->mongo_db->where_gt('quantity', 0);

        $total = $this->mongo_db->count("playbasis_goods_to_client");

        return $total;
    }

    public function getAllRedeemedGoods($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name', 'code', 'goods_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $sort_data = array(
            '_id',
            'name',
            'code'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);

        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results;
    }

    public function getTotalRedeemedGoods($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);

        $total = $this->mongo_db->count("playbasis_goods_to_client");

        return $total;
    }

    public function getCommonGoods()
    {

        $results = $this->mongo_db->get("playbasis_goods");

        $goods_list = array();

        if (count($results) > 0) {
            foreach ($results as &$rown) {
                array_push($goods_list, array(
                    "id" => $rown['_id'] . "",
                    "name" => $rown['name'],
                    "img_path" => $rown['image'],
                    "description" => $rown['description']
                ));
            }
        }//end if


        $output = array(
            "goods_list_set_id" => 0,
            "goods_list_customer_id" => 0,
            "goods_list_set" => array(
                "set_label" => "Basic Goods",
                "set_id" => "0",
                "items" => $goods_list
            )
        );

        return $output;
    }

    public function addGoods($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data_insert = array(
            // 'quantity' => (int)$data['quantity']|0 ,
            'quantity' => (isset($data['quantity']) && !empty($data['quantity'])) ? (int)$data['quantity'] : null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user'])) ? (int)$data['per_user'] : null,
            'per_user_include_inactive' => (isset($data['per_user_include_inactive']) && !empty($data['per_user_include_inactive'])) ? (bool)$data['per_user_include_inactive'] : false,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'name' => $data['name'] | '',
            'description' => $data['description'] | '',
            'language_id' => (int)1,
            'redeem' => $data['redeem'],
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? $data['sponsor'] : false,
            'date_start' => null,
            'date_expire' => null,
        );

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $data_insert['date_start'] = new MongoDate($date_start_another);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $data_insert['date_start'] = new MongoDate($date_start_another);
            }
            if (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        if (isset($data['organize_id'])) {
            $data_insert['organize_id'] = new MongoID($data['organize_id']);
        }

        if (isset($data['organize_role'])) {
            $data_insert['organize_role'] = $data['organize_role'];
        }

        $b = $this->mongo_db->insert('playbasis_goods', $data_insert);
        return $b;
    }
    public function auditBeforeCoupon($event,$goods_id, $user_id)
    {
        $goods_data = $this->getGoodsOfClientPrivate($goods_id);
        if ($goods_data && array_key_exists('group', $goods_data)) {
            $goods_data['quantity'] = $this->checkGoodsGroupQuantity($goods_data['site_id'], $goods_data['group']);
        }
        $insert_data = array('client_id' => $goods_data['client_id'],
            'site_id' => $goods_data['site_id'],
            'goods_id' => $goods_data['goods_id'],
            'event' => $event,
            'before' => $goods_data,
            'user_id' => $user_id);
        return $this->mongo_db->insert('playbasis_goods_to_client_audit', $insert_data);
    }

    public function auditAfterCoupon($event, $goods_id, $user_id, $audit_id=null)
    {
        $goods_data = $this->getGoodsOfClientPrivate($goods_id);
        if ($goods_data && array_key_exists('group', $goods_data)) {
            $goods_data['quantity'] = $this->checkGoodsGroupQuantity($goods_data['site_id'], $goods_data['group']);
        }
        $audit_log = array();
        if ($audit_id){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $audit_log = $this->mongo_db->get('playbasis_goods_to_client_audit');
        }

        if ($audit_log){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $this->mongo_db->set('after', $goods_data);
            $this->mongo_db->update('playbasis_goods_to_client_audit');
        } else {
            $insert_data = array('client_id' => $goods_data['client_id'],
                'site_id' => $goods_data['site_id'],
                'goods_id' => $goods_data['goods_id'],
                'event' => $event,
                'before' => null,
                'after' => $goods_data,
                'user_id' => $user_id);
            $this->mongo_db->insert('playbasis_goods_to_client_audit', $insert_data);
        }
    }

    public function auditBeforeGoods($event,$goods_id, $user_id)
    {
        $goods_data = $this->getGoodsToClient($goods_id);
        if ($goods_data && array_key_exists('group', $goods_data)) {
            $goods_data['quantity'] = $this->checkGoodsGroupQuantity($goods_data['site_id'], $goods_data['group']);
        }
        $insert_data = array('client_id' => $goods_data['client_id'],
                             'site_id' => $goods_data['site_id'],
                             'goods_id' => $goods_data['goods_id'],
                             'event' => $event,
                             'before' => $goods_data,
                             'user_id' => $user_id);
        return $this->mongo_db->insert('playbasis_goods_to_client_audit', $insert_data);
    }

    public function auditAfterGoods($event, $goods_id, $user_id, $audit_id=null)
    {
        $goods_data = $this->getGoodsToClient($goods_id);
        if ($goods_data && array_key_exists('group', $goods_data)) {
            $goods_data['quantity'] = $this->checkGoodsGroupQuantity($goods_data['site_id'], $goods_data['group']);
        }
        $audit_log = array();
        if ($audit_id){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $audit_log = $this->mongo_db->get('playbasis_goods_to_client_audit');
        }

        if ($audit_log){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $this->mongo_db->set('after', $goods_data);
            $this->mongo_db->update('playbasis_goods_to_client_audit');
        } else {
            $insert_data = array('client_id' => $goods_data['client_id'],
                                 'site_id' => $goods_data['site_id'],
                                 'goods_id' => $goods_data['goods_id'],
                                 'event' => $event,
                                 'before' => null,
                                 'after' => $goods_data,
                                 'user_id' => $user_id);
            $this->mongo_db->insert('playbasis_goods_to_client_audit', $insert_data);
        }
    }

    public function addGoodsToClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $data_insert = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'goods_id' => new MongoID($data['goods_id']),
            // 'quantity' => (int)$data['quantity']|0 ,
            'quantity' => (isset($data['quantity']) && !empty($data['quantity'])) ? (int)$data['quantity'] : null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user'])) ? (int)$data['per_user'] : null,
            'per_user_include_inactive' => (isset($data['per_user_include_inactive']) && !empty($data['per_user_include_inactive'])) ? (bool)$data['per_user_include_inactive'] : false,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order'] | 1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'name' => $data['name'] | '',
            'description' => $data['description'] | '',
            'language_id' => (int)1,
            'redeem' => $data['redeem'],
            'code' => $data['code'] | '',
            'tags' => isset($tags) ? $tags : null,
            'deleted' => false,
            'sponsor' => isset($data['sponsor']) ? $data['sponsor'] : false,
            'date_start' => null,
            'date_expire' => null,
            'custom_param' => isset($data['custom_param']) ? $data['custom_param'] : array(),
            'distinct_id' => isset($data['distinct_id']) ? $data['distinct_id'] : null,
        );

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $data_insert['date_start'] = new MongoDate($date_start_another);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $data_insert['date_start'] = new MongoDate($date_start_another);
            }
            if (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        if (isset($data['date_expired_coupon']) && $data['date_expired_coupon']){
            $data_insert['date_expired_coupon'] = new MongoDate(strtotime($data['date_expired_coupon'])) ;
        }

        if (isset($data['organize_id'])) {
            $data_insert['organize_id'] = new MongoID($data['organize_id']);
        }

        if (isset($data['organize_role'])) {
            $data_insert['organize_role'] = $data['organize_role'];
        }

        return $this->mongo_db->insert('playbasis_goods_to_client', $data_insert);
    }

    public function addGoodsToClient_bulk($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        return $this->mongo_db->batch_insert('playbasis_goods_to_client', $data);
    }

    public function editGoods($goods_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        // $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('quantity',
            (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null);
        // $this->mongo_db->set('per_user', (int)$data['per_user']);
        $this->mongo_db->set('per_user',
            (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('per_user_include_inactive', isset($data['per_user_include_inactive']) ? $data['per_user_include_inactive'] : false);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);

        if (isset($data['sponsor'])) {
            $this->mongo_db->set('sponsor', (bool)$data['sponsor']);
        } else {
            $this->mongo_db->set('sponsor', false);
        }

        if (isset($data['organize_id'])) {
            $this->mongo_db->set('organize_id', new MongoID($data['organize_id']));
        } else {
            $this->mongo_db->set('organize_id', null);
        }

        if (isset($data['organize_role'])) {
            $this->mongo_db->set('organize_role', $data['organize_role']);
        } else {
            $this->mongo_db->set('organize_role', null);
        }

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', null);
            }
            elseif (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
            else {
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', null);
            }
        }

        $this->mongo_db->update('playbasis_goods');

        if (isset($data['image'])) {
            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update('playbasis_goods');
        }
    }

    public function editGoodsToClient($goods_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        // $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('quantity',
            (isset($data['quantity']) && !($data['quantity'] === "")) ? (int)$data['quantity'] : null);
        // $this->mongo_db->set('per_user', (int)$data['per_user']);
        $this->mongo_db->set('per_user',
            (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('per_user_include_inactive', isset($data['per_user_include_inactive']) ? $data['per_user_include_inactive'] : false);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('code', isset($data['code']) ? $data['code'] : '');
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('custom_param', isset($data['custom_param']) ? $data['custom_param'] : array());
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] :false);
        if(isset($data['distinct_id']) && !is_null($data['distinct_id'])){
            $this->mongo_db->set('distinct_id', $data['distinct_id']);
        }

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', null);
            } elseif (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            } else {
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', null);
            }
        }

        if (isset($data['date_expired_coupon']) && $data['date_expired_coupon']){
            $this->mongo_db->set('date_expired_coupon', new MongoDate(strtotime($data['date_expired_coupon'])) );
        }else{
            $this->mongo_db->unset_field('date_expired_coupon');
        }

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }

        $this->mongo_db->set('organize_id', isset($data['organize_id']) ? new MongoID($data['organize_id']) : null);
        $this->mongo_db->set('organize_role', isset($data['organize_role']) ? $data['organize_role'] : null);

        $this->mongo_db->update('playbasis_goods_to_client');
        if (isset($data['date_expired_coupon']) && $data['date_expired_coupon']){
            $this->updateDateExpireGoodsPlayer($data['client_id'], $data['site_id'], $data['goods_id'], strtotime($data['date_expired_coupon']));
        }else{
            $this->updateDateExpireGoodsPlayer($data['client_id'], $data['site_id'], $data['goods_id'], null);
        }
    }

    public function editGoodsGroupToClient($group, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('group', $data['name']);
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !($data['per_user'] === "")) ? (int)$data['per_user'] : null);
        $this->mongo_db->set('per_user_include_inactive', isset($data['per_user_include_inactive']) ? $data['per_user_include_inactive'] : false);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
        $this->mongo_db->set('custom_param', isset($data['custom_param']) ? $data['custom_param'] : array());
        if(isset($data['distinct_id']) && !is_null($data['distinct_id'])){
            $this->mongo_db->set('distinct_id', $data['distinct_id']);
        }
        if (isset($data['days_expire'])){
            $this->mongo_db->set('days_expire', $data['days_expire'] ? $data['days_expire'] : null);
        }

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }

        $this->mongo_db->set('organize_id', isset($data['organize_id']) ? new MongoID($data['organize_id']) : null);
        $this->mongo_db->set('organize_role', isset($data['organize_role']) ? $data['organize_role'] : null);

        $this->mongo_db->update_all('playbasis_goods_to_client');
    }

    public function editGoodsGroupLog($group, $data)
    {
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->set('group', $data['name']);
        $this->mongo_db->update_all('playbasis_goods_log');
    }

    public function editGoodsGroupCoupon($group, $goods_id, $data, $filter)
    {
        if(isset($filter['filter_batch']) && $filter['filter_batch']){
            $this->mongo_db->where('batch_name', $filter['filter_batch']);
        }

        if(isset($filter['filter_goods'])){
            $this->mongo_db->where('goods_id', new MongoID($filter['filter_goods']));
        }

        if (isset($filter['filter_coupon_name']) && !is_null($filter['filter_coupon_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($filter['filter_coupon_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if(isset($filter['filter_voucher_code']) && !is_null($filter['filter_voucher_code'])){
            $regex_code = new MongoRegex("/" . preg_quote(utf8_strtolower($filter['filter_voucher_code'])) . "/i");
            $this->mongo_db->where('code', $regex_code);
        }

        if (isset($filter['filter_date_start']) && !is_null($filter['filter_date_start'])) {
            $this->mongo_db->where('date_start', new MongoDate(strtotime($filter['filter_date_start'])));
        }

        if(isset($filter['filter_date_end']) && !is_null($filter['filter_date_end'])){
            $this->mongo_db->where('date_expire', new MongoDate(strtotime($filter['filter_date_end'])));
        }

        if(isset($filter['filter_date_expire']) && !is_null($filter['filter_date_expire'])){
            $this->mongo_db->where('date_expired_coupon', new MongoDate(strtotime($filter['filter_date_expire'])));
        }

        if (!$filter){
            $this->mongo_db->where('goods_id', new MongoID($goods_id));
        }

        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if(isset($data['coupon_check_batch_name']) && $data['coupon_check_batch_name'] === "on") {
            $this->mongo_db->set('batch_name', $data['coupon_batch_name']);
        }

        if(isset($data['coupon_check_name']) && $data['coupon_check_name'] === "on") {
            if(isset($data['coupon_name']) && $data['coupon_name']){
                $this->mongo_db->set('name', $data['coupon_name']);
            }
        }

        if(isset($data['coupon_check_code']) && $data['coupon_check_code'] === "on") {
            if (isset($data['coupon_code']) && $data['coupon_code']) {
                $this->mongo_db->set('code', $data['coupon_code']);
            }
        }

        if(isset($data['coupon_check_date_start']) && $data['coupon_check_date_start'] === "on") {
            $this->mongo_db->set('date_start', $data['coupon_date_start'] ? new MongoDate(strtotime($data['coupon_date_start'])) : null);
        }

        if(isset($data['coupon_check_date_expire']) && $data['coupon_check_date_expire'] === "on") {
            $this->mongo_db->set('date_expire', $data['coupon_date_expire'] ? new MongoDate(strtotime($data['coupon_date_expire'])) : null);
        }

        if(isset($data['coupon_check_date_expired_coupon']) && $data['coupon_check_date_expired_coupon'] === "on"){
            if($data['coupon_date_expired_coupon']){
                $this->mongo_db->set('date_expired_coupon', new MongoDate(strtotime($data['coupon_date_expired_coupon'])));
            } else {
                $this->mongo_db->unset_field('date_expired_coupon');
            }
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $this->mongo_db->update_all('playbasis_goods_to_client');
    }
    public function deleteGoodsGroupCoupon($group, $goods_id, $data, $filter)
    {
        if(isset($filter['filter_batch']) && $filter['filter_batch']){
            $this->mongo_db->where('batch_name', $filter['filter_batch']);
            if(isset($filter['filter_goods'])){
                $this->mongo_db->where('goods_id', new MongoID($filter['filter_goods']));
            }
        } else {
            $this->mongo_db->where('goods_id', new MongoID($goods_id));
        }

        if (isset($filter['filter_coupon_name']) && !is_null($filter['filter_coupon_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($filter['filter_coupon_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        if(isset($filter['filter_voucher_code']) && !is_null($filter['filter_voucher_code'])){
            $regex_code = new MongoRegex("/" . preg_quote(utf8_strtolower($filter['filter_voucher_code'])) . "/i");
            $this->mongo_db->where('code', $regex_code);
        }

        if (isset($filter['filter_date_start']) && !is_null($filter['filter_date_start'])) {
            $this->mongo_db->where('date_start', new MongoDate(strtotime($filter['filter_date_start'])));
        }

        if(isset($filter['filter_date_end']) && !is_null($filter['filter_date_end'])){
            $this->mongo_db->where('date_expire', new MongoDate(strtotime($filter['filter_date_end'])));
        }

        if(isset($filter['filter_date_expire']) && !is_null($filter['filter_date_expire'])){
            $this->mongo_db->where('date_expired_coupon', new MongoDate(strtotime($filter['filter_date_expire'])));
        }

        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->update_all('playbasis_goods_to_client');
    }

    public function checkBatchNameExistInClient($group, $data)
    {
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('batch_name', $data['batch_name']);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->count('playbasis_goods_to_client');
    }

    public function checkBatchNameExistInDistinct($group, $data)
    {
        $this->mongo_db->where('name', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where_in('batch_name', array($data['batch_name']));
        return $this->mongo_db->count('playbasis_goods_distinct_to_client');
    }

    public function removeBatchNameInDistinct($group, $data)
    {
        $this->mongo_db->where('name', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->pull('batch_name', $data['batch_name']);
        $this->mongo_db->update('playbasis_goods_distinct_to_client');
    }

    public function addBatchNameInDistinct($group, $data)
    {
        $this->mongo_db->where('name', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->push('batch_name', $data['batch_name']);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->update('playbasis_goods_distinct_to_client');
    }

    public function editGoodsGroupPLayer($group, $data)
    {
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->set('group', $data['name']);
        $this->mongo_db->update_all('playbasis_goods_to_player');
    }

    public function editGoodsToClientFromAdmin($goods_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id', new MongoID($goods_id));
        $this->mongo_db->set('code', isset($data['code']) ? $data['code'] : null);
        $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);
        $this->mongo_db->set('organize_id', isset($data['organize_id']) ? new MongoID($data['organize_id']) : null);
        $this->mongo_db->set('organize_role', isset($data['organize_role']) ? $data['organize_role'] : null);

        if (isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']) {
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if ($date_start_another < $date_expire_another) {
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        } else {
            if (isset($data['date_start']) && $data['date_start']) {
                $date_start_another = strtotime($data['date_start']);
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', null);
            } elseif (isset($data['date_expire']) && $data['date_expire']) {
                $date_expire_another = strtotime($data['date_expire']);
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            } else {
                $this->mongo_db->set('date_start', null);
                $this->mongo_db->set('date_expire', null);
            }
        }

        $this->mongo_db->update_all('playbasis_goods_to_client');

        if (isset($data['image'])) {
            $this->mongo_db->where('goods_id', new MongoID($goods_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update_all('playbasis_goods_to_client');
        }
    }

    public function deleteGoods($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_goods');
    }

    public function deleteGoodsClient($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_goods_to_client');
    }

    public function deleteGoodsGroupClient($group, $client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update_all('playbasis_goods_to_client');
    }

    public function deleteGoodsClientFromAdmin($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id', new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update_all('playbasis_goods_to_client');
    }

    public function increaseOrderByOne($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));

        $goods = $this->mongo_db->get('playbasis_goods');

        $currentOrder = $goods[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_goods');
    }

    public function decreaseOrderByOne($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods');

        $currentOrder = $goods[0]['sort_order'];

        if ($currentOrder != 0) {
            $newOrder = $currentOrder - 1;

            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_goods');
        }
    }

    public function increaseOrderByOneClient($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));

        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_goods_to_client');
    }

    public function increaseOrderOfGroupByOneClient($goods_id,$group, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_goods_to_client');

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('group', $group);
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update_all('playbasis_goods_to_client', array("w" => 0, "j" => false));
    }

    public function decreaseOrderByOneClient($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];

        if ($currentOrder != 0) {
            $newOrder = $currentOrder - 1;

            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_goods_to_client');
        }
    }

    public function decreaseOrderOfGroupByOneClient($goods_id, $group, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];

        if ($currentOrder != 0) {
            $newOrder = $currentOrder - 1;

            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_goods_to_client');

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('group', $group);
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update_all('playbasis_goods_to_client', array("w" => 0, "j" => false));
        }
    }

    public function checkGoodsIsSponsor($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $badge = $this->mongo_db->get('playbasis_goods_to_client');
        return isset($badge[0]['sponsor']) ? $badge[0]['sponsor'] : null;
    }

    public function checkGoodsIsPublic($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id', $goods_id);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function listGoodsByGroupAndCode($group, $code, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where('group', $group);
        $this->mongo_db->where('code', $code);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function listRedeemedGoods($goods_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('goods_id', $goods_id_list);
        $this->mongo_db->where_gt('value', 0);
        return $this->mongo_db->get('playbasis_goods_to_player');
    }

    public function listRedeemedGoodsBySite($site_id, $fields = array(), $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_gt('value', 0);
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }
        return $this->mongo_db->get('playbasis_goods_to_player');
    }

    public function countRedeemedGoodsBySite($site_id, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_gt('value', 0);
        return $this->mongo_db->count('playbasis_goods_to_player');
    }

    public function listVerifiedGoods($goods_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('goods_id', $goods_id_list);
        return $this->mongo_db->get('playbasis_merchant_goodsgroup_redeem_log');
    }

    public function markAsVerifiedGoods($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $this->mongo_db->insert('playbasis_merchant_goodsgroup_redeem_log', array_merge($data, array(
            'date_added' => $d,
            'date_modified' => $d,
        )));

        //remove goods from player's inventory after verify
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('cl_player_id', $data['cl_player_id']);
        $this->mongo_db->where('goods_id', $data['goods_id']);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->update('playbasis_goods_to_player');
    }

    public function updateDateExpireGoodsPlayer($client_id, $site_id, $goods_id, $date_expire)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('goods_id', new MongoId($goods_id));
        $this->mongo_db->where('date_expire', array('$exists' => true));
        if(!is_null($date_expire)) {
            $this->mongo_db->set('date_expire', new MongoDate($date_expire));
        }else{
            $this->mongo_db->unset_field('date_expire');
        }
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->update_all('playbasis_goods_to_player');
    }

    public function listGoods($goods_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('goods_id', $goods_id_list);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    // Using in CRON
    public function getGoodsDetails($data, $is_sponsor = false)
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
        if (isset($result[0]['image'])) {
            $result[0]['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
        }
        return $result ? $result[0] : array();
    }

    public function getAllGoods($data, $nin = array())
    {
        $this->set_site_mongodb($data['site_id']);
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
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'deleted' => false,
            'status' => true,
        ));
        $this->mongo_db->where('site_id', $data['site_id']);

        $this->mongo_db->order_by(array('sort_order' => 'asc'));
        if (!empty($nin)) {
            $this->mongo_db->where_not_in('_id', $nin);
        }

        if(array_key_exists('specific', $data)){
            $this->mongo_db->where($data['specific']);
        }
        
        if (!empty($data['tags'])){
            $this->mongo_db->where_in('tags', $data['tags']);
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

                $g['image'] = $this->config->item('IMG_PATH') . $g['image'];
                $g['_id'] = $g['_id'] . "";
                $g['goods_id'] = $g['goods_id'] . "";
                $g['date_start'] = $g['date_start'] ? datetimeMongotoReadable($g['date_start']) : null;
                $g['date_expire'] = $g['date_expire'] ? datetimeMongotoReadable($g['date_expire']) : null;
            }
        }
        return $goods;
    }

    public function listActiveItems($data, $from, $to)
    {

        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where(array(
            '$and' => array(
                array(
                    '$or' => array(
                        array('date_start' => array('$lte' => $this->new_mongo_date($to))),
                        array('date_start' => null)
                    )
                ),
                array(
                    '$or' => array(
                        array('date_expire' => array('$gte' => $this->new_mongo_date($to, '23:59:59'))),
                        array('date_expire' => null)
                    )
                )
            ),
            'status' => true
        ));
        $this->mongo_db->where_in('_id', $data['in']);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function listExpiredItems($data, $from, $to)
    {
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where(array(
            'date_expire' => array(
                '$gte' => $this->new_mongo_date($from),
                '$lte' => $this->new_mongo_date($to, '23:59:59')
            ),
            'status' => true
        ));
        $this->mongo_db->where_in('_id', $data['in']);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function listGoodsIdsByGroup($client_id, $site_id, $group)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'group' => $group,
            'deleted' => false,
            'status' => true
        ));
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

    public function redeemLogCount($data, $goods_id, $group=false, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array('client_id' => $data['client_id']);
        if ($from || $to) {
            $query['date_added'] = array();
        }
        if ($from) {
            $query['date_added']['$gte'] = $this->new_mongo_date($from);
        }
        if ($to) {
            $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
        }
        $this->mongo_db->where($query);
        $this->mongo_db->where('site_id', $data['site_id']);

        
        if($group){
            $this->mongo_db->where('group', $goods_id);
        } else {
            $this->mongo_db->where_in('goods_id', is_array($goods_id) ? $goods_id : array($goods_id));
        }
        
        return $this->mongo_db->count('playbasis_goods_log');
    }

    public function redeemLog($data, $goods_id, $group=false, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), this.amount); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array(
            'client_id' => $data['client_id'],
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
        if ($group){
            $query['group'] = $goods_id;
        } else {
            $query['goods_id'] = array('$in' => is_array($goods_id) ? $goods_id : array($goods_id));
        }
        $query['site_id'] = $data['site_id'];

        $result = $this->mongo_db->command(array(
            'mapReduce' => 'playbasis_goods_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => array('inline' => 1),
        ));
        $result = $result['results'] ? $result['results'] : array();
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

}