<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rule_model extends MY_Model
{
    public function getActionGigsawList($siteId="",$clientId=""){
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN))
            throw new Exception("error_xor_client_site");
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'action_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('status',  true);
        if ($clientId) {
            $this->mongo_db->where('site_id',  new MongoID($siteId));
            $this->mongo_db->where('client_id',  new MongoID($clientId));
            $results = $this->mongo_db->get("playbasis_action_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_action");
        }

        $output = array(
            'error'=>1,
            'success'=>false,
            'msg'=>'Error , invalid request format or missing parameter'
        );

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name',  'action');
        $this->mongo_db->where('category',  'ACTION');
        $jigsaw = $this->mongo_db->get("playbasis_jigsaw");

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {
                    if (!$clientId) // default action use _id instead
                        $rowx["action_id"] = $rowx["_id"];
                    $rowx['specific_id'] = $rowx['action_id']."";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
//                    $rowx['dataSet'] = unserialize($rowx['init_dataset']);
                    $rowx['dataSet'] = $rowx['init_dataset'];
//                    $rowx['id']=1;#hard code set id to be '1'
                    $rowx['id']=$jigsaw[0]['_id']."";
                    $rowx['category']='ACTION';
                    unset($rowx['action_id']);
                    unset($rowx['init_dataset']);
                }
                $output = $results;
            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getConditionGigsawList($siteId,$clientId){
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN))
            throw new Exception("error_xor_client_site");
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'jigsaw_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('category', 'CONDITION');
        if ($clientId) {
            $this->mongo_db->where('site_id', new MongoID($siteId));
            $this->mongo_db->where('client_id', new MongoID($clientId));
            $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");
        } else {
            $results = $this->mongo_db->get("playbasis_jigsaw");
        }

        $output = array(
            'error'=>1,
            'success'=>false,
            'msg'=>'Error , invalid request format or missing parameter'
        );

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {
                    $rowx['id']=$rowx['jigsaw_id']."";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
//                    $rowx['dataSet'] = unserialize(trim($rowx['init_dataset']));
                    $rowx['dataSet'] = $rowx['init_dataset'];
                    $rowx['specific_id']= $rowx['jigsaw_id']."";//'';//no specific id for contion so using the same id with jigsaw id.
                    $rowx['category']='CONDITION';
                    unset($rowx['jigsaw_id']);
                    unset($rowx['init_dataset']);
                }
                $output = $results;
            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getRewardGigsawList($siteId,$clientId){
        if (filter_var($clientId, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($siteId, FILTER_VALIDATE_BOOLEAN))
            throw new Exception("error_xor_client_site");
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array(
            'reward_id',
            'name',
            'description',
            'sort_order',
            'icon',
            'status',
            'init_dataset'
        ));
        $this->mongo_db->where('status', true);
        if ($clientId) {
            $this->mongo_db->where('site_id', new MongoID($siteId));
            $this->mongo_db->where('client_id', new MongoID($clientId));
            $ds = $this->mongo_db->get("playbasis_reward_to_client");
        } else {
            $ds = $this->mongo_db->get("playbasis_reward");
        }

        $output = array(
            'error'=>1,
            'success'=>false,
            'msg'=>'Error , invalid request format or missing parameter'
        );

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name',  'reward');
        $this->mongo_db->where('category',  'REWARD');
        $this->mongo_db->where('status', true);
        $jigsaw = $this->mongo_db->get("playbasis_jigsaw");

        try{
            if(count($ds)>0){
                foreach ($ds as &$rowx) {
                    $rowx['specific_id'] = $rowx['reward_id']."";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
//                    $rowx['dataSet'] = unserialize($rowx['init_dataset']);
                    $rowx['dataSet'] = isset($rowx['init_dataset'])?$rowx['init_dataset']:null;
//                    $rowx['id']=2;#hard code set id to be '2'
                    $rowx['id']=$jigsaw[0]['_id']."";
                    $rowx['category']='REWARD';
                    unset($rowx['reward_id']);
                    unset($rowx['init_dataset']);
                }

                // append custom reward
                if ($clientId) {
                    $this->mongo_db->select(array(
                        'jigsaw_id',
                        'name',
                        'description',
                        'sort_order',
                        'icon',
                        'status',
                        'category',
                        'init_dataset'
                    ));
                    $this->mongo_db->where('site_id', new MongoID($siteId));
                    $this->mongo_db->where('client_id', new MongoID($clientId));
                    $this->mongo_db->where('category', 'REWARD');
                    $this->mongo_db->where('status', true);
//                    $this->mongo_db->where('name', array('$nin'=>array('reward', 'customPointReward')));
                    $ds2 = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

                    if(count($ds2)>0){
                        $ds2[0]['specific_id'] = $ds2[0]['jigsaw_id']."";//'';
                        $ds2[0]['jigsaw_category'] = 'REWARD';
                        $ds2[0]['dataSet'] = $ds2[0]['init_dataset'];
                        $ds2[0]['id']=$ds2[0]['jigsaw_id']."";
                        $ds2[0]['category']='REWARD';

                        unset($ds2[0]['jigsaw_id']);
                        unset($ds2[0]['init_dataset']);
                        array_push($ds, $ds2[0]);
                    }
                }

                $output = $ds;


            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    function saveRule($input){
        $response = function($msg) {
            return array("success" => $msg);
        };
        $this->set_site_mongodb($this->session->userdata('site_id'));
        //set time
        $input['date_added'] = $input['date_modified'] = date('Y-m-d H:i:s');

        if($input['rule_id']=='undefined'){
            $res = $this->mongo_db->insert('playbasis_rule', array(
                'client_id' =>  new MongoID($input['client_id']),
                'site_id' =>  new MongoID($input['site_id']),
                'action_id' => new MongoID($input['action_id']),
                'name' => $input['name'],
                'description' => $input['description'],
                'tags' => $input['tags'],
                'jigsaw_set' => $input['jigsaw_set'],
                'active_status' => (bool)$input['active_status'],
                'date_added' => new MongoDate(strtotime($input['date_added'])),
                'date_modified' => new MongoDate(strtotime($input['date_modified']))
            ));

            if($res){
                return $response(true);
            }else{
                return $response(false);
            }

        }else{
            // check that this rule is from template or not
            $this->mongo_db->where('_id', new MongoID($input['rule_id']));
            $rule = $this->mongo_db->get("playbasis_rule");
            if ($rule) {
                $rule = $rule[0];
                $this->mongo_db->set('date_modified',
                    new MongoDate(strtotime($input['date_modified'])));
                if (!$this->isSameRules($rule, $input)) {
                    $this->mongo_db->where('_id', new MongoID($input['rule_id']));
                    $this->mongo_db->set('client_id', new MongoID($input['client_id']));
                    $this->mongo_db->set('site_id', new MongoID($input['site_id']));
                    $this->mongo_db->set('action_id', new MongoID($input['action_id']));
                    $this->mongo_db->set('name', $input['name']);
                    $this->mongo_db->set('description', $input['description']);
                    $this->mongo_db->set('tags', $input['tags']);
                    $this->mongo_db->set('jigsaw_set',$input['jigsaw_set']);
                    $this->mongo_db->set('active_status', (bool)$input['active_status']);
                    $this->mongo_db->unset_field('clone_id');
                }
                if($this->mongo_db->update('playbasis_rule'))
                    return $response(true);
            }
            // save process failed
            return $response(false);
        }
    }

    /*
     * Clone Rule from Template to Client's rule
     * template rule cannot duplicate in client table
     * but allow if that rule is edited.
     * @param string $rule_id
     * @param string $client_id
     * @param string $site_id
     * @return array
     */
    function cloneRule($rule_id, $client_id, $site_id){
        $response = function($msg) {
            return array("success" => $msg);
        };
        try {
            $rule_obj = new MongoID($rule_id);
            $client_obj = new MongoID($client_id);
            $site_obj = new MongoID($site_id);
        } catch (Exception $e) {
            return $response(false);
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));
        // client must not have this template
        $this->mongo_db->limit(1);
        $is_client_used = $this->mongo_db->get_where("playbasis_rule",
            array(
                "clone_id" => $rule_obj,
                "client_id" => $client_obj,
                "site_id" => $site_obj
            )
        );
        // get template rule
        if (!$is_client_used) {
            $template = $this->getById($rule_obj);
            if ($template) {
                // move _id to clone_id
                $template["clone_id"] = $template["_id"];
                $template["_id"] = new MongoID();
                // save to client
                $template["client_id"] = $client_obj;
                $template["site_id"] = $site_obj;
                $template["active_status"] = false;
                $template["date_added"] = new MongoDate();
                $template["date_modified"] = new MongoDate();
                if ($this->mongo_db->insert("playbasis_rule", $template))
                    return $response(true);
            }
        }
        // clone process not complete
        return $response(false);
    }

    /*
     * get rule by _id
     * @param string id
     * @return array
     */
    public function getById($id)
    {
        try {
            $id = new MongoID($id);
        } catch (Exception $e) {
            return array();
        }
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $result = $this->mongo_db->get_where("playbasis_rule",
            array("_id" => $id));
        if ($result)
            return $result[0];
        else
            return array();
    }

    public function deleteRule($ruleId,$siteId,$clientId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($ruleId));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $res = $this->mongo_db->delete('playbasis_rule');

        if($res){
            return array('success'=>true,'message'=>$res);
        }else{
            return array('success'=>false);
        }
    }

    function changeRuleState($ruleId,$state,$siteId,$clientId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($ruleId));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $this->mongo_db->set('active_status', (bool)$state);
        $res = $this->mongo_db->update('playbasis_rule');

        if($res){
            return array('success'=>true,'other'=>$res);
        }else{
            return array('success'=>false);
        }

    }

    public function getRuleById($siteId,$clientId,$ruleId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{

            $this->mongo_db->where('site_id', new MongoID($siteId));
            $this->mongo_db->where('client_id', new MongoID($clientId));
            $this->mongo_db->where('_id', new MongoID($ruleId));
            $results = $this->mongo_db->get("playbasis_rule");
            $ds = $this->unserializeRuleSet($results);

            if(count($ds)>0){
                $ds[0]['rule_id'] = $ds[0]['_id']."";
                $ds[0]['site_id'] = $ds[0]['site_id']."";
                $ds[0]['client_id'] = $ds[0]['client_id']."";
                $ds[0]['action_id'] = $ds[0]['action_id']."";
                $ds[0]['_id'] = $ds[0]['_id']."";
                $output = $ds;
            }

        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getRulesByCombinationId($siteId, $clientId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $output = array(
            'error'=>1,
            'success'=>false,
            'msg'=>'Error , invalid request format or missing parameter'
        );

        try {
            $siteObj = new MongoID($siteId);
            $clientObj = new MongoID($clientId);
        }
        catch (Exception $e) {
            return $output;
        }

        $this->mongo_db->select(array(
            '_id',
            'client_id',
            'site_id',
            'action_id',
            'name',
            'description',
            'tags',
            'active_status',
            'date_added',
            'date_modified'
        ));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        if ($clientObj == $siteObj) // Admin
            $this->mongo_db->where('active_status', true);
        $results = $this->mongo_db->get("playbasis_rule");

        $output = array(
            'error'=>1,
            'success'=>false,
            'msg'=>'Error , invalid request format or missing parameter'
        );

        try{
            if(count($results)>0) {
                $output = $results;
                 /*Cut time string off*/
                foreach($output as  &$value){
                    $value['rule_id'] = strval($value["_id"]);
                    $value['client_id'] = strval($value["client_id"]);
                    $value['site_id'] = strval($value["site_id"]);
                    $value['action_id'] = strval($value["action_id"]);
                    foreach ($value as $k2 => &$v2) {
                        if($k2 == "date_added"){
                            $value[$k2] = substr($this->datetimeMongotoReadable($value[$k2]) , 0 ,-8);
                        }
                        if($k2 == "date_modified"){
                            $value[$k2] = $this->datetimeMongotoReadable($value[$k2]);
                        }
                        if($k2 == "rule_id"){
                            $value[$k2] = $value["_id"]."";
                        }
                    }//End for : inner
                }//End for : outter
                 /*End : Cut time string off*/
            }
            else {
                $output['error'] = 2;
                $output['msg'] = 'No data';
            }


        }catch(Exception $e){
            //Exception stuff
        }

        if (isset($output["date_added"]))
            $this->vsort($output, "date_added");
        return $output;
    }

    private function vsort (&$array, $key) {
        $res=array();
        $sort=array();
        reset($array);
        foreach ($array as $ii => $va) {
            if(isset($va[$key])){
                $sort[$ii]=$va[$key];
            }
        }
        asort($sort);
        foreach ($sort as $ii => $va) {
            $res[$ii]=$array[$ii];
        }
        $array=$res;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    private function unserializeRuleSet($dataSet){
        foreach ($dataSet AS &$rowx) {
//            $rowx['jigsaw_set'] = unserialize(trim($rowx['jigsaw_set']));
            $rowx['jigsaw_set'] = $rowx['jigsaw_set'];
            $rowx['date_added'] = $this->datetimeMongotoReadable($rowx['date_added']);
            $rowx['date_modified'] = $this->datetimeMongotoReadable($rowx['date_modified']);
        }
        return $dataSet;
    }

    private function isSameRules($rule1, $rule2) {
        if ($rule1["client_id"] != $rule2["client_id"] or
            $rule1["site_id"] != $rule2["site_id"] or
            $rule1["action_id"] != $rule2["action_id"] or
            $rule1["name"] != $rule2["name"] or
            $rule1["description"] != $rule2["description"] or
            $rule1["tags"] != $rule2["tags"] or
            $rule1["jigsaw_set"] != $rule2["jigsaw_set"])
            return false;
        else
            return true;
    }
}
