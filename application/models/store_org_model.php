<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_org_model extends MY_Model
{
    public function createNode(
        $client_id,
        $site_id,
        $name,
        $description = null,
        $organize = null,
        $parent = null,
        $status = true
    ) {
        $this->load->helper('url');

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'slug' => url_title($name, 'dash', true),
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        if (isset($organize)) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($organize));
            if (isset($parent_result)) {
                $insert_data['organize'] = $parent_result['_id'];
            }
        }

        if (isset($parent)) {
            $parent_result = $this->retrieveNodeById(new MongoId($parent));
            if (isset($parent_result)) {
                $insert_data['parent'] = $parent_result['_id'];
            }
        }

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_client', $insert_data);

        return $insert;
    }

    public function countNodes($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_store_organize_to_client');

        return $total;
    }

    public function retrieveNode($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");

            $store_org = $this->retrieveOrganize($client_id,$site_id,$optionalParams);
            $store_query = array();
            foreach ($store_org as $store){
                array_push($store_query, $store['_id']);
            }

            $query = array( '$or' => array( array( "name" => $regex ), array( "organize"=> array('$in' => $store_query))));
            $this->mongo_db->where($query);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
            };
        }
        if (isset($optionalParams['organize']) && !is_null($optionalParams['organize'])) {
            //make sure 'id' is valid before passing here
            try {
                $organize = new MongoId($optionalParams['organize']);
                $this->mongo_db->where('organize', $organize);
            } catch (Exception $e) {
            };
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

        if (isset($optionalParams['order']) && (utf8_strtolower($optionalParams['order']) == 'desc')) {
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
            if ($optionalParams['offset'] < 0) {
                $optionalParams['offset'] = 0;
            }

            if ($optionalParams['limit'] < 1) {
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

    public function retrieveNodeById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize_to_client");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function retrieveNodeByNameAndOrganize($client_id, $site_id, $name, $organize)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('organize', new MongoId($organize));
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_store_organize_to_client");

        return $results ? $results[0] : null;
    }

    public function retrieveNodeByNameAndOrganizeButNotID($client_id, $site_id, $name, $organize, $node_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', $node_id);
        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('organize', new MongoId($organize));
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_store_organize_to_client");

        return $results ? $results[0] : null;
    }

    public function updateNodeById($nodeId, $updateData)
    {
        $parent_data = null;
        if (isset($updateData['parent'])) {
            $parent_result = $this->retrieveNodeById(new MongoId($updateData['parent']));
            if (isset($parent_result)) {
                $parent_data = $parent_result['_id'];
            }
        }

        $organize_data = null;
        if (isset($updateData['organize'])) {
            $organize_result = $this->retrieveOrganizeById(new MongoId($updateData['organize']));
            if (isset($organize_result)) {
                $organize_data = $organize_result['_id'];
            }
        }

        if (isset($parent_data)) {
            $this->mongo_db->set('parent', $parent_data);
        } else {
            $this->mongo_db->unset_field('parent');
        }

        if (isset($organize_data)) {
            $this->mongo_db->set('organize', $organize_data);
        } else {
            $this->mongo_db->unset_field('organize');
        }

        $this->mongo_db->where('_id', new MongoID($nodeId));
        $this->mongo_db->where('client_id', $updateData['client_id']);
        $this->mongo_db->where('site_id', $updateData['site_id']);

        $this->mongo_db->set('name', $updateData['name']);
        $this->mongo_db->set('description', $updateData['description']);

        $this->mongo_db->set('status', $updateData['status']);
        $this->mongo_db->set('slug', url_title($updateData['name'], 'dash', true));
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_store_organize_to_client');

        return $update;
    }

    public function deleteNodeById($nodeId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($nodeId));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_store_organize_to_client');
    }

    public function deleteNodeByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, "makeMongoIdObj"));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_store_organize_to_client');

        return $update;
    }

    public function createOrganize($client_id, $site_id, $name, $description = null, $parent = null, $status = true)
    {
        $this->load->helper('url');

        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'slug' => url_title($name, 'dash', true),
            'deleted' => false,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );

        if (isset($parent)) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($parent));
            if (isset($parent_result)) {
                $parent_data = $parent_result['_id'];
                $insert_data['parent'] = $parent_data;
            }
        }

        $insert = $this->mongo_db->insert('playbasis_store_organize', $insert_data);

        return $insert;
    }

    public function countOrganizes($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('deleted', false);
        $total = $this->mongo_db->count('playbasis_store_organize');

        return $total;
    }

    public function retrieveOrganize($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        // Searching
        if (isset($optionalParams['search']) && !is_null($optionalParams['search'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($optionalParams['search'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }
        if (isset($optionalParams['id']) && !is_null($optionalParams['id'])) {
            //make sure 'id' is valid before passing here
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
            };
        }

        // Sorting
        $sort_data = array('_id', 'name', 'status', 'description');

        if (isset($optionalParams['order']) && (utf8_strtolower($optionalParams['order']) == 'desc')) {
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
            if ($optionalParams['offset'] < 0) {
                $optionalParams['offset'] = 0;
            }

            if ($optionalParams['limit'] < 1) {
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

    public function retrieveOrganizeByName($client_id, $site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        return $this->mongo_db->get("playbasis_store_organize");
    }

    public function retrieveOrganizeByNameButNotID($client_id, $site_id, $name, $org_id)
    {
        //$this->mongo_db->select(array('_id', 'cl_player_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('_id', $org_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('name', $name);
        $results = $this->mongo_db->get('playbasis_store_organize');
        return $results ? $results[0] : array();
    }

    public function retrieveOrganizeById($id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($id));
        $this->mongo_db->where('deleted', false);
        $c = $this->mongo_db->get("playbasis_store_organize");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function updateOrganizeById($organizeId, $updateData)
    {
        $parent_data = null;
        if (isset($updateData['parent'])) {
            $parent_result = $this->retrieveOrganizeById(new MongoId($updateData['parent']));
            if (isset($parent_result)) {
                $parent_data = $parent_result['_id'];
                $this->mongo_db->set('parent', $parent_data);
            }
        } else {
            $this->mongo_db->unset_field('parent');
        }

        $this->mongo_db->where('_id', new MongoID($organizeId));
        $this->mongo_db->where('client_id', $updateData['client_id']);
        $this->mongo_db->where('site_id', $updateData['site_id']);

        $this->mongo_db->set('name', $updateData['name']);
        $this->mongo_db->set('description', $updateData['description']);

        $this->mongo_db->set('status', $updateData['status']);
        $this->mongo_db->set('slug', url_title($updateData['name'], 'dash', true));
        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update('playbasis_store_organize');

        return $update;
    }

    public function deleteOrganizeById($organizeId)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($organizeId));
        $this->mongo_db->set('deleted', true);
        return $this->mongo_db->update('playbasis_store_organize');
    }

    public function deleteOrganizeByIdArray($id_array)
    {
        if (!empty($id_array)) {
            array_walk($id_array, array($this, 'makeMongoIdObj'));
            $this->mongo_db->where_in('_id', $id_array);
            $this->mongo_db->set('deleted', true);
        }

        $this->mongo_db->set('date_modified', new MongoDate());

        $update = $this->mongo_db->update_all('playbasis_store_organize');

        return $update;
    }

    public function listNodes($node_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('_id', $node_id_list);
        return $this->mongo_db->get("playbasis_store_organize_to_client");
    }

    public function listOrganizations($organization_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('_id', $organization_id_list);
        return $this->mongo_db->get("playbasis_store_organize");
    }

    public function getlistByOrgId($client_id, $site_id, $org_id)
    {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'organize' => $org_id,
        ));

        $result = $this->mongo_db->get('playbasis_store_organize_to_client');
        if (empty($result)) {
            return null;
        } else {
            return $result;
        }
    }

    public function findAdjacentChildNode($client_id, $site_id, $node_id)
    {
        $this->mongo_db->select(array(
            'name',
            'organize',

        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'parent' => $node_id,
        ));
        $result = $this->mongo_db->get('playbasis_store_organize_to_client');
        if (empty($result)) {
            return null;
        } else {
            return $result;
        }
    }

    public function getSaleHistoryOfNode(
        $client_id,
        $site_id,
        $node_list,
        $action,
        $parameter,
        $month = null,
        $year = null,
        $count
    ) {
        $result = array();

        $node_to_match = array();
        foreach ($node_list as $node) {
            array_push($node_to_match, array('node_id' => new MongoId($node)));
        }
        // default is present month/year
        if (!isset($month)) {
            $month = date("m", time());
        }
        if (!isset($year)) {
            $year = date("Y", time());
        }

        $this_month_time = strtotime($year . "-" . $month);

        $first = date('Y-m-01', strtotime('-' . ($count) . ' month', $this_month_time));
        $from = strtotime($first . ' 00:00:00');

        $last = date('Y-m-t', $this_month_time);
        $to = strtotime($last . ' 23:59:59');

        $status = $this->mongo_db->aggregate('playbasis_validated_action_log', array(

            array(
                '$match' => array(
                    'action_name' => $action,
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                    '$or' => $node_to_match
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        "year" => array('$year' => '$date_added'),
                        "month" => array('$month' => '$date_added')
                    ),
                    $parameter => array('$push' => '$parameters.' . $parameter)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        array_push($status['result'], 0);
        $gap = 0;
        for ($index = 0; $index < $count; $index++) {
            $current_month = date("m", strtotime('-' . ($index) . ' month', $this_month_time));
            $current_year = date("Y", strtotime('-' . ($index) . ' month', $this_month_time));

            if ($status['result'][$index - $gap]['_id']['month'] != $current_month || $status['result'][$index - $gap]['_id']['year'] != $current_year) {
                $result[$current_year][$current_month] = array($parameter => 0);
                $gap++;
            } else {
                $result[$current_year][$current_month] = array($parameter => array_sum($status['result'][$index - $gap][$parameter]));
            }

        }

        return $result;
    }

    public function getPlayersByNodeId($client_id, $site_id, $node_id, $role = null)
    {
        $this->mongo_db->select(array('pb_player_id'));

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'node_id' => $node_id,
        ));
        if (!is_null($role)) {
            $this->mongo_db->where_exists('roles.' . $role, true);
        }
        $result = $this->mongo_db->get('playbasis_store_organize_to_player');
        if (empty($result)) {
            return null;
        } else {
            return $result;
        }

    }

    public function getMonthlyPeerLeaderboard(
        $ranked_by,
        $limit,
        $client_id,
        $site_id,
        $node_to_match = null,
        $month = null,
        $year = null
    ) {
        $limit = intval($limit);
        $this->set_site_mongodb($site_id);
        /* get reward_id */
        $reward_id = $this->getRewardIdByName($client_id, $site_id, $ranked_by);
        /* get latest RESET event for that reward_id (if exists) */
        $reset = $this->getResetRewardEvent($site_id, $reward_id);
        $resetTime = null;
        if ($reset) {
            $reset_time = array_values($reset);
            $resetTime = $reset_time[0]->sec;
        }
        // default is present month
        if (is_null($year) || is_null($month)) {
            $selected_time = time();
        } else {
            $selected_time = strtotime($year . "-" . $month);
        }


        // Aggregate the data
        $first = date('Y-m-01', $selected_time);
        $from = strtotime($first . ' 00:00:00');

        $last = date('Y-m-t', $selected_time);
        $to = strtotime($last . ' 23:59:59');

        $match = array(
            'event_type' => 'REWARD',
            'site_id' => $site_id,
            'reward_id' => $reward_id,
            'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
        );
        if ($node_to_match) {
            // set match parameter for aggregate
            $match['$or'] = $node_to_match;
        }

        if ($resetTime && $resetTime > $from) {
            $from = $resetTime;
        }
        $results = $this->mongo_db->aggregate('playbasis_event_log', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array(
                    '_id' => array('pb_player_id' => '$pb_player_id'),
                    'value' => array('$sum' => '$value')
                )
            ),
            array(
                '$sort' => array('value' => -1),
            ),
            array(
                '$limit' => $limit + 5,
            ),
        ));
        return $results ? $this->removeDeletedPlayers($results['result'], $limit, $ranked_by) : array();
    }

    private function getRewardIdByName($client_id, $site_id, $name)
    {
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where(array(
            'name' => $name,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_reward_to_client');
        return $results ? $results[0]['reward_id'] : null;
    }

    public function getResetRewardEvent($site_id, $reward_id = null)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array('reward_id', 'date_added'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('event_type', 'RESET');
        if ($reward_id) {
            $this->mongo_db->where('reward_id', $reward_id);
            $this->mongo_db->limit(1);
        }
        $this->mongo_db->order_by(array('date_added' => 'DESC')); // use 'date_added' instead of '_id'
        $results = $this->mongo_db->get('playbasis_event_log');
        $ret = array();
        if ($results) {
            foreach ($results as $result) {
                $reward_id = $result['reward_id']->{'$id'};
                if (array_key_exists($reward_id, $ret)) {
                    continue;
                }
                $ret[$reward_id] = $result['date_added'];
            }
        }

        return $ret;
    }

    private function removeDeletedPlayers($results, $limit, $rankedBy)
    {
        $total = count($results);
        $c = 0;
        for ($i = 0; $i < $total; $i++) {
            if ($c < $limit) {
                $this->mongo_db->select(array('cl_player_id'));
                if (isset($results[$i]['_id']['pb_player_id'])) {
                    $results[$i]['pb_player_id'] = $results[$i]['_id']['pb_player_id'];
                    unset($results[$i]['_id']);
                }
                $this->mongo_db->where(array('_id' => $results[$i]['pb_player_id']));
                $p = $this->mongo_db->get('playbasis_player');
                if ($p) {
                    $p = $p[0];
                    $results[$i]['player_id'] = $p['cl_player_id'];
                    $results[$i][$rankedBy] = $results[$i]['value'];
                    unset($results[$i]['cl_player_id']);
                    unset($results[$i]['value']);
                    $c++;
                } else {
                    unset($results[$i]);
                }
            } else {
                unset($results[$i]);
            }
        }
        return array_values($results);
    }

    function makeMongoIdObj(&$value)
    {
        $value = new MongoId($value);
    }
}