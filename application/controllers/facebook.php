<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST2_Controller.php';
require APPPATH . '/libraries/facebook-php-sdk/facebook.php';

class Facebook extends REST2_Controller
{
    private $facebook = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('social_model');
    }

    public function realtimeupdate_get()
    {
        //$mode = $this->input->get('hub_mode');
        //$verifyToken = $this->input->get('hub_verify_token');
        $challenge = $this->input->get('hub_challenge');
        echo $challenge;
    }

    public function realtimeupdate_post()
    {
        echo $this->request->body;
    }
}
