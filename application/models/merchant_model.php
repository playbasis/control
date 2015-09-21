<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('PIN_CODE_LENGTH', 6);

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

//    public function retrieveMerchant($promo_content_id)
//    {
//        $this->set_site_mongodb($this->session->userdata('site_id'));
//
//        $this->mongo_db->where('_id', new MongoId($promo_content_id));
//        $c = $this->mongo_db->get('playbasis_promo_content_to_client');
//
//        if ($c) {
//            return $c[0];
//        } else {
//            return null;
//        }
//    }
//
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

        // TODO: Create PIN for new branches

        return $insert;
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

    private function checkPINCodeExisted($client_id, $site_id, $pin_code)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('pin_code', $pin_code);
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_merchant_branch_to_client');

        return $total ? true : false;
    }

    public function generatePINCode($clientId, $siteId)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        do {
            for ($i = 0; $i < PIN_CODE_LENGTH; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        } while ($this->checkPINCodeExisted($clientId, $siteId, $randomString));

        return $randomString;
    }
//
//    public function updateMerchant($data)
//    {
//        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
//        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
//        $this->mongo_db->where('_id', new MongoID($data['_id']));
//
//        $this->mongo_db->set('name', $data['name']);
//        $this->mongo_db->set('desc', $data['desc']);
//        $this->mongo_db->set('image', $data['image']);
//        $this->mongo_db->set('date_start', new MongoDate(strtotime($data['date_start'])));
//        $this->mongo_db->set('date_end', new MongoDate(strtotime($data['date_end'])));
//        $this->mongo_db->set('date_modified', new MongoDate());
//        $this->mongo_db->set('status', $data['status']);
//
//        $update = $this->mongo_db->update('playbasis_promo_content_to_client');
//
//        return $update;
//    }
//
    public function deleteMerchant($merchant_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($merchant_id));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_merchant_to_client');
    }
}