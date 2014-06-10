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
        if($goodsId)
        {
            try {
                $goodsId = new MongoId($goodsId);
            }catch (MongoException $ex) {
                $goodsId = null;
            }
            //get goods by specific id
            $badge['goods'] = $this->goods_model->getGoods(array_merge($this->validToken, array(
                'goods_id' => new MongoId($goodsId)
            )));
            $this->response($this->resp->setRespond($badge), 200);
        }
        else
        {
            //get all goods relate to clients
            $badgesList['goods_list'] = $this->goods_model->getAllGoods($this->validToken);
            $this->response($this->resp->setRespond($badgesList), 200);
        }
    }
}
?>