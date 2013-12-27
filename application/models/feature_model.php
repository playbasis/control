<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
class Feature_model extends MY_Model
{
    public function getFeature($feature_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($feature_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results ? $results[0] : null;
    }

    public function getFeatures() {
        $this->set_site_mongodb(0);
        $this->mongo_db->where('status', true);
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results;
    }

    public function getFeatureByClientId($client_id) {

        $this->set_site_mongodb(0);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature_to_client");

        $temp = array();

        foreach($results as $result){
            $temp[$result['feature_id'].""] = array(
                '_id'=>$result['_id'],
                'name'=>$result['name'],
                'link'=>$result['link'],
                'icon'=>$result['icon']
                );
        }        
        return $temp;
    }
}
?>