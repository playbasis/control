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
    public function findActiveEnergyRewards($mongo_site_id = 0)
    {
        $this->set_site_mongodb($mongo_site_id);

        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array('status' => true));
        $this->mongo_db->where_in('type', array('loss', 'gain'));
        $result = $this->mongo_db->get('playbasis_reward_to_client');

        return !empty($result) ? $result : array();
    }
    public function findActiveEnergyRewardsById( $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array('status' => true,
                                    'client_id' => $client_id,
                                    'site_id' => $site_id
        ));
        $this->mongo_db->where_in('type', array('loss', 'gain'));
        $result = $this->mongo_db->get('playbasis_reward_to_client');

        return !empty($result) ? $result : array();
    }
    /**
     * @param $client_id
     * @param $site_id
     * @param $energy_id
     * @param $timestamp_now
     * @param string $changing_period must be in '00:00' format
     * @param $energy_inc_value
     * @param int $mongo_site_id DEPRECATED used to switch between db.
     * @return array
     */
    public function updatePlayersEnergyValueWithConditions(
        $client_id,
        $site_id,
        $energy_id,
        $timestamp_now,
        $changing_period,
        $energy_inc_value,
        $mongo_site_id = 0
    ) {
        if (!strrchr($changing_period, ':')) {
            return false;
        }

        $tmp1 = new MongoDate(time());
        $tmp2 = new MongoDate(time());

        $temp_arr = explode(':', $changing_period);
        $period['hours'] = (int)$temp_arr[0];
        $period['minutes'] = (int)$temp_arr[1];
        $period['ts'] = (int)(($period['minutes'] * 60) + ($period['hours'] * 60 * 60));

        $this->set_site_mongodb($mongo_site_id);
        $this->mongo_db->select(array('type', 'energy_props'));
        $this->mongo_db->where(array('reward_id' => $energy_id));
        $energy = $this->mongo_db->get('playbasis_reward_to_client');

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'reward_id' => $energy_id
        ));
        $this->mongo_db->where_lt('date_modified', new MongoDate(($timestamp_now - $period['ts'])));

        if ($energy[0]['type'] == "gain") {
            $this->mongo_db->where_lt('value', intval($energy[0]['energy_props']['maximum']));
            $this->mongo_db->inc('value', $energy_inc_value);

        } elseif ($energy[0]['type'] == "loss") {
            $this->mongo_db->where_gt('value', 0);
            $this->mongo_db->inc('value', -(int)$energy_inc_value);
        }
        $this->mongo_db->set('date_modified', new MongoDate($timestamp_now));
        $this->mongo_db->set('date_cron_modified', new MongoDate($timestamp_now));
        return $this->mongo_db->update_all('playbasis_reward_to_player');
    }

    /**
     * @param $client_id
     * @param $site_id
     * @param bool|false $return_count_only
     * @param int $offset
     * @param null $limit
     * @param int $mongo_site_id DEPRECATED used to switch between db.
     * @return array|int
     */
    public function findPlayersToInsert(
        $client_id,
        $site_id,
        $return_count_only = false,
        $offset = 0,
        $limit = null,
        $mongo_site_id = 0
    ) {
        $this->set_site_mongodb($mongo_site_id);

        $this->mongo_db->select(array('_id', 'cl_player_id'));

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $this->mongo_db->order_by(array('_id' => 'ASC'));

        if ($return_count_only == true) {
            $result = $this->mongo_db->count('playbasis_player');
            return $result;
        } else {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
            $result = $this->mongo_db->get('playbasis_player');
            return !empty($result) ? $result : array();
        }
    }

    /**
     * Bulk insert initial energy value to db.
     * Use with caution and make sure unique indexes is set
     *
     * @param $batch_data
     * @param int $mongo_site_id
     * @return bool
     */
    public function bulkInsertInitialValue($batch_data, $mongo_site_id = 0)
    {
        $this->set_site_mongodb($mongo_site_id);

        if (!empty($batch_data) && is_array($batch_data)) {
            try {
                return $this->mongo_db->batch_insert('playbasis_reward_to_player', $batch_data,
                    array("w" => 0, "j" => false));

            } catch (Exception $e) {
                var_dump($e);
            }


        }
        return false;
    }
}