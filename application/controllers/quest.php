<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Quest extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Quest_model');
        $this->load->model('Plan_model');
        $this->load->model('Badge_model');
        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Push_model');
        $this->load->model('Rule_model');
        $this->load->model('Goods_model');
        $this->load->model('Permission_model');
        $this->load->model('Store_org_model');
        $this->load->model('Feature_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("quest", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
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
        $this->load->model('Image_model');

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

        $config['base_url'] = site_url('quest/page');
        $config["uri_segment"] = 3;

        $config['total_rows'] = 0;

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        } else {
            $this->data['org_status'] = false;
        }

        if ($client_id) {
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);
            /* query required variables for validation of quest & mission */
            $questList = $this->makeListOfId($this->data['quests'], '_id');
            $actionList = $this->makeListOfId($this->Rule_model->getActionJigsawList($site_id, $client_id),
                'specific_id');
            $rewardList = $this->makeListOfId($this->Rule_model->getRewardJigsawList($site_id, $client_id),
                'specific_id');
            $badgeList = $this->makeListOfId($this->Badge_model->getBadgeBySiteId(array('site_id' => $site_id->{'$id'})),
                'badge_id');

            foreach ($this->data['quests'] as &$quest) {
//                $quest['image'] = $this->Image_model->resize($quest['image'], 100, 100);
                $info = pathinfo($quest['image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($quest['image'], 0,
                            utf8_strrpos($quest['image'], '.')) . '-100x100.' . $extension;
                    $quest['image'] = S3_IMAGE . $new_image;
                } else {
                    $quest['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                }
                /* check error in setting of quest & mission */
                $quest['error'] = $this->checkQuestError($quest, array(
                    'questList' => $questList,
                    'actionList' => $actionList,
                    'rewardList' => $rewardList,
                    'badgeList' => $badgeList,
                ));

                $org_name = null;
                if ($this->data['org_status']) {
                    if (isset($quest['organize_id']) && !empty($quest['organize_id'])) {
                        $org = $this->Store_org_model->retrieveOrganizeById($quest['organize_id']);
                        $org_name = $org["name"];
                    }
                }
                $quest['organize_name'] = $org_name;
            }

            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
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

        $this->data['main'] = 'quest';
        $this->render_page('template');
    }

    public function insert()
    {
        // Get Usage
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $quests = $this->Quest_model->getTotalQuestsClientSite(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $missions = $this->Quest_model->getTotalMissionsClientSite(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));

        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $lmts = $this->Plan_model->getPlanLimitById(
            $plan_id,
            'others',
            array('quest', 'mission')
        );

        $this->data['message'] = array();
        if ($lmts['quest'] && $quests >= $lmts['quest']) {
            $this->data['message'][] = $this->lang->line('error_quest_limit');
        }
        if ($lmts['mission'] && $missions >= $lmts['mission']) {
            $this->data['message'][] = $this->lang->line('error_mission_limit');
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'quest/insert';

        $this->form_validation->set_rules('quest_name', $this->lang->line('form_quest_name'),
            'trim|required|xss_clean');
        $missions_input = $this->input->post('missions');
        if ($missions_input != false && !empty($missions_input)) {
            foreach ($missions_input as $i => $mission) {
                if (!empty($mission['mission_name'])) {
                    $this->form_validation->set_rules('missions[' . $i . '][mission_name]',
                        $this->lang->line('form_mission_name'), 'trim|required|xss_clean|check_space');
                    $this->form_validation->set_rules('missions[' . $i++ . '][mission_number]',
                        $this->lang->line('form_mission_number'), 'trim|required|xss_clean|check_space');
                }
            }
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->form_validation->run() && $this->data['message'] == null) {
                $data = $this->input->post();

                if (!$this->validateModify()) {
                    $this->data['message'][] = $this->lang->line('error_permission');
                }

                if (!$this->data['message']) {
                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(),
                            'store_org') &&
                        !$this->input->post('global_quest')
                    ) {
                        if ($data['organize_role'] && !$data['organize_id']) {
                            $this->data['message'][] = $this->lang->line('text_fail_set_role');
                        }
                    }
                }

                if (!$this->data['message']) {
                    foreach ($data as $key => $value) {
                        if (in_array($key, array('condition', 'rewards', 'feedbacks', 'missions'))) {
                            $i = 0;
                            foreach ($value as $k => $v) {
                                foreach ($v as $ke => &$item) {
                                    if (in_array($ke,
                                            array('condition_id', 'reward_id', 'template_id')) && !empty($item)
                                    ) {
                                        $item = new MongoId($item);
                                    }
                                }

                                $qdata = array(
                                    'client_id' => $client_id,
                                    'site_id' => $site_id
                                );
                                unset($data[$key][$k]);
                                switch ($key) {
                                    case 'condition':
                                        $v["condition_data"] = $this->questObjectData($v, "condition_type",
                                            "condition_id", $qdata);
                                        break;
                                    case 'rewards':
                                        $v["reward_data"] = $this->questObjectData($v, "reward_type", "reward_id",
                                            $qdata);
                                        break;
                                    case 'feedbacks':
                                        $v["feedback_data"] = $this->questObjectData($v, "feedback_type", "template_id",
                                            $qdata);
                                        break;
                                    default:
                                        break;
                                }
                                $data[$key][$i] = $v;
                                if ($key == 'missions') {
                                    $data[$key][$i]['mission_number'] = $i + 1;
                                }

                                // clean if value is null
                                if (isset($v['reward_value']) && ($v['reward_value'] == null || $v['reward_value'] == '')) {
                                    unset($data[$key][$i]);
                                }
                                $i++;
                            }
                        }
                        if ($key == 'missions') {
                            $im = 0;
                            foreach ($value as $kk => $val) {

                                if (!$val['mission_name'] || !$val['mission_number']) {
                                    unset($data[$key][$kk - 1]);
                                    continue;
                                }

                                unset($data[$key][$kk]);
                                $data[$key][$im] = $val;
                                $data[$key][$im]['mission_id'] = new MongoId();
                                foreach ($val as $k => $v) {
                                    if (in_array($k, array('completion', 'rewards', 'feedbacks'))) {
                                        $i = 0;
                                        foreach ($v as $koo => $voo) {
                                            foreach ($voo as $kkk => &$vvv) {
                                                if (in_array($kkk, array(
                                                        'completion_id',
                                                        'reward_id',
                                                        'template_id'
                                                    )) && !empty($vvv)
                                                ) {
                                                    $vvv = new MongoId($vvv);
                                                }
                                                if ($kkk == 'completion_element_id') {
                                                    if (isset($vvv) && empty($vvv)) {
                                                        $vvv = new MongoId();
                                                    }
                                                }
                                            }
                                            $qdata = array(
                                                'client_id' => $client_id,
                                                'site_id' => $site_id
                                            );
                                            unset($data[$key][$im][$k][$koo]);
                                            switch ($k) {
                                                case 'completion':
                                                    $voo["completion_data"] = $this->questObjectData($voo,
                                                        "completion_type", "completion_id", $qdata);
                                                    break;
                                                case 'rewards':
                                                    $voo["reward_data"] = $this->questObjectData($voo, "reward_type",
                                                        "reward_id", $qdata);
                                                    break;
                                                case 'feedbacks':
                                                    $voo["feedback_data"] = $this->questObjectData($voo,
                                                        "feedback_type", "template_id", $qdata);
                                                    break;
                                                default:
                                                    break;
                                            }
                                            $data[$key][$im][$k][$i] = $voo;

                                            // clean if value is null
                                            if (isset($voo['reward_value']) && ($voo['reward_value'] == null || $voo['reward_value'] == '')) {
                                                unset($data[$key][$im][$k][$i]);
                                            }
                                            $i++;
                                        }
                                    }
                                }
                                $im++;

                            }
                        }
                    }
                    if (!isset($data['missions'])) {
                        $data['missions'] = array();
                    }

                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
                    ) {
                        if ($this->input->post('global_quest')) {
                            $data['organize_id'] = null;
                            $data['organize_role'] = null;
                        } else {
                            if ($data['organize_id']) {
                                $data['organize_id'] = new MongoID($data['organize_id']);
                            } else {
                                $data['organize_id'] = null;
                            }
                            if (!$data['organize_role']) {
                                $data['organize_role'] = null;
                            }
                        }
                    }

                    $data['status'] = (isset($data['status'])) ? true : false;
                    $data['mission_order'] = (isset($data['mission_order'])) ? true : false;
                    $data['tags'] = (isset($data['tags']) && $data['tags']) ? explode(',', $data['tags']) : null;
                    $data['date_added'] = new MongoDate(strtotime(date("Y-m-d H:i:s")));

                    $data['client_id'] = $client_id;
                    $data['site_id'] = $site_id;

                    $this->Quest_model->addQuestToClient($data);
                    redirect('/quest', 'refresh');
                } // end validation and message == null
            }
        }
        $this->getForm();
    }

    private function questObjectData($object_data, $key_type, $key_id, $query_data)
    {
        $condition_data = array();
        switch ($object_data[$key_type]) {
            case "QUEST":
                $query_data['quest_id'] = $object_data[$key_id];
                $query_data['short_detail'] = true;
                $quest_detail = $this->Quest_model->getQuestByClientSiteId($query_data);
                $condition_data = $quest_detail;
                break;
            case "POINT":
                $condition_data = array("name" => 'point');
                break;
            case "CUSTOM_POINT":
                $query_data['reward_id'] = $object_data[$key_id];
                $reward_detail = $this->Quest_model->getCustomPoint($query_data);
                $condition_data = array("name" => $reward_detail['name']);
                break;
            case "GOODS":
                $query_data['goods_id'] = $object_data[$key_id];
                $goods_detail = $this->Goods_model->getGoodsOfClientPrivate($query_data['goods_id']);
                unset($goods_detail['redeem']);
                $condition_data = $goods_detail;
                break;
            case "BADGE":
                $query_data['badge_id'] = $object_data[$key_id];
                $badge_detail = $this->Quest_model->getBadge($query_data);
                $condition_data = $badge_detail;
                break;
            case "QUIZ":
                $query_data['_id'] = $object_data[$key_id];
                $quiz_detail = $this->Quest_model->getQuiz($query_data);
                $condition_data = $quiz_detail;
                break;
            case "EXP":
                $condition_data = array("name" => 'exp');
                break;
            case "ACTION":
                $query_data['action_id'] = $object_data[$key_id];
                $action_detail = $this->Quest_model->getAction($query_data);
                $condition_data = $action_detail;
                break;
            case "EMAIL":
                $query_data['template_id'] = $object_data[$key_id];
                $template_detail = $this->Email_model->getTemplate($query_data['template_id']);
                $condition_data = array(
                    'name' => isset($template_detail['name']) ? $template_detail['name'] : '',
                    'message' => $template_detail && isset($template_detail['body']) ? $template_detail['body'] : ''
                );
                break;
            case "SMS":
                $query_data['template_id'] = $object_data[$key_id];
                $template_detail = $this->Sms_model->getTemplate($query_data['template_id']);
                $condition_data = array(
                    'name' => isset($template_detail['name']) ? $template_detail['name'] : '',
                    'message' => $template_detail && isset($template_detail['body']) ? $template_detail['body'] : ''
                );
                break;
            case "PUSH":
                $query_data['template_id'] = $object_data[$key_id];
                $template_detail = $this->Push_model->getTemplate($query_data['template_id']);
                $condition_data = array(
                    'name' => isset($template_detail['name']) ? $template_detail['name'] : '',
                    'message' => $template_detail && isset($template_detail['body']) ? $template_detail['body'] : ''
                );
                break;
        }
        return $condition_data;
    }

    public function getForm($quest_id = null)
    {

        $this->load->model('Image_model');

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        if (isset($quest_id) && !empty($quest_id)) {
            $data['quest_id'] = $quest_id;
            $editQuest = $this->Quest_model->getQuestByClientSiteId($data);
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') { // preserve user input even validation fails
                $editQuest = array(
                    'quest_name' => $this->input->post('quest_name'),
                    'description' => $this->input->post('description'),
                    'hint' => $this->input->post('hint'),
                    'mission_order' => $this->input->post('mission_order'),
                    'sort_order' => $this->input->post('sort_order'),
                    'status' => $this->input->post('status'),
                    'condition' => $this->input->post('condition'),
                    'rewards' => $this->input->post('rewards'),
                    'feedbacks' => $this->input->post('feedbacks'),
                    'missions' => $this->input->post('missions'),
                    'tags' => $this->input->post('tags'),
                );
            }
        }

        $this->load->model('Email_model');
        $this->load->model('Sms_model');
        $this->load->model('Image_model');
        $this->load->model('Level_model');
        $this->load->model('Push_model');

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($editQuest)) {
            $this->data['image'] = $editQuest['image'];
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

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (!empty($goods_info)) {
            $this->data['date_start'] = $goods_info['date_start'];
        } else {
            $this->data['date_start'] = "";
        }

        if ($this->input->post('date_end')) {
            $this->data['date_end'] = $this->input->post('date_end');
        } elseif (!empty($goods_info)) {
            $this->data['date_end'] = $goods_info['date_end'];
        } else {
            $this->data['date_end'] = "";
        }

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
            if ($this->input->post('organize_id')) {
                $this->data['organize_id'] = $this->input->post('organize_id');
            } elseif (!empty($editQuest) && isset($editQuest['organize_id'])) {
                $this->data['organize_id'] = $editQuest['organize_id'];
            } else {
                $this->data['organize_id'] = null;
            }

            if ($this->input->post('organize_role')) {
                $this->data['organize_role'] = $this->input->post('organize_role');
            } elseif (!empty($editQuest) && isset($editQuest['organize_role'])) {
                $this->data['organize_role'] = $editQuest['organize_role'];
            } else {
                $this->data['organize_role'] = null;
            }
        } else {
            $this->data['org_status'] = false;
        }

        if ($this->input->post('tags')) {
            $this->data['tags'] = $this->input->post('tags');
        } elseif (!empty($editQuest) && isset($editQuest['tags'])) {
            $this->data['tags'] = $editQuest['tags'];
        } else {
            $this->data['tags'] = null;
        }

        $this->data['levels'] = $this->Level_model->getLevelsSite($data);

        $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($data);

        $this->data['customPoints'] = $this->Quest_model->getCustomPoints($data);

        $this->data['badges'] = $this->Quest_model->getBadgesByClientSiteId($data);

        $results = $this->Goods_model->getGroupsAggregate($this->User_model->getSiteId());
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }

        $goods_list = $this->Goods_model->getGoodsBySiteId(array(
            'site_id' => $this->User_model->getSiteId(),
            'sort' => 'sort_order',
            '$nin' => $ids
        ));
        foreach ($goods_list as &$g) {
            $g['_id'] = $g['_id'] . "";
            $g['goods_id'] = $g['goods_id'] . "";
            $g['client_id'] = $g['client_id'] . "";
            $g['site_id'] = $g['site_id'] . "";
        }

        $this->data['goods_items'] = $goods_list;

        $this->data['quizs'] = $this->Quest_model->getQuizsByClientSiteId($data);

        $this->data['actions'] = $this->Quest_model->getActionsByClientSiteId($data);

        $this->data['exp_id'] = $this->Quest_model->getExpId($data);

        $this->data['point_id'] = $this->Quest_model->getPointId($data);

        $this->load->model('Feature_model');

        $this->data['emails'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'email') ? $this->Email_model->listTemplatesBySiteId($data['site_id']) : null;

        $this->data['smses'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'sms') ? $this->Sms_model->listTemplatesBySiteId($data['site_id']) : null;

        $this->data['pushes'] = $this->Feature_model->getFeatureExistByClientId($data['client_id'],
            'push') ? $this->Push_model->listTemplatesBySiteId($data['site_id']) : null;

        if (/*$quest_id != null &&*/
            isset($editQuest) && !empty($editQuest)
        ) {

            $this->data['editQuest']['quest_name'] = isset($editQuest['quest_name']) ? $editQuest['quest_name'] : null;
            $this->data['editQuest']['description'] = isset($editQuest['description']) ? $editQuest['description'] : null;
            $this->data['editQuest']['hint'] = isset($editQuest['hint']) ? $editQuest['hint'] : null;
            $this->data['editQuest']['mission_order'] = isset($editQuest['mission_order']) ? $editQuest['mission_order'] : false;
            $this->data['editQuest']['sort_order'] = isset($editQuest['sort_order']) ? $editQuest['sort_order'] : false;
            $this->data['editQuest']['status'] = isset($editQuest['status']) ? $editQuest['status'] : false;
            $this->data['editQuest']['tags'] = isset($editQuest['tags']) ? $editQuest['tags'] : null;

            $countQuest = 0;
            $countCustomPoints = 0;
            $countBadges = 0;
            $countQuizs = 0;

            $qdata = array(
                'client_id' => $data['client_id'],
                'site_id' => $data['site_id'],
            );

            if (isset($editQuest['condition'])) {
                if (is_array($editQuest['condition'])) {
                    foreach ($editQuest['condition'] as $condition) {
                        if ($condition['condition_type'] == 'DATETIME_START') {
                            $this->data['editDateStartCon']['condition_type'] = $condition['condition_type'];
                            $this->data['editDateStartCon']['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editDateStartCon']['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                        }

                        if ($condition['condition_type'] == 'DATETIME_END') {
                            $this->data['editDateEndCon']['condition_type'] = $condition['condition_type'];
                            $this->data['editDateEndCon']['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editDateEndCon']['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                        }

                        if ($condition['condition_type'] == 'LEVEL_START') {
                            $this->data['editLevelStartCon']['condition_type'] = $condition['condition_type'];
                            $this->data['editLevelStartCon']['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editLevelStartCon']['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                        }
                        if ($condition['condition_type'] == 'LEVEL_END') {
                            $this->data['editLevelEndCon']['condition_type'] = $condition['condition_type'];
                            $this->data['editLevelEndCon']['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editLevelEndCon']['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                        }
                        if ($condition['condition_type'] == 'QUEST') {
                            $this->data['editQuestConditionCon'][$countQuest]['condition_type'] = $condition['condition_type'];
                            $this->data['editQuestConditionCon'][$countQuest]['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editQuestConditionCon'][$countQuest]['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                            $this->data['editQuestConditionCon'][$countQuest]['condition_data'] = isset($condition['condition_data']) ? $condition['condition_data'] : $this->questObjectData($this->data['editQuestConditionCon'][$countQuest],
                                "condition_type", "condition_id", $qdata);

                            $condition_data = $this->data['editQuestConditionCon'][$countQuest]['condition_data'];
                            if (isset($condition_data['image'])) {
                                $info = pathinfo($condition_data['image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($condition_data['image'], 0,
                                            utf8_strrpos($condition_data['image'], '.')) . '-100x100.' . $extension;
                                    $this->data['editQuestConditionCon'][$countQuest]['condition_data']['image'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['editQuestConditionCon'][$countQuest]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            } else {
                                $this->data['editQuestConditionCon'][$countQuest]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                            $countQuest++;
                        }
                        if ($condition['condition_type'] == 'POINT') {
                            $this->data['editPointsCon']['condition_type'] = $condition['condition_type'];
                            $this->data['editPointsCon']['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editPointsCon']['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                        }
                        if ($condition['condition_type'] == 'CUSTOM_POINT') {
                            $this->data['editCustomPointsCon'][$countCustomPoints]['condition_type'] = $condition['condition_type'];
                            $this->data['editCustomPointsCon'][$countCustomPoints]['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editCustomPointsCon'][$countCustomPoints]['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                            $countCustomPoints++;
                        }
                        if ($condition['condition_type'] == 'QUIZ') {
                            $this->data['editQuizCon'][$countQuizs]['condition_type'] = $condition['condition_type'];
                            $this->data['editQuizCon'][$countQuizs]['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editQuizCon'][$countQuizs]['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                            $this->data['editQuizCon'][$countQuizs]['condition_data'] = isset($condition['condition_data']) ? $condition['condition_data'] : $this->questObjectData($this->data['editQuizCon'][$countQuizs],
                                "condition_type", "condition_id", $qdata);

                            $condition_data = $this->data['editQuizCon'][$countQuizs]['condition_data'];
                            if (isset($condition_data['image'])) {
                                $info = pathinfo($condition_data['image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($condition_data['image'], 0,
                                            utf8_strrpos($condition_data['image'], '.')) . '-100x100.' . $extension;
                                    $this->data['editQuizCon'][$countQuizs]['condition_data']['image'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['editQuizCon'][$countQuizs]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            } else {
                                $this->data['editQuizCon'][$countQuizs]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                            $countQuizs++;
                        }
                        if ($condition['condition_type'] == 'BADGE') {
                            $this->data['editBadgeCon'][$countBadges]['condition_type'] = $condition['condition_type'];
                            $this->data['editBadgeCon'][$countBadges]['condition_id'] = isset($condition['condition_id']) ? $condition['condition_id'] : null;
                            $this->data['editBadgeCon'][$countBadges]['condition_value'] = isset($condition['condition_value']) ? $condition['condition_value'] : null;
                            $this->data['editBadgeCon'][$countBadges]['condition_data'] = isset($condition['condition_data']) ? $condition['condition_data'] : $this->questObjectData($this->data['editBadgeCon'][$countBadges],
                                "condition_type", "condition_id", $qdata);

                            $condition_data = $this->data['editBadgeCon'][$countBadges]['condition_data'];
                            if (isset($condition_data['image'])) {
                                $info = pathinfo($condition_data['image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($condition_data['image'], 0,
                                            utf8_strrpos($condition_data['image'], '.')) . '-100x100.' . $extension;
                                    $this->data['editBadgeCon'][$countBadges]['condition_data']['image'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['editBadgeCon'][$countBadges]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            } else {
                                $this->data['editBadgeCon'][$countBadges]['condition_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                            $countBadges++;
                        }
                    }
                }
            }

            if (isset($editQuest['rewards'])) {
                $countCustomPoints = 0;
                $countBadges = 0;
                $countGoods = 0;
                if (is_array($editQuest['rewards'])) {
                    foreach ($editQuest['rewards'] as $reward) {
                        if ($reward['reward_type'] == 'POINT') {
                            $this->data['editPointsRew']['reward_type'] = $reward['reward_type'];
                            $this->data['editPointsRew']['reward_id'] = isset($reward['reward_id']) ? $reward['reward_id'] : null;
                            $this->data['editPointsRew']['reward_value'] = isset($reward['reward_value']) ? $reward['reward_value'] : null;
                        }
                        if ($reward['reward_type'] == 'EXP') {
                            $this->data['editExpRew']['reward_type'] = $reward['reward_type'];
                            $this->data['editExpRew']['reward_id'] = isset($reward['reward_id']) ? $reward['reward_id'] : null;
                            $this->data['editExpRew']['reward_value'] = isset($reward['reward_value']) ? $reward['reward_value'] : null;
                        }
                        if ($reward['reward_type'] == 'CUSTOM_POINT') {
                            $this->data['editCustomPointsRew'][$countCustomPoints]['reward_type'] = $reward['reward_type'];
                            $this->data['editCustomPointsRew'][$countCustomPoints]['reward_id'] = isset($reward['reward_id']) ? $reward['reward_id'] : null;
                            $this->data['editCustomPointsRew'][$countCustomPoints]['reward_value'] = isset($reward['reward_value']) ? $reward['reward_value'] : null;
                            $countCustomPoints++;
                        }

                        if ($reward['reward_type'] == 'GOODS') {
                            $this->data['editGoodsRew'][$countGoods]['reward_type'] = $reward['reward_type'];
                            $this->data['editGoodsRew'][$countGoods]['reward_id'] = isset($reward['reward_id']) ? $reward['reward_id'] : null;
                            $this->data['editGoodsRew'][$countGoods]['reward_value'] = isset($reward['reward_value']) ? $reward['reward_value'] : null;
                            $this->data['editGoodsRew'][$countGoods]['reward_data'] = isset($reward['reward_data']) ? $reward['reward_data'] : $this->questObjectData($this->data['editGoodsRew'][$countGoods],
                                "reward_type", "reward_id", $qdata);

                            $reward_data = $this->data['editGoodsRew'][$countGoods]['reward_data'];
                            if (isset($reward_data['image'])) {
                                $info = pathinfo($reward_data['image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($reward_data['image'], 0,
                                            utf8_strrpos($reward_data['image'], '.')) . '-100x100.' . $extension;
                                    $this->data['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            } else {
                                $this->data['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                            $countGoods++;
                        }
                        if ($reward['reward_type'] == 'BADGE') {
                            $this->data['editBadgeRew'][$countBadges]['reward_type'] = $reward['reward_type'];
                            $this->data['editBadgeRew'][$countBadges]['reward_id'] = isset($reward['reward_id']) ? $reward['reward_id'] : null;
                            $this->data['editBadgeRew'][$countBadges]['reward_value'] = isset($reward['reward_value']) ? $reward['reward_value'] : null;
                            $this->data['editBadgeRew'][$countBadges]['reward_data'] = isset($reward['reward_data']) ? $reward['reward_data'] : $this->questObjectData($this->data['editBadgeRew'][$countBadges],
                                "reward_type", "reward_id", $qdata);

                            $reward_data = $this->data['editBadgeRew'][$countBadges]['reward_data'];
                            if (isset($reward_data['image'])) {
                                $info = pathinfo($reward_data['image']);
                                if (isset($info['extension'])) {
                                    $extension = $info['extension'];
                                    $new_image = 'cache/' . utf8_substr($reward_data['image'], 0,
                                            utf8_strrpos($reward_data['image'], '.')) . '-100x100.' . $extension;
                                    $this->data['editBadgeRew'][$countBadges]['reward_data']['image'] = S3_IMAGE . $new_image;
                                } else {
                                    $this->data['editBadgeRew'][$countBadges]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                }
                            } else {
                                $this->data['editBadgeRew'][$countBadges]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            }
                            $countBadges++;
                        }
                    }
                }
            }

            if (isset($editQuest['feedbacks'])) {
                $countEmails = 0;
                $countSmses = 0;
                $countPushes = 0;
                if (is_array($editQuest['feedbacks'])) {
                    foreach ($editQuest['feedbacks'] as $feedback) {
                        if ($feedback['feedback_type'] == 'EMAIL') {
                            $this->data['editEmailRew'][$countEmails]['feedback_type'] = $feedback['feedback_type'];
                            $this->data['editEmailRew'][$countEmails]['template_id'] = isset($feedback['template_id']) ? $feedback['template_id'] : null;
                            $this->data['editEmailRew'][$countEmails]['subject'] = isset($feedback['subject']) ? $feedback['subject'] : null;
                            $this->data['editEmailRew'][$countEmails]['feedback_data'] = isset($feedback['feedback_data']) ? $feedback['feedback_data'] : $this->questObjectData($this->data['editEmailRew'][$countEmails],
                                "feedback_type", "template_id", $qdata);
                            $countEmails++;
                        }
                        if ($feedback['feedback_type'] == 'SMS') {
                            $this->data['editSmsRew'][$countSmses]['feedback_type'] = $feedback['feedback_type'];
                            $this->data['editSmsRew'][$countSmses]['template_id'] = isset($feedback['template_id']) ? $feedback['template_id'] : null;
                            $this->data['editSmsRew'][$countSmses]['feedback_data'] = isset($feedback['feedback_data']) ? $feedback['feedback_data'] : $this->questObjectData($this->data['editSmsRew'][$countSmses],
                                "feedback_type", "template_id", $qdata);
                            $countSmses++;
                        }
                        if ($feedback['feedback_type'] == 'PUSH') {
                            $this->data['editPushRew'][$countPushes]['feedback_type'] = $feedback['feedback_type'];
                            $this->data['editPushRew'][$countPushes]['template_id'] = isset($feedback['template_id']) ? $feedback['template_id'] : null;
                            $this->data['editPushRew'][$countPushes]['feedback_data'] = isset($feedback['feedback_data']) ? $feedback['feedback_data'] : $this->questObjectData($this->data['editPushRew'][$countPushes],
                                "feedback_type", "template_id", $qdata);
                            $countPushes++;
                        }
                    }
                }
            }

            if (isset($editQuest['missions'])) {

                $missionCount = 0;
                if (is_array($editQuest['missions'])) {
                    foreach ($editQuest['missions'] as $mission) {
                        $this->data['editMission'][$missionCount]['mission_id'] = isset($mission['mission_id']) ? $mission['mission_id'] : $missionCount + 1;
                        $this->data['editMission'][$missionCount]['mission_name'] = $mission['mission_name'];
                        $this->data['editMission'][$missionCount]['mission_number'] = $mission['mission_number'];
                        $this->data['editMission'][$missionCount]['description'] = $mission['description'];
                        $this->data['editMission'][$missionCount]['hint'] = $mission['hint'];

                        if (isset($mission['image'])) {
                            $info = pathinfo($mission['image']);
                            if (isset($info['extension'])) {
                                $extension = $info['extension'];
                                $new_image = 'cache/' . utf8_substr($mission['image'], 0,
                                        utf8_strrpos($mission['image'], '.')) . '-100x100.' . $extension;
                                $this->data['editMission'][$missionCount]['image'] = S3_IMAGE . $new_image;
                                $this->data['editMission'][$missionCount]['imagereal'] = $mission['image'];
                            } else {
                                $this->data['editMission'][$missionCount]['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                $this->data['editMission'][$missionCount]['imagereal'] = S3_IMAGE . "no_image.jpg";
                            }
                        } else {
                            $this->data['editMission'][$missionCount]['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                            $this->data['editMission'][$missionCount]['imagereal'] = S3_IMAGE . "no_image.jpg";
                        }
                        /*if (!empty($mission['image']) && $mission['image'] && (S3_IMAGE . $mission['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $mission['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $this->data['editMission'][$missionCount]['image'] = $this->Image_model->resize($mission['image'], 100, 100);
                            $this->data['editMission'][$missionCount]['imagereal'] = $mission['image'];
                        }else{
                            $this->data['editMission'][$missionCount]['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                        }*/

                        if (isset($mission['completion'])) {
                            $countActions = 0;
                            $countCustomPoints = 0;
                            $countBadge = 0;
                            $countQuizs = 0;
                            foreach ($mission['completion'] as $mm) {
                                if ($mm['completion_type'] == 'ACTION') {
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_type'] = $mm['completion_type'];
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_value'] = $mm['completion_value'];
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_id'] = $mm['completion_id'];
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_filter'] = isset($mm['completion_filter']) ? $mm['completion_filter'] : null;
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_op'] = isset($mm['completion_op']) ? $mm['completion_op'] : "sum";
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_string'] = isset($mm['completion_string']) ? $mm['completion_string'] : "";
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['filtered_param'] = isset($mm['filtered_param']) ? $mm['filtered_param'] : null;
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_sum_param'] = isset($mm['completion_sum_param']) ? $mm['completion_sum_param'] : "";
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_title'] = $mm['completion_title'];
                                    $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_element_id'] = $mm['completion_element_id'];
                                    $countActions++;
                                }

                                if ($mm['completion_type'] == 'POINT') {
                                    $this->data['editMission'][$missionCount]['editPoint']['completion_type'] = $mm['completion_type'];
                                    $this->data['editMission'][$missionCount]['editPoint']['completion_value'] = $mm['completion_value'];
                                    $this->data['editMission'][$missionCount]['editPoint']['completion_id'] = $mm['completion_id'];
                                    $this->data['editMission'][$missionCount]['editPoint']['completion_title'] = $mm['completion_title'];
                                }

                                if ($mm['completion_type'] == 'CUSTOM_POINT') {
                                    $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_type'] = $mm['completion_type'];
                                    $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_value'] = $mm['completion_value'];
                                    $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_id'] = $mm['completion_id'];
                                    $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_title'] = $mm['completion_title'];
                                    $countCustomPoints++;
                                }
                                if ($mm['completion_type'] == 'QUIZ') {
                                    $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_type'] = $mm['completion_type'];
                                    $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_value'] = $mm['completion_value'];
                                    $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_id'] = $mm['completion_id'];
                                    $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_title'] = $mm['completion_title'];
                                    $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_data'] = isset($mm['completion_data']) ? $mm['completion_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editQuiz'][$countQuizs],
                                        "completion_type", "completion_id", $qdata);

                                    $completion_data = $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_data'];
                                    if (isset($completion_data['image'])) {
                                        $info = pathinfo($completion_data['image']);
                                        if (isset($info['extension'])) {
                                            $extension = $info['extension'];
                                            $new_image = 'cache/' . utf8_substr($completion_data['image'], 0,
                                                    utf8_strrpos($completion_data['image'],
                                                        '.')) . '-100x100.' . $extension;
                                            $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_data']['image'] = S3_IMAGE . $new_image;
                                        } else {
                                            $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                        }
                                    } else {
                                        $this->data['editMission'][$missionCount]['editQuiz'][$countQuizs]['completion_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                    }
                                    $countQuizs++;
                                }
                                if ($mm['completion_type'] == 'BADGE') {
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_type'] = $mm['completion_type'];
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_value'] = $mm['completion_value'];
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_id'] = $mm['completion_id'];
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_title'] = $mm['completion_title'];
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data'] = isset($mm['completion_data']) ? $mm['completion_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editBadge'][$countBadge],
                                        "completion_type", "completion_id", $qdata);

                                    $completion_data = $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data'];
                                    if (isset($completion_data['image'])) {
                                        $info = pathinfo($completion_data['image']);
                                        if (isset($info['extension'])) {
                                            $extension = $info['extension'];
                                            $new_image = 'cache/' . utf8_substr($completion_data['image'], 0,
                                                    utf8_strrpos($completion_data['image'],
                                                        '.')) . '-100x100.' . $extension;
                                            $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data']['image'] = S3_IMAGE . $new_image;
                                        } else {
                                            $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                        }
                                    } else {
                                        $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                    }
                                    $countBadge++;
                                }
                            }
                        }

                        $countBadge = 0;
                        $countCustomPoints = 0;
                        $countGoods = 0;
                        if (isset($mission['rewards'])) {
                            foreach ($mission['rewards'] as $rr) {
                                if ($rr['reward_type'] == 'POINT') {
                                    $this->data['editMission'][$missionCount]['editPointRew']['reward_type'] = $rr['reward_type'];
                                    $this->data['editMission'][$missionCount]['editPointRew']['reward_value'] = $rr['reward_value'];
                                    $this->data['editMission'][$missionCount]['editPointRew']['reward_id'] = $rr['reward_id'];
                                }

                                if ($rr['reward_type'] == 'EXP') {
                                    $this->data['editMission'][$missionCount]['editExpRew']['reward_type'] = $rr['reward_type'];
                                    $this->data['editMission'][$missionCount]['editExpRew']['reward_value'] = $rr['reward_value'];
                                    $this->data['editMission'][$missionCount]['editExpRew']['reward_id'] = $rr['reward_id'];
                                }

                                if ($rr['reward_type'] == 'CUSTOM_POINT') {
                                    $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_type'] = $rr['reward_type'];
                                    $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_value'] = $rr['reward_value'];
                                    $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_id'] = $rr['reward_id'];
                                    $countCustomPoints++;
                                }

                                if ($rr['reward_type'] == 'GOODS') {
                                    $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_type'] = $rr['reward_type'];
                                    $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_value'] = $rr['reward_value'];
                                    $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_id'] = $rr['reward_id'];
                                    $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_data'] = isset($rr['reward_data']) ? $rr['reward_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods],
                                        "reward_type", "reward_id", $qdata);

                                    $reward_data = $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_data'];
                                    if (isset($reward_data['image'])) {
                                        $info = pathinfo($reward_data['image']);
                                        if (isset($info['extension'])) {
                                            $extension = $info['extension'];
                                            $new_image = 'cache/' . utf8_substr($reward_data['image'], 0,
                                                    utf8_strrpos($reward_data['image'],
                                                        '.')) . '-100x100.' . $extension;
                                            $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . $new_image;
                                        } else {
                                            $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                        }
                                    } else {
                                        $this->data['editMission'][$missionCount]['editGoodsRew'][$countGoods]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                    }
                                    $countGoods++;
                                }

                                if ($rr['reward_type'] == 'BADGE') {
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_type'] = $rr['reward_type'];
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_value'] = $rr['reward_value'];
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_id'] = $rr['reward_id'];
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data'] = isset($rr['reward_data']) ? $rr['reward_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge],
                                        "reward_type", "reward_id", $qdata);

                                    $reward_data = $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data'];
                                    if (isset($reward_data['image'])) {
                                        $info = pathinfo($reward_data['image']);
                                        if (isset($info['extension'])) {
                                            $extension = $info['extension'];
                                            $new_image = 'cache/' . utf8_substr($reward_data['image'], 0,
                                                    utf8_strrpos($reward_data['image'],
                                                        '.')) . '-100x100.' . $extension;
                                            $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data']['image'] = S3_IMAGE . $new_image;
                                        } else {
                                            $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                        }
                                    } else {
                                        $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data']['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                                    }
                                    $countBadge++;
                                }
                            }
                        }

                        $countEmails = 0;
                        $countSmses = 0;
                        $countPushes = 0;
                        if (isset($mission['feedbacks'])) {
                            foreach ($mission['feedbacks'] as $rr) {
                                if ($rr['feedback_type'] == 'EMAIL') {
                                    $this->data['editMission'][$missionCount]['editEmailRew'][$countEmails]['feedback_type'] = $rr['feedback_type'];
                                    $this->data['editMission'][$missionCount]['editEmailRew'][$countEmails]['template_id'] = $rr['template_id'];
                                    $this->data['editMission'][$missionCount]['editEmailRew'][$countEmails]['subject'] = $rr['subject'];
                                    $this->data['editMission'][$missionCount]['editEmailRew'][$countEmails]['feedback_data'] = isset($rr['feedback_data']) ? $rr['feedback_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editEmailRew'][$countEmails],
                                        "feedback_type", "template_id", $qdata);
                                    $countEmails++;
                                }
                                if ($rr['feedback_type'] == 'SMS') {
                                    $this->data['editMission'][$missionCount]['editSmsRew'][$countSmses]['feedback_type'] = $rr['feedback_type'];
                                    $this->data['editMission'][$missionCount]['editSmsRew'][$countSmses]['template_id'] = $rr['template_id'];
                                    $this->data['editMission'][$missionCount]['editSmsRew'][$countSmses]['feedback_data'] = isset($rr['feedback_data']) ? $rr['feedback_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editSmsRew'][$countSmses],
                                        "feedback_type", "template_id", $qdata);
                                    $countSmses++;
                                }
                                if ($rr['feedback_type'] == 'PUSH') {
                                    $this->data['editMission'][$missionCount]['editPushRew'][$countPushes]['feedback_type'] = $rr['feedback_type'];
                                    $this->data['editMission'][$missionCount]['editPushRew'][$countPushes]['template_id'] = $rr['template_id'];
                                    $this->data['editMission'][$missionCount]['editPushRew'][$countPushes]['feedback_data'] = isset($rr['feedback_data']) ? $rr['feedback_data'] : $this->questObjectData($this->data['editMission'][$missionCount]['editPushRew'][$countPushes],
                                        "feedback_type", "template_id", $qdata);
                                    $countPushes++;
                                }
                            }
                        }

                        $missionCount++;
                    }
                }
            }
        }

        $this->data['main'] = 'quest_form';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function autocomplete()
    {
        $json = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('filter_name')) {

            if ($this->input->get('filter_name')) {
                $filter_name = $this->input->get('filter_name');
            } else {
                $filter_name = null;
            }

            $data = array(
                'filter_name' => $filter_name
            );

            if ($client_id) {
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $results_quest = $this->Quest_model->getQuestsByClientSiteId($data);
            } else {
                //For admins because there is no client id?
            }

            foreach ($results_quest as $result) {
                $json[] = array(
                    'name' => html_entity_decode($result['quest_name'], ENT_QUOTES, 'UTF-8'),
                    // 'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    // 'icon' => html_entity_decode($result['icon'], ENT_QUOTES, 'UTF-8'),
                    // 'color' => html_entity_decode($result['color'], ENT_QUOTES, 'UTF-8'),
                    // 'sort_order' => html_entity_decode($result['sort_order'], ENT_QUOTES, 'UTF-8'),
                    'status' => html_entity_decode($result['status'], ENT_QUOTES, 'UTF-8'),
                );
            }
        }
        $this->output->set_output(json_encode($json));
    }

    public function increase_order($quest_id)
    {
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->increaseOrderByOneClient($quest_id, $client_id);
        } else {
            $this->Quest_model->increaseOrderByOne($quest_id);
        }
        $json = array('success' => 'Okay!');
        $this->output->set_output(json_encode($json));
    }

    public function decrease_order($quest_id)
    {
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->decreaseOrderByOneClient($quest_id, $client_id);
        } else {
            $this->Quest_model->decreaseOrderByOne($quest_id);
        }
        $json = array('success' => 'Okay!');
        $this->output->set_output(json_encode($json));
    }

    public function _getListForAjax($offset)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $this->load->model('Image_model');

        $this->load->library('pagination');

        $config['per_page'] = 10;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
        );
        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('action/page');
        $config["uri_segment"] = 3;

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        } else {
            $this->data['org_status'] = false;
        }

        if ($client_id) {
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);

            foreach ($this->data['quests'] as &$quest) {
                //                $quest['image'] = $this->Image_model->resize($quest['image'], 100, 100);
                $info = pathinfo($quest['image']);
                if (isset($info['extension'])) {
                    $extension = $info['extension'];
                    $new_image = 'cache/' . utf8_substr($quest['image'], 0,
                            utf8_strrpos($quest['image'], '.')) . '-100x100.' . $extension;
                    $quest['image'] = S3_IMAGE . $new_image;
                } else {
                    $quest['image'] = S3_IMAGE . "cache/no_image-100x100.jpg";
                }

                $org_name = null;
                if ($this->data['org_status']) {
                    if (isset($quest['organize_id']) && !empty($quest['organize_id'])) {
                        $org = $this->Store_org_model->retrieveOrganizeById($quest['organize_id']);
                        $org_name = $org["name"];
                    }
                }
                $quest['organize_name'] = $org_name;
            }

            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
        }

        $this->pagination->initialize($config);
    }

    public function getListForAjax($offset)
    {
        $this->_getListForAjax($offset);
        $this->render_page('quest_ajax');
    }

    public function getListForAjaxReset($offset = 0)
    {
        $this->_getListForAjax($offset);
        $this->render_page('quest_ajax_reset');
    }

    public function delete()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['message'] = null;

        if (!$this->validateModify()) {
            $this->error['message'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['message'] == null) {

            if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
                foreach ($this->input->post('selected') as $quest_id) {
                    $this->Quest_model->deleteQuestClient($quest_id);
                }
            } else {
                /*
                foreach ($this->input->post('selected') as $action_id) {
                    $this->Action_model->delete($action_id);
                }
                 */
            }

            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));

            redirect('/quest', 'refresh');
        }

        $this->getList(0);
    }

    public function reset()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        if (!$this->validateModify()) {
            $this->output->set_output(json_encode(array(
                "success" => false,
                "message" => $this->lang->line('error_permission')
            )));
            return;
        }

        $success = false;
        $message = array();
        $selected = $this->input->post('selected');
        if (!$selected) {
            $message[] = 'No quest has been selected';
        }
        $pb_player_id = $this->input->post('pb_player_id');
        if (!$pb_player_id) {
            $message[] = '"pb_player_id" is not sent';
        }
        if ($this->User_model->getUserGroupId() == $this->User_model->getAdminGroupID()) {
            $message[] = 'Super-admin cannot use this function';
        }
        if ($selected && $pb_player_id && $this->error['warning'] == null && $this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
            $success = true;
            if (is_array($selected)) {
                foreach ($selected as $quest_id) {
                    $success = $success && $this->Quest_model->resetQuestClient($quest_id, $pb_player_id);
                }
            } else {
                $success = $this->Quest_model->resetQuestClient($selected, $pb_player_id);
            }
            if (!$success) {
                $message[] = 'There is an error in processing quest reset for the player';
            } else {
                $message[] = 'Successfully reset the selected quests for the player';
            }
        }

        $this->output->set_output(json_encode(array("success" => $success, "message" => implode(', ', $message))));
    }

    public function edit($quest_id)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $missions = $this->Quest_model->getTotalMissionsClientSite(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));

        $this_missions = $this->Quest_model->getTotalMissionsInQuest(array(
            'quest_id' => new MongoId($quest_id)
        ));

        // Get Limit
        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
        $lmts = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'mission');

        $this->data['message'] = array();
        if (isset($lmts['mission']) && $missions >= $lmts['mission']) {
            $this->data['message'][] = $this->lang->line('error_mission_limit');
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'quest/edit/' . $quest_id;

        $this->form_validation->set_rules('quest_name', $this->lang->line('form_quest_name'),
            'trim|required|xss_clean');
        $missions_input = $this->input->post('missions');
        if ($missions_input != false && !empty($missions_input)) {
            foreach ($missions_input as $i => $mission) {
                $this->form_validation->set_rules('missions[' . $i . '][mission_name]',
                    $this->lang->line('form_mission_name'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('missions[' . $i++ . '][mission_number]',
                    $this->lang->line('form_mission_number'), 'trim|required|xss_clean');
            }
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->form_validation->run() && $this->data['message'] == null) {
                $data = $this->input->post();
                $new_missions = isset($data['missions']) ? sizeof($data['missions']) : 0;
                $all_missions = ($missions - $this_missions) + $new_missions;

                if (isset($lmts['mission'])
                    && isset($data['status'])
                    && $all_missions > $lmts['mission']
                ) {
                    $this->data['message'][] = $this->lang->line('error_mission_limit');
                }

                if (!$this->validateModify()) {
                    $this->data['message'][] = $this->lang->line('error_permission');
                }

                if (!$this->data['message']) {
                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(),
                            'store_org') &&
                        !$this->input->post('global_quest')
                    ) {
                        if ($data['organize_role'] && !$data['organize_id']) {
                            $this->data['message'][] = $this->lang->line('text_fail_set_role');
                        }
                    }
                }

                if (!$this->data['message']) {
                    foreach ($data as $key => $value) {
                        if (in_array($key, array('condition', 'rewards', 'feedbacks', 'missions'))) {
                            $i = 0;
                            foreach ($value as $k => $v) {
                                foreach ($v as $ke => &$item) {
                                    if (in_array($ke,
                                            array('condition_id', 'reward_id', 'template_id')) && !empty($item)
                                    ) {
                                        $item = new MongoId($item);
                                    }
                                }
                                $qdata = array(
                                    'client_id' => $client_id,
                                    'site_id' => $site_id
                                );
                                unset($data[$key][$k]);
                                switch ($key) {
                                    case 'condition':
                                        $v["condition_data"] = $this->questObjectData($v, "condition_type",
                                            "condition_id", $qdata);
                                        break;
                                    case 'rewards':
                                        $v["reward_data"] = $this->questObjectData($v, "reward_type", "reward_id",
                                            $qdata);
                                        break;
                                    case 'feedbacks':
                                        $v["feedback_data"] = $this->questObjectData($v, "feedback_type", "template_id",
                                            $qdata);
                                        break;
                                    default:
                                        break;
                                }
                                $data[$key][$i] = $v;
                                if ($key == 'missions') {
                                    $data[$key][$i]['mission_number'] = $i + 1;
                                }

                                // clean if value is null
                                if (isset($v['reward_value']) && ($v['reward_value'] == null || $v['reward_value'] == '')) {
                                    unset($data[$key][$i]);
                                }
                                $i++;
                            }
                        }
                        if ($key == 'missions') {
                            $im = 0;
                            foreach ($value as $kk => $val) {
                                if (!$val['mission_name'] || !$val['mission_number']) {
                                    unset($data[$key][$kk - 1]);
                                    continue;
                                }

                                unset($data[$key][$kk]);
                                $data[$key][$im] = $val;
                                try {
                                    $data[$key][$im]['mission_id'] = new MongoId($kk);
                                } catch (MongoException $ex) {
                                    $data[$key][$im]['mission_id'] = new MongoId();
                                }

                                foreach ($val as $k => $v) {
                                    if (in_array($k, array('completion', 'rewards', 'feedbacks'))) {
                                        $i = 0;
                                        foreach ($v as $koo => $voo) {
                                            foreach ($voo as $kkk => &$vvv) {
                                                if (in_array($kkk, array(
                                                        'completion_id',
                                                        'reward_id',
                                                        'template_id'
                                                    )) && !empty($vvv)
                                                ) {
                                                    $vvv = new MongoId($vvv);
                                                }
                                                if ($kkk == 'completion_element_id') {
                                                    if (isset($vvv) && !empty($vvv)) {
                                                        $vvv = new MongoId($vvv);
                                                    } else {
                                                        $vvv = new MongoId();
                                                    }
                                                }
                                            }
                                            $qdata = array(
                                                'client_id' => $client_id,
                                                'site_id' => $site_id
                                            );
                                            unset($data[$key][$im][$k][$koo]);
                                            switch ($k) {
                                                case 'completion':
                                                    $voo["completion_data"] = $this->questObjectData($voo,
                                                        "completion_type", "completion_id", $qdata);
                                                    break;
                                                case 'rewards':
                                                    $voo["reward_data"] = $this->questObjectData($voo, "reward_type",
                                                        "reward_id", $qdata);
                                                    break;
                                                case 'feedbacks':
                                                    $voo["feedback_data"] = $this->questObjectData($voo,
                                                        "feedback_type", "template_id", $qdata);
                                                    break;
                                                default:
                                                    break;
                                            }
                                            $data[$key][$im][$k][$i] = $voo;

                                            // clean if value is null
                                            if (isset($voo['reward_value']) && ($voo['reward_value'] == null || $voo['reward_value'] == '')) {
                                                unset($data[$key][$im][$k][$i]);
                                            }
                                            $i++;
                                        }
                                    }
                                }
                                $im++;

                            }
                        }
                    }

                    $data['status'] = (isset($data['status'])) ? true : false;
                    $data['mission_order'] = (isset($data['mission_order'])) ? true : false;
                    $data['tags'] = (isset($data['tags']) && $data['tags']) ? explode(',', $data['tags']) : null;
                    $data['client_id'] = $client_id;
                    $data['site_id'] = $site_id;

                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
                    ) {
                        if ($this->input->post('global_quest')) {
                            $data['organize_id'] = null;
                            $data['organize_role'] = null;
                        } else {
                            if ($data['organize_id']) {
                                $data['organize_id'] = new MongoID($data['organize_id']);
                            } else {
                                $data['organize_id'] = null;
                            }
                            if (!$data['organize_role']) {
                                $data['organize_role'] = null;
                            }
                        }
                    }

                    if ($this->Quest_model->editQuestToClient($quest_id, $data)) {
                        redirect('/quest', 'refresh');
                    } else {
                        echo "Did not update";
                    }
                }
            }
        }

        if (!empty($client_id) && !empty($site_id)) {
            $this->getForm($quest_id);
        }
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'quest')) {
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
                'quest') && $this->Feature_model->getFeatureExistByClientId($client_id, 'quest')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function playQuest($quest_id = null)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $raw_result = $this->curl(
            API_SERVER . "/Engine/quest",
            array(
                "client_id" => strval($client_id),
                "site_id" => strval($site_id),
                "quest_id" => strval($quest_id)
            ));

        try {
            $obj_result = json_decode($raw_result);

            // if success, assume that this quest ok
            if ($obj_result->success) {
                $this->output->set_output(json_encode(array("success" => true)));
            } else {
                $this->output->set_output(json_encode(array("success" => false)));
            }
        } catch (Exception $e) {
            $this->output->set_output(json_encode(array("success" => false)));
        }
    }

    private function checkQuestError($quest, $params)
    {
        $questList = $params['questList'];
        $actionList = $params['actionList'];
        $rewardList = $params['rewardList'];
        $badgeList = $params['badgeList'];
        $error = array();
        /* check condition of the quest */
        if (array_key_exists('condition', $quest) && is_array($quest['condition'])) {
            foreach ($quest['condition'] as $condition) {
                switch ($condition['condition_type']) {
                    case 'DATETIME_START':
                    case 'DATETIME_END':
                    case 'LEVEL_START':
                    case 'LEVEL_END':
                        /* nothing to check */
                        break;
                    case 'QUEST':
                        if (empty($condition['condition_id'])) {
                            $error[] = '[CONDITION] [condition_id] for ' . $condition['condition_data']['name'] . ' is missing';
                        } else {
                            if (!$questList || !in_array($condition['condition_id']->{'$id'}, $questList)) {
                                $error[] = '[CONDITION] ' . $condition['condition_type'] . ' [' . $condition['condition_data']['quest_name'] . '] is invalid';
                            }
                        }
                        break;
                    case 'POINT':
                    case 'CUSTOM_POINT':
                        if (empty($condition['condition_id'])) {
                            $error[] = '[CONDITION] [condition_id] for ' . $condition['condition_data']['name'] . ' is missing';
                        } else {
                            if (!$rewardList || !in_array($condition['condition_id']->{'$id'}, $rewardList)) {
                                $error[] = '[CONDITION] ' . $condition['condition_type'] . ' [' . $condition['condition_data']['name'] . '] is invalid';
                            }
                        }
                        break;
                    case 'BADGE':
                        if (empty($condition['condition_id'])) {
                            $error[] = '[CONDITION] [condition_id] for ' . $condition['condition_data']['name'] . ' is missing';
                        } else {
                            if (!$badgeList || !in_array($condition['condition_id']->{'$id'}, $badgeList)) {
                                $error[] = '[CONDITION] ' . $condition['condition_type'] . ' [' . $condition['condition_data']['name'] . '] is invalid';
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        /* check rewards of the quest */
        if (array_key_exists('rewards', $quest) && is_array($quest['rewards'])) {
            foreach ($quest['rewards'] as $reward) {
                switch ($reward['reward_type']) {
                    case 'EXP':
                    case 'POINT':
                    case 'CUSTOM_POINT':
                        if (empty($reward['reward_id'])) {
                            $error[] = '[REWARD] [reward_id] for ' . $reward['reward_data']['name'] . ' is missing';
                        } else {
                            if (!$rewardList || !in_array($reward['reward_id']->{'$id'}, $rewardList)) {
                                $error[] = '[REWARD] ' . $reward['reward_type'] . ' [' . $reward['reward_data']['name'] . '] is invalid';
                            }
                        }
                        break;
                    case 'BADGE':
                        if (empty($reward['reward_id'])) {
                            $error[] = '[REWARD] [reward_id] for ' . $reward['reward_data']['name'] . ' is missing';
                        } else {
                            if (!$badgeList || !in_array($reward['reward_id']->{'$id'}, $badgeList)) {
                                $error[] = '[REWARD] ' . $reward['reward_type'] . ' [' . $reward['reward_data']['name'] . '] is invalid';
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        /* check missions */
        if (array_key_exists('missions', $quest) && is_array($quest['missions'])) {
            foreach ($quest['missions'] as $i => $mission) {
                /* check missions's completion */
                if (array_key_exists('completion', $mission) && is_array($mission['completion'])) {
                    foreach ($mission['completion'] as $j => $completion) {
                        switch ($completion['completion_type']) {
                            case 'ACTION':
                                if (empty($completion['completion_id'])) {
                                    $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] [completion_id] for ' . $completion['completion_data']['name'] . ' is missing';
                                } else {
                                    if (!$actionList || !in_array($completion['completion_id']->{'$id'}, $actionList)) {
                                        $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] ' . $completion['completion_type'] . ' [' . $completion['completion_data']['name'] . '] is invalid';
                                    }
                                }
                                break;
                            case 'POINT':
                            case 'CUSTOM_POINT':
                                if (empty($completion['completion_id'])) {
                                    $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] [completion_id] for ' . $completion['completion_data']['name'] . ' is missing';
                                } else {
                                    if (!$rewardList || !in_array($completion['completion_id']->{'$id'}, $rewardList)) {
                                        $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] ' . $completion['completion_type'] . ' [' . $completion['completion_data']['name'] . '] is invalid';
                                    }
                                }
                                break;
                            case 'BADGE':
                                if (empty($completion['completion_id'])) {
                                    $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] [completion_id] for ' . $completion['completion_data']['name'] . ' is missing';
                                } else {
                                    if (!$badgeList || !in_array($completion['completion_id']->{'$id'}, $badgeList)) {
                                        $error[] = '[M' . strval($i) . ',' . strval($j) . ':COMPLETION] ' . $completion['completion_type'] . ' [' . $completion['completion_data']['name'] . '] is invalid';
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
                /* check missions's rewards */
                if (array_key_exists('rewards', $mission) && is_array($mission['rewards'])) {
                    foreach ($mission['rewards'] as $j => $reward) {
                        switch ($reward['reward_type']) {
                            case 'EXP':
                            case 'POINT':
                            case 'CUSTOM_POINT':
                                if (empty($reward['reward_id'])) {
                                    $error[] = '[M' . strval($i) . ',' . strval($j) . ':REWARD] [reward_id] for ' . (isset($reward['reward_data']['name']) ? $reward['reward_data']['name'] : '#no-name') . ' is missing';
                                } else {
                                    if (!$rewardList || !in_array($reward['reward_id']->{'$id'}, $rewardList)) {
                                        $error[] = '[M' . strval($i) . ',' . strval($j) . ':REWARD] ' . $reward['reward_type'] . ' [' . (isset($reward['reward_data']['name']) ? $reward['reward_data']['name'] : '#no-name') . '] is invalid';
                                    }
                                }
                                break;
                            case 'BADGE':
                                if (empty($reward['reward_id'])) {
                                    $error[] = '[M' . strval($i) . ',' . strval($j) . ':REWARD] [reward_id] for ' . (isset($reward['reward_data']['name']) ? $reward['reward_data']['name'] : '#no-name') . ' is missing';
                                } else {
                                    if (!$badgeList || !in_array($reward['reward_id']->{'$id'}, $badgeList)) {
                                        $error[] = '[M' . strval($i) . ',' . strval($j) . ':REWARD] ' . $reward['reward_type'] . ' [' . (isset($reward['reward_data']['name']) ? $reward['reward_data']['name'] : '#no-name') . '] is invalid';
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        return implode(', ', $error);
    }

    private function makeListOfId($arr, $field)
    {
        if (!$arr || !is_array($arr)) {
            return null;
        }
        $ret = array();
        foreach ($arr as $each) {
            $ret[] = $each instanceof MongoID ? $each[$field]->{'$id'} : $each[$field];
        }
        return $ret;
    }

    private function curl($url, $data)
    {
        $data = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }

}
