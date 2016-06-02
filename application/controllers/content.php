<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Content extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('content_model');
        $this->load->model('player_model');
        $this->load->model('store_org_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $exclude_ids = array();

        if (isset($query_data['player_id']) && !empty($query_data['player_id'])) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $query_data['player_id']
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        if ((isset($query_data['only_new_content']) && !empty($query_data['only_new_content'])) && (strtolower($query_data['only_new_content']) === "true")) {
            if (!isset($query_data['player_id'])){
                $this->response($this->error->setError('PARAMETER_MISSING', 'player_id'), 200);
            }
            $exclude_ids = $this->content_model->getContentIDToPlayer($this->validToken['client_id'],
                $this->validToken['site_id'], $pb_player_id);
            if (isset($query_data['limit']) && isset($query_data['sort']) && $query_data['sort'] == "random") {
                $query_data['_limit'] = $query_data['limit'];
                $query_data['limit'] += count($exclude_ids);
            }
        }

        // Get organize associated between player and content
        if (!empty($pb_player_id)){
            $all_content_to_node = array();
            $content_to_node_and_player = array();

            // Retrieve all content_id associate to store_org
            $content_to_node = $this->store_org_model->retrieveAllContentToNode($this->validToken['client_id'],
                $this->validToken['site_id']);
            foreach ($content_to_node as $ct){
                if (isset($ct['content_id']) && !empty($ct['content_id'])){
                    $all_content_to_node[] = $ct['content_id'];
                }
            }

            // Retrieve content_id associate to store_org with player
            $nodes_list = $this->store_org_model->getAssociatedNodeOfPlayer($this->validToken['client_id'],
                $this->validToken['site_id'], $pb_player_id);
            $query_data['content_id_organize_assoc'] = array();
            foreach ($nodes_list as $val){
                $content_to_node = $this->store_org_model->retrieveAllContentToNode($this->validToken['client_id'],
                    $this->validToken['site_id'], $val['node_id']);
                foreach ($content_to_node as $ct){
                    if (isset($ct['content_id']) && !empty($ct['content_id'])){
                        $content_to_node_and_player[] = $ct['content_id'];
                    }
                }
            }
            $query_data['content_id_organize_assoc'] = array_merge($query_data['content_id_organize_assoc'], array_diff($all_content_to_node, $content_to_node_and_player));
        }

        if (isset($query_data['category']) && !empty($query_data['category'])) {
            $category_result = $this->content_model->retrieveContentCategory($this->validToken['client_id'], $this->validToken['site_id'],
                array('name' => $query_data['category']));
            if($category_result){
                $query_data['category'] = new MongoId($category_result[0]['_id']);
            }else{
                $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
            }
        }
        if (isset($query_data['tags']) && !empty($query_data['tags'])) {
            $query_data = array_merge($query_data, array(
                'tags' => explode(',', $this->input->get('tags'))
            ));
        }

        if (!isset($query_data['status']) || (strtolower($query_data['status'])!=='all')){
            $query_data = array_merge($query_data, array(
                'status' => (isset($query_data['status']) && (strtolower($query_data['status'])==='false')) ? false : true
            ));
        }else{
            unset($query_data['status']);
        }

        $contents = $this->content_model->retrieveContent($this->client_id, $this->site_id, $query_data, (isset($query_data['only_new_content']) && !empty($query_data['only_new_content'])) && (strtolower($query_data['only_new_content']) === "true" && isset($query_data['sort']) && $query_data['sort'] == "random") ? array() : $exclude_ids);

        foreach ($contents as &$content){
            $nodes_list = $this->store_org_model->getAssociatedNodeOfContent($this->validToken['client_id'],
                $this->validToken['site_id'], $content['_id']);
            $organization = array();
            if (!empty($nodes_list)) {
                foreach ($nodes_list as $node) {
                    $org_node = $this->store_org_model->retrieveNodeById($this->validToken['site_id'], $node['node_id']);
                    $name = $org_node['name'];
                    $org_info = $this->store_org_model->retrieveOrganizeById($this->validToken['client_id'],
                        $this->validToken['site_id'], $org_node['organize']);
                    $node_id = (String)$node['node_id'];
                    $roles = array();
                    if (isset($node['roles']) && is_array($node['roles'])) {
                        foreach ($node['roles'] as $role_name => $date_join) {
                            array_push($roles,
                                array('role' => $role_name, 'join_date' => datetimeMongotoReadable($date_join)));
                        }
                    }
                    if (empty($roles)) {
                        $roles = null;
                    }
                    array_push($organization, array(
                        'name' => $name,
                        'node_id' => $node_id,
                        'organize_type' => $org_info['name'],
                        'roles' => $roles
                    ));
                }
                $content['organize'] = $organization;
            }
        }

        if (isset($query_data['full_html']) && $query_data['full_html'] == "true") {
            if(is_array($contents))foreach ($contents as &$content){
                $content['detail'] = '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">' .
                        '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">' .
                        '<style>img{ max-width: 100%}</style>' .
                        '</head><title></title><body>' . $content['detail'] . '</body></html>';
            }
        }
        array_walk_recursive($contents, array($this, "convert_mongo_object_and_category"));

        $result = array();
        if (isset($query_data['sort']) && $query_data['sort'] == "random") {
            if (isset($query_data['order'])) {
                if (is_numeric($query_data['order'])) {
                    srand(intval($query_data['order']));
                }
            }
            if (isset($query_data['_limit'])) $query_data['limit'] = $query_data['_limit'];
            $m = count($contents);
            $n = $this->content_model->retrieveContentCount($this->client_id, $this->site_id, $query_data, $exclude_ids);
            if (isset($query_data['limit']) && $query_data['limit'] < $n) $n = $query_data['limit'];
            if (!isset($query_data['offset']) || $query_data['offset'] < 0) $query_data['offset'] = 0;
            $numbers = range(0, $m-1);
            shuffle($numbers);
            $c = 0;
            foreach ($numbers as $i) {
                if (!is_array($exclude_ids) || !in_array($contents[$i]['_id'], $exclude_ids)) {
                    if (!isset($query_data['offset']) || $c >= $query_data['offset']) {
                        $result[] = $contents[$i];
                        if (count($result) >= $n) break;
                    }
                    $c++;
                }
            }
        }else{
            $result = $contents;
        }
        if(isset($query_data['pin']) && $query_data['pin'] && !$result){
            $this->response($this->error->setError('PIN_CODE_INVALID'), 200);
        }
        if(isset($query_data['node_id']) && $query_data['node_id'] && !$result){
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function countContent_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get();
        $exclude_ids = array();

        if (isset($query_data['player_id']) && !empty($query_data['player_id'])) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->validToken['client_id'],
                'site_id' => $this->validToken['site_id'],
                'cl_player_id' => $query_data['player_id']
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            }
        }

        if ((isset($query_data['only_new_content']) && !empty($query_data['only_new_content'])) && (strtolower($query_data['only_new_content']) === "true")) {
            if (!isset($query_data['player_id'])){
                $this->response($this->error->setError('PARAMETER_MISSING', 'player_id'), 200);
            }
            $exclude_ids = $this->content_model->getContentIDToPlayer($this->validToken['client_id'], $this->validToken['site_id'], $pb_player_id);
        }

        // Get organize associated between player and content
        if (!empty($pb_player_id)){
            $all_content_to_node = array();
            $content_to_node_and_player = array();

            // Retrieve all content_id associate to store_org
            $content_to_node = $this->store_org_model->retrieveAllContentToNode($this->validToken['client_id'],
                $this->validToken['site_id']);
            foreach ($content_to_node as $ct){
                if (isset($ct['content_id']) && !empty($ct['content_id'])){
                    $all_content_to_node[] = $ct['content_id'];
                }
            }

            // Retrieve content_id associate to store_org with player
            $nodes_list = $this->store_org_model->getAssociatedNodeOfPlayer($this->validToken['client_id'],
                $this->validToken['site_id'], $pb_player_id);
            $query_data['content_id_organize_assoc'] = array();
            foreach ($nodes_list as $val){
                $content_to_node = $this->store_org_model->retrieveAllContentToNode($this->validToken['client_id'],
                    $this->validToken['site_id'], $val['node_id']);
                foreach ($content_to_node as $ct){
                    if (isset($ct['content_id']) && !empty($ct['content_id'])){
                        $content_to_node_and_player[] = $ct['content_id'];
                    }
                }
            }
            $query_data['content_id_organize_assoc'] = array_merge($query_data['content_id_organize_assoc'], array_diff($all_content_to_node, $content_to_node_and_player));
        }

        if (isset($query_data['category']) && !empty($query_data['category'])) {
            $category_result = $this->content_model->retrieveContentCategory($this->validToken['client_id'], $this->validToken['site_id'],
                array('name' => $query_data['category']));
            if($category_result){
                $query_data['category'] = new MongoId($category_result[0]['_id']);
            }else{
                $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
            }
        }

        if (isset($query_data['tags']) && !empty($query_data['tags'])) {
            $query_data = array_merge($query_data, array(
                'tags' => explode(',', $this->input->get('tags'))
            ));
        }

        if (!isset($query_data['status']) || (strtolower($query_data['status'])!=='all')){
            $query_data = array_merge($query_data, array(
                'status' => (isset($query_data['status']) && (strtolower($query_data['status'])==='false')) ? false : true
            ));
        }else{
            unset($query_data['status']);
        }

        $count_value = $this->content_model->retrieveContentCount($this->validToken['client_id'], $this->validToken['site_id'], $query_data , $exclude_ids);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $count_value,'processing_time' => $t)), 200);
    }

    public function listCategory_get()
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

        $categories = $this->content_model->retrieveContentCategory($this->client_id, $this->site_id, $query_data);
        if (empty($categories)) {
            $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
        }

        $result = array();
        if (is_array($categories)) {
            foreach ($categories as $category) {

                array_push($result, array(
                    "_id" => $category['_id']."",
                    "name" => $category['name']
                ));
                //array_push($result, $category['name']);
            }
        }

        if (empty($result)) {
            $result = null;
        }
        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function insert_post()
    {
        $this->benchmark->mark('start');
        $contentInfo['client_id'] = $this->validToken['client_id'];
        $contentInfo['site_id'] = $this->validToken['site_id'];

        $required = $this->input->checkParam(array(
            'title',
            'summary',
            'detail',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        if($this->input->post('node_id')) {
            if (strpos($this->input->post('node_id'), ' ') > 0) {
                $this->response($this->error->setError('CONTENT_NODE_ID__SPACE_EXIST'), 200);
            }
            else
            {
                $check_node_id = $this->content_model->findContent($this->client_id, $this->site_id, $this->input->post('node_id'));
                if($check_node_id)
                {
                    $this->response($this->error->setError('CONTENT_NODE_ID_ALREADY_EXISTS'), 200);
                }
                $contentInfo['node_id'] = $this->input->post('node_id');
            }
        }
        else{
            do{
                $random_node_id = new MongoId();
                $random_node_id = $random_node_id . "";
                $check_node_id = $this->content_model->findContent($this->client_id, $this->site_id, $random_node_id);
            } while($check_node_id);
            $contentInfo['node_id'] = $random_node_id;
        }

        $contentInfo['title']    = $this->input->post('title');
        $contentInfo['summary']  = $this->input->post('summary');
        $contentInfo['detail']   = $this->input->post('detail');

        if($this->input->post('category')) {
            $category = $this->content_model->retrieveContentCategory($this->client_id, $this->site_id, array(
                'name' => $this->input->post('category')
            ));
            if (empty($category)) {
                $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
            }
            $contentInfo['category'] = new MongoId($category[0]['_id']);
        }

        if ($this->input->post('player_id')) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id'    => $this->validToken['client_id'],
                'site_id'      => $this->validToken['site_id'],
                'cl_player_id' => $this->input->post('player_id')
            ));
            if (empty($pb_player_id)) {
                $this->response($this->error->setError('USER_ID_INVALID'), 200);
            }
            $contentInfo['pb_player_id'] = $pb_player_id;
        }
        $contentInfo['image']      = ($this->input->post('image')) ? $this->input->post('image') : "no_image.jpg";
        $contentInfo['date_start'] = (isset($contentInfo['date_start']) && !empty($contentInfo['date_start'])) ? new MongoDate(strtotime($this->input->post('date_start'))) : null;
        $contentInfo['date_end']   = (isset($contentInfo['date_end']) && !empty($contentInfo['date_end'])) ? new MongoDate(strtotime($this->input->post('date_end'))) : null;
        $contentInfo['status']     = strtolower($this->input->post('status')) == 'true';

        if ($this->input->post('pin')){
            $contentInfo['pin'] = $this->input->post('pin');
        }
        $contentInfo['tags'] = $this->input->post('tags') && !is_null($this->input->post('status')) ? explode(',', $this->input->post('tags')) : null;

        if ($this->input->post('key')) {
            $data['custom'] = array();
            $keys = explode(',', $this->input->post('key'));
            $value = $this->input->post('value');
            $values = explode(',', $value);
            foreach ($keys as $i => $key) {
                $contentInfo['custom'][$key] = isset($values[$i]) ? $values[$i] : null;
            }
        }

        $insert = $this->content_model->createContent($contentInfo);
        $result_node_id = "";
        if($insert){
            $result_node_id = $contentInfo['node_id'];
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $insert, 'node_id' => $result_node_id, 'processing_time' => $t)), 200);
    }

    public function update_post($node_id = null)
    {
        $this->benchmark->mark('start');
        $contentInfo = array();

        if(!$node_id) {
            $this->response($this->error->setError('PARAMETER_INVALID',array('node_id')), 200);
        }

        if($this->input->post('title')){
            $contentInfo['title'] = $this->input->post('title');
        }

        if($this->input->post('summary')){
            $contentInfo['summary'] = $this->input->post('summary');
        }

        if($this->input->post('detail')){
            $contentInfo['detail'] = $this->input->post('detail');
        }

        if($this->input->post('category')) {
            $category = $this->content_model->retrieveContentCategory($this->client_id, $this->site_id, array(
                'name' => $this->input->post('category')
            ));
            if (empty($category)) {
                $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
            }
            $contentInfo['category'] = $category[0]['_id'];
        }

        if($this->input->post('date_start')){
            $contentInfo['date_start'] = new MongoDate(strtotime($this->input->post('date_start')));
        }

        if($this->input->post('date_end')){
            $contentInfo['date_end'] = new MongoDate(strtotime($this->input->post('date_end')));
        }

        if($this->input->post('image')){
            $contentInfo['image'] = $this->input->post('image');
        }

        if($this->input->post('status')){
            $contentInfo['status'] = strtolower($this->input->post('status'))=='true';
        }

        if ($this->input->post('pin')){
            $contentInfo['pin'] = $this->input->post('pin');
        }

        if ($this->input->post('tags')){
            $contentInfo['tags'] = explode(',', $this->input->post('tags'));
        }

        if ($this->input->post('key')) {
            $data['custom'] = array();
            $keys = explode(',', $this->input->post('key'));
            $value = $this->input->post('value');
            $values = explode(',', $value);
            foreach ($keys as $i => $key) {
                $contentInfo['custom'][$key] = isset($values[$i]) ? $values[$i] : null;
            }
        }

        $update = $this->content_model->updateContent($this->validToken['client_id'], $this->validToken['site_id'], $contentInfo , $node_id);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function action_post($action = null, $node_id = null, $player_id = null)
    {
        $this->load->library('RestClient');

        $this->benchmark->mark('start');

        $actionInfo['client_id'] = $this->validToken['client_id'];
        $actionInfo['site_id']   = $this->validToken['site_id'];
        $actionInfo['action']    = $action;

        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id'    => $this->validToken['client_id'],
            'site_id'      => $this->validToken['site_id'],
            'cl_player_id' => $player_id
        ));
        if (empty($pb_player_id)) {
            $this->response($this->error->setError('USER_ID_INVALID'), 200);
        }
        $actionInfo['pb_player_id'] = $pb_player_id;

        if(!$node_id) {
            $this->response($this->error->setError('PARAMETER_INVALID',array('node_id')), 200);
        }

        $contents = $this->content_model->getContentByNodeId($this->validToken['client_id'], $this->validToken['site_id'], $node_id);
        if(isset($contents[0]['_id']) && !empty($contents[0]['_id'])){
            $actionInfo['content_id'] = $contents[0]['_id'];
        }
        else{
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }
        $actionInfo['custom'] = null;
        $key = $this->input->post('key');
        if ($key) {
            $keys = explode(',', $key);
            $value = $this->input->post('value');
            $values = explode(',', $value);
            if (count($values) != count($keys)){
                $this->response($this->error->setError('PARAMETER_INVALID', array('key','value')), 200);
            }
            foreach ($keys as $i => $key) {
                $actionInfo['custom'][$key] = isset($values[$i]) ? $values[$i] : null;
            }
        }

        $playerContent = $this->content_model->retrieveExistingPlayerContent(array(
            'client_id'    => $this->validToken['client_id'],
            'site_id'      => $this->validToken['site_id'],
            'content_id'   => $contents[0]['_id'],
            'pb_player_id' => $pb_player_id
        ));

        if(empty($playerContent)) {
            $action = json_decode(json_encode($this->content_model->addPlayerAction($actionInfo)), true);
            $action = (isset($action['$id']));
        }else{
            $action = json_decode(json_encode($this->content_model->updatePlayerContent($actionInfo)), true);
        }

        if($action) {

            // Sent action to action log
            $result = $this->restclient->post($this->config->base_url() . 'Engine/rule', array(
                'token'      => $this->input->post('token'),
                'api_key'    => $this->input->post('api_key'),
                'action'     => $actionInfo['action'],
                'player_id'  => $player_id,
                'content_id' => json_decode(json_encode($actionInfo['content_id']), true)['$id']
            ));

            if(!isset($result->success)){
                $this->response($this->error->setError('INTERNAL_ERROR', isset($result->error) ? $result->error :null), 200);
            }
            else if($result->success == false){
                $this->response($this->error->setError('INTERNAL_ERROR', isset($result->message) ? $result->message :null ), 200);
            }
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $action, 'processing_time' => $t)), 200);
    }

    public function generatePin_post($content_id)
    {
        $this->benchmark->mark('start');

        $pin = $this->input->post('pin');
        if (empty($pin)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('pin')), 200);
            die();
        }

        if (empty($content_id)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('content_id')), 200);
        }
        $this->checkValidContent($content_id);

        $pin_data = $this->generatePinDict($pin);

        $is_updated = $this->content_model->setPinToContent($this->client_id, $this->site_id, $content_id, $pin_data);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function giveFeedback_post($node_id, $player_id)
    {
        $this->benchmark->mark('start');

        $postData = $this->input->post();

        if(!$node_id) {
            $this->response($this->error->setError('PARAMETER_INVALID',array('node_id')), 200);
        }

        $contents = $this->content_model->getContentByNodeId($this->validToken['client_id'], $this->validToken['site_id'], $node_id);
        if(isset($contents[0]['_id']) && !empty($contents[0]['_id'])){
            $data['content_id'] = $contents[0]['_id'];
        }
        else{
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }

        if (empty($player_id)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('$player_id')), 200);
            die();
        }
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id'    => $this->validToken['client_id'],
            'site_id'      => $this->validToken['site_id'],
            'cl_player_id' => $player_id
        ));
        if (empty($pb_player_id)) {
            $this->response($this->error->setError('USER_ID_INVALID'), 200);
        }
        $data['pb_player_id'] = $pb_player_id;

        if (empty($postData['feedback'])) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('feedback')), 200);
        }
        $data['feedback'] = $postData['feedback'];
        $data['custom'] = null;
        $key = $this->input->post('key');
        if ($key) {
            $data['custom'] = array();
            $keys = explode(',', $key);
            $value = $this->input->post('value');
            $values = explode(',', $value);
            if (count($values) != count($keys)){
                $this->response($this->error->setError('PARAMETER_INVALID', array('key','value')), 200);
            }
            foreach ($keys as $i => $key) {
                $data['custom'][$key] = isset($values[$i]) ? $values[$i] : null;
            }
        }

        $result = $this->content_model->setContentFeedback($this->client_id, $this->site_id, $data);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result'=> $result,'processing_time' => $t)), 200);
    }

    public function deleteContent_post($node_id = null)
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        if(!$node_id){
            $this->response($this->error->setError('PARAMETER_MISSING', array('node_id')), 200);
        }

        $result = $this->content_model->getContentByNodeId($client_id, $site_id, $node_id);
        if($result){
            $this->content_model->deleteContent($client_id, $site_id, $node_id);
        } else {
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function createContentCategory_post()
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        if(!$this->input->post('name')){
            $this->response($this->error->setError('PARAMETER_MISSING', array('name')), 200);
        }

        $this->content_model->createContentCategory($client_id, $site_id, $this->input->post('name'));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function updateContentCategory_post()
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $postData = $this->input->post();

        $required = $this->input->checkParam(array(
            'id',
            'name'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        if (isset($postData['id'])) {
            try {
                $postData['id'] = new MongoId($postData['id']);
            } catch (Exception $e) {
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }
        }

        $categories = $this->content_model->retrieveContentCategory($client_id, $site_id, array(
            'id' => $postData['id']
        ));
        if (empty($categories)) {
            $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
        }

        $this->content_model->updateContentCategory($postData['id'], array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'name' => $postData['name']
        ));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    public function deleteContentCategory_post()
    {
        $this->benchmark->mark('start');
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];
        $postData = $this->input->post();

        if(!isset($postData['id'])){
            $this->response($this->error->setError('PARAMETER_MISSING', array('id')), 200);
        }else {
            try {
                $postData['id'] = new MongoId($postData['id']);
            } catch (Exception $e) {
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }

            $categories = $this->content_model->retrieveContentCategory($client_id, $site_id, array(
                'id' => $postData['id']
            ));
            if (empty($categories)) {
                $this->response($this->error->setError('CONTENT_CATEGORY_NOT_FOUND'), 200);
            }
        }

        $this->content_model->deleteContentCategory($postData['id']);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object_and_category(&$item, $key)
    {
        if ($key === 'category') {
            $item = $this->content_model->getContentCategoryNameById($this->client_id, $this->site_id, $item);
        }
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }

    }

    /**
     * @param $content_id
     */
    private function checkValidContent($content_id)
    {
        try {
            $query_data['id'] = new MongoId($content_id);
        } catch (Exception $e) {
            $this->response($this->error->setError('PARAMETER_INVALID', array('content_id')), 200);
        }
        $contents = $this->content_model->retrieveContent($this->validToken['client_id'], $this->validToken['site_id'], $query_data);
        if(!isset($contents[0]['_id'])){
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }
        return $contents;
    }
}