<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Domain_model extends MY_Model
{

    public function getDomain($site_id) {

        $this->set_site_mongodb(0);
        $results = $this->mongo_db->where('_id', new MongoID($site_id))
            ->get("playbasis_client_site");

        return $results ? $results[0] : null  ;
    }

    public function getTotalDomainsByClientId($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('domain_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_client_site");

        return $total;
    }

    public function getDomainsByClientId($data) {

        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('domain_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            'domain_name',
            'status',
            'sort_order',
            '_id'
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

        $client_data = $this->mongo_db->get("playbasis_client_site");

        return $client_data;
    }

    public function getTotalDomains($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('domain_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_client_site");

        return $total;
    }

    public function getDomains($data) {

        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('domain_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            'domain_name',
            'status',
            'sort_order',
            '_id'
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

        $client_data = $this->mongo_db->get("playbasis_client_site");

        return $client_data;
    }

    public function resetToken ($site_id) {

        $secret = $this->genAccessSecret($site_id);

        $token = $this->checkAccessToken("",$secret);

        if ($token == '0' || $token == 0) {
            $this->set_site_mongodb(0);
            $this->mongo_db->where('_id', new MongoID($site_id));
            $this->mongo_db->set('api_secret', $secret);
            $this->mongo_db->update('playbasis_client_site');
        }
    }

    public function checkAccessToken($keys="", $secret) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('api_secret', $secret);

        $total = $this->mongo_db->count("playbasis_client_site");

        return $total;
    }

    private function genAccessKey($site_id) {
        $salt = "R0b3rt pl@yb@s1s";

        $key = sprintf("%u",crc32(sha1($site_id).$salt));

        return $key;
    }

    private function genAccessSecret($site_id) {
        $salt = "R0b3rt pl@yb@s1s";

        $secret = md5($site_id.time().$salt);

        return $secret;
    }

    public function addDomain($data, $client_id) {
        $this->set_site_mongodb(0);

        if(isset($data['client_id'])){
            $client_id = $data['client_id'];
        }

        $data_insert = array(
            'client_id' =>  new MongoID($client_id),
            'domain_name' => $data['domain_name']|'',
            'site_name' => $data['site_name']|'' ,
            'api_key'=> '',
            'api_secret' => '',
            'date_start' => null,
            'date_expire' => null,
            'image' => isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => true,
            'deleted' => false,
            'limit_users' => null ,
            'last_send_limit_users' => null,
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
        );
        if(isset($data['limit_users']) && $data['limit_users']){
            $data_insert['limit_users'] = $data['limit_users'];
        }

        if(isset($data['date_start']) && $data['date_start']){
            $data_insert['date_start'] = new MongoDate(strtotime($data['date_start']));
        }

        if(isset($data['date_expire']) && $data['date_expire']){
            $data_insert['date_expire'] = new MongoDate(strtotime($data['date_expire']));
        }

        $c = $this->mongo_db->insert('playbasis_client_site', $data_insert);

        $keys = $this->genAccessKey($c);
        $secret = $this->genAccessSecret($c);

        $this->mongo_db->where('_id',  new MongoID($c));
        $this->mongo_db->set('api_key', $keys);
        $this->mongo_db->set('api_secret', $secret);
        $this->mongo_db->update('playbasis_client_site');

        return $c;
    }

    public function deleteDomain($site_id){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($site_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_client_site');
    }
}
?>