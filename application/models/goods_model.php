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
        $this->mongo_db->select(array('value'));
        $this->mongo_db->where(array(
            'site_id' => $site_id,
            'goods_id' => $goodsId,
            'pb_player_id' => $pb_player_id
        ));
        $this->mongo_db->limit(1);
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return isset($goods[0]) ? $goods[0]['value'] : null;
    }


    public function getPlayerGoods($site_id, $date_start, $date_end)
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getPlayerGoodsUsed($site_id, $date_start, $date_end)
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where('value',0);
        $this->mongo_db->where_gte('date_modified',new MongoDate(strtotime($date_start)));
        $this->mongo_db->where_lte('date_modified',new MongoDate(strtotime($date_end)));
        $goods = $this->mongo_db->get('playbasis_goods_to_player');
        return $goods;
    }

    public function getPlayerGoodsActive($site_id, $date_start, $date_end)
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where_gt('value',0);
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
        $goods_list_data = $this->mongo_db->get("playbasis_goods");

        return $goods_list_data;
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
        $total = $this->mongo_db->count("playbasis_goods");

        return $total;
    }

    public function getGoodsBySiteId($data = array())
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
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        if (array_key_exists('$nin', $data)) {
            $this->mongo_db->where_not_in('_id', $data['$nin']);
        }
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results;
    }

    public function getTotalGoodsBySiteId($data)
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
        $this->mongo_db->where('site_id', $data['site_id'] ? new MongoID($data['site_id']) : null);
        if (array_key_exists('$nin', $data)) {
            $this->mongo_db->where_not_in('_id', $data['$nin']);
        }
        $total = $this->mongo_db->count("playbasis_goods_to_client");

        return $total;
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

    public function checkExists($site_id, $group)
    {
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('group', $group);
        return $this->mongo_db->count("playbasis_goods_to_client") > 0;
    }

    public function getAvailableGoodsByGroup($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name', 'code', 'goods_id' ,'date_expired_coupon'));

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
        $this->mongo_db->where('group', $data['group']);
        $this->mongo_db->where_gt('quantity', 0);

        $results = $this->mongo_db->get("playbasis_goods_to_client");
        if(is_array($results)) foreach ($results as $index => $goods){
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

        $this->mongo_db->insert('playbasis_goods_to_client', $data_insert);
    }

    public function addGoodsToClient_bulk($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        return $this->mongo_db->batch_insert('playbasis_goods_to_client', $data, array("w" => 0, "j" => false));
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
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('code', isset($data['code']) ? $data['code'] : '');
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        if (isset($data['sponsor'])) {
            $this->mongo_db->set('sponsor', (bool)$data['sponsor']);
        } else {
            $this->mongo_db->set('sponsor', false);
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

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
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

        $this->mongo_db->update('playbasis_goods_to_client');
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
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        $this->mongo_db->set('tags', isset($tags) ? $tags : null);
        $this->mongo_db->set('sponsor', isset($data['sponsor']) ? (bool)$data['sponsor'] : false);

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

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
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

        $this->mongo_db->update_all('playbasis_goods_to_client');
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
        $this->set_site_mongodb($data['site_id']);
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
        $this->set_site_mongodb($data['site_id']);
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

    public function redeemLogCount($data, $goods_id, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id']);
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
        $this->mongo_db->where_in('goods_id', is_array($goods_id) ? $goods_id : array($goods_id));
        return $this->mongo_db->count('playbasis_goods_log');
    }

    public function redeemLog($data, $goods_id, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), this.amount); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'goods_id' => array('$in' => is_array($goods_id) ? $goods_id : array($goods_id))
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