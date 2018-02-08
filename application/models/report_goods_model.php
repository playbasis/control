<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_goods_model extends MY_Model
{

    function index_id($obj)
    {
        return $obj['_id'];
    }

    public function getTotalReportGoods($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('cl_player_id', $data['username']);
        }

        if(isset($data['status']) && !is_null($data['status'])){
            if($data['status'] == 'active') {
                $this->mongo_db->where('status', array('$exists' => false));
            } elseif($data['status'] == 'expired') {
                $this->mongo_db->where('date_expire', array('$lt' => new MongoDate()));
            } elseif($data['status'] == 'gifted'){
                $this->mongo_db->where('status', 'sender');
            } else {
                $this->mongo_db->where('status', $data['status']);
            }
        }

        // $results = $this->mongo_db->count("playbasis_goods_to_player");
        $results = $this->mongo_db->count("playbasis_goods_log");

        return $results;
    }

    public function getReportGoods($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (!empty($data['goods_id']) && !empty($data['group'])) {
            $this->mongo_db->where('$or', array(array('group' => array('$in' => $data['group'])),
                array('goods_id' => array('$in' => $data['goods_id']))));
        } elseif (!empty($data['group'])){
            $this->mongo_db->where_in('group', $data['group']);
        } elseif (!empty($data['goods_id'])){
            $this->mongo_db->where_in('goods_id', $data['goods_id']);
        }

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('cl_player_id', $data['username']);
        }

        if(isset($data['status']) && !is_null($data['status'])){
            if($data['status'] == 'active') {
                $this->mongo_db->where(array('$or' => array(array('status' => array('$exists' => false)) , array('status' => 'receiver'))));
                $this->mongo_db->where('date_expire', array('$gt' => new MongoDate()));
            } elseif($data['status'] == 'expired') {
                $this->mongo_db->where('date_expire', array('$lt' => new MongoDate()));
            } elseif($data['status'] == 'gifted'){
                $this->mongo_db->where('status', 'sender');
            } else {
                $this->mongo_db->where('status', $data['status']);
            }
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
        $this->mongo_db->order_by(array('date_added' => 'ASC'));
        $results = $this->mongo_db->get("playbasis_goods_log");

        return $results;
    }

    public function getGoodsName($goods_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($goods_id));
        $var = $this->mongo_db->get('playbasis_goods');

        return isset($var[0]) ? $var[0] : null;

    }

    public function getAllGoodsFromSite($data_filter)
    {

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get('playbasis_goods_to_client');

    }

    public function listGoodsIdByGroup($site_id, $group)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('group', $group);

        return $this->mongo_db->get('playbasis_goods_to_client');
    }
}