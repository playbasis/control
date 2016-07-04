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
            '_id',
            'client_id',
            'site_id',
            'game_name',
            'item_category_id',
            'item_list',
            'image',
            'game_config',
            'tags',
            'status',
            'date_added',
            'date_modified',

        ));

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'deleted' => false
        ));

        if (isset($data['status'])) {
            $this->mongo_db->where('status', $data['status']);
        }

        $result = $this->mongo_db->get('playbasis_game_to_client');

        return !empty($result) ? $result : array();
    }
}