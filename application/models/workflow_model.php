<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workflow_model extends MY_Model
{

    public function getPlayerByApprovalStatus($client_id, $site_id, $approval_status) {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where(array(
            'approve_status' => $approval_status,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));

        return $this->mongo_db->get('playbasis_player');
    }

    public function getOrganizationToPlayer($client_id, $site_id, $player_id) {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where(array(
            'pb_player_id' => $player_id,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $results = $this->mongo_db->get("playbasis_store_organize_to_player");

        return $results;
    }

    public function createPlayer($data){
        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
        $this->_api = $this->playbasisapi;

        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId(),
        ));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        if (!$platform) {
            if ($this->input->post('format') == 'json') {
                echo json_encode(array('status' => 'fail', 'message' => 'Cannot find any active platform'));
                exit();
            }
        }
        $this->_api->set_api_key($result['api_key']);
        $this->_api->set_api_secret($result['api_secret']);
        $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
        $this->_api->auth($pkg_name);

        $status = $this->_api->register($data['cl_player_id'], $data['username'], $data['email'], $data);
        return $status;
    }

    public function editPlayer($player_id,$data){
        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
        $this->_api = $this->playbasisapi;

        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId(),
        ));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        if (!$platform) {
            if ($this->input->post('format') == 'json') {
                echo json_encode(array('status' => 'fail', 'message' => 'Cannot find any active platform'));
                exit();
            }
        }
        $this->_api->set_api_key($result['api_key']);
        $this->_api->set_api_secret($result['api_secret']);
        $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
        $this->_api->auth($pkg_name);

        $status = $this->_api->updatePlayer($player_id,  $data);
        return $status;
    }


    public function approvePlayer($user_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approve_status', "approved");
        $this->mongo_db->set('date_approved', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        return $this->mongo_db->update('playbasis_player');
    }

    public function rejectPlayer($user_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approve_status', "rejected");
        $this->mongo_db->set('date_approved', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        return $this->mongo_db->update('playbasis_player');
    }

    public function deletePlayer($user_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('_id', new MongoID($user_id));

        return $this->mongo_db->delete('playbasis_player');
    }
}