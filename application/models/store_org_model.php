<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_org_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function retrieveNode($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($optionalParams['search'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['id'])) {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            }
        }
        if (isset($optionalParams['organize_id']) && !is_null($optionalParams['organize_id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['organize_id'])) {
                $organize = new MongoId($optionalParams['organize_id']);
                $this->mongo_db->where('organize', $organize);
            }
        }
        if (isset($optionalParams['parent_id']) && !is_null($optionalParams['parent_id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['parent_id'])) {
                $parent_node_id = new MongoId($optionalParams['parent_id']);
                $this->mongo_db->where('parent', $parent_node_id);
            }
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        // Paging
        if (isset($optionalParams['offset']) || isset($optionalParams['limit'])) {
            if (isset($optionalParams['offset'])) {
                if ($optionalParams['offset'] < 0) {
                    $optionalParams['offset'] = 0;
                }
            } else {
                $optionalParams['offset'] = 0;
            }

            if (isset($optionalParams['limit'])) {
                if ($optionalParams['limit'] < 1) {
                    $optionalParams['limit'] = 20;
                }
            } else {
                $optionalParams['limit'] = 20;
            }

            $this->mongo_db->limit((int)$optionalParams['limit']);
            $this->mongo_db->offset((int)$optionalParams['offset']);
        }

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_store_organize_to_client");
    }

    public function retrieveOrganize($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(mb_strtolower($optionalParams['search'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            if (MongoId::isValid($optionalParams['id'])) {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            }
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        // Paging
        if (isset($optionalParams['offset']) || isset($optionalParams['limit'])) {
            if (isset($optionalParams['offset'])) {
                if ($optionalParams['offset'] < 0) {
                    $optionalParams['offset'] = 0;
                }
            } else {
                $optionalParams['offset'] = 0;
            }

            if (isset($optionalParams['limit'])) {
                if ($optionalParams['limit'] < 1) {
                    $optionalParams['limit'] = 20;
                }
            } else {
                $optionalParams['limit'] = 20;
            }

            $this->mongo_db->limit((int)$optionalParams['limit']);
            $this->mongo_db->offset((int)$optionalParams['offset']);
        }

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get("playbasis_store_organize");
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

    public function retrieveOrganizeById($client_id, $site_id, $id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize");

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
            $this->mongo_db->where_exists('roles.' . $role_name, true);
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

        $this->mongo_db->set('roles.' . $role['name'], $role['value']);

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

        $this->mongo_db->unset_field('roles.' . $role_name_to_unset);

        $update = $this->mongo_db->update('playbasis_store_organize_to_player');

        return $update;
    }
}