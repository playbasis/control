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

    public function getMerchantGoodsGroups($client_id, $site_id, $goods_group)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'goods_group' => $goods_group,
            'status' => true,
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        return (!empty($result) && $result) ? $result : null;
    }

    public function getMerchantBranchByBranchesAndPinCode(
        $client_id,
        $site_id,
        $branches_id_array = array(),
        $pin_code = null
    ) {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pin_code' => $pin_code,
            'status' => true,
            'deleted' => false
        ));

        $this->mongo_db->where_in('_id', $branches_id_array);

        $result = $this->mongo_db->get('playbasis_merchant_branch_to_client');

        return (!empty($result) && $result) ? $result[0] : null;
    }

    public function logMerchantRedeem(
        $client_id,
        $site_id,
        $goods_id,
        $goods_group,
        $cl_player_id,
        $pb_player_id,
        $branch
    ) {
        $this->set_site_mongodb($site_id);

        $result = $this->mongo_db->insert('playbasis_merchant_goodsgroup_redeem_log', array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'goods_id' => $goods_id,
            'goods_group' => $goods_group,
            'cl_player_id' => $cl_player_id,
            'pb_player_id' => $pb_player_id,
            'branch' => $branch,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate(),
        ));

        return $result;
    }

    public function getMerchantRedeemLogByGoodsId($client_id, $site_id, $goods_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'goods_id' => $goods_id,
        ));

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_redeem_log');

        return (!empty($result) && $result) ? $result[0] : null;
    }

    public function getMerchantRedeemLogByLogId($client_id, $site_id, $log_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            '_id' => $log_id,
        ));

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_redeem_log');

        return (!empty($result) && $result) ? $result[0] : null;
    }
}