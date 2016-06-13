<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Link extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('link_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/utility', 'utility');
        $this->load->library('curl');
    }

    public function view_get()
    {
        $link = $this->input->get('link');
        if (!$link) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'link'
            )), 200);
        }
        $data = $this->link_model->findByLink($this->client_id, $this->site_id, $link);
        if (!$data) {
            $conf = $this->link_model->getConfig($this->client_id, $this->site_id);
            if (!$conf) $this->response($this->error->setError('LINK_CONFIG_NOT_FOUND'), 200);
            if (!isset($conf['type']) || !in_array($conf['type'], array('branch.io'))) $this->response($this->error->setError('LINK_CONFIG_INVALID_TYPE'), 200);
            if ($conf['type'] == 'branch.io') {
                if (!isset($conf['key']) || !$conf['key']) $this->response($this->error->setError('LINK_CONFIG_INVALID_BRANCH_KEY'), 200);
            }
            $params = array(
                'branch_key' => $conf['key'],
                'url' => $link
            );
            $this->curl->create('https://api.branch.io/v1/url'.($params ? '?'.http_build_query($params, NULL, '&') : ''));
            //$this->curl->ssl(false);
            $this->curl->http_header('Content-Type', 'application/json');
            $response = $this->curl->execute();
            if (!$response) $this->response($this->error->setError('LINK_BRANCH_ERROR'), 200);
            $res = json_decode($response);
            if (!isset($res->data)) $this->response($this->error->setError('LINK_BRANCH_ERROR'), 200);
            $data = (array) $res->data;
            $keys = array();
            foreach ($data as $k => $v) {
                if ($k == 'url' || in_array($k[0], array('$', '~'))) $keys[] = $k;
            }
            foreach ($keys as $k) unset($data[$k]);
            $this->link_model->save($this->client_id, $this->site_id, $data, $link);
        }
        $this->response($this->resp->setRespond(array('data' => $data)), 200);
    }

    public function generate_post()
    {
        $data = $this->input->post();
        $data = array_merge($data, array('vendor' => 'playbasis'));
        foreach (array('api_key', 'api_secret', 'token', 'iodocs') as $k) unset($data[$k]);
        $link = $this->link_model->find($this->client_id, $this->site_id, $data);
        if (!$link) {
            $conf = $this->link_model->getConfig($this->client_id, $this->site_id);
            if (!$conf) $this->response($this->error->setError('LINK_CONFIG_NOT_FOUND'), 200);
            if (!isset($conf['type']) || !in_array($conf['type'], array('branch.io'))) $this->response($this->error->setError('LINK_CONFIG_INVALID_TYPE'), 200);
            if ($conf['type'] == 'branch.io') {
                if (!isset($conf['key']) || !$conf['key']) $this->response($this->error->setError('LINK_CONFIG_INVALID_BRANCH_KEY'), 200);
            }
            $message = array(
                'branch_key' => $conf['key'],
                'feature' => 'api',
                'data' => json_encode($data)
            );
            $this->curl->create('https://api.branch.io/v1/url');
            //$this->curl->ssl(false);
            $this->curl->http_header('Content-Type', 'application/json');
            $this->curl->post(json_encode($message));
            $response = $this->curl->execute();
            if (!$response) $this->response($this->error->setError('LINK_BRANCH_ERROR'), 200);
            $res = json_decode($response);
            if (!isset($res->url)) $this->response($this->error->setError('LINK_BRANCH_ERROR'), 200);
            $link = $res->url;
            $this->link_model->save($this->client_id, $this->site_id, $data, $link);
        }
        $this->response($this->resp->setRespond(array('link' => $link)), 200);
    }
}