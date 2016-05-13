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
        } elseif (!$this->validateModify()) {
            echo "<script>alert('" . $this->lang->line('error_permission') . "'); history.go(-1);</script>";
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

            if (!$this->validateAccess()) {
                $this->output->set_status_header('401');
                $this->output->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => $this->lang->line('error_access')
                )));
            }

            switch ($_SERVER['REQUEST_METHOD']) {
                case "GET":
                    if (isset($fileId)) {
                        try {
                            $result = $this->Image_model->retrieveImage($fileId);
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

                        $result = $this->Image_model->retrieveImages($client_id, $site_id, $query_data);
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
                            'rows' => $result
                        );
                    }
                    break;
                case "DELETE":
                    if (isset($fileId)) {
                        try {
                            $result = $this->Image_model->deleteImage($fileId);
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

    public function upload_s3()
    {

        $json = array();

        if ($this->input->post('directory') || $this->input->post('directory') == "") {

            if ($_FILES['file'] && $_FILES['file']['tmp_name']) {
                $filename = basename(html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8'));

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

                if ($_FILES['file']['size'] > MAX_UPLOADED_FILE_SIZE) {
                    $json['error'] = $this->lang->line('error_file_size');
                }

                $image_info = getimagesize($_FILES['file']['tmp_name']);
                $image_width = $image_info[0];
                $image_height = $image_info[1];

                //if($image_width < 500 || $image_width >1000){
                if ($image_width > 3000) {
                    $json['error'] = $this->lang->line('error_width');
                    // $json['error'] = $image_height." ".$image_width;
                }

                //if($image_height < 500 || $image_height >1000){
                if ($image_height > 3000) {
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

                if (!in_array($_FILES['file']['type'], $allowed)) {
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

                if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $_FILES['file']['error'];
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
        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit_images = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'image');

        $size = $this->Image_model->getTotalSize($client_id);
        if ($limit_images && ($size + $_FILES['file']['size'] > $limit_images)) {
            $json['error'] = $this->lang->line('error_overall_size_limit_reached');
        }

        if (!isset($json['error'])) {
            //create a new bucket
            //$this->s3->putBucket("elasticbeanstalk-ap-southeast-1-007834438823", S3::ACL_PUBLIC_READ);

            $this->s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");

            //move the file
            if ($this->s3->putObjectFile($_FILES['file']['tmp_name'], "elasticbeanstalk-ap-southeast-1-007834438823",
                rtrim('data/' . str_replace('../', '', $this->input->post('directory')), '/') . "/" . $filename,
                S3::ACL_PUBLIC_READ)
            ) {
                $url = rtrim(S3_IMAGE . 'data/' . str_replace('../', '', $this->input->post('directory')),
                        '/') . "/" . urlencode($filename);
                @copy($url, $directory . '/' . $filename);

                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $this->Image_model->registerImageToSite($client_id, $site_id, $_FILES['file']['size'], $filename,
                    rtrim('data/' . str_replace('../', '', $this->input->post('directory')),
                        '/') . "/" . urlencode($filename));

                $this->Image_model->resize('data/' . $filename, MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH,
                    MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT);
                $this->Image_model->resize('data/' . $filename, MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH,
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