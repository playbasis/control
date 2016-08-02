<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Insights extends MY_Controller
{

    private $_api;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

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
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title_user'] = $this->lang->line('heading_title_user');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->data['main'] = 'insights';
        $this->render_page('template');
    }

    // API Calls

    public function getAction()
    {
        $action = $this->_api->getAction();
        //$action = $this->_api->getActionUsedOnly();
        $this->responseJson($action);
    }

    public function getActionLog()
    {
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $actionUnitType = $this->input->get('actionUnitType', true);

        $actionLog = $this->_api->getActionLog($startDate, $endDate);
        $formattedActionLog = $this->formatUnitType($actionLog, $actionUnitType);

        $this->responseJson($formattedActionLog);
    }

    public function getReward()
    {
        $reward = $this->_api->getReward();
        $this->responseJson($reward);
    }

    public function getRewardBadge()
    {
        $rewardBadge = $this->_api->getRewardBadge();
        $this->responseJson($rewardBadge);
    }

    public function getRewardBadgeLog()
    {
        $formattedRewardLog = $this->getRewardLog('badge');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardLevelLog()
    {
        $formattedRewardLog = $this->getRewardLog('level');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardExpLog()
    {
        $formattedRewardLog = $this->getRewardLog('exp');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardPointLog()
    {
        $formattedRewardLog = $this->getRewardLog('point');
        $this->responseJson($formattedRewardLog);
    }

    private function getRewardLog($rewardType, $startDate = null, $endDate = null, $rewardUnitType = null)
    {
        $startDate = !is_null($startDate) ? $startDate : $this->input->get('startDate', true);
        $endDate = !is_null($endDate) ? $endDate : $this->input->get('endDate', true);
        $rewardUnitType = !is_null($rewardUnitType) ? $rewardUnitType : $this->input->get('rewardUnitType', true);
        switch ($rewardType) {
            case 'badge':
                $rewardLog = $this->_api->getRewardBadgeLog($startDate, $endDate);
                break;
            case 'level':
                $rewardLog = $this->_api->getRewardLevelLog($startDate, $endDate);
                break;
            case 'exp':
                $rewardLog = $this->_api->getRewardExpLog($startDate, $endDate);
                break;
            case 'point':
                $rewardLog = $this->_api->getRewardPointLog($startDate, $endDate);
                break;
        }

        $formattedRewardLog = $this->formatUnitType($rewardLog, $rewardUnitType);

        return $formattedRewardLog;
    }

    public function getActionCompareLog()
    {

        $compareDate = $this->input->get('compareDate', true);
        $actionCompareType = $this->input->get('actionCompareType', true);

        $compareDateArray = explode("-", $compareDate);
        $compareDateTimestamp = mktime(0, 0, 0, $compareDateArray[1], $compareDateArray[2], $compareDateArray[0]);

        $n1 = date('Y-m-d', $compareDateTimestamp);
        $n2 = date('Y-m-d', strtotime('-1 day', $compareDateTimestamp));
        $n3 = date('Y-m-d', strtotime('-2 day', $compareDateTimestamp));

        $actions = $this->_api->getAction();
        $actionLog = $this->_api->getActionLog($n3, $n1);

        $formattedActionLog = $this->formatUnitType($actionLog, 'day');
        $formattedActionLog->response = $this->reFormatResponse($formattedActionLog->response);

        $n1Result = property_exists($formattedActionLog->response,
            $n1) ? (object)$formattedActionLog->response->$n1 : (object)array();
        $n2Result = property_exists($formattedActionLog->response,
            $n2) ? (object)$formattedActionLog->response->$n2 : (object)array();
        $n3Result = property_exists($formattedActionLog->response,
            $n3) ? (object)$formattedActionLog->response->$n3 : (object)array();

        $comparedResult = array(
            $n1 => array(),
            $n2 => array()
        );

        foreach ($actions->response as $index => $action) {
            $n1ResultActionValue = (property_exists($n1Result, $action) ? $n1Result->$action : 0);
            $n2ResultActionValue = (property_exists($n2Result, $action) ? $n2Result->$action : 0);
            $n3ResultActionValue = (property_exists($n3Result, $action) ? $n3Result->$action : 0);

            switch ($actionCompareType) {
                case 'percentage':
                    $comparedResult[$n1][$action] = $n1ResultActionValue . '_' . ($this->calcDiffPercent($n1ResultActionValue,
                                $n2ResultActionValue) . '%');
                    $comparedResult[$n2][$action] = $n2ResultActionValue . '_' . ($this->calcDiffPercent($n2ResultActionValue,
                                $n3ResultActionValue) . '%');
                    break;
                case 'gross':
                    $comparedResult[$n1][$action] = $n1ResultActionValue . '_' . ($n1ResultActionValue - $n2ResultActionValue);
                    $comparedResult[$n2][$action] = $n2ResultActionValue . '_' . ($n2ResultActionValue - $n3ResultActionValue);
                    break;
            }
        }

        $formattedActionLog->response = $comparedResult;

        $this->responseJson($formattedActionLog);
    }

    public function getRewardCompareLog()
    {

        $compareDate = $this->input->get('compareDate', true);
        $rewardType = $this->input->get('rewardType', true);
        $rewardCompareType = $this->input->get('rewardCompareType', true);

        $compareDateArray = explode("-", $compareDate);
        $compareDateTimestamp = mktime(0, 0, 0, $compareDateArray[1], $compareDateArray[2], $compareDateArray[0]);

        $n1 = date('Y-m-d', $compareDateTimestamp);
        $n2 = date('Y-m-d', strtotime('-1 day', $compareDateTimestamp));
        $n3 = date('Y-m-d', strtotime('-2 day', $compareDateTimestamp));

        $badges = $this->_api->getRewardBadge();
        $formattedRewardLog = $this->getRewardLog($rewardType, $n3, $n1, 'day');

        $formattedRewardLog->response = $this->reFormatResponse($formattedRewardLog->response, $rewardType);

        $n1Result = property_exists($formattedRewardLog->response,
            $n1) ? (object)$formattedRewardLog->response->$n1 : (object)array();
        $n2Result = property_exists($formattedRewardLog->response,
            $n2) ? (object)$formattedRewardLog->response->$n2 : (object)array();
        $n3Result = property_exists($formattedRewardLog->response,
            $n3) ? (object)$formattedRewardLog->response->$n3 : (object)array();

        $comparedResult = array(
            $n1 => array(),
            $n2 => array()
        );

        if ($rewardType == 'badge') {

            foreach ($badges->response->badges as $badge) {

                $badge_id = ($badge->badge_id);

                $n1ResultRewardValue = property_exists($n1Result, $badge_id) ? $n1Result->$badge_id : 0;
                $n2ResultRewardValue = property_exists($n2Result, $badge_id) ? $n2Result->$badge_id : 0;
                $n3ResultRewardValue = property_exists($n3Result, $badge_id) ? $n3Result->$badge_id : 0;

                switch ($rewardCompareType) {
                    case 'percentage':
                        $comparedResult[$n1][$badge->badge_id] = $n1ResultRewardValue . '_' . ($this->calcDiffPercent($n1ResultRewardValue,
                                    $n2ResultRewardValue) . '%');
                        $comparedResult[$n2][$badge->badge_id] = $n2ResultRewardValue . '_' . ($this->calcDiffPercent($n2ResultRewardValue,
                                    $n3ResultRewardValue) . '%');
                        break;
                    case 'gross':
                        $comparedResult[$n1][$badge->badge_id] = $n1ResultRewardValue . '_' . ($n1ResultRewardValue - $n2ResultRewardValue);
                        $comparedResult[$n2][$badge->badge_id] = $n2ResultRewardValue . '_' . ($n2ResultRewardValue - $n3ResultRewardValue);
                        break;
                }
            }

        } else {

            $compareResult = $formattedRewardLog->response->$compareDate;

            foreach ($compareResult as $type => $result) {

                $n1ResultRewardValue = (property_exists($n1Result, $rewardType) ? $n1Result->$rewardType : 0);
                $n2ResultRewardValue = (property_exists($n2Result, $rewardType) ? $n2Result->$rewardType : 0);
                $n3ResultRewardValue = (property_exists($n3Result, $rewardType) ? $n3Result->$rewardType : 0);

                switch ($rewardCompareType) {
                    case 'percentage':
                        $comparedResult[$n1][$rewardType] = $n1ResultRewardValue . '_' . ($this->calcDiffPercent($n1ResultRewardValue,
                                    $n2ResultRewardValue) . '%');
                        $comparedResult[$n2][$rewardType] = $n2ResultRewardValue . '_' . ($this->calcDiffPercent($n2ResultRewardValue,
                                    $n3ResultRewardValue) . '%');
                        break;
                    case 'gross':
                        $comparedResult[$n1][$rewardType] = $n1ResultRewardValue . '_' . ($n1ResultRewardValue - $n2ResultRewardValue);
                        $comparedResult[$n2][$rewardType] = $n2ResultRewardValue . '_' . ($n2ResultRewardValue - $n3ResultRewardValue);
                        break;
                }
            }
        }

        $formattedRewardLog->response = $comparedResult;

        $this->responseJson($formattedRewardLog);
    }

    public function getUserRegLog()
    {
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $userUnitType = $this->input->get('userUnitType', true);

        $userLog = $this->_api->getUserRegLog($startDate, $endDate);
        $formattedUserLog = $this->formatUnitType($userLog, $userUnitType);

        $this->responseJson($formattedUserLog);
    }

    public function getUserDAULog()
    {
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $userUnitType = $this->input->get('userUnitType', true);

        $userLog = $this->_api->getUserDAULog($startDate, $endDate);
        $formattedUserLog = $this->formatUnitType($userLog, $userUnitType);

        $this->responseJson($formattedUserLog);
    }


    public function getUserMAULog()
    {
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $userUnitType = $this->input->get('userUnitType', true);

        $userLog = $this->_api->getUserMAULog($startDate, $endDate, $userUnitType);

        $this->responseJson($userLog);
    }

    // Helpers methods

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
        foreach ($data->response as $response) {
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
        $data->response = array();
        foreach ($formattedData as $formattedDataDate => $formattedDataData) {
            $data->response[] = (object)array($formattedDataDate => $formattedDataData);
        }
        return $data;
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'insights') && $this->Feature_model->getFeatureExistByClientId($client_id, 'insights')
        ) {
            return true;
        } else {
            return false;
        }
    }
}