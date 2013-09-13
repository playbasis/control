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

    public function getTotalDomainsByClientId($client_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', new MongoID($client_id));

        $total = $this->mongo_db->count("playbasis_client_site");

        return $total;
    }

    public function getDomainsByClientId($data) {

        $this->set_site_mongodb(0);

        $client_data = array();

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
            $order = " DESC";
        } else {
            $order = " ASC";
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

        $results = $this->mongo_db->get("playbasis_client_site");

        foreach ($results as $result) {
            $client_data[] = array(
                'site_id' => $result['_id'],
                'client_id' => $result['client_id'],
                'domain_name' => $result['domain_name'],
                'site_name' => $result['site_name'],
                'status'  	  => (bool)$result['status'],
                'api_key' => $result['api_key'],
                'api_secret' => $result['api_secret'],
                'date_start' => $result['date_start'],
                'date_expire' => $this->datetimeMongotoReadable($result['date_expire']),
                'limit_users' => $result['limit_users'],
                'date_added' => $this->datetimeMongotoReadable($result['date_added']),
                'date_modified' => $this->datetimeMongotoReadable($result['date_modified'])
            );
        }

        return $client_data;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }
}
?>