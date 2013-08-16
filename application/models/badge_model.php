<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Badge_model extends MY_Model
{
    public function getBadge($badge_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($badge_id));
        $results = $this->mongo_db->get("playbasis_badge");

        return $results;
    }

    public function getBadges($data = array()) {
        $this->set_site_mongodb(0);

        $badges_data = array();

        $this->mongo_db->where('status', true);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (int)$data['filter_status']);
        }

        $sort_data = array(
            'badge_id',
            'name',
            'status',
            'sort_order'
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

        $results = $this->mongo_db->get("playbasis_badge");

        foreach ($results as $result) {

            $badges_data[] = array(
                'badge_id' => $result['_id'],
                'image' => $result['image'],
                'quantity' => $result['quantity'],
                'name' => $result['name'],
                'description' => $result['description'],
                'hint' => $result['hint'],
                'status' => $result['status'],
                'sort_order'  => $result['sort_order'],
                'date_added' => $this->datetimeMongotoReadable($result['date_added']),
                'date_modified' => $this->datetimeMongotoReadable($result['date_modified'])
            );


        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $badges_data = array_slice($badges_data, $data['start'], $data['limit']);
        }

        return $badges_data;
    }

    public function getBadgeBySiteId($site_id) {
        $badges_client_data = array();

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($site_id));
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        foreach ($results as $result) {

            $badge_info = $this->getBadge($result['badge_id']);

            if($badge_info){
                $badges_client_data[] = array(
                    'client_id' => $result['client_id']."",
                    'badge_id' => $result['badge_id']."",
                    'name' => isset($badge_info[0]['name'])?$badge_info[0]['name']:'',
                    'description' => isset($badge_info[0]['description'])?$badge_info[0]['description']:'',
                    'hint' => isset($badge_info[0]['hint'])?$badge_info[0]['hint']:'',
                    'quantity' => isset($badge_info[0]['quantity'])?$badge_info[0]['quantity']:0,
                    'image' => isset($badge_info[0]['image'])?$badge_info[0]['image']:'',
                    'status' => isset($badge_info[0]['status'])?$badge_info[0]['status']:false
                );
            }
        }

        return $badges_client_data;
    }

    public function getCommonBadges(){
        $this->set_site_mongodb(0);

        $results = $this->mongo_db->get("playbasis_badge");

        $badges = array();

        if(count($results)>0){
            foreach ($results as &$rown) {
                array_push($badges, array("id"=>$rown['_id']."","name"=>$rown['name'],"img_path"=>$rown['image'],"description"=>$rown['description']));
            }
        }//end if


        $output = array(
            "badges_set_id"=>0,
            "badges_customer_id"=>0,
            "badges_set"=>array(
                "set_label"=>"Basic Badge",
                "set_id"=>"0",
                "items"=>$badges
            )
        );

        return $output;
    }
}