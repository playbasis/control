<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Player extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Player_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("player", $lang['folder']);
    }

    public function index()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            exit();
        }

        $this->load->model('Image_model');

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $params = $this->getParameters();

        $limit = isset($params['limit']) ? $params['limit'] : 10;

        $data = array(
            'sort' => $params['sort'],
            'order' => $params['order'],
            'start' => ($params['page'] - 1) * $limit,
            'limit' => $limit,
        );

        $this->data['players'] = array();

        $this->data['site_id'] = $this->User_model->getSiteId();
        $this->data['client_id'] = $this->User_model->getClientId();

        $results = $this->Player_model->getPlayers($data);

        foreach ($results as $result) {
            $color = $this->colorizeLevel($result['level']);

            if ($result['image']) {
                $image = $result['image'];
            } else {
                $image = S3_IMAGE . "cache/no_image-100x100.jpg";
            }

            $this->data['players'][] = array(
                'pb_player_id' => $result['_id'],
                'firstname' => $result['first_name'],
                'lastname' => $result['last_name'],
                'nickname' => $result['nickname'],
                'gender' => $result['gender'],
                'image' => $image,
                'level' => $result['level'],
                'color' => $color,
                'exp' => $result['exp'],
                'status' => $result['status'],
                'date_added' => date($this->lang->line('date_format_short'),
                    strtotime($this->datetimeMongotoReadable($result['date_added']))),
                'last_active' => date($this->lang->line('date_format_short'),
                    strtotime($this->datetimeMongotoReadable($result['date_modified'])))
            );
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

        $url = '';

        if ($this->input->get('sort')) {
            $url .= '&sort=' . $this->input->get('sort');
        }

        if ($this->input->get('order')) {
            $url .= '&order=' . $this->input->get('order');
        }

//        $pagination = new Pagination();
//        $pagination->total = $total;
//        $pagination->page = $params['page'];
//        $pagination->limit = $limit;
//        $pagination->text = $this->language->get('text_pagination');
//        $pagination->url = $this->url->link('player',  $url . '&page={page}', 'SSL');
//
//        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $params['sort'];
        $this->data['order'] = $params['order'];

        // get Action list and reward list
        if ($this->data['client_id']) {
            $this->data['rewardList'] = $this->Player_model->getRewardListAPI($this->data['client_id'],
                $this->data['site_id']);
            $this->data['actionList'] = $this->Player_model->getActionListAPI($this->data['client_id'],
                $this->data['site_id']);
        } else {
            $this->data['rewardList'] = array();
            $this->data['actionList'] = array();
        }


        $this->data['main'] = 'player';
        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('player');
    }

    public function getSummary()
    {

        //level_value gender_value reward_id reward_value action id action_value
        //choose parameter level gender reward action

        //reuturn
        // >options
        // >chart data
        /*
            parameter set :
            ==============
            |level
            |level:1-6
            |level:1-6|gender
            |lelel:1-6|gender:m|action:like
            |lelel:1-6|gender:m|action:like|action_value:1-100|reward:coin|reward_value:1-100
        */

        if (!$this->input->get('filter_sort')) {
            return;
        }
        $paremSet = $this->input->get('filter_sort');
        $paramSet = explode("|", $paremSet);

        $paramSet = array_filter($paramSet);

        $value = end($paramSet);

        $value_data = explode(':', $value);

        if (empty($value_data[1]) && $value_data[1] !== 0) {
            switch ($value_data[0]) {
                case 'level' :
                    $json = $this->firstDonut();
                    break;
                case 'gender' :
                    $json = $this->genderDonut();
                    break;

            }
        } else {
            switch ($value_data[0]) {
                case 'level' :
                    $json = $this->levelDonut();
                    break;
                case 'gender' :
                    $json = $this->genderDonut();
                    break;
                case ($value_data[0] == 'action_id' || $value_data[0] == 'action_value'):
                    $json = $this->actionDonut();
                    break;
                case ($value_data[0] == 'reward_id' || $value_data[0] == 'reward_value'):
                    $json = $this->rewardDonut();
                    break;
            }
        }

        $this->output->set_output($json);

    }

    private function firstDonut()
    {
        $json = array();

        $data = $this->filterData();

//        $total_players = $this->Player_model->getDonutTotalPlayer($data);

        $output = $this->Player_model->getDonutMaxLevel($data);

        $max_last = $output['result'];
        $total_players = $output['total'];

        if (isset($max_last['level']) && $max_last['level'] >= 3) {

            $json = $this->splitThree($data, $total_players, $max_last);

        } else {
            $json[] = $this->levelRange($data, 'level', 1, 1, $total_players);

            $json[] = $this->levelRange($data, 'level', 2, 2, $total_players);

            $json[] = $this->levelRange($data, 'level', 3, 3, $total_players);
        }

        $return_data = array(
            'donut' => $json,
            'options' => $this->getOptions(1, 1, 1, 1)
        );

        return json_encode($return_data);
    }

    private function splitThree($data, $total_players, $max_last)
    {
        $range = ceil($max_last['level'] / 3);

        $start_text = '1';

        $mid = $start_text + $range;
        $high = $mid + $range;

//        $high = $max_last['level'] - $range;
//        $mid = $high - $range;


        /*if(!isset($data['show_level_0'])){
            $start_text = '1';
        }else{
            $start_text = '0';
        }*/

        if ((int)$high >= (int)$max_last['level']) {
            $json[] = $this->levelRange($data, 'low', $start_text, ($mid - 1), $total_players);

            $json[] = $this->levelRange($data, 'high', $mid, $max_last['level'], $total_players);
        } else {
            $json[] = $this->levelRange($data, 'low', $start_text, ($mid - 1), $total_players);

            $json[] = $this->levelRange($data, 'medium', $mid, ($high - 1), $total_players);

            $json[] = $this->levelRange($data, 'high', $high, $max_last['level'], $total_players);
        }

        return $json;
    }

    private function levelRange($data, $text, $min, $max, $total_players)
    {
        if ($min == $max) {
            $text_level = (int)$min;
        } else {
            $text_level = (int)$min . "-" . (int)$max;
        }

        $data['filter_sort'] = array(array('name' => 'level', 'value' => $text_level));

        $player = $this->Player_model->getDonutLevel($data);

        $sum = 0;
        foreach ($player as $p) {
            $sum = $sum + (int)$p['value'];
        }

        $json = array(
            'label' => $text . ':' . $text_level,
            'data' => ($total_players > 0) ? round(($sum * 100) / $total_players) : 0,
            'value' => $sum,
        );

        return $json;
    }

    public function levelDonut()
    {
        $json = array();

        $data = $this->filterData();

        $total_players = $this->Player_model->getDonutTotalPlayer($data);

        if (isset($data['filter_sort'])) {
            // print_r($data['filter_sort']);

            foreach ($data['filter_sort'] as $filter) {
                $filter_name = $filter['name'];

                if ($filter_name == 'level') {
                    $level_pos = strrpos($filter['value'], '-');

                    if ($level_pos) {
                        $lv_explode = explode('-', $filter['value']);

                        $min_lv = $lv_explode[0];
                        $max_lv = $lv_explode[1];
                        $lv_range = $max_lv - $min_lv;
                    } else {
                        $min_lv = $filter['value'];
                        $max_lv = $filter['value'];
                        $lv_range = 1;
                    }
                    break;
                }
            }
        }

        $lv_loop = ($lv_range > 10) ? 10 : $lv_range;

        $range = ceil($lv_range / $lv_loop) - 1;

        for ($s = 1; $s <= 10; $s++) {
            $max_data = (($min_lv + $range) * $s) - (($min_lv - 1) * ($s - 1));
            $min_data = $max_data - $range;

            if ($max_data >= $max_lv) {
                $max_data = $max_lv;
            }
            $json[] = $this->levelRange($data, 'level', $min_data, $max_data, $total_players);
            if ($max_data >= $max_lv) {
                break;
            }
        }

        $return_data = array(
            'donut' => $json,
            'options' => $this->getOptions(1, 1, 1, 1)
        );

        return json_encode($return_data);
    }

    public function genderDonut()
    {
        $json = array();

        $data = $this->filterData();

        $output = $this->Player_model->getDonutGender($data);

        $gender_player = $output['result'];
        $total_players = $output['total'];

        foreach ($gender_player as $player) {
            if ($player['gender_id'] == 1) {
                $text = "Male";
            } elseif ($player['gender_id'] == 2) {
                $text = "Female";
            } else {
                $text = "Unknow";
            }
            $json[] = array(
                'label' => "gender:" . $text,
                'data' => round(($player['value'] * 100) / $total_players),
                'value' => $player['value'],
            );
        }

        $return_data = array(
            'donut' => $json,
            'options' => $this->getOptions(1, 1, 1, 1)
        );

        return json_encode($return_data);
    }

    private function actionRange($data, $text, $min, $max, $total_players)
    {
        if ($min == $max) {
            $text_range = $min;
        } else {
            $text_range = $min . "-" . $max;
        }

        $data['filter_sort'] = array_merge($data['filter_sort'],
            array(array('name' => 'action_value', 'value' => $text_range)));

        $output = $this->Player_model->getDonutAction($data);

        $json = array(
            'label' => $text . ':' . $text_range,
//            'data' => round(($a['value'] * 100) / $total_players),
            'data' => round(($output['total'] * 100) / $total_players),
//            'value' => $a['value'],
            'value' => $output['total'],
        );

        return $json;
    }

    public function actionDonut()
    {
        $json = array();

        $data = $this->filterData();

//        $total_players = $this->Player_model->getDonutTotalPlayer($data);

        $output = $this->Player_model->getDonutAction($data);

        $action_player = $output['result'];
        $total_players = $output['total'];

        if (count($action_player) >= 10) {
            $first_player = current($action_player);
            $last_player = end($action_player);
            reset($action_player);

            $range = ceil(($last_player['action_value'] - $first_player['action_value']) / 10) - 1;

            for ($s = 1; $s <= 10; $s++) {
                $max_data = (($first_player['action_value'] + $range) * $s) - (($first_player['action_value'] - 1) * ($s - 1));
                $min_data = $max_data - $range;

                if ($max_data >= $last_player['action_value']) {
                    $max_data = $last_player['action_value'];
                }
                $json[] = $this->actionRange($data, 'action_value', $min_data, $max_data, $total_players);
                if ($max_data >= $last_player['action_value']) {
                    break;
                }
            }

        } else {
            foreach ($action_player as $player) {
                $json[] = array(
                    'label' => "action_value:" . $player['action_value'],
                    'data' => round(($player['value'] * 100) / $total_players),
                    'value' => $player['value'],
                );
            }
        }

        $return_data = array(
            'donut' => $json,
            'options' => $this->getOptions(1, 1, 1, 1)
        );

        return json_encode($return_data);
    }

    private function rewardRange($data, $text, $min, $max, $total_players)
    {
        if ($min == $max) {
            $text_range = $min;
        } else {
            $text_range = $min . "-" . $max;
        }

        $data['filter_sort'] = array_merge($data['filter_sort'],
            array(array('name' => 'reward_value', 'value' => $text_range)));

        $output = $this->Player_model->getDonutReward($data);

        $json = array(
            'label' => $text . ':' . $text_range,
            'data' => round(($output['total'] * 100) / $total_players),
            'value' => $output['total'],
        );

        return $json;
    }

    public function rewardDonut()
    {
        $json = array();

        $data = $this->filterData();

//        $total_players = $this->Player_model->getDonutTotalPlayer($data);

        $output = $this->Player_model->getDonutReward($data);

        $reward_player = $output['result'];
        $total_players = $output['total'];

        if (count($reward_player) >= 10) {
            $first_player = current($reward_player);
            $last_player = end($reward_player);
            reset($reward_player);

            $range = ceil(($last_player['reward_value'] - $first_player['reward_value']) / 10) - 1;

            for ($s = 1; $s <= 10; $s++) {
                $max_data = (($first_player['reward_value'] + $range) * $s) - (($first_player['reward_value'] - 1) * ($s - 1));
                $min_data = $max_data - $range;

                if ($max_data >= $last_player['reward_value']) {
                    $max_data = $last_player['reward_value'];
                }

                $json[] = $this->rewardRange($data, 'reward_value', $min_data, $max_data, $total_players);
                if ($max_data >= $last_player['reward_value']) {
                    break;
                }
            }

        } else {
            foreach ($reward_player as $player) {
                $json[] = array(
                    'label' => "reward_value:" . $player['reward_value'],
                    'data' => round(($player['value'] * 100) / $total_players),
                    'value' => $player['value'],
                );
            }
        }

        $return_data = array(
            'donut' => $json,
            'options' => $this->getOptions(1, 1, 1, 1)
        );

        return json_encode($return_data);
    }

    public function getOptions($viewUserEnable, $genderEnable, $actionListEnable, $rewardListEnable)
    {
        $output = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($viewUserEnable) {
            $output['Gender'] = array();
        }

        if ($genderEnable) {
            $output['View_User'] = array();
        }

        if ($actionListEnable) {
            $output['Action'] = array();
            $tmp = $this->Player_model->getActionListAPI($client_id, $site_id);

            foreach ($tmp as $key => $value) {
                array_push($output['Action'], array('id' => $key, 'name' => $value));
            }

        }

        if ($rewardListEnable) {
            $output['Reward'] = array();
            $tmp = $this->Player_model->getRewardListAPI($client_id, $site_id);

            foreach ($tmp as $key => $value) {
                array_push($output['Reward'], array('id' => $key, 'name' => $value));
            }

        }

        return $output;
    }

    private function filterData()
    {

        if ($this->input->get('filter_sort')) {
            $sort = explode('|', $this->input->get('filter_sort'));

            if (is_array($sort)) {
                foreach ($sort as $value) {
                    $sort_explode = explode(':', $value);

                    if (!empty($sort_explode[1]) && $sort_explode[0] == "gender") {
                        switch ($sort_explode[1]) {
                            case "Male":
                                $sort_explode[1] = 1;
                                break;
                            case "Female":
                                $sort_explode[1] = 2;
                                break;
                            default :
                                $sort_explode[1] = 0;
                                break;
                        }
                    }

                    if (is_array($sort_explode)) {
                        $sort_data[] = array(
                            'name' => (isset($sort_explode[0]) && !empty($sort_explode[0])) ? $sort_explode[0] : '',
                            'value' => (isset($sort_explode[1]) && (!empty($sort_explode[1]) || $sort_explode[1] === "0")) ? $sort_explode[1] : ''
                        );
                    }
                }
            }

        } else {
            $sort_data = array();
        }

        $data = array(
            'filter_sort' => $sort_data
        );

//        if (isset($this->input->get('show_level_0'))) {
        $data['show_level_0'] = true;
//        }

        $data['client_id'] = $this->User_model->getClientId();
        $data['site_id'] = $this->User_model->getSiteId();

        return $data;

    }

    private function getParameters()
    {
        // default value of each params
        $paramList = array(
            'sort' => 'p.date_added',
            'order' => 'DESC',
            'page' => 1
        );

        foreach ($paramList as $key => $value) {
            if ($this->input->get($key)) {
                $paramList[$key] = $this->input->get($key);
            }
        }
        return $paramList;
    }

    private function colorizeLevel($level)
    {
        $player_color = array(
            'rgb(120, 205, 81)',
            'rgb(148, 64, 237)',
            'rgb(250, 187, 61)',
            'rgb(47, 171, 233)',
            'rgb(250, 88, 51)',
            '#5bb4ff',
            '#4c54fe',
            '#b94cff',
            '#ff7ee5',
            '#ff4545'
        );

        switch ($level) {
            case ($level >= 0) && ($level <= 10) :
                $color = $player_color[0];
                break;
            case ($level >= 11) && ($level <= 20) :
                $color = $player_color[1];
                break;
            case ($level >= 21) && ($level <= 30) :
                $color = $player_color[2];
                break;
            case ($level >= 31) && ($level <= 40) :
                $color = $player_color[3];
                break;
            case ($level >= 41) && ($level <= 50) :
                $color = $player_color[4];
                break;
            case ($level >= 51) && ($level <= 60) :
                $color = $player_color[5];
                break;
            case ($level >= 61) && ($level <= 70) :
                $color = $player_color[6];
                break;
            case ($level >= 71) && ($level <= 80) :
                $color = $player_color[7];
                break;
            case ($level >= 81) && ($level <= 90) :
                $color = $player_color[8];
                break;
            case ($level >= 91) && ($level <= 100) :
                $color = $player_color[9];
                break;
        };
        return $color;
    }

    private function getAge($birthdate)
    {
        $now = new DateTime();
        $birthdate = $this->datetimeMongotoReadable($birthdate);
        $oDateBirth = new DateTime($birthdate);
        $oDateInterval = $now->diff($oDateBirth);

        return $oDateInterval->y;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'player') && $this->Feature_model->getFeatureExistByClientId($client_id, 'player')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

?>