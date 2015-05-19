<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lithium_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function findSiteIdByToken($token) {
        $this->mongo_db->select(array('site_id'));
        $this->mongo_db->where('token', $token);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_lithium_subscription');
        return $result ? $result[0]['site_id'] : array();
    }
}
?>