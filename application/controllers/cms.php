<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class CMS extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('client_model');
        $this->load->model('player_model');
        $this->load->model('CMS_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }
    public function getArticles_post()
    {
        $client_id = $this->input->post('client_id');
        $site_id = $this->input->post('site_id');
        $category = $this->input->post('category');
        $paging = $this->input->post('paging');
        $page = $this->input->post('page');
        $results = $this->CMS_model->listArticles('article',$category,$site_id,$client_id);
        $this->response($this->resp->setRespond($results), 200);
    }
}

