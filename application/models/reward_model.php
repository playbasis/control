<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reward_model extends MY_Model
{
    public function getBadgeRewardBySiteId($site_id) {

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($site_id));
        $this->mongo_db->where('group',  'NONPOINT');
        $this->mongo_db->where('name',  'badge');
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results ? $results[0] : null;
    }

    public function getReward($reward_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($reward_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_reward");

        return $results ? $results[0] : null;
    }

    public function getRewards($data = array()) {
        $this->set_site_mongodb(0);
        
        $reward_data = "";

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $order = -1;
        } else {
            $order = 1;
        }

        $sort_data = array(
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('sort_order' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results = $this->mongo_db->get("playbasis_reward");

        foreach ($results as $result) {
            $limit = $this->getRewardLimitByRewardId($data['plan_id'], $result['reward_id']);

            $reward_data[] = array(
                'reward_id' => $result['reward_id'],
                'name' => $result['name'],
                'description' => $result['description'],
                'limit' => $limit,
                'status'  	  => (bool)$result['status'],
                'sort_order'  => $result['sort_order'],
                'date_added' => $result['date_added'],
                'date_modified' => $result['date_modified'],
                'badge_id' => $result['badge_id']
            );
        }

        return $reward_data;
    }

    public function getRewardLimitByRewardId($plan_id, $reward_id) {
        $this->set_site_mongodb(0);
        $this->mongo_db->where('reward_id', new MongoID($reward_id));
        $this->mongo_db->where('plan_id', (int)$plan_id);
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results ? $results[0]['limit'] : null;
    }

    public function getRewardByClientId($client_id) {
        $this->set_site_mongodb(0);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $reward_data =  $this->mongo_db->get("playbasis_reward_to_client");

        return $reward_data;
    }

    public function getAnotherRewardBySiteId($site_id) {

        $this->set_site_mongodb(0);
        $reward_data = array();

        $this->mongo_db->where('group',  'NONPOINT');
        $this->mongo_db->where('name',  'badge');
        $reward = $this->mongo_db->get("playbasis_reward");

        $reward_id = $reward ? $reward[0]["_id"] : null;

        $this->mongo_db->where('site_id',  new MongoID($site_id));
        $this->mongo_db->where('reward_id',  new MongoID($reward_id));
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        foreach ($results as $result) {
            $reward_info = $this->getReward($result['reward_id']);
            if($reward_info){
                $reward_data[] = array(
                    'reward_id' => $result['_id'],
                    'site_id' => $result['site_id'],
                    'client_id' => $result['client_id'],
                    'limit' => $result['limit'],
                    'group' => $reward_info['group'],
                    'name' => $reward_info['name'],
                    'description' => $reward_info['description']
                );
            }
        }

        return $reward_data;
    }
}
?>