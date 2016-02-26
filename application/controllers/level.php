<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Level extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Level_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("level", $lang['folder']);
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

        $this->getList($offset);
    }

    public function insert()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'level/insert';

        $this->form_validation->set_rules('exp', $this->lang->line('entry_exp'),
            'trim|required|numeric|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('level', $this->lang->line('entry_level'),
            'trim|required|numeric|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        // $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'), 'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('level_title', "", '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
                    $data = $this->input->post();
                    $data['client_id'] = $this->User_model->getClientId();
                    $data['site_id'] = $this->User_model->getSiteId();

                    $checkLevelExists = $this->Level_model->checkLevelExists($data);

                    if (!$checkLevelExists) {
                        $insertLevel = $this->Level_model->addLevelSite($data);

                        if ($insertLevel) {
                            $this->session->set_flashdata('success', $this->lang->line('text_success'));
                            redirect('/level', 'refresh');
                        } else {
                            $this->data['message'] = $this->lang->line('error_exp_level');
                        }
                    } else {
                        $this->data['message'] = $this->lang->line('error_exp_level_exists');
                    }
                } else {
                    $this->Level_model->addLevel($this->input->post());
                }
            }
        }

        $this->getForm();
    }

    public function update($level_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'level/update/' . $level_id;

        $this->form_validation->set_rules('exp', $this->lang->line('entry_exp'),
            'trim|required|numeric|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('level', $this->lang->line('entry_level'),
            'trim|required|numeric|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        // $this->form_validation->set_rules('sort_order', $this->lang->line('entry_sort_order'), 'numeric|trim|xss_clean|check_space|greater_than[-1]|less_than[2147483647]');
        $this->form_validation->set_rules('level_title', "", '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {

                $checkLevelExistsEdit = $this->Level_model->checkLevelExistsEdit($this->input->post(), $level_id);

                if (!$checkLevelExistsEdit) {
                    if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
                        $insertEditClient = $this->Level_model->editLevelSite($level_id, $this->input->post());

                        if ($insertEditClient) {
                            $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                            redirect('/level', 'refresh');
                        } else {
                            $this->data['message'] = $this->lang->line('error_exp_level');
                        }
                    } else {
                        $this->Level_model->editLevel($level_id, $this->input->post());
                    }
                } else {
                    $this->data['message'] = $this->lang->line('error_exp_level_exists');
                }


            }
        }

        $this->getForm($level_id);
    }

    public function useTemplate($template)
    {
        $this->data['message'] = null;

        if (!$this->validateModify()) {
            $this->data['message'] = $this->lang->line('error_permission');
        }

        if ($this->data['message'] == null) {
            if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {

                // playbasis_client_exp_table
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();
            } else {
                // playbasis_exp_table
                $client_id = "";
                $site_id = "";
            }

            $max = $this->input->post("max");
            if ($max && intval($max)) {
                $max = intval($max);
            } else {
                $max = 100;
            }

            try {
                $result = $this->Level_model->addTemplate($template, $client_id, $site_id, $max);
                if ($result) {
                    $this->session->set_flashdata("success", $this->lang->line("text_success"));
                } else {
                    $this->data["message"] = $this->lang->line("error_exp_level");
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
            redirect("/level", "refresh");
        }
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

            if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
                foreach ($this->input->post('selected') as $level_id) {
                    $this->Level_model->deleteLevelSite($level_id);
                }
            } else {
                foreach ($this->input->post('selected') as $level_id) {
                    $this->Level_model->deleteLevel($level_id);
                }
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/level', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset)
    {

        $per_page = 2 * NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('level/page');

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
        } else {
            $sort = 'level';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page;

        $data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        $this->data['levels'] = array();

        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
            $data['client_id'] = $this->User_model->getClientId();
            $data['site_id'] = $this->User_model->getSiteId();

            $client_id = $data["client_id"];
            $site_id = $data["site_id"];

            $total = $this->Level_model->getTotalLevelsSite($data);

            $results = $this->Level_model->getLevelsSite($data);
        } else {
            $total = $this->Level_model->getTotalLevels($data);

            $results = $this->Level_model->getLevels($data);

            $client_id = "";
            $site_id = "";
        }

        if ($results) {
            foreach ($results as $result) {

                $this->data['levels'][] = array(
                    'level_id' => $result['_id'],
                    'level' => $result['level'],
                    'title' => $result['level_title'],
                    'exp' => number_format($result['exp'], 0),
                    'status' => ($result['status'] == false) ? $this->lang->line('text_disabled') : $this->lang->line('text_enabled'),
                    'selected' => $this->input->post('selected') && in_array($result['level_id'],
                            $this->input->post('selected')),
                );
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

        $config['total_rows'] = $total;
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

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'level';

        $this->data["all_levels"] = $this->Level_model->countLevels($client_id, $site_id);

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('level');
    }

    private function getForm($level_id = null)
    {

        $this->load->model('Image_model');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = array();
        }

        if (isset($this->error['exp'])) {
            $this->data['error_warning'] = $this->error['exp'];
        } else {
            $this->data['error_warning'] = array();
        }

        if (isset($this->error['level'])) {
            $this->data['error_warning'] = $this->error['level'];
        } else {
            $this->data['error_warning'] = array();
        }

        if (isset($level_id) && ($level_id != 0)) {
            if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
                $level_info = $this->Level_model->getLevelSite($level_id);
            } else {
                $level_info = $this->Level_model->getLevel($level_id);
            }

        }

        if (isset($level_id)) {
            $this->data['level_id'] = $level_id;
        } else {
            $this->data['level_id'] = null;
        }

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($level_info)) {
            $this->data['image'] = $level_info['image'];
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

        /*if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($level_info) && $level_info['image'] && (S3_IMAGE . $level_info['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $level_info['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($level_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }*/

        $this->data['no_image'] = S3_IMAGE . "cache/no_image-100x100.jpg";

        if ($this->input->post('level_title')) {
            $this->data['level_title'] = $this->input->post('level_title');
        } elseif (!empty($level_info)) {
            $this->data['level_title'] = $level_info['level_title'];
        } else {
            $this->data['level_title'] = '';
        }

        if ($this->input->post('exp')) {
            $this->data['exp'] = $this->input->post('exp');
        } elseif (!empty($level_info)) {
            $this->data['exp'] = $level_info['exp'];
        } else {
            $this->data['exp'] = null;
        }

        if ($this->input->post('level')) {
            $this->data['level'] = $this->input->post('level');
        } elseif (!empty($level_info)) {
            $this->data['level'] = $level_info['level'];
        } else {
            $this->data['level'] = null;
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($level_info)) {
            $this->data['status'] = $level_info['status'];
        } else {
            $this->data['status'] = false;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $this->data['main'] = 'level_form';

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('level_form');
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'level')) {
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
                'level') && $this->Feature_model->getFeatureExistByClientId($client_id, 'level')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

?>
