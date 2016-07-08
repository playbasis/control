<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getTemplateById($client_id, $site_id, $query_data)
    {
        $this->set_site_mongodb($site_id);
        if (isset($query_data['game_id']) && !empty($query_data['game_id'])) {
            $this->mongo_db->where('game_id', new MongoId($query_data['game_id']));
        }else{
            return false;
        }

        $this->mongo_db->where(array(
            '$and' => array(
                array(
                    '$or' => array(
                        array('date_start' => array('$lt' => new MongoDate())),
                        array('date_start' => null)
                    )
                ),
                array(
                    '$or' => array(
                        array('date_end' => array('$gte' => new MongoDate())),
                        array('date_end' => null)
                    )
                )
            )
        ));
        $this->mongo_db->order_by(array('weight' => 'asc'));
        $this->mongo_db->limit(1);
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_template_to_client');
        return !empty($result) ? $result[0] : array();

    }
}