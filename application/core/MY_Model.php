<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	protected $dbs = null;
	protected $site = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->dbs = $this->multi_db_load($this);
	}
	public function set_site($site_id)
	{
		$this->site = (isset($this->dbs[$site_id]) && $this->dbs[$site_id]) ? $site_id : 0;
	}
	public function site_db()
	{
		return $this->dbs[$this->site];
	}
	//load all databases
	private function multi_db_load($mdl)
	{
		//array of database groups to load for each site_id
		$dblist = array(
			0 => 'developer',
			1 => 'demo'
		);
		$multiDBs = array();
		foreach($dblist as $key => $value)
		{
			$multiDBs[$key] = $mdl->load->database($value, TRUE);
		}
		return $multiDBs;
	}
}

