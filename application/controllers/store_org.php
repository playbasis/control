<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Store_org extends REST2_Controller
{
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

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key)
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


    private function recurGetChildUnder($client_id,$site_id,$parent_node,&$result,&$layer =0, $num = 0){
        //array_push($result,$num);
        //array_push($result,$layer);
        if($num++<=$layer || $layer==0){
            array_push($result,$parent_node);
        }

        $nodes = $this->store_org_model->findAdjacentChildNode($client_id,$site_id,new MongoId($parent_node));
        if(isset($nodes)){
            foreach($nodes as $node){

                $this->recurGetChildUnder($client_id,$site_id,$node['_id'],$result,$layer,$num);
            }
        }else{
            return $result;
        }
    }

    private function recurGetChildByLevel($client_id,$site_id,$parent_node,&$result,&$layer =0, $num = 0){
        //array_push($result,$num);
        //array_push($result,$layer);
        if($num++==$layer || $layer==0){
            array_push($result,$parent_node);
        }

        $nodes = $this->store_org_model->findAdjacentChildNode($client_id,$site_id,new MongoId($parent_node));
        if(isset($nodes)){
            foreach($nodes as $node){

                $this->recurGetChildByLevel($client_id,$site_id,$node['_id'],$result,$layer,$num);
            }
        }else{
            return $result;
        }
    }

    public function getChildNode_get($node_id = '',$layer=0)
    {
        $result = array();

        if(!$node_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);

        $this->recurGetChildUnder($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node_id),$result,$layer);

        $this->response($this->resp->setRespond($result), 200);
    }

    public function saleReport_get($node_id = '') {
        $result = array();

        if(!$node_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);

        $month = $this->input->get('month');
        if(!$month)
            $month = date("m",time());
        $year = $this->input->get('year');
        if(!$year)
            $year = date("Y",time());
        $action = $this->input->get('action');
        if(!$action)
            $action = "sell";
        $parameter = $this->input->get('parameter');
        if(!$parameter)
            $parameter = "amount";

        $list = array();
        $this->recurGetChildUnder($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node_id),$list);

        $table=$this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],$this->validToken['site_id'],$list,$action,$parameter,$month,$year,2);

        $this_month_time = strtotime($year."-".$month);
        $previous_month_time = strtotime('-1 month', $this_month_time);

        $current_month = date("m",$this_month_time);
        $current_year = date("Y",$this_month_time);

        $previous_month = date("m",$previous_month_time);
        $previous_year = date("Y",$previous_month_time);

        $current_month_sales =  $table[$current_year][$current_month][$parameter];
        $previous_month_sales = $table[$previous_year][$previous_month][$parameter];

        $result[$parameter]  = $current_month_sales;
        // for debug
        //$result['previous_month_amount'] = $previous_month_sales;

        if($current_month_sales==0&&$previous_month_sales==0){
            $result['percent_changed']=0;
        }elseif($previous_month_sales==0){
            $result['percent_changed']=100;
        }else{
            $result['percent_changed']=(($current_month_sales-$previous_month_sales)*100)/$previous_month_sales;
        }

        $this->response($this->resp->setRespond($result), 200);
    }

    public function saleHistory_get($node_id = '',$count = 6) {
        $result = array();
        if(!$node_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);

        $month = $this->input->get('month');
        if(!$month)
            $month = date("m",time());
        $year = $this->input->get('year');
        if(!$year)
            $year = date("Y",time());
        $action = $this->input->get('action');
        if(!$action)
            $action = "sell";
        $parameter = $this->input->get('parameter');
        if(!$parameter)
            $parameter = "amount";


        $node_list = array();
        $this->recurGetChildUnder($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node_id),$node_list);

        $table=$this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],$this->validToken['site_id'],$node_list,$action,$parameter,$month,$year,$count+1);

        $this_month_time = strtotime($year."-".$month);
        for($index = 0; $index < $count; $index++) {
            $current_month = date("m", strtotime('-' . ($index) . ' month', $this_month_time));
            $current_year = date("Y", strtotime('-' . ($index) . ' month', $this_month_time));

            $previous_month = date("m",strtotime('-'.($index+1).' month', $this_month_time));
            $previous_year = date("Y",strtotime('-'.($index+1).' month', $this_month_time));

            $current_month_sales =  $table[$current_year][$current_month][$parameter];
            $previous_month_sales = $table[$previous_year][$previous_month][$parameter];

            $result[$current_year][$current_month][$parameter] = $current_month_sales;

            if($current_month_sales==0&&$previous_month_sales==0){
                $result[$current_year][$current_month]['percent_changed']=0;
            }elseif($previous_month_sales==0){
                $result[$current_year][$current_month]['percent_changed']=100;
            }else{
                $result[$current_year][$current_month]['percent_changed']=(($current_month_sales-$previous_month_sales)*100)/$previous_month_sales;
            }
        }

        $this->response($this->resp->setRespond($result), 200);
    }

    public function saleBoard_get($node_id = '',$level = null) {
        $result = array();

        if(!$node_id)
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'node_id'
            )), 200);

        $month = $this->input->get('month');
        if(!$month)
            $month = date("m",time());
        $year = $this->input->get('year');
        if(!$year)
            $year = date("Y",time());
        $action = $this->input->get('action');
        if(!$action)
            $action = "sell";
        $parameter = $this->input->get('parameter');
        if(!$parameter)
            $parameter = "amount";
        //$this->benchmark->mark('rank_peer_start');

        $candidate_node = array();
        $this->recurGetChildByLevel($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node_id),$candidate_node,$level);

        foreach($candidate_node as $node){
            $list = array();
            $this->recurGetChildUnder($this->validToken['client_id'],$this->validToken['site_id'],new MongoId($node),$list);

            $table=$this->store_org_model->getSaleHistoryOfNode($this->validToken['client_id'],$this->validToken['site_id'],$list,$action,$parameter,$month,$year,2);

            $this_month_time = strtotime($year."-".$month);
            $previous_month_time = strtotime('-1 month', $this_month_time);

            $current_month = date("m",$this_month_time);
            $current_year = date("Y",$this_month_time);

            $previous_month = date("m",$previous_month_time);
            $previous_year = date("Y",$previous_month_time);

            $current_month_sales =  $table[$current_year][$current_month]['amount'];
            $previous_month_sales = $table[$previous_year][$previous_month]['amount'];

            $temp['amount']  = $current_month_sales;
            //$temp['previous_month_amount'] = $previous_month_sales;

            if($current_month_sales==0&&$previous_month_sales==0){
                $temp['percent_changed']=0;
            }elseif($previous_month_sales==0){
                $temp['percent_changed']=100;
            }else{
                $temp['percent_changed']=(($current_month_sales-$previous_month_sales)*100)/$previous_month_sales;
            }

            array_push($result,array_merge(array('node_id' => new MongoId($node)),$temp));
        }

        foreach ($result as $key => $raw){
            $temp_name[$key] = $raw['node_id'];
            $temp_value[$key] =  $raw['amount'];
        }
        if (isset($temp_value) && isset($temp_name))
        {
            array_multisort( $temp_value, SORT_DESC,$temp_name, SORT_ASC, $result);
        }

        //$this->benchmark->mark('rank_peer_end');
        //$result['processing_time'] = $this->benchmark->elapsed_time('rank_peer_start', 'rank_peer_end');
        $this->response($this->resp->setRespond($result), 200);

    }
}