<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Player_model extends MY_Model
{
    public function getPlayers($data) {
        $player_data = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if (isset($data['client_id']) && isset($data['site_id'])) {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        }

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            }else{
                $order = 1;
            }
        }else{
            $order = 1;
        }

        $sort_data = array(
            'first_name',
            'exp',
            'level',
            'status',
            'sort_order',
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        }else{
            $this->mongo_db->order_by(array('_id' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results =  $this->mongo_db->get('playbasis_player');

        foreach ($results as $result) {
            $player_data[] = array(
                'pb_player_id' => $result['pb_player_id'],
                'username' => $result['username'],
                'first_name' => $result['first_name'],
                'last_name' => $result['last_name'],
                'email' => $result['email'],
                'nickname' => $result['nickname'],
                'gender' => $result['gender'] === '1' ? 'male' : 'female',
                'image' => $result['image'],
                'level' => $result['level'],
                'exp' => $result['exp'],
                'action' => $this->getPlayerAction($site_id, $client_id, $result['pb_player_id']),
                'status' => (bool)$result['status'],
                'date_added' => $this->datetimeMongotoReadable($result['date_added']),
                'date_modified' => $this->datetimeMongotoReadable($result['date_modified']),
                // 'points' => $this->getUserPoint($result['pb_player_id'] , $sql_reward),
                'points' => 0,
                'age' => $this->getAge($result['birth_date'])
            );
        }

        return $player_data;
    }

    private function getPlayerAction($site_id, $client_id, $player_id) {

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($player_id));
        $results =  $this->mongo_db->get('playbasis_action_to_client');

        $player_action = array();

        foreach ($results as $result) {
            $player_action[$result['action_name']] = array(
                'action_name' => $result['action_name'],
                'amount' => $player_action[$result['action_name']]? $player_action[$result['action_name']]['amount'] + 1 : 1
            );
        }

        return $player_action;
    }

    public function getTotalPlayers($site_id, $client_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $results = $this->mongo_db->count("playbasis_player");

        return $results;
    }

    public function getRewardListAPI($client_id, $site_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $rewards =  $this->mongo_db->get('playbasis_reward_to_client');

        $result = array();
        foreach ($rewards as $reward) {
            $result += array($reward['reward_id']."" => $reward['name']);
        }
        return $result;
    }

    public function getActionListAPI($client_id, $site_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $actions =  $this->mongo_db->get('playbasis_action_to_client');

        $result = array();
        foreach ($actions as $action) {
            $result += array($action['action_id']."" => $action['name']);
        }

        return $result;
    }

    public function getDonutMaxLevel($data = array()){

        $this->set_site_mongodb(0);

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        $total = count($result);

        foreach($result as $r){
            $donut_data[$r['level']] = array(
                'level' => $r['level'],
                'value' => (isset($donut_data[$r['level']]))?$donut_data[$r['level']]['value']+1:1
            );
        }
        krsort($donut_data);

        $output['result'] = $donut_data[key($donut_data)];
        $output['total'] = $total;

        return $output;
    }

    public function getDonutLevel($data) {

        $this->set_site_mongodb(0);

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        if(isset($data['filter_sort']) && isset($data['filter_sort'][0]) && isset($data['filter_sort'][0]['level'])){
            $level = $data['filter_sort'][0]['level'];
        }else{
            $level = $result[0]['level'];
        }

        $donut_data[] = array(
            'level' => $level,
            'value' => count($result)
        );
        return $donut_data;

    }

    public function getDonutGender($data) {

        $this->set_site_mongodb(0);

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        $total = count($result);

        foreach($result as $r){
            $donut_data[$r['gender']] = array(
                'gender_id' => $r['gender'],
                'value' => (isset($donut_data[$r['gender']]))?$donut_data[$r['gender']]['value']+1:1
            );
        }
        krsort($donut_data);

        $output['result'] = $donut_data;
        $output['total'] = $total;
        return $output;
    }

    public function getDonutAction($data = array()){

        $this->set_site_mongodb(0);

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        $total = count($result);

        foreach($result as $r){
            $donut_data[$r['action_value']] = array(
                'action_value' => $r['action_value'],
                'value' => (isset($donut_data[$r['action_value']]))?$donut_data[$r['action_value']]['value']+1:1
            );
        }
        krsort($donut_data);

        $output['result'] = $donut_data;
        $output['total'] = $total;
        return $output;
    }

    public function getDonutReward($data = array()){

        $this->set_site_mongodb(0);

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        $total = count($result);

        foreach($result as $r){
            $donut_data[$r['value']] = array(
                'reward_value' => $r['value'],
                'value' => (isset($donut_data[$r['value']]))?$donut_data[$r['value']]['value']+1:1
            );
        }
        krsort($donut_data);

        $output['result'] = $donut_data;
        $output['total'] = $total;
        return $output;
    }

    public function getDonutTotalPlayer($data = array()) {
        //level_value gender_value reward_id reward_value action id action_value
        //choose parameter level gender reward action

        //reuturn
        // >options
        // >chart data
        /*
            parameter set :
            ==============
            |level
            |level:1-6
            |level:1-6|gender
            |lelel:1-6|gender:m|action:like
            |lelel:1-6|gender:m|action:like:1-100|reward:coin
        */
        $this->set_site_mongodb(0);

        $res = $this->filterMongoPlayer($data);

        $result = $this->mongoQueryPlayer($data, $res);

        return count($result);
    }

    public function getIsotopePlayer($data) {

        $this->set_site_mongodb(0);

        $res = $this->filterMongoPlayer($data);

        $res['reward_id_value'] = true;

        $result = $this->mongoQueryPlayer($data, $res);

        $total = count($result);

        $sort_data = array(
            'first_name',
            'exp',
            'level',
            'status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $this->vsort($result, $data['sort'], 'desc');
            } else {
                $this->vsort($result, $data['sort'], 'asc');
            }
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $result = array_slice($result, $data['start'], $data['limit']);
        }

        $output['result'] = $result;
        $output['total'] = $total;

        return $output;

    }

//    public function getIsotopeTotalPlayer($data = array()) {
//
//        $this->set_site_mongodb(0);
//
//        $res = $this->filterMongoPlayer($data);
//
//        $result = $this->mongoQueryPlayer($data, $res);
//
//        return count($result);
//    }

    public function getActionsByPlayerId($pb_player_id) {

//        $this->benchmark->mark('action_start');

        $this->set_site_mongodb(0);

        $this->load->model('Action_model');

        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $action =  $this->mongo_db->get('playbasis_action_log');

        $action_data = array();
        foreach ($action as $a) {
            $action_info = $this->Action_model->getAction($a['action_id']);
            $action_data[$a['action_id'].""] = array(
                'action_id' => $a['action_id'],
                'name' => $a['action_name'],
                'icon' => $action_info ? $action_info[0]['icon'] : '',
                'total' => ( isset($action_data[$a['action_id'].""] ) ) ? $action_data[$a['action_id'].""]['total'] + 1 : 1,
            );
        }

//        $this->benchmark->mark('action_end');
//
//        echo "ActionsByPlayerId : ".$this->benchmark->elapsed_time('action_start', 'action_end');

        return $action_data;
    }

    public function getEventLog($pb_player_id, $type) {

//        $this->benchmark->mark('event_start');

        $this->set_site_mongodb(0);

        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $this->mongo_db->where('event_type', strtoupper($type));
        $event =  $this->mongo_db->get('playbasis_event_log');

        $event_data = array();
        foreach ($event as $e) {
            $event_data[$e['event_type'].""] = array(
                'name' => $e['event_type'],
                'total' => ( isset($event_data[$e['event_type']] ) ) ? $event_data[$e['event_type']]['total'] + 1 : 1,
                'date_modified' => $this->datetimeMongotoReadable($e['date_modified']),
            );
        }

//        $this->benchmark->mark('event_end');
//
//        echo "EventLog : ".$this->benchmark->elapsed_time('event_start', 'event_end');

        return $event_data;

    }

    public function getBadgeByPlayerId($data) {

        $this->set_site_mongodb(0);

        $this->mongo_db->where('pb_player_id', new MongoID($data['pb_player_id']));

        if(isset($data['filter_badges_id'])){
            $this->mongo_db->where('badge_id', new MongoID($data['filter_badges_id']));
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $order = " DESC";
        } else {
            $order = " ASC";
        }

        $sort_data = array(
            'amount',
            'date_modified',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('amount' => $order));
        }

        $badges_data =  $this->mongo_db->get('playbasis_badge_to_player');

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $badges_data = array_slice($badges_data, $data['start'], $data['limit']);
        }

        return $badges_data;
    }

    private function mongoQueryPlayer($data, $res){
//        $this->benchmark->mark('mongo_start');

        $this->set_site_mongodb(0);

        $action_id_value_string = "";
        if(isset($res['action_id_value'])){
            $action_id_value_string = $res['action_id_value'];
        }

        $action_value_string = "";
        if(isset($res['action_value'])){
            $action_value_string = $res['action_value'];
        }

//        if(isset($res['action_id_value']) || isset($res['action_value'])){
//            $actions = $this->mongo_db->command('{ aggregate : "playbasis_action_log", pipeline : [
//                {
//                $match :
//                {
//                    "client_id" : '.new MongoID($data['client_id']).',
//                    "site_id" : '.new MongoID($data['site_id']).',
//                    '.$action_id_value_string.'
//                }
//                },
//                {
//                $group:
//                {
//                    _id: {pb_player_id:"$pb_player_id",action_id:"$action_id",action_name:"action_name"},
//                    action_value: {$sum:1},
//                }
//                },
//                {
//                $project:
//                {
//                     _id: 0,
//                     pb_player_id: "$_id.pb_player_id",
//                     action_id: "$_id.action_id",
//                     action_name: "$_id.action_name",
//                     action_value: 1,
//                }
//                },
//                '.$action_value_string.'
//            ]}');
//        }

        if(isset($res['reward_id_value'])){
            $this->mongo_db->where($res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $this->mongo_db->where($res['reward_value']);
        }
        if(isset($res['reward_id_value']) || isset($res['reward_value'])){
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $reward_total =  $this->mongo_db->count('playbasis_reward_to_player');
        }

        if(isset($res['level_value'])){
            $this->mongo_db->where($res['level_value']);
        }
        if(isset($res['exp_value'])){
            $this->mongo_db->where($res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $this->mongo_db->where($res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $this->mongo_db->where(array('level' => array('$ne' => 0)) );
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        $player_total =  $this->mongo_db->count('playbasis_player');

        if(isset($res['level_value'])){
            $this->mongo_db->where($res['level_value']);
        }
        if(isset($res['exp_value'])){
            $this->mongo_db->where($res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $this->mongo_db->where($res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $this->mongo_db->where(array('level' => array('$ne' => 0)) );
        }

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        $player =  $this->mongo_db->limit(300000)->get('playbasis_player');


        $players = array();
        foreach( $player as $p) {
            $p['pb_player_id'] = $p['_id'];
            unset($p['_id']);
            $players[] = $p;
        }

        if(isset($rewards)){
            $players = $this->matchData('pb_player_id', $rewards, $players);
        }

        if(isset($actions)){
            $players = $this->matchData('pb_player_id', $actions, $players);
        }

//        $this->benchmark->mark('mongo_end');
//
//        echo "all process in mongo : ".$this->benchmark->elapsed_time('mongo_start', 'mongo_end');

        return $players;
    }

    private function matchData($keyOfData, $arrayCheck, $arrayOfData){

//        $this->benchmark->mark('match_start');

        $ret = array();
        foreach( $arrayCheck as $a) {
            unset($a['_id']);
            foreach( $arrayOfData as $array) {
                if( $array[$keyOfData] == $a[$keyOfData] ) {
                    $ret[] = array_merge($array, $a);
                }
            }
        }

//        $this->benchmark->mark('match_end');
//
//        echo "match data : ".$this->benchmark->elapsed_time('match_start', 'match_end');

        return $ret;
    }

    private function filterMongoPlayer($data){

        $res = array();

        if (!empty($data['filter_sort'])) {

            foreach ($data['filter_sort'] as $filter) {
                $filter_name = $filter['name'];

                switch ($filter_name) {
                    case 'action_id':
//                        $action_pos = strrpos($filter['value'], '-');
//
//                        if ($action_pos === false) {
//                            $res['action_id_value'] =  array('action_id' => new MongoID($filter['value']));
//                        } else {
//                            $action_id_explode = explode('-', $filter['value']);
//                            $action_id_explode = $this->check_array($action_id_explode);
//
//                            $res['action_id_value'] =  array('action_id' => array('$gte' => new MongoID($action_id_explode[0]), '$lte' => new MongoID($action_id_explode[1])));
//                        };

                        $action_id_pos = strrpos($filter['value'], '-');

                        if ($action_id_pos === false) {
//                            $res['action_id_value'] = '"action_id" : '.new MongoID($filter['value']).',';
                            $res['action_id_value'] =  array('action_id' => new MongoID($filter['value']));
                        } else {
                            $action_id_explode = explode('-', $filter['value']);
                            $action_id_explode = $this->check_array($action_id_explode);

//                            $res['action_id_value'] = '"action_id" : '.$action_id_explode[0].',';
                            $res['action_id_value'] = array('action_id' => array('$gte' => new MongoID($action_id_explode[0]), '$lte' => new MongoID($action_id_explode[1])));
                        };

                        break;
                    case 'action_value':
//                        $action_pos = strrpos($filter['value'], '-');
//
//                        if ($action_pos === false) {
//                            $res['action_value'] =  array('action_value' => (int)$filter['value']);
//                        } else {
//                            $action_explode = explode('-', $filter['value']);
//                            $action_explode = $this->check_array($action_explode);
//
//                            $res['action_value'] =  array('action_value' => array('$gte' => (int)$action_explode[0], '$lte' => (int)$action_explode[1]));
//                        };

                        $action_pos = strrpos($res['value'], '-');

//                        if ($action_pos === false) {
//                            $res['action_value'] = '{
//                                $match :
//                                {
//                                    "action_value" : '.$res['action_value'].',
//                                }
//                                },';
//                        } else {
//                            $action_explode = explode('-', $res['action_value']);
//                            $action_explode = $this->check_array($action_explode);
//
//                            $res['action_value'] = '{
//                                $match :
//                                {
//                                    "action_value" : {"$gte" => '.(int)$action_explode[0].', "$lte" => '.(int)$action_explode[1].'}
//                                }
//                                },';
//                        };

                        if ($action_pos === false) {
                            $res['action_value'] =  array('action_value' => (int)$filter['value']);
                        } else {
                            $action_explode = explode('-', $filter['value']);
                            $action_explode = $this->check_array($action_explode);

                            $res['action_value'] =  array('action_value' => array('$gte' => (int)$action_explode[0], '$lte' => (int)$action_explode[1]));
                        };

                        break;
                    case 'reward_id':
                        $reward_pos = strrpos($filter['value'], '-');

                        if ($reward_pos === false) {
                            $res['reward_id_value'] =  array('reward_id' => new MongoID($filter['value']));
                        } else {
                            $reward_id_explode = explode('-', $filter['value']);
                            $reward_id_explode = $this->check_array($reward_id_explode);

                            $res['reward_id_value'] =  array('reward_id' => array('$gte' => new MongoID($reward_id_explode[0]), '$lte' => new MongoID($reward_id_explode[1])));
                        };

                        break;
                    case 'reward_value':
                        $reward_pos = strrpos($filter['value'], '-');

                        if ($reward_pos === false) {
                            $res['reward_value'] =  array('value' => (int)$filter['value']);
                        } else {
                            $reward_explode = explode('-', $filter['value']);
                            $reward_explode = $this->check_array($reward_explode);

                            $res['reward_value'] =  array('value' => array('$gte' => (int)$reward_explode[0], '$lte' => (int)$reward_explode[1]));
                        };

                        break;
                    case 'level':
                        if(isset($filter['value']) && ($filter['value'] || $filter['value'] === 0)){
                            $level_pos = strrpos($filter['value'], '-');

                            if ($level_pos === false) {
                                $res['level_value'] =  array('level' => (int)$filter['value']);
                            } else {
                                $level_explode = explode('-', $filter['value']);
                                $level_explode = $this->check_array($level_explode);

                                $res['level_value'] =  array('level' => array('$gte' => (int)$level_explode[0], '$lte' => (int)$level_explode[1]));
                            };
                        }
                        break;
                    case 'exp':
                        $exp_pos = strrpos($filter['value'], '-');

                        if ($exp_pos === false) {
                            $res['exp_value'] =  array('exp' => (int)$filter['value']);
                        } else {
                            $exp_explode = explode('-', $filter['value']);
                            $exp_explode = $this->check_array($exp_explode);

                            $res['exp_value'] =  array('exp' => array('$gte' => (int)$exp_explode[0], '$lte' => (int)$exp_explode[1]));
                        };

                        break;
                    case 'gender_id':
                        $action_pos = strrpos($filter['value'], '-');

                        if ($action_pos === false) {
                            $res['gender_value'] =  array('gender' => (int)$filter['value']);
                        } else {
                            $gender_explode = explode('-', $filter['value']);
                            $gender_explode = $this->check_array($gender_explode);

                            $res['gender_value'] =  array('gender' => array('$gte' => (int)$gender_explode[0], '$lte' => (int)$gender_explode[1]));
                        };

                        break;
                    case 'gender_value':
                        if (isset($filter['value'])) {
                            if ($filter['value']=='m') {
                                $res['gender_value'] = array('gender' => 1);
                            } else if ($filter['value']=='f') {
                                $res['gender_value'] = array('gender' => 2);
                            };
                        }
                        break;
                };
            }
        }

        return $res;
    }

    private function check_array($old_array){
        $new_array=array();
        foreach($old_array as $b){
            if($b){
                array_push($new_array,$b);
            }else if(is_numeric($b)){
                array_push($new_array,$b);
            }
        }
        asort($new_array);
        return $new_array;
    }

    private function getAge($birthdate) {
        $now = new DateTime();
        $birthdate = $this->datetimeMongotoReadable($birthdate);
        $oDateBirth = new DateTime($birthdate);
        $oDateInterval = $now->diff($oDateBirth);

        return $oDateInterval->y;
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

    private function vsort (&$array, $key, $order='asc') {
        $res=array();
        $sort=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sort[$ii]=$va[$key];
        }
        if(strtolower($order) == 'asc'){
            asort($sort);
        }else{
            arsort($sort);
        }
        foreach ($sort as $ii => $va) {
            $res[$ii]=$array[$ii];
        }
        $array=$res;
    }
}
?>