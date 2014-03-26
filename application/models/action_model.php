<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
		$this->load->library('mongo_db');
	}
	public function listActions($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array('name'));
		$this->mongo_db->select(array(),array('_id'));
		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'status' => true
		));
		$result = $this->mongo_db->get('playbasis_action_to_client');
		return $result ? $result : array();
	}
	public function findAction($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array('action_id'));
		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'name' => strtolower($data['action_name'])
		));
		$result = $this->mongo_db->get('playbasis_action_to_client');
		return $result ? $result[0]['action_id'] : array() ;
	}
	public function actionLog($data, $action_name, $from=null, $to=null)
	{
		$this->set_site_mongodb($data['site_id']);
		$map = new MongoCode("function() { emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), 1); }");
		$reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
		$query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id'], 'action_name' => $action_name);
		if ($from || $to) $query['date_added'] = array();
		if ($from) $query['date_added']['$gte'] = $this->new_mongo_date($from);
		if ($to) $query['date_added']['$lte'] = $this->new_mongo_date($to);
		$this->mongo_db->command(array(
			'mapReduce' => 'playbasis_action_log',
			'map' => $map,
			'reduce' => $reduce,
			'query' => $query,
			'out' => 'mapreduce_action_log',
		));
		$result = $this->mongo_db->get('mapreduce_action_log');
		return $result ? $result : array();
	}
}
?>