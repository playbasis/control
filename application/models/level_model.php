<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Level_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
    }

    public function getLevelDetail($level, $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $leveldata = array();
        $level_range = array($level, intval($level)+1);

        //check if client have their own exp table setup
        $this->mongo_db->select(array("level","exp","level_title","image"));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_in("level", $level_range);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => 1));
        $this->mongo_db->limit(2);
        $levela = $this->mongo_db->get('playbasis_client_exp_table');

        if(empty($levela))
        {
            //get level from default exp table instead
            $this->mongo_db->select(array("level","exp","level_title","image"));
            $this->mongo_db->where_in("level", $level_range);
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $this->mongo_db->limit(2);
            $levela = $this->mongo_db->get('playbasis_exp_table');

        }
        $i = 0;
        foreach($levela as $l){
            $l['min_exp'] = $l['exp'];
//            var_Dump($levela[$i+1]);
            $l['max_exp'] = (isset($levela[$i+1]))?intval($levela[$i+1]['exp'])-1:null;
            unset($l['exp']);
            $leveldata = $l;
            break;
        }

        return $leveldata;
    }

    public function getLevelsDetail($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        //check if client have their own exp table setup
        $this->mongo_db->select(array("level","exp","level_title","image"));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => 1));
        $leveldata = $this->mongo_db->get('playbasis_client_exp_table');

        if(empty($leveldata))
        {
            //get level from default exp table instead
            $this->mongo_db->select(array("level","exp","level_title","image"));
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $leveldata = $this->mongo_db->get('playbasis_exp_table');

        }

        $i = 0;
        foreach($leveldata as &$l){
            $l['max_exp'] = (isset($leveldata[$i+1]))?intval($leveldata[$i+1]['min_exp'])-1:null;
            $i++;
        }

        return $leveldata;
    }
}
?>