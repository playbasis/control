<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Insights extends MY_Controller {

    private $_api;

	public function __construct()
	{
		parent::__construct();

		$this->load->model('User_model');
		if(!$this->User_model->isLogged()){
			redirect('/login', 'refresh');
		}

		$lang = get_lang($this->session, $this->config);
		$this->lang->load($lang['name'], $lang['folder']);
		$this->load->helper('url');

		$result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
		$this->_api = $this->playbasisapi;
		$this->_api->set_api_key($result['api_key']);
		$this->_api->set_api_secret($result['api_secret']);
	}

    public function index(){
        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

    public function getAction(){
//        $action = $this->_api->getAction();
//        $action = $this->_api->getActionUsedOnly();
        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      "register",
      "transfer",
      "withdraw",
      "deposit",
      "pay",
      "geofence",
      "day",
      "join-friend",
      "invite-friend",
      "re-engage-user",
      "complete-profile"
   ],
   "timestamp":1448891656,
   "time":"Mon, 30 Nov 2015 20:54:16 +0700 Asia\/Bangkok",
   "version":"2.1.40"
}';
        $action = json_decode($json);
        $this->responseJson($action);
    }

    public function getActionLog()
    {
        $startDate = $this->input->get('startDate', true);
        $endDate = $this->input->get('endDate', true);
        $actionUnitType = $this->input->get('actionUnitType', true);

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      {
         "2015-11-25":{
            "register":1000,
            "pay":10143,
            "transfer":1500,
            "withdraw":20,
            "deposit":412,
            "geofence":20,
            "day":10,
            "complete-profile":55,
            "join-friend":680,
            "invite-friend":350
         }
      },
      {
         "2015-11-26":{
            "register":2000,
            "pay":6150,
            "transfer":7949,
            "withdraw":494,
            "deposit":1256,
            "geofence":210,
            "day":110,
            "complete-profile":515,
            "join-friend":180,
            "invite-friend":56
         }
      },
      {
         "2015-11-27":{
            "register":3000,
            "pay":494,
            "transfer":1500,
            "withdraw":7949,
            "deposit":1256,
            "geofence":20,
            "day":10,
            "complete-profile":1255,
            "join-friend":568,
            "invite-friend":223
         }
      },
      {
         "2015-11-28":{
            "register":4000,
            "pay":412,
            "transfer":200,
            "withdraw":10143,
            "deposit":6150,
            "geofence":20,
            "day":10,
            "complete-profile":2,
            "join-friend":111,
            "invite-friend":88
         }
      },
      {
         "2015-11-29":{
            "register":5000,
            "withdraw":33541,
            "transfer":1008,
            "deposit":6910,
            "pay":435,
            "geofence":55,
            "day":55,
            "complete-profile":12,
            "join-friend":557,
            "invite-friend":123
         }
      },
      {
         "2015-11-30":{
            "register":4000,
            "deposit":677,
            "transfer":1002,
            "pay":5720,
            "withdraw":32191,
            "geofence":620,
            "day":310,
            "complete-profile":755,
            "join-friend":80,
            "invite-friend":30
         }
      },
      {
         "2015-12-01":{
            "register":3000,
            "pay":841,
            "deposit":709,
            "withdraw":31202,
            "transfer":2005,
            "geofence":210,
            "day":160,
            "complete-profile":61,
            "join-friend":60,
            "invite-friend":450
         }
      },
      {
         "2015-12-02":{
            "register":2000,
            "transfer":14,
            "pay":600,
            "withdraw":14143,
            "deposit":675,
            "geofence":420,
            "day":210,
            "complete-profile":155,
            "join-friend":623,
            "invite-friend":345
         }
      },
      {
         "2015-12-03":{
            "register":1000,
            "transfer":600,
            "pay":14,
            "withdraw":675,
            "deposit":14143,
            "geofence":200,
            "day":198,
            "complete-profile":384,
            "join-friend":899,
            "invite-friend":532
         }
      }
   ],
   "timestamp":1448880302,
   "time":"Mon, 30 Nov 2015 17:45:02 +0700 Asia\\/Bangkok",
   "version":"2.1.39"
}';
        $result = json_decode($json);

        $formattedActionLog = $this->formatUnitType($result, $actionUnitType);

        $this->responseJson($formattedActionLog);
    }

    public function getReward(){
        $reward = $this->_api->getReward();
        $this->responseJson($reward);
    }

    public function getRewardBadge(){
        $rewardBadge = $this->_api->getRewardBadge();
        $this->responseJson($rewardBadge);
    }

    public function getRewardBadgeLog(){
        $formattedRewardLog = $this->getRewardLog('badge');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardLevelLog(){
        $formattedRewardLog = $this->getRewardLog('level');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardExpLog(){
        $formattedRewardLog = $this->getRewardLog('exp');
        $this->responseJson($formattedRewardLog);
    }

    public function getRewardPointLog()
    {

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      {
         "2015-11-01":{
            "point":0
         }
      },
      {
         "2015-11-02":{
            "point":0
         }
      },
      {
         "2015-11-03":{
            "point":0
         }
      },
      {
         "2015-11-04":{
            "point":0
         }
      },
      {
         "2015-11-05":{
            "point":0
         }
      },
      {
         "2015-11-06":{
            "point":0
         }
      },
      {
         "2015-11-07":{
            "point":0
         }
      },
      {
         "2015-11-08":{
            "point":0
         }
      },
      {
         "2015-11-09":{
            "point":0
         }
      },
      {
         "2015-11-10":{
            "point":0
         }
      },
      {
         "2015-11-11":{
            "point":0
         }
      },
      {
         "2015-11-12":{
            "point":0
         }
      },
      {
         "2015-11-13":{
            "point":0
         }
      },
      {
         "2015-11-14":{
            "point":0
         }
      },
      {
         "2015-11-15":{
            "point":0
         }
      },
      {
         "2015-11-16":{
            "point":64
         }
      },
      {
         "2015-11-17":{
            "point":38
         }
      },
      {
         "2015-11-18":{
            "point":26
         }
      },
      {
         "2015-11-19":{
            "point":12
         }
      },
      {
         "2015-11-20":{
            "point":2
         }
      },
      {
         "2015-11-21":{
            "point":3
         }
      },
      {
         "2015-11-22":{
            "point":66
         }
      },
      {
         "2015-11-23":{
            "point":20
         }
      },
      {
         "2015-11-24":{
            "point":25
         }
      },
      {
         "2015-11-25":{
            "point":76
         }
      },
      {
         "2015-11-26":{
            "point":34
         }
      },
      {
         "2015-11-27":{
            "point":58
         }
      },
      {
         "2015-11-28":{
            "point":13
         }
      },
      {
         "2015-11-29":{
            "point":6
         }
      },
      {
         "2015-11-30":{
            "point":8
         }
      },
      {
         "2015-12-01":{
            "point":12
         }
      },
      {
         "2015-12-02":{
            "point":10
         }
      },
      {
         "2015-12-03":{
            "point":5
         }
      }
   ],
   "timestamp":1448888993,
   "time":"Mon, 30 Nov 2015 20:09:53 +0700 Asia\/Bangkok",
   "version":"2.1.39"
}';
        $result = json_decode($json);
        $this->responseJson($result);
    }

    private function getRewardLog($rewardType,$startDate = null,$endDate = null,$rewardUnitType = null){
        $startDate      = !is_null($startDate)?$startDate:$this->input->get('startDate', TRUE);
        $endDate        = !is_null($endDate)?$endDate:$this->input->get('endDate', TRUE);
        $rewardUnitType = !is_null($rewardUnitType)?$rewardUnitType:$this->input->get('rewardUnitType',TRUE);
        switch($rewardType){
            case 'badge':
                $rewardLog = $this->_api->getRewardBadgeLog($startDate,$endDate);
                break;
            case 'level':
                $rewardLog = $this->_api->getRewardLevelLog($startDate,$endDate);
                break;
            case 'exp':
                $rewardLog = $this->_api->getRewardExpLog($startDate,$endDate);
                break;
            case 'point':
                $rewardLog = $this->_api->getRewardPointLog($startDate,$endDate);
                break;
        }

        $formattedRewardLog = $this->formatUnitType($rewardLog,$rewardUnitType);

        return $formattedRewardLog;
    }

    public function getActionCompareLog(){

        $compareDate            = $this->input->get('compareDate', TRUE);
        $actionCompareType      = $this->input->get('actionCompareType',TRUE);

        $compareDateArray       = explode("-", $compareDate);
        $compareDateTimestamp   = mktime(0, 0, 0, $compareDateArray[1], $compareDateArray[2], $compareDateArray[0]);

        $n1 = date('Y-m-d', $compareDateTimestamp );
        $n2 = date('Y-m-d', strtotime( '-1 day', $compareDateTimestamp));
        $n3 = date('Y-m-d', strtotime( '-2 day', $compareDateTimestamp));

        $actions    = $this->_api->getAction();
        $actionLog  = $this->_api->getActionLog($n3, $n1);

        $formattedActionLog             = $this->formatUnitType($actionLog, 'day');
        $formattedActionLog->response   = $this->reFormatResponse($formattedActionLog->response);

        $n1Result       = property_exists($formattedActionLog->response,$n1) ? (object)$formattedActionLog->response->$n1 : (object)array();
        $n2Result       = property_exists($formattedActionLog->response,$n2) ? (object)$formattedActionLog->response->$n2 : (object)array();
        $n3Result       = property_exists($formattedActionLog->response,$n3) ? (object)$formattedActionLog->response->$n3 : (object)array();

        $comparedResult = array(
            $n1 => array(),
            $n2 => array()
        );

        foreach($actions->response as $index => $action){
            $n1ResultActionValue = (property_exists($n1Result,$action)?$n1Result->$action:0);
            $n2ResultActionValue = (property_exists($n2Result,$action)?$n2Result->$action:0);
            $n3ResultActionValue = (property_exists($n3Result,$action)?$n3Result->$action:0);

            switch($actionCompareType){
                case 'percentage':
                    $comparedResult[$n1][$action] = $n1ResultActionValue .'_'.($this->calcDiffPercent($n1ResultActionValue, $n2ResultActionValue).'%');
                    $comparedResult[$n2][$action] = $n2ResultActionValue .'_'.($this->calcDiffPercent($n2ResultActionValue, $n3ResultActionValue).'%');
                    break;
                case 'gross':
                    $comparedResult[$n1][$action] = $n1ResultActionValue .'_'.($n1ResultActionValue - $n2ResultActionValue);
                    $comparedResult[$n2][$action] = $n2ResultActionValue .'_'.($n2ResultActionValue - $n3ResultActionValue);
                    break;
            }
        }

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":{
      "2015-12-02":{
         "geofence":"2_100%",
         "register":"5_100%",
         "complete-profile":"8_100%",
         "transfer":"12_100%",
         "withdraw":"48_100%",
         "deposit":"20_100%",
         "pay":"5_100%"
      },
      "2015-12-03":{
         "geofence":"5_100%",
         "register":"10_100%",
         "complete-profile":"5_100%",
         "transfer":"30_100%",
         "withdraw":"20_100%",
         "deposit":"15_100%",
         "pay":"15_100%"
      }
   },
   "timestamp":1448889953,
   "time":"Mon, 30 Nov 2015 20:25:53 +0700 Asia\/Bangkok",
   "version":"2.1.40"
}';
        $result = json_decode($json);

        $this->responseJson($result);
    }

    public function getRewardCompareLog(){

        $compareDate            = $this->input->get('compareDate', TRUE);
        $rewardType             = $this->input->get('rewardType',TRUE);
        $rewardCompareType      = $this->input->get('rewardCompareType',TRUE);

        $compareDateArray       = explode("-", $compareDate);
        $compareDateTimestamp   = mktime(0, 0, 0, $compareDateArray[1], $compareDateArray[2], $compareDateArray[0]);

        $n1 = date('Y-m-d', $compareDateTimestamp );
        $n2 = date('Y-m-d', strtotime( '-1 day', $compareDateTimestamp));
        $n3 = date('Y-m-d', strtotime( '-2 day', $compareDateTimestamp));

        $badges             = $this->_api->getRewardBadge();
        $formattedRewardLog = $this->getRewardLog($rewardType, $n3, $n1, 'day');

        $formattedRewardLog->response  = $this->reFormatResponse($formattedRewardLog->response,$rewardType);

        $n1Result       = property_exists($formattedRewardLog->response,$n1) ? (object)$formattedRewardLog->response->$n1 : (object)array();
        $n2Result       = property_exists($formattedRewardLog->response,$n2) ? (object)$formattedRewardLog->response->$n2 : (object)array();
        $n3Result       = property_exists($formattedRewardLog->response,$n3) ? (object)$formattedRewardLog->response->$n3 : (object)array();

        $comparedResult = array(
            $n1 =>array(),
            $n2 =>array()
        );

        if ($rewardType == 'badge'){

            foreach($badges->response->badges as $badge){

                $badge_id = ($badge->badge_id);

                $n1ResultRewardValue    = property_exists($n1Result,$badge_id) ? $n1Result->$badge_id : 0;
                $n2ResultRewardValue    = property_exists($n2Result,$badge_id) ? $n2Result->$badge_id : 0;
                $n3ResultRewardValue    = property_exists($n3Result,$badge_id) ? $n3Result->$badge_id : 0;

                switch($rewardCompareType){
                    case 'percentage':
                        $comparedResult[$n1][$badge->badge_id] = $n1ResultRewardValue .'_'.($this->calcDiffPercent($n1ResultRewardValue, $n2ResultRewardValue).'%');
                        $comparedResult[$n2][$badge->badge_id] = $n2ResultRewardValue .'_'.($this->calcDiffPercent($n2ResultRewardValue, $n3ResultRewardValue).'%');
                        break;
                    case 'gross':
                        $comparedResult[$n1][$badge->badge_id] = $n1ResultRewardValue .'_'.($n1ResultRewardValue - $n2ResultRewardValue);
                        $comparedResult[$n2][$badge->badge_id] = $n2ResultRewardValue .'_'.($n2ResultRewardValue - $n3ResultRewardValue);
                        break;
                }
            }

        } else {

            $compareResult  = $formattedRewardLog->response->$compareDate;

            foreach($compareResult as $type => $result){

                $n1ResultRewardValue = (property_exists($n1Result,$rewardType)?$n1Result->$rewardType:0);
                $n2ResultRewardValue = (property_exists($n2Result,$rewardType)?$n2Result->$rewardType:0);
                $n3ResultRewardValue = (property_exists($n3Result,$rewardType)?$n3Result->$rewardType:0);

                switch($rewardCompareType){
                    case 'percentage':
                        $comparedResult[$n1][$rewardType] = $n1ResultRewardValue .'_'.($this->calcDiffPercent($n1ResultRewardValue, $n2ResultRewardValue).'%');
                        $comparedResult[$n2][$rewardType] = $n2ResultRewardValue .'_'.($this->calcDiffPercent($n2ResultRewardValue, $n3ResultRewardValue).'%');
                        break;
                    case 'gross':
                        $comparedResult[$n1][$rewardType] = $n1ResultRewardValue .'_'.($n1ResultRewardValue - $n2ResultRewardValue);
                        $comparedResult[$n2][$rewardType] = $n2ResultRewardValue .'_'.($n2ResultRewardValue - $n3ResultRewardValue);
                        break;
                }
            }
        }

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":{
      "2015-12-02":{
         "point":"33_100%"
      },
      "2015-12-03":{
         "point":"66_100%"
      }
   },
   "timestamp":1448888994,
   "time":"Mon, 30 Nov 2015 20:09:54 +0700 Asia\/Bangkok",
   "version":"2.1.39"
}';

        $result = json_decode($json);

        $this->responseJson($result);
    }

    public function getUserRegLog(){
        $startDate      = $this->input->get('startDate', TRUE);
        $endDate        = $this->input->get('endDate', TRUE);
        $userUnitType   = $this->input->get('userUnitType',TRUE);

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      {
         "2015-11-01":{
            "count":1914
         }
      },
      {
         "2015-11-02":{
            "count":1884
         }
      },
      {
         "2015-11-03":{
            "count":1937
         }
      },
      {
         "2015-11-04":{
            "count":1753
         }
      },
      {
         "2015-11-05":{
            "count":1682
         }
      },
      {
         "2015-11-06":{
            "count":1607
         }
      },
      {
         "2015-11-07":{
            "count":1745
         }
      },
      {
         "2015-11-08":{
            "count":1810
         }
      },
      {
         "2015-11-09":{
            "count":1637
         }
      },
      {
         "2015-11-10":{
            "count":1597
         }
      },
      {
         "2015-11-11":{
            "count":1600
         }
      },
      {
         "2015-11-12":{
            "count":1532
         }
      },
      {
         "2015-11-13":{
            "count":2424
         }
      },
      {
         "2015-11-14":{
            "count":3327
         }
      },
      {
         "2015-11-15":{
            "count":3197
         }
      },
      {
         "2015-11-16":{
            "count":2899
         }
      },
      {
         "2015-11-17":{
            "count":2097
         }
      },
      {
         "2015-11-18":{
            "count":2390
         }
      },
      {
         "2015-11-19":{
            "count":2288
         }
      },
      {
         "2015-11-20":{
            "count":2134
         }
      },
      {
         "2015-11-21":{
            "count":2390
         }
      },
      {
         "2015-11-22":{
            "count":2465
         }
      },
      {
         "2015-11-23":{
            "count":1813
         }
      },
      {
         "2015-11-24":{
            "count":1760
         }
      },
      {
         "2015-11-25":{
            "count":1678
         }
      },
      {
         "2015-11-26":{
            "count":2249
         }
      },
      {
         "2015-11-27":{
            "count":2649
         }
      },
      {
         "2015-11-28":{
            "count":2886
         }
      },
      {
         "2015-11-29":{
            "count":2692
         }
      },
      {
         "2015-11-30":{
            "count":2692
         }
      },
      {
         "2015-12-01":{
            "count":2692
         }
      },
      {
         "2015-12-02":{
            "count":2692
         }
      },
      {
         "2015-12-03":{
            "count":2692
         }
      }
   ],
   "timestamp":1448885304,
   "time":"Mon, 30 Nov 2015 19:08:24 +0700 Asia\\/Bangkok",
   "version":"2.1.39"
}';
        $result = json_decode($json);
        $formattedUserLog = $this->formatUnitType($result, $userUnitType);

        $this->responseJson($formattedUserLog);
    }

    public function getUserDAULog(){
        $startDate      = $this->input->get('startDate', TRUE);
        $endDate        = $this->input->get('endDate', TRUE);
        $userUnitType   = $this->input->get('userUnitType',TRUE);

//        $userLog            = $this->_api->getUserDAULog($startDate,$endDate);
        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      {
         "2015-11-01":{
            "count":1582
         }
      },
      {
         "2015-11-02":{
            "count":544
         }
      },
      {
         "2015-11-03":{
            "count":1455
         }
      },
      {
         "2015-11-04":{
            "count":1245
         }
      },
      {
         "2015-11-05":{
            "count":1245
         }
      },
      {
         "2015-11-06":{
            "count":955
         }
      },
      {
         "2015-11-07":{
            "count":982
         }
      },
      {
         "2015-11-08":{
            "count":1637
         }
      },
      {
         "2015-11-09":{
            "count":1810
         }
      },
      {
         "2015-11-10":{
            "count":1532
         }
      },
      {
         "2015-11-11":{
            "count":1600
         }
      },
      {
         "2015-11-12":{
            "count":1597
         }
      },
      {
         "2015-11-13":{
            "count":224
         }
      },
      {
         "2015-11-14":{
            "count":327
         }
      },
      {
         "2015-11-15":{
            "count":3197
         }
      },
      {
         "2015-11-16":{
            "count":2099
         }
      },
      {
         "2015-11-17":{
            "count":2997
         }
      },
      {
         "2015-11-18":{
            "count":2390
         }
      },
      {
         "2015-11-19":{
            "count":5488
         }
      },
      {
         "2015-11-20":{
            "count":2334
         }
      },
      {
         "2015-11-21":{
            "count":2390
         }
      },
      {
         "2015-11-22":{
            "count":2465
         }
      },
      {
         "2015-11-23":{
            "count":2813
         }
      },
      {
         "2015-11-24":{
            "count":3760
         }
      },
      {
         "2015-11-25":{
            "count":1678
         }
      },
      {
         "2015-11-26":{
            "count":4249
         }
      },
      {
         "2015-11-27":{
            "count":1649
         }
      },
      {
         "2015-11-28":{
            "count":3886
         }
      },
      {
         "2015-11-29":{
            "count":1692
         }
      },
      {
         "2015-11-30":{
            "count":1692
         }
      },
      {
         "2015-12-01":{
            "count":3692
         }
      },
      {
         "2015-12-02":{
            "count":5692
         }
      },
      {
         "2015-12-03":{
            "count":6692
         }
      }
   ],
   "timestamp":1448885304,
   "time":"Mon, 30 Nov 2015 19:08:24 +0700 Asia\\/Bangkok",
   "version":"2.1.39"
}';
        $result = json_decode($json);
        $formattedUserLog   = $this->formatUnitType($result,$userUnitType);

        $this->responseJson($formattedUserLog);
    }


    public function getUserMAULog(){
        $startDate      = $this->input->get('startDate', TRUE);
        $endDate        = $this->input->get('endDate', TRUE);
        $userUnitType   = $this->input->get('userUnitType',TRUE);

//        $userLog        = $this->_api->getUserMAULog($startDate, $endDate, $userUnitType);

        $json = '{
   "success":true,
   "error_code":"0000",
   "message":"Success",
   "response":[
      {
         "2015-11-01":{
            "count":1582
         }
      },
      {
         "2015-11-02":{
            "count":744
         }
      },
      {
         "2015-11-03":{
            "count":455
         }
      },
      {
         "2015-11-04":{
            "count":945
         }
      },
      {
         "2015-11-05":{
            "count":845
         }
      },
      {
         "2015-11-06":{
            "count":595
         }
      },
      {
         "2015-11-07":{
            "count":782
         }
      },
      {
         "2015-11-08":{
            "count":857
         }
      },
      {
         "2015-11-09":{
            "count":810
         }
      },
      {
         "2015-11-10":{
            "count":132
         }
      },
      {
         "2015-11-11":{
            "count":600
         }
      },
      {
         "2015-11-12":{
            "count":597
         }
      },
      {
         "2015-11-13":{
            "count":924
         }
      },
      {
         "2015-11-14":{
            "count":827
         }
      },
      {
         "2015-11-15":{
            "count":397
         }
      },
      {
         "2015-11-16":{
            "count":299
         }
      },
      {
         "2015-11-17":{
            "count":997
         }
      },
      {
         "2015-11-18":{
            "count":390
         }
      },
      {
         "2015-11-19":{
            "count":588
         }
      },
      {
         "2015-11-20":{
            "count":734
         }
      },
      {
         "2015-11-21":{
            "count":290
         }
      },
      {
         "2015-11-22":{
            "count":465
         }
      },
      {
         "2015-11-23":{
            "count":813
         }
      },
      {
         "2015-11-24":{
            "count":760
         }
      },
      {
         "2015-11-25":{
            "count":178
         }
      },
      {
         "2015-11-26":{
            "count":249
         }
      },
      {
         "2015-11-27":{
            "count":149
         }
      },
      {
         "2015-11-28":{
            "count":886
         }
      },
      {
         "2015-11-29":{
            "count":162
         }
      },
      {
         "2015-11-30":{
            "count":692
         }
      },
      {
         "2015-12-01":{
            "count":692
         }
      },
      {
         "2015-12-02":{
            "count":692
         }
      },
      {
         "2015-12-03":{
            "count":692
         }
      }
   ],
   "timestamp":1448885304,
   "time":"Mon, 30 Nov 2015 19:08:24 +0700 Asia\\/Bangkok",
   "version":"2.1.39"
}';

        $result = json_decode($json);
        $formattedUserLog   = $this->formatUnitType($result,$userUnitType);
        $this->responseJson($formattedUserLog);
    }

    // Helpers methods

    private function responseJson($data){

        header('Content-Type: application/json');

        $res = json_encode($data);

        echo($res);

        return;
    }

    private function reFormatResponse($wrongFormattedResponse, $key = 'playbasis'){
        $rightFormattedResponse = array();
        foreach($wrongFormattedResponse as $index => $value){
            foreach($value as $date => $rewardTypeValue){
                $rightFormattedResponse[$date] = is_object($rewardTypeValue)?$rewardTypeValue:(object)array($key => $rewardTypeValue);
            }
        }

        return (object)$rightFormattedResponse;
    }

    private function calcDiffPercent($baseValue,$compareValue){
        if($compareValue == 0){
            $result = 100;
        }else{
            $result = ceil((($baseValue-$compareValue)/$compareValue)*100);
        }
        return $result;
    }

    private function getFirstDayOfWeek($ddate){
        $duedt                  = explode("-", $ddate);
        $date                   = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $week                   = (int)date('W', $date);
        $timestamp              = mktime( 0, 0, 0, 1, 1,  $duedt[0] ) + ( $week * 7 * 24 * 60 * 60 );
        $timestamp_for_monday   = $timestamp - 86400 * ( date( 'N', $timestamp ) - 1 );
        $firstDayOfWeek         = date( 'Y-m-d', $timestamp_for_monday );

        return $firstDayOfWeek;
    }

    private function getFirstDayOfMonth($ddate){
        $duedt = explode("-", $ddate);
        $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $firstDayOfMonth = date( 'Y-m-01', $date );

        return $firstDayOfMonth;
    }

    private function formatUnitType($data, $unitType){

        $fixesData = array();
        foreach($data->response as $response){
            foreach($response as $date => $values){
                $fixesData[$date] = $values;
            }
        }

        $formattedData      = (object)$fixesData;
        $formattedResponse  = array();
        if(isset($fixesData)){
            switch($unitType){
                case 'day':
                    return $data;
                    break;
                case 'week':
                    foreach($fixesData as $responseDate => $responseData){

                        $firstDayOfWeek = $this->getFirstDayOfWeek($responseDate);

                        if(!isset($formattedResponse[$firstDayOfWeek])){
                            $formattedResponse[$firstDayOfWeek] = array();
                        }

                        foreach($responseData as $key => $value){
                            if(!isset($formattedResponse[$firstDayOfWeek][$key])){
                                $formattedResponse[$firstDayOfWeek][$key] = 0;
                            }
                            $formattedResponse[$firstDayOfWeek][$key] += $value;
                        }

                    }
                    $formattedData = (object)$formattedResponse;
                    break;
                case 'month':
                    foreach($fixesData as $responseDate => $responseData){

                        $firstDayOfMonth = $this->getFirstDayOfMonth($responseDate);

                        if(!isset($formattedResponse[$firstDayOfMonth])){
                            $formattedResponse[$firstDayOfMonth] = array();
                        }

                        foreach($responseData as $key => $value){
                            if(!isset($formattedResponse[$firstDayOfMonth][$key])){
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
        foreach($formattedData as $formattedDataDate => $formattedDataData){
            $data->response[] = (object)array($formattedDataDate => $formattedDataData);
        }
        return $data;
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'insights') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'insights')) {
            return true;
        } else {
            return false;
        }
    }
}