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

    public function createPlayerToNode($client_id, $site_id, $pb_player_id, $node_id , $role = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $pb_player_id,
            'node_id' => $node_id,

        );

        if(isset($role))
            $insert_data['role'] = $role;

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_player', $insert_data);

        return $insert;
    }

    public function createRoleToPlayer($client_id, $site_id, $pb_player_id, $role = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', $pb_player_id);

        if(isset($role))
            $this->mongo_db->set('role', $role);
        else
            $this->mongo_db->unset_field('role');
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_player');

        return $update;
    }
}