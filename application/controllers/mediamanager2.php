<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class MediaManager2 extends MY_Controller
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
        $this->lang->load("mediamanager2", $lang['folder']);
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
        $this->render_page('mediamanager2');
    }

    public function media($fileId = null)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$this->validateAccess()) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

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

                        $this->output->set_status_header('200');
                        $response = $result;
                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('error_no_contents'));
                    }
                } else {
                    $query_data = $this->input->get(null, true);

                    $result = $this->Image_model->retrieveImages($client_id, $site_id, $query_data);
                    foreach($result as &$document){
                        if(isset($document['_id'])){
                            $document['_id'] = $document['_id']."";
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
                    }

                    $this->output->set_status_header('200');
                    $count_images = $this->Image_model->countImages($client_id, $site_id);

                    $response = array(
                        'total' => $count_images,
                        'rows' => $result
                    );
                }

                echo json_encode($response);
                die();
            }
        }
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'mediamanager2')) {
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
                'mediamanager2') && $this->Feature_model->getFeatureExistByClientId($client_id, 'mediamanager2')
        ) {
            return true;
        } else {
            return false;
        }
    }

}