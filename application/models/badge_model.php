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
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
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

        $results = $this->mongo_db->get("playbasis_badge");

        foreach ($results as $result) {

            $badges_data[] = array(
                'badge_id' => $result['_id'],
                'image' => $result['image'],
                'quantity' => $result['quantity'],
                'name' => $result['name'],
                'description' => $result['description'],
                'hint' => $result['hint'],
                'status' => (bool)$result['status'],
                'sort_order'  => $result['sort_order'],
                'date_added' => $this->datetimeMongotoReadable($result['date_added']),
                'date_modified' => $this->datetimeMongotoReadable($result['date_modified'])
            );
        }

        return $badges_data;
    }

    public function getTotalBadges(){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('status', true);

        $total = $this->mongo_db->count("playbasis_badge");

        return $total;
    }

    public function getBadgeBySiteId($site_id, $limit=null, $offset=null) {
        $badges_client_data = array();

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($site_id));

        if ($limit || $offset) {
            if ($offset < 0) {
                $offset = 0;
            }

            if ($limit < 1) {
                $limit = 20;
            }

            $this->mongo_db->limit((int)$limit);
            $this->mongo_db->offset((int)$offset);
        }

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
                    'status' => isset($badge_info[0]['status'])?$badge_info[0]['status']:false,
                    'sort_order'  =>  isset($badge_info[0]['sort_order'])?$badge_info[0]['sort_order']:0,
                );
            }
        }

        return $badges_client_data;
    }

    public function getTotalBadgeBySiteId($site_id) {

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($site_id));
        $total = $this->mongo_db->count("playbasis_badge_to_client");

        return $total;
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

    public function addBadge($data) {
        $this->set_site_mongodb(0);

        $b = $this->mongo_db->insert('playbasis_badge', array(
            'stackable' => (int)$data['stackable']|0 ,
            'substract' => (int)$data['substract']|0,
            'quantity' => (int)$data['quantity']|0 ,
            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => (bool)$data['status'],
            'sort_order' => (int)$data['sort_order']|1,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'name' => $data['name']|'' ,
            'description' => $data['description']|'',
            'hint' => $data['hint']|'' ,
            'language_id' => (int)1,
        ));

        $this->mongo_db->insert('playbasis_badge_to_client', array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'badge_id' => new MongoID($b)
        ));
    }

    public function editBadge($badge_id, $data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($badge_id));
        $this->mongo_db->set('stackable', (int)$data['stackable']);
        $this->mongo_db->set('substract', (int)$data['substract']);
        $this->mongo_db->set('quantity', (int)$data['quantity']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('hint', $data['hint']);
        $this->mongo_db->set('language_id', (int)1);
        $this->mongo_db->update('playbasis_badge');

        if (isset($data['image'])) {
            $this->mongo_db->where('_id', new MongoID($badge_id));
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $this->mongo_db->update('playbasis_badge');
        }

    }

    public function deleteBadge($badge_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($badge_id));
        $this->mongo_db->delete('playbasis_badge');
        $this->mongo_db->where('badge_id',  new MongoID($badge_id));
        $this->mongo_db->delete('playbasis_badge_to_client');
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