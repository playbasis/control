<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Setting extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('setting_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
    }
    
    public function appStatus_get()
    {
        $setting = $this->setting_model->retrieveSetting($this->client_id,$this->site_id);
        $response = array();
        if ($setting){
            $response['app_status'] = $this->setting_model->appStatus($this->client_id,$this->site_id);
            if ($response['app_status']) $response['app_period'] = isset($setting['app_period']) && !empty($setting['app_period']) ? $setting['app_period'] : null;
        }
        array_walk_recursive($response, array($this, 'convert_mongo_date'));

        $this->response($this->resp->setRespond($response), 200);
    }

    private function convert_mongo_date(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }
    }
}