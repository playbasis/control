<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    //protected $dbs = null;
    //protected $dbGroups = null;
    protected $site = 0;

    //array of database groups to load for each site_id
	protected static $dblist = array(
		0 => 'core',
		1 => 'core_true'
	);
    //mongodb setup
    private static $mongoBDsNames = array(
        0 => 'core',
        1 => 'core_true'
    );
    private static $mongoDBs = array(
        0 => 0,
        '52ea1ec18d8c897807000075' => 1,
        '52ea1ebd8d8c89001a00004e' => 1
    );
    protected $mongoSite = 0;

    public function __construct()
    {
        parent::__construct();
    }
    public function get_site()
    {
        return $this->site;
    }
    public function set_site_mongodb($site_id)
    {
        $currDB = self::$mongoDBs[$this->mongoSite];
        $id = is_object($site_id) ? $site_id->{'$id'} : $site_id;
        $this->mongoSite = isset(self::$mongoDBs[$id]) ? $id : 0;
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
        $this->mongo_db->inc($table, 1);
        $this->mongo_db->update('id_vault');
        $this->mongo_db->select($table);
        $result = $this->mongo_db->get('id_vault');
        return ($result) ? $result[0][$table] : 0;
    }
    //load all databases
    //private function multi_db_load($mdl)
    //{
    //    $this->dbs = array();
    //    $this->dbGroups = array();
    //    foreach(self::$dblist as $key => $value)
    //    {
    //        $this->dbs[$key] = $mdl->load->database($value, TRUE);
    //        $this->dbGroups[$key] = $value;
    //    }
    //}
	/* Assume $str to be in 'YYYY-mm-dd' format */
	public function new_mongo_date($str, $t='00:00:00') {
		return new MongoDate(strtotime($str.' '.$t));
	}
	public static function get_number_of_days($year_month) {
		return date('t', strtotime($year_month.'-01 00:00:00'));
	}
	public static function get_year_month($key) {
		$str = explode('-', $key, 3);
		return $str[0].'-'.$str[1];
	}
	public static function week_to_date($key) {
		$str = explode('-', $key, 3);
		$year_month = $str[0].'-'.$str[1];
		$ndays = MY_Model::get_number_of_days($year_month);
		$h = array();
		foreach (array(1,2,3,4) as $i => $j) {
			$d = ceil($i*$ndays/4.0)+1;
			$h['w'.$j] = ($d < 10 ? '0'.$d : ''.$d);
		}
		return $year_month.'-'.$h[$str[2]];
	}
	public static function date_to_week($str) {
		$key = explode('-', $str, 3);
		$year_month = $key[0].'-'.$key[1];
		$ndays = MY_Model::get_number_of_days($year_month);
		return $year_month.'-w'.ceil(intval($key[2])/($ndays/4.0));
	}
}
