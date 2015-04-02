<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jive_model extends MY_Model{
    public function hasValidRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_jive_to_client") > 0;
    }

    public function getJiveRegistration($site_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('jive_tenant_id', 'jive_client_id', 'jive_client_secret', 'jive_url'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_jive_to_client");
        return $results ? $results[0] : null;
    }

    public function getTotalQuizs($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted',  false);
        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".preg_quote(utf8_strtolower($data['filter_name']))."/i");
            $this->mongo_db->where('name', $regex);
        }

        $results = $this->mongo_db->count("playbasis_quiz_to_client");

        return $results;
    }

    public function addQuizToClient($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if(isset($data['date_start'])) {
            $data['date_start'] = $data['date_start'] ? new MongoDate(strtotime($data['date_start'])) : null;
        }
        if(isset($data['date_expire'])) {
            $data['date_expire'] = $data['date_expire'] ? new MongoDate(strtotime($data['date_expire'])) : null;
        }

        $data = array_merge($data, array(
                'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'deleted' => false
            )
        );

        return $this->mongo_db->insert('playbasis_quiz_to_client', $data);
    }

    public function editQuizToClient($quiz_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quiz_id));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if(isset($data['name']) && !is_null($data['name'])){
            $this->mongo_db->set('name', $data['name']);
        }
        if(isset($data['description']) && !is_null($data['description'])){
            $this->mongo_db->set('description', $data['description']);
        }

        if(isset($data['weight']) && !is_null($data['weight'])){
            $this->mongo_db->set('weight', $data['weight']);
        }

        if(isset($data['image']) && !is_null($data['image'])){
            $this->mongo_db->set('image', $data['image']);
        }

        if(isset($data['description_image']) && !is_null($data['description_image'])){
            $this->mongo_db->set('description_image', $data['description_image']);
        }

        if(isset($data['status'])){
            $this->mongo_db->set('status', (boolean)$data['status']);
        }

        if(isset($data['date_start'])){
            if($data['date_start'] != ''){
                $this->mongo_db->set('date_start', new MongoDate(strtotime($data['date_start'])));
            }else{
                $this->mongo_db->set('date_start', null);
            }
        }

        if(isset($data['date_expire'])){
            if($data['date_expire']  != ''){
                $this->mongo_db->set('date_expire', new MongoDate(strtotime($data['date_expire'])));
            }else{
                $this->mongo_db->set('date_expire', null);
            }
        }

        if(isset($data['grades']) && !is_null($data['grades'])){
            $this->mongo_db->set('grades', $data['grades']);
        }

        if(isset($data['questions']) && !is_null($data['questions'])){
            $this->mongo_db->set('questions', $data['questions']);
        }

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_quiz_to_client');

        return true;
    }

    public function delete($quiz_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quiz_id));

        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_quiz_to_client');

        return true;
    }
}
?>