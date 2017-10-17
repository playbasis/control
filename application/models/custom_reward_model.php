<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_reward_model extends MY_Model
{

    public function retrieveCustomRewardByID($client_id, $site_id, $item_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($item_id));
        $this->mongo_db->where('deleted', false);

        $this->mongo_db->select(array('name', 'file_name', 'tags', 'file_id'));
        $result = $this->mongo_db->get('playbasis_custom_reward_to_client');

        return $result ? $result[0] : null;
    }

    public function retrieveCustomRewardFileByID($client_id, $site_id, $file_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('_id', new MongoId($file_id));

        $result = $this->mongo_db->get('playbasis_custom_reward_file_to_client');

        return $result ? $result[0] : null;
    }

    public function retrieveCustomReward($data)
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

        $result = $this->mongo_db->get('playbasis_custom_reward_to_client');

        return $result;
    }

    public function getTotalCustomReward($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        return $this->mongo_db->count("playbasis_custom_reward_to_client");
    }

    public function insertCustomReward($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $result = $this->insertCustomRewardFile($data);
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
                'custom_reward_data' => $data['custom_reward_data'],
                'tags' => isset($tags) ? $tags : null,
                'deleted' => false,
                'date_added' => new MongoDate(),
                'date_modified' => new MongoDate()
            );
            $result = $this->mongo_db->insert('playbasis_custom_reward_to_client', $insert_data);
        }

        return $result;
    }

    private function insertCustomRewardFile($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'file_content' =>$data['file_content'],
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_custom_reward_file_to_client', $insert_data);

        return $insert;
    }

    public function updateCustomReward($client_id, $site_id, $item_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $result = false;
        $data_info = $this->retrieveCustomRewardByID($client_id, $site_id, $item_id);
        if(isset($data_info['file_id'])){
            $result = $this->updateCustomRewardFile($client_id, $site_id, $data_info['file_id'], $data );
            if($result){
                if (!empty($data['tags'])){
                    $tags = explode(',', $data['tags']);
                }

                $this->mongo_db->where('_id', new MongoID($item_id));
                $this->mongo_db->where('client_id', new MongoID($client_id));
                $this->mongo_db->where('site_id', new MongoID($site_id));

                $this->mongo_db->set('name', $data['name']);

                if(isset($data['file_name']) && $data['file_name']) {
                    $this->mongo_db->set('file_name', $data['file_name']);
                }
                if(isset($data['custom_reward_data']) && $data['custom_reward_data']){
                    $this->mongo_db->set('custom_reward_data' , $data['custom_reward_data']);
                }
                $this->mongo_db->set('tags', isset($tags) ? $tags : null);
                $this->mongo_db->set('date_modified', new MongoDate());

                $result = $this->mongo_db->update('playbasis_custom_reward_to_client');
            }
        }

        return $result;
    }

    public function updateCustomRewardFile($client_id, $site_id, $file_id, $data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($file_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        if(isset($data['file_content']) && $data['file_content']){
            $this->mongo_db->set('file_content' , $data['file_content']);
        }
        $this->mongo_db->set('date_modified', new MongoDate());

        $result = $this->mongo_db->update('playbasis_custom_reward_file_to_client');

        return $result;
    }

    public function deleteCustomReward($client_id, $site_id, $item_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data_info = $this->retrieveCustomRewardByID($client_id, $site_id, $item_id);
        if(isset($data_info['file_id'])){
            $this->deleteCustomRewardFile($client_id, $site_id, $data_info['file_id']);
        }

        $this->mongo_db->where('_id', new MongoID($item_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->set('date_modified', new MongoDate());
        $this->mongo_db->set('deleted', true);

        $result = $this->mongo_db->update('playbasis_custom_reward_to_client');

        return $result;
    }

    private function deleteCustomRewardFile($client_id, $site_id, $file_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($file_id));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));

        $result = $this->mongo_db->delete('playbasis_custom_reward_file_to_client');

        return $result;
    }

}

?>