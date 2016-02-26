<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reward_model extends MY_Model
{
    public function getBadgeRewardBySiteId($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('group', 'NONPOINT');
        $this->mongo_db->where('name', 'badge');
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results ? $results[0] : null;
    }

    public function getReward($reward_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($reward_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_reward");

        return $results ? $results[0] : null;
    }

    public function getRewards($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

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
            $limit = $this->getRewardLimitByRewardId($data['plan_id'], $result['_id']);

            $reward_data[] = array(
                'reward_id' => $result['reward_id'],
                'name' => $result['name'],
                'description' => $result['description'],
                'limit' => $limit,
                'status' => (bool)$result['status'],
                'sort_order' => $result['sort_order'],
                'date_added' => $result['date_added'],
                'date_modified' => $result['date_modified'],
                'badge_id' => isset($result['badge_id']) ? $result['badge_id'] : null
            );
        }

        return $reward_data;
    }

    public function getRewardLimitByRewardId($plan_id, $reward_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('reward_to_plan.reward_id', new MongoID($reward_id));
        $this->mongo_db->where('_id', new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_plan");

        return $results ? $results[0]['limit'] : null;
    }

    public function getRewardByClientId($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $reward_data = $this->mongo_db->get("playbasis_reward_to_client");

        return $reward_data;
    }

    public function getAnotherRewardBySiteId($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('group', 'POINT');
        $this->mongo_db->where('status', true);
        $this->mongo_db->where_not_in('name', array('badge', 'exp', 'point'));
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results;
    }

    public function getAnotherRewardByClientId($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('group', 'POINT');
        $this->mongo_db->where_not_in('name', array('badge', 'exp', 'point'));
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results;
    }

    public function getRewardByName($name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', $name);
        return $this->mongo_db->get('playbasis_reward');
    }
}

?>