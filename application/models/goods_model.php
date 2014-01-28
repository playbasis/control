<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Goods_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
    }
    public function getAllGoods($data)
    {
        //get goods ids
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('goods_id','image','name','description','redeem'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'deleted' => false
        ));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');
        return $goods;
    }
    public function getGoods($data)
    {
        //get badge id
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('goods_id','image','name','description','redeem'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'goods_id' => $data['goods_id'],
            'deleted' => false
        ));
        $result = $this->mongo_db->get('playbasis_goods_to_client');
        return $result ? $result[0] : array();
    }
}
?>