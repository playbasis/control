<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_Model
{
    public function getAction($action_id) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id',  new MongoID($action_id));
        $results = $this->mongo_db->get("playbasis_action");

        return $results ? $results[0] : null;
    }

    public function getActions($data = null){
        $this->set_site_mongodb(0);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
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

        $results = $this->mongo_db->get("playbasis_action");

        return $results;
    }

    public function getTotalActions(){
        $this->set_site_mongodb(0);

        $results = $this->mongo_db->count("playbasis_action");

        return $results;
    }

    public function getTotalActionReport($data) {

        $this->set_site_mongodb(0);

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['action_id']) && $data['action_id'] != 0) {
            $this->mongo_db->where('action_id',  new MongoID($data['action_id']));
        }

        $results = $this->mongo_db->count("playbasis_action_log");

        return $results;

    }

     public function getActionClient($data) {
         $this->set_site_mongodb(0);

         $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status','date_added','date_modified'));

         $this->mongo_db->where('client_id',  new MongoID($data['client_id']));

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

         $results = $this->mongo_db->get("playbasis_action_to_client");

         $actions = array();
         $tmp = array();
         foreach ($results as $result) {
             if (!in_array($result['action_id'], $tmp)) {
                 $a = $result;
                 $actions[] = $a;
                 $tmp[] = $result['action_id'];
             }
         }

         return $actions;
     }

    //dupicate function just count on getActionClient
    /*public function getTotalActionClient($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->get("playbasis_action_to_client");

        $actions = array();
        $tmp = array();
        foreach ($results as $result) {
            if (!in_array($result['action_id'], $tmp)) {
                $a = $result;
                $actions[] = $a;
                $tmp[] = $result['action_id'];
            }
        }

        return count($actions);
    }*/

    public function getActionSite($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('action_id','name','description','icon','color','sort_order','status','date_added','date_modified'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

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

        return $this->mongo_db->get("playbasis_action_to_client");
    }

    public function getTotalActionSite($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
            $this->mongo_db->where('name', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        return $this->mongo_db->count("playbasis_action_to_client");
    }

    public function getActionReport($data) {

        $this->set_site_mongodb(0);

        if (isset($data['username']) && $data['username'] != '') {
            $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
            $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
            $regex = new MongoRegex("/".utf8_strtolower($data['username'])."/i");
            $this->mongo_db->where('username', $regex);
            $users = $this->mongo_db->get("playbasis_player");

            $user_id =array();
            foreach($users as $u){
                $user_id[] = $u["_id"];
            }

            $this->mongo_db->where_in('pb_player_id',  $user_id);
        }

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['date_start']) && $data['date_start'] != '' && isset($data['date_expire']) && $data['date_expire'] != '' ) {
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime($data['date_start'])), '$lte' => new MongoDate(strtotime($data['date_expire']))));
        }

        if (isset($data['action_id']) && $data['action_id'] != 0) {
            $this->mongo_db->where('action_id',  new MongoID($data['action_id']));
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

        $results = $this->mongo_db->get("playbasis_action_log");

        return $results;

    }

    public function getAllIcons(){

        $handle = fopen(base_url('stylesheet/custom/font-awesome.css'), 'r');
        $all_icons = array();

        if($handle){
            while(($line = fgets($handle)) != false){
                $fa_line = substr($line, 0, 3);
                if($fa_line == ".fa"){
                    $temp = explode(":b", $line);
                    if((substr($temp[0], 1) != 'fa-icon-large')){
                        $all_icons[] = substr($temp[0], 1);    
                    }
                }
            }
        }else{
            echo "File of the font-awesome.css not found";
        }

        sort($all_icons);
        return $all_icons;
    }

    public function addAction($data){
        $this->set_site_mongodb(0);

        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $temp[0] = (object)array(
            'param_name' => 'url',
             'label' => 'URL or filter String',
             'placeholder' => '',
             'sortOrder' => $data['sort'],
             'field_type' => 'text',
             'value' => ''
            );
        $temp[1] = (object)array(
            'param_name' => 'regex',
             'label' => 'Regex',
             'placeholder' => '',
             'sortOrder' => $data['sort'],
             'field_type' => 'boolean',
             'value' => false,
            );

        $init_dataset = serialize($temp);

        $data_insert = array(
                'name'=>utf8_strtolower($data['name']),
                'description'=>$data['description'],
                'icon'=>$data['icon'],
                'color'=>$data['color'],
                'sort_order'=>$data['sort'],
                'status'=>$data['status'],
                'init_dataset'=>$init_dataset,
                'date_added'=>$date_added,
                'date_modified'=>$date_modified
            );

        return $this->mongo_db->insert('playbasis_action', $data_insert);

    }

    public function addActionToClient($data){
        $this->set_site_mongodb(0);

        $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $date_modified = new MongoDate(strtotime(date("Y-m-d H:i:s")));

        $temp[0] = (object)array(
            'param_name' => 'url',
             'label' => 'URL or filter String',
             'placeholder' => '',
             'sortOrder' => $data['sort'],
             'field_type' => 'text',
             'value' => ''
            );
        $temp[1] = (object)array(
            'param_name' => 'regex',
             'label' => 'Regex',
             'placeholder' => '',
             'sortOrder' => $data['sort'],
             'field_type' => 'boolean',
             'value' => false
            );

        $init_dataset = serialize($temp);

        $data_insert = array(
                'action_id'=>new MongoID($data['action_id']),
                'client_id'=>new MongoID($data['client_id']),
                'site_id'=>new MongoID($data['site_id']),
                'name'=>utf8_strtolower($data['name']),
                'description'=>$data['description'],
                'icon'=>$data['icon'],
                'color'=>$data['color'],
                'init_dataset'=>$init_dataset,
                'sort_order'=>$data['sort'],
                'status'=>$data['status'],
                'date_added'=>$date_added,
                'date_modified'=>$date_modified
            );

        $this->mongo_db->insert('playbasis_action_to_client', $data_insert);
    }

    public function delete($action_id){
        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($action_id));
        $this->mongo_db->delete('playbasis_action');

        $this->mongo_db->where('action_id', new MongoID($action_id));
        $this->mongo_db->delete('playbasis_action_to_client');
    }

    public function editAction($action_id, $data){

        $this->set_site_mongodb(0);

        $this->mongo_db->where('_id', new MongoID($action_id));

        if(isset($data['name']) && !is_null($data['name'])){
            $this->mongo_db->set('name', utf8_strtolower($data['name']));
        }

        if(isset($data['description']) && !is_null($data['description'])){
            $this->mongo_db->set('description', $data['description']);
        }        

        if(isset($data['status']) && !is_null($data['status'])){
            $this->mongo_db->set('status', $data['status']);
        }

        if(isset($data['sort']) && !is_null($data['sort'])){
            $this->mongo_db->set('sort_order', $data['sort']);
        }

        if(isset($data['icon']) && !is_null($data['icon'])){
            $this->mongo_db->set('icon', $data['icon']);
        }

        if(isset($data['color']) && !is_null($data['color'])){
            $this->mongo_db->set('color', $data['color']);
        }

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_action');

    }

    public function editActionToClient($action_id, $data){
        $this->set_site_mongodb(0);

         $this->mongo_db->where('action_id', new MongoID($action_id));

        if(isset($data['name']) && !is_null($data['name'])){
            $this->mongo_db->set('name', utf8_strtolower($data['name']));
        }

        if(isset($data['description']) && !is_null($data['description'])){
            $this->mongo_db->set('description', $data['description']);
        }

        if(isset($data['sort']) && !is_null($data['sort'])){
            $this->mongo_db->set('sort_order', $data['sort']);
        }

        if(isset($data['icon']) && !is_null($data['icon'])){
            $this->mongo_db->set('icon', $data['icon']);
        }

        if(isset($data['color']) && !is_null($data['color'])){
            $this->mongo_db->set('color', $data['color']);
        }

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_action_to_client');
    }

    public function checkActionExists($data){

        $this->set_site_mongodb(0);
        
        $this->mongo_db->where('name', utf8_strtolower($data['name']));
        return $this->mongo_db->get('playbasis_action');

    }

    public function checkActionClientExists($data){

        $this->set_site_mongodb(0);

        $this->mongo_db->where('name', utf8_strtolower($data['name']));
        return $this->mongo_db->get('playbasis_action_to_client');

    }
}
?>