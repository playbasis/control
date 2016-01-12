<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Store_org extends REST2_Controller
{
    private $organizesData;
    private $nodesData;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('player_model');
        $this->load->model('store_org_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function playerRegister_post($node_id, $player_id)
    {
        $this->benchmark->mark('start');

        $this->checkParams($node_id, $player_id);
        $node_id = $this->findNodeId($node_id);
        $pb_player_id = $this->findPbPlayerId($player_id);

        $existed_player_organize = $this->store_org_model->retrievePlayerToNode($this->client_id, $this->site_id,
            $pb_player_id, $node_id);
        if (!$existed_player_organize) {
            $player_organize_id = $this->store_org_model->createPlayerToNode($this->client_id, $this->site_id,
                $pb_player_id, $node_id);
        } else {
            $this->response($this->error->setError('STORE_ORG_PLAYER_ALREADY_EXISTS_WITH_NODE'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function playerRemove_post($node_id, $player_id)
    {
        $this->benchmark->mark('start');

        $this->checkParams($node_id, $player_id);
        $node_id = $this->findNodeId($node_id);
        $pb_player_id = $this->findPbPlayerId($player_id);

        $existed_player_organize = $this->store_org_model->retrievePlayerToNode($this->client_id, $this->site_id,
            $pb_player_id, $node_id);
        if ($existed_player_organize) {
            $is_deleted = $this->store_org_model->deletePlayerToNode($this->client_id, $this->site_id,
                $pb_player_id, $node_id);
        } else {
            $this->response($this->error->setError('STORE_ORG_PLAYER_NOT_EXISTS_WITH_NODE'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function playerRoleSet_post($node_id, $player_id)
    {
        $this->benchmark->mark('start');

        $this->checkParams($node_id, $player_id);

        $role_name = $this->input->post('role');
        if (empty($role_name)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('role')), 200);
            die();
        }

        $node_id = $this->findNodeId($node_id);
        $pb_player_id = $this->findPbPlayerId($player_id);

        $role_data = $this->makeRoleDict($role_name);

        $existed_player_organize = $this->store_org_model->retrievePlayerToNode($this->client_id, $this->site_id,
            $pb_player_id, $node_id);
        if ($existed_player_organize) {
            $is_updated = $this->store_org_model->setPlayerRoleToNode($this->client_id, $this->site_id,
                $pb_player_id, $node_id, $role_data);
        } else {
            $this->response($this->error->setError('STORE_ORG_PLAYER_NOT_EXISTS_WITH_NODE'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function playerRoleUnset_post($node_id, $player_id)
    {
        $this->benchmark->mark('start');

        $this->checkParams($node_id, $player_id);

        $role_name = $this->input->post('role');
        if (empty($role_name)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('role')), 200);
            die();
        }
        $node_id = $this->findNodeId($node_id);
        $pb_player_id = $this->findPbPlayerId($player_id);

        $existed_player_organize = $this->store_org_model->retrievePlayerToNode($this->client_id, $this->site_id,
            $pb_player_id, $node_id);
        if ($existed_player_organize) {
            if (isset($existed_player_organize['roles']) && is_array($existed_player_organize['roles'])) {
                foreach ($existed_player_organize['roles'] as $key => $value) {
                    if ($key === $role_name) {
                        $is_updated = $this->store_org_model->unsetPlayerRoleToNode($this->client_id, $this->site_id,
                            $pb_player_id, $node_id, $role_name);

                        $this->benchmark->mark('end');
                        $t = $this->benchmark->elapsed_time('start', 'end');
                        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
                    } else {
                        continue;
                    }
                }
            }
            $this->response($this->error->setError('STORE_ORG_PLAYER_ROLE_NOT_EXISTS'), 200);

        } else {
            $this->response($this->error->setError('STORE_ORG_PLAYER_NOT_EXISTS_WITH_NODE'), 200);
        }
    }

    public function listOrganizes_get()
    {
        $this->benchmark->mark('start');

        $query_data = $this->input->get(null, true);

        if (isset($query_data['id'])) {
            try {
                $query_data['id'] = new MongoId($query_data['id']);
            } catch (Exception $e) {
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }
        }

        $results = $this->store_org_model->retrieveOrganize($this->client_id, $this->site_id, $query_data);
        $formatted_results = $this->organizesResultFormatter($results);

        $key_allowed_output = array(
            "_id",
            "name",
            "description",
            "status",
            "slug",
            "date_added",
            "date_modified",
            "parent"
        );
        foreach ($formatted_results as &$result) {
            $result = array_intersect_key($result, array_flip($key_allowed_output));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('results' => $formatted_results, 'processing_time' => $t)), 200);
    }

    public function listNodes_get()
    {
        $this->benchmark->mark('start');

        $query_data = $this->input->get(null, true);

        if (isset($query_data['id'])) {
            try{
                $query_data['id'] = new MongoId($query_data['id']);
            }catch (Exception $e){
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }
        }

        if (isset($query_data['organize_id'])) {
            try {
                $query_data['organize_id'] = new MongoId($query_data['organize_id']);
            }catch (Exception $e){
                $this->response($this->error->setError('PARAMETER_INVALID', array('organize_id')), 200);
            }
        }

        if (isset($query_data['parent_id'])) {
            try {
                $query_data['parent_id'] = new MongoId($query_data['parent_id']);
            }catch (Exception $e){
                $this->response($this->error->setError('PARAMETER_INVALID', array('parent_id')), 200);
            }
        }

        $results = $this->store_org_model->retrieveNode($this->client_id, $this->site_id, $query_data);
        $formatted_results = $this->nodesResultFormatter($results);

        $key_allowed_output = array(
            "_id",
            "name",
            "description",
            "status",
            "slug",
            "date_added",
            "date_modified",
            "organize",
            "parent"
        );
        foreach ($formatted_results as &$result) {
            $result = array_intersect_key($result, array_flip($key_allowed_output));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('results' => $formatted_results, 'processing_time' => $t)), 200);
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $value this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$value, $key)
    {
        if (is_object($value)) {
            if (get_class($value) === 'MongoId') {
                $value = $value->{'$id'};
            } else {
                if (get_class($value) === 'MongoDate') {
                    $value = datetimeMongotoReadable($value);
                }
            }
        }
    }

    private function apply_organize_parent_name(&$value, $key)
    {
        if ($key === "parent") {
            $org_res = $this->_findOrganizeById($value);
            if (isset($org_res)) {
                $value = array(
                    'id' => $org_res['_id']->{'$id'},
                    'name' => $org_res['name']
                );
            }
        }
    }

    private function apply_node_and_organize_parent_name(&$value, $key)
    {
        if ($key === "parent") {
            $org_res = $this->_findNodeById($value);
            if (isset($org_res)) {
                $value = array(
                    'id' => $org_res['_id']->{'$id'},
                    'name' => $org_res['name']
                );
            }
        } elseif ($key === "organize") {
            $org_res = $this->_findOrganizeById($value);
            if (isset($org_res)) {
                $value = array(
                    'id' => $org_res['_id']->{'$id'},
                    'name' => $org_res['name']
                );
            }
        }
    }

    private function validClPlayerId($cl_player_id)
    {
        return (!preg_match("/^([-a-z0-9_-])+$/i", $cl_player_id)) ? false : true;
    }

    /**
     * @param $node_id
     * @return MongoId
     */
    private function findNodeId($node_id)
    {
        $node_id = new MongoId($node_id);
        $node = $this->store_org_model->retrieveNodeById($this->site_id, $node_id);
        if ($node === null) {
            $this->response($this->error->setError('STORE_ORG_NODE_NOT_FOUND'), 200);
            die();
        }
        return $node_id;
    }

    /**
     * @param $player_id
     * @return null
     */
    private function findPbPlayerId($player_id)
    {
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            return $pb_player_id;
        }
        return $pb_player_id;
    }

    /**
     * @param $node_id
     * @param $player_id
     */
    private function checkParams($node_id, $player_id)
    {
        if (empty($node_id) || empty($player_id)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('node_id', 'player_id')), 200);
        }

        try {
            $tmp_id = new MongoId($node_id);
            unset($tmp_id); // just unset variable to avoid unused var.
        } catch (Exception $e) {
            $this->response($this->error->setError('PARAMETER_INVALID', array('node_id')), 200);
        }

        if (!$this->validClPlayerId($player_id)) {
            $this->response($this->error->setError('USER_ID_INVALID'), 200);
        }
    }

    /**
     * @param $name
     * @return array
     */
    private function makeRoleDict($name)
    {
        return array('name' => $name, 'value' => new MongoDate());
    }

    private function recurGetChildUnder($client_id, $site_id, $parent_node, &$result, &$layer = 0, $num = 0)
    {
        //array_push($result,$num);
        //array_push($result,$layer);
        if ($num++ <= $layer || $layer == 0) {
            array_push($result, $parent_node);
        }

        $nodes = $this->store_org_model->findAdjacentChildNode($client_id, $site_id, new MongoId($parent_node));
        if (isset($nodes)) {
            foreach ($nodes as $node) {

                $this->recurGetChildUnder($client_id, $site_id, $node['_id'], $result, $layer, $num);
            }
        } else {
            return $result;
        }
    }

    private function recurGetChildByLevel($client_id, $site_id, $parent_node, &$result, &$layer = 0, $num = 0)
    {
        //array_push($result,$num);
        //array_push($result,$layer);
        if ($num++ == $layer || $layer == 0) {
            array_push($result, $parent_node);
        }

        $nodes = $this->store_org_model->findAdjacentChildNode($client_id, $site_id, new MongoId($parent_node));
        if (isset($nodes)) {
            foreach ($nodes as $node) {

                $this->recurGetChildByLevel($client_id, $site_id, $node['_id'], $result, $layer, $num);
            }
        } else {
            return $result;
        }
    }

    public function getChildNode_get($node_id = '', $layer = 0)
    {
        $this->benchmark->mark('start');

        $results = array();
        $candidate_nodes = array();

        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }

        $check_node= $this->store_org_model->retrieveNodeById($this->validToken['site_id'],new MongoId($node_id));
        if ($check_node == null) {
            $this->response($this->error->setError('STORE_ORG_NODE_NOT_FOUND'), 200);
        }

        $this->recurGetChildUnder($this->validToken['client_id'], $this->validToken['site_id'], new MongoId($node_id),
            $candidate_nodes, $layer);

        foreach ($candidate_nodes as $node) {
            $node_info = $this->store_org_model->retrieveNodeById($this->validToken['site_id'], $node);
            if($node_id!=$node_info['_id'] && !is_null($node_info) ) {
                array_push($results, $node_info);
            }
        }

        $formatted_results = $this->nodesResultFormatter($results);
        $key_allowed_output = array(
            "_id",
            "name",
            "description",
            "status",
            "slug",
            "date_added",
            "date_modified",
            "organize",
            "parent"
        );
        foreach ($formatted_results as &$result) {
            $result = array_intersect_key($result, array_flip($key_allowed_output));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('results' => $formatted_results, 'processing_time' => $t)), 200);
    }

    public function saleReport_get($node_id = '')
    {
        $result = array();

        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }

        $month = $this->input->get('month');
        if (!$month) {
            $month = date("m", time());
        }
        $year = $this->input->get('year');
        if (!$year) {
            $year = date("Y", time());
        }
        $action = $this->input->get('action');
        if (!$action) {
            $action = "sell";
        }
        $parameter = $this->input->get('parameter');
        if (!$parameter) {
            $parameter = "amount";
        }

        $list = array();
        $this->recurGetChildUnder($this->validToken['client_id'], $this->validToken['site_id'], new MongoId($node_id),
            $list);

        $table = $this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],
            $this->validToken['site_id'], $list, $action, $parameter, $month, $year, 2);

        $this_month_time = strtotime($year . "-" . $month);
        $previous_month_time = strtotime('-1 month', $this_month_time);

        $current_month = date("m", $this_month_time);
        $current_year = date("Y", $this_month_time);

        $previous_month = date("m", $previous_month_time);
        $previous_year = date("Y", $previous_month_time);

        $current_month_sales = $table[$current_year][$current_month][$parameter];
        $previous_month_sales = $table[$previous_year][$previous_month][$parameter];

        $result[$parameter] = $current_month_sales;
        $result['previous_'.$parameter] = $previous_month_sales;

        if ($current_month_sales == 0 && $previous_month_sales == 0) {
            $result['percent_changed'] = 0;
        } elseif ($previous_month_sales == 0) {
            $result['percent_changed'] = 100;
        } else {
            $result['percent_changed'] = (($current_month_sales - $previous_month_sales) * 100) / $previous_month_sales;
        }

        $this->response($this->resp->setRespond($result), 200);
    }

    public function saleHistory_get($node_id = '', $count = '')
    {
        $result = array();
        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }
        if (!$count) {
            if($count==0){
                $this->response($this->error->setError('PARAMETER_INVALID', array(
                    'count'
                )), 200);
            }else{
                $this->response($this->error->setError('PARAMETER_MISSING', array(
                    'count'
                )), 200);
            }

        }

        $month = $this->input->get('month');
        if (!$month) {
            $month = date("m", time());
        }
        $year = $this->input->get('year');
        if (!$year) {
            $year = date("Y", time());
        }
        $action = $this->input->get('action');
        if (!$action) {
            $action = "sell";
        }
        $parameter = $this->input->get('parameter');
        if (!$parameter) {
            $parameter = "amount";
        }


        $node_list = array();
        $this->recurGetChildUnder($this->validToken['client_id'], $this->validToken['site_id'], new MongoId($node_id),
            $node_list);

        $table = $this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],
            $this->validToken['site_id'], $node_list, $action, $parameter, $month, $year, $count + 1);

        $this_month_time = strtotime($year . "-" . $month);
        for ($index = 0; $index < $count; $index++) {
            $current_month = date("m", strtotime('-' . ($index) . ' month', $this_month_time));
            $current_year = date("Y", strtotime('-' . ($index) . ' month', $this_month_time));

            $previous_month = date("m", strtotime('-' . ($index + 1) . ' month', $this_month_time));
            $previous_year = date("Y", strtotime('-' . ($index + 1) . ' month', $this_month_time));

            $current_month_sales = $table[$current_year][$current_month][$parameter];
            $previous_month_sales = $table[$previous_year][$previous_month][$parameter];

            $result[$current_year][$current_month][$parameter] = $current_month_sales;
            $result[$current_year][$current_month]['previous_'.$parameter] = $previous_month_sales;

            if ($current_month_sales == 0 && $previous_month_sales == 0) {
                $result[$current_year][$current_month]['percent_changed'] = 0;
            } elseif ($previous_month_sales == 0) {
                $result[$current_year][$current_month]['percent_changed'] = 100;
            } else {
                $result[$current_year][$current_month]['percent_changed'] = (($current_month_sales - $previous_month_sales) * 100) / $previous_month_sales;
            }
        }

        $this->response($this->resp->setRespond($result), 200);
    }

    public function saleBoard_get($node_id = '', $layer = '')
    {
        $result = array();

        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }

        $month = $this->input->get('month');
        if (!$month) {
            $month = date("m", time());
        }
        $year = $this->input->get('year');
        if (!$year) {
            $year = date("Y", time());
        }
        $action = $this->input->get('action');
        if (!$action) {
            $action = "sell";
        }
        $parameter = $this->input->get('parameter');
        if (!$parameter) {
            $parameter = "amount";
        }
        //$this->benchmark->mark('rank_peer_start');

        $candidate_node = array();
        $this->recurGetChildByLevel($this->validToken['client_id'], $this->validToken['site_id'], new MongoId($node_id),
            $candidate_node, $layer);

        foreach ($candidate_node as $node) {
            $list = array();
            $this->recurGetChildUnder($this->validToken['client_id'], $this->validToken['site_id'], new MongoId($node),
                $list);

            $table = $this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],
                $this->validToken['site_id'], $list, $action, $parameter, $month, $year, 2);

            $this_month_time = strtotime($year . "-" . $month);
            $previous_month_time = strtotime('-1 month', $this_month_time);

            $current_month = date("m", $this_month_time);
            $current_year = date("Y", $this_month_time);

            $previous_month = date("m", $previous_month_time);
            $previous_year = date("Y", $previous_month_time);

            $current_month_sales = $table[$current_year][$current_month]['amount'];
            $previous_month_sales = $table[$previous_year][$previous_month]['amount'];

            $temp[$parameter] = $current_month_sales;
            $temp['previous_'.$parameter] = $previous_month_sales;

            if ($current_month_sales == 0 && $previous_month_sales == 0) {
                $temp['percent_changed'] = 0;
            } elseif ($previous_month_sales == 0) {
                $temp['percent_changed'] = 100;
            } else {
                $temp['percent_changed'] = (($current_month_sales - $previous_month_sales) * 100) / $previous_month_sales;
            }
            $temp2 = $this->store_org_model->retrieveNodeById($this->validToken['site_id'],$node);
            array_push($result, array_merge(array('node_id' => $node."",'name'=>$temp2['name']), $temp));
        }

        foreach ($result as $key => $raw) {
            $temp_name[$key] = $raw['node_id'];
            $temp_value[$key] = $raw[$parameter];
        }
        if (isset($temp_value) && isset($temp_name)) {
            array_multisort($temp_value, SORT_DESC, $temp_name, SORT_ASC, $result);
        }

        //$this->benchmark->mark('rank_peer_end');
        //$result['processing_time'] = $this->benchmark->elapsed_time('rank_peer_start', 'rank_peer_end');
        $this->response($this->resp->setRespond($result), 200);

    }
    public function players_get($node_id = '')
    {
        $result = array();

        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }

        try {
            $node_id = new MongoId($node_id);
        }catch (Exception $e){
            $this->response($this->error->setError('STORE_ORG_NODE_NOT_FOUND'), 200);
        }

        // input are valid, let process
        $input = $this->input->get();
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];


        $role = isset ($input['role'])? $input['role']:null;
        $players_list = $this->store_org_model->getPlayersByNodeId($client_id,$site_id,$node_id,$role);

        if (is_null($players_list) ){ // not found
            $this->response($this->error->setError('STORE_ORG_NODE_NOT_FOUND', array(
                'node_id'
            )), 200);
        }

        $result = array();
        foreach ($players_list as $player){
            array_push($result, array (
                'player_id' => $this->player_model->getClientPlayerId($player['pb_player_id'],$site_id),
            ));
        }
        $this->response($this->resp->setRespond($result), 200);
    }

    /**
     * @param $result
     * @return mixed
     */
    private function organizesResultFormatter($result)
    {
        array_walk_recursive($result, array($this, "convert_mongo_object"));

        // Apply Name field to each parent
        $this->organizesData = $this->store_org_model->retrieveOrganize($this->client_id, $this->site_id);
        array_walk_recursive($result, array($this, "apply_organize_parent_name"));

        return $result;
    }

    /**
     * @param $result
     * @return mixed
     */
    private function nodesResultFormatter($result)
    {
        array_walk_recursive($result, array($this, "convert_mongo_object"));

        // Apply Name field to each parent
        $this->nodesData = $this->store_org_model->retrieveNode($this->client_id, $this->site_id);
        $this->organizesData = $this->store_org_model->retrieveOrganize($this->client_id, $this->site_id);
        array_walk_recursive($result, array($this, "apply_node_and_organize_parent_name"));

        return $result;
    }

    private function _findOrganizeById($organize_id)
    {
        foreach ($this->organizesData as $element) {
            if ($organize_id == $element['_id']) {
                return $element;
            }
        }

        return false;
    }

    private function _findNodeById($node_id)
    {
        foreach ($this->nodesData as $element) {
            if ($node_id == $element['_id']) {
                return $element;
            }
        }

        return false;
    }
    public function rankPeer_get($node_id, $rank_by)
    {
        // Check validity of action and parameter
        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }
        if (!$rank_by) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'rank_by'
            )), 200);
        }
        // Now, getting all input
        $this->benchmark->mark('rank_peer_start');
        $input = $this->input->get();
        $limit = isset($input['limit']) ? $input['limit'] : 20;
        $year = isset($input['year']) ? $input['year'] : date("Y", time());
        $month = isset($input['month']) ? $input['month'] : date("m", time());
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $backup_limit = $limit;
        $role = isset($input['role']) ? $input['role'] : null;
        $list = array();

        // get node list of this node id
        if (is_null($role)){
            $this->store_org_model->recurGetChild($client_id,$site_id,new MongoId ($node_id), $list);
            // if list is null, node id is the second lowest of organization. we just need to find player
            if (is_null($list)) $list = array (new MongoId ($node_id));
            $node_to_match = array();
            foreach($list as $node){
                $player_list = $this->store_org_model->getPlayersByNodeId($client_id,$site_id,$node);
                foreach ($player_list as $player)
                    array_push($node_to_match, array('pb_player_id'=>new MongoId($player['pb_player_id'])));
            }
        }
        else{
            $list = $this->store_org_model->findAdjacentChildNode($client_id,$site_id,new MongoId ($node_id));
            // if list is null, node id is the second lowest of organization. we just need to find player
            if (is_null($list)) $list = array (array('_id' => new MongoId ($node_id)));
            $node_to_match = array();
            foreach($list as $node){
                if ($node['_id'] == new MongoId ($node_id)) continue; // if role is set, mean input node id is excluded
                $player_list = $this->store_org_model->getPlayersByNodeId($client_id,$site_id,$node['_id'],$role);
                foreach ($player_list as $player)
                    array_push($node_to_match, array('pb_player_id'=>new MongoId($player['pb_player_id'])));
            }
        }
        if ( isset($input['player_id'])){
            $given_player_id = $input['player_id'];
            $limit = count($node_to_match);
        }

        $results = $this->store_org_model->getMonthlyPeerLeaderboard($rank_by, $limit, $client_id,
            $site_id, $node_to_match, $month, $year);

        $prev_month = $month -1 ? $month -1 : 12;
        $prev_year = $month -1 ? $year : $year - 1;
        $previous_result = $this->store_org_model->getMonthlyPeerLeaderboard($rank_by, $limit, $client_id,
            $site_id, $node_to_match, $prev_month, $prev_year);

        $leaderboard_list = array();
        foreach ($node_to_match as $key=> $node){
            $current_value = $this->getValueFromLeaderboardList('pb_player_id',$node['pb_player_id'],$rank_by,$results);
            $prev_value = $this->getValueFromLeaderboardList('pb_player_id',$node['pb_player_id'],$rank_by,$previous_result);
            array_push($leaderboard_list,array ('player_id' => $this->player_model->getClientPlayerId($node['pb_player_id'],$site_id),
                    $rank_by => $current_value,
                    'previous_'.$rank_by => $prev_value,
                    'percent_changed' => $prev_value==0? $current_value > 0? 100:0: (($current_value- $prev_value)*100)/$prev_value)
            );
        }
        $return_list['leaderboard'] = $leaderboard_list = $this->sortResult($leaderboard_list,$rank_by,'player_id');
        foreach ($leaderboard_list as $key => $rank){
            $rank_no = $key +1;
            if ($rank_no > $backup_limit) unset($return_list['leaderboard'][$key]);
            if (isset ($given_player_id) && ($rank['player_id'] == $given_player_id)){
                $myrank = array(
                    'player_id' => $rank['player_id'],
                    'rank' => $rank_no,
                    'ranked_by' => $rank_by,
                    'ranked_value' => $rank[$rank_by],
                );
                $return_list['my_rank'] = $myrank;
            }

        }
        if (isset ($given_player_id) && !isset($return_list['my_rank']))
        {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }


        $this->benchmark->mark('rank_peer_end');
        $result['processing_time'] = $this->benchmark->elapsed_time('rank_peer_start', 'rank_peer_end');

        $this->response($this->resp->setRespond($return_list), 200);
    }

    public function rankPeerByAccumulateAction_get($node_id, $action, $param)
    {
        $this->benchmark->mark('rank_peer_start');
        // Check validity of action and parameter
        if (!$node_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);
        }
        // Check validity of action and parameter
        if(!$action)
            $this->response($this->error->setError('ACTION_NOT_FOUND', array(
                'action'
            )), 200);
        if(!$param)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'parameter'
            )), 200);

        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $action_id = $this->client_model->getAction(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'action_name' => $action
        ));
        if(!$action_id) $this->response($this->error->setError('ACTION_NOT_FOUND'), 200);

        // Now, getting all input

        $input = $this->input->get();
        if ( isset($input['player_id'])){
            $given_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'cl_player_id' => $input['player_id']));
        }
        $limit = isset($input['limit']) ? $input['limit'] : 20;
        $year = isset($input['year']) ? $input['year'] : date("Y", time());
        $month = isset($input['month']) ? $input['month'] : date("m", time());

        $prev_month = $month -1 ? $month -1 : 12;
        $prev_year = $month -1 ? $year : $year - 1;

        $results = array();
        $leaderboard_list = array();
        $node_list = $this->store_org_model->findAdjacentChildNode($client_id,$site_id,new MongoID($node_id));
        // get node list of this node id
        if ($node_list )foreach ($node_list as $node){
            if ($node['_id'] == $node_id) continue;
            $list = array();
            $this->store_org_model->recurGetChild($client_id,$site_id,$node['_id'], $list);

            if (!empty($list)) {
                $result = $this->store_org_model->getSaleHistoryOfNode($client_id, $site_id, $list, $action,
                    $param, $month, $year, 2);
                $current_value = $result[$year][$month][$param];
                $prev_value = $result[$prev_year][$prev_month][$param];
                array_push($leaderboard_list,array ( 'name' => $node['name'],
                    $param => $current_value,
                    'previous_'.$param => $prev_value,
                    'percent_changed' => $prev_value==0? $current_value > 0? 100:0: (($current_value- $prev_value)*100)/$prev_value,
                    'node_id' => $node['_id']
                ));
            }
        }

        $results['leaderboard'] = $leaderboard_list = $this->sortResult($leaderboard_list,$param,'name');
        foreach ($leaderboard_list as $key => $rank){
            $rank_no = $key +1;
            unset($results['leaderboard'][$key]['node_id']);
            if ($rank_no > $limit){
                unset($results['leaderboard'][$key]);
            }
            $players_in_node = $this->store_org_model->getPlayersByNodeId($client_id, $site_id,$rank['node_id']);

            if (isset ($given_player_id) && in_array(array('pb_player_id' => $given_player_id),$players_in_node)){
                $myrank = array(
                    'player_id' => $input['player_id'],
                    'node_name' => $rank['name'],
                    'rank' => $rank_no,
                    'ranked_by' => $param,
                    'ranked_value' => $rank[$param],
                );
                $results['my_rank'] = $myrank;
            }
        }
        if (isset ($given_player_id) && !isset($results['my_rank']))
        {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        $this->benchmark->mark('rank_peer_end');
        $result['processing_time'] = $this->benchmark->elapsed_time('rank_peer_start', 'rank_peer_end');
        $this->response($this->resp->setRespond($results), 200);
    }
    private function getValueFromLeaderboardList ($key, $name_to_key, $name_of_value, $list){
        foreach ($list as $player){
            if (isset($player[$key]) && ($player[$key] ==$name_to_key) ){
                return ($player[$name_of_value]);
            }
        }
        return 0;
    }
    private function sortResult ($list, $sort_by, $name){
        $result = $list;
        foreach ($list as $key => $raw){

            $temp_name[$key] = $raw[$name];
            $temp_value[$key] =  $raw[$sort_by];
        }
        if (isset($temp_value) && isset($temp_name))
        {
            array_multisort( $temp_value, SORT_DESC,$temp_name, SORT_ASC, $result);
        }
        return $result;
    }
}