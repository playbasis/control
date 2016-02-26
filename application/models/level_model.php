<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getLevelDetail($level, $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $leveldata = array();
        $level_range = array(intval($level), intval($level) + 1);

        //check if client have their own exp table setup
        $this->mongo_db->select(array("level", "exp", "level_title", "image"));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_in("level", $level_range);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => 1));
        $this->mongo_db->limit(2);
        $levela = $this->mongo_db->get('playbasis_client_exp_table');

        if (empty($levela)) {
            //get level from default exp table instead
            $this->mongo_db->select(array("level", "exp", "level_title", "image"));
            $this->mongo_db->select(array(), array('_id'));
            $this->mongo_db->where_in("level", $level_range);
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $this->mongo_db->limit(2);
            $levela = $this->mongo_db->get('playbasis_exp_table');

        }
        $i = 0;

        foreach ($levela as $l) {
            $l['min_exp'] = $l['exp'];
            $l['max_exp'] = (isset($levela[$i + 1])) ? intval($levela[$i + 1]['exp']) - 1 : null;
            $l['level_image'] = $l['image'];
            unset($l['exp']);
            unset($l['image']);
            $leveldata = $l;
            break;
        }

        return $leveldata;
    }

    public function getLevelsDetail($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        //check if client have their own exp table setup
        $this->mongo_db->select(array("level", "exp", "level_title", "image"));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => 1));
        $leveldata = $this->mongo_db->get('playbasis_client_exp_table');

        if (empty($leveldata)) {
            //get level from default exp table instead
            $this->mongo_db->select(array("level", "exp", "level_title", "image"));
            $this->mongo_db->select(array(), array('_id'));
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $leveldata = $this->mongo_db->get('playbasis_exp_table');

        }

        $i = 0;
        foreach ($leveldata as &$l) {
            $l['min_exp'] = $l['exp'];
            $l['max_exp'] = (isset($leveldata[$i + 1])) ? intval($leveldata[$i + 1]['exp']) - 1 : null;
            $l['level_image'] = $l['image'];
            unset($l['exp']);
            unset($l['image']);
            $i++;
        }

        return $leveldata;
    }

    public function getLevelByExp($exp, $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        //check if client have their own exp table setup
        $this->mongo_db->select(array("level", "exp", "level_title", "image"));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_lte("exp", $exp);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => -1));
        $this->mongo_db->limit(1);
        $levela = $this->mongo_db->get('playbasis_client_exp_table');

        //check if client have their own exp table setup
        $this->mongo_db->select(array("level", "exp", "level_title", "image"));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_gt("exp", $exp);
        $this->mongo_db->where("status", true);
        $this->mongo_db->order_by(array('exp' => 1));
        $this->mongo_db->limit(1);
        $levelb = $this->mongo_db->get('playbasis_client_exp_table');

        if (empty($levela)) {
            //get level from default exp table instead
            $this->mongo_db->select(array("level", "exp", "level_title", "image"));
            $this->mongo_db->select(array(), array('_id'));
            $this->mongo_db->where_lte("exp", $exp);
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $this->mongo_db->limit(1);
            $levela = $this->mongo_db->get('playbasis_exp_table');

            //check if client have their own exp table setup
            $this->mongo_db->select(array("level", "exp", "level_title", "image"));
            $this->mongo_db->select(array(), array('_id'));
            $this->mongo_db->where_gt("exp", $exp);
            $this->mongo_db->where("status", true);
            $this->mongo_db->order_by(array('exp' => 1));
            $this->mongo_db->limit(1);
            $levelb = $this->mongo_db->get('playbasis_client_exp_table');

        }

        if (empty($levela)) {
            $levela['exp'] = 0;
            $levela['level'] = 1;
            $levela['level_title'] = '';
            $levela['image'] = '';
        } else {
            $levela = $levela[0];
        }
        if (empty($levelb)) {
            $levelb = $levela;
        } else {
            $levelb = $levelb[0];
        }

        $l['min_exp'] = $levela['exp'];
        $l['max_exp'] = $levelb['exp'];
        $l['level'] = $levela['level'];
        $l['level_title'] = $levela['level_title'];
        $l['level_image'] = $levela['image'];

        $leveldata = $l;

        return $leveldata;
    }
}

?>