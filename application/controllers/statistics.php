<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Statistics  extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_model');
        $this->load->model('Plan_model');
        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Statistic_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index()
    {
        $config['base_url'] = site_url('statistics');
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }
    
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['main'] = 'statistics';

        $this->load->vars($this->data);
        $this->render_page('template');
    }
    
    public function getActionData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action = array('paybill', 'topup', 'reload_for_others', 'addon', 'want');
            $action_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $action_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                         'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', 
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getActionData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $value);
                }
            } elseif($type == 'month'){
                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getActionData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $action_label);
                        $action_data[$key][$index] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                        'borderColor' => $action_border_color[$key],
                        'data' => $action_data[$key],
                        'label' => $value);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getActionData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['day']) - 1] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                        'borderColor' => $action_border_color[$key],
                        'data' => $action_data[$key],
                        'label' => $value);
                }

            }
            
            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getGoodsSuperData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $action_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                         'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');

                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'superdeal') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsSuperData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }

            } elseif($type == 'month'){
                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'superdeal') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsSuperData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $action_label);
                        $action_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'superdeal') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsSuperData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['day']) - 1] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            }

            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getGoodsMonthlyData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $action_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                         'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');

                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'monthly') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsMonthlyData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){  
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }

            } elseif($type == 'month'){
                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'monthly') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsMonthlyData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $action_label);
                        $action_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                $top_reward = $this->Statistic_model->getTopGoodsData($client_id, $site_id, $from, $to);
                $monthly_reward = array();
                foreach ($top_reward as $v){
                    if(strpos($v['_id']['reward_name'], 'monthly') !== false) {
                        array_push($monthly_reward,$v);
                        if(sizeof($monthly_reward) == 5){
                            break;
                        }
                    }
                }
                foreach ($monthly_reward as $key => $value){
                    $data = $this->Statistic_model->getGoodsMonthlyData($client_id, $site_id, $value['_id']['reward_id'], $from, $to, $type);
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['day']) - 1] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            }

            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getBadgeData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $action_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                         'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $badge_reward = $this->Statistic_model->getTopBadgeData($client_id, $site_id, $from, $to);
                $size = sizeof($badge_reward) > 5 ? 5 : sizeof($badge_reward);
                for ($key = 0 ; $key < $size ; $key++){
                    $data = $this->Statistic_model->getBadgeData($client_id, $site_id, $badge_reward[$key]['_id'], $from, $to, $type);
                    $badge_name = $this->Statistic_model->getBadgeNameToClient($client_id, $site_id, $badge_reward[$key]['_id']);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $badge_name);
                }
            } elseif($type == 'month'){
                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $badge_reward = $this->Statistic_model->getTopBadgeData($client_id, $site_id, $from, $to);
                $size = sizeof($badge_reward) > 5 ? 5 : sizeof($badge_reward);
                for ($key = 0 ; $key < $size ; $key++){
                    $data = $this->Statistic_model->getBadgeData($client_id, $site_id, $badge_reward[$key]['_id'], $from, $to, $type);
                    $badge_name = $this->Statistic_model->getBadgeNameToClient($client_id, $site_id, $badge_reward[$key]['_id']);
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $action_label);
                        $action_data[$key][$index] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $badge_name);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                $badge_reward = $this->Statistic_model->getTopBadgeData($client_id, $site_id, $from, $to);
                $size = sizeof($badge_reward) > 5 ? 5 : sizeof($badge_reward);
                for ($key = 0 ; $key < $size ; $key++){
                    $data = $this->Statistic_model->getBadgeData($client_id, $site_id, $badge_reward[$key]['_id'], $from, $to, $type);
                    $badge_name = $this->Statistic_model->getBadgeNameToClient($client_id, $site_id, $badge_reward[$key]['_id']);
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['day']) - 1] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => $action_color[$key],
                                                  'borderColor' => $action_border_color[$key],
                                                  'data' => $action_data[$key],
                                                  'label' => $badge_name);
                }
            }

            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getRegisterData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $data = $this->Statistic_model->getRegisterData($client_id, $site_id, $from, $to, $type);
                $action_data[0] = array_fill(0, 24, 0);
                foreach ($data as $v){
                    $action_data[0][intval($v['_id']['hour'])] = $v['value'];
                }
                $action_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                           'borderColor' => 'rgba(255, 159, 64, 1)',
                                           'data' => $action_data[0],
                                           'label' => 'Players');
            } elseif($type == 'month'){

                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getRegisterData($client_id, $site_id, $from, $to, $type);
                $action_data[0] = array_fill(0, $day, 0);
                foreach ($data as $v){
                    $key = array_search($v['_id']['date'], $action_label);
                    $action_data[0][$key] = $v['value'];
                }
                $action_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                           'borderColor' => 'rgba(255, 159, 64, 1)',
                                           'data' => $action_data[0],
                                           'label' => 'Players');
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                $data = $this->Statistic_model->getRegisterData($client_id, $site_id, $from, $to, $type);
                $action_data[0] = array_fill(0, 7, 0);
                foreach ($data as $v){
                    $action_data[0][intval($v['_id']['day']) - 1] = $v['value'];
                }
                $action_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                           'borderColor' => 'rgba(255, 159, 64, 1)',
                                           'data' => $action_data[0],
                                           'label' => 'Players');
            }

            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getMGMData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action = array('invite');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                      '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getMGMData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                                  'borderColor' => 'rgba(255, 99, 132, 1)',
                                                  'data' => $action_data[$key],
                                                  'label' => 'MGM');
                }
            } elseif($type == 'month'){
                $date = date("Y-m-d", strtotime("-30 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array();
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getMGMData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $action_label);
                        $action_data[$key][$index] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                                  'borderColor' => 'rgba(255, 99, 132, 1)',
                                                  'data' => $action_data[$key],
                                                  'label' => 'MGM');
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $date = date("Y-m-d");
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $action_label = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                foreach ($action as $key => $value){
                    $data = $this->Statistic_model->getMGMData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['day']) - 1] = $v['value'];
                    }
                    $action_dataset[$key] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                                  'borderColor' => 'rgba(255, 99, 132, 1)',
                                                  'data' => $action_data[$key],
                                                  'label' => 'MGM');
                }
            }

            echo json_encode(array('label'=> $action_label, 'data' => $action_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'statistics') && $this->Feature_model->getFeatureExistByClientId($client_id, 'statistics'))
        {
            return true;
        } else {
            return false;
        }
    }
}