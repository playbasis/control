<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('MAX_UPLOADED_FILE_SIZE', 3 * 1024 * 1024);

class FileManager extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        // if(!$this->User_model->isLogged()){
        //     redirect('/login', 'refresh');
        // }
        $this->load->model('Image_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("filemanager", $lang['folder']);

    }

    public function index()
    {

        $this->data['title'] = $this->lang->line('heading_title');

        $this->data['site_id'] = $this->User_model->getSiteId();
        $this->data['client_id'] = $this->User_model->getClientId();

        $this->data['directory'] = S3_IMAGE . 'data/';

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

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

        $this->load->vars($this->data);
        $this->load->view('filemanager');
    }

    public function image()
    {
        if ($this->input->get('image')) {
            //thumbnail
            $this->Image_model->resize($this->input->get('image'), 40, 40);
            $this->Image_model->resize($this->input->get('image'), 50, 50);
            $this->Image_model->resize($this->input->get('image'), 140, 140);
            $this->output->set_output($this->Image_model->resize(html_entity_decode($this->input->get('image'),
                ENT_QUOTES, 'UTF-8'), 100, 100));
        }
    }

    public function directory()
    {
        $json = array();

        if (isset($this->input->post['directory'])) {
            $directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->input->post['directory']),
                    '/') . '/*', GLOB_ONLYDIR);

            if ($directories) {
                $i = 0;

                foreach ($directories as $directory) {
                    $json[$i]['data'] = basename($directory);
                    $json[$i]['attributes']['directory'] = utf8_substr($directory, strlen(DIR_IMAGE . 'data/'));

                    $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

                    if ($children) {
                        $json[$i]['children'] = ' ';
                    }

                    $i++;
                }
            }
        }

        $this->output->set_output(json_encode($json));
    }

    public function files()
    {
        $json = array();

        if (!empty($this->input->post['directory'])) {
            $directory = DIR_IMAGE . 'data/' . str_replace('../', '', $this->input->post['directory']);
        } else {
            $directory = DIR_IMAGE . 'data/';
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

    public function create()
    {

        $json = array();

        if (isset($this->input->post['directory'])) {
            if (isset($this->input->post['name']) || $this->input->post['name']) {
                $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->input->post['directory']), '/');

                if (!is_dir($directory)) {
                    $json['error'] = $this->lang->line('error_directory');
                }

                if (file_exists($directory . '/' . str_replace('../', '', $this->input->post['name']))) {
                    $json['error'] = $this->lang->line('error_exists');
                }
            } else {
                $json['error'] = $this->lang->line('error_name');
            }
        } else {
            $json['error'] = $this->lang->line('error_directory');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            mkdir($directory . '/' . str_replace('../', '', $this->input->post['name']), 0777);

            $json['success'] = $this->lang->line('text_create');
        }

        $this->output->set_output(json_encode($json));
    }

    public function delete()
    {

        $json = array();

        if (isset($this->input->post['path'])) {
            $path = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '',
                    html_entity_decode($this->input->post['path'], ENT_QUOTES, 'UTF-8')), '/');

            if (!file_exists($path)) {
                $json['error'] = $this->lang->line('error_select');
            }

            if ($path == rtrim(DIR_IMAGE . 'data/', '/')) {
                $json['error'] = $this->lang->line('error_delete');
            }
        } else {
            $json['error'] = $this->lang->line('error_select');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            if (is_file($path)) {
                unlink($path);
            } elseif (is_dir($path)) {
                $this->recursiveDelete($path);
            }

            $json['success'] = $this->lang->line('text_delete');
        }

        $this->output->set_output(json_encode($json));
    }

    protected function recursiveDelete($directory)
    {
        if (is_dir($directory)) {
            $handle = opendir($directory);
        }

        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($directory . '/' . $file)) {
                    unlink($directory . '/' . $file);
                } else {
                    $this->recursiveDelete($directory . '/' . $file);
                }
            }
        }

        closedir($handle);

        rmdir($directory);

        return true;
    }

    public function move()
    {

        $json = array();

        if (isset($this->input->post['from']) && isset($this->input->post['to'])) {
            $from = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '',
                    html_entity_decode($this->input->post['from'], ENT_QUOTES, 'UTF-8')), '/');

            if (!file_exists($from)) {
                $json['error'] = $this->lang->line('error_missing');
            }

            if ($from == DIR_IMAGE . 'data') {
                $json['error'] = $this->lang->line('error_default');
            }

            $to = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '',
                    html_entity_decode($this->input->post['to'], ENT_QUOTES, 'UTF-8')), '/');

            if (!file_exists($to)) {
                $json['error'] = $this->lang->line('error_move');
            }

            if (file_exists($to . '/' . basename($from))) {
                $json['error'] = $this->lang->line('error_exists');
            }
        } else {
            $json['error'] = $this->lang->line('error_directory');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            rename($from, $to . '/' . basename($from));

            $json['success'] = $this->lang->line('text_move');
        }

        $this->output->set_output(json_encode($json));
    }

    public function copy()
    {

        $json = array();

        if (isset($this->input->post['path']) && isset($this->input->post['name'])) {
            if ((utf8_strlen($this->input->post['name']) < 3) || (utf8_strlen($this->input->post['name']) > 255)) {
                $json['error'] = $this->lang->line('error_filename');
            }

            $old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '',
                    html_entity_decode($this->input->post['path'], ENT_QUOTES, 'UTF-8')), '/');

            if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
                $json['error'] = $this->lang->line('error_copy');
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace('../', '',
                    html_entity_decode($this->input->post['name'], ENT_QUOTES, 'UTF-8') . $ext);

            if (file_exists($new_name)) {
                $json['error'] = $this->lang->line('error_exists');
            }
        } else {
            $json['error'] = $this->lang->line('error_select');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            if (is_file($old_name)) {
                copy($old_name, $new_name);
            } else {
                $this->recursiveCopy($old_name, $new_name);
            }

            $json['success'] = $this->lang->line('text_copy');
        }

        $this->output->set_output(json_encode($json));
    }

    function recursiveCopy($source, $destination)
    {
        $directory = opendir($source);

        @mkdir($destination);

        while (false !== ($file = readdir($directory))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    $this->recursiveCopy($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }

        closedir($directory);
    }

    public function folders()
    {
        $this->response->setOutput($this->recursiveFolders(DIR_IMAGE . 'data/'));
    }

    protected function recursiveFolders($directory)
    {
        $output = '';

        $output .= '<option value="' . utf8_substr($directory,
                strlen(DIR_IMAGE . 'data/')) . '">' . utf8_substr($directory,
                strlen(DIR_IMAGE . 'data/')) . '</option>';

        $directories = glob(rtrim(str_replace('../', '', $directory), '/') . '/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $output .= $this->recursiveFolders($directory);
        }

        return $output;
    }

    public function rename()
    {

        $json = array();

        if (isset($this->input->post['path']) && isset($this->input->post['name'])) {
            if ((utf8_strlen($this->input->post['name']) < 3) || (utf8_strlen($this->input->post['name']) > 255)) {
                $json['error'] = $this->lang->line('error_filename');
            }

            $old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '',
                    html_entity_decode($this->input->post['path'], ENT_QUOTES, 'UTF-8')), '/');

            if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
                $json['error'] = $this->lang->line('error_rename');
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace('../', '',
                    html_entity_decode($this->input->post['name'], ENT_QUOTES, 'UTF-8') . $ext);

            if (file_exists($new_name)) {
                $json['error'] = $this->lang->line('error_exists');
            }
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            rename($old_name, $new_name);

            $json['success'] = $this->lang->line('text_rename');
        }

        $this->output->set_output(json_encode($json));
    }

    public function upload()
    {

        $json = array();

        if (isset($this->input->post['directory'])) {
            if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
                $filename = basename(html_entity_decode($_FILES['image']['name'], ENT_QUOTES, 'UTF-8'));

                $t = explode('.', $filename);
                $type = end($t);

                $filename = md5($this->User_model->getClientId() . $this->User_model->getSiteId() . $filename) . "." . $type;

                if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                    $json['error'] = $this->lang->line('error_filename');
                }

                $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->input->post['directory']), '/');

                if (!is_dir($directory)) {
                    $json['error'] = $this->lang->line('error_directory');
                }

                if ($_FILES['image']['size'] > MAX_UPLOADED_FILE_SIZE) {
                    $json['error'] = $this->lang->line('error_file_size');
                }

                $allowed = array(
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/x-png',
                    'image/gif',
                    'application/x-shockwave-flash'
                );

                if (!in_array($_FILES['image']['type'], $allowed)) {
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

                if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $_FILES['image']['error'];
                }
            } else {
                $json['error'] = $this->lang->line('error_file');
            }
        } else {
            $json['error'] = $this->lang->line('error_directory');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if (!isset($json['error'])) {
            if (@move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {
                $json['success'] = $this->lang->line('text_uploaded');
            } else {
                $json['error'] = $this->lang->line('error_uploaded');
            }
        }

        $this->output->set_output(json_encode($json));
    }

    public function upload_s3()
    {

        $json = array();

        if ($this->input->post('directory') || $this->input->post('directory') == "") {

            if ($_FILES['image'] && $_FILES['image']['tmp_name']) {
                $filename = basename(html_entity_decode($_FILES['image']['name'], ENT_QUOTES, 'UTF-8'));

                $t = explode('.', $filename);
                $type = end($t);

                $filename = md5($this->User_model->getClientId() . $this->User_model->getSiteId() . $filename) . "." . $type;

                if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                    $json['error'] = $this->lang->line('error_filename');
                }

                $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->input->post('directory')), '/');

                if (!is_dir($directory)) {
                    $json['error'] = $this->lang->line('error_directory');
                }

                if ($_FILES['image']['size'] > MAX_UPLOADED_FILE_SIZE) {
                    $json['error'] = $this->lang->line('error_file_size');
                }

                $image_info = getimagesize($_FILES["image"]["tmp_name"]);
                $image_width = $image_info[0];
                $image_height = $image_info[1];

                //if($image_width < 500 || $image_width >1000){
                if ($image_width > 2000) {
                    $json['error'] = $this->lang->line('error_width');
                    // $json['error'] = $image_height." ".$image_width;
                }

                //if($image_height < 500 || $image_height >1000){
                if ($image_height > 2000) {
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

                if (!in_array($_FILES['image']['type'], $allowed)) {
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

                if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $_FILES['image']['error'];
                }
            } else {
                $json['error'] = $this->lang->line('error_file');
            }
        } else {
            $json['error'] = $this->lang->line('error_directory');
        }

        if (!$this->User_model->hasPermission('modify', 'filemanager')) {
            $json['error'] = $this->lang->line('error_permission');
        }

        if ($this->validateMediaManagerAccess()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $this->load->model('Plan_model');
            $this->load->model('Permission_model');
            // Get Limit
            $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
            $limit_images = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'image');

            $size = $this->Image_model->getTotalSize($client_id);
            if ($limit_images && ($size + $_FILES['image']['size'] > $limit_images)) {
                $json['error'] = $this->lang->line('error_overall_size_limit_reached');
            }
        }

        if (!isset($json['error'])) {
            //create a new bucket
            //$this->s3->putBucket("elasticbeanstalk-ap-southeast-1-007834438823", S3::ACL_PUBLIC_READ);

            $this->s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");

            //move the file
            if ($this->s3->putObjectFile($_FILES['image']['tmp_name'], "elasticbeanstalk-ap-southeast-1-007834438823",
                rtrim('data/' . str_replace('../', '', $this->input->post('directory')), '/') . "/" . $filename,
                S3::ACL_PUBLIC_READ)
            ) {
                $url = rtrim(S3_IMAGE . 'data/' . str_replace('../', '', $this->input->post('directory')),
                        '/') . "/" . urlencode($filename);
                @copy($url, $directory . '/' . $filename);

                if ($this->validateMediaManagerAccess()) {
                    $client_id = $this->User_model->getClientId();
                    $site_id = $this->User_model->getSiteId();

                    $this->Image_model->registerImageToSite($client_id, $site_id, $_FILES['image']['size'], $filename,
                        $url);

                    $this->Image_model->resize('data/' . $filename, MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH,
                        MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT);
                    $this->Image_model->resize('data/' . $filename, MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH,
                        MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT);
                }

                $json['success'] = $this->lang->line('text_uploaded');
            } else {
                $json['error'] = $this->lang->line('error_uploaded');
            }
        }

        $this->output->set_output(json_encode($json));
    }

    private function validateMediaManagerAccess()
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