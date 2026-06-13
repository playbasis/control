<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('MAX_UPLOADED_FILE_SIZE', 3 * 1024 * 1024);

class MediaManager extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Image_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("mediamanager", $lang['folder']);
    }

    public function index()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->data['main'] = 'mediamanager';

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (isset($content_info['image'])) {
            $this->data['image'] = $content_info['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->data['image']) {
            $info = pathinfo($this->data['image']);
            if (isset($info['extension'])) {
                $extension = $info['extension'];
                $new_image = 'cache/' . utf8_substr($this->data['image'], 0,
                        utf8_strrpos($this->data['image'], '.')) . '-100x100.' . $extension;
                $this->data['thumb'] = S3_IMAGE . $new_image;
            } else {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }
        } else {
            $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function dialog()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        } elseif (!$this->validateModify()) {
            echo "<script>alert('" . $this->lang->line('error_permission') . "'); history.go(-1);</script>";
            die();
        }

        if ($this->input->get('field')) {
            $this->data['field'] = $this->input->get('field');
        } else {
            $this->data['field'] = '';
        }

        if ($this->input->get('CKEditorFuncNum')) {
            $this->data['fckeditor'] = $this->input->get('CKEditorFuncNum');
        } else {
            $this->data['fckeditor'] = false;
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->render_page('mediamanager_dialog');
    }

    public function media($fileId = null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if (!$this->requireMediaManagerAccess()) {
                return;
            }

            switch ($_SERVER['REQUEST_METHOD']) {
                case "GET":
                    if (isset($fileId)) {
                        try {
                            $result = $this->Image_model->retrieveImage($client_id, $site_id, $fileId);
                            if (isset($result['_id'])) {
                                $result['_id'] = $result['_id'] . "";
                            }
                            if (isset($result['date_added'])) {
                                $result['date_added'] = datetimeMongotoReadable($result['date_added']);
                            }
                            if (isset($result['date_modified'])) {
                                $result['date_modified'] = datetimeMongotoReadable($result['date_modified']);
                            }
                            if (isset($result['file_name'])) {
                                $info = pathinfo($result['file_name']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_lg_image = 'cache/data/' . utf8_substr($result['file_name'], 0,
                                            utf8_strrpos($result['file_name'],
                                                '.')) . '-' . MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT . '.' . $extension;
                                    $lg_thumb = S3_IMAGE . $new_lg_image;
                                    $new_sm_image = 'cache/data/' . utf8_substr($result['file_name'], 0,
                                            utf8_strrpos($result['file_name'],
                                                '.')) . '-' . MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT . '.' . $extension;
                                    $sm_thumb = S3_IMAGE . $new_sm_image;
                                } else {
                                    $lg_thumb = S3_IMAGE . "cache/no_image-50x50.jpg";
                                    $sm_thumb = S3_IMAGE . "cache/no_image-50x50.jpg";
                                }
                                $result['lg_thumb'] = $lg_thumb;
                                $result['sm_thumb'] = $sm_thumb;
                            }
                            if (isset($result['url'])) {
                                $result['url'] = S3_IMAGE . $result['url'];
                            }

                            $this->output->set_status_header('200');
                            $response = $result;
                        } catch (Exception $e) {
                            $this->output->set_status_header('404');
                            $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                        }
                    } else {
                        $query_data = $this->input->get(null, true);
                        if (isset($query_data['folder']) && $query_data['folder'] === '') {
                            $query_data['folder'] = 'false';
                        }
                        if (isset($query_data['folder']) && $query_data['folder'] !== 'false' && !$this->isValidMongoId($query_data['folder'])) {
                            $this->output->set_status_header('400');
                            $this->output->set_output(json_encode(array('status' => 'error')));
                            return;
                        }

                        $result = $this->Image_model->retrieveImages($client_id, $site_id, $query_data);
                        $folder = $this->Image_model->retrieveFolder($client_id, $site_id);
                        foreach ($folder as &$folder_document) {
                            if (isset($folder_document['_id'])) {
                                $folder_document['_id'] = $folder_document['_id'] . "";
                            }
                            if (isset($folder_document['date_added'])) {
                                $folder_document['date_added'] = datetimeMongotoReadable($folder_document['date_added']);
                            }
                            if (isset($folder_document['date_modified'])) {
                                $folder_document['date_modified'] = datetimeMongotoReadable($folder_document['date_modified']);
                            }
                        }
                        foreach ($result as &$document) {
                            if (isset($document['_id'])) {
                                $document['_id'] = $document['_id'] . "";
                            }
                            if (isset($document['date_added'])) {
                                $document['date_added'] = datetimeMongotoReadable($document['date_added']);
                            }
                            if (isset($document['date_modified'])) {
                                $document['date_modified'] = datetimeMongotoReadable($document['date_modified']);
                            }
                            if (isset($document['file_name'])) {
                                $info = pathinfo($document['file_name']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_lg_image = 'cache/data/' . utf8_substr($document['file_name'], 0,
                                            utf8_strrpos($document['file_name'],
                                                '.')) . '-' . MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT . '.' . $extension;
                                    $lg_thumb = S3_IMAGE . $new_lg_image;
                                    $new_sm_image = 'cache/data/' . utf8_substr($document['file_name'], 0,
                                            utf8_strrpos($document['file_name'],
                                                '.')) . '-' . MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT . '.' . $extension;
                                    $sm_thumb = S3_IMAGE . $new_sm_image;
                                } else {
                                    $lg_thumb = S3_IMAGE . "cache/no_image-50x50.jpg";
                                    $sm_thumb = S3_IMAGE . "cache/no_image-50x50.jpg";
                                }
                                $document['lg_thumb'] = $lg_thumb;
                                $document['sm_thumb'] = $sm_thumb;
                            }
                            if (isset($document['url'])) {
                                $document['url'] = S3_IMAGE . $document['url'];
                            }
                        }

                        $this->output->set_status_header('200');
                        $count_images = $this->Image_model->countImages($client_id, $site_id);

                        $response = array(
                            'total' => $count_images,
                            'rows' => $result,
                            'folders' => $folder
                        );
                    }
                    break;
                case "DELETE":
                    if (!$this->requireMediaManagerModify()) {
                        return;
                    }
                    if (isset($fileId)) {
                        try {
                            $result = $this->Image_model->deleteImage($client_id, $site_id, $fileId);
                            if ($result) {
                                $this->output->set_status_header('200');
                                $response = array('status' => 'success');
                            } else {
                                $this->output->set_status_header('404');
                                $response = array(
                                    'status' => 'error',
                                    'message' => $this->lang->line('error_no_contents')
                                );
                            }
                        } catch (Exception $e) {
                            $this->output->set_status_header('400');
                            $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                        }
                    } else {
                        $response = array('status' => 'error');
                        $this->output->set_header(400);
                    }
                    break;
                default:
                    $response = array('status' => 'error');
                    $this->output->set_header(400);
                    break;
            }
            $this->output->set_output(json_encode($response));
        }
    }

    private function requireMediaManagerAccess()
    {
        if (!$this->validateAccess()) {
            $this->output->set_status_header('401');
            $this->output->set_output(json_encode(array(
                'status' => 'error',
                'message' => $this->lang->line('error_access')
            )));
            return false;
        }

        return true;
    }

    private function requireMediaManagerModify()
    {
        if (!$this->requireMediaManagerAccess()) {
            return false;
        }

        if (!$this->validateModify()) {
            $this->output->set_status_header('401');
            $this->output->set_output(json_encode(array(
                'status' => 'error',
                'message' => $this->lang->line('error_permission')
            )));
            return false;
        }

        return true;
    }

    public function insertFolder(){
        if (!$this->requireMediaManagerModify()) {
            return;
        }
        $query_data = $this->input->get(null, true);
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $user_id = $this->User_model->getId();

        $result = $this->Image_model->insertNewFolder($client_id, $site_id, $user_id, $query_data);
    }

    public function unsetAllFile(){
        if (!$this->requireMediaManagerModify()) {
            return;
        }
        $query_data = $this->input->get(null, true);
        if (!$this->hasValidMongoIdParam($query_data, 'elementID')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode(false));
            return;
        }
        $result = $this->Image_model->unsetAllFile($query_data);
        $this->output->set_output(json_encode($result));
    }

    public function deleteFolder(){
        if (!$this->requireMediaManagerModify()) {
            return;
        }
        $query_data = $this->input->get(null, true);
        if (!$this->hasValidMongoIdParam($query_data, 'elementID')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode(false));
            return;
        }
        $result = $this->Image_model->deleteFolder_model($query_data);
        $this->output->set_output(json_encode($result));
    }

    public function updateImageCategory(){
        if (!$this->requireMediaManagerModify()) {
            return;
        }
        $query_data = $this->input->get(null, true);
        if (!$this->hasValidMongoIdParam($query_data, 'elementID')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode(false));
            return;
        }
        if (!isset($query_data['folder_id']) || ($query_data['folder_id'] !== 'root' && !$this->isValidMongoId($query_data['folder_id']))) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode(false));
            return;
        }
        $result = $this->Image_model->updateImageCategory($query_data);
        $this->output->set_output(json_encode($result));
    }

    public function updateFolderName(){
        if (!$this->requireMediaManagerModify()) {
            return;
        }
        $query_data = $this->input->get(null, true);
        if (!$this->hasValidMongoIdParam($query_data, 'elementID')) {
            $this->output->set_status_header('400');
            $this->output->set_output(json_encode(false));
            return;
        }
        $result = $this->Image_model->updateFolder($query_data);
        $this->output->set_output(json_encode($result));
    }

    private function hasValidMongoIdParam($params, $key)
    {
        return is_array($params) && isset($params[$key]) && $this->isValidMongoId($params[$key]);
    }

    private function isValidMongoId($id)
    {
        return is_scalar($id) && preg_match('/^[0-9a-f]{24}$/i', (string)$id) === 1;
    }

    public function image()
    {
        if ($this->input->get('image')) {
            $image = $this->cleanRelativePath($this->input->get('image'));
            if ($image === false || $image === '') {
                $this->output->set_status_header('400');
                return;
            }

            //thumbnail
            $this->Image_model->resize($image, 40, 40);
            $this->Image_model->resize($image, 50, 50);
            $this->Image_model->resize($image, 140, 140);
            $this->output->set_output($this->Image_model->resize($image, 100, 100));
        }
    }

    public function files()
    {
        $json = array();

        $directory = $this->dataLocalPath(!empty($this->input->post['directory']) ? $this->input->post['directory'] : '');
        if ($directory === false) {
            $this->output->set_output(json_encode($json));
            return;
        }

        $allowed = array(
            '.jpg',
            '.jpeg',
            '.png',
            '.gif'
        );

        $files = glob(rtrim($directory, '/') . '/*');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $ext = strrchr($file, '.');
                } else {
                    $ext = '';
                }

                if (in_array(strtolower($ext), $allowed)) {
                    $size = filesize($file);

                    $i = 0;

                    $suffix = array(
                        'B',
                        'KB',
                        'MB',
                        'GB',
                        'TB',
                        'PB',
                        'EB',
                        'ZB',
                        'YB'
                    );

                    while (($size / 1024) > 1) {
                        $size = $size / 1024;
                        $i++;
                    }

                    $json[] = array(
                        'filename' => basename($file),
                        'file' => utf8_substr($file, utf8_strlen(DIR_IMAGE . 'data/')),
                        'size' => round(utf8_substr($size, 0, utf8_strpos($size, '.') + 4), 2) . $suffix[$i]
                    );
                }
            }
        }

        $this->output->set_output(json_encode($json));
    }

    private function isValidUploadEntry($field)
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return false;
        }
        foreach (array('name', 'tmp_name', 'size', 'type', 'error') as $key) {
            if (!isset($_FILES[$field][$key]) || !is_scalar($_FILES[$field][$key])) {
                return false;
            }
        }
        return $_FILES[$field]['tmp_name'] !== '';
    }

    public function upload_s3()
    {

        $json = array();

        if ($this->input->post('directory') || $this->input->post('directory') == "") {

            if ($this->isValidUploadEntry('file')) {
                $upload = $_FILES['file'];
                $filename = basename(html_entity_decode($upload['name'], ENT_QUOTES, 'UTF-8'));

                $t = explode('.', $filename);
                $type = end($t);

                $filename = md5($this->User_model->getClientId() . $this->User_model->getSiteId() . $filename) . "." . $type;

                if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                    $json['error'] = $this->lang->line('error_filename');
                }

                $clean_directory = $this->cleanRelativePath($this->input->post('directory'));
                if ($clean_directory === false) {
                    $json['error'] = $this->lang->line('error_directory');
                }

                $directory = $this->dataLocalPath($clean_directory);

                if (!isset($json['error']) && !is_dir($directory)) {
                    $json['error'] = $this->lang->line('error_directory');
                }

                if ($upload['size'] > MAX_UPLOADED_FILE_SIZE) {
                    $json['error'] = $this->lang->line('error_file_size');
                }

                $image_info = @getimagesize($upload['tmp_name']);
                if ($image_info === false) {
                    $json['error'] = $this->lang->line('error_file_type');
                    $image_width = 0;
                    $image_height = 0;
                } else {
                    $image_width = $image_info[0];
                    $image_height = $image_info[1];
                }

                //if($image_width < 500 || $image_width >1000){
                if ($image_width > MEDIA_MANAGER_MAX_IMAGE_WIDTH) {
                    $json['error'] = $this->lang->line('error_width');
                    // $json['error'] = $image_height." ".$image_width;
                }

                //if($image_height < 500 || $image_height >1000){
                if ($image_height > MEDIA_MANAGER_MAX_IMAGE_HEIGHT) {
                    $json['error'] = $this->lang->line('error_height');
                    // $json['error'] = $image_height." ".$image_width;
                }

//                if(intval($image_height) != intval($image_width)){
//                    $json['error'] = $this->lang->line('error_square');
//                }

                $allowed = array(
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/x-png',
                    'image/gif',
                    'application/x-shockwave-flash'
                );

                if (!in_array($upload['type'], $allowed)) {
                    $json['error'] = $this->lang->line('error_file_type');
                }

                $allowed = array(
                    '.jpg',
                    '.jpeg',
                    '.gif',
                    '.png',
                    '.flv'
                );

                if (!in_array(strtolower(strrchr($filename, '.')), $allowed)) {
                    $json['error'] = $this->lang->line('error_file_type');
                }

                if ($upload['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $upload['error'];
                }
            } else {
                $json['error'] = $this->lang->line('error_file');
            }
        } else {
            $json['error'] = $this->lang->line('error_directory');
        }

        if (!$this->User_model->hasPermission('modify', 'mediamanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }


        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->model('Plan_model');
        $this->load->model('Permission_model');
        if (!isset($json['error'])) {
            // Get Limit
            $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
            try {
                $limit_images = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'image');
            } catch (Exception $e) {
                $json['error'] = $this->lang->line('error_uploaded');
            }

            if (!isset($json['error'])) {
                $size = $this->Image_model->getTotalSize($client_id);
                if ($limit_images && ($size + $upload['size'] > $limit_images)) {
                    $json['error'] = $this->lang->line('error_overall_size_limit_reached');
                }
            }
        }

        if (!isset($json['error'])) {
            //create a new bucket
            //$this->s3->putBucket("elasticbeanstalk-ap-southeast-1-007834438823", S3::ACL_PUBLIC_READ);

            $this->s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");

            //move the file
            $s3_directory = $this->dataS3Prefix($clean_directory);
            $s3_key = $s3_directory . "/" . $filename;

            if ($this->s3->putObjectFile($_FILES['file']['tmp_name'], "elasticbeanstalk-ap-southeast-1-007834438823",
                $s3_key, S3::ACL_PUBLIC_READ)
            ) {
                $url = S3_IMAGE . $s3_directory . "/" . urlencode($filename);
                @copy($url, $directory . '/' . $filename);

                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
                $user_id = $this->User_model->getId();

                $this->Image_model->registerImageToSite($client_id, $site_id, $user_id, $_FILES['file']['size'], $filename,
                    $s3_directory . "/" . urlencode($filename));

                $this->Image_model->resize($s3_key, MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH,
                    MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT);
                $this->Image_model->resize($s3_key, MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH,
                    MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT);

                $json['success'] = $this->lang->line('text_uploaded');
            } else {
                $json['error'] = $this->lang->line('error_uploaded');
            }
        }

        if (!isset($json['error'])) {
            $this->output->set_status_header('200');
        } else {
            $this->output->set_status_header('400');
        }
        $this->output->set_output(json_encode($json));
    }

    private function cleanRelativePath($path)
    {
        if (is_array($path)) {
            return false;
        }

        $path = html_entity_decode((string)$path, ENT_QUOTES, 'UTF-8');

        if (strpos($path, "\0") !== false) {
            return false;
        }

        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $path)) {
            return false;
        }

        if ($path[0] === '/' || strpos($path, '\\') !== false) {
            return false;
        }

        if (preg_match('/[^A-Za-z0-9_\\.\\-\\/ ]/', $path)) {
            return false;
        }

        $clean = array();
        $segments = explode('/', $path);

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            if ($segment === '.' || strpos($segment, '..') !== false) {
                return false;
            }

            $clean[] = $segment;
        }

        return implode('/', $clean);
    }

    private function dataLocalPath($path)
    {
        $path = $this->cleanRelativePath($path);

        if ($path === false) {
            return false;
        }

        return rtrim(DIR_IMAGE . 'data/' . $path, '/');
    }

    private function dataS3Prefix($path)
    {
        $path = $this->cleanRelativePath($path);

        if ($path === false || $path === '') {
            return 'data';
        }

        return 'data/' . $path;
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'mediamanager')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'mediamanager') && $this->Feature_model->getFeatureExistByClientId($client_id, 'mediamanager')
        ) {
            return true;
        } else {
            return false;
        }
    }

}
