<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player_model extends MY_Model
{
    public function getPlayerById($player_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $player_data = null;

        $this->mongo_db->where('_id', new MongoID($player_id));

        $result = $this->mongo_db->get('playbasis_player');

//        foreach ($results as $result) {
//            $player_data = array(
//                'cl_player_id' => $result['cl_player_id'],
//                'pb_player_id' => $result['_id'],
//                'username' => $result['username'],
//                'first_name' => $result['first_name'],
//                'last_name' => $result['last_name'],
//                'email' => $result['email'],
//                'nickname' => $result['nickname'],
//                'gender' => $result['gender'] === '1' ? 'male' : 'female',
//                'image' => $result['image'],
//                'level' => $result['level'],
//                'exp' => $result['exp'],
//                'status' => (bool)$result['status'],
//                'date_added' => $this->datetimeMongotoReadable($result['date_added']),
//                'date_modified' => $this->datetimeMongotoReadable($result['date_modified']),
//                'points' => 0,
//                'age' => $this->getAge($result['birth_date'])
//            );
//        }

        return $result ? $result[0] : null;
    }

    public function getPlaybasisId($clientData)
    {
        if (!$clientData) {
            return null;
        }
        $this->set_site_mongodb($clientData['site_id']);
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where(array(
            'client_id' => $clientData['client_id'],
            'site_id' => $clientData['site_id'],
            'cl_player_id' => $clientData['cl_player_id']
        ));
        $this->mongo_db->limit(1);
        $id = $this->mongo_db->get('playbasis_player');
        return ($id) ? $id[0]['_id'] : null;
    }

    public function getPlayers($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['client_id']) && isset($data['site_id'])) {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        }

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            } else {
                $order = 1;
            }
        } else {
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
        } else {
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

        $player_data = $this->mongo_db->get('playbasis_player');

        return $player_data;
    }

    public function getPlayerPoint($data, $reward_id = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select('value');
        $this->mongo_db->where('pb_player_id', new MongoID($data['pb_player_id']));
        if ($reward_id) {
            $this->mongo_db->where('reward_id', $reward_id);
        }

        $point = $this->mongo_db->get('playbasis_reward_to_player');

        return $point ? $point[0]['value'] : 0;
    }

    private function getPlayerAction($site_id, $client_id, $player_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($player_id));
        $results = $this->mongo_db->get('playbasis_action_to_client');

        $player_action = array();

        foreach ($results as $result) {
            $player_action[$result['action_name']] = array(
                'action_name' => $result['action_name'],
                'amount' => $player_action[$result['action_name']] ? $player_action[$result['action_name']]['amount'] + 1 : 1
            );
        }

        return $player_action;
    }

    public function getTotalPlayers($site_id, $client_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $results = $this->mongo_db->count("playbasis_player");

        return $results;
    }

    public function getRewardListAPI($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $rewards = $this->mongo_db->get('playbasis_reward_to_client');

        $result = array();
        foreach ($rewards as $reward) {
            $result += array($reward['reward_id'] . "" => $reward['name']);
        }
        return $result;
    }

    public function getActionListAPI($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $actions = $this->mongo_db->get('playbasis_action_to_client');

        $result = array();
        foreach ($actions as $action) {
            $result += array($action['action_id'] . "" => $action['name']);
        }

        return $result;
    }

    /*public function getDonutMaxLevel($data = array()){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total =  $this->mongo_db->count('playbasis_summary_of_player');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player",
                'pipeline' => array(
                    array('$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.level',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'level' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'level' => -1
                        ),
                    ),
                    array(
                        '$limit' => 1
                    )
                )
            )
        );

        $output['result'] = $donut_data["result"] ? $donut_data["result"][0] : array();
        $output['total'] = $total;

        return $output;
    }

    public function getDonutLevel($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $result =  $this->mongo_db->count('playbasis_summary_of_player');

        if(isset($data['filter_sort']) && isset($data['filter_sort'][0]) && isset($data['filter_sort'][0]['level'])){
            $level = $data['filter_sort'][0]['level'];
        }else{
            $level = '';
        }

        $donut_data[] = array(
            'level' => $level,
            'value' => $result
        );
        return $donut_data;

    }

    public function getDonutGender($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total =  $this->mongo_db->count('playbasis_summary_of_player');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player",
                'pipeline' => array(
                    array('$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.gender',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'gender_id' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'gender' => -1
                        ),
                    )
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }

    public function getDonutAction($data = array()){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total =  $this->mongo_db->count('playbasis_summary_of_player');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player",
                'pipeline' => array(
                    array('$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.action_'.$res['action_id_check'].'.action_value',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'action_value' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'action_value' => 1
                        ),
                    ),
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }

    public function getDonutReward($data = array()){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total =  $this->mongo_db->count('playbasis_summary_of_player');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player",
                'pipeline' => array(
                    array('$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.reward_'.$res['reward_id_check'].'.value',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'reward_value' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'reward_value' => 1
                        ),
                    ),
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }*/

    //public function getDonutTotalPlayer($data = array()) {
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
    /*$this->set_site_mongodb(0);

    $res = $this->filterMongoPlayer($data);

    $match = array(
        '_id.client_id' => new MongoID($data['client_id']),
        '_id.site_id' => new MongoID($data['site_id'])
    );

    if(isset($res['action_id_value'])){
        $match = array_merge($match, $res['action_id_value']);
    }
    if(isset($res['action_value'])){
        $match = array_merge($match, $res['action_value']);
    }

    if(isset($res['reward_id_value'])){
        $match = array_merge($match, $res['reward_id_value']);
    }
    if(isset($res['reward_value'])){
        $match = array_merge($match, $res['reward_value']);
    }

    if(isset($res['level_value'])){
        $match = array_merge($match, $res['level_value']);
    }
    if(isset($res['exp_value'])){
        $match = array_merge($match, $res['exp_value']);
    }
    if(isset($res['gender_value'])){
        $match = array_merge($match, $res['gender_value']);
    }
    if(!isset($data['show_level_0'])){
        $show_level = array('level' => array('$ne' => 0));
        $match = array_merge($match, $show_level);
    }

    $this->mongo_db->where($match);

    $result =  $this->mongo_db->count('playbasis_summary_of_player');

    return $result;
}*/

    public function getDonutMaxLevel($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        $fil = false;
        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
            $fil = true;
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
            $fil = true;
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
            $fil = true;
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
            $fil = true;
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
            $fil = true;
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
            $fil = true;
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
            $fil = true;
        }
        /*if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }*/

        $this->mongo_db->where($match);

        if ($fil) {
            $total = $this->mongo_db->count('playbasis_summary_of_player_beta');
            $database = "playbasis_summary_of_player_beta";
        } else {
            $total = $this->mongo_db->count('playbasis_player');
            $database = "playbasis_player";
        }


        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => $database,
                'pipeline' => array(
                    array(
                        '$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$level',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'level' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'level' => -1
                        ),
                    ),
                    array(
                        '$limit' => 1
                    )
                )
            )
        );

        $output['result'] = $donut_data["result"] ? $donut_data["result"][0] : array();
        $output['total'] = $total;

        return $output;
    }

    public function getDonutLevel($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $donut_data = array();

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        $fil = false;
        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
            $fil = true;
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
            $fil = true;
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
            $fil = true;
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
            $fil = true;
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
            $fil = true;
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
            $fil = true;
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
            $fil = true;
        }
        if (!isset($data['show_level_0'])) {
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        if ($fil) {
            $result = $this->mongo_db->count('playbasis_summary_of_player_beta');
        } else {
            $result = $this->mongo_db->count('playbasis_player');
        }

        if (isset($data['filter_sort']) && isset($data['filter_sort'][0]) && isset($data['filter_sort'][0]['level'])) {
            $level = $data['filter_sort'][0]['level'];
        } else {
            $level = '';
        }

        $donut_data[] = array(
            'level' => $level,
            'value' => $result
        );
        return $donut_data;

    }

    public function getDonutGender($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        $fil = false;
        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
            $fil = true;
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
            $fil = true;
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
            $fil = true;
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
            $fil = true;
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
            $fil = true;
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
            $fil = true;
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
            $fil = true;
        }
        if (!isset($data['show_level_0'])) {
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        if ($fil) {
            $total = $this->mongo_db->count('playbasis_summary_of_player_beta');
            $database = "playbasis_summary_of_player_beta";
        } else {
            $total = $this->mongo_db->count('playbasis_player');
            $database = "playbasis_player";
        }

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => $database,
                'pipeline' => array(
                    array(
                        '$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$gender',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'gender_id' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'gender' => -1
                        ),
                    )
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }

    public function getDonutAction($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
        }
        if (!isset($data['show_level_0'])) {
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total = $this->mongo_db->count('playbasis_summary_of_player_beta');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player_beta",
                'pipeline' => array(
                    array(
                        '$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.action_' . $res['action_id_check'] . '.action_value',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'action_value' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'action_value' => 1
                        ),
                    ),
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }

    public function getDonutReward($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
        }
        if (!isset($data['show_level_0'])) {
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total = $this->mongo_db->count('playbasis_summary_of_player_beta');

        $donut_data = $this->mongo_db->command(
            array(
                'aggregate' => "playbasis_summary_of_player_beta",
                'pipeline' => array(
                    array(
                        '$match' => $match,
                    ),
                    array(
                        '$group' => array(
                            '_id' => '$value.reward_' . $res['reward_id_check'] . '.reward_value',
                            'value' => array('$sum' => 1)
                        ),
                    ),
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'reward_value' => '$_id',
                            'value' => 1,
                        )
                    ),
                    array(
                        '$sort' => array(
                            'reward_value' => 1
                        ),
                    ),
                )
            )
        );

        $output['result'] = $donut_data['result'];
        $output['total'] = $total;
        return $output;
    }

    public function getDonutTotalPlayer($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
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

        $res = $this->filterMongoPlayer($data);

        $match = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id'])
        );

        if (isset($res['action_id_value'])) {
            $match = array_merge($match, $res['action_id_value']);
        }
        if (isset($res['action_value'])) {
            $match = array_merge($match, $res['action_value']);
        }

        if (isset($res['reward_id_value'])) {
            $match = array_merge($match, $res['reward_id_value']);
        }
        if (isset($res['reward_value'])) {
            $match = array_merge($match, $res['reward_value']);
        }

        if (isset($res['level_value'])) {
            $match = array_merge($match, $res['level_value']);
        }
        if (isset($res['exp_value'])) {
            $match = array_merge($match, $res['exp_value']);
        }
        if (isset($res['gender_value'])) {
            $match = array_merge($match, $res['gender_value']);
        }
        if (!isset($data['show_level_0'])) {
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $result = $this->mongo_db->count('playbasis_summary_of_player_beta');

        return $result;
    }

    /*public function getIsotopePlayer($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        $match = array(
            '_id.client_id' => new MongoID($data['client_id']),
            '_id.site_id' => new MongoID($data['site_id'])
        );

        if(isset($res['action_id_value'])){
            $match = array_merge($match, $res['action_id_value']);
        }
        if(isset($res['action_value'])){
            $match = array_merge($match, $res['action_value']);
        }

        if(isset($res['reward_id_value'])){
            $match = array_merge($match, $res['reward_id_value']);
        }
        if(isset($res['reward_value'])){
            $match = array_merge($match, $res['reward_value']);
        }

        if(isset($res['level_value'])){
            $match = array_merge($match, $res['level_value']);
        }
        if(isset($res['exp_value'])){
            $match = array_merge($match, $res['exp_value']);
        }
        if(isset($res['gender_value'])){
            $match = array_merge($match, $res['gender_value']);
        }
        if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $match = array_merge($match, $show_level);
        }

        $this->mongo_db->where($match);

        $total =  $this->mongo_db->count('playbasis_summary_of_player');

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
            'status'
        );

        $this->mongo_db->where($match);

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        }else{
            $this->mongo_db->order_by(array('level' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 100;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results =  $this->mongo_db->get('playbasis_summary_of_player');

        $player_data = array();
        foreach ($results as $result) {
            $player_info = $this->getPlayerById($result['_id']['pb_player_id']);
            // clean id from old system it's a mysql id generate
            unset($player_info['pb_player_id']);
            $player_data[] = array_merge($player_info,$result);
        }

        $output['result'] = $player_data;
        $output['total'] = $total;

        return $output;

    }*/

    public function getIsotopePlayer($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['sort']) && $data['sort'] == 'point') {
            $this->mongo_db->where('name', 'point');
            $results = $this->mongo_db->get("playbasis_reward");
            $point_id = ($results) ? $results[0] : null;
        }
        $this->benchmark->mark('isotope_filter_start');
        $res = $this->filterMongoPlayer($data);
        $this->benchmark->mark('isotope_filter_end');
        //echo "isotope_filter".$this->benchmark->elapsed_time('isotope_filter_start', 'isotope_filter_end');
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('first_name', $regex);
        }

        $fil = false;
        if (isset($res['action_id_value'])) {
            $this->mongo_db->where($res['action_id_value']);
            $fil = true;
        }
        if (isset($res['action_value'])) {
            $this->mongo_db->where($res['action_value']);
            $fil = true;
        }
        if (isset($res['reward_id_value'])) {
            $this->mongo_db->where($res['reward_id_value']);
            $fil = true;
        }
        if (isset($res['reward_value'])) {
            $this->mongo_db->where($res['reward_value']);
            $fil = true;
        }
        if (isset($res['level_value'])) {
            $this->mongo_db->where($res['level_value']);
            $fil = true;
        }
        if (isset($res['exp_value'])) {
            $this->mongo_db->where($res['exp_value']);
            $fil = true;
        }
        if (isset($res['gender_value'])) {
            $this->mongo_db->where($res['gender_value']);
            $fil = true;
        }
        /*if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $this->mongo_db->where($show_level);
        }*/

//        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('client_id', $data['client_id']);
//        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            } else {
                $order = 1;
            }
        } else {
            $order = 1;
        }

        $sort_data = array(
            'first_name',
            'exp',
            'level',
            'status',
            'point'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'point') {
                if ($point_id && isset($point_id['_id'])) {
                    $this->mongo_db->order_by(array('value.reward_' . $point_id['_id'] . '.reward_value' => $order));
                }
            } else {
                $this->mongo_db->order_by(array($data['sort'] => $order));
            }
            $fil = true;
        } else {
            $this->mongo_db->order_by(array('level' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 100;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        if ($fil) {
            $results = $this->mongo_db->get('playbasis_summary_of_player_beta');
        } else {
            $results = $this->mongo_db->get('playbasis_player');
        }

        $output['result'] = $results;
        $output['total'] = $this->getIsotopeTotalPlayer($data);

        return $output;

    }

    public function getIsotopeTotalPlayer($data = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $res = $this->filterMongoPlayer($data);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('first_name', $regex);
        }

        $fil = false;
        if (isset($res['action_id_value'])) {
            $this->mongo_db->where($res['action_id_value']);
            $fil = true;
        }
        if (isset($res['action_value'])) {
            $this->mongo_db->where($res['action_value']);
            $fil = true;
        }
        if (isset($res['reward_id_value'])) {
            $this->mongo_db->where($res['reward_id_value']);
            $fil = true;
        }
        if (isset($res['reward_value'])) {
            $this->mongo_db->where($res['reward_value']);
            $fil = true;
        }
        if (isset($res['level_value'])) {
            $this->mongo_db->where($res['level_value']);
            $fil = true;
        }
        if (isset($res['exp_value'])) {
            $this->mongo_db->where($res['exp_value']);
            $fil = true;
        }
        if (isset($res['gender_value'])) {
            $this->mongo_db->where($res['gender_value']);
            $fil = true;
        }
        /*if(!isset($data['show_level_0'])){
            $show_level = array('level' => array('$ne' => 0));
            $this->mongo_db->where($show_level);
        }*/

//        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('client_id', $data['client_id']);
//        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('site_id', $data['site_id']);

        if ($fil) {
            $results = $this->mongo_db->count('playbasis_summary_of_player_beta');
        } else {
            $results = $this->mongo_db->count('playbasis_player');
        }

        return $results;
    }

    public function getActionsByPlayerId($pb_player_id, &$buffer = array())
    {
        $this->load->model('Action_model');

        //$this->benchmark->mark('action_start');

        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $action = $this->mongo_db->get('playbasis_action_log');

        $action_data = array();
        foreach ($action as $a) {
            $action_id = $a['action_id'] . "";
            if (!array_key_exists($action_id, $buffer)) {
                $action_info = $this->Action_model->getAction($a['action_id']);
                $buffer[$action_id] = array(
                    'action_id' => $a['action_id'],
                    'name' => $a['action_name'],
                    'icon' => $action_info ? $action_info['icon'] : '',
                );
            }
            if (!array_key_exists($action_id, $action_data)) {
                $action_data[$action_id] = $buffer[$action_id];
            }
            if (array_key_exists('total', $action_data[$action_id])) {
                $action_data[$action_id]['total']++;
            } else {
                $action_data[$action_id]['total'] = 1;
            }
        }

        //$this->benchmark->mark('action_end');
        //echo "ActionsByPlayerId : ".$this->benchmark->elapsed_time('action_start', 'action_end');

        return $action_data;
    }

    public function getEventLog($pb_player_id, $type)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
//        $this->benchmark->mark('event_start');


        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $this->mongo_db->where('event_type', strtoupper($type));
        $event = $this->mongo_db->get('playbasis_event_log');

        $event_data = array();
        foreach ($event as $e) {
            $event_data[$e['event_type'] . ""] = array(
                'name' => $e['event_type'],
                'total' => (isset($event_data[$e['event_type']])) ? $event_data[$e['event_type']]['total'] + 1 : 1,
                'date_modified' => $this->datetimeMongotoReadable($e['date_modified']),
            );
        }

//        $this->benchmark->mark('event_end');
//
//        echo "EventLog : ".$this->benchmark->elapsed_time('event_start', 'event_end');

        return $event_data;

    }

    public function getBadgeByPlayerId($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('pb_player_id', new MongoID($data['pb_player_id']));
        $this->mongo_db->where_ne('badge_id', null);

        if (isset($data['filter_badges_id'])) {
            $this->mongo_db->where('badge_id', new MongoID($data['filter_badges_id']));
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $order = " DESC";
        } else {
            $order = " ASC";
        }

        $sort_data = array(
            'value',
            'date_modified',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('value' => $order));
        }

        $badges_data = $this->mongo_db->get('playbasis_reward_to_player');

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

    private function filterMongoPlayer($data)
    {

        $res = array();

//        $this->mongo_db->where('group',  'NONPOINT');
//        $this->mongo_db->where('name',  'badge');
//        $reward = $this->mongo_db->get("playbasis_reward");
//        $reward_badge_id = $reward ? $reward[0]["_id"] : null;

        if (!empty($data['filter_sort'])) {

            $action_id = '';
            $reward_id = '';
            foreach ($data['filter_sort'] as $filter) {
                $filter_name = $filter['name'];

                switch ($filter_name) {
                    case 'action_id':
                        $action_id_pos = strrpos($filter['value'], '-');

                        if ($action_id_pos === false) {
                            $action_id = $filter['value'];
                        } else {
                            $action_id_explode = explode('-', $filter['value']);
                            $action_id_explode = $this->check_array($action_id_explode);

                            $action_id = $action_id_explode[0];
//                            $res['action_id_value'] = array('action_id' => array('$gte' => new MongoID($action_id_explode[0]), '$lte' => new MongoID($action_id_explode[1])));
                        };

                        $res['action_id_check'] = $action_id;
                        $res['action_id_value'] = array('value.action_' . $action_id . '.action_id' => new MongoID($action_id));

                        break;
                    case 'action_value':
                        $action_pos = strrpos($filter['value'], '-');

                        if ($action_id != '') {
                            if ($action_pos === false) {
                                $res['action_value'] = array('value.action_' . $action_id . '.action_value' => (int)$filter['value']);
                            } else {
                                $action_explode = explode('-', $filter['value']);
                                $action_explode = $this->check_array($action_explode);

                                $res['action_value'] = array(
                                    'value.action_' . $action_id . '.action_value' => array(
                                        '$gte' => (int)$action_explode[0],
                                        '$lte' => (int)$action_explode[1]
                                    )
                                );
                            };
                        }

                        break;
                    case 'reward_id':
                        $reward_pos = strrpos($filter['value'], '-');

                        if ($reward_pos === false) {
//                            $res['reward_id_value'] =  array('reward_id' => new MongoID($filter['value']));
                            $reward_id = $filter['value'];
                        } else {
                            $reward_id_explode = explode('-', $filter['value']);
                            $reward_id_explode = $this->check_array($reward_id_explode);

//                            $res['reward_id_value'] =  array('reward_id' => array('$gte' => new MongoID($reward_id_explode[0]), '$lte' => new MongoID($reward_id_explode[1])));
                            $reward_id = $reward_id_explode[0];
                        };

                        $res['reward_id_check'] = $reward_id;
                        $res['reward_id_value'] = array('value.reward_' . $reward_id . '.reward_id' => new MongoID($reward_id));

                        break;
                    case 'reward_value':
                        $reward_pos = strrpos($filter['value'], '-');

                        if ($reward_id != '') {
                            if ($reward_pos === false) {
                                $res['reward_value'] = array('value.reward_' . $reward_id . '.reward_value' => (int)$filter['value']);
                            } else {
                                $reward_explode = explode('-', $filter['value']);
                                $reward_explode = $this->check_array($reward_explode);

                                $res['reward_value'] = array(
                                    'value.reward_' . $reward_id . '.reward_value' => array(
                                        '$gte' => (int)$reward_explode[0],
                                        '$lte' => (int)$reward_explode[1]
                                    )
                                );
                            };
                        }

                        break;
                    case 'level':
                        if (isset($filter['value']) && ($filter['value'] || $filter['value'] === "0")) {
                            $level_pos = strrpos($filter['value'], '-');

                            if ($level_pos === false) {
                                $res['level_value'] = array('level' => (int)$filter['value']);
                            } else {
                                $level_explode = explode('-', $filter['value']);
                                $level_explode = $this->check_array($level_explode);

                                $res['level_value'] = array(
                                    'level' => array(
                                        '$gte' => (int)$level_explode[0],
                                        '$lte' => (int)$level_explode[1]
                                    )
                                );
                            };
                        }
                        break;
                    case 'exp':
                        $exp_pos = strrpos($filter['value'], '-');

                        if ($exp_pos === false) {
                            $res['exp_value'] = array('exp' => (int)$filter['value']);
                        } else {
                            $exp_explode = explode('-', $filter['value']);
                            $exp_explode = $this->check_array($exp_explode);

                            $res['exp_value'] = array(
                                'exp' => array(
                                    '$gte' => (int)$exp_explode[0],
                                    '$lte' => (int)$exp_explode[1]
                                )
                            );
                        };

                        break;
                    case 'gender':
                        if (isset($filter['value'])) {
                            if ($filter['value'] == 'Male') {
                                $res['gender_value'] = array('gender' => 1);
                            } else {
                                if ($filter['value'] == 'Female') {
                                    $res['gender_value'] = array('gender' => 2);
                                }
                            };
                        }
                        break;
                    case 'gender_id':
                        $action_pos = strrpos($filter['value'], '-');

                        if ($action_pos === false) {
                            $res['gender_value'] = array('gender' => (int)$filter['value']);
                        } else {
                            $gender_explode = explode('-', $filter['value']);
                            $gender_explode = $this->check_array($gender_explode);

                            $res['gender_value'] = array(
                                'gender' => array(
                                    '$gte' => (int)$gender_explode[0],
                                    '$lte' => (int)$gender_explode[1]
                                )
                            );
                        };

                        break;
                    case 'gender_value':
                        if (isset($filter['value'])) {
                            if ($filter['value'] == 'm') {
                                $res['gender_value'] = array('gender' => 1);
                            } else {
                                if ($filter['value'] == 'f') {
                                    $res['gender_value'] = array('gender' => 2);
                                }
                            };
                        }
                        break;
                };
            }
        }

        return $res;
    }

    private function check_array($old_array)
    {
        $new_array = array();
        foreach ($old_array as $b) {
            if ($b) {
                array_push($new_array, $b);
            } else {
                if (is_numeric($b)) {
                    array_push($new_array, $b);
                }
            }
        }
        asort($new_array);
        return $new_array;
    }

    private function getAge($birthdate)
    {
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

    private function vsort(&$array, $key, $order = 'asc')
    {
        $res = array();
        $sort = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sort[$ii] = $va[$key];
        }
        if (strtolower($order) == 'asc') {
            asort($sort);
        } else {
            arsort($sort);
        }
        foreach ($sort as $ii => $va) {
            $res[$ii] = $array[$ii];
        }
        $array = $res;
    }

    public function getPlayerByCode($code)
    {
        $this->mongo_db->select(array(
            'client_id',
            'site_id',
            'cl_player_id',
            'email',
            'username',
            'first_name',
            'last_name'
        ));
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_player');
        return $results ? $results[0] : array();
    }

    public function getPlayerByPasswordResetCode($code)
    {
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_player_password_reset');
        return $results ? $results[0] : array();
    }

    public function getPlayerByEmailVerifyCode($code)
    {
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_player_email_verify');
        return $results ? $results[0] : array();
    }

    public function deletePasswordResetCode($code)
    {
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->delete('playbasis_player_password_reset');
        return $result;
    }

    public function deleteEmailVerifyCode($code)
    {
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->delete('playbasis_player_email_verify');
        return $result;
    }

    public function setPlayerPasswordByPlayerId($player_id, $password)
    {
        $this->mongo_db->where('_id', $player_id);
        $this->mongo_db->set('password', do_hash($password));
        $this->mongo_db->set('date_modified', new MongoDate());
        $result = $this->mongo_db->update('playbasis_player');
        return $result;
    }

    public function verifyEmailByPlayerId($player_id)
    {
        $this->mongo_db->where('_id', $player_id);
        $this->mongo_db->set('email_verify', true);
        $this->mongo_db->set('date_modified', new MongoDate());
        $result = $this->mongo_db->update('playbasis_player');
        return $result;
    }

    public function listDevices($client_id, $site_id, $pb_player_id = null, $fields = null)
    {
        if ($fields) {
            $this->mongo_db->select($fields);
        }

        if ($pb_player_id) {
            $this->mongo_db->where('pb_player_id', $pb_player_id);
        }

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
        ));
        return $this->mongo_db->get('playbasis_player_device');
    }

    public function listPlayers($pb_player_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('_id', $pb_player_id_list);
        return $this->mongo_db->get('playbasis_player');
    }

    public function listPlayersOrganize($pb_player_id_list, $fields = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->where_in('pb_player_id', $pb_player_id_list);
        return $this->mongo_db->get("playbasis_store_organize_to_player");
    }

    public function findLatestProcessActionLogTime()
    {
        $this->mongo_db->limit(1);
        return $this->mongo_db->get('playbasis_player_dau_latest');
    }


    public function listActionLog($d)
    {
        $this->mongo_db->select(array(
            'pb_player_id',
            'client_id',
            'site_id',
            'action_id',
            'date_modified',
        ));
        if ($d) {
            $this->mongo_db->where_gt('date_modified', $d);
        }
        $this->mongo_db->order_by(array('date_modified' => 'ASC'));
        return $this->mongo_db->get('playbasis_action_log', true);
    }

    public function computeDau($action, $d)
    {
        $this->mongo_db->select(array());
        $this->mongo_db->where(array(
            'pb_player_id' => $action['pb_player_id'],
            'client_id' => $action['client_id'],
            'site_id' => $action['site_id'],
            'action_id' => $action['action_id'],
            'date_added' => new MongoDate($d)
        ));
        $this->mongo_db->limit(1);
        $r = $this->mongo_db->get('playbasis_player_dau');
        if ($r) {
            $r = $r[0];
            $this->mongo_db->where(array('_id' => $r['_id']));
            $this->mongo_db->inc('count', 1);
            $this->mongo_db->update('playbasis_player_dau', array("w" => 0, "j" => false));
        } else {
            $this->mongo_db->insert('playbasis_player_dau', array(
                'pb_player_id' => $action['pb_player_id'],
                'client_id' => $action['client_id'],
                'site_id' => $action['site_id'],
                'action_id' => $action['action_id'],
                'count' => 1,
                'date_added' => new MongoDate($d)
            ), array("w" => 0, "j" => false));
        }
    }

    public function updateLatestProcessActionLogTime($d)
    {
        $this->mongo_db->limit(1);
        $r = $this->mongo_db->get('playbasis_player_dau_latest');
        if ($r) {
            $r = $r[0];
            if ($d->sec < $r['date_added']->sec) {
                return false;
            }
            $this->mongo_db->where(array('_id' => $r['_id']));
            $this->mongo_db->set('date_added', $d);
            $this->mongo_db->update('playbasis_player_dau_latest', array("w" => 0, "j" => false));
        } else {
            $this->mongo_db->insert('playbasis_player_dau_latest', array('date_added' => $d),
                array("w" => 0, "j" => false));
        }
        return true;
    }

    public function computeMau($action, $d)
    {
        $data = array();
        $end = strtotime(date('Y-m-d', strtotime('+30 day', $d)));
        $cur = $d;
        while ($cur != $end) {
            $data[] = array(
                'pb_player_id' => $action['pb_player_id'],
                'client_id' => $action['client_id'],
                'site_id' => $action['site_id'],
                'date_added' => new MongoDate($cur)
            );
            $cur = strtotime(date('Y-m-d', strtotime('+1 day', $cur)));
        }
        return $this->mongo_db->batch_insert('playbasis_player_mau', $data, array("w" => 0, "j" => false, "continueOnError" => true));
    }

    public function countActionLog($last=null)
    {
        $this->mongo_db->select(array(
            'pb_player_id',
            'client_id',
            'site_id',
            'action_id',
            'date_added',
        ));
        if ($last) {
            $this->mongo_db->where_gt('_id', $last);
        }
        return $this->mongo_db->count('playbasis_action_log');
    }

    public function streamActionLog($last, $limit=1000000, $return_cursor=false)
    {
        $this->mongo_db->select(array(
            'pb_player_id',
            'client_id',
            'site_id',
            'action_id',
            'date_added',
        ));
        if ($last) {
            $this->mongo_db->where_gt('_id', $last);
        }
        $this->mongo_db->limit($limit); // https://scalegrid.io/blog/fast-paging-with-mongodb/
        $this->mongo_db->order_by(array('date_added' => 'ASC'));
        return $this->mongo_db->get('playbasis_action_log', $return_cursor);
    }

    public function findRecentPlayers($days)
    {
        $this->set_site_mongodb(0);
        $d = strtotime("-" . $days . " day");
        $this->mongo_db->where_gt('date_added', new MongoDate($d));
        return $this->mongo_db->distinct('pb_player_id', 'playbasis_action_log');
    }

    public function findDistinctEmails($pb_player_ids)
    {
        $this->mongo_db->where_in('_id', $pb_player_ids);
        return $this->mongo_db->distinct('email', 'playbasis_player');
    }

    public function findProcessedEmails($emails)
    {
        $this->mongo_db->select(array());
        $this->mongo_db->where_in('_id', $emails);
        return $this->mongo_db->get('playbasis_player_fc');
    }

    public function findNewEmails($emails)
    {
        return array_diff($emails, array_merge(array('no-reply@playbasis.com', 'info@playbasis.com'),
            array_map('index_id', $this->findProcessedEmails($emails))));
    }

    public function findPlayersBySiteId($site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('email', 'cl_player_id', 'username'));
        $this->mongo_db->where('site_id', $site_id);
        return $this->mongo_db->get('playbasis_player');
    }

    public function getClientPlayerId($pb_player_id, $site_id)
    {
        if (!$pb_player_id) {
            return null;
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('cl_player_id'));
        $this->mongo_db->where('_id', $pb_player_id);
        $id = $this->mongo_db->get('playbasis_player');
        return ($id) ? $id[0]['cl_player_id'] : null;
    }

    public function readPlayer($id, $site_id, $fields = null)
    {
        if (!$id) {
            return array();
        }
        $this->set_site_mongodb($site_id);
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('_id', $id);
        $result = $this->mongo_db->get('playbasis_player');
        if (!$result) {
            return $result;
        }
        $result = $result[0];
        if (isset($result['date_added'])) {
            // $result['registered'] = date('Y-m-d H:i:s', $result['date_added']->sec);
            $result['registered'] = datetimeMongotoReadable($result['date_added']);
            unset($result['date_added']);
        }
        if (isset($result['birth_date']) && $result['birth_date']) {
            $result['birth_date'] = date('Y-m-d', $result['birth_date']->sec);
        }
        return $result;
    }

    public function generateCode($pb_player_id)
    {
        $code = null;
        for ($i = 0; $i < 2; $i++) {
            $code = get_random_password(8, 8, true, true);
            if (!$this->existsCode($code)) {
                break;
            }
        }
        if (!$code) {
            throw new Exception('Cannot generate unique player code');
        }
        $this->mongo_db->where('_id', $pb_player_id);
        $this->mongo_db->set('code', $code);
        $this->mongo_db->update('playbasis_player');
        return $code;
    }

    public function getMonthLeaderboardsByCustomParameter($input, $client_id, $site_id)
    {

        $rankBy = $input['param'];
        $limit = $input['limit'];
        $group_by = $input['group_by'];
        $param_str = "$" . "parameters" . "." . $rankBy;
        $group_by_str = "$" . $group_by;

        // default is present month
        if (isset($input['year']) && isset($input['month'])) {
            $selected_time = strtotime($input['year'] . "-" . $input['month']);
        } else {
            $selected_time = time();
        }

        // Aggregate the data
        $first = date('Y-m-01', $selected_time);
        $from = strtotime($first . ' 00:00:00');

        $last = date('Y-m-t', $selected_time);
        $to = strtotime($last . ' 23:59:59');
        $raw_result = $this->mongo_db->aggregate('playbasis_validated_action_log', array(
            array(
                '$match' => array(
                    'action_name' => $input['action_name'],
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to))
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array($group_by => $group_by_str),
                    $rankBy => array('$push' => $param_str)
                )
            ),
            array(
                '$sort' => array($rankBy => -1),
            ),
            array(
                '$limit' => $limit + 20,
            )
        ));
        // This function will remove the deleted player and also name key to $rankBy
        //$raw_result = $raw_result ? $this->removeDeletedPlayers($raw_result['result'], $limit, $rankBy) : array();

        // Sort the leader !
        $result = array();
        foreach ($raw_result['result'] as $key => $raw) {
            $result[$key][$group_by] = $raw['_id'][$group_by];

            $temp_name[$key] = $raw['_id'][$group_by];
            if ($input['mode'] == "sum") {
                $temp_value[$key] = array_sum($raw[$rankBy]);
            } else {
                $temp_value[$key] = count($raw[$rankBy]);
            }
            $result[$key][$rankBy] = $temp_value[$key];
        }
        if (isset($temp_value) && isset($temp_name)) {
            array_multisort($temp_value, SORT_DESC, $temp_name, SORT_ASC, $result);
        }

        return $result;
    }

    public function getMonthlyLeaderboard($ranked_by, $limit, $client_id, $site_id)
    {
        $limit = intval($limit);
        $this->set_site_mongodb($site_id);
        /* get reward_id */
        $reward_id = $this->getRewardIdByName($client_id, $site_id, $ranked_by);
        /* get latest RESET event for that reward_id (if exists) */
        $reset = $this->getResetRewardEvent($site_id, $reward_id);
        $resetTime = null;
        if ($reset) {
            $reset_time = array_values($reset);
            $resetTime = $reset_time[0]->sec;
        }
        /* list top players */
        $now = time();
        $first = date('Y-m-01', $now);
        $from = strtotime($first . ' 00:00:00');
        if ($resetTime && $resetTime > $from) {
            $from = $resetTime;
        }
        $results = $this->mongo_db->aggregate('playbasis_event_log', array(
            array(
                '$match' => array(
                    'event_type' => 'REWARD',
                    'site_id' => $site_id,
                    'reward_id' => $reward_id,
                    'date_added' => array('$gte' => new MongoDate($from)),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array('pb_player_id' => '$pb_player_id'),
                    'value' => array('$sum' => '$value')
                )
            ),
            array(
                '$sort' => array('value' => -1),
            ),
            array(
                '$limit' => $limit + 5,
            ),
        ));
        return $results ? $this->removeDeletedPlayers($results['result'], $limit, $ranked_by) : array();
    }

    private function getRewardIdByName($client_id, $site_id, $name)
    {
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where(array(
            'name' => $name,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_reward_to_client');
        return $results ? $results[0]['reward_id'] : null;
    }

    public function getResetRewardEvent($site_id, $reward_id = null)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->select(array('reward_id', 'date_added'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('event_type', 'RESET');
        if ($reward_id) {
            $this->mongo_db->where('reward_id', $reward_id);
            $this->mongo_db->limit(1);
        }
        $this->mongo_db->order_by(array('date_added' => 'DESC')); // use 'date_added' instead of '_id'
        $results = $this->mongo_db->get('playbasis_event_log');
        $ret = array();
        if ($results) {
            foreach ($results as $result) {
                $reward_id = $result['reward_id']->{'$id'};
                if (array_key_exists($reward_id, $ret)) {
                    continue;
                }
                $ret[$reward_id] = $result['date_added'];
            }
        }

        return $ret;
    }

    private function removeDeletedPlayers($results, $limit, $rankedBy)
    {
        $total = count($results);
        $c = 0;
        for ($i = 0; $i < $total; $i++) {
            if ($c < $limit) {
                $this->mongo_db->select(array('cl_player_id'));
                if (isset($results[$i]['_id']['pb_player_id'])) {
                    $results[$i]['pb_player_id'] = $results[$i]['_id']['pb_player_id'];
                    unset($results[$i]['_id']);
                }
                $this->mongo_db->where(array('_id' => $results[$i]['pb_player_id']));
                $p = $this->mongo_db->get('playbasis_player');
                if ($p) {
                    $p = $p[0];
                    $results[$i]['player_id'] = $p['cl_player_id'];
                    $results[$i][$rankedBy] = $results[$i]['value'];
                    unset($results[$i]['cl_player_id']);
                    unset($results[$i]['value']);
                    $c++;
                } else {
                    unset($results[$i]);
                }
            } else {
                unset($results[$i]);
            }
        }
        return array_values($results);
    }

    private function _new_registration_all_customers($from = null, $to = null)
    {
        $this->mongo_db->where('status', true);
        if ($from) {
            $this->mongo_db->where_gte('date_added', $this->new_mongo_date($from));
        }
        if ($to) {
            $this->mongo_db->where_lte('date_added', $this->new_mongo_date($to, '23:59:59'));
        }
        return $this->mongo_db->count('playbasis_player');
    }

    public function new_registration_all_customers($from = null, $to = null, $site_ids = array())
    {
        $this->set_site_mongodb(0);
        $n = $this->_new_registration_all_customers($from, $to);
        if (is_array($site_ids)) {
            foreach ($site_ids as $site_id) {
                $this->set_site_mongodb(new MongoId($site_id)); // set to dedicated DB (if any)
                $n += $this->_new_registration_all_customers($from, $to);
            }
        }
        return $n;
    }

    public function new_registration($data, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $action_id = $this->findAction(array_merge($data, array('action_name' => 'register')));
        if (!$action_id) {
            return array();
        }
        $match = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'action_id' => $action_id,
        );
        if (($from || $to) && !isset($match['date_added'])) {
            $match['date_added'] = array();
        }
        if ($from) {
            $match['date_added']['$gte'] = new MongoDate(strtotime($from . ' 00:00:00'));
        }
        if ($to) {
            $match['date_added']['$lte'] = new MongoDate(strtotime($to . ' 23:59:59'));
        }
        $_result = $this->mongo_db->aggregate('playbasis_player_dau', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => '$date_added', 'value' => array('$sum' => 1))
            ),
        ));
        $_result = $_result ? $_result['result'] : array();
        $result = array();
        if (is_array($_result)) {
            foreach ($_result as $key => $value) {
                array_push($result, array('_id' => date('Y-m-d', $value['_id']->sec), 'value' => $value['value']));
            }
        }
        usort($result, 'cmp1');
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

    public function findAction($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array('action_id'));
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['action_name'])
        ));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_action_to_client');
        return $result ? $result[0]['action_id'] : array();
    }

    public function daily_active_user_per_day($data, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $match = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
        );
        if (($from || $to) && !isset($match['date_added'])) {
            $match['date_added'] = array();
        }
        if ($from) {
            $match['date_added']['$gte'] = new MongoDate(strtotime($from . ' 00:00:00'));
        }
        if ($to) {
            $match['date_added']['$lte'] = new MongoDate(strtotime($to . ' 23:59:59'));
        }
        $_result = $this->mongo_db->aggregate('playbasis_player_dau', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => '$date_added', 'value' => array('$sum' => '$count'))
            ),
        ));
        $_result = $_result ? $_result['result'] : array();
        $result = array();
        if (is_array($_result)) {
            foreach ($_result as $key => $value) {
                array_push($result, array('_id' => date('Y-m-d', $value['_id']->sec), 'value' => $value['value']));
            }
        }
        usort($result, 'cmp1');
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

    public function monthy_active_user_per_day($data, $from = null, $to = null)
    {
        $this->set_site_mongodb($data['site_id']);
        $match = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
        );
        if (($from || $to) && !isset($match['date_added'])) {
            $match['date_added'] = array();
        }
        if ($from) {
            $match['date_added']['$gte'] = new MongoDate(strtotime($from . ' 00:00:00'));
        }
        if ($to) {
            $match['date_added']['$lte'] = new MongoDate(strtotime($to . ' 23:59:59'));
        }
        $_result = $this->mongo_db->aggregate('playbasis_player_mau', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => '$date_added', 'value' => array('$sum' => 1))
            ),
        ));
        $_result = $_result ? $_result['result'] : array();
        $result = array();
        if (is_array($_result)) {
            foreach ($_result as $key => $value) {
                array_push($result, array('_id' => date('Y-m-d', $value['_id']->sec), 'value' => $value['value']));
            }
        }
        usort($result, 'cmp1');
        if ($from && (!isset($result[0]['_id']) || $result[0]['_id'] != $from)) {
            array_unshift($result, array('_id' => $from, 'value' => 0));
        }
        if ($to && (!isset($result[count($result) - 1]['_id']) || $result[count($result) - 1]['_id'] != $to)) {
            array_push($result, array('_id' => $to, 'value' => 0));
        }
        return $result;
    }

    public function playerWithEnoughCriteria($data, $criteria)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id']);
        $ids = array();
        if (is_array($criteria)) {
            foreach ($criteria as $k => $v) {
                switch ($k) {
                    case 'exp':
                        if (is_array($v)) {
                            foreach ($v as $n) {
                                $query['exp'] = array('$gte' => $n);
                                break;
                            }
                        }
                        break;
                    case 'level':
                        if (is_array($v)) {
                            foreach ($v as $n) {
                                $query['level'] = array('$gte' => $n);
                                break;
                            }
                        }
                        break;
                    case 'point':
                        $reward_id = $this->get_reward_id_of_point($data);
                        if (is_array($v)) {
                            foreach ($v as $n) {
                                array_push($ids, $this->playerWithEnoughReward($data, $reward_id, $n));
                                break;
                            }
                        }
                        break;
                    case 'badge':
                        if (is_array($v)) {
                            foreach ($v as $id => $n) {
                                array_push($ids, $this->playerWithEnoughBadge($data, $id, $n));
                                break;
                            }
                        }
                        break;
                    case 'custom':
                        if (is_array($v)) {
                            foreach ($v as $id => $n) {
                                array_push($ids, $this->playerWithEnoughReward($data, $id, $n));
                                break;
                            }
                        }
                        break;
                    default:
                        /* error, not support type */
                        break;
                }
            }
        }
        //echo 'YYY'; var_dump($ids); echo 'YYY';
        $ids_intersect = null;
        if (is_array($ids)) {
            foreach ($ids as $each) {
                if ($ids_intersect == null) {
                    $ids_intersect = $each;
                } else {
                    $ids_intersect = array_intersect($ids_intersect, $each);
                }
            }
        }
        //echo 'AAA'; var_dump($ids_intersect); echo 'AAA';
        if (!empty($ids)) {
            $query['_id'] = array('$in' => $ids_intersect);
        }
        //echo 'BBB'; var_dump($query); echo 'BBB';
        $result = $this->mongo_db->command(array(
            'count' => 'playbasis_player',
            'query' => $query
        ));
        return $result['n'];
    }

    public function get_reward_id_of_point($data)
    {
        return $this->get_reward_id_by_name($data, 'point');
    }

    public function get_reward_id_by_name($data, $name)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array('client_id' => $data['client_id'], 'site_id' => $data['site_id'], 'name' => $name);
        $this->mongo_db->select(array('reward_id'));
        $this->mongo_db->where($query);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_reward_to_client');
        return $results ? $results[0]['reward_id'] : null;
    }

    public function playerWithEnoughReward($data, $reward_id, $n)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'reward_id' => $reward_id,
            'value' => array('$gte' => $n)
        );
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where($query);
        $result = array();
        $arr = $this->mongo_db->get('playbasis_reward_to_player');
        if (is_array($arr)) {
            foreach ($arr as $each) {
                array_push($result, $each['pb_player_id']);
            }
        }
        return $result;
    }

    public function playerWithEnoughBadge($data, $badge_id, $n)
    {
        $this->set_site_mongodb($data['site_id']);
        $query = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'badge_id' => $badge_id,
            'value' => array('$gte' => $n)
        );
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where($query);
        $result = array();
        $arr = $this->mongo_db->get('playbasis_reward_to_player');
        if (is_array($arr)) {
            foreach ($arr as $each) {
                array_push($result, $each['pb_player_id']);
            }
        }
        return $result;
    }

    public function getLeaderboardByLevelForReport($limit, $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('cl_player_id', 'first_name', 'last_name', 'username', 'image', 'exp', 'level'));
        $this->mongo_db->where(array(
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $this->mongo_db->order_by(array('level' => -1, 'exp' => -1));
        $this->mongo_db->limit($limit);
        return $this->mongo_db->get('playbasis_player');
    }
}

function index_id($obj)
{
    return $obj['_id'];
}

function cmp1($a, $b)
{
    if ($a['_id'] == $b['_id']) {
        return 0;
    }
    return ($a['_id'] < $b['_id']) ? -1 : 1;
}