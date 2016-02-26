<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feature_model extends MY_Model
{
    public function getFeature($feature_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($feature_id));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results ? $results[0] : null;
    }

    public function getFeatures()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('status', true);
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results;
    }

    public function getFeatureByClientId($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('status', true);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature_to_client");

        $temp = array();

        foreach ($results as $result) {
            $temp[$result['feature_id'] . ""] = array(
                '_id' => $result['_id'],
                'name' => $result['name'],
                'link' => $result['link'],
                'icon' => $result['icon']
            );
        }
        return $temp;
    }

    public function getFeatureBySiteId($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('status', true);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature_to_client");

        return $results;
    }

    public function getFeatureName($feature_name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', $feature_name);
        $results = $this->mongo_db->get("playbasis_feature");

        return $results ? $results[0] : null;
    }

    public function getFeatureExistByClientId($client_id, $link)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('status', true);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('link', $link);
        $this->mongo_db->limit(1);

        return $this->mongo_db->count("playbasis_feature_to_client") > 0;
    }
}

?>