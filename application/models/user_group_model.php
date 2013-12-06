<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_model extends MY_model{

	public function __construct(){
		parent::__construct();
		$this->load->library('mongo_db');

	}

	public function getTotalNumUsers(){
		return $this->mongo_db->count('user_group');
	}


}