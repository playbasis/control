<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Plan_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('memcached_library');
		$this->load->helper('memcache');
		$this->load->library('mongo_db');
	}

	public function listDisplayPlans($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->where('display', true);
		$this->mongo_db->order_by(array('price' => 1));
		return $this->mongo_db->get("playbasis_plan");
	}
}
?>