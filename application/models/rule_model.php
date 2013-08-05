<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rule_model extends MY_Model
{
    public function getActionGigsawList($siteId,$clientId){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id',  new MongoID($siteId));
        $this->mongo_db->where('client_id',  new MongoID($clientId));
        $results = $this->mongo_db->get("playbasis_action_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {
                    $rowx['dataSet'] = unserialize($rowx['init_dataset']);
                    $rowx['id']=1;#hard code set id to be '1'
                    $rowx['category']='ACTION';
                }
                $output = $results;
            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getConditionGigsawList($siteId,$clientId){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $this->mongo_db->where('category', 'CONDITION');
        $results = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {

                    $rowx['dataSet'] = unserialize(trim($rowx['init_dataset']));
                    $rowx['specific_id']= $rowx['_id'];
                    $rowx['category']='CONDITION';
                }
                $output = $results;
            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getRewardGigsawList($siteId,$clientId){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $this->mongo_db->where('reward_id', array('$lt'=>10000));
        $results = $this->mongo_db->get("playbasis_reward_to_client");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            if(count($results)>0){
                foreach ($results as &$rowx) {
                    $rowx['dataSet'] = unserialize(trim($rowx['init_dataset']));
                    $rowx['id']=2;#hard code set id to be '2'
                    $rowx['category']='REWARD';
                }

                // append custom reward
                $this->set_site_mongodb(0);

                $this->mongo_db->where('site_id', new MongoID($siteId));
                $this->mongo_db->where('client_id', new MongoID($clientId));
                $this->mongo_db->where('category', 'REWARD');
                $this->mongo_db->where('jigsaw_id', array('$ne'=>2));
                $ds2 = $this->mongo_db->get("playbasis_game_jigsaw_to_client");

                if(count($ds2)>0){
                    $ds2->row['specific_id'] = $ds2->row['id'];//'';
                    $ds2->row['jigsaw_category'] = 'REWARD';

                    $ds2->row['dataSet'] = unserialize(trim($ds2->row['dataSet']));

                    array_push($results, $ds2->row);
                }

                $output = $results;


            }


        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getRuleById($siteId,$clientId,$ruleId){



        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            $this->set_site_mongodb(0);

            $this->mongo_db->where('site_id', new MongoID($siteId));
            $this->mongo_db->where('client_id', new MongoID($clientId));
            $this->mongo_db->where('_id', new MongoID($ruleId));
            $results = $this->mongo_db->get("playbasis_rule");
            $ds = $this->unserializeRuleSet($results);

            if(count($ds)>0)
                $output = $ds;

        }catch(Exception $e){
            //Exception stuff
        }

        return $output;

    }

    public function getRulesByCombinationId($siteId,$clientId){

        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id', new MongoID($siteId));
        $this->mongo_db->where('client_id', new MongoID($clientId));
        $results = $this->mongo_db->get("playbasis_rule");

        $output = array( 'error'=>1 ,'success'=>false ,'msg'=>'Error , invalid request format or missing parameter');

        try{
            if(count($results)>0) {
                $output = $results;
                /*Cut time string off*/
                foreach($output as  &$value){
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

        return $output;
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
            $rowx['jigsaw_set'] = unserialize(trim($rowx['jigsaw_set']));
            $rowx['date_added'] = $this->datetimeMongotoReadable($rowx['date_added']);
            $rowx['date_modified'] = $this->datetimeMongotoReadable($rowx['date_modified']);
        }
        return $dataSet;
    }
}