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
		$this->_api = $this->playbasisapi;
	}
	public function set_read_preference($readPreference, $tags=array())
	{
		$this->mongo_db->setReadPreference($readPreference, $tags);
	}
	public function set_read_preference_primary()
	{
		$this->mongo_db->setReadPreferencePrimary();
	}
	public function set_read_preference_secondary()
	{
		$this->mongo_db->setReadPreferenceSecondary();
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
	public function new_mongo_date($str, $t='00:00:00') {
		return new MongoDate(strtotime($str.' '.$t));
	}
}

