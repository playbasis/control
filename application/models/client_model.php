<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Client_model extends MY_Model
{
    public function getClient($client_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($client_id));
        $results = $this->mongo_db->get("playbasis_client");

        return $results ? $results[0] : null;
    }

    public function getTotalClients($data){
        $this->set_site_mongodb(0);
        $this->mongo_db->where('deleted', false);

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('first_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $total = $this->mongo_db->count("playbasis_client");
        return $total;
    }

    public function getClients($data){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('first_name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $sort_data = array(
            'first_name',
            'last_name',
            'status',
            '_id'
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

        $results = $this->mongo_db->get("playbasis_client");

        return $results;
    }

    public function addClient($data) {
        $this->set_site_mongodb(0);

        $insert_data = array(
            'first_name' => isset($data['first_name'])?$data['first_name'] : '' ,
            'last_name' => isset($data['last_name'])?$data['last_name'] : '' ,
            'mobile' => isset($data['mobile'])?$data['mobile'] : '' ,
            'email' => isset($data['email'])?$data['email'] : '' ,
            'company' => isset($data['company'])?$data['company'] : '' ,
            'status' => (bool)$data['status'],
            'deleted' => false,
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
        );

        if (isset($data['image'])) {
            $insert_data['image'] = html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8');
        }

        return $this->mongo_db->insert('playbasis_client', $insert_data);
    }

    public function editClient($client_id, $data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($client_id));
        $this->mongo_db->set('first_name', $data['first_name']);
        $this->mongo_db->set('last_name', $data['last_name']);
        $this->mongo_db->set('mobile', $data['mobile']);
        $this->mongo_db->set('email', $data['email']);
        $this->mongo_db->set('company', isset($data['company'])?$data['company'] : '');
        $this->mongo_db->set('status', (bool)$data['status']);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }

        $this->mongo_db->update('playbasis_client');

        if (isset($data['domain_value'])) {
            foreach ($data['domain_value'] as $domain_value) {

                $this->mongo_db->where('_id',  new MongoID($domain_value['site_id']));
                $this->mongo_db->set('status', (bool)$domain_value['status']);
                $this->mongo_db->set('limit_users', $domain_value['limit_users']);
                if($domain_value['domain_start_date']){
                    $this->mongo_db->set('date_start', new MongoDate(strtotime($domain_value['domain_start_date'])));
                }
                if($domain_value['domain_expire_date']){
                    $this->mongo_db->set('date_expire', new MongoDate(strtotime($domain_value['domain_expire_date'])));
                }
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                $this->mongo_db->update('playbasis_client_site');

                $data_filter = array(
                    'client_id' => $client_id,
                    'site_id' => $domain_value['site_id'],
                    'plan_id' => $domain_value['plan_id'],
                    'status' => (bool)$domain_value['status'],
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                );

                $this->addPlanToPermission($data_filter);
                $this->copyRewardToClient($data_filter);
                $this->copyFeaturedToClient($data_filter);
                $this->copyActionToClient($data_filter);
                $this->copyJigsawToClient($data_filter);

            }
        }

        if (isset($data['user_value'])) {

            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->delete_all('user_to_client');

            foreach ($data['user_value'] as $user_value) {

                $this->mongo_db->where('client_id', new MongoID($user_value['user_id']));
                $this->mongo_db->set('user_group_id',  new MongoID($user_value['user_group_id']));
                $this->mongo_db->set('status',  (bool)$user_value['status']);
                $this->mongo_db->update('user');

                $data_insert = array(
                    'client_id' => new MongoID($client_id),
                    'user_id' => new MongoID($user_value['user_id']),
                    'status' => (bool)$user_value['status']
                );
                $this->mongo_db->insert('user_to_client', $data_insert);
            }
        }
    }



    public function deleteClient($client_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($client_id));
        $this->mongo_db->set('status', (bool)false);
        $this->mongo_db->set('deleted', (bool)true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_client');
    }

    /****start Dupicate with another model but in codeigniter cannot load another model within model ****/
    public function addPlanToPermission($data){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->delete('playbasis_permission');

        $data_insert = array(
            'plan_id' =>  new MongoID($data['plan_id']),
            'client_id' =>  new MongoID($data['client_id']),
            'site_id' => new MongoID($data['site_id']),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
        );

        return $this->mongo_db->insert('playbasis_permission', $data_insert);
    }

    public function getPlan($plan_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($plan_id));
        $results = $this->mongo_db->get("playbasis_plan");

        return $results ? $results[0] : null;
    }

    public function getReward($reward_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($reward_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_reward");

        return $results ? $results[0] : null;
    }

    public function getFeature($feature_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($feature_id));
        $this->mongo_db->order_by(array('sort_order' => 1));
        $results = $this->mongo_db->get("playbasis_feature");

        return $results ? $results[0] : null;
    }

    public function getAction($action_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results ? $results[0] : null;
    }
    /****end Dupicate with another model but in codeigniter cannot load another model within model ****/

    public function getJigsaw($jigsaw_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($jigsaw_id));
        $results = $this->mongo_db->get("playbasis_jigsaw");

        return $results ? $results[0] : null;
    }

    public function copyRewardToClient($data_filter){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->where('is_custom', false);
        $this->mongo_db->delete_all("playbasis_reward_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if ($plan_data['reward_to_plan']) {
            foreach ($plan_data['reward_to_plan'] as $reward) {
                $limit = empty($reward['limit'])? "NULL": (int)$reward['limit'];

                $reward_data = $this->getReward($reward['reward_id']);

                $insert_data = array(
                    'reward_id' => new MongoID($reward['reward_id']) ,
                    'client_id' => new MongoID($data_filter['client_id']) ,
                    'site_id' => new MongoID($data_filter['site_id']) ,
                    'group' => $reward_data['group'] ,
                    'name' => $reward_data['name'] ,
                    'description' => $reward_data['description'] ,
                    'init_dataset' => $reward_data['init_dataset'],
                    'limit' => $limit,
                    'sort_order' => $reward_data['sort_order'],
                    'status' =>  (bool)$reward_data['status'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'is_custom' => false,
                );

                $this->mongo_db->insert('playbasis_reward_to_client', $insert_data);
            }
        }
    }

    public function copyFeaturedToClient($data_filter){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->delete_all("playbasis_feature_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);


        if ($plan_data['feature_to_plan']) {
            foreach ($plan_data['feature_to_plan'] as $feature_id) {

                $feature_data = $this->getFeature($feature_id);

                $insert_data = array(
                    'feature_id' => new MongoID($feature_id) ,
                    'client_id' => new MongoID($data_filter['client_id']) ,
                    'site_id' => new MongoID($data_filter['site_id']) ,
                    'name' => $feature_data['name'] ,
                    'description' => $feature_data['description'] ,
                    'sort_order' => $feature_data['sort_order'],
                    'status' =>  (bool)$feature_data['status'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'link' => $feature_data['link'],
                    'icon' => $feature_data['icon']
                );

                $this->mongo_db->insert('playbasis_feature_to_client', $insert_data);
            }
        }
    }

    public function copyActionToClient($data_filter){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->delete_all("playbasis_action_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if ($plan_data['action_to_plan']) {
            foreach ($plan_data['action_to_plan'] as $action_id) {

                $action_data = $this->getAction($action_id);

                $insert_data = array(
                    'action_id' => new MongoID($action_id) ,
                    'client_id' => new MongoID($data_filter['client_id']) ,
                    'site_id' => new MongoID($data_filter['site_id']) ,
                    'name' => $action_data['name'] ,
                    'description' => $action_data['description'] ,
                    'icon' => $action_data['icon'],
                    'color' => $action_data['color'],
                    'init_dataset' => $action_data['init_dataset'],
                    'sort_order' => $action_data['sort_order'],
                    'status' =>  (bool)$action_data['status'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                );

                $this->mongo_db->insert('playbasis_action_to_client', $insert_data);
            }
        }
    }

    public function copyJigsawToClient($data_filter){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id', new MongoID($data_filter['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data_filter['site_id']));
        $this->mongo_db->delete_all("playbasis_game_jigsaw_to_client");

        $plan_data = $this->getPlan($data_filter['plan_id']);

        if ($plan_data['jigsaw_to_plan']) {
            foreach ($plan_data['jigsaw_to_plan'] as $jigsaw_id) {

                $jigsaw_data = $this->getJigsaw($jigsaw_id);

                $insert_data = array(
                    'jigsaw_id' => new MongoID($jigsaw_id) ,
                    'client_id' => new MongoID($data_filter['client_id']) ,
                    'site_id' => new MongoID($data_filter['site_id']) ,
                    'name' => $jigsaw_data['name'] ,
                    'description' => $jigsaw_data['description'] ,
                    'category' => $jigsaw_data['category'],
                    'class_path' => $jigsaw_data['class_path'],
                    'init_dataset' => $jigsaw_data['init_dataset'],
                    'sort_order' => $jigsaw_data['sort_order'],
                    'status' =>  (bool)$jigsaw_data['status'],
                    'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                    'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                );

                $this->mongo_db->insert('playbasis_game_jigsaw_to_client', $insert_data);
            }
        }
    }

    public function insertClient(){
        $this->set_site_mongodb(0);

        $data = $this->input->post();

        $data_insert_client = array(
            'first_name'=>$data['firstname'],
            'last_name'=>$data['lastname'],
            'mobile'=>'',
            'email'=>$data['email'],
            'company'=>null,
            'image'=>isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            'status'=>true,
            'deleted'=>false,
            'date_added'=> new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_modified'=>''
        );

        $this->mongo_db->insert('playbasis_client', $data_insert_client);

        return $data_inserted_client_id = $data_insert_client['_id'];
    }

    public function editClientPlan($client_id, $data){
        $this->set_site_mongodb(0);
        
        if (isset($data['domain_value'])) {
            $data_filter = array(
                'client_id' => $client_id,
                'site_id' => $data['domain_value']['site_id'],
                'plan_id' => $data['domain_value']['plan_id'],
                'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
            );

            if(isset($data['domain_value']['status'])){
                $data_filter['status'] = $data['domain_value']['status'];
            }

//                $this->addPlanToPermission($data_filter);
            $this->copyRewardToClient($data_filter);
            $this->copyFeaturedToClient($data_filter);
            $this->copyActionToClient($data_filter);
            $this->copyJigsawToClient($data_filter);

        }
    }

}
?>