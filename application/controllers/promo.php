<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Promo extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('promo_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');

        $promo_data['site_id'] = $this->site_id;
        $promo_data['client_id'] = $this->client_id;

        $promos = $this->promo_model->listPromos($promo_data, false);
        if (empty($promos)) {
            $this->response($this->error->setError('PROMO_CONTENT_NOT_FOUND'), 200);
        }

        foreach ($promos as $k => $v) {
            $promos[$k] ['description'] = $promos[$k] ['desc'];
            unset($promos[$k]['desc']);
        }

        array_walk_recursive($promos, array($this, "convert_mongo_object"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $promos, 'processing_time' => $t)), 200);
    }

    public function detail_get($promo_id = 0)
    {
        $this->benchmark->mark('start');

        if (empty($promo_id)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('promo_id')), 200);
        }

        $promo_id = new MongoId($promo_id);

        $promo_data['site_id'] = $this->site_id;
        $promo_data['client_id'] = $this->client_id;
        $promo_data['promo_id'] = $promo_id;

        $promo = $this->promo_model->getPromo($promo_data, true);
        if (empty($promo)) {
            $this->response($this->error->setError('PROMO_CONTENT_NOT_FOUND'), 200);
        }

        foreach ($promo as $k => $v) {
            $promo[$k] ['description'] = $promo[$k] ['desc'];
            unset($promo[$k]['desc']);
        }

        array_walk_recursive($promo, array($this, "convert_mongo_object"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $promo, 'processing_time' => $t)), 200);
    }

    public function detailByName_get($name = null)
    {
        $this->benchmark->mark('start');

        if (empty($name)) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('promo_name')), 200);
        }

        $promo_data['site_id'] = $this->site_id;
        $promo_data['client_id'] = $this->client_id;
        $promo_data['name'] = $name;

        $promo = $this->promo_model->getPromo($promo_data, true);
        if (empty($promo)) {
            $this->response($this->error->setError('PROMO_CONTENT_NOT_FOUND'), 200);
        }

        foreach ($promo as $k => $v) {
            $promo[$k] ['description'] = $promo[$k] ['desc'];
            unset($promo[$k]['desc']);
        }

        array_walk_recursive($promo, array($this, "convert_mongo_object"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $promo, 'processing_time' => $t)), 200);
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key)
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