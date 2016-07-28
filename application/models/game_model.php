<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends MY_Model
{

    public function getGameSetting($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_name', $data['game_name']);

        if(isset($data['filter_status'])){
            $this->mongo_db->where('status', $data['filter_status']);
        }
        $results = $this->mongo_db->get("playbasis_game_to_client");
        $results = $results ? $results[0] : null;

        return $results;
    }

    public function getGameList($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);

        $results = $this->mongo_db->get("playbasis_game_to_client");

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
            $this->mongo_db->where('game_name', $data['game_name']);
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

    public function getGameStageItem($client_id, $site_id, $game_id, $data=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('deleted', false);
        if(isset($data['item_id'])){
            $this->mongo_db->where('item_id', new MongoID($data['item_id']));
        }

        $results = $this->mongo_db->get("playbasis_game_item_to_client");

        return $results;
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
            $this->mongo_db->where('item_id', new MongoID($data['item_id']));

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

    public function deleteGameStageItem($client_id, $site_id, $game_id, $data)
    {
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('game_id', new MongoId($game_id));

        if(isset($data['id'])){
            $this->mongo_db->where('item_id', new MongoId($data['id']));
        }

        if(isset($data['del_items'])){
            $this->mongo_db->where_in('item_id', $data['del_items']);
        }

        $result = $this->mongo_db->update_all('playbasis_game_item_to_client');

        return $result;
    }

    public function getGameStage($client_id, $site_id, $game_id, $data=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('game_id', new MongoId($game_id));
        $this->mongo_db->where('deleted', false);
        if(isset($data['id'])){
            $this->mongo_db->where('_id', new MongoId($data['id']));
        }

        if(isset($data['exclude_id'])){
            $this->mongo_db->where_not_in('_id', $data['exclude_id']);
        }
        $this->mongo_db->order_by(array('stage_level' => 'ASC'));
        $results = $this->mongo_db->get("playbasis_game_stage_to_client");
        return $results;
    }

    public function updateGameStage($client_id, $site_id, $game_id, $data, $stage_id = null)
    {
        if ($stage_id) {
            $date = new MongoDate();
            $date_array = array(
                'date_modified' => $date
            );
            $update_data = array_merge($data, $date_array);

            foreach ($update_data as $key => $value) {
                $this->mongo_db->set($key, $value);
            }

            $this->mongo_db->where('client_id', new MongoId($client_id));
            $this->mongo_db->where('site_id', new MongoId($site_id));
            $this->mongo_db->where('game_id', new MongoId($game_id));
            $this->mongo_db->where('_id', new MongoId($stage_id));

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

    public function deleteGameStage($client_id, $site_id, $game_id, $data)
    {
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('game_id', new MongoId($game_id));

        if(isset($data['id'])){
            $this->mongo_db->where('_id', new MongoId($data['id']));
        }

        if(isset($data['exclude_id'])){
            $this->mongo_db->where_not_in('_id', $data['exclude_id']);
        }

        $result = $this->mongo_db->update_all('playbasis_game_stage_to_client');

        return $result;
    }

    public function countGameTemplate($client_id, $site_id, $game_id, $data=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('deleted', false);
        if(isset($data['id'])){
            $this->mongo_db->where('_id', new MongoID($data['id']));
        }

        $results = $this->mongo_db->count("playbasis_game_template_to_client");

        return $results;
    }

    public function getGameTemplate($client_id, $site_id, $game_id, $data=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('deleted', false);
        if(isset($data['id'])){
            $this->mongo_db->where('_id', new MongoID($data['id']));
        }
        $results = $this->mongo_db->get("playbasis_game_template_to_client");

        return $results;
    }

    public function updateGameTemplate($client_id, $site_id, $game_id, $data)
    {
        if (isset($data['id']) && $this->getGameTemplate($client_id, $site_id, $game_id, $data)) {
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
            $this->mongo_db->where('_id', new MongoID($data['id']));

            $result = $this->mongo_db->update('playbasis_game_template_to_client');

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

            $result = $this->mongo_db->insert('playbasis_game_template_to_client', $insert_data);
        }

        return $result;
    }

    public function deleteGameTemplate($client_id, $site_id, $game_id, $template_id)
    {
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('game_id', new MongoId($game_id));
        $this->mongo_db->where('_id', new MongoId($template_id));

        $result = $this->mongo_db->update('playbasis_game_template_to_client');
        log_message('error', $result );
        return $result;
    }

    public function getGameItemTemplate($client_id, $site_id, $game_id, $data=null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('game_id', new MongoID($game_id));
        $this->mongo_db->where('item_id', new MongoID($data['item_id']));
        $this->mongo_db->where('deleted', false);
        
        if(isset($data['template_id'])){
            $this->mongo_db->where('template_id', new MongoID($data['template_id']));
        }

        $results = $this->mongo_db->get("playbasis_game_item_to_template");

        return $results;
    }

    public function updateGameItemTemplate($client_id, $site_id, $game_id, $data)
    {
        if (isset($data['id']) && $this->getGameItemTemplate($client_id, $site_id, $game_id, $data)) {
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
            $this->mongo_db->where('template_id', new MongoID($data['template_id']));
            $this->mongo_db->where('item_id', new MongoID($data['item_id']));
            $this->mongo_db->where('deleted', false);

            $result = $this->mongo_db->update('playbasis_game_item_to_template');

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

            $result = $this->mongo_db->insert('playbasis_game_item_to_template', $insert_data);
        }

        return $result;
    }

    public function getItemToPlayerById($client_id, $site_id, $player_id, $item_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('badge_id', null);
        $this->mongo_db->where('badge_id', new MongoID($item_id));
        $this->mongo_db->where('pb_player_id', new MongoID($player_id));
        $result = $this->mongo_db->get('playbasis_reward_to_player');

        return $result ? $result[0] : null;
    }

    public function deductItemToPlayerById($client_id, $site_id, $player_id, $item_id, $quantity)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $date = new MongoDate();
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where(array(
            'pb_player_id' => new MongoID($player_id),
            'badge_id' => new MongoID($item_id)
        ));
        $this->mongo_db->set('date_modified', $date);
        $this->mongo_db->inc('value', intval($quantity));
        $this->mongo_db->update('playbasis_reward_to_player');
    }
}