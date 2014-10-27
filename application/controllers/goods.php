<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Goods extends REST2_Controller
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
        /* process group */
        $results = $this->goods_model->getGroupsAggregate($this->validToken['site_id']);
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }
        /* main */
        if($goodsId) // given specified goods_id
        {
            $goods['goods'] = $this->goods_model->getGoods(array_merge($this->validToken, array(
                'goods_id' => new MongoId($goodsId)
            )));
            $goods['goods']['is_group'] = array_key_exists('group', $goods['goods']);
            if ($goods['goods']['is_group']) {
                $group = $goods['goods']['group'];
                foreach ($group_name as $each) {
                    if ($each['group'] == $group) {
                        $goods['goods']['quantity'] = $each['quantity'];
                        break;
                    }
                }
            }
            $this->response($this->resp->setRespond($goods), 200);
        }
        else // list all
        {
            $goodsList['goods_list'] = $this->goods_model->getAllGoods($this->validToken, $ids);
            if (is_array($goodsList['goods_list'])) foreach ($goodsList['goods_list'] as &$goods) {
                $goods_id = $goods['_id'];
                $is_group = array_key_exists('group', $goods);
                if ($is_group) {
                    $goods['is_group'] = true;
                    $goods['name'] = $group_name[$goods_id]['group'];
                    $goods['quantity'] = $group_name[$goods_id]['quantity'];
                }
                unset($goods['_id']);
                $goods['code'] = null;
            }
            $this->response($this->resp->setRespond($goodsList), 200);
        }
    }
}
?>