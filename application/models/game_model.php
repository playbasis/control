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

    public function getGameNameByID($client_id, $site_id, $game_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('_id', new MongoID($game_id));
        
        $results = $this->mongo_db->get("playbasis_game_to_client");
        $results = $results ? $results[0]['game_name'] : null;

        return $results;
    }

    public function getGameList($client_id, $site_id, $game_id=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);

        if ($game_id){
            $this->mongo_db->where('_id', new MongoID($game_id));
        }
        $this->mongo_db->order_by(array('game_name', 'ASC'));
        $results = $this->mongo_db->get("playbasis_game_to_client");

        return $results;
    }

    public function countGameList($client_id, $site_id, $game_id=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);

        if ($game_id){
            $this->mongo_db->where('_id', new MongoID($game_id));
        }
        $results = $this->mongo_db->count("playbasis_game_to_client");

        return $results;
    }

    public function getCampaign($client_id, $site_id, $campaign_id=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('deleted', false);

        if ($campaign_id){
            $this->mongo_db->where('_id', new MongoID($campaign_id));
        }
        $results = $this->mongo_db->get("playbasis_campaign_to_client");

        return $results;
    }

    public function countCampaign($client_id, $site_id, $campaign_id=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('deleted', false);

        if ($campaign_id){
            $this->mongo_db->where('_id', new MongoID($campaign_id));
        }
        $results = $this->mongo_db->count("playbasis_campaign_to_client");

        return $results;
    }

    public function insertGameCampaign($client_id, $site_id, $data)
    {
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
        $results = $this->mongo_db->insert("playbasis_game_campaign_to_client",$insert_data);

        return $results;
    }

    public function getGameCampaign($client_id, $site_id, $data=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('deleted', false);

        if (isset($data['limit'])) {
            $this->mongo_db->limit((int)$data['limit']);
        } else {
            $this->mongo_db->limit(10);
        }

        if (isset($data['offset'])) {
            $this->mongo_db->offset((int)$data['offset']);
        } else {
            $this->mongo_db->offset(0);
        }

        $results = $this->mongo_db->get("playbasis_game_campaign_to_client");

        return $results;
    }

    public function countGameCampaign($client_id, $site_id, $campaign_id=false)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->count("playbasis_game_campaign_to_client");

        return $results;
    }

    public function deleteGameCampaign($game_campaign_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($game_campaign_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update("playbasis_game_campaign_to_client");
    }

    public function updateStatusGameCampaign($game_campaign_id,$status)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($game_campaign_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('status', $status);
        $this->mongo_db->update("playbasis_game_campaign_to_client");
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

    public function resetItemToPlayerById($client_id, $site_id, $item_list)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $date = new MongoDate();
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_in('badge_id', $item_list);
        $this->mongo_db->set('date_modified', $date);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->update_all('playbasis_reward_to_player');
    }

    public function updateGameStageLastReset($client_id, $site_id, $game_id, $stage_id, $reset_date)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('game_id', new MongoId($game_id));
        $this->mongo_db->where('_id', new MongoId($stage_id));
        $this->mongo_db->set('last_reset', $reset_date);
        $this->mongo_db->update('playbasis_game_stage_to_client');
    }

    public function getRules($client_id, $site_id, $id =false)
    {
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        if ($id){
            $this->mongo_db->where('_id', new MongoId($id));
        }
        return $this->mongo_db->get('playbasis_rule');
    }

    public function countRules($client_id, $site_id, $id =false)
    {
        $this->mongo_db->select(array('name'));
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        if ($id){
            $this->mongo_db->where('_id', new MongoId($id));
        }
        return $this->mongo_db->count('playbasis_rule');
    }
}