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

    public function getBadgeBySiteId($site_id) {
        $badges_client_data = array();

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($site_id));
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        foreach ($results as $result) {

            $badge_info = $this->getBadge($result['badge_id']);

            if($badge_info){
                $badges_client_data[] = array(
                    'client_id' => $result['client_id'],
                    'badge_id' => $result['badge_id'],
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
                array_push($badges, array("id"=>$rown['_id'],"name"=>$rown['name'],"img_path"=>$rown['image'],"description"=>$rown['description']));
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