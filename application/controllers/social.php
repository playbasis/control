<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Social extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('social_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        $token = $this->auth_model->getApiInfo($credential);
        //echo '<br>getFacebookCredentials<br>';
        //$result = $this->social_model->getFacebookCredentials($token['client_id'], $token['site_id']);
        //print_r($result);
        echo '<br>getClientFromFacebookPageId<br>';
        $facebook_page_id = '145245468987996';
        $result = $this->social_model->getClientFromFacebookPageId($facebook_page_id);
        print_r($result);
        echo '<br>getClientFromHashTag<br>';
        $hashtag = '#playbasis';
        $result = $this->social_model->getClientFromHashTag($hashtag);
        print_r($result);
        echo '<br>getClientFromHost<br>';
        $host = 'api.pbapp.net';
        $result = $this->social_model->getClientFromHost($host);
        print_r($result);
        $facebook_id = '802465011';
        $twitter_id = '1223610780';
        $instagram_id = '47839082';
        echo '<br>getPBPlayerIdFromFacebookId<br>';
        $pb_player_id = $this->social_model->getPBPlayerIdFromFacebookId($facebook_id, $token['client_id'],
            $token['site_id']);
        print_r($pb_player_id);
        echo '<br>getPBPlayerIdFromFacebookId<br>';
        $pb_player_id = $this->social_model->getPBPlayerIdFromTwitterId($twitter_id, $token['client_id'],
            $token['site_id']);
        print_r($pb_player_id);
        echo '<br>getPBPlayerIdFromInstagramId<br>';
        $pb_player_id = $this->social_model->getPBPlayerIdFromInstagramId($instagram_id, $token['client_id'],
            $token['site_id']);
        print_r($pb_player_id);
        echo '</pre>';
    }
}

?>