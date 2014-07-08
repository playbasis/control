<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rule_model extends MY_Model
{
    public function getActionGigsawList($siteId,$clientId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('action_id','name','description','sort_order','icon','status','init_dataset'));
        $this->mongo_db->where('site_id',  new MongoID($siteId));
        $this->mongo_db->where('client_id',  new MongoID($clientId));
        $results = $this->mongo_db->get("playbasis_action_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name',  'action');
        $this->mongo_db->where('category',  'ACTION');
        $jigsaw = $this->mongo_db->get("playbasis_jigsaw");

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {
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
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('jigsaw_id','name','description','sort_order','icon','status','init_dataset'));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $this->mongo_db->where('category', 'CONDITION');
        $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

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
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id'));
        $reward_res= $this->mongo_db->get("playbasis_reward");

        $reward_id = array();
        foreach($reward_res as $r){
            $reward_id[] = $r['_id'];
        }

        $this->mongo_db->select(array('reward_id','name','description','sort_order','icon','status','init_dataset'));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $this->mongo_db->where('reward_id', array('$in'=>$reward_id));
        $ds = $this->mongo_db->get("playbasis_reward_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name',  'reward');
        $this->mongo_db->where('category',  'REWARD');
        $jigsaw = $this->mongo_db->get("playbasis_jigsaw");

        try{
            if(count($ds)>0){
                foreach ($ds as &$rowx) {
                    $rowx['specific_id'] = $rowx['reward_id']."";
                    $rowx['name'] = htmlspecialchars($rowx['name'], ENT_QUOTES);
                    $rowx['description'] = htmlspecialchars($rowx['description'], ENT_QUOTES);
//                    $rowx['dataSet'] = unserialize($rowx['init_dataset']);
                    $rowx['dataSet'] = $rowx['init_dataset'];
//                    $rowx['id']=2;#hard code set id to be '2'
                    $rowx['id']=$jigsaw[0]['_id']."";
                    $rowx['category']='REWARD';
                    unset($rowx['reward_id']);
                    unset($rowx['init_dataset']);
                }

                // append custom reward

                $this->mongo_db->select(array('jigsaw_id','name','description','sort_order','icon','status','category','init_dataset'));
                $this->mongo_db->where('site_id', new MongoID($siteId));
                $this->mongo_db->where('client_id', new MongoID($clientId));
                $this->mongo_db->where('category', 'REWARD');
                $this->mongo_db->where('name', array('$ne'=>'reward'));
                $ds2 = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

                if(count($ds2)>0){
                    $ds2[0]['specific_id'] = $ds2[0]['jigsaw_id']."";//'';
                    $ds2[0]['jigsaw_category'] = 'REWARD';

//                    $ds2[0]['dataSet'] = unserialize(trim($ds2[0]['init_dataset']));
                    $ds2[0]['dataSet'] = $ds2[0]['init_dataset'];
                    $ds2[0]['id']=$ds2[0]['jigsaw_id']."";
                    $ds2[0]['category']='REWARD';

                    unset($ds2[0]['jigsaw_id']);
                    unset($ds2[0]['init_dataset']);
                    array_push($ds, $ds2[0]);
                }

                $output = $ds;


            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    function saveRule($input){
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
//                'jigsaw_set' => serialize($input['jigsaw_set']),
                'jigsaw_set' => $input['jigsaw_set'],
                'active_status' => (bool)$input['active_status'],
                'date_added' => new MongoDate(strtotime($input['date_added'])),
                'date_modified' => new MongoDate(strtotime($input['date_modified']))
            ));

            if($res){
                return array('success'=>true);
            }else{
                return array('success'=>false);
            }

        }else{

            $this->mongo_db->where('_id', new MongoID($input['rule_id']));
            $this->mongo_db->set('client_id', new MongoID($input['client_id']));
            $this->mongo_db->set('site_id', new MongoID($input['site_id']));
            $this->mongo_db->set('action_id', new MongoID($input['action_id']));
            $this->mongo_db->set('name', $input['name']);
            $this->mongo_db->set('description', $input['description']);
            $this->mongo_db->set('tags', $input['tags']);
//            $this->mongo_db->set('jigsaw_set',serialize($input['jigsaw_set']));
            $this->mongo_db->set('jigsaw_set',$input['jigsaw_set']);
            $this->mongo_db->set('active_status', (bool)$input['active_status']);
            $this->mongo_db->set('date_modified', new MongoDate(strtotime($input['date_modified'])));
            $res = $this->mongo_db->update('playbasis_rule');

            if($res){
                return array('success'=>true);
            }else{
                return array('success'=>false);
            }

        }

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

    public function getRulesByCombinationId($siteId,$clientId){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id','client_id','site_id','action_id','name','description','tags','active_status','date_added','date_modified'));
        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $results = $this->mongo_db->get("playbasis_rule");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            if(count($results)>0) {
                $output = $results;
                /*Cut time string off*/
                foreach($output as  &$value){
                    $value['rule_id'] = $value["_id"]."";
                    $value['client_id'] = $value["client_id"]."";
                    $value['site_id'] = $value["site_id"]."";
                    $value['action_id'] = $value["action_id"]."";
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
}