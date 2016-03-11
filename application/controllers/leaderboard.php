<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Leaderboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Leaderboard_model');
        $this->load->model('Goods_model');
        $this->load->model('Quest_model');
        $this->load->model('Feature_model');
        $this->load->model('Badge_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');
        $this->load->model('Store_org_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("leaderboard", $lang['folder']);
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
        $this->data['form'] = 'leaderboard/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();


        $this->load->model('Permission_model');
        $this->load->model('Plan_model');

        $this->data['message'] = null;


        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['month'] = (isset($data['month']) && $data['month']) ? new MongoDate(strtotime($data['month'])) : "";
                $data['status'] = $data['status'] == 'enable' ? true : false;
                $data['occur_once'] = $data['occur_once'] == 'true' ? true : false;

                $insert = $this->Leaderboard_model->createLeaderBoard($data);
                if ($insert) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success'));
                    redirect('/leaderboard', 'refresh');
                }
            }
        }
        $this->getForm();
    }

    public function update($leaderboard_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'leaderboard/update/' . $leaderboard_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $data = $this->input->post();

                $data['_id'] = $leaderboard_id;
                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['status'] = $data['status'] == 'enable' ? true : false;
                $data['month'] = (isset($data['month']) && $data['month']) ? new MongoDate(strtotime($data['month'])) : "";
                $data['occur_once'] = $data['occur_once'] == 'true' ? true : false;

                $update = $this->Leaderboard_model->updateLeaderBoard($data);
                if ($update) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                    redirect('/leaderboard', 'refresh');
                }
            }
        }

        $this->getForm($leaderboard_id);
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
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
        );

        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('leaderboard/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;

        if ($client_id) {
            $this->data['client_id'] = $client_id;

            $leaderboards = $this->Leaderboard_model->retrieveLeaderBoards($filter);

            foreach ($leaderboards as $key => $leaderboard) {
                if (isset($leaderboard['selected_org']) && ($leaderboard['selected_org'] != "")) {
                    $org_info = $this->Store_org_model->retrieveOrganizeById(new MongoID($leaderboard['selected_org']));
                    $leaderboards[$key]['selected_org'] = $org_info['name'];
                }
            }
            $this->data['leaderboards'] = $leaderboards;
            $config['total_rows'] = $this->Leaderboard_model->countLeaderBoards($client_id, $site_id);
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

        $this->data['main'] = 'leaderboard';
        $this->render_page('template');
    }

    public function getForm($leaderboard_id = null)
    {
        $this->data['main'] = 'leaderboard_form';
        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        if (isset($leaderboard_id) && ($leaderboard_id != 0)) {
            if ($this->User_model->getClientId()) {
                $leaderboard_info = $this->Leaderboard_model->retrieveLeaderBoard($leaderboard_id);
                $this->data = array_merge($this->data, $leaderboard_info);
            }
        } else {
            $this->data['status'] = true;
            $this->data['rankBy'] = "action";
        }

        $inputs = $this->input->post();
        if (isset($inputs) && $inputs) {
            foreach ($inputs as $key => $input) {
                $this->data[$key] = $input;
            }
        }


        if (isset($this->data['rewards'])) {
            foreach ($this->data['rewards'] as $rank => $reward_type) {
                if (!is_array($reward_type)) {
                    continue;
                }
                foreach ($reward_type as $type => $list) {
                    if (!is_array($list)) {
                        continue;
                    }
                    foreach ($list as $id => $item) {
                        switch ($type) {
                            case "goods":
                                $this->data['rewards'][$rank][$type][$id]['reward_data'] = $this->Goods_model->getGoodsOfClientPrivate($id);
                                break;
                            case "badges":
                                $this->data['rewards'][$rank][$type][$id]['reward_data'] = $this->Quest_model->getBadge(array(
                                    'client_id' => $this->data['client_id'],
                                    'site_id' => $this->data['site_id'],
                                    'badge_id' => $id
                                ));
                                break;
                            case "feedbacks":
                                $this->getFeedbackData($this->data['rewards'][$rank][$type][$id]);
                                break;
                        }
                        if (isset($this->data['rewards'][$rank][$type][$id]['reward_data']['image'])) {
                            $info = pathinfo($this->data['rewards'][$rank][$type][$id]['reward_data']['image']);
                            if (isset($info['extension'])) {
                                $extension = $info['extension'];
                                $new_image = 'cache/' . utf8_substr($this->data['rewards'][$rank][$type][$id]['reward_data']['image'],
                                        0,
                                        utf8_strrpos($this->data['rewards'][$rank][$type][$id]['reward_data']['image'],
                                            '.')) . '-100x100.' . $extension;
                                $this->data['rewards'][$rank][$type][$id]['reward_data']['image'] = S3_IMAGE . $new_image;
                            } else {
                                $this->data['rewards'][$rank][$type][$id]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                        }
                    }
                }
            }
        }

        $this->data['rewards_list'] = $this->Leaderboard_model->getRewards($this->data);

        $this->data['org_lists'] = $this->Store_org_model->retrieveOrganize($this->data['client_id'],
            $this->data['site_id']);

        $this->data['actions'] = $this->Quest_model->getActionsByClientSiteId($this->data);
        $this->data['exp_id'] = $this->Quest_model->getExpId($this->data);

        $this->data['point_id'] = $this->Quest_model->getPointId($this->data);

        $this->data['emails'] = $this->Feature_model->getFeatureExistByClientId($this->data['client_id'],
            'email') ? $this->Email_model->listTemplatesBySiteId($this->data['site_id']) : null;

        $this->data['smses'] = $this->Feature_model->getFeatureExistByClientId($this->data['client_id'],
            'sms') ? $this->Sms_model->listTemplatesBySiteId($this->data['site_id']) : null;

        $this->data['pushes'] = $this->Feature_model->getFeatureExistByClientId($this->data['client_id'],
            'push') ? $this->Push_model->listTemplatesBySiteId($this->data['site_id']) : null;

        $this->data['goods_items'] = $this->Goods_model->getGoodsBySiteId($this->data);

        $this->data['customPoints'] = $this->Quest_model->getCustomPoints($this->data);

        $this->data['badges'] = $this->Quest_model->getBadgesByClientSiteId($this->data);

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
            foreach ($this->input->post('selected') as $leaderboard_id) {
                $this->Leaderboard_model->deleteLeaderBoard($leaderboard_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/leaderboard', 'refresh');
        }

        $this->getList(0);
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'leaderboard')) {
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
                'leaderboard') && $this->Feature_model->getFeatureExistByClientId($client_id, 'leaderboard')
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function getFeedbackData(&$feedbacks) // Beware !! parameter has been passing by reference
    {
        $feedback_data = null;
        foreach ($feedbacks as $feedback) {
            switch ($feedback['feedback_type']) {
                case "EMAIL":
                    $feedback_data = $this->Email_model->getTemplate($feedback['template_id']);
                    break;
                case "SMS":
                    $feedback_data = $this->Sms_model->getTemplate($feedback['template_id']);
                    break;
                case "PUSH":
                    $feedback_data = $this->Push_model->getTemplate($feedback['template_id']);
                    break;
            }
            $feedback_data['message'] = $feedback_data && isset($feedback_data['body']) ? $feedback_data['body'] : '';
            $feedbacks[$feedback['template_id']]['feedback_data'] = $feedback_data;
        }
    }

}