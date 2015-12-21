<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_org_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveNodeById($site_id, $id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize_to_client");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function createPlayerToNode($client_id, $site_id, $pb_player_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'node_id' => $node_id,

        );

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_player', $insert_data);

        return $insert;
    }

    public function retrievePlayerToNode($client_id, $site_id, $pb_player_id, $node_id, $role_name = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('pb_player_id', new MongoId($pb_player_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        if (isset($role_name)) {
            $this->mongo_db->where('roles.name', $role_name);
        }

        $c = $this->mongo_db->get("playbasis_store_organize_to_player");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function deletePlayerToNode($client_id, $site_id, $pb_player_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('pb_player_id', new MongoId($pb_player_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));
        $c = $this->mongo_db->delete("playbasis_store_organize_to_player");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function setPlayerRoleToNode($client_id, $site_id, $pb_player_id, $node_id, $role)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('pb_player_id', new MongoId($pb_player_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $this->mongo_db->push('roles', $role);

        $update = $this->mongo_db->update('playbasis_store_organize_to_player');

        return $update;
    }

    public function unsetPlayerRoleToNode($client_id, $site_id, $pb_player_id, $node_id, $role_name_to_unset)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('pb_player_id', new MongoId($pb_player_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $this->mongo_db->pull('roles', array("name" => $role_name_to_unset));

        $update = $this->mongo_db->update('playbasis_store_organize_to_player');

        return $update;
    }
}