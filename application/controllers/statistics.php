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
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y'));
                $date = date("Y-m-d", strtotime("-$day days"));
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
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != $day){
                    $last_date = $this->Statistic_model->getLastActionDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $action_value_array = array();
                        foreach ($action as $v){
                            $action_value = $this->Statistic_model->getActionDataCount($client_id, $site_id, $v, $from_update, $to_update);
                            $action_value_array[$v] = $action_value;
                        }
                        $this->Statistic_model->updateActionDataCache($client_id, $site_id, $from_update, $action_value_array);
                    }
                    $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($action as $key => $value){
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $index => $v){
                        $action_data[$key][$index] = $v[$value];
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
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                for($i=7; $i > 0 ; $i--){
                    $action_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != 7){
                    $last_date = $this->Statistic_model->getLastActionDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $action_value_array = array();
                        foreach ($action as $v){
                            $action_value = $this->Statistic_model->getActionDataCount($client_id, $site_id, $v, $from_update, $to_update);
                            $action_value_array[$v] = $action_value;
                        }
                        $this->Statistic_model->updateActionDataCache($client_id, $site_id, $from_update, $action_value_array);
                    }
                    $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($action as $key => $value){
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $index => $v){
                        $action_data[$key][$index] = $v[$value];
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

    public function downloadActionData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_data = array();
            $action = array('paybill', 'topup', 'reload_for_others', 'addon', 'want');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticActionReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser

            $exporter->addRow(array(
                    $this->lang->line('column_date'),
                    $this->lang->line('column_addon'),
                    $this->lang->line('column_paybill'),
                    $this->lang->line('column_topup'),
                    $this->lang->line('column_reload_for_others'),
                    $this->lang->line('column_want'),
                )
            );

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
                }

                foreach ($action_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $action_data[3][$key],
                            $action_data[0][$key],
                            $action_data[1][$key],
                            $action_data[2][$key],
                            $action_data[4][$key])
                    );
                }

            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y'));
                $date = date("Y-m-d", strtotime("-$day days"));
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
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);

                foreach ($data as $v){
                    $exporter->addRow(array(
                        datetimeMongotoReadable($v['date']),
                        $v['addon'],
                        $v['paybill'],
                        $v['topup'],
                        $v['reload_for_others'],
                        $v['want'])
                    );
                }

            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $action_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionDataCache($client_id, $site_id, $from, $to);

                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['addon'],
                            $v['paybill'],
                            $v['topup'],
                            $v['reload_for_others'],
                            $v['want'])
                    );
                }
            }
            $exporter->finalize();
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getActionAmountData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_dataset = array();
            $action_data = array();
            $action = array('paybill', 'topup', 'reload_for_others', 'addon');
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
                    $data = $this->Statistic_model->getActionAmountData($client_id, $site_id, $value, $from, $to, $type);
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
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y'));
                $date = date("Y-m-d", strtotime("-$day days"));
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
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != $day){
                    $last_date = $this->Statistic_model->getLastActionAmountDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $action_value_array = array();
                        foreach ($action as $v){
                            $action_value = $this->Statistic_model->getActionAmountData($client_id, $site_id, $v, $from_update, $to_update, 'week');
                            $action_value_array[$v] = number_format((float)$action_value[0]['value'], 2, '.', '');
                        }
                        $this->Statistic_model->updateActionAmountDataCache($client_id, $site_id, $from_update, $action_value_array);
                    }
                    $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($action as $key => $value){
                    $action_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $index => $v){
                        $action_data[$key][$index] = $v[$value];
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
                $date = date("Y-m-d", strtotime("-1 days"));
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                for($i=7; $i > 0 ; $i--){
                    $action_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != 7){
                    $last_date = $this->Statistic_model->getLastActionAmountDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $action_value_array = array();
                        foreach ($action as $v){
                            $action_value = $this->Statistic_model->getActionAmountData($client_id, $site_id, $v, $from_update, $to_update, 'week');
                            $action_value_array[$v] = number_format((float)$action_value[0]['value'], 2, '.', '');
                        }
                        $this->Statistic_model->updateActionAmountDataCache($client_id, $site_id, $from_update, $action_value_array);
                    }
                    $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($action as $key => $value){
                    $action_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $index => $v){
                        $action_data[$key][$index] = $v[$value];
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

    public function downloadActionAmountData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $action_data = array();
            $action = array('paybill', 'topup', 'reload_for_others', 'addon', 'want');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticActionValueReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser

            $exporter->addRow(array(
                    $this->lang->line('column_date'),
                    $this->lang->line('column_addon'),
                    $this->lang->line('column_paybill'),
                    $this->lang->line('column_topup'),
                    $this->lang->line('column_reload_for_others')
                )
            );

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
                    $data = $this->Statistic_model->getActionAmountData($client_id, $site_id, $value, $from, $to, $type);
                    $action_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $action_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                }

                foreach ($action_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $action_data[3][$key],
                            $action_data[0][$key],
                            $action_data[1][$key],
                            $action_data[2][$key],
                            $action_data[4][$key])
                    );
                }

            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y'));
                $date = date("Y-m-d", strtotime("-$day days"));
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
                for($i=$day; $i >= 1 ;$i--){
                    $action_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);

                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['addon'],
                            $v['paybill'],
                            $v['topup'],
                            $v['reload_for_others'])
                    );
                }

            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $action_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getActionAmountDataCache($client_id, $site_id, $from, $to);

                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['addon'],
                            $v['paybill'],
                            $v['topup'],
                            $v['reload_for_others'])
                    );
                }
            }
            $exporter->finalize();
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getGoodsSuperData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $goods_dataset = array();
            $goods_data = array();
            $goods_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $goods_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
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
                $goods_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
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
                    $goods_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $goods_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }

            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $goods_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $goods_label[] = ''.date("d", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $goods_label);
                        $goods_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                $start_day = date("D", strtotime("-1 days"));
                $week_day = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                $adjust_day = array_search($start_day, $week_day);
                for($i=7; $i > 0 ; $i--){
                    $goods_label[] =  date("D", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $goods_data[$key][(intval($v['_id']['day'])+(6-$adjust_day)-1)%7] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            }

            echo json_encode(array('label'=> $goods_label, 'data' => $goods_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function downloadGoodsSuperData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $goods_data = array();
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticGoodsSuperdealReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser
            $goods_list = array();
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $goods_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
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
                    $goods_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $goods_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }

            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $goods_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $goods_label[] = ''.date("d", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $goods_label);
                        $goods_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                $start_day = date("D", strtotime("-1 days"));
                $week_day = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                $adjust_day = array_search($start_day, $week_day);
                for($i=7; $i > 0 ; $i--){
                    $goods_label[] =  date("D", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $goods_data[$key][(intval($v['_id']['day'])+(6-$adjust_day)-1)%7] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }
            }
            $exporter->finalize();
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getGoodsMonthlyData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $goods_dataset = array();
            $goods_data = array();
            $goods_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $goods_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
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
                $goods_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
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
                    $goods_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){  
                        $goods_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }

            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $goods_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $goods_label[] = ''.date("d", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $goods_label);
                        $goods_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                $start_day = date("D", strtotime("-1 days"));
                $week_day = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                $adjust_day = array_search($start_day, $week_day);
                for($i=7; $i > 0 ; $i--){
                    $goods_label[] =  date("D", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $goods_data[$key][(intval($v['_id']['day'])+(6-$adjust_day)-1)%7] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    $goods_dataset[$key] = array('backgroundColor' => $goods_color[$key],
                                                 'borderColor' => $goods_border_color[$key],
                                                 'data' => $goods_data[$key],
                                                 'label' => $goods_name ? $goods_name : $value['_id']['reward_name']);
                }
            }

            echo json_encode(array('label'=> $goods_label, 'data' => $goods_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function downloadGoodsMonthlyData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $goods_data = array();
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticGoodsMonthlyReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser
            $goods_list = array();
            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $goods_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
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
                    $goods_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $goods_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $goods_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $goods_label[] = ''.date("d", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $v){
                        $index = array_search($v['_id']['date'], $goods_label);
                        $goods_data[$key][$index] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                $start_day = date("D", strtotime("-1 days"));
                $week_day = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                $adjust_day = array_search($start_day, $week_day);
                for($i=7; $i > 0 ; $i--){
                    $goods_label[] =  date("D", strtotime("-".$i." days"));
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
                    $goods_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $v){
                        $goods_data[$key][(intval($v['_id']['day'])+(6-$adjust_day)-1)%7] = $v['value'];
                    }
                    $goods_name = $this->Statistic_model->getGoodsByRewardRedeem($client_id, $site_id, $value['_id']['reward_id'].'');
                    array_push($goods_list,$goods_name );
                }
                $exporter->addRow(array_merge(array($this->lang->line('column_date')), $goods_list));
                foreach ($goods_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $goods_data[0][$key],
                            $goods_data[1][$key],
                            $goods_data[2][$key],
                            $goods_data[3][$key],
                            $goods_data[4][$key])
                    );
                }
            }
            $exporter->finalize();
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getBadgeData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $badge_dataset = array();
            $badge_data = array();
            $badge = array('MyAdd-on Badges', 'MyAdd-On Badges', 'MyReload Badges', 'GIFTBOX-RELOAD', 'GIFTBOX-ADDON');
            $badge_id = array('56406ff5be120b1e2d8b45a9', '585753ab9f73d2af3f8b4567', '56406fc4be120b1f2d8b4579', '587c408828fe0998658b4567', '587c414028fe0999658b4567');
            $badge_color = array('rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)');
            $badge_border_color = array('rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
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
                $badge_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                     '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                foreach($badge_id as $key => $value){
                    $data = $this->Statistic_model->getBadgeData($client_id, $site_id, $value, $from, $to, $type);
                    $badge_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $badge_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                    $badge_dataset[$key] = array('backgroundColor' => $badge_color[$key],
                                                 'borderColor' => $badge_border_color[$key],
                                                 'data' => $badge_data[$key],
                                                 'label' => $badge[$key]);
                }
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $badge_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $badge_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != $day){
                    $last_date = $this->Statistic_model->getLastBadgeDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $badge_value_array = array();
                        foreach ($badge as $k => $v){
                            $badge_value = $this->Statistic_model->getBadgeDataCount($client_id, $site_id, $badge_id[$k], $from_update, $to_update);
                            $badge_value_array[$v] = $badge_value;
                        }
                        $this->Statistic_model->updateBadgeDataCache($client_id, $site_id, $from_update, $badge_value_array);
                    }
                    $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($badge as $key => $value){
                    $badge_data[$key] = array_fill(0, $day, 0);
                    foreach ($data as $index => $v){
                        $badge_data[$key][$index] = $v[$value];
                    }
                    $badge_dataset[$key] = array('backgroundColor' => $badge_color[$key],
                                                 'borderColor' => $badge_border_color[$key],
                                                 'data' => $badge_data[$key],
                                                 'label' => $value);
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $badge_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != 7){
                    $last_date = $this->Statistic_model->getLastBadgeDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $badge_value_array = array();
                        foreach ($badge as $k => $v){
                            $badge_value = $this->Statistic_model->getBadgeDataCount($client_id, $site_id, $badge_id[$k], $from_update, $to_update);
                            $badge_value_array[$v] = $badge_value;
                        }
                        $this->Statistic_model->updateBadgeDataCache($client_id, $site_id, $from_update, $badge_value_array);
                    }
                    $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);
                }
                foreach ($badge as $key => $value){
                    $badge_data[$key] = array_fill(0, 7, 0);
                    foreach ($data as $index => $v){
                        $badge_data[$key][$index] = $v[$value];
                    }
                    $badge_dataset[$key] = array('backgroundColor' => $badge_color[$key],
                                                 'borderColor' => $badge_border_color[$key],
                                                 'data' => $badge_data[$key],
                                                 'label' => $value);
                }
            }

            echo json_encode(array('label'=> $badge_label, 'data' => $badge_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function downloadBadgeData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $badge_data = array();
            $badge = array('MyAdd-on Badges', 'MyAdd-On Badges', 'MyReload Badges', 'GIFTBOX-RELOAD', 'GIFTBOX-ADDON');
            $badge_id = array('56406ff5be120b1e2d8b45a9', '585753ab9f73d2af3f8b4567', '56406fc4be120b1f2d8b4579', '587c408828fe0998658b4567', '587c414028fe0999658b4567');
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");

            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticBadgeReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser

            $exporter->addRow(array_merge(array($this->lang->line('column_date')), $badge));

            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $badge_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                    '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                foreach($badge_id as $key => $value){
                    $data = $this->Statistic_model->getBadgeData($client_id, $site_id, $value, $from, $to, $type);
                    $badge_data[$key] = array_fill(0, 24, 0);
                    foreach ($data as $v){
                        $badge_data[$key][intval($v['_id']['hour'])] = $v['value'];
                    }
                }
                foreach ($badge_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $badge_data[3][$key],
                            $badge_data[0][$key],
                            $badge_data[1][$key],
                            $badge_data[2][$key],
                            $badge_data[4][$key])
                    );
                }
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $badge_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $badge_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);

                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['MyAdd-on Badges'],
                            $v['MyAdd-On Badges'],
                            $v['MyReload Badges'],
                            $v['GIFTBOX-RELOAD'],
                            $v['GIFTBOX-ADDON'])
                    );
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $badge_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getBadgeDataCache($client_id, $site_id, $from, $to);
                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['MyAdd-on Badges'],
                            $v['MyAdd-On Badges'],
                            $v['MyReload Badges'],
                            $v['GIFTBOX-RELOAD'],
                            $v['GIFTBOX-ADDON'])
                    );
                }
            }
            $exporter->finalize();
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getRegisterData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $register_dataset = array();
            $register_data = array();
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
                $register_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                        '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $data = $this->Statistic_model->getRegisterData($client_id, $site_id, $from, $to, $type);
                $register_data[0] = array_fill(0, 24, 0);
                foreach ($data as $v){
                    $register_data[0][intval($v['_id']['hour'])] = $v['value'];
                }
                $register_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                           'borderColor' => 'rgba(255, 159, 64, 1)',
                                           'data' => $register_data[0],
                                           'label' => 'Players');
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $register_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $register_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != $day){
                    $last_date = $this->Statistic_model->getLastRegisterDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $register_value = $this->Statistic_model->getRegisterDataCount($client_id, $site_id, $from_update, $to_update);
                        $this->Statistic_model->updateRegisterDataCache($client_id, $site_id, $from_update, $register_value);
                    }
                    $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                }

                $register_data[0] = array_fill(0, $day, 0);
                foreach ($data as $index => $v){
                    $register_data[0][$index] = $v['value'];
                }
                $register_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                             'borderColor' => 'rgba(255, 159, 64, 1)',
                                             'data' => $register_data[0],
                                             'label' => 'Players');
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $register_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != 7){
                    $last_date = $this->Statistic_model->getLastRegisterDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $register_value = $this->Statistic_model->getRegisterDataCount($client_id, $site_id, $from_update, $to_update);
                        $this->Statistic_model->updateRegisterDataCache($client_id, $site_id, $from_update, $register_value);
                    }
                    $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                }
                $register_data[0] = array_fill(0, 7, 0);
                foreach ($data as $index => $v){
                    $register_data[0][$index] = $v['value'];
                }
                $register_dataset[0] = array('backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                                             'borderColor' => 'rgba(255, 159, 64, 1)',
                                             'data' => $register_data[0],
                                             'label' => 'Players');
            }

            echo json_encode(array('label'=> $register_label, 'data' => $register_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function downloadRegisterData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $register_data = array();
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticRegisterReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser

            $exporter->addRow(array($this->lang->line('column_date'),'Players'));

            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $register_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                    '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $data = $this->Statistic_model->getRegisterData($client_id, $site_id, $from, $to, $type);
                $register_data[0] = array_fill(0, 24, 0);
                foreach ($data as $v){
                    $register_data[0][intval($v['_id']['hour'])] = $v['value'];
                }
                foreach ($register_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $register_data[0][$key])
                    );
                }
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $register_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $register_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                foreach ($data as $v){
                    $exporter->addRow(array(
                        datetimeMongotoReadable($v['date']),
                        $v['value'])
                    );
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $register_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getRegisterDataCache($client_id, $site_id, $from, $to);
                foreach ($data as $v){
                    $exporter->addRow(array(
                        datetimeMongotoReadable($v['date']),
                        $v['value'])
                    );
                }
            }

        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function getMGMData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $mgm_dataset = array();
            $mgm_data = array();
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
                $mgm_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                                   '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $data = $this->Statistic_model->getMGMData($client_id, $site_id, $from, $to, $type);
                $mgm_data[0] = array_fill(0, 24, 0);
                foreach ($data as $v){
                    $mgm_data[0][intval($v['_id']['hour'])] = $v['value'];
                }
                $mgm_dataset[0] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                        'borderColor' => 'rgba(255, 99, 132, 1)',
                                        'data' => $mgm_data[0],
                                        'label' => 'MGM');
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $mgm_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $mgm_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != $day){
                    $last_date = $this->Statistic_model->getLastMGMDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $mgm_value = $this->Statistic_model->getMGMDataCount($client_id, $site_id, $from_update, $to_update);
                        $this->Statistic_model->updateMGMDataCache($client_id, $site_id, $from_update, $mgm_value);
                    }
                    $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                }

                $mgm_data[0] = array_fill(0, $day, 0);
                foreach ($data as $index => $v){
                    $mgm_data[0][$index] = $v['value'];
                }
                $mgm_dataset[0] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                        'borderColor' => 'rgba(255, 99, 132, 1)',
                                        'data' => $mgm_data[0],
                                        'label' => 'MGM');
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $mgm_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                if(sizeof($data) != 7){
                    $last_date = $this->Statistic_model->getLastMGMDataCache($client_id, $site_id);
                    $diff_date = date_diff($last_date && dateMongotoReadable($last_date) ? date_create(dateMongotoReadable($last_date)) : date_create(date("Y-m-d", strtotime("-33 days"))),date_create(date("Y-m-d", strtotime("-1 days"))));
                    $diff_day = intval($diff_date->format("%a"))-1;
                    $diff_day = $diff_day > 31 ? 31 : $diff_day;
                    for($i=$diff_day; $i >= 1 ;$i--){
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $from_update = strtotime($date_update);
                        $date_update = date("Y-m-d", strtotime("-".$i." days"));
                        $date_zone_update = new DateTime($date_update, $UTC_8);
                        $date_zone_update->setTimezone($UTC_7);
                        $date_update = $date_zone_update->format('Y-m-d H:i:s');
                        $currentDate_update = strtotime($date_update);
                        $to_update = $currentDate_update + ("86399");
                        $mgm_value = $this->Statistic_model->getMGMDataCount($client_id, $site_id, $from_update, $to_update);
                        $this->Statistic_model->updateMGMDataCache($client_id, $site_id, $from_update, $mgm_value);
                    }
                    $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                }
                $mgm_data[0] = array_fill(0, 7, 0);
                foreach ($data as $index => $v){
                    $mgm_data[0][$index] = $v['value'];
                }
                $mgm_dataset[0] = array('backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                        'borderColor' => 'rgba(255, 99, 132, 1)',
                                        'data' => $mgm_data[0],
                                        'label' => 'MGM');
            }

            echo json_encode(array('label'=> $mgm_label, 'data' => $mgm_dataset));
        } else {
            $this->output->set_output(json_encode(array('success' => false)));
        }
    }

    public function downloadMGMData($type=null){
        if ($this->User_model->getClientId()) {
            $client_id = $this->User_model->getClientId();
            $site_id = $this->User_model->getSiteId();

            $mgm_data = array();
            $UTC_7 = new DateTimeZone("Asia/Bangkok");
            $UTC_8 = new DateTimeZone("Asia/Kuala_Lumpur");
            $this->load->helper('export_data');

            $exporter = new ExportDataCSV('browser', "StatisticMGMReport_" . date("YmdHis") . ".csv");

            $exporter->initialize(); // starts streaming data to web browser

            $exporter->addRow(array($this->lang->line('column_date'),'MGM'));

            if($type == 'day'){
                $date = date("Y-m-d");
                $date_zone = new DateTime($date, $UTC_8);
                $date_zone->setTimezone($UTC_7);
                $date = $date_zone->format('Y-m-d H:i:s');
                $from = strtotime($date);
                $currentDate = strtotime($date);
                $to = $currentDate + ("86399");
                $mgm_label = array('00:00','01:00','02:00','03:00','04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
                    '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
                $data = $this->Statistic_model->getMGMData($client_id, $site_id, $from, $to, $type);
                $mgm_data[0] = array_fill(0, 24, 0);
                foreach ($data as $v){
                    $mgm_data[0][intval($v['_id']['hour'])] = $v['value'];
                }
                foreach ($mgm_label as $key => $label) {
                    $exporter->addRow(array(
                            $label,
                            $mgm_data[0][$key])
                    );
                }
            } elseif($type == 'month'){
                $day = cal_days_in_month(CAL_GREGORIAN, date('m') - 1 , date('Y')); // 31
                $date = date("Y-m-d", strtotime("-".$day." days"));
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
                $mgm_label = array();
                for($i=$day; $i >= 1 ;$i--){
                    $mgm_label[] = ''.date("d", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['value'])
                    );
                }
            } else {
                $date = date("Y-m-d", strtotime("-7 days"));
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
                for($i=7; $i > 0 ; $i--){
                    $mgm_label[] =  date("D", strtotime("-".$i." days"));
                }
                $data = $this->Statistic_model->getMGMDataCache($client_id, $site_id, $from, $to);
                foreach ($data as $v){
                    $exporter->addRow(array(
                            datetimeMongotoReadable($v['date']),
                            $v['value'])
                    );
                }
            }


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