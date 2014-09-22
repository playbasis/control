<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Goods_model extends MY_Model
{
    public function getGoods($goods_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods");

        return $results ? $results[0] : null;
    }

    public function getGoodsToClient($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0] : null;
    }

    public function getGoodsOfClientPrivate($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id',  new MongoID($goods_id));
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results ? $results[0] : null;
    }

    public function getGoodsList($data = array()) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
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

    public function getTotalGoods($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count("playbasis_goods");

        return $total;
    }

    public function getGoodsBySiteId($data = array()) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
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
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $results = $this->mongo_db->get("playbasis_goods_to_client");

        return $results;
    }

    public function getTotalGoodsBySiteId($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $total = $this->mongo_db->count("playbasis_goods_to_client");

        return $total;
    }

    public function getCommonGoods(){

        $results = $this->mongo_db->get("playbasis_goods");

        $goods_list = array();

        if(count($results)>0){
            foreach ($results as &$rown) {
                array_push($goods_list, array("id"=>$rown['_id']."","name"=>$rown['name'],"img_path"=>$rown['image'],"description"=>$rown['description']));
            }
        }//end if


        $output = array(
            "goods_list_set_id"=>0,
            "goods_list_customer_id"=>0,
            "goods_list_set"=>array(
                "set_label"=>"Basic Goods",
                "set_id"=>"0",
                "items"=>$goods_list
            )
        );

        return $output;
    }

    public function addGoods($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data_insert =  array(
            // 'quantity' => (int)$data['quantity']|0 ,
            'quantity' => (isset($data['quantity']) && !empty($data['quantity']))?(int)$data['quantity']:null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user']))?(int)$data['per_user']:null,
            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order']|1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'name' => $data['name']|'' ,
            'description' => $data['description']|'',
            'language_id' => (int)1,
            'redeem' => $data['redeem'],
            'deleted'=>false,
            'sponsor'=>isset($data['sponsor'])?$data['sponsor']:false,
            'date_start' => null,
            'date_expire' => null,
        );

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']){
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if($date_start_another < $date_expire_another){
                $data_insert['date_start'] = new MongoDate($date_start_another);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        $b = $this->mongo_db->insert('playbasis_goods',$data_insert);
        return $b;
    }

    public function addGoodsToClient($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data_insert = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'goods_id' => new MongoID($data['goods_id']),
            // 'quantity' => (int)$data['quantity']|0 ,
            'quantity' => (isset($data['quantity']) && !empty($data['quantity']))?(int)$data['quantity']:null,
            'per_user' => (isset($data['per_user']) && !empty($data['per_user']))?(int)$data['per_user']:null,
            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order']|1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'name' => $data['name']|'' ,
            'description' => $data['description']|'',
            'language_id' => (int)1,
            'redeem' => $data['redeem'],
            'deleted'=>false,
            'sponsor'=>isset($data['sponsor'])?$data['sponsor']:false,
            'date_start' => null,
            'date_expire' => null,
        );

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']){
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if($date_start_another < $date_expire_another){
                $data_insert['date_start'] = new MongoDate($date_start_another);
                $data_insert['date_expire'] = new MongoDate($date_expire_another);
            }
        }

        $this->mongo_db->insert('playbasis_goods_to_client', $data_insert);
    }

    public function addGoodsToClient_bulk($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        return $this->mongo_db->batch_insert('playbasis_goods_to_client', $data);
    }

    public function editGoods($goods_id, $data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        // $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('quantity', (isset($data['quantity']) && !empty($data['quantity']))?(int)$data['quantity']:null);
        // $this->mongo_db->set('per_user', (int)$data['per_user']);
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !empty($data['per_user']))?(int)$data['per_user']:null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        if(isset($data['sponsor'])){
            $this->mongo_db->set('sponsor', (bool)$data['sponsor']);
        }else{
            $this->mongo_db->set('sponsor', false);
        }

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']){
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if($date_start_another < $date_expire_another){
                    $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                    $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        }else{
            $this->mongo_db->set('date_start', null);
            $this->mongo_db->set('date_expire', null);
        }

        $this->mongo_db->update('playbasis_goods');

        if (isset($data['image'])) {
            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update('playbasis_goods');
        }

    }

    public function editGoodsToClient($goods_id, $data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        $this->mongo_db->set('client_id', new MongoID($data['client_id']));
        $this->mongo_db->set('site_id', new MongoID($data['site_id']));
        // $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('quantity', (isset($data['quantity']) && !empty($data['quantity']))?(int)$data['quantity']:null);
        // $this->mongo_db->set('per_user', (int)$data['per_user']);
        $this->mongo_db->set('per_user', (isset($data['per_user']) && !empty($data['per_user']))?(int)$data['per_user']:null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);
        if(isset($data['sponsor'])){
            $this->mongo_db->set('sponsor', (bool)$data['sponsor']);
        }else{
            $this->mongo_db->set('sponsor', false);
        }

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']){
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if($date_start_another < $date_expire_another){
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        }else{
            $this->mongo_db->set('date_start', null);
            $this->mongo_db->set('date_expire', null);
        }

        $this->mongo_db->update('playbasis_goods_to_client');

        if (isset($data['image'])) {
            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update('playbasis_goods_to_client');
        }

    }

    public function editGoodsToClientFromAdmin($goods_id, $data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id',  new MongoID($goods_id));
        $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->set('redeem', $data['redeem']);

        if(isset($data['sponsor'])){
            $this->mongo_db->set('sponsor', (bool)$data['sponsor']);
        }else{
            $this->mongo_db->set('sponsor', false);
        }

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_expire']) && $data['date_expire']){
            $date_start_another = strtotime($data['date_start']);
            $date_expire_another = strtotime($data['date_expire']);

            if($date_start_another < $date_expire_another){
                $this->mongo_db->set('date_start', new MongoDate($date_start_another));
                $this->mongo_db->set('date_expire', new MongoDate($date_expire_another));
            }
        }else{
            $this->mongo_db->set('date_start', null);
            $this->mongo_db->set('date_expire', null);
        }

        $this->mongo_db->update_all('playbasis_goods_to_client');

        if (isset($data['image'])) {
            $this->mongo_db->where('goods_id', new MongoID($goods_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update_all('playbasis_goods_to_client');
        }

    }

    public function deleteGoods($goods_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_goods');
    }

    public function deleteGoodsClient($goods_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_goods_to_client');

    }

    public function deleteGoodsClientFromAdmin($goods_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id',  new MongoID($goods_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update_all('playbasis_goods_to_client');

    }

    public function increaseOrderByOne($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));

        $goods = $this->mongo_db->get('playbasis_goods');

        $currentOrder = $goods[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_goods');

    }

    public function decreaseOrderByOne($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods');

        $currentOrder = $goods[0]['sort_order'];

        if($currentOrder != 0){
            $newOrder = $currentOrder - 1;

            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_goods');
        }
        
    }

    public function increaseOrderByOneClient($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));

        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];
        $newOrder = $currentOrder + 1;

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $this->mongo_db->set('sort_order', $newOrder);
        $this->mongo_db->update('playbasis_goods_to_client');

    }

    public function decreaseOrderByOneClient($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');

        $currentOrder = $goods[0]['sort_order'];

        if($currentOrder != 0){
            $newOrder = $currentOrder - 1;

            $this->mongo_db->where('_id', new MongoID($goods_id));
            $this->mongo_db->set('sort_order', $newOrder);
            $this->mongo_db->update('playbasis_goods_to_client');
        }
        
    }

    public function checkGoodsIsSponsor($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $badge = $this->mongo_db->get('playbasis_goods_to_client');
        return isset($badge[0]['sponsor'])?$badge[0]['sponsor']:null;
    }

    public function checkGoodsIsPublic($goods_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('goods_id', $goods_id);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }

}