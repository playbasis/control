<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Quest extends MY_Controller
{

	public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Quest_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("quest", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index(){
    	$this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {

        /*
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }
        */

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    public function getList($offset){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $this->load->model('Image_model');

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($editQuest) && $editQuest['image'] && (S3_IMAGE . $editQuest['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $editQuest['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($editQuest['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        $this->load->library('pagination');

        $config['per_page'] = 10;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id'=>$client_id,
            'site_id'=>$site_id,
            'sort'=>'sort_order'
        );

        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('quest/page');
        $config["uri_segment"] = 3;

        if($client_id){
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);

            foreach($this->data['quests'] as &$quest){
                $quest['image'] = $this->Image_model->resize($quest['image'], 100, 100);
            }

            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
        }

        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

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

        $this->pagination->initialize($config);
    	
    	$this->data['main'] = 'quest';
        $this->render_page('template');

    }

    public function insert(){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'quest/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();

            // echo "<pre>";
            //     var_dump($data);
            // echo "</pre>";

            

            foreach($data as $key => $value){
                if($key == 'condition' || $key == 'rewards' || $key == 'missions'){
                    $i = 0;
                    foreach($value as $k => $v){
                        foreach($v as $ke => &$item){
                            if(($ke == 'condition_id' || $ke == 'reward_id') && !empty($item)){
                                $item = new MongoId($item);
                            }                            
                        }
                        if(in_array('DATETIME_START', $v)){
                            $v['condition_value'] = new MongoDate(strtotime(date($v['condition_value']." 00:00:00")));
                        }
                        if(in_array('DATETIME_END', $v)){
                            $v['condition_value'] = new MongoDate(strtotime(date($v['condition_value']." 23:59:59")));   
                        }
                        $qdata = array(
                            'client_id' => $client_id,
                            'site_id' => $site_id
                        );
                        unset($data[$key][$k]);
                        if($key == 'condition'){
                            $v["condition_data"] = $this->questObjectData($v, "condition_type", "condition_id", $qdata);
                        }
                        if($key == 'rewards'){
                            $v["reward_data"] = $this->questObjectData($v, "reward_type", "reward_id", $qdata);
                        }
                        $data[$key][$i] = $v;
                        if($key == 'missions'){
                            $data[$key][$i]['mission_number'] = $i + 1;
                        }
                        $i++;
                    }
                }
                if($key == 'missions'){
                    $im = 0;
                    foreach($value as $kk => $val){         

                        unset($data[$key][$kk]);
                        $data[$key][$im] = $val;
                        $data[$key][$im]['mission_id'] = new MongoId();
                        foreach($val as $k => $v){
                            if($k == 'completion' || $k == 'rewards'){
                                $i = 0;
                                foreach($v as $koo => $voo){                                    
                                    foreach($voo as $kkk => &$vvv){
                                        if(($kkk == 'completion_id' || $kkk == 'reward_id') && !empty($vvv)){
                                            $vvv = new MongoId($vvv);
                                        }
                                        if($kkk == 'completion_element_id'){
                                            if(isset($vvv) && empty($vvv)){
                                                $vvv = new MongoId();
                                            }
                                        }
                                    }
                                    $qdata = array(
                                        'client_id' => $client_id,
                                        'site_id' => $site_id
                                    );
                                    unset($data[$key][$im][$k][$koo]);
                                    if($k == 'completion'){
                                        $voo["completion_data"] = $this->questObjectData($voo, "completion_type", "completion_id", $qdata);
                                    }
                                    if($k == 'rewards'){
                                        $voo["reward_data"] = $this->questObjectData($voo, "reward_type", "reward_id", $qdata);
                                    }
                                    $data[$key][$im][$k][$i] = $voo;
                                    $i++;
                                }    
                            }
                        }
                        $im++;
                        
                    }
                }
            }

            $data['status'] = (isset($data['status']))?true:false;
            $data['mission_order'] = (isset($data['mission_order']))?true:false;

            $data['date_added'] = new MongoDate(strtotime(date("Y-m-d H:i:s")));

            $data['client_id'] = $client_id;
            $data['site_id'] = $site_id;

//             echo "<pre>";
//                 var_dump($data);
//             echo "</pre>";

            $this->Quest_model->addQuestToClient($data);
            redirect('/quest', 'refresh');
            
        }else{
            $this->getForm();        
        }
    }

    private function questObjectData($object_data, $key_type, $key_id, $query_data){
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
            case "BADGE":
                $query_data['badge_id'] = $object_data[$key_id];
                $badge_detail = $this->Quest_model->getBadge($query_data);
                $condition_data = $badge_detail;
                break;
            case "EXP":
                $condition_data = array("name" => 'exp');
                break;
            case "ACTION":
                $query_data['action_id'] = $object_data[$key_id];
                $action_detail = $this->Quest_model->getAction($query_data);
                $condition_data = $action_detail;
                break;
        }
        return $condition_data;
    }

    public function getForm($quest_id = null){

        $this->load->model('Image_model');

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        if(isset($quest_id) && !empty($quest_id)){
            $data['quest_id'] = $quest_id;
            $editQuest = $this->Quest_model->getQuestByClientSiteId($data);
        }


        $this->load->model('Image_model');
        $this->load->model('Level_model');

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
        } elseif (!empty($editQuest)) {
            $this->data['image'] = $editQuest['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->input->post('image') && (S3_IMAGE . $this->input->post('image') != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $this->input->post('image') != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($this->input->post('image'), 100, 100);
        } elseif (!empty($editQuest) && $editQuest['image'] && (S3_IMAGE . $editQuest['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $editQuest['image'] != 'HTTP/1.0 403 Forbidden')) {
            $this->data['thumb'] = $this->Image_model->resize($editQuest['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->Image_model->resize('no_image.jpg', 100, 100);
        }

        if ($this->input->post('date_start')) {
            $this->data['date_start'] = $this->input->post('date_start');
        } elseif (!empty($goods_info)) {
            $this->data['date_start'] = $goods_info['date_start'];
        } else {
            $this->data['date_start'] = "-";
        }

        if ($this->input->post('date_end')) {
            $this->data['date_end'] = $this->input->post('date_end');
        } elseif (!empty($goods_info)) {
            $this->data['date_end'] = $goods_info['date_end'];
        } else {
            $this->data['date_end'] = "-";
        }

        $this->data['levels'] = $this->Level_model->getLevelsSite($data);

        $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($data);

        $this->data['customPoints'] = $this->Quest_model->getCustomPoints($data);

        $this->data['badges'] = $this->Quest_model->getBadgesByClientSiteId($data);

        $this->data['actions'] = $this->Quest_model->getActionsByClientSiteId($data);

        $this->data['exp_id'] = $this->Quest_model->getExpId($data);

        $this->data['point_id'] = $this->Quest_model->getPointId($data);

        if($quest_id != null && isset($editQuest) && !empty($editQuest)){
            // $data['quest_id'] = $quest_id;
            // $editQuest = $this->Quest_model->getQuestByClientSiteId($data);

            $this->data['editQuest']['quest_name'] = isset($editQuest['quest_name'])?$editQuest['quest_name']:null;
            $this->data['editQuest']['description'] = isset($editQuest['description'])?$editQuest['description']:null;
            $this->data['editQuest']['hint'] = isset($editQuest['hint'])?$editQuest['hint']:null;
            $this->data['editQuest']['mission_order'] = isset($editQuest['mission_order'])?$editQuest['mission_order']:false;
            $this->data['editQuest']['sort_order'] = isset($editQuest['sort_order'])?$editQuest['sort_order']:false;
            $this->data['editQuest']['status'] = isset($editQuest['status'])?$editQuest['status']:false;

            $countQuest = 0;
            $countCustomPoints = 0;
            $countBadges = 0;
            if(isset($editQuest['condition'])){
                foreach($editQuest['condition'] as $condition){
                    if($condition['condition_type'] == 'DATETIME_START'){
                        $this->data['editDateStartCon']['condition_type'] = $condition['condition_type'];
                        $this->data['editDateStartCon']['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editDateStartCon']['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                    }

                    if($condition['condition_type'] == 'DATETIME_END'){
                        $this->data['editDateEndCon']['condition_type'] = $condition['condition_type'];
                        $this->data['editDateEndCon']['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editDateEndCon']['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                    }    

                    if($condition['condition_type'] == 'LEVEL_START'){
                        $this->data['editLevelStartCon']['condition_type'] = $condition['condition_type'];
                        $this->data['editLevelStartCon']['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editLevelStartCon']['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                    }
                    if($condition['condition_type'] == 'LEVEL_END'){
                        $this->data['editLevelEndCon']['condition_type'] = $condition['condition_type'];
                        $this->data['editLevelEndCon']['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editLevelEndCon']['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                    }
                    if($condition['condition_type'] == 'QUEST'){
                        $this->data['editQuestConditionCon'][$countQuest]['condition_type'] = $condition['condition_type']; 
                        $this->data['editQuestConditionCon'][$countQuest]['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editQuestConditionCon'][$countQuest]['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;

                        if (!empty($condition['condition_data']['image']) && $condition['condition_data']['image'] && (S3_IMAGE . $condition['condition_data']['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $condition['condition_data']['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $this->data['editQuestConditionCon'][$countQuest]['condition_data']['image'] = $this->Image_model->resize($condition['condition_data']['image'], 100, 100);
                        } else {
                            $this->data['editQuestConditionCon'][$countQuest]['condition_data']['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                        }

                        $countQuest++;
                    }
                    if($condition['condition_type'] == 'POINT'){
                        $this->data['editPointsCon']['condition_type'] = $condition['condition_type']; 
                        $this->data['editPointsCon']['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editPointsCon']['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                    }
                    if($condition['condition_type'] == 'CUSTOM_POINT'){
                        $this->data['editCustomPointsCon'][$countCustomPoints]['condition_type'] = $condition['condition_type']; 
                        $this->data['editCustomPointsCon'][$countCustomPoints]['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editCustomPointsCon'][$countCustomPoints]['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                        $countCustomPoints++;
                    }
                    if($condition['condition_type'] == 'BADGE'){
                        $this->data['editBadgeCon'][$countBadges]['condition_type'] = $condition['condition_type']; 
                        $this->data['editBadgeCon'][$countBadges]['condition_id'] = isset($condition['condition_id'])?$condition['condition_id']:null;
                        $this->data['editBadgeCon'][$countBadges]['condition_value'] = isset($condition['condition_value'])?$condition['condition_value']:null;
                        $this->data['editBadgeCon'][$countBadges]['condition_data'] = isset($condition['condition_data'])?$condition['condition_data']:null;

                        if (!empty($condition['condition_data']['image']) && $condition['condition_data']['image'] && (S3_IMAGE . $condition['condition_data']['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $condition['condition_data']['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $this->data['editBadgeCon'][$countBadges]['condition_data']['image'] = $this->Image_model->resize($condition['condition_data']['image'], 100, 100);
                        } else {
                            $this->data['editBadgeCon'][$countBadges]['condition_data']['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                        }

                        $countBadges++;
                    }
                }    
            }

            if(isset($editQuest['rewards'])){
                $countCustomPoints = 0;
                $countBadges = 0;
                foreach($editQuest['rewards'] as $reward){
                    if($reward['reward_type'] == 'POINT'){
                        $this->data['editPointsRew']['reward_type'] = $reward['reward_type']; 
                        $this->data['editPointsRew']['reward_id'] = isset($reward['reward_id'])?$reward['reward_id']:null;
                        $this->data['editPointsRew']['reward_value'] = isset($reward['reward_value'])?$reward['reward_value']:null;
                    }
                    if($reward['reward_type'] == 'EXP'){
                        $this->data['editExpRew']['reward_type'] = $reward['reward_type']; 
                        $this->data['editExpRew']['reward_id'] = isset($reward['reward_id'])?$reward['reward_id']:null;
                        $this->data['editExpRew']['reward_value'] = isset($reward['reward_value'])?$reward['reward_value']:null;
                    }
                    if($reward['reward_type'] == 'CUSTOM_POINT'){
                        $this->data['editCustomPointsRew'][$countCustomPoints]['reward_type'] = $reward['reward_type']; 
                        $this->data['editCustomPointsRew'][$countCustomPoints]['reward_id'] = isset($reward['reward_id'])?$reward['reward_id']:null;
                        $this->data['editCustomPointsRew'][$countCustomPoints]['reward_value'] = isset($reward['reward_value'])?$reward['reward_value']:null;
                        $countCustomPoints++;
                    }
                    if($reward['reward_type'] == 'BADGE'){
                        $this->data['editBadgeRew'][$countBadges]['reward_type'] = $reward['reward_type']; 
                        $this->data['editBadgeRew'][$countBadges]['reward_id'] = isset($reward['reward_id'])?$reward['reward_id']:null;
                        $this->data['editBadgeRew'][$countBadges]['reward_value'] = isset($reward['reward_value'])?$reward['reward_value']:null;
                        $this->data['editBadgeRew'][$countBadges]['reward_data'] = isset($reward['reward_data'])?$reward['reward_data']:null;

                        if (!empty($reward['reward_data']['image']) && $reward['reward_data']['image'] && (S3_IMAGE . $reward['reward_data']['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $reward['reward_data']['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $this->data['editBadgeRew'][$countBadges]['reward_data']['image'] = $this->Image_model->resize($reward['reward_data']['image'], 100, 100);
                        } else {
                            $this->data['editBadgeRew'][$countBadges]['reward_data']['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                        }

                        $countBadges++;
                    }
                }
            }

            if(isset($editQuest['missions'])){

                $missionCount = 0;
                foreach($editQuest['missions'] as $mission){
                    $this->data['editMission'][$missionCount]['mission_id'] = $mission['mission_id'];
                    $this->data['editMission'][$missionCount]['mission_name'] = $mission['mission_name'];
                    $this->data['editMission'][$missionCount]['mission_number'] = $mission['mission_number'];
                    $this->data['editMission'][$missionCount]['description'] = $mission['description'];
                    $this->data['editMission'][$missionCount]['hint'] = $mission['hint'];

                    if (!empty($mission['image']) && $mission['image'] && (S3_IMAGE . $mission['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $mission['image'] != 'HTTP/1.0 403 Forbidden')) {
                        $this->data['editMission'][$missionCount]['image'] = $this->Image_model->resize($mission['image'], 100, 100);
                        $this->data['editMission'][$missionCount]['imagereal'] = $mission['image'];
                    }else{
                        $this->data['editMission'][$missionCount]['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                    }

                    if(isset($mission['completion'])){
                        $countActions = 0;
                        $countCustomPoints = 0;
                        $countBadge = 0;
                        foreach($mission['completion'] as $mm){
                            if($mm['completion_type'] == 'ACTION'){
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_type'] = $mm['completion_type'];
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_value'] = $mm['completion_value'];
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_id'] = $mm['completion_id'];
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_filter'] = $mm['completion_filter'];
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_title'] = $mm['completion_title'];
                                $this->data['editMission'][$missionCount]['editAction'][$countActions]['completion_element_id'] = $mm['completion_element_id'];

                                $countActions++;
                            }

                            if($mm['completion_type'] == 'POINT'){
                                $this->data['editMission'][$missionCount]['editPoint']['completion_type'] = $mm['completion_type'];
                                $this->data['editMission'][$missionCount]['editPoint']['completion_value'] = $mm['completion_value'];
                                $this->data['editMission'][$missionCount]['editPoint']['completion_id'] = $mm['completion_id'];
                                $this->data['editMission'][$missionCount]['editPoint']['completion_title'] = $mm['completion_title'];
                            }

                            if($mm['completion_type'] == 'CUSTOM_POINT'){
                                $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_type'] = $mm['completion_type'];
                                $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_value'] = $mm['completion_value'];
                                $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_id'] = $mm['completion_id'];
                                $this->data['editMission'][$missionCount]['editCustomPoint'][$countCustomPoints]['completion_title'] = $mm['completion_title'];
                                $countCustomPoints++;
                            }

                            if($mm['completion_type'] == 'BADGE'){
                                $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_type'] = $mm['completion_type'];
                                $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_value'] = $mm['completion_value'];
                                $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_id'] = $mm['completion_id'];
                                $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_title'] = $mm['completion_title'];

                                if (!empty($mm['completion_data']['image']) && $mm['completion_data']['image'] && (S3_IMAGE . $mm['completion_data']['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $mm['completion_data']['image'] != 'HTTP/1.0 403 Forbidden')) {
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data']['image'] = $this->Image_model->resize($mm['completion_data']['image'], 100, 100);
                                } else {
                                    $this->data['editMission'][$missionCount]['editBadge'][$countBadge]['completion_data']['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                                }

                                $countBadge++;
                            }
                        }
                    }

                    $countBadge = 0;
                    $countCustomPoints = 0;
                    if(isset($mission['rewards'])){
                        foreach($mission['rewards'] as $rr){
                            if($rr['reward_type'] == 'POINT'){
                                $this->data['editMission'][$missionCount]['editPointRew']['reward_type'] = $rr['reward_type'];
                                $this->data['editMission'][$missionCount]['editPointRew']['reward_value'] = $rr['reward_value'];
                                $this->data['editMission'][$missionCount]['editPointRew']['reward_id'] = $rr['reward_id'];
                            }    

                            if($rr['reward_type'] == 'EXP'){
                                $this->data['editMission'][$missionCount]['editExpRew']['reward_type'] = $rr['reward_type'];
                                $this->data['editMission'][$missionCount]['editExpRew']['reward_value'] = $rr['reward_value'];
                                $this->data['editMission'][$missionCount]['editExpRew']['reward_id'] = $rr['reward_id'];                         
                            }

                            if($rr['reward_type'] == 'CUSTOM_POINT'){
                                $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_type'] = $rr['reward_type'];
                                $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_value'] = $rr['reward_value'];
                                $this->data['editMission'][$missionCount]['editCustomPointRew'][$countCustomPoints]['reward_id'] = $rr['reward_id'];
                                $countCustomPoints++;
                            }

                            if($rr['reward_type'] == 'BADGE'){
                                $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_type'] = $rr['reward_type'];
                                $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_value'] = $rr['reward_value'];
                                $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_id'] = $rr['reward_id'];
                                $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data'] = $rr['reward_data'];


                                if (!empty($rr['reward_data']['image']) && $rr['reward_data']['image'] && (S3_IMAGE . $rr['reward_data']['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $rr['reward_data']['image'] != 'HTTP/1.0 403 Forbidden')) {
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data']['image'] = $this->Image_model->resize($rr['reward_data']['image'], 100, 100);
                                } else {
                                    $this->data['editMission'][$missionCount]['editBadgeRew'][$countBadge]['reward_data']['image'] = $this->Image_model->resize('no_image.jpg', 100, 100);
                                }

                                $countBadge++;
                            }
                        }    
                    }

                    $missionCount++;
                    
                }
            }
        }

        $this->data['main'] = 'quest_form';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function autocomplete(){
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

            if($client_id){
                $data['client_id'] = $client_id;
                $data['site_id'] = $site_id;
                $results_quest = $this->Quest_model->getQuestsByClientSiteId($data);
            }else{
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

    public function increase_order($quest_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->increaseOrderByOneClient($quest_id, $client_id);
        }else{
            $this->Quest_model->increaseOrderByOne($quest_id);    
        }

        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));

    }

    public function decrease_order($quest_id){

        if($this->User_model->getClientId()){
            $client_id = $this->User_model->getClientId();
            $this->Quest_model->decreaseOrderByOneClient($quest_id, $client_id);
        }else{
            $this->Quest_model->decreaseOrderByOne($quest_id);    
        }
        // redirect('action', 'refresh');

        $json = array('success'=>'Okay!');

        $this->output->set_output(json_encode($json));
    }

    public function getListForAjax($offset) {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        
        $this->load->library('pagination');

        $config['per_page'] = 10;

        $filter = array(
                'limit' => $config['per_page'],
                'start' => $offset,
                'client_id'=>$client_id,
                'site_id'=>$site_id,
                'sort'=>'sort_order'
            );
        if(isset($_GET['filter_name'])){
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('action/page');
        $config["uri_segment"] = 3;

        if($client_id){
            $this->data['quests'] = $this->Quest_model->getQuestsByClientSiteId($filter);
            $config['total_rows'] = $this->Quest_model->getTotalQuestsClientSite($filter);
        }else{
            /*
            // $this->data['actions'] = $this->Action_model->getActions($filter);
            $allActions = $this->Action_model->getActions($filter);

            foreach ($allActions as &$action){
                $actionIsPublic = $this->checkActionIsPublic($action['_id']);
                $action['is_public'] =  $actionIsPublic;
            }

            $this->data['actions'] = $allActions;
            $config['total_rows'] = $this->Action_model->getTotalActions();
            */
        }

        $this->pagination->initialize($config);

        $this->render_page('quest_ajax');
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

    if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {

            if($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()){
                foreach ($this->input->post('selected') as $quest_id) {
                    $this->Quest_model->deleteQuestClient($quest_id);
                }
            }else{
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

    public function edit($quest_id){

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'quest/edit/'.$quest_id;

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();


        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = $this->input->post();

            foreach($data as $key => $value){
                if($key == 'condition' || $key == 'rewards' || $key == 'missions'){
                    $i = 0;
                    foreach($value as $k => $v){
                        foreach($v as $ke => &$item){
                            if(($ke == 'condition_id' || $ke == 'reward_id') && !empty($item)){
                                $item = new MongoId($item);
                            }
                        }
                        $qdata = array(
                            'client_id' => $client_id,
                            'site_id' => $site_id
                        );
                        unset($data[$key][$k]);
                        if($key == 'condition'){
                            $v["condition_data"] = $this->questObjectData($v, "condition_type", "condition_id", $qdata);
                        }
                        if($key == 'rewards'){
                            $v["reward_data"] = $this->questObjectData($v, "reward_type", "reward_id", $qdata);
                        }
                        $data[$key][$i] = $v;
                        if($key == 'missions'){
                            $data[$key][$i]['mission_number'] = $i + 1;
                        }
                        $i++;
                    }
                }
                if($key == 'missions'){
                    $im = 0;
                    foreach($value as $kk => $val){         

                        unset($data[$key][$kk]);
                        $data[$key][$im] = $val;
                        try {
                            $data[$key][$im]['mission_id'] = new MongoId($kk);
                        } catch (MongoException $ex) {
                            $data[$key][$im]['mission_id'] = new MongoId();
                        }

                        foreach($val as $k => $v){
                            if($k == 'completion' || $k == 'rewards'){
                                $i = 0;
                                foreach($v as $koo => $voo){
                                    foreach($voo as $kkk => &$vvv){
                                        if(($kkk == 'completion_id' || $kkk == 'reward_id') && !empty($vvv)){
                                            $vvv = new MongoId($vvv);
                                        }
                                        if($kkk == 'completion_element_id'){
                                            if(isset($vvv) && !empty($vvv)){
                                                $vvv = new MongoId($vvv);
                                            }else{
                                                $vvv = new MongoId();
                                            }
                                        }
                                    }
                                    $qdata = array(
                                        'client_id' => $client_id,
                                        'site_id' => $site_id
                                    );
                                    unset($data[$key][$im][$k][$koo]);
                                    if($k == 'completion'){
                                        $voo["completion_data"] = $this->questObjectData($voo, "completion_type", "completion_id", $qdata);
                                    }
                                    if($k == 'rewards'){
                                        $voo["reward_data"] = $this->questObjectData($voo, "reward_type", "reward_id", $qdata);
                                    }
                                    $data[$key][$im][$k][$i] = $voo;
                                    $i++;
                                }    
                            }
                        }
                        $im++;
                        
                    }
                }
            }

            $data['status'] = (isset($data['status']))?true:false;
            $data['mission_order'] = (isset($data['mission_order']))?true:false;            

            if($this->Quest_model->editQuestToClient($quest_id, $data)){
                redirect('/quest', 'refresh');
            }else{
                echo "Did not update";
            }


        }

        if(!empty($client_id) && !empty($site_id)){

            $this->getForm($quest_id);

        }
        
    }

     private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'quest')) {
            return true;
        } else {
            return false;
        }
    }

}