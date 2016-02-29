<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Quiz extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Quiz_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("quiz", $lang['folder']);
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
            'sort' => 'sort_order'
        );

        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('quiz/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;

        if ($client_id) {
            $this->data['quizs'] = $this->Quiz_model->getQuizs($filter);
            $badge_list = $this->Badge_model->getBadgeBySiteId(array("site_id" => $site_id));
            $point_list = $this->Reward_model->getAnotherRewardBySiteId($site_id);
            foreach ($this->data['quizs'] as &$quiz) {
                $error = $this->checkQuizError($quiz, $badge_list, $point_list);
                $quiz['error'] = !empty($error) ? 'The following rewards are not available: ' . implode(',',
                        $error) : null;
            }
            $config['total_rows'] = $this->Quiz_model->getTotalQuizs($filter);
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

        $this->data['main'] = 'quiz';
        $this->render_page('template');
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm(0);
    }

    public function edit($quiz_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getForm($quiz_id);
    }

    private function getForm($quiz_id = null)
    {

        $this->load->model('Image_model');
        $this->load->model('Badge_model');
        $this->load->model('Reward_model');

        $quiz_info = array();

        if (isset($quiz_id) && ($quiz_id != 0)) {
            if ($this->User_model->getClientId()) {
                $quiz_info = $this->Quiz_model->getQuiz($quiz_id);
            } else {
                $quiz_info = $this->Quiz_model->getQuiz($quiz_id);
            }
        }

        $site_id = $this->User_model->getSiteId();

        $quizs = $this->Quiz_model->getTotalQuizs(array(
            'site_id' => $site_id
        ));

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');
        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $limit_quiz = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'quiz');

        $this->data['message'] = null;
        if ($limit_quiz && $quizs >= $limit_quiz) {
            $this->data['message'] = $this->lang->line('error_quiz_limit');
        }

        if ($this->input->post()) {
            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            $data = $this->input->post();

            $quiz = array();

            foreach ($data as $key => $value) {
                if ($key == "quiz") {
                    foreach ($value as $qkey => $qvalue) {
                        if ($qkey == "grades") {
                            foreach ($qvalue as $ggkey => $ggvalue) {
                                $grades = array();
                                $grades['grade_id'] = new MongoId($ggkey);
                                foreach ($ggvalue as $gggkey => $gggvalue) {

                                    if ($gggkey == "rewards") {

                                        foreach ($gggvalue as $rkey => $rvalue) {
                                            if ($rkey == "badge") {

                                                $badge = array();

                                                foreach ($rvalue as $bkey => $bvalue) {
                                                    if (!empty($bvalue)) {
                                                        $b["badge_id"] = new MongoId($bkey);
                                                        $b["badge_value"] = $bvalue;

                                                        $badge[] = $b;
                                                    }
                                                }
                                                if ($badge) {
                                                    $grades[$gggkey]["badge"] = $badge;
                                                }

                                            }
                                            if ($rkey == "exp" && !empty($rvalue)) {
                                                $grades[$gggkey]["exp"] = $rvalue;
                                            }
                                            if ($rkey == "point" && !empty($rvalue)) {
                                                $grades[$gggkey]["point"] = $rvalue;
                                            }
                                            if ($rkey == "custom") {
                                                $custom = array();

                                                foreach ($rvalue as $ckey => $cvalue) {
                                                    if (!empty($cvalue)) {
                                                        $c["custom_id"] = new MongoId($ckey);
                                                        $c["custom_value"] = $cvalue;

                                                        $custom[] = $c;
                                                    }
                                                }
                                                if ($custom) {
                                                    $grades[$gggkey]["custom"] = $custom;
                                                }

                                            }
                                        }

                                    } else {
                                        $grades[$gggkey] = $gggvalue;
                                    }

                                }
                                $quiz["grades"][] = $grades;
                            }

                        } else {
                            if ($qkey == "questions") {
                                foreach ($qvalue as $qqkey => $qqvalue) {
                                    $questions = array();
                                    $questions['question_id'] = new MongoId($qqkey);

                                    foreach ($qqvalue as $qqqkey => $qqqvalue) {

                                        if ($qqqkey == "options") {

                                            $options = array();

                                            foreach ($qqqvalue as $okey => $ovalue) {
                                                $option = $ovalue;
                                                $option['option_id'] = new MongoId($okey);

                                                $options[] = $option;
                                            }
                                            if ($options) {
                                                $questions["options"] = $options;
                                            }
                                        } else {
                                            $questions[$qqqkey] = $qqqvalue;
                                        }
                                    }
                                    $quiz["questions"][] = $questions;
                                }
                            }
                        }
                    }
                } else {
                    switch ($key) {
                        case 'status':
                            $value = ('true' === $value);
                            break;
                        case 'weight':
                            $value = $value ? intval($value) : 1;
                            break;
                        case 'type':
                            if (!$value) {
                                $value = 'quiz';
                            }
                            break;
                        default:
                            break;
                    }
                    $quiz[$key] = $value;
                }
            }

            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('description', $this->lang->line('description'),
                'trim|required|xss_clean');

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $quiz['client_id'] = $this->User_model->getClientId();
                $quiz['site_id'] = $this->User_model->getSiteId();
                $quiz['question_order'] = (isset($data['question_order']) && ($data['question_order'] == 'on')) ? true : false;
                if ($quiz_info) {
                    $this->Quiz_model->editQuizToClient($quiz_id, $quiz);
                } else {
                    $this->Quiz_model->addQuizToClient($quiz);
                }
                redirect('/quiz', 'refresh');
            }

            $quiz_info = array_merge($quiz_info, $quiz);
        }

        $this->data['quiz'] = $quiz_info;

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        $this->data['badge_list'] = array();
        $this->data['badge_list'] = $this->Badge_model->getBadgeBySiteId(array("site_id" => $data['site_id']));

        $this->data['point_list'] = array();
        $this->data['point_list'] = $this->Reward_model->getAnotherRewardBySiteId($data['site_id']);

        $this->load->model('Feature_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');
        $this->data['emails'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'email') ? $this->Email_model->listTemplatesBySiteId($data['site_id']) : null;
        $this->data['smses'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'sms') ? $this->Sms_model->listTemplatesBySiteId($data['site_id']) : null;
        $this->data['pushes'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'push') ? $this->Push_model->listTemplatesBySiteId($data['site_id']) : null;
        $this->data['client_id'] = $data['client_id'];
        $this->data['site_id'] = $data['site_id'];

        $this->data['main'] = 'quiz_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {

            foreach ($this->input->post('selected') as $quiz_id) {
                $this->Quiz_model->delete($quiz_id);
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/quiz', 'refresh');
        }

        $this->getList(0);
    }

    private function checkQuizError($quiz, $badge_list, $point_list)
    {
        $badges = array();
        $customs = array();
        if (!empty($quiz['grades'])) {
            foreach ($quiz['grades'] as $grade) {
                if (isset($grade["rewards"])) {
                    foreach ($grade["rewards"] as $rk => $rv) {
                        if ($rk == "custom") {
                            foreach ($rv as $b) {
                                array_push($customs, $b['custom_id']);
                            }
                        }
                        if ($rk == "badge") {
                            foreach ($rv as $b) {
                                array_push($badges, $b['badge_id']);
                            }
                        }
                    }
                }
            }
        }
        $badges_avail = array_map('index_badge_id', $badge_list);
        $customs_avail = array_map('index_reward_id', $point_list);
        return array_merge(array_diff($badges, $badges_avail), array_diff($customs, $customs_avail));
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'quiz')) {
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
                'quiz') && $this->Feature_model->getFeatureExistByClientId($client_id, 'quiz')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

function index_badge_id($obj)
{
    return $obj['badge_id'];
}

function index_reward_id($obj)
{
    return $obj['reward_id'];
}

?>