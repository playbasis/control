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

        $this->set_site_mongodb($this->session->userdata('site_id'));


        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
            $users1 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('email', $data['username']);
            $users2 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where_in('pb_player_id',
                array_merge(array_map('index_id', $users1), array_map('index_id', $users2)));
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));


        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['is_group']) && $data['is_group']) {
            if (isset($data['goods_id']) && $data['goods_id'] != '') {
                $ids = $this->listGoodsIdByGroup($data['goods_id']);
                $this->mongo_db->where_in('goods_id', array_map(function ($obj) {return $obj['goods_id'];}, $ids));
            }
        } else {
            if (isset($data['goods_id']) && $data['goods_id'] != '') {
                $goodsRewards = array(
                    'goods_id' => new MongoID($data['goods_id']),
                );
                $this->mongo_db->where($goodsRewards);
            }
        }

        // $results = $this->mongo_db->count("playbasis_goods_to_player");
        $results = $this->mongo_db->count("playbasis_goods_log");

        return $results;
    }

    public function getReportGoods($data)
    {

        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['username'])) . "/i");
            $this->mongo_db->where('username', $regex);
            $users1 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('email', $data['username']);
            $users2 = $this->mongo_db->get("playbasis_player");

            $this->mongo_db->where_in('pb_player_id',
                array_merge(array_map('index_id', $users1), array_map('index_id', $users2)));
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '') {
            $this->mongo_db->where('date_added', array(
                '$gt' => new MongoDate(strtotime($data['date_start'])),
                '$lte' => new MongoDate(strtotime($data['date_expire']))
            ));
        }

        if (isset($data['is_group']) && $data['is_group']) {
            if (isset($data['goods_id']) && $data['goods_id'] != '') {
                $ids = $this->listGoodsIdByGroup($data['goods_id']);
                $this->mongo_db->where_in('goods_id', array_map(function ($obj) {return $obj['goods_id'];}, $ids));
            }
        } else {
            if (isset($data['goods_id']) && $data['goods_id'] != '') {
                $goodsRewards = array(
                    'goods_id' => new MongoID($data['goods_id']),
                );
                $this->mongo_db->where($goodsRewards);
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

        // $results = $this->mongo_db->get("playbasis_goods_to_player");
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

        return $this->mongo_db->get('playbasis_goods_to_client');

    }

    public function listGoodsIdByGroup($group)
    {
        $this->mongo_db->select(array('goods_id'));
        $this->mongo_db->where('group', $group);
        return $this->mongo_db->get('playbasis_goods_to_client');
    }
}