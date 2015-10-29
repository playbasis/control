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
        $required = $this->input->checkParam(array(
            'client_id',
            'site_id',
            'category',
            'type'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $client_id = $this->input->post('client_id');
        $site_id = $this->input->post('site_id');
        $category = $this->input->post('category');
        $type = $this->input->post('type');
        $paging = $this->input->post('paging');
        $page = $this->input->post('page');

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'type' => $type,
            'category' => $category,
            'paging' => $paging,
            'page' => $page
        );

        $results = $this->CMS_model->listArticles($data);
        $this->response($this->resp->setRespond($results), 200);
    }
    public function getArticle_post()
    {
        $required = $this->input->checkParam(array(
            'client_id',
            'site_id',
            'article_id'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $client_id = $this->input->post('client_id');
        $site_id = $this->input->post('site_id');
        $id = $this->input->post('article_id');

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'id' => $id
        );

        $results = $this->CMS_model->getArticleByID($data);
        $this->response($this->resp->setRespond($results), 200);
    }
}

