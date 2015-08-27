<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Energy_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    /**
     * @param int $mongo_site_id DEPRECATED used to switch between db.
     * @return array result of active energies, empty array if none found.
     */
    public function findActiveEnergyTypeRewards($mongo_site_id = 0)
    {
        $this->set_site_mongodb($mongo_site_id);

        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array('status' => true));
        $this->mongo_db->where_in('type', array('loss', 'gain'));
        $result = $this->mongo_db->get('playbasis_reward_to_client');

        return !empty($result) ? $result : array();
    }

    /**
     * @param $client_id
     * @param $site_id
     * @param $energy_id
     * @param int $mongo_site_id DEPRECATED used to switch between db.
     * @return array
     */
    public function findPlayersByEnergy($client_id, $site_id, $energy_id, $mongo_site_id = 0)
    {
        $this->set_site_mongodb($mongo_site_id);

        $this->mongo_db->select(array('pb_player_id', 'cl_player_id'));

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'reward_id' => $energy_id
        ));
        $result = $this->mongo_db->get('playbasis_reward_to_player');

        return !empty($result) ? $result : array();
    }

    /**
     * @param $client_id
     * @param $site_id
     * @param array $exclusions list of pb_player_id to exclude from query.
     * @param int $mongo_site_id DEPRECATED used to switch between db.
     * @return array
     */
    public function findPlayersWithExclusions($client_id, $site_id, $exclusions = array(), $mongo_site_id = 0)
    {
        $this->set_site_mongodb($mongo_site_id);

        $this->mongo_db->select(array('_id', 'cl_player_id'));

        if (!empty($exclusions)) {
            $this->mongo_db->where_not_in('_id', $exclusions);
        }

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $result = $this->mongo_db->get('playbasis_player');

        return !empty($result) ? $result : array();
    }
}