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
        $this->load->model('template_model');
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
            $template = $this->template_model->getTemplateById($this->client_id, $this->site_id, array(
                'game_id' => $game['_id']
            ));
            if (isset($template)) {
                $game['template'] = array(
                    'config' => isset($template['config']) && !empty($template['config']) ? $template['config'] : null,
                    //'date_start' => isset($template['date_start']) && !empty($template['date_start']) ? $template['date_start'] : null,
                    //'date_end' => isset($template['date_end']) && !empty($template['date_end']) ? $template['date_end'] : null,
                );
            }

            // Show stage and item config if required
            if (isset($query_data['game_name']) && !empty($query_data['game_name'])){
                $stages = $this->game_model->retrieveStage($this->client_id, $this->site_id, $game['_id'], array(
                    'stage_level' => isset($query_data['stage_level']) && !empty($query_data['stage_level']) ? $query_data['stage_level'] : null,
                    'stage_name' => isset($query_data['stage_name']) && !empty($query_data['stage_name']) ? $query_data['stage_name'] : null,
                ));

                // Get stage setting
                foreach ($stages as &$stage){
                    $items = $this->game_model->retrieveItem($this->client_id, $this->site_id, $game['_id'], $query_data, $stage['item_id']);

                    // Get item name
                    foreach ($items as &$item){
                        $item['item_name'] = $this->badge_model->getBadge(array(
                            'client_id' => $this->client_id,
                            'site_id' => $this->site_id,
                            'badge_id' => new MongoId($item['item_id']),
                        ))['name'];
                        unset($item['item_id']);
                    }
                    $stage['item'] = $items;
                    unset($stage['item_id']);
                }
                $game['stage'] = $stages;
            }
            unset($game['_id']);
        }
        array_walk_recursive($games, array($this, "convert_mongo_object_and_optional"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $games, 'processing_time' => $t)), 200);
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
            $item_id_list = (isset($stage[0]['item_id']) && !empty($stage[0]['item_id']) ? $stage[0]['item_id'] : null);
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
}