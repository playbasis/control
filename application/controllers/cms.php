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

    public function getArticles_get()
    {
        $category = $this->input->get('category');
        $type = $this->input->get('type');
        $paging = $this->input->get('paging');
        $page = $this->input->get('page');

        $data = array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'type' => $type,
            'category' => $category,
            'paging' => $paging,
            'page' => $page
        );

        $results = $this->CMS_model->listArticles($data);
        $this->response($this->resp->setRespond($results), 200);
    }

    public function getArticle_get($article_id)
    {
        $data = array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'id' => $article_id
        );
        $results = $this->CMS_model->getArticleByID($data);
        $this->response($this->resp->setRespond($results), 200);


    }
}

