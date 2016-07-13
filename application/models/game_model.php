<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveGame($client_id, $site_id, $data = array())
    {
        $this->set_site_mongodb($site_id);

        // Searching
        if (isset($data['game_id']) && !empty($data['game_id'])){
            $this->mongo_db->where('_id', new MongoId($data['game_id']));
        }
        if (isset($data['game_name']) && !empty($data['game_name'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($data['game_name'])) . "/i");
            $this->mongo_db->where('game_name', $regex);
        }
        if (isset($data['tags']) && !empty($data['tags'])){
            $this->mongo_db->where_in('tags', $data['tags']);
        }

        // Sorting
        $sort_data = array('_id', 'game_name', 'date_added', 'date_modified');

        if (isset($data['order']) && (mb_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            if (isset($data['sort']) && $data['sort'] === "random") {
                $this->mongo_db->order_by(array('date_added' => 1));
            } else {
                $this->mongo_db->order_by(array('_id' => $order));
            }
        }

        // Paging
        if ((isset($data['offset']) || isset($data['limit'])) && !(isset($data['sort']) && $data['sort'] == "random")) {
            if (isset($data['offset']) && !empty($data['offset'])) {
                if ($data['offset'] < 0) {
                    $data['offset'] = 0;
                }
            } else {
                $data['offset'] = 0;
            }

            if (isset($data['limit']) && !empty($data['limit'])) {
                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }
            } else {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['offset']);
        }

        $this->mongo_db->select(array(
            'game_name',
            'item_category_id',
            'item_list',
            'image',
            'game_config',
            'tags',
            'date_added',
            'date_modified',
        ));
        //$this->mongo_db->select(array(), array('_id'));

        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'deleted' => false
        ));

        if (isset($data['status'])) {
            $this->mongo_db->where('status', $data['status']);
        }

        $result = $this->mongo_db->get('playbasis_game_to_client');

        return !empty($result) ? $result : array();
    }

    public function retrieveStage($client_id, $site_id, $game_id, $query_data)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('game_id', new MongoId($game_id));

        if(isset($query_data['stage_name']) && !empty($query_data['stage_name'])){
            $this->mongo_db->where('stage_name', $query_data['stage_name']);
        }

        if(isset($query_data['stage_level']) && !empty($query_data['stage_level'])){
            $this->mongo_db->where('stage_level', (int)$query_data['stage_level']);
        }

        $this->mongo_db->select(array(
            //'game_id',
            'stage_name',
            'stage_level',
            'descriptrion',
            'stage_config',
            'image',
            'item_id',
            //'date_added',
            //'date_modified',
        ));
        $this->mongo_db->select(array(), array('_id'));

        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_game_stage_to_client');
        return !empty($result) ? $result : array();
    }

    public function retrieveItem($client_id, $site_id, $game_id, $query_data, $item_id_list = null)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('game_id', new MongoId($game_id));

        if (isset($query_data['game_item_name']) && !empty($query_data['game_item_name'])){
            $this->mongo_db->where('game_item_name', $query_data['game_item_name']);
        }
        if (isset($query_data['game_item_id']) && !empty($query_data['game_item_id'])){
            $this->mongo_db->where('_id', new MongoId($query_data['game_item_id']));
        }

        if (isset($item_id_list) && !empty($item_id_list)){
            $this->mongo_db->where_in('item_id', $item_id_list);
        }

        $this->mongo_db->select(array(
            //'game_id',
            'item_id',
            'item_config',
            //'date_added',
            //'date_modified',
        ));
        $this->mongo_db->select(array(), array('_id'));

        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_game_item_to_client');
        return !empty($result) ? $result : array();
    }

    public function getTemplateByCurrentDate($client_id, $site_id, $query_data)
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
                        //array('date_start' => null)
                    )
                ),
                array(
                    '$or' => array(
                        array('date_end' => array('$gte' => new MongoDate())),
                        //array('date_end' => null)
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

        $result = $this->mongo_db->get('playbasis_game_template_to_client');
        return !empty($result) ? $result[0] : array();

    }

    public function getTemplate($client_id, $site_id, $query_data)
    {
        $this->set_site_mongodb($site_id);
        if (isset($query_data['game_id']) && !empty($query_data['game_id'])) {
            $this->mongo_db->where('game_id', new MongoId($query_data['game_id']));
        }else{
            return false;
        }

        if (isset($query_data['template_id']) && !empty($query_data['template_id'])){
            $this->mongo_db->where('_id', new MongoId($query_data['template_id']));
        }
        if (isset($query_data['template_name']) && !empty($query_data['template_name'])){
            $this->mongo_db->where('template_name', $query_data['template_name']);
        }

        $this->mongo_db->limit(1);
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_game_template_to_client');
        return !empty($result) ? $result[0] : array();

    }

    public function getItemTemplate($client_id, $site_id, $query_data)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'game_id' => new MongoId($query_data['game_id']),
            'item_id' => new MongoId($query_data['item_id']),
            'template_id' => new MongoId($query_data['template_id']),
            'deleted' => false
        ));

        $result = $this->mongo_db->get('playbasis_game_item_to_template');
        return !empty($result) ? $result[0] : array();
    }
}