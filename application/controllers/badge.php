<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Badge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("badge", $lang['folder']);
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
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
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
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    public function insert()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'badge/insert';

        //I took out the check_space because some badges may have spaces? - Joe
        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('quantity', $this->lang->line('entry_quantity'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('per_user', $this->lang->line('entry_per_user'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('description', $this->lang->line('form_description'),
            'trim|xss_clean|max_length[1000]');
        $this->form_validation->set_rules('stackable', "", '');
        $this->form_validation->set_rules('substract', "", '');
        $this->form_validation->set_rules('claim', "", '');
        $this->form_validation->set_rules('redeem', "", '');


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($this->checkLimitBadge()) {
                $this->data['message'] = null;

                if (!$this->validateModify()) {
                    $this->data['message'] = $this->lang->line('error_permission');
                }

                $badge_data = $this->input->post();

                if ($this->form_validation->run() && $this->data['message'] == null) {

                    if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
                        $this->Badge_model->addBadge($badge_data, true);
                        $this->session->set_flashdata('success', $this->lang->line('text_success'));
                        redirect('/badge', 'refresh');
                    }

                    if ($this->User_model->getClientId()) {

                        $badge_id = $this->Badge_model->addBadge($badge_data);

                        $badge_data['badge_id'] = $badge_id;
                        $badge_data['client_id'] = $this->User_model->getClientId();
                        $badge_data['site_id'] = $this->User_model->getSiteId();

                        $this->Badge_model->addBadgeToClient($badge_data);

                        $this->session->set_flashdata('success', $this->lang->line('text_success'));

                        redirect('/badge', 'refresh');
                    } else {

                        $this->load->model('Client_model');

                        if (isset($badge_data['sponsor'])) {
                            $badge_data['sponsor'] = true;
                        } else {
                            $badge_data['sponsor'] = false;
                        }

                        if (isset($badge_data['admin_client_id']) && $badge_data['admin_client_id'] != 'all_clients') {

                            $clients_sites = $this->Client_model->getSitesByClientId($badge_data['admin_client_id']);

                            $badge_data['badge_id'] = $this->Badge_model->addBadge($badge_data);

                            $badge_data['client_id'] = $badge_data['admin_client_id'];

                            foreach ($clients_sites as $client) {
                                $badge_data['site_id'] = $client['_id'];
                                $this->Badge_model->addBadgeToClient($badge_data);
                            }
                        } else {
                            $badge_data['badge_id'] = $this->Badge_model->addBadge($badge_data);

                            $all_sites_clients = $this->Client_model->getAllSitesFromAllClients();

                            foreach ($all_sites_clients as $site) {
                                $badge_data['site_id'] = $site['_id'];
                                $badge_data['client_id'] = $site['client_id'];
                                $this->Badge_model->addBadgeToClient($badge_data);
                            }
                        }
                        redirect('/badge', 'refresh');
                    }

                }
            } else {
                $this->session->set_flashdata('limit_reached', $this->lang->line('text_reach_limit_badge'));
                redirect('/badge/insert', 'refresh');
            }
        }
        $this->getForm();
    }

    public function update($badge_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'badge/update/' . $badge_id;

        //I took out the check_space because some badges may have spaces? - Joe
        //OK - Wee
        $this->form_validation->set_rules('name', $this->lang->line('name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('quantity', $this->lang->line('entry_quantity'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('per_user', $this->lang->line('entry_per_user'),
            'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('description', $this->lang->line('form_description'),
            'trim|xss_clean|max_length[1000]');
        $this->form_validation->set_rules('stackable', "", '');
        $this->form_validation->set_rules('substract', "", '');
        $this->form_validation->set_rules('claim', "", '');
        $this->form_validation->set_rules('redeem', "", '');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->checkOwnerBadge($badge_id)) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $badge_data = $this->input->post();

            if ($this->form_validation->run() && $this->data['message'] == null) {
                if ($this->User_model->getClientId()) {
                    if (!$this->Badge_model->checkBadgeIsSponsor($badge_id)) {
                        $badge_data['client_id'] = $this->User_model->getClientId();
                        $badge_data['site_id'] = $this->User_model->getSiteId();
                        $this->Badge_model->editBadgeToClient($badge_id, $badge_data);
                    } else {
                        redirect('/badge', 'refresh');
                    }
                } else {
                    $this->Badge_model->editBadge($badge_id, $badge_data);

                    $this->Badge_model->editBadgeToClientFromAdmin($badge_id, $badge_data);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));

                redirect('/badge', 'refresh');
            }
        }

        $this->getForm($badge_id);
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $badge_id) {
                if ($this->checkOwnerBadge($badge_id)) {

                    if ($this->User_model->getClientId()) {
                        if (!$this->Badge_model->checkBadgeIsSponsor($badge_id)) {
                            $this->Badge_model->deleteBadgeClient($badge_id);
                        } else {
                            redirect('/badge', 'refresh');
                        }
                    } else {
                        $this->Badge_model->deleteBadge($badge_id);
                        $this->Badge_model->deleteClientBadgeFromAdmin($badge_id);
                    }

                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/badge', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset)
    {

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('badge/page');


        $this->load->model('Badge_model');
        $this->load->model('Image_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['badges'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->getUserGroupId() == $setting_group_id) {
            $data['limit'] = $per_page;
            $data['start'] = $offset;
            $data['sort'] = 'sort_order';

            $results = $this->Badge_model->getBadges($data, true);

            $badge_total = $this->Badge_model->getTotalBadges($data, true);

            foreach ($results as $result) {

                /*if ($result['image']){
                    $info = pathinfo($result['image']);
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($result['image'], 0, utf8_strrpos($result['image'], '.')).'-50x50.'.$extension;

                    $headers = get_headers(S3_IMAGE.$new_image, 1);
                    if($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.0 403 Forbidden'){
                        $image = $new_image;
                    }else{
                        $headers = get_headers(S3_IMAGE.$result['image'], 1);
                        if($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.0 403 Forbidden') {
                            $image = $this->Image_model->resize($result['image'], 50, 50);
                        }else{
                            $image = S3_IMAGE."cache/no_image-50x50.jpg";
                        }
                    }
                } else {
                    $image = S3_IMAGE."cache/no_image-50x50.jpg";
                }*/
                /*if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }*/
                if ($result['image']) {
                    $info = pathinfo($result['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($result['image'], 0,
                                utf8_strrpos($result['image'], '.')) . '-50x50.' . $extension;
                        $image = S3_IMAGE . $new_image;
                    } else {
                        $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                    }
                } else {
                    $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                }

                $badgeIsPublic = $this->checkBadgeIsPublic($result['_id']);
                $this->data['badges'][] = array(
                    'badge_id' => $result['_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'per_user' => (isset($result['per_user']) && !empty($result['per_user'])) ? $result['per_user']: null ,
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order' => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'],
                            $this->input->post('selected'))),
                    'is_public' => $badgeIsPublic,
                    // 'sponsor' => $result['sponsor']
                );
            }
        } else {  // Client Account
            $this->load->model('Reward_model');

            // get all client badges id
            $client_badges = $this->Badge_model->getAllBadgeIdByClientId($client_id, $site_id);
            // sync template
            $this->Badge_model->syncTemplate($client_badges, $client_id, $site_id);

            $badge_data = array(
                'site_id' => $site_id,
                'limit' => $per_page,
                'start' => $offset,
                'sort' => 'sort_order'
            );

            $badges = $this->Badge_model->getBadgeBySiteId($badge_data);

            $reward_limit_data = $this->Reward_model->getBadgeRewardBySiteId($site_id);

            $badge_total = $this->Badge_model->getTotalBadgeBySiteId($badge_data);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data['limit'] - $badge_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = S3_IMAGE . "cache/no_image-50x50.jpg";

                foreach ($badges as $badge) {

                    $badge_info = $this->Badge_model->getBadgeToClient($badge['_id']);

                    if ($badge_info) {

                        if ($badge_info['image']) {
                            $info = pathinfo($badge_info['image']);
                            if (isset($info['extension'])) {
                                $extension = $info['extension'];
                                $new_image = 'cache/' . utf8_substr($badge_info['image'], 0,
                                        utf8_strrpos($badge_info['image'], '.')) . '-50x50.' . $extension;
                                $image = S3_IMAGE . $new_image;
                            } else {
                                $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                            }
                        } else {
                            $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                        }

                        /*if ($badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $image = $this->Image_model->resize($badge_info['image'], 50, 50);
                        }else {
                            $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                        }*/

                        if (!$badge_info['deleted']) {
                            $this->data['badges'][] = array(
                                'badge_id' => $badge_info['_id'],
                                'name' => $badge_info['name'],
                                'hint' => $badge_info['hint'],
                                'quantity' => $badge_info['quantity'],
                                'per_user' => (isset($badge_info['per_user']) && !empty($badge_info['per_user'])) ? $badge_info['per_user']: null ,
                                'status' => $badge_info['status'],
                                'image' => $image,
                                'sort_order' => $badge_info['sort_order'],
                                'selected' => ($this->input->post('selected') && in_array($badge_info['_id'],
                                        $this->input->post('selected'))),
                                'sponsor' => isset($badge_info['sponsor']) ? $badge_info['sponsor'] : null,
                                "is_template" => isset($badge_info["is_template"]) ? $badge_info["is_template"] : false
                            );
                        }
                    }
                }
            }
        }

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

        $config['total_rows'] = $badge_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

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

        $this->data['main'] = 'badge';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function getListForAjax($offset)
    {

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('badge/page');


        $this->load->model('Badge_model');
        $this->load->model('Image_model');

        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['badges'] = array();
        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_model->getUserGroupId() == $setting_group_id) {

            $data['limit'] = $per_page;
            $data['start'] = $offset;
            $data['sort'] = 'sort_order';

            $results = $this->Badge_model->getBadges($data);

            $badge_total = $this->Badge_model->getTotalBadges($data);

            foreach ($results as $result) {

                if ($result['image']) {
                    $info = pathinfo($result['image']);
                    if (isset($info['extension'])) {
                        $extension = $info['extension'];
                        $new_image = 'cache/' . utf8_substr($result['image'], 0,
                                utf8_strrpos($result['image'], '.')) . '-50x50.' . $extension;
                        $image = S3_IMAGE . $new_image;
                    } else {
                        $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                    }
                } else {
                    $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                }

                /*if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }*/
                $badgeIsPublic = $this->checkBadgeIsPublic($result['_id']);
                $this->data['badges'][] = array(
                    'badge_id' => $result['_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'per_user' => (isset($result['per_user']) && !empty($result['per_user'])) ? $result['per_user']: null ,
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order' => $result['sort_order'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'],
                            $this->input->post('selected'))),
                    'is_public' => $badgeIsPublic,
                    'sponsor' => isset($badge_info['sponsor']) ? $badge_info['sponsor'] : null
                );
            }
        } else {

            $this->load->model('Reward_model');

            $badge_data = array(
                'site_id' => $site_id,
                'limit' => $per_page,
                'start' => $offset,
                'sort' => 'sort_order'
            );

            $badges = $this->Badge_model->getBadgeBySiteId($badge_data);

            $reward_limit_data = $this->Reward_model->getBadgeRewardBySiteId($site_id);

            $badge_total = $this->Badge_model->getTotalBadgeBySiteId($badge_data);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data['limit'] - $badge_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = S3_IMAGE . "cache/no_image-50x50.jpg";

                foreach ($badges as $badge) {

                    $badge_info = $this->Badge_model->getBadgeToClient($badge['_id']);

                    if ($badge_info) {

                        if ($badge_info['image']) {
                            $info = pathinfo($badge_info['image']);
                            if (isset($info['extension'])) {
                                $extension = $info['extension'];
                                $new_image = 'cache/' . utf8_substr($badge_info['image'], 0,
                                        utf8_strrpos($badge_info['image'], '.')) . '-50x50.' . $extension;
                                $image = S3_IMAGE . $new_image;
                            } else {
                                $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                            }
                        } else {
                            $image = S3_IMAGE . "cache/no_image-50x50.jpg";
                        }

                        /*if ($badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $image = $this->Image_model->resize($badge_info['image'], 50, 50);
                        }
                        else {
                            $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                        }*/

                        if (!$badge_info['deleted']) {
                            $this->data['badges'][] = array(
                                'badge_id' => $badge_info['_id'],
                                'name' => $badge_info['name'],
                                'hint' => $badge_info['hint'],
                                'quantity' => $badge_info['quantity'],
                                'per_user' => (isset($badge_info['per_user']) && !empty($badge_info['per_user'])) ? $badge_info['per_user']: null,
                                'status' => $badge_info['status'],
                                'image' => $image,
                                'sort_order' => $badge_info['sort_order'],
                                'selected' => ($this->input->post('selected') && in_array($badge_info['_id'],
                                        $this->input->post('selected'))),
                                'sponsor' => isset($badge_info['sponsor']) ? $badge_info['sponsor'] : null
                            );
                        }


                    }
                }
            }
        }

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

        $config['total_rows'] = $badge_total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;

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

        $this->data['main'] = 'badge';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('badge_ajax');
    }

    private function getForm($badge_id = null)
    {

        $this->load->model('Image_model');

        if (isset($badge_id) && ($badge_id != 0)) {
            if ($this->User_model->getClientId()) {
                $badge_info = $this->Badge_model->getBadgeToClient($badge_id);
            } else {
                $badge_info = $this->Badge_model->getBadge($badge_id);
            }

        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['name'] = $badge_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('description')) {
            $this->data['description'] = htmlentities($this->input->post('description'));
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['description'] = htmlentities($badge_info['description']);
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('hint')) {
            $this->data['hint'] = $this->input->post('hint');
        } elseif (isset($badge_id) && ($badge_id != 0)) {
            $this->data['hint'] = $badge_info['hint'];
        } else {
            $this->data['hint'] = '';
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($badge_info)) {
            $this->data['image'] = $badge_info['image'];
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
            } elsE {
                $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
            }
        } else {
            $this->data['thumb'] = S3_IMAGE . "cache/no_image-100x100.jpg";
        }

        /*if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($badge_info) && $badge_info['image'] && (S3_IMAGE . $badge_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($badge_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }*/

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($badge_info)) {
            $this->data['sort_order'] = $badge_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($badge_info)) {
            $this->data['status'] = $badge_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if ($this->input->post('stackable')) {
            $this->data['stackable'] = $this->input->post('stackable');
        } elseif (!empty($badge_info)) {
            $this->data['stackable'] = $badge_info['stackable'];
        } else {
            $this->data['stackable'] = 1;
        }

        if ($this->input->post('substract')) {
            $this->data['substract'] = $this->input->post('substract');
        } elseif (!empty($badge_info)) {
            $this->data['substract'] = $badge_info['substract'];
        } else {
            $this->data['substract'] = 1;
        }

        if ($this->input->post('claim')) {
            $this->data['claim'] = $this->input->post('claim');
        } elseif (!empty($badge_info) && isset($badge_info['claim'])) {
            $this->data['claim'] = $badge_info['claim'];
        } else {
            $this->data['claim'] = 0;
        }

        if ($this->input->post('redeem')) {
            $this->data['redeem'] = $this->input->post('redeem');
        } elseif (!empty($badge_info) && isset($badge_info['redeem'])) {
            $this->data['redeem'] = $badge_info['redeem'];
        } else {
            $this->data['redeem'] = 0;
        }

        if ($this->input->post('quantity')) {
            $this->data['quantity'] = $this->input->post('quantity');
        } elseif (!empty($badge_info)) {
            $this->data['quantity'] = $badge_info['quantity'];
        } else {
            $this->data['quantity'] = 1;
        }

        if ($this->input->post('per_user')) {
            $this->data['per_user'] = $this->input->post('per_user');
        } elseif (!empty($badge_info)) {
            $this->data['per_user'] = (isset($badge_info['per_user']) && !empty($badge_info['per_user'])) ? $badge_info['per_user']: null;
        } else {
            $this->data['per_user'] = null;
        }

        if ($this->input->post('sponsor')) {
            // echo $this->input->post('sponsor');
            $this->data['sponsor'] = $this->input->post('sponsor');
        } elseif (!empty($badge_info)) {
            $this->data['sponsor'] = isset($badge_info['sponsor']) ? $badge_info['sponsor'] : null;
        } else {
            $this->data['sponsor'] = false;
        }

        if (isset($badge_id)) {
            $this->data['badge_id'] = $badge_id;
        } else {
            $this->data['badge_id'] = null;
        }

        if ($this->User_model->getClientId()) {
            if ($this->data['sponsor']) {
                redirect('badge', 'refresh');
            }
        }

        $this->load->model('Client_model');
        $this->data['to_clients'] = $this->Client_model->getClients(array());
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'badge_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'badge')) {
            return true;
        } else {
            return false;
        }
    }

    private function checkLimitBadge()
    {

        // if(isset($client)){
        if ($this->User_model->getClientId()) {
            $error = null;

            if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

                $this->load->model('Reward_model');

                $plan_limit = $this->Reward_model->getRewardByClientId($this->User_model->getClientId());

                $data = array(
                    'site_id' => $this->User_model->getSiteId()
                );
                // $badges_count = $this->Badge_model->getTotalBadgeBySiteId($data);
                $badges_count = $this->Badge_model->getTotalBadgeBySiteIdWithoutSponsor($data);


                foreach ($plan_limit as $plan) {
                    if ($plan['site_id'] == $this->input->post('site_id')) {
                        if ($plan['name'] == 'badge') {
                            if ($plan['limit']) {
                                $limit_badge = $plan['limit'];
                            }
                        }
                    }
                }

                if (isset($limit_badge)) {
                    if ($badges_count >= $limit_badge) {
                        $over_limit = true;
                    } else {
                        $over_limit = false;
                    }
                } else {
                    $over_limit = false;
                }
            }

            if (!$over_limit) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }

    }

    private function checkOwnerBadge($badgeId)
    {

        $error = null;

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

            $badge_data = array('site_id' => $this->User_model->getSiteId());

            $badges = $this->Badge_model->getBadgeBySiteId($badge_data);
            $has = false;

            foreach ($badges as $badge) {
                if ($badge['_id'] . "" == $badgeId . "") {
                    $has = true;
                }
            }

            if (!$has) {
                $error = $this->lang->line('error_permission');
            }
        }

        if (!$error) {
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
                'badge') && $this->Feature_model->getFeatureExistByClientId($client_id, 'badge')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function increase_order($badge_id)
    {

        if ($this->User_model->getClientId()) {
            $this->Badge_model->increaseOrderByOneClient($badge_id);
        } else {
            $this->Badge_model->increaseOrderByOne($badge_id);
        }

        $json = array('success' => 'Okay increase!');

        $this->output->set_output(json_encode($json));
    }

    public function decrease_order($badge_id)
    {

        if ($this->User_model->getClientId()) {
            $this->Badge_model->decreaseOrderByOneClient($badge_id);
        } else {
            $this->Badge_model->decreaseOrderByOne($badge_id);
        }

        $json = array('success' => 'Okay decrease!');

        $this->output->set_output(json_encode($json));
    }

    public function checkBadgeIsPublic($badge_id)
    {
        $allBadgesFromClients = $this->Badge_model->checkBadgeIsPublic($badge_id);

        if (isset($allBadgesFromClients[0]['client_id'])) {
            $firstBadge = $allBadgesFromClients[0]['client_id'];
            foreach ($allBadgesFromClients as $badge) {
                if ($badge['client_id'] != $firstBadge) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

}
