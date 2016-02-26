<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('PIN_CODE_LENGTH', 7);

class Merchant_model extends MY_Model
{
    public function countMerchants($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_merchant_to_client');

        return $total;
    }

    public function retrieveMerchants($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
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

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_merchant_to_client");
    }

    public function retrieveMerchant($merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($merchant_id));
        $c = $this->mongo_db->get('playbasis_merchant_to_client');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function createMerchant($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => $data['name'],
            'desc' => $data['desc'],
            'branches' => $data['branches'],
            'status' => $data['status'],
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_merchant_to_client', $insert_data);

        return $insert;
    }

    public function updateMerchant($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($data['_id']));

        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('desc', $data['desc']);
        $this->mongo_db->set('status', $data['status']);

        $this->mongo_db->push(array('branches' => array('$each' => $data['branches'])));

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_merchant_to_client');

        return $update;
    }

    public function deleteMerchant($merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($merchant_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_merchant_to_client');
    }

    public function bulkInsertBranches($batch_data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (!empty($batch_data) && is_array($batch_data)) {
            try {
                return $this->mongo_db->batch_insert('playbasis_merchant_branch_to_client', $batch_data,
                    array("w" => 0, "j" => false));
            } catch (Exception $e) {
                var_dump($e);
            }
        }
        return false;
    }

    public function retrieveBranches($client_id, $site_id, $arrBranchID = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);

        $this->mongo_db->where_in('_id', $arrBranchID);

        $result = $this->mongo_db->get('playbasis_merchant_branch_to_client');

        return $result;
    }

    public function retrieveBranchesJSON($client_id, $site_id, $arrBranchID = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'branch_name', 'pin_code', 'status', 'date_modified'), null);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);

        $this->mongo_db->where_in('_id', $arrBranchID);

        $results = $this->mongo_db->get('playbasis_merchant_branch_to_client');

        foreach ($results as &$result) {
            $result['_id'] = $result['_id'] . "";
        }

        return json_encode($results);
    }

    public function updateBranchById($_id, $name = null, $status = null)
    {
        $this->mongo_db->where('_id', new MongoID($_id));

        if ($name != null) {
            $this->mongo_db->set('branch_name', $name);
        }

        if (!empty($status)) {
            switch ($status) {
                case 'Enabled':
                    $this->mongo_db->set('status', true);
                    break;
                case 'Disabled':
                    $this->mongo_db->set('status', false);
                    break;
            }
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_merchant_branch_to_client');

        return $update;
    }

    public function removeBranchById($id)
    {
        if (!empty($id)) {
            $this->mongo_db->where('_id', new MongoId($id));
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_merchant_branch_to_client');

        return $update;
    }

    public function removeBranchesByIdArray($id_array)
    {
        if (!empty($id_array)) {
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_merchant_branch_to_client');

        return $update;
    }

    private function checkPINCodeExisted($pin_code)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('pin_code', $pin_code);
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_merchant_branch_to_client');

        return $total ? true : false;
    }

    public function generatePINCode($clientId, $siteId)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = null;

        do {
            $randomString = '';
            for ($i = 0; $i < PIN_CODE_LENGTH; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        } while ($this->checkPINCodeExisted($randomString));

        return $randomString;
    }

    public function createMerchantGoodsGroup($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'merchant_id' => new MongoId($data['merchant_id']),
            'goods_group' => $data['goods_group'],
            'branches_allow' => $data['branches_allow'],
            'status' => $data['status'],
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_merchant_goodsgroup_to_client', $insert_data);

        return $insert;
    }

    public function retrieveMerchantGoodsGroup($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));

        $c = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function updateMerchantGoodsGroup($data)
    {
        $this->mongo_db->where('_id', new MongoID($data['_id']));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('merchant_id', new MongoId($data['merchant_id']));

        $this->mongo_db->set('goods_group', $data['goods_group']);
        $this->mongo_db->set('branches_allow', $data['branches_allow']);
        $this->mongo_db->set('status', $data['status']);

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_merchant_goodsgroup_to_client');

        return $update;
    }

    public function deleteMerchantGoodsGroupById($merchantGoodsGroup_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($merchantGoodsGroup_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_merchant_goodsgroup_to_client');
    }

    public function deleteMerchantGoodsGroupByMerchantId($client_id, $site_id, $merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('merchant_id', new MongoId($merchant_id));

        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update_all('playbasis_merchant_goodsgroup_to_client');
    }

    public function retrieveMerchantGoodsGroups($client_id, $site_id, $merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('merchant_id', new MongoId($merchant_id));
        $this->mongo_db->where('deleted', false);

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        return $result;
    }

    public function retrieveMerchantGoodsGroupsJSON($client_id, $site_id, $merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'goods_group', 'branches_allow', 'status'), null);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('merchant_id', new MongoId($merchant_id));
        $this->mongo_db->where('deleted', false);

        $result = $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');

        return json_encode($result);
    }

    public function isValidPin($pin_code)
    {
        return $this->getBranchByPin($pin_code);
    }

    public function getBranchByPin($pin_code)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('pin_code', $pin_code);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->limit(1);
        $record = $this->mongo_db->get('playbasis_merchant_branch_to_client');
        return $record ? $record[0] : array();
    }

    public function findMerchantByBranchId($branch_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('branches.b_id', $branch_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->limit(1);
        $record = $this->mongo_db->get('playbasis_merchant_to_client');
        return $record ? $record[0] : array();
    }

    public function findGoodsByBranchId($branch_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('goods_group'));
        $this->mongo_db->where('branches_allow.b_id', $branch_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get('playbasis_merchant_goodsgroup_to_client');
    }
}