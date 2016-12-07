<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sequence_model extends MY_Model
{

    public function retrieveSequenceByID($client_id, $site_id, $sequence_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($sequence_id));
        $this->mongo_db->where('deleted', false);

        $this->mongo_db->select(array('name', 'file_name', 'tags', 'file_id'));
        $result = $this->mongo_db->get('playbasis_sequence_to_client');

        return $result ? $result[0] : null;
    }

    public function retrieveSequenceFileByID($client_id, $site_id, $sequence_file_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($sequence_file_id));

        $result = $this->mongo_db->get('playbasis_sequence_file_to_client');

        return $result ? $result[0] : null;
    }

    public function retrieveSequence($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($data['client_id']));
        $this->mongo_db->where('site_id', new MongoId($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
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

        $this->mongo_db->select(array('name', 'file_name', 'tags'));

        $result = $this->mongo_db->get('playbasis_sequence_to_client');

        return $result;
    }

    public function getTotalSequence($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        return $this->mongo_db->count("playbasis_sequence_to_client");
    }

    public function insertSequence($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $result = $this->insertSequenceFile($data);
        if($result) {
            if (!empty($data['tags'])) {
                $tags = explode(',', $data['tags']);
            }

            $insert_data = array(
                'client_id' => new MongoId($data['client_id']),
                'site_id' => new MongoId($data['site_id']),
                'name' => $data['name'],
                'file_name' => $data['file_name'],
                'file_id' => new MongoId($result),
                'sequence_list' => $data['sequence_list'],
                'tags' => isset($tags) ? $tags : null,
                'deleted' => false,
                'date_added' => new MongoDate(),
                'date_modified' => new MongoDate()
            );
            $result = $this->mongo_db->insert('playbasis_sequence_to_client', $insert_data);
        }

        return $result;
    }

    private function insertSequenceFile($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'file_content' =>$data['file_content'],
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_sequence_file_to_client', $insert_data);

        return $insert;
    }

    public function updateSequence($client_id, $site_id, $sequence_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $result = false;
        $sequence_info = $this->retrieveSequenceByID($client_id, $site_id, $sequence_id);
        if(isset($sequence_info['file_id'])){
            $result = $this->updateSequenceFile($client_id, $site_id, $sequence_info['file_id'], $data );
            if($result){
                if (!empty($data['tags'])){
                    $tags = explode(',', $data['tags']);
                }

                $this->mongo_db->where('_id', new MongoID($sequence_id));
                $this->mongo_db->where('client_id', new MongoID($client_id));
                $this->mongo_db->where('site_id', new MongoID($site_id));

                $this->mongo_db->set('name', $data['name']);

                if(isset($data['file_name']) && $data['file_name']) {
                    $this->mongo_db->set('file_name', $data['file_name']);
                }
                if(isset($data['sequence_list']) && $data['sequence_list']){
                    $this->mongo_db->set('sequence_list' , $data['sequence_list']);
                }
                $this->mongo_db->set('tags', isset($tags) ? $tags : null);
                $this->mongo_db->set('date_modified', new MongoDate());

                $result = $this->mongo_db->update('playbasis_sequence_to_client');
            }
        }

        return $result;
    }

    public function updateSequenceFile($client_id, $site_id, $sequence_file_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($sequence_file_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        if(isset($data['file_content']) && $data['file_content']){
            $this->mongo_db->set('file_content' , $data['file_content']);
        }
        $this->mongo_db->set('date_modified', new MongoDate());

        $result = $this->mongo_db->update('playbasis_sequence_file_to_client');

        return $result;
    }

    public function deleteSequence($client_id, $site_id, $sequence_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sequence_info = $this->retrieveSequenceByID($client_id, $site_id, $sequence_id);
        if(isset($sequence_info['file_id'])){
            $this->deleteSequenceFile($client_id, $site_id, $sequence_info['file_id']);
        }

        $this->mongo_db->where('_id', new MongoID($sequence_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);

        $result = $this->mongo_db->update('playbasis_sequence_to_client');

        return $result;
    }

    private function deleteSequenceFile($client_id, $site_id, $sequence_file_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($sequence_file_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        $result = $this->mongo_db->delete('playbasis_sequence_file_to_client');

        return $result;
    }

}

?>