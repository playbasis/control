<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	//protected $dbs = null;
	//protected $dbGroups = null;
	protected $site = 0;
	
	//array of database groups to load for each site_id
	protected static $dblist = array(
		0 => 'core',
		1 => 'core'
	);
	
	//mongodb setup
	private static $mongoBDsNames = array(
		0 => 'pbapp',
		1 => 'pbapp'
	);
	private static $mongoDBs = array(
		0 => 0,
		1 => 0,
		2 => 0
	);
	protected $mongoSite = 0;
	
	public function __construct()
	{
		parent::__construct();
		//$this->multi_db_load($this);
	}
	public function set_site($site_id)
	{
		$this->site = (isset(self::$dblist[$site_id]) && self::$dblist[$site_id]) ? $site_id : 0;
		$clientdb = self::$dblist[$this->site];
		$this->db->query("USE $clientdb;");
	}
	public function set_site_mongodb($site_id)
	{
		$currDB = self::$mongoDBs[$this->mongoSite];
		$this->mongoSite = isset(self::$mongoDBs[$site_id]) ? $site_id : 0;
		$newDB = self::$mongoDBs[$this->mongoSite];
		if($currDB == $newDB)
			return; //no need to switch
		$this->mongo_db->switch_db(self::$mongoBDsNames[$newDB]);
	}
	public function site_db()
	{
		return $this->db; //$this->dbs[$this->site];
	}
	public function generate_id_mongodb($table)
	{
		$this->mongo_db->select($table);
		$this->mongo_db->inc(array($table => 1));
		$this->mongo_db->update('id_vault');
		$this->mongo_db->select($table);
		$result = $this->mongo_db->get('id_vault');
		return ($result) ? $result[0][$table] : 0;
	}
	//load all databases
	//private function multi_db_load($mdl)
	//{
	//	$this->dbs = array();
	//	$this->dbGroups = array();
	//	foreach(self::$dblist as $key => $value)
	//	{
	//		$this->dbs[$key] = $mdl->load->database($value, TRUE);
	//		$this->dbGroups[$key] = $value;
	//	}
	//}
}

