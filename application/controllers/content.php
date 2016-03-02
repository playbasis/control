<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Content extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('content_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');

        $query_data = $this->input->get(null, true);

        if (isset($query_data['id'])) {
            try {
                $query_data['id'] = new MongoId($query_data['id']);
            } catch (Exception $e) {
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }
        }

        $contents = $this->content_model->retrieveContent($this->client_id, $this->site_id, $query_data);
        if (empty($contents)) {
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }

        if (isset($query_data['full_html']) && $query_data['full_html'] == "true"){
            if(is_array($contents))foreach ($contents as &$content){
                $content['detail'] = '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">'.
                    '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">'.
                    '<style>img{ max-width: 100%}</style>'.
                    '</head><title></title><body>'.$content['detail'].'</body></html>';
            }
        }
        array_walk_recursive($contents, array($this, "convert_mongo_object_and_category"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $contents, 'processing_time' => $t)), 200);
    }

    public function category_get()
    {
        $this->benchmark->mark('start');

        $query_data = $this->input->get(null, true);

        if (isset($query_data['id'])) {
            try {
                $query_data['id'] = new MongoId($query_data['id']);
            } catch (Exception $e) {
                $this->response($this->error->setError('PARAMETER_INVALID', array('id')), 200);
            }
        }

        $categories = $this->content_model->retrieveContentCategory($this->client_id, $this->site_id, $query_data);
        if (empty($categories)) {
            $this->response($this->error->setError('CONTENT_NOT_FOUND'), 200);
        }

        $result = array();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                array_push($result, $category['name']);
            }
        }

        if (empty($result)) {
            $result = null;
        }
        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object_and_category(&$item, $key)
    {
        if ($key == 'category') {
            $item = $this->content_model->getContentCategoryNameById($this->client_id, $this->site_id, $item);
        }
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