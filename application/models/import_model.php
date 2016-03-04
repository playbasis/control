<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('mongo_db');
    }

    public function insertData($data, $limit = null)
    {
    /*    try {
            $this->checkClientUserLimitWarning(
                $data['client_id'], $data['site_id'], $limit);
        } catch (Exception $e) {
            if ($e->getMessage() == "USER_EXCEED") {
                return false;
            } else {
                throw new Exception($e->getMessage());
            }
        }*/
        $this->set_site_mongodb($data['site_id']);
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_import', array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'url' => $data['url'],
            'port' => $data['port'],
            'username' => $data['username'],
            'password' => $data['password'],
            'action' => $data['action'],
            'routine' => $data['routine'],
            'date_added' => $mongoDate
        ));
    }

    public function readUrl($client_id, $site_id)
    {
        if (!$client_id) {
            return null;
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('url'));
        //$this->mongo_db->where('_id', $pb_player_id);
        $this->mongo_db->order_by(array('date_added' => 'desc'));
        $this->mongo_db->limit(1);
        $url = $this->mongo_db->get('playbasis_import');
        return $url ? $url[0]:null;
    }

    public function readLatestDataAction($client_id, $site_id, $action)
    {
        if (!$client_id) {
            return null;
        }
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array($action));
        $this->mongo_db->where(array(
            'action' => $action));
        $this->mongo_db->order_by(array('date_added' => 'desc'));
        $this->mongo_db->limit(1);
        $data = $this->mongo_db->get('playbasis_import');
        return $data ? $data[0]:null;
    }
}