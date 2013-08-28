<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_Model
{
    public function getAction($action_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results;
    }
}
?>