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
		$this->mongo_db->select(array('name','icon'));
		$this->mongo_db->select(array(),array('_id'));
		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'status' => true
		));
		$result = $this->mongo_db->get('playbasis_action_to_client');
		if (!$result) $result = array();
		return $result;
	}

	public function listActionsOnlyUsed($data)
	{

		/*
		| -----------------------------------------------------------------------
		| ACTIONS THAT ARE IN PLAYERS HAVE...
		| -----------------------------------------------------------------------
		*/
		$rawUsedActions = $this->mongo_db->command(array('distinct'=>'playbasis_action_log', 'key'=>'action_id','query'=>array('client_id'=>$data['client_id'], 'site_id'=>$data['site_id'])));

		$usedActions = $rawUsedActions['values'];

		$this->set_site_mongodb($data['site_id']);

		$this->mongo_db->select(array('name','icon'));

		$this->mongo_db->select(array(),array('_id'));
		
		$this->mongo_db->where_in('action_id', $usedActions);

		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'status' => true
		));

		$result = $this->mongo_db->get('playbasis_action_to_client');
		if (!$result) $result = array();		

		/*
		| -----------------------------------------------------------------------
		| ACTIONS THAT ARE IN RULE ENGINES BUT PLAYERS MAY OR MAY HAVE NOT HAVE THEM...
		| -----------------------------------------------------------------------

		$rawUsedActions = $this->getUsedActionByClientSiteId($data['client_id'], $data['site_id']);

		$usedActions = $rawUsedActions['values'];
		$this->set_site_mongodb($data['site_id']);

		$this->mongo_db->select(array('name','icon'));

		$this->mongo_db->select(array(),array('_id'));
		
		$this->mongo_db->where_in('action_id', $usedActions);

		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'status' => true
		));

		$result = $this->mongo_db->get('playbasis_action_to_client');
		if (!$result) $result = array();
		*/

		return $result;
	}

	public function getUsedActionByClientSiteId($client_id, $site_id){
		return $this->mongo_db->command(array('distinct'=>'playbasis_rule', 'key'=>'action_id','query'=>array('client_id'=>$client_id, 'site_id'=>$site_id,'active_status'=>true)));
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
		$map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), 1); }");
		$reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
		$query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id'], 'action_name' => $action_name);
		if ($from || $to) $query['date_added'] = array();
		if ($from) $query['date_added']['$gte'] = $this->new_mongo_date($from);
		if ($to) $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
		$result = $this->mongo_db->command(array(
			'mapReduce' => 'playbasis_action_log',
			'map' => $map,
			'reduce' => $reduce,
			'query' => $query,
			'out' => array('inline' => 1),
		));
		if (!$result) $result = array();
		if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) array_unshift($result, array('_id' => $from, 'value' => 'SKIP'));
		if ($to && (!isset($result[count($result)-1]['_id']) || $result[count($result)-1]['_id'] != $to)) array_push($result, array('_id' => $to, 'value' => 'SKIP'));
		return $result;
	}
}
?>