<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Energy_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function findActiveEnergyRewardsById($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where(array(
            'status' => true,
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $this->mongo_db->where_in('type', array('loss', 'gain'));
        $result = $this->mongo_db->get('playbasis_reward_to_client');

        return !empty($result) ? $result : array();
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
                    array("w" => 0, "j" => false, "continueOnError" => true));

            } catch (Exception $e) {
                var_dump($e);
            }


        }
        return false;
    }
}