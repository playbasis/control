<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    private function getById($id, $collection)
    {
        $this->mongo_db->where(array('_id' => $id));
        $results = $this->mongo_db->get($collection);
        return $results ? $results[0] : null;
    }

    public function getPlanById($plan_id)
    {
        return $this->getById($plan_id, 'playbasis_plan');
    }

    public function getLimitPlanByClientId($client_id, $feature_group = null)
    {

        $plan_id = $this->getPlanIdByClientId(new MongoID($client_id));
        $this->mongo_db->select(array('limit_' . $feature_group));
        $this->mongo_db->where('_id', new MongoID($plan_id));
        $result = $this->mongo_db->get("playbasis_plan");
        $result = $result[0];

        return isset($result['limit_' . $feature_group]) != null ? $result['limit_' . $feature_group] : null;
    }

    public function listDisplayPlans($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->where('display', true);
        $this->mongo_db->order_by(array('price' => 1));
        return $this->mongo_db->get("playbasis_plan");
    }

    public function getPlanIdByClientId($client_id)
    {
        $permission = $this->getLatestPermissionByClientId($client_id);
        return $permission ? $permission['plan_id'] : null;
    }

    public function getLatestPermissionByClientId($client_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_permission');
        return $results ? $results[0] : null;
    }
}

?>