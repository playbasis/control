<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Internal extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('plan_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
    }

    public function listLimitFeature_get()
    {
        $required = $this->input->checkParam(array(
            'client_id',
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }


        $client_id = $this->input->get('client_id');
        $feature_group = $this->input->get('feature_group');
        $result = $this->plan_model->getLimitPlanByClientId($client_id, $feature_group);
        $this->response($this->resp->setRespond($result), 200);

    }
}