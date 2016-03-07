<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';

class Content extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Content_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("content", $lang['folder']);
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

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->getList(0);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'content/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $contents = $this->Content_model->countContents($client_id, $site_id);

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');

//        Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit_content = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'content');

        $this->data['message'] = null;

        if ($limit_content && $contents >= $limit_content) {
            $this->data['message'] = $this->lang->line('error_contents_limit');
        }

        $this->form_validation->set_rules('title', $this->lang->line('entry_title'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('summary', $this->lang->line('entry_summary'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('detail', $this->lang->line('entry_detail'),
            'trim|max_length[4096000]|xss_clean');
        $this->form_validation->set_rules('date_start', $this->lang->line('entry_date_start'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('date_end', $this->lang->line('entry_date_end'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('category', $this->lang->line('entry_category'), 'trim|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $content_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['title'] = $content_data['title'];
                $data['summary'] = $content_data['summary'];
                $data['detail'] = $content_data['detail'];
                $data['date_start'] = $content_data['date_start'];
                $data['date_end'] = $content_data['date_end'];
                $data['image'] = $content_data['image'];
                if (isset($content_data['category']) && !empty($content_data['category'])) {
                    $data['category'] = $content_data['category'];
                }
                $data['status'] = $content_data['status'] == 'on' ? true : false;

                $insert = $this->Content_model->createContent($data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/content', 'refresh');
                }
            }
        }
        $this->getForm();
    }

    public function update($content_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'content/update/' . $content_id;

        $this->form_validation->set_rules('title', $this->lang->line('entry_title'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('summary', $this->lang->line('entry_summary'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('detail', $this->lang->line('entry_detail'),
            'trim|max_length[4096000]|xss_clean');
        $this->form_validation->set_rules('date_start', $this->lang->line('entry_date_start'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('date_end', $this->lang->line('entry_date_end'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('category', $this->lang->line('entry_category'), 'trim|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $content_data = $this->input->post();

                $data['_id'] = $content_id;
                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['title'] = $content_data['title'];
                $data['summary'] = $content_data['summary'];
                $data['detail'] = $content_data['detail'];
                $data['date_start'] = $content_data['date_start'];
                $data['date_end'] = $content_data['date_end'];
                $data['image'] = $content_data['image'];
                $data['category'] = $content_data['category'];
                $data['status'] = isset($content_data['status']) ? true : false;

                $update = $this->Content_model->updateContent($data);
                if ($update) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/content', 'refresh');
                }
            }
        }

        $this->getForm($content_id);
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function getList($offset)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
            'limit' => $config['per_page'],
            'offset' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'sort_order'
        );

        if (isset($_GET['title'])) {
            $filter['title'] = $_GET['title'];
        }

        $config['base_url'] = site_url('content/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $this->data['push_feature_existed'] = $this->Feature_model->getFeatureExistByClientId($client_id, 'push');

            $contents = $this->Content_model->retrieveContents($client_id, $site_id, $filter);
            foreach ($contents as &$content) {
                if (array_key_exists('category', $content)) {
                    $content['category'] = $this->Content_model->retrieveContentCategoryById($content['category']);
                }
            }

            $this->data['contents'] = $contents;
            $config['total_rows'] = $this->Content_model->countContents($client_id, $site_id);
        }

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = "<li class='page_index_nav next'>";
        $config['next_tag_close'] = "</li>";

        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = "<li class='page_index_nav prev'>";
        $config['prev_tag_close'] = "</li>";

        $config['num_tag_open'] = '<li class="page_index_number">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page_index_number active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page_index_nav next">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page_index_nav prev">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['pagination_total_pages'] = ceil(floatval($config["total_rows"]) / $config["per_page"]);
        $this->data['pagination_total_rows'] = $config["total_rows"];

        $this->data['main'] = 'content';
        $this->render_page('template');
    }

    public function getForm($content_id = null)
    {
        $this->data['main'] = 'content_form';

        if (isset($content_id) && ($content_id != 0)) {
            if ($this->User_model->getClientId()) {
                $content_info = $this->Content_model->retrieveContent($content_id);
            }
        }

        if ($this->input->post('title')) {
            $this->data['title'] = $this->input->post('title');
        } elseif (isset($content_info['title'])) {
            $this->data['title'] = $content_info['title'];
        } else {
            $this->data['title'] = '';
        }

        if ($this->input->post('summary')) {
            $this->data['summary'] = $this->input->post('summary');
        } elseif (isset($content_info['detail'])) {
            $this->data['summary'] = $content_info['summary'];
        } else {
            $this->data['summary'] = '';
        }

        if ($this->input->post('detail')) {
            $this->data['detail'] = $this->input->post('detail');
        } elseif (isset($content_info['detail'])) {
            $this->data['detail'] = $content_info['detail'];
        } else {
            $this->data['detail'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (isset($content_info['image'])) {
            $this->data['image'] = $content_info['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->input->post('category')) {
            $this->data['category'] = $this->input->post('category');
        } elseif (isset($content_info['category'])) {
            $this->data['category'] = $content_info['category'];
        } else {
            $this->data['category'] = '';
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (isset($content_info['date_start'])) {
            $this->data['date_start'] = $content_info['date_start'];
        } else {
            $this->data['date_start'] = '';
        }

        if ($this->input->post('date_end')) {
            $this->data['date_end'] = $this->input->post('date_end');
        } elseif (isset($content_info['date_end'])) {
            $this->data['date_end'] = $content_info['date_end'];
        } else {
            $this->data['date_end'] = '';
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

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (isset($content_info['status'])) {
            $this->data['status'] = $content_info['status'];
        } else {
            $this->data['status'] = true;
        }

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if ($this->input->post('selected') && $this->error['message'] == null) {
            foreach ($this->input->post('selected') as $content_id) {
                $this->Content_model->deleteContent($content_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/content', 'refresh');
        }

        $this->getList(0);
    }

    public function push($content_id)
    {
        if ($this->session->userdata('user_id') && $this->input->is_ajax_request()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validatePushAccess()) {
                    $this->output->set_status_header('401');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
                    die();
                }

                $this->load->model('Push_model');
                $this->load->model('Player_model');

                if (isset($content_id) && (!empty($content_id))) {
                    // confirm login?
                    //if ($this->User_model->getClientId()) {
                    $content_info = $this->Content_model->retrieveContent($content_id);

                    if (!empty($content_info)) {
                        if (array_key_exists('category', $content_info)) {
                            $content_info['category'] = $this->Content_model->retrieveContentCategoryById($content_info['category']);
                        }

                        $client_id = $this->User_model->getClientId();
                        $site_id = $this->User_model->getSiteId();

                        // get all devices_tokens from all players
                        $devices = $this->Player_model->listDevices($client_id, $site_id, null,
                            array('device_token', 'os_type'));

                        // if player devices data available
                        if (!empty($devices)) {
                            $device_tokens_android = array();
                            $device_tokens_iOS = array();

                            for ($i = 0; $i <= round(count($devices) / 1000); $i++) {
                                $offset = 1000 * $i;

                                $devices_slice = array_slice($devices, $offset, 1000);
                                // loop each devices
                                foreach ($devices_slice as $device) {
                                    switch (strtolower($device['os_type'])) {
                                        case "ios":
                                            array_push($device_tokens_iOS, $device['device_token']);
                                            break;
                                        case "android":
                                            array_push($device_tokens_android, $device['device_token']);
                                            break;
                                        default:
                                            break;
                                    }

                                }
                                //     prep notification message
                                if (array_key_exists('category', $content_info)) {
                                    $content_cat = ucfirst($content_info['category']['name']);
                                } else {
                                    $content_cat = 'content!';
                                }

                                $notificationData = array(
                                    'title' => "Checkout new " . $content_cat,
                                    // android only
                                    'message' => "Checkout new " . $content_cat . ", '" . ucfirst($content_info['title']) . "' is available.",
                                    // message in iOS, body in android
                                    'badge_number' => 1,
                                );
                                //     initial push
                                if (!empty($device_tokens_android)) {
                                    $this->initiateContentPush($device_tokens_android, $notificationData,
                                        'android');
                                }
                                if (!empty($device_tokens_iOS)) {
                                    $this->initiateContentPush($device_tokens_iOS, $notificationData, 'ios');
                                }

                                // empty
                                $device_tokens_android = array();
                                $device_tokens_iOS = array();
                            }
                            $this->output->set_status_header('200');
                            echo json_encode(array(
                                'status' => 'success',
                                'devices' => count($devices)
                            ));
                        } else {
                            $this->output->set_status_header('404');
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => $this->lang->line('error_no_device_info')
                            ));
                        }
                    } else {
                        $this->output->set_status_header('404');
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => $this->lang->line('error_empty_content')
                        ));
                    }
                }
            }
        } else {
            $this->output->set_status_header('403');
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_access')));
        }
    }

    private function initiateContentPush($deviceTokens = array(), $notificationData, $type = null)
    {
        $type = strtolower($type);

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        switch ($type) {
            case "ios":
                $setup = $this->Push_model->getIosSetup($client_id, $site_id);
                if (!$setup) {
                    throw new Exception("iOS push service has not been setup yet!: ");
                    break;
                } // suppress the error for now

                $f_cert = tmpfile();
                $f_ca = tmpfile();

                $environment = $setup['env'] == 'prod' ? ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION : ApnsPHP_Abstract::ENVIRONMENT_SANDBOX;

                $meta = stream_get_meta_data($f_cert);
                $certificate = $meta['uri'];
                fwrite($f_cert, $setup['certificate']);

                $password = $setup['password'];

                $meta = stream_get_meta_data($f_ca);
                $ca = $meta['uri'];
                fwrite($f_ca, $setup['ca']);

                $push = new ApnsPHP_Push($environment, $certificate);

                $logger = new ApnsPHP_Log_Hidden();
                $push->setLogger($logger);

                // Set the Provider Certificate passphrase
                $push->setProviderCertificatePassphrase($password);

                // Set the Root Certificate Authority to verify the Apple remote peer
                $push->setRootCertificationAuthority($ca);

                // Connect to the Apple Push Notification Service
                $push->connect();

                foreach ($deviceTokens as $deviceToken) {
                    // Instantiate a new Message with a single recipient
                    $message = new ApnsPHP_Message($deviceToken);

                    // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
                    // over a ApnsPHP_Message object retrieved with the getErrors() message.
//                    $message->setCustomIdentifier("Playbasis-Notification");

                    // Set badge icon
                    $message->setBadge($notificationData['badge_number']);

                    // Set a message
                    $message->setText($notificationData['message']);

                    // Play the default sound
                    $message->setSound();

                    // Set a custom property
//                    $message->setCustomProperty('DataInfo', $notificationData['dataInfo']);

                    // Set the expiry value to 30 seconds
                    $message->setExpiry(30);

                    // Add the message to the message queue
                    $push->add($message);
                }

                // Send all messages in the message queue
                $push->send();

                // Disconnect from the Apple Push Notification Service
                $push->disconnect();

                // Examine the error message container
//                $aErrorQueue = $push->getErrors();
//                if (!empty($aErrorQueue)) {
//                    var_dump($aErrorQueue);
//                }

                fclose($f_cert);
                fclose($f_ca);

                break;

            case "android":
                $setup = $this->Push_model->getAndroidSetup($client_id);
                if (!$setup) {
                    throw new Exception("Android push service has not been setup yet!: ");
                    break;
                } // suppress the error for now

                $api_access_key = $setup['api_key'];

                $registrationIds = $deviceTokens;
                $msg = array
                (
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'badge' => $notificationData['badge_number'],
                    'sound' => 'default',
                );

                $fields = array
                (
                    'registration_ids' => $registrationIds,
                    'data' => $msg
                );

                $headers = array
                (
                    'Authorization: key=' . $api_access_key,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                //echo $result;
                break;

            default:
                throw new Exception("Unsupported device type: " . $type);
                break;
        }
    }

    public function category($categoryId = null)
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

                if (isset($categoryId)) {
                    try {
                        $result = $this->Content_model->retrieveContentCategoryById($categoryId);
                        if (isset($result['_id'])) {
                            $result['_id'] = $result['_id'] . "";
                        }

                        $this->output->set_status_header('200');
                        $response = $result;

                    } catch (Exception $e) {
                        $this->output->set_status_header('404');
                        $response = array('status' => 'error', 'message' => $this->lang->line('text_empty_content'));
                    }
                } else {
                    $query_data = $this->input->get(null, true);

                    $result = $this->Content_model->retrieveContentCategory($client_id, $site_id, $query_data);
                    foreach ($result as &$document) {
                        if (isset($document['_id'])) {
                            $document['_id'] = $document['_id'] . "";
                        }
                    }

                    $count_category = $this->Content_model->countContentCategory($client_id, $site_id);

                    $this->output->set_status_header('200');
                    $response = array(
                        'total' => $count_category,
                        'rows' => $result
                    );
                }

                echo json_encode($response);
                die();

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateModify()) {
                    $this->output->set_status_header('403');
                    echo json_encode(array('status' => 'error', 'message' => $this->lang->line('error_permission')));
                    die();
                }

                //todo: Add validation here
                $category_data = $this->input->post();

                $name = !empty($category_data['category-name']) ? $category_data['category-name'] : null;

                $result = null;
                if (!empty($category_data) && !isset($categoryId)) {
                    if (isset($category_data['action']) && $category_data['action'] == 'delete' && isset($category_data['id']) && !empty($category_data['id'])) {
                        foreach ($category_data['id'] as &$id_entry) {
                            try {
                                $id_entry = new MongoId($id_entry);
                            } catch (Exception $e) {
                                $this->output->set_status_header('400');
                                echo json_encode(array('status' => 'error'));
                                die;
                            }
                        }
                        $result = $this->Content_model->deleteContentCategoryByIdArray($category_data['id']);
                    } else {
                        $result = $this->Content_model->createContentCategory($client_id, $site_id, $name);
                    }
                } else {
                    try {
                        $categoryId = new MongoId($categoryId);
                        if (isset($category_data['action']) && $category_data['action'] == 'delete') {
                            $result = $this->Content_model->deleteContentCategory($categoryId);
                        } else {
                            $result = $this->Content_model->updateContentCategory($categoryId, array(
                                'client_id' => $client_id,
                                'site_id' => $site_id,
                                'name' => $name
                            ));
                        }
                    } catch (Exception $e) {
                        $this->output->set_status_header('400');
                        echo json_encode(array('status' => 'error'));
                        die;
                    }
                }

                if (!$result) {
                    $this->output->set_status_header('400');
                    echo json_encode(array('status' => 'error'));
                } elseif (!isset($categoryId) && !isset($category_data['action'])) {
                    $this->output->set_status_header('201');
                    // todo: should return newly create object
                    $category_result = $this->Content_model->retrieveContentCategoryById($result);
                    if (isset($category_result['_id'])) {
                        $category_result['_id'] = $category_result['_id'] . "";
                    }
                    echo json_encode(array('status' => 'success', 'rows' => $category_result));
                } else {
                    $this->output->set_status_header('200');
                    // todo: should return update object
                    echo json_encode(array('status' => 'success'));
                }
            }
        }
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'content')) {
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
                'content') && $this->Feature_model->getFeatureExistByClientId($client_id, 'content')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function validatePushAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'push') && $this->Feature_model->getFeatureExistByClientId($client_id, 'push')
        ) {
            return true;
        } else {
            return false;
        }
    }

}

class ApnsPHP_Log_Hidden implements ApnsPHP_Log_Interface
{
    /**
     * Logs a message.
     *
     * @param  $sMessage @type string The message.
     */
    public function log($sMessage)
    {
        return;
    }
}