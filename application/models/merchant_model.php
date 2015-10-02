<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchant_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('mongo_db');
    }

    public function getMerchantGoodsGroup($client_id, $site_id, $goodsgroup)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'status' => true,
            'deleted' => false
        ));

        if (isset($data['goodsgroup']) && !empty($data['goodsgroup'])) {
            $this->mongo_db->where(array(
                'goods_group' => $goodsgroup
            ));
        }

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        return $result;
    }
}