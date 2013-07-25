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
}
?>