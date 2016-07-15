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

    public function getClientRewardIDByName($client_id, $site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('reward_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('name', $name);
        $result =  $this->mongo_db->get('playbasis_reward_to_client');

        return $result ? $result[0]['reward_id']."" : null;
    }

    public function getClientRewardNameByRewardID($client_id, $site_id, $reward_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('reward_id', $reward_id);
        $result =  $this->mongo_db->get('playbasis_reward_to_client');

        return $result ? $result[0]['name'] : null;
    }

    public function badgeLog($data, $badge_id, $from = null, $to = null)
    {
        if (!($badge_id instanceof MongoId)) {
            $badge_id = new MongoId($badge_id);
        }
        $this->set_site_mongodb($data['site_id']);
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), this.value); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('site_id' => $data['site_id'], 'event_type' => 'REWARD', 'item_id' => $badge_id);
        if ($from || $to) {
            $query['date_added'] = array();
        }
        if ($from) {
            $query['date_added']['$gte'] = $this->new_mongo_date($from);
        }
        if ($to) {
            $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
        }
        $result = $this->mongo_db->command(array(
            'mapReduce' => 'playbasis_event_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => array('inline' => 1),
        ));
        $result = $result ? $result['results'] : array();
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 'SKIP'));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 'SKIP'));
        }
        return $result;
    }
}

?>