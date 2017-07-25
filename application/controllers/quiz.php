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
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');

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
                                                if(isset($ovalue['is_range_option']) && $ovalue['is_range_option'] == "on"){
                                                    if(!is_numeric($ovalue['range_min']) || !is_numeric($ovalue['range_max'])){
                                                        $this->data['message'] = "[Error in question '".$qqvalue["question"]."' ] ".$this->lang->line('error_range_error');
                                                    }
                                                    $option['is_range_option'] = true;
                                                } else {
                                                    $option['is_range_option'] = false;
                                                }

                                                if(isset($ovalue['is_text_option']) && $ovalue['is_text_option'] == "on"){
                                                    $option['is_text_option'] = true;
                                                } else {
                                                    $option['is_text_option'] = false;
                                                }

                                                if(isset($ovalue['terminate']) && $ovalue['terminate'] == "on"){
                                                    $option['terminate'] = true;
                                                } else {
                                                    $option['terminate'] = false;
                                                }

                                                if(!(isset($ovalue['option']) && $ovalue['option'])){
                                                    $option['option'] = "";
                                                }
                                                $option['option_id'] = new MongoId($okey);

                                                $options[] = $option;
                                            }
                                            if ($options) {
                                                $questions["options"] = $options;
                                            }
                                        } elseif($qqqkey == "is_multiple_choices"){
                                            $questions[$qqqkey] = $qqqvalue == "true" ? true:false;
                                        }
                                        else {
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
                        case 'tags':
                            $value = explode(',', $value);
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

    function jsonErrorResponse($msg = 'Error, invalid request format or missing parameter')
    {
        echo json_encode(
            array(
                'error' => 1,
                'success' => false,
                'msg' => $msg
            )
        );
    }

    private function push_validation_error(&$array,$key,$value){
        if(isset($array[$key])){
            $array[$key] = array_keys(array_flip(array_merge($array[$key],array($value))));
        }else{
            $array[$key] = array($value);
        }
    }

    private function convertData(&$quiz_info,$function = 'export',&$validation_result = null)
    {
        $client_id = $quiz_info['client_id'];
        $site_id = $quiz_info['site_id'];
        $qdata = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
        );

        if (isset($quiz_info['grades']) && $quiz_info['grades']) {
            foreach($quiz_info['grades'] as &$grade){
                if($function=='import') {
                    $grade['grade_id'] = new MongoId($grade['grade_id']['$id']);
                }

                // convert Rewards
                if(isset($grade['rewards']) && $grade['rewards']){

                    // convert Badge
                    if(isset($grade['rewards']['badge']) && $grade['rewards']['badge']){
                        foreach($grade['rewards']['badge'] as &$badge){
                            if($function == 'export') {
                                $badge_name = $this->Badge_model->getNameOfBadgeID($client_id, $site_id, $badge['badge_id']);
                                $badge['badge_name'] = $badge_name;
                            }else{
                                $badgeInfo = $this->Badge_model->getBadgeByName($client_id, $site_id, $badge['badge_name']);
                                if($badgeInfo){
                                    $badge['badge_id'] = $badgeInfo['badge_id'];
                                    unset($badge['badge_name']);
                                }else{
                                    $this->push_validation_error($validation_result, 'BADGE', $badge['badge_name']);
                                }
                            }
                        }
                    }

                    // convert Custom point
                    if(isset($grade['rewards']['custom']) && $grade['rewards']['custom']){
                        foreach($grade['rewards']['custom'] as &$custom){
                            if($function == 'export') {
                                $custom_name = $this->Reward_model->getClientRewardNameByRewardID($client_id, $site_id, $custom['custom_id']);
                                $custom['custom_name'] = $custom_name;
                            }else{
                                $customPoint_id = $this->Reward_model->getClientRewardIDByName($client_id, $site_id, $custom['custom_name']);
                                if($customPoint_id){
                                    $custom['custom_id'] = new MongoId($customPoint_id);
                                    unset($custom['custom_name']);
                                }else{
                                    $this->push_validation_error($validation_result, 'CUSTOM_POINT', $custom['custom_name']);
                                }
                            }
                        }
                    }


                }

                // convert Feedbacks
                if(isset($grade['feedbacks']) && $grade['feedbacks']){

                    // convert Email
                    if(isset($grade['feedbacks']['email']) && $grade['feedbacks']['email']){
                        $import_data = array();
                        foreach($grade['feedbacks']['email'] as $email => &$value){
                            if($function == 'export') {
                                if(isset($value['checked']) && $value['checked'] == "on") {
                                    $info = $this->Email_model->getTemplate($email);
                                    $value['template_name'] = $info['name'];
                                }else{
                                    unset($grade['feedbacks']['email'][$email]);
                                }
                            }else{
                                $email_template_id = $this->Email_model->getTemplateIDByName($site_id, $value['template_name']);
                                if($email_template_id){
                                    //unset($grade['feedbacks']['email'][$email]);
                                    $import_data[$email_template_id]['checked'] = "on";
                                    $import_data[$email_template_id]['subject'] = $value['subject'];

                                }else{
                                    $this->push_validation_error($validation_result, 'EMAIL', $value['template_name']);
                                }
                            }
                        }

                        if($function != 'export' ){
                            $grade['feedbacks']['email'] = $import_data;
                        }
                    }

                    // convert SMS
                    if(isset($grade['feedbacks']['sms']) && $grade['feedbacks']['sms']){
                        $import_data = array();
                        foreach($grade['feedbacks']['sms'] as $sms => &$value){
                            if($function == 'export') {
                                if(isset($value['checked']) && $value['checked'] == "on") {
                                    $info = $this->Sms_model->getTemplate($sms);
                                    $value['template_name'] = $info['name'];
                                }else{
                                    unset($grade['feedbacks']['sms'][$sms]);
                                }
                            }else{
                                $sms_template_id = $this->Sms_model->getTemplateIDByName($site_id, $value['template_name']);
                                if($sms_template_id){

                                    $import_data[$sms_template_id]['checked'] = "on";
                                    //unset($grade['feedbacks']['sms'][$sms]);
                                }else{
                                    $this->push_validation_error($validation_result, 'SMS', $value['template_name']);
                                }
                            }
                        }

                        if($function != 'export' ){
                            $grade['feedbacks']['sms'] = $import_data;
                        }
                    }

                    // convert Push
                    if(isset($grade['feedbacks']['push']) && $grade['feedbacks']['push']){
                        $import_data = array();
                        foreach($grade['feedbacks']['push'] as $push => &$value){
                            if($function == 'export') {
                                if(isset($value['checked']) && $value['checked'] == "on") {
                                    $info = $this->Push_model->getTemplate($push);
                                    $value['template_name'] = $info['name'];
                                }else{
                                    unset($grade['feedbacks']['push'][$push]);
                                }
                            }else{
                                $push_template_id = $this->Push_model->getTemplateIDByName($site_id, $value['template_name']);
                                if($push_template_id){

                                    $import_data[$push_template_id]['checked'] = "on";
                                    //unset($grade['feedbacks']['push'][$push]);
                                }else{
                                    $this->push_validation_error($validation_result, 'PUSH', $value['template_name']);
                                }
                            }
                        }

                        if($function != 'export' ){
                            $grade['feedbacks']['push'] = $import_data;
                        }
                    }

                }
            }
        }
    }

    public function importQuiz()
    {
        if (!$this->input->post('array_quizs')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission_import'));
            return;
        }

        $array_quizs = json_decode($this->input->post('array_quizs'),true);
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $validation_result = array();
        foreach($array_quizs as &$quiz){

            $quiz['client_id'] = $client_id;
            $quiz['site_id'] = $site_id;

            if (isset($quiz['questions']) && $quiz['questions']) {
                foreach ($quiz['questions'] as &$question) {
                    $question['question_id'] = new MongoId($question['question_id']['$id']);
                    if (isset($question['options']) && $question['options']) {
                        foreach ($question['options'] as &$option) {
                            $option['option_id'] = new MongoId($option['option_id']['$id']);
                        }
                    }
                }
            }

            $this->convertData($quiz, 'import', $validation_result);
        }

        if(!$validation_result){ // passed data validation
            foreach($array_quizs as $quiz2) {
                $import_result = $this->Quiz_model->addQuizToClient($quiz2);
            }
            $this->output->set_output(json_encode(array('status'=>'success')));
        }else{ // failed data validation
            $this->output->set_output(json_encode(array('status'=>'fail','results'=>$validation_result)));
        }
    }

    public function exportQuiz()
    {
        if (!$this->input->post('array_quizs')) {
            $this->jsonErrorResponse();
            return;
        }

        if (!$this->validateModify()) {
            $this->jsonErrorResponse($this->lang->line('error_permission_export'));
            return;
        }

        $array_quizs = array();
        foreach($this->input->post('array_quizs') as $quiz_id){
            if($quiz_id == "on")continue;

            $quiz_info = $this->Quiz_model->getQuiz($quiz_id);
            unset($quiz_info['_id']);

            $this->convertData($quiz_info);

            $quiz_info['client_id'] = null;
            $quiz_info['site_id'] = null;
            $quiz_info['date_start'] = $this->datetimeMongotoReadable($quiz_info['date_start']);
            $quiz_info['date_expire'] = $this->datetimeMongotoReadable($quiz_info['date_expire']);

            $array_quizs[] = $quiz_info;
        }

        $this->output->set_output(json_encode($array_quizs));
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = null;
        }
        return $dateTimeMongo;
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