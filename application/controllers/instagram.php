<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Instagram extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('social_model');
        $this->load->model('tool/respond', 'resp');
    }

    public function feed_get()
    {
        $hub_mode = $this->input->get('hub_mode');
        if ($hub_mode == 'subscribe') {
            echo $this->input->get('hub_challenge');
        } else {
            echo 'playbasis <3 instagram';
        }
    }

    public function feed_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'), true);
        $this->social_model->saveInstagramFeedData($jsonArray);
    }
}
