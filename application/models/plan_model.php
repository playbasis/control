<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Plan_model extends MY_Model
{
    public function getPlan($plan_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_plan");

        return $results ? $results[0] : null;
    }

    public function getPlans($data = array()) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $plans = $this->mongo_db->get("playbasis_plan");

        return $plans;
    }

    public function getAvailablePlans(){
        $this->set_site_mongodb($this->session->userdata('site_id'));        
        $plans = $this->mongo_db->get("playbasis_plan");

        foreach($plans as $key => $plan){
            $planId = $plan['_id'];
            $count = 1;
            $this->mongo_db->where('plan_id', new MongoId($planId));
            $permissions = $this->mongo_db->get('playbasis_permission');
            $planLimit = !empty($plan['limit_num_client'])?$plan['limit_num_client']:null;
            if(!empty($planLimit)){
                foreach ($permissions as $permission) {
                    $clientId = $permission['client_id'];
                    $this->mongo_db->where('client_id', new MongoId($clientId));
                    $client = $this->mongo_db->get('playbasis_client_site');
                    if (!empty($client)){
                        $theClient = $client[0];
                        if($theClient['date_expire'] > new MongoDate()){
                            $count ++;
                        }
                    }
                }    
                if($planLimit < $count){
                    unset($plans[$key]);
                }
            }
        }
        return $plans;
    }

    public function getTotalPlans($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_plan");

        return $total;
    }

    public function getClientByPlan($plan_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('plan_id',  new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_permission");

        return $results;
    }

    public function getClientByPlanOnlyClient($plan_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('client_id'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('plan_id',  new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_permission");

        $client_id_list = array();
        foreach($results as $r){
            $client_id_list[$r['client_id'].""] = $r;
        }
        return $client_id_list;
    }

    public function getFeatures($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $feaures_data = $this->mongo_db->get("playbasis_feature");

        return $feaures_data;
    }

    public function getActions($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $actions_data = $this->mongo_db->get("playbasis_action");

        return $actions_data;
    }

    public function getJigsaws($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $jigsaws_data = $this->mongo_db->get("playbasis_jigsaw");

        return $jigsaws_data;
    }

    public function getRewards($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sort_data = array(
            '_id',
            'name',
            'status',
            'sort_order'
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $rewards_data = $this->mongo_db->get("playbasis_reward");

        return $rewards_data;
    }

    public function addPlan($data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if(isset($data['sort_order'])){
            $sort_order = (int)$data['sort_order'];
        }else{
            $sort_order = 0;
        }

        $dinsert = array(
            'name' => $data['name']|'' ,
            'description' => $data['description']|'',
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'status' => (bool)$data['status'],
            // 'sort_order' => (int)$data['sort_order']|1
            'sort_order' => $sort_order
        );
        if (isset($data['limit_num_client']) && !empty($data['limit_num_client'])){
            $dinsert['limit_num_client'] = new MongoInt32($data['limit_num_client']);
        }else{
            $dinsert['limit_num_client'] = null;
        }   

        if (isset($data['feature_data'])) {
            $feature = array();
            foreach ($data['feature_data'] as $feature_value) {
                array_push($feature, new MongoId($feature_value));
            }
            $dinsert['feature_to_plan'] = $feature;
        }
        if (isset($data['action_data'])) {
            $action = array();
            foreach ($data['action_data'] as $action_value) {
                array_push($action, new MongoId($action_value));
            }
            $dinsert['action_to_plan'] = $action;
        }
        if (isset($data['jigsaw_data'])) {
            $jigsaw = array();
            foreach ($data['jigsaw_data'] as $jigsaw_value) {
                array_push($jigsaw, new MongoId($jigsaw_value));
            }
            $dinsert['jigsaw_to_plan'] = $jigsaw;
        }
        if (isset($data['reward_data'])) {
            $reward = array();
            foreach ($data['reward_data'] as $reward_value => $value) {
                $arr_val = array(
                    'reward_id' => new MongoId($value['reward_id']),
                    'limit' => (isset($value['limit']) && $value['limit'] != '')? new MongoInt32($value['limit']) : null
                ) ;
                array_push($reward, $arr_val);
            }
            $dinsert['reward_to_plan'] = $reward;
        }
        $p = $this->mongo_db->insert('playbasis_plan', $dinsert);
    }

    public function editPlan($plan_id, $data) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($plan_id));
        $this->mongo_db->set('name', $data['name']);
        $this->mongo_db->set('description', $data['description']);
        $this->mongo_db->set('limit_num_client', !empty($data['limit_num_client'])?new MongoInt32($data['limit_num_client']):null);
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        if (isset($data['feature_data'])) {
            $feature = array();
            foreach ($data['feature_data'] as $feature_value) {
                array_push($feature, new MongoId($feature_value));
            }
            $this->mongo_db->set('feature_to_plan', $feature);
        }else{
            $feature = array();
            $this->mongo_db->set('feature_to_plan', $feature);
        }
        if (isset($data['action_data'])) {
            $action = array();
            foreach ($data['action_data'] as $action_value) {
                array_push($action, new MongoId($action_value));
            }
            $this->mongo_db->set('action_to_plan', $action);
        }else{
            $action = array();
            $this->mongo_db->set('action_to_plan', $action);
        }
        if (isset($data['jigsaw_data'])) {
            $jigsaw = array();
            foreach ($data['jigsaw_data'] as $jigsaw_value) {
                array_push($jigsaw, new MongoId($jigsaw_value));
            }
            $this->mongo_db->set('jigsaw_to_plan', $jigsaw);
        }else{
            $jigsaw = array();
            $this->mongo_db->set('jigsaw_to_plan', $jigsaw);
        }
        if (isset($data['reward_data'])) {
            $reward = array();
            foreach ($data['reward_data'] as $reward_value => $value) {
                $arr_val = array(
                    'reward_id' => new MongoId($value['reward_id']),
                    'limit' => (isset($value['limit']) && $value['limit'] != '')? new MongoInt32($value['limit']) : null
                ) ;
                array_push($reward, $arr_val);
            }
            $this->mongo_db->set('reward_to_plan', $reward);
        }
        if (isset($data['limit_noti'])) {
            $limit_noti = array();
            foreach ($data['limit_noti'] as $key => $value) {
                $limit_noti[$key] = $value['limit'];
            }
            $this->mongo_db->set('limit_notifications', $limit_noti);
        }
        if (isset($data['limit_req'])) {
            $limit_req = array();
            for ($i=0; $i<sizeof($data['limit_req']); $i++) {
                $item = $data['limit_req'][$i];
                if (!$item['field']) continue;
                // strip only first path of the api and lowercase
                $item['field'] = strtolower(preg_replace(
                    "/(\w+)\/.*/", '${1}',
                    $item['field']));
                if (substr($item['field'], 0, 1) != "/") {
                    $item['field'] = "/".$item['field'];
                }
                $limit_req[$item['field']] = $item['limit'];
            }
            $this->mongo_db->set('limit_requests', $limit_req);
        }
        $this->mongo_db->update('playbasis_plan');

    }

    public function deletePlan($plan_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($plan_id));
        $this->mongo_db->delete('playbasis_plan');

    }

    public function getPlanID($name){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('name', $name);
        $results =  $this->mongo_db->get('playbasis_plan');
        return $results ? $results[0]['_id'] : null;
    }

    public function checkPlanExistsByName($plan_name){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('name'));
        $plan_names = $this->mongo_db->get('playbasis_plan');

        foreach($plan_names as $plan){
            if(trim(strtolower($plan['name'])) == strtolower($plan_name)){
                return true;
            }
        }

        return false;

    }
}
?>
