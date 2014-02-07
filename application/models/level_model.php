<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Level_model extends MY_Model
{
    public function getTotalLevels($data) {

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->count("playbasis_exp_table");

        return $results;
    }

    public function getLevel($level_id) {

        $this->mongo_db->where('_id',  new MongoID($level_id));
        $results = $this->mongo_db->get("playbasis_exp_table");

        return $results ? $results[0] : null;
    }

    public function getLevelSite($level_id) {

        $this->mongo_db->where('_id',  new MongoID($level_id));
        $results = $this->mongo_db->get("playbasis_client_exp_table");

        return $results ? $results[0] : null;
    }

    public function getLevels($data) {

        $level_data = array();

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
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
            'level_title',
            'level',
            'status',
            'sort_order'
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

        $level_data =  $this->mongo_db->get('playbasis_exp_table');

//        foreach ($results as $result) {
//            $level_data[] = array(
//                'level_id' => $result['_id'],
//                'level' => $result['level'],
//                'title' => $result['level_title'],
//                'exp' => number_format($result['exp'], 0),
//                'status' => $result['status'],
//                'sort_order' => $result['sort_order']
//            );
//        }

        return $level_data;
    }

    public function getTotalLevelsSite($data) {

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->count("playbasis_client_exp_table");

        return $results;
    }

    public function getLevelsSite($data) {

        $level_data = array();

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
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
            'level_title',
            'level',
            'status',
            'sort_order'
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

        $level_data =  $this->mongo_db->get('playbasis_client_exp_table');

//        foreach ($results as $result) {
//            $level_data[] = array(
//                'level_id' => $result['_id'],
//                'level' => $result['level'],
//                'title' => $result['level_title'],
//                'exp' => number_format($result['exp'], 0),
//                'status' => $result['status'],
//                'sort_order' => $result['sort_order']
//            );
//        }

        return $level_data;
    }

    public function addLevel($data) {

        $data_insert = array(
            'level_title' => $data['level_title']|'' ,
            'level' => (int)$data['level']|0,
            'exp' => (int)$data['exp']|0 ,
            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            // 'tags' => $data['tags']|'' ,
            'tags' => (isset($data['tags']))?$data['tags']:0,
            'status' => (bool)$data['status'],
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
        );

        $exp_id = $this->mongo_db->insert('playbasis_exp_table', $data_insert);
        return $exp_id;
    }

    public function addLevelSite($data) {

        if(isset($data['level'])){
            $toInsertLevel = $data['level'];

            $exists = $this->checkLevelExists($data);

            if(!$exists){
                $this->mongo_db->where('client_id', new MongoID($data['client_id']));
                $this->mongo_db->where('site_id', new MongoID($data['site_id']));
                $this->mongo_db->where_lt('level', floatval($toInsertLevel));
                $this->mongo_db->order_by(array('level' => 'DESC'));
                $lowerLevels = $this->mongo_db->get('playbasis_client_exp_table');
                $nextLowerLevel = isset($lowerLevels[0])?$lowerLevels[0]:null;

                $this->mongo_db->where('client_id', new MongoID($data['client_id']));
                $this->mongo_db->where('site_id', new MongoID($data['site_id']));
                $this->mongo_db->where_gt('level', floatval($toInsertLevel));
                $this->mongo_db->order_by(array('level' => 'ASC'));
                $higerLevels = $this->mongo_db->get('playbasis_client_exp_table');
                $nextHigherLevel = isset($higerLevels[0])?$higerLevels[0]:null;

                if(!$nextLowerLevel && $nextHigherLevel){
                    if($data['exp']<$nextHigherLevel['exp']){
                        $data_insert = array(
                            'client_id' => new MongoID($data['client_id']),
                            'site_id' => new MongoID($data['site_id']),
                            'level_title' => $data['level_title']|'' ,
                            'level' => (int)$data['level'],
                            'exp' => (int)$data['exp']|0 ,
                            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
                            // 'tags' => $data['tags']|'' ,
                            'tags' => (isset($data['tags']))?$data['tags']:0,
                            'status' => (bool)$data['status'],
                            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                        );
                        $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                        return $exp_id;
                    }else{
                        return false;
                    }
                }

                if($nextLowerLevel && !$nextHigherLevel){
                    if($data['exp']>$nextLowerLevel['exp']){
                        $data_insert = array(
                            'client_id' => new MongoID($data['client_id']),
                            'site_id' => new MongoID($data['site_id']),
                            'level_title' => $data['level_title']|'' ,
                            'level' => (int)$data['level'],
                            'exp' => (int)$data['exp']|0 ,
                            'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
                            // 'tags' => $data['tags']|'' ,
                            'tags' => (isset($data['tags']))?$data['tags']:0,
                            'status' => (bool)$data['status'],
                            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                        );
                        $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                        return $exp_id;
                    }else{
                        return false;
                    }
                }

                if($data['exp']>$nextLowerLevel['exp'] && $data['exp']<$nextHigherLevel['exp']){
                    $data_insert = array(
                        'client_id' => new MongoID($data['client_id']),
                        'site_id' => new MongoID($data['site_id']),
                        'level_title' => $data['level_title']|'' ,
                        'level' => (int)$data['level'],
                        'exp' => (int)$data['exp']|0 ,
                        'image'=> isset($data['image'])? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
                        // 'tags' => $data['tags']|'' ,
                        'tags' => (isset($data['tags']))?$data['tags']:0,
                        'status' => (bool)$data['status'],
                        'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                        'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                    );
                    $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                    return $exp_id;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }
    }

    public function editLevel($level_id, $data) {

        $this->mongo_db->where('_id',  new MongoID($level_id));
        if(isset($data['level_title'])){
            $this->mongo_db->set('level_title', $data['level_title']);
        }
        if(isset($data['level'])){
            $this->mongo_db->set('level', (int)$data['level']);
        }
        if(isset($data['exp'])){
            $this->mongo_db->set('exp', (int)$data['exp']);
        }
        if(isset($data['image'])){
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }
        if(isset($data['tags'])){
            $this->mongo_db->set('tags', $data['tags']);
        }
        if(isset($data['status'])){
            $this->mongo_db->set('status', (bool)$data['status']);
        }
        if(isset($data['sort_order'])){
            $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        }
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_exp_table');
    }

    public function editLevelSite($level_id, $data) {

        $this->mongo_db->where('_id',  new MongoID($level_id));
        if(isset($data['level_title'])){
            $this->mongo_db->set('level_title', $data['level_title']);
        }
        if(isset($data['level'])){
            $this->mongo_db->set('level', (int)$data['level']);
        }
        if(isset($data['exp'])){
            $this->mongo_db->set('exp', (int)$data['exp']);
        }
        if(isset($data['image'])){
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }
        if(isset($data['tags'])){
            $this->mongo_db->set('tags', $data['tags']);
        }
        if(isset($data['status'])){
            $this->mongo_db->set('status', (bool)$data['status']);
        }
        if(isset($data['sort_order'])){
            $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        }
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_client_exp_table');
    }

    public function deleteLevel($level_id) {

        $this->mongo_db->where('_id', new MongoID($level_id));
        $this->mongo_db->delete('playbasis_exp_table');
    }

    public function deleteLevelSite($level_id){

        $this->mongo_db->where('_id',  new MongoID($level_id));
        $this->mongo_db->delete('playbasis_client_exp_table');
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

    public function checkLevelExists($data){
        $toInsertLevel = $data['level'];
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('level', (int)$toInsertLevel);
        $check = $this->mongo_db->get('playbasis_client_exp_table');

        if($check){
            echo "TRUE";
            return true;
        }else{
            echo "FALSE";
            return false;
        }
    }
}
?>