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
        $this->set_site($site_id);
        //check if client have their own exp table setup
        $this->site_db()->select("level,exp AS min_exp,level_title,image");
        $this->site_db()->where('client_id', $client_id);
        $this->site_db()->where('site_id', $site_id);
        $this->site_db()->where("level", $level);
        $this->site_db()->where("status", '1');
        $this->site_db()->order_by('exp');
        $this->site_db()->limit(1);
        $level = db_get_row_array($this, 'playbasis_client_exp_table');
        $level['max_exp'] = null;

        $next_level = (intval($level)+1);
        $this->site_db()->select("exp AS max_exp");
        $this->site_db()->where('client_id', $client_id);
        $this->site_db()->where('site_id', $site_id);
        $this->site_db()->where("level", $next_level);
        $this->site_db()->where("status", '1');
        $this->site_db()->order_by('exp');
        $this->site_db()->limit(1);
        $levelmax = db_get_row_array($this, 'playbasis_client_exp_table');

        if(isset($levelmax['max_exp'])){
            $level['max_exp'] = intval($levelmax['max_exp'])-1;
        }
        if(!isset($level['level']))
        {
            //get level from default exp table instead
            $this->site_db()->select("level,exp AS min_exp,level_title,image");
            $this->site_db()->where("level", $level);
            $this->site_db()->where("status", '1');
            $this->site_db()->order_by('exp');
            $this->site_db()->limit(1);
            $level = db_get_row_array($this, 'playbasis_exp_table');
            $level['max_exp'] = null;

            $this->site_db()->select("exp AS max_exp");
            $this->site_db()->where("level", $next_level);
            $this->site_db()->where("status", '1');
            $this->site_db()->order_by('exp');
            $this->site_db()->limit(1);
            $levelmax = db_get_row_array($this, 'playbasis_exp_table');

            if(isset($levelmax['max_exp'])){
                $level['max_exp'] = intval($levelmax['max_exp'])-1;
            }
        }
        return $level;
    }

    public function getLevelsDetail($client_id, $site_id)
    {
        $this->set_site($site_id);
        $this->site_db()->select_min('level');
        $slevel = db_get_row_array($this, 'playbasis_exp_table');
        if(!isset($slevel['level'])){
            $min_level = 1;
        }else{
            $min_level = $slevel['level'];
        }

        $this->site_db()->select_max('level');
        $slevel = db_get_row_array($this, 'playbasis_exp_table');
        if(!isset($slevel['level'])){
            $max_level = 100;
        }else{
            $max_level = $slevel['level'];
        }

        for($i = $min_level; $i <= $max_level; $i++){
            $level[$i] = $this->getLevelDetail($i, $client_id, $site_id);
        }

        return $level;
    }
}
?>