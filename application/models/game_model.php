<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends MY_Model
{

    public function getGameSetting($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('name', $data['name']);

        $results = $this->mongo_db->get("playbasis_game_to_client");
        $results = $results ? $results[0] : null;

        return $results;
    }

    public function getGameStageItem($client_id, $site_id, $game_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('item_id', $data['item_id']);

        $results = $this->mongo_db->get("playbasis_game_item_to_client");
        $results = $results ? $results[0] : null;

        return $results;
    }

    public function getGameStage($client_id, $site_id, $game_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('level', $data['level']);

        $results = $this->mongo_db->get("playbasis_game_stage_to_client");
        $results = $results ? $results[0] : null;
        
        return $results;
    }

    public function updateGameSetting($client_id, $site_id, $data)
    {
        $game = $this->getGameSetting($client_id, $site_id, $data);
        if ($game) {
            $date = new MongoDate();
            $date_array = array(
                'date_modified' => $date
            );

            $update_data = array_merge($data, $date_array);

            foreach ($update_data as $key => $value) {
                $this->mongo_db->set($key, $value);
            }
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('name', $data['name']);
            $result = $this->mongo_db->update('playbasis_game_to_client');
            if($result) $result = $game['_id'];
        } else {
            $this->set_site_mongodb($this->session->userdata('site_id'));

            $date = new MongoDate();
            $date_array = array(
                'client_id'     => $client_id,
                'site_id'       => $site_id,
                'date_added'    => $date,
                'date_modified' => $date,
                'deleted'       => false
            );
            $insert_data = array_merge($data, $date_array);

            $result = $this->mongo_db->insert('playbasis_game_to_client', $insert_data);
        }

        return $result;
        
        
    }

    public function updateGameStageItem($client_id, $site_id, $game_id, $data)
    {
        if ($this->getGameStageItem($client_id, $site_id, $game_id, $data)) {
            $date = new MongoDate();
            $date_array = array(
                'date_modified' => $date
            );

            $update_data = array_merge($data, $date_array);

            foreach ($update_data as $key => $value) {
                $this->mongo_db->set($key, $value);
            }
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('game_id', new MongoID($game_id));
            $this->mongo_db->where('item_id', $data['item_id']);

            $result = $this->mongo_db->update('playbasis_game_item_to_client');

        } else {
            $this->set_site_mongodb($this->session->userdata('site_id'));

            $date = new MongoDate();
            $date_array = array(
                'client_id'     => $client_id,
                'site_id'       => $site_id,
                'game_id'       => $game_id,
                'date_added'    => $date,
                'date_modified' => $date,
                'deleted'       => false
            );
            $insert_data = array_merge($data, $date_array);

            $result = $this->mongo_db->insert('playbasis_game_item_to_client', $insert_data);
        }

        return $result;
    }

    public function updateGameStage($client_id, $site_id, $game_id, $data)
    {
        if ($this->getGameStage($client_id, $site_id, $game_id, $data)) {
            $date = new MongoDate();
            $date_array = array(
                'date_modified' => $date
            );

            $update_data = array_merge($data, $date_array);

            foreach ($update_data as $key => $value) {
                $this->mongo_db->set($key, $value);
            }

            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->where('game_id', new MongoID($game_id));
            $this->mongo_db->where('level', $data['level']);

            $result = $this->mongo_db->update('playbasis_game_stage_to_client');

        } else {
            $this->set_site_mongodb($this->session->userdata('site_id'));

            $date = new MongoDate();
            $date_array = array(
                'client_id'     => $client_id,
                'site_id'       => $site_id,
                'game_id'       => $game_id,
                'date_added'    => $date,
                'date_modified' => $date,
                'deleted'       => false
            );
            $insert_data = array_merge($data, $date_array);

            $result = $this->mongo_db->insert('playbasis_game_stage_to_client', $insert_data);
        }

        return $result;
    }
}