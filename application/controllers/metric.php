<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('CACHE_NEVER_EXPIRE', -1);
define('CACHE_TTL_IN_SEC', 10 * 60);

function sort_by_name_ic($a, $b)
{
    return strcasecmp($a['name'], $b['name']);
}

class Metric extends MY_Controller
{

    private $_api;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->driver('cache', array('adapter' => 'file'));

        $this->load->model('Client_model');
        $this->load->model('Service_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->load->helper('url');

        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(),
            $this->User_model->getSiteId());
        $this->_api = $this->playbasisapi;
        $this->_api->set_api_key($result['api_key']);
        $this->_api->set_api_secret($result['api_secret']);
    }

    public function index()
    {
        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'metric';
        $this->render_page('template');
    }

    // API Calls

    public function getRewardBadge()
    {
        log_message('error', 'getRewardBadge');
        $rewardBadge = $this->listClients();
        $this->responseJson($rewardBadge);
    }

    public function getRewardBadgeLog()
    {
        log_message('error', 'getRewardBadgeLog');
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $rewardUnitType = $this->input->get('rewardUnitType', true);
        $formattedRewardLog = $this->getRewardLog($startDate, $endDate, $rewardUnitType);
        $this->responseJson($formattedRewardLog);
    }

    private function getRewardLog($startDate = null, $endDate = null, $rewardUnitType = null)
    {
        log_message('error', 'getRewardLog');
        $key = 'getRewardLog-' . $startDate . '-' . $endDate;
        $cached = $this->cache->get($key);
        if ($cached !== false) {
            $formattedRewardLog = $this->formatUnitType($cached, $rewardUnitType);
            $formattedRewardLog = array('success' => true, 'response' => $formattedRewardLog);
            log_message('error', 'cache HIT: key=' . $key);
            return $formattedRewardLog;
        }
        log_message('error', 'cache MISS: key=' . $key);
        $out = $this->convertForPlot($this->Client_model->listClients(array('X')), $startDate, $endDate);
        $this->cache->save($key, $out, $endDate < date('Y-m-d') ? CACHE_NEVER_EXPIRE : CACHE_TTL_IN_SEC);
        $formattedRewardLog = $this->formatUnitType($out, $rewardUnitType);
        $formattedRewardLog = array('success' => true, 'response' => $formattedRewardLog);
        return $formattedRewardLog;
    }

    public function getRewardCompareLog()
    {
        log_message('error', 'getRewardCompareLog');

        $compareDate = $this->input->get('compareDate', true);
        $rewardType = $this->input->get('rewardType', true);
        $rewardCompareType = $this->input->get('rewardCompareType', true);

        $compareDateArray = explode("-", $compareDate);
        $compareDateTimestamp = mktime(0, 0, 0, $compareDateArray[1], $compareDateArray[2], $compareDateArray[0]);

        $n1 = date('Y-m-d', $compareDateTimestamp);
        $n2 = date('Y-m-d', strtotime('-1 day', $compareDateTimestamp));
        $n3 = date('Y-m-d', strtotime('-2 day', $compareDateTimestamp));

        //$badges             = $this->_api->getRewardBadge();
        $badges = $this->listClients();
        $formattedRewardLog = $this->getRewardLog($n3, $n1, 'day');

        $formattedRewardLog['response'] = $this->reFormatResponse($formattedRewardLog['response'], $rewardType);

        $n1Result = property_exists($formattedRewardLog['response'],
            $n1) ? (object)$formattedRewardLog['response']->$n1 : (object)array();
        $n2Result = property_exists($formattedRewardLog['response'],
            $n2) ? (object)$formattedRewardLog['response']->$n2 : (object)array();
        $n3Result = property_exists($formattedRewardLog['response'],
            $n3) ? (object)$formattedRewardLog['response']->$n3 : (object)array();

        $comparedResult = array(
            $n1 => array(),
            $n2 => array()
        );

        if ($rewardType == 'badge') {
            foreach ($badges['response']['badges'] as $badge) {

                $badge_id = $badge['badge_id'];

                $n1ResultRewardValue = array_key_exists($badge_id, $n1Result->badge) ? $n1Result->badge[$badge_id] : 0;
                $n2ResultRewardValue = array_key_exists($badge_id, $n2Result->badge) ? $n2Result->badge[$badge_id] : 0;
                $n3ResultRewardValue = array_key_exists($badge_id, $n3Result->badge) ? $n3Result->badge[$badge_id] : 0;

                switch ($rewardCompareType) {
                    case 'percentage':
                        $comparedResult[$n1][$badge_id] = $n1ResultRewardValue . '_' . ($this->calcDiffPercent($n1ResultRewardValue,
                                    $n2ResultRewardValue) . '%');
                        $comparedResult[$n2][$badge_id] = $n2ResultRewardValue . '_' . ($this->calcDiffPercent($n2ResultRewardValue,
                                    $n3ResultRewardValue) . '%');
                        break;
                    case 'gross':
                        $comparedResult[$n1][$badge_id] = $n1ResultRewardValue . '_' . ($n1ResultRewardValue - $n2ResultRewardValue);
                        $comparedResult[$n2][$badge_id] = $n2ResultRewardValue . '_' . ($n2ResultRewardValue - $n3ResultRewardValue);
                        break;
                }
            }
        }

        $formattedRewardLog['response'] = $comparedResult;

        $this->responseJson($formattedRewardLog);
    }

    // Helpers methods

    private function listClients()
    {
        $rewardBadge = array('success' => false);
        $clients = $this->Client_model->listClients(array('first_name', 'last_name', 'company'));
        if ($clients && is_array($clients)) {
            foreach ($clients as &$client) {
                $client['badge_id'] = $client['_id'] . '';
                //$client['name'] = $client['first_name'].(!empty($client['last_name']) ? ' '.$client['last_name'] : '').(!empty($client['company']) ? ' ('.$client['company'].')' : '');
                $client['name'] = $client['first_name'];
            }
            usort($clients, 'sort_by_name_ic');
            $rewardBadge = array('success' => true, 'response' => array('badges' => $clients));
        }
        return $rewardBadge;
    }

    private function convertForPlot($data, $startDate, $endDate)
    {
        // group the data from different clients if they share the same dates
        $log = array();
        foreach ($data as $client) {
            log_message('error', $client['_id'] . '');
            foreach ($this->Service_model->log($client['_id'], $startDate, $endDate) as $value) {
                if (!array_key_exists($value['_id'], $log)) {
                    $log[$value['_id']] = array();
                }
                $log[$value['_id']][$client['_id'] . ''] = $value['value'];
            }
        }
        // fill missing data (add dates from given 'from-to' period)
        $out = array();
        $from = date('Y-m-d', strtotime($startDate));
        $to = date('Y-m-d', strtotime($endDate));
        $curr = $from;
        while ($curr <= $to) {
            $out[] = array($curr => (array_key_exists($curr, $log) ? $log[$curr] : array("" => 1)));
            $curr = date('Y-m-d', strtotime("+1 day", strtotime($curr)));
        }
        return $out;
    }

    private function responseJson($data)
    {

        header('Content-Type: application/json');

        $res = json_encode($data);

        echo($res);

        return;
    }

    private function reFormatResponse($wrongFormattedResponse, $key = 'playbasis')
    {
        $rightFormattedResponse = array();
        foreach ($wrongFormattedResponse as $index => $value) {
            foreach ($value as $date => $rewardTypeValue) {
                $rightFormattedResponse[$date] = is_object($rewardTypeValue) ? $rewardTypeValue : (object)array($key => $rewardTypeValue);
            }
        }

        return (object)$rightFormattedResponse;
    }

    private function calcDiffPercent($baseValue, $compareValue)
    {
        if ($compareValue == 0) {
            $result = 100;
        } else {
            $result = ceil((($baseValue - $compareValue) / $compareValue) * 100);
        }
        return $result;
    }

    private function getFirstDayOfWeek($ddate)
    {
        $duedt = explode("-", $ddate);
        $date = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $week = (int)date('W', $date);
        $timestamp = mktime(0, 0, 0, 1, 1, $duedt[0]) + ($week * 7 * 24 * 60 * 60);
        $timestamp_for_monday = $timestamp - 86400 * (date('N', $timestamp) - 1);
        $firstDayOfWeek = date('Y-m-d', $timestamp_for_monday);

        return $firstDayOfWeek;
    }

    private function getFirstDayOfMonth($ddate)
    {
        $duedt = explode("-", $ddate);
        $date = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $firstDayOfMonth = date('Y-m-01', $date);

        return $firstDayOfMonth;
    }

    private function formatUnitType($data, $unitType)
    {

        $fixesData = array();
        foreach ($data as $response) {
            foreach ($response as $date => $values) {
                $fixesData[$date] = $values;
            }
        }

        $formattedData = (object)$fixesData;
        $formattedResponse = array();
        if (isset($fixesData)) {
            switch ($unitType) {
                case 'day':
                    return $data;
                    break;
                case 'week':
                    foreach ($fixesData as $responseDate => $responseData) {

                        $firstDayOfWeek = $this->getFirstDayOfWeek($responseDate);

                        if (!isset($formattedResponse[$firstDayOfWeek])) {
                            $formattedResponse[$firstDayOfWeek] = array();
                        }

                        foreach ($responseData as $key => $value) {
                            if (!isset($formattedResponse[$firstDayOfWeek][$key])) {
                                $formattedResponse[$firstDayOfWeek][$key] = 0;
                            }
                            $formattedResponse[$firstDayOfWeek][$key] += $value;
                        }

                    }
                    $formattedData = (object)$formattedResponse;
                    break;
                case 'month':
                    foreach ($fixesData as $responseDate => $responseData) {

                        $firstDayOfMonth = $this->getFirstDayOfMonth($responseDate);

                        if (!isset($formattedResponse[$firstDayOfMonth])) {
                            $formattedResponse[$firstDayOfMonth] = array();
                        }

                        foreach ($responseData as $key => $value) {
                            if (!isset($formattedResponse[$firstDayOfMonth][$key])) {
                                $formattedResponse[$firstDayOfMonth][$key] = 0;
                            }
                            $formattedResponse[$firstDayOfMonth][$key] += $value;
                        }
                    }
                    $formattedData = (object)$formattedResponse;
                    break;
            }
        }
        $data = array();
        foreach ($formattedData as $formattedDataDate => $formattedDataData) {
            $data[] = (object)array($formattedDataDate => $formattedDataData);
        }
        return $data;
    }

    private function validateAccess()
    {
        return true;
        if ($this->User_model->hasPermission('access', 'metric')) {
            return true;
        } else {
            return false;
        }
    }
}