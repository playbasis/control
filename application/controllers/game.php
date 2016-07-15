<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Game extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('badge_model');
        $this->load->model('game_model');
        $this->load->model('reward_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);

        if (isset($query_data['tags']) && !empty($query_data['tags'])) {
            $query_data['tags'] = explode(',', $query_data['tags']);
        }

        if (!isset($query_data['status']) || (strtolower($query_data['status']) !== 'all')){
            $query_data['status'] = (isset($query_data['status']) && (strtolower($query_data['status'])==='false')) ? false : true;
        }else{
            unset($query_data['status']);
        }

        $games = $this->game_model->retrieveGame($this->client_id, $this->site_id, $query_data);

        // Find game template
        foreach ($games as &$game){

            $template = $this->game_model->getTemplateByCurrentDate($this->client_id, $this->site_id, array(
                'game_id' => $game['_id']
            ));

            // Get 'default' template if template not found
            if (empty($template)) {
                $template = $this->game_model->getTemplate($this->client_id, $this->site_id, array(
                    'game_id' => $game['_id'],
                    'template_name' => 'default',
                ));
            }
            $game['template'] = array(
                'template_name' => isset($template['template_name']) && !empty($template['template_name']) ? $template['template_name'] : null,
                'config' => isset($template['config']) && !empty($template['config']) ? $template['config'] : null,
                //'date_start' => isset($template['date_start']) && !empty($template['date_start']) ? $template['date_start'] : null,
                //'date_end' => isset($template['date_end']) && !empty($template['date_end']) ? $template['date_end'] : null,
            );

            // Show stage and item config if required
            if (isset($query_data['game_name']) && !empty($query_data['game_name'])){
                $stages = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game['_id'], array(
                    'stage_level' => isset($query_data['stage_level']) && !empty($query_data['stage_level']) ? $query_data['stage_level'] : null,
                    'stage_name' => isset($query_data['stage_name']) && !empty($query_data['stage_name']) ? $query_data['stage_name'] : null,
                ));
                if ((isset($query_data['stage_level']) && !empty($query_data['stage_level'])) || (isset($query_data['stage_name']) && !empty($query_data['stage_name']))){
                    if (empty($stages)){
                        $this->response($this->error->setError('GAME_STAGE_NOT_FOUND'), 200);
                    }
                }

                // Get stage setting
                foreach ($stages as &$stage){
                    $items = $this->game_model->retrieveItem($this->client_id, $this->site_id, $game['_id'], $query_data, $stage['item_list']);

                    // Get item name
                    foreach ($items as &$item){
                        $item['item_name'] = $this->badge_model->getBadge(array(
                            'client_id' => $this->client_id,
                            'site_id' => $this->site_id,
                            'badge_id' => new MongoId($item['item_id']),
                        ))['name'];

                        // Get item template
                        $item_template = $this->game_model->getItemTemplate($this->client_id, $this->site_id, array(
                            'game_id' => $game['_id'],
                            'item_id' => $item['item_id'],
                            'template_id' => $template['_id'],
                        ));
                        $item['images'] = isset($item_template['images']) && !empty($item_template['images']) ? $item_template['images'] : null;

                        unset($item['item_id']);
                    }
                    $stage['item'] = $items;
                    unset($stage['item_list']);
                }
                $game['stage'] = $stages;
            }
            unset($game['_id']);
        }
        array_walk_recursive($games, array($this, "convert_mongo_object_and_optional"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond($games), 200);
    }

    public function itemList_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $item_id_list = array();

        $required = $this->input->checkParam(array(
            'game_name',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (empty($game)){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }

        // Get item list if stage_level or stage_name has been queried
        if ((isset($query_data['stage_level']) && !empty($query_data['stage_level'])) ||
            (isset($query_data['stage_name']) && !empty($query_data['stage_name']))) {
            $stage = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game[0]['_id'], $query_data);
            $item_id_list = (isset($stage[0]['item_list']) && !empty($stage[0]['item_list']) ? $stage[0]['item_list'] : null);
        }

        // Return empty array if item_id_list is null which means invalid stage input
        if (isset($item_id_list)) {
            $item = $this->game_model->retrieveItem($this->client_id, $this->site_id, $game[0]['_id'], $query_data, $item_id_list);
        }else{
            $item = array();
        }
        array_walk_recursive($item, array($this, "convert_mongo_object_and_optional"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $item, 'processing_time' => $t)), 200);
    }

    public function stageList_get()
    {
        $this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $required = $this->input->checkParam(array(
            'game_name',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (empty($game)){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }

        $stage = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game[0]['_id'], $query_data);
        array_walk_recursive($stage, array($this, "convert_mongo_object_and_optional"));


        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $stage, 'processing_time' => $t)), 200);
    }

    private function getList($data, $objectType)
    {
        // Add object into list of array
        $list = array();
        foreach ($data as $val){
            try{
                array_push($list, new MongoId($val[$objectType]));
            }
            catch(MongoException $e){
                array_push($list, $val[$objectType]);
            }
        }
        return $list;
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object_and_optional(&$item, $key)
    {
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

    private function getPlayerItemStatus($game_id, $pb_player_id, $harvested_item, $item_id){
        $item_status = array();
        $item_status['item_id'] = $item_id;
        $item_status['item_name'] = $this->badge_model->getBadgeName($this->client_id, $this->site_id, $item_id);
        // get item config
        $item_info = $this->game_model->retrieveItem($this->client_id, $this->site_id, $game_id, null, array(new MongoId($item_id)));
        if($item_info){
            $item_status['item_config'] = $item_info[0]['item_config'];
        }else{
            $item_status['item_config'] = null;
        }


        if (in_array(new MongoId($item_id), $harvested_item)) {
            $item_status['item_status'] = "harvested";
            $item_status['item_image'] = null;
        }else{
            $item_record = $this->reward_model->getItemToPlayerRecords($this->client_id, $this->site_id, $pb_player_id, $item_id);
            if ($item_record) {
                /* $now = new Datetime('now');
                 $updated_date = new Datetime(datetimeMongotoReadable(isset($item_record['date_modified'] ) ? $item_record['date_modified'] : $item_records['date_added']));
                 $interval = $now->diff($updated_date);*/

                $item_status['item_status'] = $item_record['value'] . "";
            } else {
                $item_status['item_status'] = "0";
            }

            // Get current game template
            $template = $this->game_model->getTemplateByCurrentDate($this->client_id, $this->site_id, array(
                'game_id' => $game_id
            ));

            // Get 'default' template if current template not found
            if (empty($template)) {
                $template = $this->game_model->getTemplate($this->client_id, $this->site_id, array(
                    'game_id' => $game_id,
                    'template_name' => 'default',
                ));
            }

            // Get item template
            $item_template = $this->game_model->getItemTemplate($this->client_id, $this->site_id, array(
                'game_id' => $game_id,
                'item_id' => $item_id,
                'template_id' => $template['_id'],
            ));
            $item_level = (int)$item_status['item_status'];
            $item_status['item_image'] = isset($item_template['images'][$item_level]) && $item_template['images'][$item_level] ? $item_template['images'][$item_level] : null;
        }

        return $item_status;
    }

    public function playerItemStatus_get()
    {
        //$this->benchmark->mark('start');
        $query_data = $this->input->get(null, true);
        $required = $this->input->checkParam(array(
            'game_name',
            'player_id',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        //validate playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $query_data['player_id']
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        //validate game name
        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (empty($game)){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }
        $game_id = $game[0]['_id'];

        $response = array();

        if(strtolower($query_data['game_name']) == "farm") {

            if( (isset($query_data['stage_level']) && $query_data['stage_level']) ){

                $stage_info = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game_id, array('stage_level' =>   $query_data['stage_level'] ));
                if ($stage_info){
                    $stage_info = $stage_info[0];

                    $harvested_item = array();
                    $stage_to_player = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $game_id, $pb_player_id, array('stage_level' => $query_data['stage_level']));
                    if (isset($stage_to_player['harvested_item']) && $stage_to_player['harvested_item']) {
                        $harvested_item = $stage_to_player['harvested_item'];
                    }

                    if((isset($query_data['item_id']) && $query_data['item_id'])){
                        if (!in_array(new MongoId($query_data['item_id']), $stage_info['item_list'])) {
                            $this->response($this->error->setError('GAME_ITEM_NOT_IN_STAGE'), 200);
                        }
                        $response = $this->getPlayerItemStatus($game_id, $pb_player_id, $harvested_item, $query_data['item_id']);
                    }
                    else{
                        $list_item_id = $stage_info['item_list'];

                        $response['stage_level'] = $stage_info['stage_level'];
                        $response['stage_name'] = $stage_info['stage_name'];
                        $response['items_status'] = array();
                        foreach ($list_item_id as $item_id) {
                            $response['items_status'][]  = $this->getPlayerItemStatus($game_id, $pb_player_id, $harvested_item, $item_id."");
                        }
                    }
                }else{
                    $this->response($this->error->setError('GAME_STAGE_NOT_FOUND'), 200);
                }
            }
            else{
                $harvested_item = array();
                $stage_to_player = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $game_id, $pb_player_id, array('is_current' => true));
                if ($stage_to_player) {
                    $current_stage = $stage_to_player['stage_level'];
                    $harvested_item = $stage_to_player['harvested_item'];
                } else {
                    $current_stage = 1;
                    $stage_1 = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $game_id, $pb_player_id,array('stage_level' => $current_stage));
                    if ($stage_1) {
                        $harvested_item = $stage_1['harvested_item'];
                    }
                }
                $stage_info = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game_id, array('stage_level' =>   $current_stage ));
                if ($stage_info) {
                    $stage_info = $stage_info[0];
                    if ((isset($query_data['item_id']) && $query_data['item_id'])) {
                        if (!in_array(new MongoId($query_data['item_id']), $stage_info['item_list'])) {
                            $this->response($this->error->setError('GAME_ITEM_NOT_IN_CURRENT_STAGE'), 200);
                        }
                        $response = $this->getPlayerItemStatus($game_id, $pb_player_id, $harvested_item, $query_data['item_id']);
                    } else {
                        $list_item_id = $stage_info['item_list'];

                        $response['stage_level'] = $stage_info['stage_level'];
                        $response['stage_name'] = $stage_info['stage_name'];
                        $response['items_status'] = array();
                        foreach ($list_item_id as $item_id) {
                            $response['items_status'][] = $this->getPlayerItemStatus($game_id, $pb_player_id, $harvested_item, $item_id . "");
                        }
                    }
                }else{
                    $this->response($this->error->setError('GAME_STAGE_NEVER_BEEN_SET'), 200);
                }
            }
        }

        //$this->benchmark->mark('end');
        //$t = $this->benchmark->elapsed_time('start', 'end');
        //$this->response($this->resp->setRespond(array( 'processing_time' => $t)), 200);
        $this->response($this->resp->setRespond($response), 200);
    }

    public function playerItemStatus_post()
    {
        //$this->benchmark->mark('start');
        $query_data = $this->input->post();
        $required = $this->input->checkParam(array(
            'game_name',
            'stage_level',
            'player_id',
            'item_id',
            'item_status',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        //validate playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $query_data['player_id']
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        //validate game name
        $game = $this->game_model->retrieveGame($this->client_id, $this->site_id, array(
            'game_name' => $query_data['game_name'],
            'order' => 'desc'
        ));
        if (!$game){
            $this->response($this->error->setError('GAME_NOT_FOUND'), 200);
        }
        $game_id = $game[0]['_id'];

        $stage_info = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game_id, array('stage_level' =>   $query_data['stage_level'] ));
        if (!$stage_info){
            $this->response($this->error->setError('GAME_STAGE_NOT_FOUND'), 200);
        }
        $stage_info = $stage_info[0];
        if (!in_array(new MongoId($query_data['item_id']), $stage_info['item_list'])) {
            $this->response($this->error->setError('GAME_ITEM_NOT_IN_STAGE'), 200);
        }

        if(strtolower($query_data['game_name']) == "farm") {
            if(strtolower($query_data['item_status']) == "harvested"){
                $stage_to_player = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $game_id, $pb_player_id, array('stage_level' => $query_data['stage_level']));
                $harvested_item = array();
                if ($stage_to_player) {
                    //todo: update
                    $harvested_item = $stage_to_player['harvested_item'];
                    if (!in_array(new MongoId($query_data['item_id']), $stage_to_player['harvested_item'])) {
                        $harvested_item = array_merge($harvested_item,array(new MongoId($query_data['item_id'])));
                        $this->game_model->updateStageToPlayer($this->client_id, $this->site_id, $game_id, $query_data['stage_level'], $pb_player_id,
                            array( 'harvested_item'=> $harvested_item));
                    }

                }else{
                    //todo: insert
                    $harvested_item = array(new MongoId($query_data['item_id']));
                    $this->game_model->setStageToPlayer($this->client_id, $this->site_id, $game_id, $query_data['stage_level'], $pb_player_id,
                        array( 'harvested_item'=> $harvested_item));
                }

                // check if all items in the state is harvested then process to next level
                $stage_finished = true;
                foreach($stage_info['item_list'] as $item){
                    if (!in_array($item, $harvested_item)) {
                        $stage_finished = false;
                        break;
                    }
                }

                $next_stage = null;
                if($stage_finished){
                    $all_stage = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game_id, array());

                    foreach($all_stage as $index => $stage){
                        if($stage['stage_level'] == $query_data['stage_level']){
                            if($index != count($all_stage)-1){
                                // go to next level
                                $next_stage = $all_stage[$index+1]['stage_level'];

                                // set is_current of the input stage to be false
                                $this->game_model->updateStageToPlayer($this->client_id, $this->site_id, $game_id, $query_data['stage_level'], $pb_player_id,
                                    array( 'is_current'=> false));

                                // set up next stage if not exist (is_current = true)
                                $next_stage_to_player = $this->game_model->getStageToPlayer($this->client_id, $this->site_id, $game_id, $pb_player_id, array('stage_level' => $next_stage));
                                if (!$next_stage_to_player) {
                                    $this->game_model->setStageToPlayer($this->client_id, $this->site_id, $game_id, $next_stage, $pb_player_id,
                                        array( 'is_current'=> true));
                                }
                            }
                        }
                    }

                }

            }else{
                $this->response($this->error->setError('GAME_ITEM_STATUS_NOT_SUPPORT', $query_data['game_name']), 200);
            }
        }

        //$this->benchmark->mark('end');
        //$t = $this->benchmark->elapsed_time('start', 'end');
        //$this->response($this->resp->setRespond(array( 'processing_time' => $t)), 200);
        $this->response($this->resp->setRespond(array('stage_finished'=>$stage_finished , 'next_stage'=>$next_stage)), 200);
    }

}