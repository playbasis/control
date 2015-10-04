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

    public function getMerchantGoodsGroup($client_id, $site_id, $goods_group)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'status' => true,
            'deleted' => false
        ));

        if (isset($goods_group) && !empty($goods_group)) {
            $this->mongo_db->where(array(
                'goods_group' => $goods_group
            ));
        }

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        return $result;
    }

    public function getMerchantBranchByBranchesAndPinCode($client_id, $site_id, $branches_id_array = array(), $pin_code = null)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pin_code' => $pin_code,
            'status' => true,
            'deleted' => false
        ));

        $this->mongo_db->where_in('_id',$branches_id_array);

        $result = $this->mongo_db->get('playbasis_merchant_branch_to_client');

        return $result;
    }
}