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

        if(isset($query_data['id']))
            if(!MongoId::isValid($query_data['id']))
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);

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
        foreach($formatted_results as &$result){
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

        if(isset($query_data['id']))
            if(!MongoId::isValid($query_data['id']))
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);

        if(isset($query_data['organize_id']))
            if(!MongoId::isValid($query_data['organize_id']))
                $this->response($this->error->setError('PARAMETER_INVALID', array('organize_id')), 200);

        if(isset($query_data['parent_id']))
            if(!MongoId::isValid($query_data['parent_id']))
                $this->response($this->error->setError('PARAMETER_INVALID', array('parent_id')), 200);

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
        foreach($formatted_results as &$result){
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
        }elseif($key === "organize"){
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

        if (!MongoId::isValid($node_id)) {
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

    private function _findOrganizeById($organize_id){
        foreach ( $this->organizesData as $element ) {
            if ( $organize_id == $element['_id'] ) {
                return $element;
            }
        }

        return false;
    }

    private function _findNodeById($node_id){
        foreach ( $this->nodesData as $element ) {
            if ( $node_id == $element['_id'] ) {
                return $element;
            }
        }

        return false;
    }
}