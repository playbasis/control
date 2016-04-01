<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
require_once APPPATH . '/libraries/S3.php';
define('MAX_UPLOADED_FILE_SIZE', 3 * 1024 * 1024);
define('S3_BUCKET', 'elasticbeanstalk-ap-southeast-1-007834438823');

class File extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('image_model');
        $this->load->model('player_model');
        $this->load->model('plan_model');
        $this->load->model('user_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/utility', 'utility');

    }

    public function upload_post()
    {
        $this->benchmark->mark('start');


        if (empty($_FILES)) {
            $this->response($this->error->setError('FILE_NOT_FOUND'), 200);
        } else {
            $array = $_FILES;
            reset($array);
            $image = $_FILES[key($array)];
        }

        if ($image && $image['tmp_name']) {
            $input = $this->input->post();

            if (isset($input['player_id']) && isset($input['username'])){
                $this->response($this->error->setError('PARAMETER_INVALID', array('player_id','username')), 200);
            }

            // Find pb_player_id
            $pb_player_id = isset($input['player_id']) ? $this->player_model->getPlaybasisId(array_merge($this->validToken,
                array(
                    'cl_player_id' => $input['player_id']
                ))) : null;

            // Find username
            $user = isset($input['username']) ? $this->user_model->getByUsername($input['username']):null;
            $username = isset($user) ? $user['username'] : null;

            $client_id = $this->validToken['client_id'];
            $site_id = $this->validToken['site_id'];

            $limit = $this->plan_model->getLimitPlanByClientId($client_id, "others");
            $limit_images = isset($limit['image']) ? $limit['image'] : null;
            $size = $this->image_model->getTotalSize($client_id);
            if ($limit_images && ($size + $image['size'] > $limit_images)) {
                $this->response($this->error->setError('UPLOAD_EXCEED_LIMIT'), 200);
            }
            $directory = isset($input['directory']) ? str_replace('../', '', $input['directory']) : null;
            $filename = basename(html_entity_decode($image['name'], ENT_QUOTES, 'UTF-8'));

            $t = explode('.', $filename);
            $type = end($t);

            if ($username){
                $filename = md5(rtrim($client_id . $site_id . $filename.'dashboard'))."." . $type;
            }else{
                $filename = md5(rtrim($client_id . $site_id . $filename.$pb_player_id))."." . $type;
            }

            if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                $this->response($this->error->setError('FILE_NAME_IS_INVALID'), 200);
            }

            $local_directory = rtrim(DIR_IMAGE . $directory, '/');

            if (!is_dir($local_directory)) {
                @mkdir($local_directory);
            }

            if ($image['size'] > MAX_UPLOADED_FILE_SIZE) {
                $this->response($this->error->setError('UPLOAD_FILE_TOO_LARGE'), 200);
            }

            $image_info = getimagesize($image["tmp_name"]);
            $image_width = $image_info[0];
            $image_height = $image_info[1];

            //if($image_width < 500 || $image_width >1000){
            if ($image_width > 2000) {
                $this->response($this->error->setError('IMAGE_WIDTH_IS_INVALID'), 200);
                // $json['error'] = $image_height." ".$image_width;
            }

            //if($image_height < 500 || $image_height >1000){
            if ($image_height > 2000) {
                $this->response($this->error->setError('IMAGE_HEIGHT_IS_INVALID'), 200);
            }

            $allowed = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
                'image/gif',
                'image/tiff',
                'application/x-shockwave-flash',
                'application/octet-stream'
            );

            if (!in_array($image['type'], $allowed)) {
                $this->response($this->error->setError('FILE_TYPE_NOT_ALLOWED'), 200);
            }

            $allowed = array(
                '.jpg',
                '.jpeg',
                '.gif',
                '.png',
                '.tiff',
                '.flv'
            );

            if (!in_array(strtolower(strrchr($filename, '.')), $allowed)) {
                $this->response($this->error->setError('FILE_TYPE_NOT_ALLOWED'), 200);
            }

            if ($image['error'] != UPLOAD_ERR_OK) {
                $this->response($this->error->setError('UPLOAD_FILE_ERROR', $image['error']), 200);
            }
        } else {
            $this->response($this->error->setError('FILE_NOT_FOUND'), 200);
        }

        if ($this->image_model->uploadImage($client_id, $site_id, $image, $filename, $directory, $pb_player_id, $username)) {


            $json['url'] = rtrim(S3_IMAGE . S3_CONTENT_FOLDER . $directory, '/') . "/" . urlencode($filename);
            @copy(rtrim(S3_IMAGE . S3_CONTENT_FOLDER . $directory, '/') . "/" . urlencode($filename),
                $directory . '/' . $filename);
            if ($directory) {
                $uri = $directory . "/" . $filename;
            } else {
                $uri = $filename;
            }
            $json['thumb_url'] = $this->image_model->createThumbnail($uri);
            @unlink($local_directory . '/' . $filename);
            @unlink($local_directory . '/' . THUMBNAIL_FOLDER . $filename);

        } else {
            $this->response($this->error->setError('UPLOAD_FILE_ERROR', "S3 fail"), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');

        $json['processing_time'] = $t;
        $this->response($this->resp->setRespond($json), 200);
    }

    public function delete_post()
    {
        $this->benchmark->mark('start');
        $input = $this->input->post();
        $client_id = $this->validToken['client_id'];
        $site_id = $this->validToken['site_id'];

        $filename = $input['file_name'];
        $directory = isset($input['directory']) ? str_replace('../', '', $input['directory']) : null;
        if (!$filename) {
            $this->response($this->error->setError('FILE_NOT_FOUND'), 200);
        }

        if (!$this->image_model->deleteImage($client_id, $site_id, $filename, $directory)) {

            $this->response($this->error->setError('DELETE_FILE_FAILED'), 200);
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('processing_time' => $t)), 200);
    }


}