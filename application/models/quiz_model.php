<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quiz_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
    }

    public function find($client_id, $site_id, $nin=null) {
        $d = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('name','image','description','weight'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where_lte('date_start', $d);
        $this->mongo_db->where_gt('date_expire', $d);
        if ($nin !== null) $this->mongo_db->where_not_in('_id', $nin);
        return $this->mongo_db->get('playbasis_quiz_to_client');
    }

    public function find_by_id($client_id, $site_id, $quiz_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(),array('client_id','site_id','date_added','date_modified'));
        $this->mongo_db->where('_id', $quiz_id);
        $results = $this->mongo_db->get('playbasis_quiz_to_client');
        return $results ? $results[0] : null;
    }

    public function find_quiz_done_by_player($client_id, $site_id, $pb_player_id, $limit=-1) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('quiz_id','value'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $this->mongo_db->order_by(array('date_modified' => -1));
        if ($limit > 0) $this->mongo_db->limit($limit);
        return $this->mongo_db->get('playbasis_quiz_to_player');
    }
}
?>