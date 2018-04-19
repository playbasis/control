<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custompoints_model extends MY_Model
{

    public function insertCustompoints($data)
    {

        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $field1 = array(
            "field_type" => "read_only",
            "label" => "Name",
            "param_name" => "reward_name",
            "placeholder" => "",
            "sortOrder" => "0",
            "value" => strtolower($data['name']),
        );

        $field2 = array(
            "param_name" => "item_id",
            "label" => "",
            "placeholder" => "",
            "sortOrder" => "0",
            "field_type" => "hidden",
            "value" => "",
        );

        $field3 = array(
            "field_type" => "number",
            "label" => "quantity",
            "param_name" => "quantity",
            "placeholder" => "How many ...",
            "sortOrder" => "0",
            "value" => "0",
        );

        $insert_data = array(
            'reward_id' => new MongoId(),
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'group' => 'POINT',
            'name' => strtolower($data['name']),
            'quantity' => $data['quantity'],
            'per_user' => $data['per_user'],
            'per_user_include_deducted' => $data['per_user_include_deducted'],
            'limit_per_day' => $data['limit_per_day'],
            'limit_start_time' => $data['limit_start_time'],
            'limit' => null,
            'description' => null,
            'sort' => 1,
            'tags' => isset($tags) ? $tags : null,
            'status' => true,
            'is_custom' => true,
            'pending' => $data['pending'],
            'init_dataset' => array($field1, $field2, $field3),
            'type' => $data['type'],
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        if ($data['type'] != 'normal') {
            $energy_props_arr = array(
                "maximum" => $data['maximum'],
                "changing_period" => $data['changing_period'],
                "changing_per_period" => $data['changing_per_period']
            );
            $insert_data['energy_props'] = $energy_props_arr;
        }

        $insert = $this->mongo_db->insert('playbasis_reward_to_client', $insert_data);

        return $insert;
    }

    public function getCustompoints($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if(isset($data['filter_type']) && !is_null($data['filter_type'])){
            $this->mongo_db->where('type', $data['filter_type']);
        }
        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            if ($data['filter_quantity']){
                $this->mongo_db->where('quantity', null);
            }else{
                $this->mongo_db->where_gte('quantity', 0);
            }
        }
        if (isset($data['filter_per_user']) && !is_null($data['filter_per_user'])) {
            if ($data['filter_per_user']){
                $this->mongo_db->where('per_user', null);
            }else{
                $this->mongo_db->where_gte('per_user', 0);
            }
        }
        if (isset($data['filter_per_day']) && !is_null($data['filter_per_day'])) {
            if ($data['filter_per_day']){
                $this->mongo_db->where('limit_per_day', null);
            }else{
                $this->mongo_db->where_gte('limit_per_day', 0);
            }
        }
        if (isset($data['filter_pending_support']) && !is_null($data['filter_pending_support'])) {
            if ($data['filter_pending_support']){
                $this->mongo_db->where('pending', 'on');
            }else{
                $this->mongo_db->where('pending', false);
            }
        }
        if(isset($data['filter_tags']) && $data['filter_tags']) {
            $tags = explode(',', $data['filter_tags']);
            $this->mongo_db->where_in('tags', $tags);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
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

        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('is_custom', true);
        $this->mongo_db->where('status', true);
        return $this->mongo_db->get("playbasis_reward_to_client");
    }

    public function countCustompoints($client_id, $site_id, $filter=array())
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('is_custom', true);
        $this->mongo_db->where('status', true);
        if($filter){
            if (isset($filter['filter_name']) && !is_null($filter['filter_name'])) {
                $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($filter['filter_name'])) . "/i");
                $this->mongo_db->where('name', $regex);
            }
        }
        $countCustompoints = $this->mongo_db->count('playbasis_reward_to_client');

        return $countCustompoints;
    }

    public function getCustompoint($custompoint_id)
    {
        $this->mongo_db->where('reward_id', new MongoId($custompoint_id));
        $c = $this->mongo_db->get('playbasis_reward_to_client');
        return $c ? $c[0] : null;
    }

    public function getCustompointById($custompoint_id)
    {
        $this->mongo_db->where('_id', new MongoId($custompoint_id));
        $c = $this->mongo_db->get('playbasis_reward_to_client');
        return $c ? $c[0] : null;
    }

    public function getCustompointByName($site_id, $custompoint_name)
    {
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('name', $custompoint_name);
        $this->mongo_db->where('is_custom', true);
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);

        $result =  $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0] : null;
    }

    public function getCustompointByNameButNotID($site_id, $custompoint_name, $custompoint_id)
    {
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('reward_id', new MongoId($custompoint_id));
        $this->mongo_db->where('name', $custompoint_name);
        $this->mongo_db->where('is_custom', true);
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);

        $result =  $this->mongo_db->get('playbasis_reward_to_client');
        return $result ? $result[0] : null;
    }

    public function auditBeforeCustomPoint($event,$custompoint_id, $user_id)
    {
        $custompoint_data = $this->getCustompoint($custompoint_id);
        $insert_data = array('client_id' => $custompoint_data['client_id'],
                             'site_id' => $custompoint_data['site_id'],
                             'reward_id' => $custompoint_data['reward_id'],
                             'event' => $event,
                             'before' => $custompoint_data,
                             'user_id' => $user_id);
        return $this->mongo_db->insert('playbasis_reward_to_client_audit', $insert_data);
    }

    public function auditAfterCustomPoint($event, $custompoint_id, $user_id, $audit_id=null)
    {
        $custompoint_data = $this->getCustompoint($custompoint_id);
        $audit_log = array();
        if ($audit_id){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $audit_log = $this->mongo_db->get('playbasis_reward_to_client_audit');
        }

        if ($audit_log){
            $this->mongo_db->where('_id', new MongoID($audit_id));
            $this->mongo_db->set('after', $custompoint_data);
            $this->mongo_db->update('playbasis_reward_to_client_audit');
        } else {
            $insert_data = array('client_id' => $custompoint_data['client_id'],
                                 'site_id' => $custompoint_data['site_id'],
                                 'reward_id' => $custompoint_data['reward_id'],
                                 'event' => $event,
                                 'before' => null,
                                 'after' => $custompoint_data,
                                 'user_id' => $user_id);
            $this->mongo_db->insert('playbasis_reward_to_client_audit', $insert_data);
        }
    }

    public function updateCustompoints($data)
    {
        if (!empty($data['tags'])){
            $tags = explode(',', $data['tags']);
        }

        $this->mongo_db->where('reward_id', new MongoID($data['reward_id']));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('quantity', $data['quantity']);
        $this->mongo_db->set('per_user', $data['per_user']);
        $this->mongo_db->set('per_user_include_deducted', $data['per_user_include_deducted']);
        $this->mongo_db->set('limit_per_day', $data['limit_per_day']);
        $this->mongo_db->set('limit_start_time', $data['limit_start_time']);
        $this->mongo_db->set('pending', $data['pending']);
        $this->mongo_db->set('type', $data['type']);

        if ($data['type'] != 'normal') {
            $this->mongo_db->set('energy_props.maximum', $data['maximum']);
            $this->mongo_db->set('energy_props.changing_period', $data['changing_period']);
            $this->mongo_db->set('energy_props.changing_per_period', $data['changing_per_period']);
        } else {
            $this->mongo_db->unset_field('energy_props');
        }

        $this->mongo_db->set('init_dataset.0.value', $data['name']);
        $this->mongo_db->set('init_dataset.2.label', "quantity");

        $this->mongo_db->set('tags', isset($tags) ? $tags : null);

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_reward_to_client');

        // update rule engine //
        $this->mongo_db->where(array('jigsaw_set.specific_id' => $data['reward_id']));
        $this->mongo_db->set(array('jigsaw_set.$.name' => $data['name']));
        $this->mongo_db->set(array('jigsaw_set.$.dataSet.0.value' => $data['name']));
        $this->mongo_db->set(array('jigsaw_set.$.config.reward_name' => $data['name']));
        $this->mongo_db->update_all('playbasis_rule');
        // end update rule engine //

        return $update;
    }

    public function deleteCustompoints($custompoint_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('reward_id', new MongoID($custompoint_id));
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_reward_to_client');

        $this->mongo_db->where('reward_id', new MongoID($custompoint_id));
        $this->mongo_db->delete_all('playbasis_reward_to_player');

    }

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

    public function getRewardId($data)
    {
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('is_custom', true);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('name', $data['name']);
        $this->mongo_db->where('type', $data['type']);

        $result = $this->mongo_db->get("playbasis_reward_to_client");
        return isset($result[0]['reward_id']) ? $result[0]['reward_id'] : null;
    }

    public function getRewardStatus($id)
    {
        $this->mongo_db->select(array('status'));
        $this->mongo_db->where('_id', $id);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get("playbasis_reward_status_to_player");
        return isset($result[0]) ? $result[0]['status'] : null;
    }
}