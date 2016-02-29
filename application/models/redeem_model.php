<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Redeem_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function findByReferenceId($type, $refId, $site_id = 0)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', $refId);
        $results = $this->mongo_db->get('playbasis_redeem_to_player');
        return $results ? $results[0] : array();
    }

    public function exerciseCode($type, $client_id, $site_id, $pb_player_id, $code)
    {
        $this->set_site_mongodb($site_id);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_redeem_to_player', array(
            'type' => $type,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'code' => $code,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        ));
    }
}

?>