<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends MY_Model
{
    public function getClient($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($client_id));
        $results = $this->mongo_db->get("playbasis_client");

        return $results ? $results[0] : null;
    }

    public function getTotalClients($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('company', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_client");
        return $total;
    }

    public function getClients($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('email', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            'first_name',
            'last_name',
            'company',
            'status',
            '_id'
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

        $results = $this->mongo_db->get("playbasis_client");

        return $results;
    }

    public function listClients($fields)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select($fields);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->order_by(array('first_name' => 1));
        $results = $this->mongo_db->get("playbasis_client");

        return $results;
    }

    public function addClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'company' => isset($data['company']) ? $data['company'] : '',
            'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
            'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
            'mobile' => isset($data['mobile']) ? $data['mobile'] : '',
            'email' => isset($data['email']) ? $data['email'] : '',
            'status' => (bool)$data['status'],
            'deleted' => false,
            'date_start' => $data['date_start'] ? new MongoDate(strtotime($data['date_start'])) : null,
            'date_expire' => $data['date_expire'] ? new MongoDate(strtotime($data['date_expire'])) : null,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
        );

        if (isset($data['image'])) {
            $insert_data['image'] = html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8');
        }

        return $this->mongo_db->insert('playbasis_client', $insert_data);
    }

    public function editClient($client_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($client_id));
        $this->mongo_db->set('company', isset($data['company']) ? $data['company'] : '');
        $this->mongo_db->set('first_name', $data['first_name']);
        $this->mongo_db->set('last_name', $data['last_name']);
        $this->mongo_db->set('mobile', $data['mobile']);
        $this->mongo_db->set('email', $data['email']);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('date_start', $data['date_start'] ? new MongoDate(strtotime($data['date_start'])) : null);
        $this->mongo_db->set('date_expire',
            $data['date_expire'] ? new MongoDate(strtotime($data['date_expire'])) : null);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }

        $this->mongo_db->update('playbasis_client');

        /* update plan */
        $data_filter = array(
            'client_id' => $client_id,
            'site_id' => null,
            'plan_id' => $data['plan_id']
        );
        $this->addPlanToPermission($data_filter);
        $sites = $this->getSitesByClientId($client_id);
        if ($sites) {
            foreach ($sites as $site) {
                $data_filter = array(
                    'client_id' => $client_id,
                    'site_id' => $site['_id'],
                    'plan_id' => $data['plan_id'],
                );
                $this->copyRewardToClient($data_filter);
                $this->copyFeaturedToClient($data_filter);
                $this->copyActionToClient($data_filter);
                $this->copyJigsawToClient($data_filter);
            }
        }

        if (isset($data['site_value'])) {
            foreach ($data['site_value'] as $site_value) {

                $this->mongo_db->where('_id', new MongoID($site_value['site_id']));
                $this->mongo_db->set('status', (bool)$site_value['status']);
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_client_site');

                $data_filter = array(
                    'client_id' => $client_id,
                    'site_id' => $site_value['site_id'],
                    'plan_id' => $data['plan_id'],
                    'status' => (bool)$site_value['status'],
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                );

                $this->addPlanToPermission($data_filter);
                $this->copyRewardToClient($data_filter);
                $this->copyFeaturedToClient($data_filter);
                $this->copyActionToClient($data_filter);
                $this->copyJigsawToClient($data_filter);
            }
        }

        if (isset($data['user_value'])) {

            //$this->mongo_db->where('client_id', new MongoID($client_id));
            //$this->mongo_db->delete_all('user_to_client');

            foreach ($data['user_value'] as $user_value) {

                $this->mongo_db->where('_id', new MongoID($user_value['user_id']));
                if ($user_value['user_group_id']) {
                    $this->mongo_db->set('user_group_id', new MongoID($user_value['user_group_id']));
                } else {
                    $this->mongo_db->set('user_group_id', null);
                }
                $this->mongo_db->set('status', (bool)$user_value['status']);
                $this->mongo_db->update('user');

                /* $data_insert = array(
                     'client_id' => new MongoID($client_id),
                     'user_id' => new MongoID($user_value['user_id']),
                     'status' => (bool)$user_value['status']
                 );
                 $this->mongo_db->insert('user_to_client', $data_insert);*/
            }
        }
    }

    public function deleteClient($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($client_id));
        $this->mongo_db->set('status', (bool)false);
        $this->mongo_db->set('deleted', (bool)true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_client');
    }

    public function addPlanToPermission($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $data_insert = array(
            'plan_id' => new MongoID($data['plan_id']),
            'client_id' => new MongoID($data['client_id']),
            'site_id' => null,
            'date_added' => $d,
            'date_modified' => $d,
        );
        if ($data['site_id']) { // apply to only 1 site_id
            $this->mongo_db->where(array('site_id' => new MongoID($data['site_id'])));
            $n = $this->mongo_db->count("playbasis_permission");
            if ($n > 0) {
                $this->mongo_db->where(array('site_id' => new MongoID($data['site_id'])));
                $this->mongo_db->set('plan_id', new MongoID($data['plan_id']));
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                return $this->mongo_db->update('playbasis_permission');
            } else {
                $data_insert['site_id'] = new MongoID($data['site_id']);
                return $this->mongo_db->insert('playbasis_permission', $data_insert);
            }
        } else { // no site_id, either (1) insert new client with null site_id, or (2) apply to all site_ids
            $this->mongo_db->where(array('client_id' => new MongoID($data['client_id'])));
            $n = $this->mongo_db->count("playbasis_permission");
            if ($n > 0) {
                $this->mongo_db->where(array('client_id' => new MongoID($data['client_id'])));
                $this->mongo_db->set('plan_id', new MongoID($data['plan_id']));
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                return $this->mongo_db->update_all('playbasis_permission');
            } else {
                return $this->mongo_db->insert('playbasis_permission', $data_insert);
            }
        }
    }

    /****start Dupicate with another model but in codeigniter cannot load another model within model ****/
    public function getPlan($plan_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_plan");

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

    public function getFeature($feature_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($feature_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results ? $results[0] : null;
    }

    public function getAction($action_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results ? $results[0] : null;
    }

    /****end Dupicate with another model but in codeigniter cannot load another model within model ****/

    public function getJigsaw($jigsaw_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($jigsaw_id));
        $results = $this->mongo_db->get("playbasis_jigsaw");

        return $results ? $results[0] : null;
    }

    public function copyRewardToClient($data_filter)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_reward_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if ($plan_data['reward_to_plan']) {
            $d = new MongoDate();
            $insert_data = array();
            foreach ($plan_data['reward_to_plan'] as $reward) {
                $limit = empty($reward['limit']) ? null : (int)$reward['limit'];
                $reward_data = $this->getReward($reward['reward_id']);
                $insert_data[] = array(
                    'reward_id' => new MongoID($reward['reward_id']),
                    'client_id' => new MongoID($data_filter['client_id']),
                    'site_id' => new MongoID($data_filter['site_id']),
                    'group' => $reward_data['group'],
                    'name' => $reward_data['name'],
                    'description' => $reward_data['description'],
                    'init_dataset' => $reward_data['init_dataset'],
                    'limit' => $limit,
                    'sort_order' => $reward_data['sort_order'],
                    'status' => (bool)$reward_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d,
                    'is_custom' => false,
                );
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_reward_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }
    }

    public function copyFeaturedToClient($data_filter)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->delete_all("playbasis_feature_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if (isset($plan_data['feature_to_plan'])) {
            $d = new MongoDate();
            $insert_data = array();
            foreach ($plan_data['feature_to_plan'] as $feature_id) {
                $feature_data = $this->getFeature($feature_id);
                $insert_data[] = array(
                    'feature_id' => new MongoID($feature_id),
                    'client_id' => new MongoID($data_filter['client_id']),
                    'site_id' => new MongoID($data_filter['site_id']),
                    'name' => $feature_data['name'],
                    'description' => $feature_data['description'],
                    'sort_order' => $feature_data['sort_order'],
                    'status' => (bool)$feature_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d,
                    'link' => $feature_data['link'],
                    'icon' => $feature_data['icon']
                );
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_feature_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }
    }

    public function copyActionToClient($data_filter)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_action_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if (isset($plan_data['action_to_plan'])) {
            $d = new MongoDate();
            $insert_data = array();
            foreach ($plan_data['action_to_plan'] as $action_id) {
                $this->mongo_db->where('client_id', $data_filter['client_id']);
                $this->mongo_db->where('site_id', $data_filter['site_id']);
                $this->mongo_db->where('action_id', $action_id);
                $allClients = $this->mongo_db->get('playbasis_action_to_client');
                if (!$allClients) {
                    $action_data = $this->getAction($action_id);
                    $insert_data[] = array(
                        'action_id' => new MongoID($action_id),
                        'client_id' => new MongoID($data_filter['client_id']),
                        'site_id' => new MongoID($data_filter['site_id']),
                        'name' => $action_data['name'],
                        'description' => $action_data['description'],
                        'icon' => $action_data['icon'],
                        'color' => $action_data['color'],
                        'init_dataset' => $action_data['init_dataset'],
                        'sort_order' => $action_data['sort_order'],
                        'status' => (bool)$action_data['status'],
                        'date_modified' => $d,
                        'date_added' => $d,
                        'is_custom' => false,
                    );
                }
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_action_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }
    }

    public function copyJigsawToClient($data_filter)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->delete_all("playbasis_game_jigsaw_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if (isset($plan_data['jigsaw_to_plan'])) {
            $d = new MongoDate();
            $insert_data = array();
            foreach ($plan_data['jigsaw_to_plan'] as $jigsaw_id) {
                $jigsaw_data = $this->getJigsaw($jigsaw_id);
                $insert_data[] = array(
                    'jigsaw_id' => new MongoID($jigsaw_id),
                    'client_id' => new MongoID($data_filter['client_id']),
                    'site_id' => new MongoID($data_filter['site_id']),
                    'name' => $jigsaw_data['name'],
                    'description' => $jigsaw_data['description'],
                    'category' => $jigsaw_data['category'],
                    'class_path' => $jigsaw_data['class_path'],
                    'init_dataset' => $jigsaw_data['init_dataset'],
                    'sort_order' => $jigsaw_data['sort_order'],
                    'status' => (bool)$jigsaw_data['status'],
                    'date_modified' => $d,
                    'date_added' => $d
                );
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_game_jigsaw_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }
    }

    public function insertClient($data, $plan)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $price = ($plan && array_key_exists('price', $plan) ? $plan['price'] : DEFAULT_PLAN_PRICE);
        $free_flag = ($price <= 0);

        if ($free_flag) { // free package, focus mainly on "date_start" when API is calculating usage
            $date_start = $d;
            $date_expire = new MongoDate(strtotime("+" . FOREVER . " year")); // client with free package has no expiration date
            //$date_expire = null; // client with free package has no expiration date
        } else { // trial package
            $date_start = new MongoDate(strtotime("+" . FOREVER . " year")); // client with trial package CANNOT start using our API right away after registration; instead, they have to put payment detail first
            $date_expire = $date_start;
        }

        $data_insert_client = array(
            'first_name' => $data['firstname'],
            'last_name' => $data['lastname'],
            'mobile' => '',
            'email' => $data['email'],
            'company' => isset($data['company_name']) ? $data['company_name'] : null,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status' => true,
            'deleted' => false,
            'date_start' => $date_start,
            'date_expire' => $date_expire,
            'date_added' => $d,
            'date_modified' => $d
        );

        return $this->mongo_db->insert('playbasis_client',
            $data_insert_client); // return record['_id'] if insert successfully, otherwise false
    }

    /* this method does not change client's plan, playbasis_permission */
    public function editClientPlan($client_id, $plan_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['site_value'])) {
            $data_filter = array(
                'client_id' => $client_id,
                'site_id' => $data['site_value']['site_id'],
                'plan_id' => $plan_id
            );

            if (isset($data['site_value']['status'])) {
                $data_filter['status'] = $data['site_value']['status'];
            }

            $this->copyRewardToClient($data_filter);
            $this->copyFeaturedToClient($data_filter);
            $this->copyActionToClient($data_filter);
            $this->copyJigsawToClient($data_filter);
        }
    }

    /* this method does not change client's plan, playbasis_permission */
    public function editClientsPlan($_l, $plan_id)
    {
        if (!$_l) {
            return;
        }

        $l = array();
        $site_ids = array();
        foreach ($_l as $each) {
            // prevent either (1) empty site_id, or (2) duplicate site_id
            if (empty($each['site_id']) || in_array($each['site_id'], $site_ids)) {
                continue;
            }
            $l[] = array('client_id' => $each['client_id'], 'site_id' => $each['site_id']);
            $site_ids[] = $each['site_id'];
        }
        if (!$l) {
            return;
        }

        $d = new MongoDate();
        $plan_data = $this->getPlan($plan_id);

        /* playbasis_reward_to_client */
        $this->mongo_db->where_in('site_id', $site_ids);
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_reward_to_client");
        if ($plan_data['reward_to_plan']) {
            $insert_data = array();
            foreach ($plan_data['reward_to_plan'] as $reward) {
                $limit = empty($reward['limit']) ? null : (int)$reward['limit'];
                $reward_data = $this->getReward($reward['reward_id']);
                $reward_id = new MongoID($reward['reward_id']);
                foreach ($l as $each) {
                    $insert_data[] = array(
                        'reward_id' => $reward_id,
                        'client_id' => $each['client_id'],
                        'site_id' => $each['site_id'],
                        'group' => $reward_data['group'],
                        'name' => $reward_data['name'],
                        'description' => $reward_data['description'],
                        'init_dataset' => $reward_data['init_dataset'],
                        'limit' => $limit,
                        'sort_order' => $reward_data['sort_order'],
                        'status' => (bool)$reward_data['status'],
                        'date_modified' => $d,
                        'date_added' => $d,
                        'is_custom' => false,
                    );
                }
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_reward_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }

        /* playbasis_feature_to_client */
        $this->mongo_db->where_in('site_id', $site_ids);
        $this->mongo_db->delete_all("playbasis_feature_to_client");
        if (isset($plan_data['feature_to_plan'])) {
            $insert_data = array();
            foreach ($plan_data['feature_to_plan'] as $feature_id) {
                $feature_data = $this->getFeature($feature_id);
                $feature_id = new MongoID($feature_id);
                foreach ($l as $each) {
                    $insert_data[] = array(
                        'feature_id' => $feature_id,
                        'client_id' => $each['client_id'],
                        'site_id' => $each['site_id'],
                        'name' => $feature_data['name'],
                        'description' => $feature_data['description'],
                        'sort_order' => $feature_data['sort_order'],
                        'status' => (bool)$feature_data['status'],
                        'date_modified' => $d,
                        'date_added' => $d,
                        'link' => $feature_data['link'],
                        'icon' => $feature_data['icon']
                    );
                }
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_feature_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }

        /* playbasis_action_to_client */
        $this->mongo_db->where_in('site_id', $site_ids);
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_action_to_client");
        if (isset($plan_data['action_to_plan'])) {
            $insert_data = array();
            /* build $action_ids */
            $action_ids = array();
            foreach ($plan_data['action_to_plan'] as $action_id) {
                $action_ids[] = $action_id;
            }
            /* find customActions as map $m */
            $m = array();
            $results = $this->findCustomActions($action_ids, $site_ids);
            if ($results) {
                foreach ($results as $result) {
                    $m[$result['site_id'] . '-' . $result['action_id']] = true;
                }
            }
            foreach ($plan_data['action_to_plan'] as $action_id) {
                $action_data = $this->getAction($action_id);
                $action_id = new MongoID($action_id);
                foreach ($l as $each) {
                    if (!isset($m[$each['site_id'] . '-' . $action_id])) {
                        $insert_data[] = array(
                            'action_id' => $action_id,
                            'client_id' => $each['client_id'],
                            'site_id' => $each['site_id'],
                            'name' => $action_data['name'],
                            'description' => $action_data['description'],
                            'icon' => $action_data['icon'],
                            'color' => $action_data['color'],
                            'init_dataset' => $action_data['init_dataset'],
                            'sort_order' => $action_data['sort_order'],
                            'status' => (bool)$action_data['status'],
                            'date_modified' => $d,
                            'date_added' => $d,
                            'is_custom' => false,
                        );
                    }
                }
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_action_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }

        /* playbasis_game_jigsaw_to_client */
        $this->mongo_db->where_in('site_id', $site_ids);
        $this->mongo_db->delete_all("playbasis_game_jigsaw_to_client");
        if (isset($plan_data['jigsaw_to_plan'])) {
            $insert_data = array();
            foreach ($plan_data['jigsaw_to_plan'] as $jigsaw_id) {
                $jigsaw_data = $this->getJigsaw($jigsaw_id);
                $jigsaw_id = new MongoID($jigsaw_id);
                foreach ($l as $each) {
                    $insert_data[] = array(
                        'jigsaw_id' => $jigsaw_id,
                        'client_id' => $each['client_id'],
                        'site_id' => $each['site_id'],
                        'name' => $jigsaw_data['name'],
                        'description' => $jigsaw_data['description'],
                        'category' => $jigsaw_data['category'],
                        'class_path' => $jigsaw_data['class_path'],
                        'init_dataset' => $jigsaw_data['init_dataset'],
                        'sort_order' => $jigsaw_data['sort_order'],
                        'status' => (bool)$jigsaw_data['status'],
                        'date_modified' => $d,
                        'date_added' => $d
                    );
                }
            }
            if ($insert_data) {
                $this->mongo_db->batch_insert('playbasis_game_jigsaw_to_client', $insert_data,
                    array("w" => 0, "j" => false));
            }
        }
    }

    private function findCustomActions($action_ids, $site_ids)
    {
        $this->mongo_db->where_in('site_id', $site_ids);
        $this->mongo_db->where_in('action_id', $action_ids);
        return $this->mongo_db->get('playbasis_action_to_client');
    }

    //Once the client is deleted, the permissions are deleted too
    public function deleteClientPersmission($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->delete_all('playbasis_permission');
    }

    public function getSitesByClientId($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        return $this->mongo_db->get('playbasis_client_site');
    }

    public function getAllSitesFromAllClients()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        return $this->mongo_db->get('playbasis_client_site');
    }

    public function getClientById($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', $client_id);
        $results = $this->mongo_db->get('playbasis_client');
        return $results ? $results[0] : null;
    }

    public function getPlanByClientId($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_permission');
        return $results ? $results[0] : null;
    }

    public function getStripe($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_stripe');
        return $results ? $results[0] : null;
    }

    public function insertOrUpdateStripe($client_id, $stripe_id, $subscription_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate();
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_stripe');
        if (!$results) {
            $this->mongo_db->insert('playbasis_stripe', array(
                'client_id' => $client_id,
                'stripe_id' => $stripe_id,
                'subscription_id' => $subscription_id,
                'date_added' => $d,
                'date_modified' => $d,
            ));
        } else {
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->set('stripe_id', $stripe_id);
            $this->mongo_db->set('subscription_id', $subscription_id);
            $this->mongo_db->set('date_modified', $d);
            $this->mongo_db->update('playbasis_stripe');
        }
    }

    public function removeStripe($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update('playbasis_stripe');
    }

    public function getSiteInfo($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('_id', new MongoID($site_id));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_client_site');
        return $result[0] != null ? $result[0] : null;
    }

    public function setSurveyData($client_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', $client_id);
        $this->mongo_db->set('survey', $data);
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->update('playbasis_client');
    }

    public function isSurveyData($client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', $client_id);
        $result = $this->mongo_db->get('playbasis_client');
        if ($result) {
            $result = $result[0];
        }
        return $result && isset($result['survey']);
    }
}

?>