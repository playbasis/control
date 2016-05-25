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
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
            }
        }
        if (isset($optionalParams['organize_id']) && !is_null($optionalParams['organize_id'])) {
            //make sure 'id' is valid before passing here
            try {
                $organize = new MongoId($optionalParams['organize_id']);
                $this->mongo_db->where('organize', $organize);
            } catch (Exception $e) {
            }
        }
        if (isset($optionalParams['parent_id']) && !is_null($optionalParams['parent_id'])) {
            //make sure 'id' is valid before passing here
            try {
                $parent_node_id = new MongoId($optionalParams['parent_id']);
                $this->mongo_db->where('parent', $parent_node_id);
            } catch (Exception $e) {
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
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
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

    public function retrieveNodeByNameInOrg($client_id, $site_id, $name, $org_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('name', $name);
        $this->mongo_db->where('organize', new MongoId($org_id));
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

    public function retrieveOrganizeByName($client_id, $site_id, $name)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('name', $name);
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
            'node_id' => new MongoId($node_id),

        );

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_player', $insert_data);

        return $insert;
    }

    public function createContentToNode($client_id, $site_id, $content_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id),
            'content_id' => new MongoId($content_id),
            'node_id' => new MongoId($node_id)
        );

        $insert = $this->mongo_db->insert('playbasis_store_organize_to_content', $insert_data);

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

    public function retrieveContentToNode($client_id, $site_id, $content_id, $node_id, $role_name = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        if (isset($role_name)) {
            $this->mongo_db->where_exists('roles.' . $role_name, true);
        }

        $c = $this->mongo_db->get("playbasis_store_organize_to_content");

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }

    public function retrieveAllContentToNode($client_id, $site_id, $node_id = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        if (isset($node_id)) {
            $this->mongo_db->where('node_id', new MongoId($node_id));
        }

        return $c = $this->mongo_db->get("playbasis_store_organize_to_content");
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

    public function deleteContentToNode($client_id, $site_id, $content_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));
        $c = $this->mongo_db->delete("playbasis_store_organize_to_content");

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

    public function setContentRoleToNode($client_id, $site_id, $content_id, $node_id, $role)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $this->mongo_db->set('roles.' . $role['name'], $role['value']);

        $update = $this->mongo_db->update('playbasis_store_organize_to_content');

        return $update;
    }

    public function unsetContentRoleToNode($client_id, $site_id, $content_id, $node_id, $role_name_to_unset)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('content_id', new MongoId($content_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $this->mongo_db->unset_field('roles.' . $role_name_to_unset);

        $update = $this->mongo_db->update('playbasis_store_organize_to_content');

        return $update;
    }

    public function retrieveNodeByPBPlayerID($client_id, $site_id, $pb_player_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));

        $this->mongo_db->where('pb_player_id', new MongoId($pb_player_id));


        $c = $this->mongo_db->get("playbasis_store_organize_to_player");

        if ($c) {
            return $c;
        } else {
            return null;
        }
    }

    public function getOrgInfoOfNode($client_id, $site_id, $node_id)
    {
        $this->mongo_db->select(array(
            'name',
            'description',
            'organize',
            'parent'

        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            '_id' => $node_id,
        ));
        $result = $this->mongo_db->get('playbasis_store_organize_to_client');
        return $result;
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

    public function getAssociatedNodeOfPlayer($client_id, $site_id, $player_id)
    {

        $this->mongo_db->select(array(
            'node_id',
            'roles',

        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $player_id,
        ));
        return $this->mongo_db->get('playbasis_store_organize_to_player');
    }

    public function getAssociatedNodeOfContent($client_id, $site_id, $content_id)
    {

        $this->mongo_db->select(array(
            'node_id',
            'roles',

        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'content_id' => $content_id,
        ));
        return $this->mongo_db->get('playbasis_store_organize_to_content');
    }

    public function getRoleOfPlayer($client_id, $site_id, $player_id, $node_id)
    {

        $this->mongo_db->select(array(
            'roles',

        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'pb_player_id' => $player_id,
            'node_id' => $node_id,
        ));
        $result = $this->mongo_db->get('playbasis_store_organize_to_player');
        if (empty($result)) {
            return null;
        } else {
            return $result[0];
        }

    }
}