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
        $this->mongo_db->select(array('goods_id','image','name','description','quantity','redeem','date_start','date_expire','sponsor'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'deleted' => false
        ));
        $goods = $this->mongo_db->get('playbasis_goods_to_client');
        if($goods){
            foreach($goods as &$g){
                $g['goods_id'] = $g['goods_id']."";
                $g['date_start'] = $g['date_start'] ? datetimeMongotoReadable($g['date_start']) : null;
                $g['date_expire'] = $g['date_expire'] ? datetimeMongotoReadable($g['date_expire']) : null;
            }
        }
        return $goods;
    }
    public function getGoods($data)
    {
        //get goods id
        $this->set_site_mongodb($data['site_id']);
        // $this->mongo_db->select(array('goods_id','image','name','description','quantity','redeem','date_start','date_expire','sponsor'));
        $this->mongo_db->select(array('goods_id','image','name','description','quantity','per_user','redeem','date_start','date_expire','sponsor'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'goods_id' => $data['goods_id'],
            'deleted' => false
        ));
        $result = $this->mongo_db->get('playbasis_goods_to_client');

        if(isset($result[0]['goods_id']))
        {
            $result[0]['goods_id'] = $result[0]['goods_id']."";
        }
        if(isset($result[0]['date_start']))
        {
            $result[0]['date_start'] = datetimeMongotoReadable($result[0]['date_start']);
        }
        if(isset($result[0]['date_expire']))
        {
            $result[0]['date_expire'] = datetimeMongotoReadable($result[0]['date_expire']);
        }
        return $result ? $result[0] : array();
    }
}
?>