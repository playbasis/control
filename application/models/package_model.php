<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Package_model extends MY_Model
{
	public function getAllPlans(){
		return $this->mongo_db->get('playbasis_plan');
	}
}