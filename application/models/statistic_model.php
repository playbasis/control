<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Statistic_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getNewRegister($data)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $Q =  $this->mongo_db->get('playbasis_player');

        $register_player = array();

        foreach ($Q as $r) {
            $register_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($register_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $register_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($register_player);
        return $this->fillGap($register_player, $data);
    }

    public function getPlayerRewardPointStat($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','reward_id'));
        $this->mongo_db->where('name', array('$nin' =>  array('exp', 'badge')));
        $this->mongo_db->where('status', true);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_point_player = array();

        foreach ($Q as $r) {
            $reward_point_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_point_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_point_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_point_player);
        return $this->fillGap($reward_point_player, $data);
    }

    public function getPlayerRewardBadgeStat($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','reward_id'));
        $this->mongo_db->where('name', 'badge');
        $this->mongo_db->where('status', true);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_badge_player);
        return $this->fillGap($reward_badge_player, $data);
    }

    public function getPlayerRewardLevelStat($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','reward_id'));
        $this->mongo_db->where('name', 'exp');
        $this->mongo_db->where('status', true);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_badge_player);
        return $this->fillGap($reward_badge_player, $data);
    }

    public function getDailyActionmeaturement($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','action_id','name','color','icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $actions =  $this->mongo_db->get('playbasis_action_to_client');

        foreach ($actions as &$action) {
            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-1 day' . $data['date'] )), '$lte' => new MongoDate(strtotime ($data['date']))));
            $r1 =  $this->mongo_db->get('playbasis_action_log');

            $action['value'] = (int)count($r1);

            $action['circle'] = $action['color']."Circle";
            $action['class'] = $action['icon'];

            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-2 day' . $data['date'] )), '$lte' => new MongoDate(strtotime ( '-1 day' . $data['date'] ))));
            $r2 =  $this->mongo_db->get('playbasis_action_log');

            $action['count_of_last_day'] =  (int)count($r2);

            //zero check
            $rateFactor = ($action['count_of_last_day'])? $action['count_of_last_day'] : 1;

            //advancement rate
            $action['advancement_direction'] = ($action['value'] - $action['count_of_last_day']) >= 0 ? '+' : '-';
            $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_day']);
            $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_day'])*100)/$rateFactor));
        }

        return $actions;
    }

    public function getWeeklyActionmeaturement($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','action_id','name','color','icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $actions =  $this->mongo_db->get('playbasis_action_to_client');

        foreach ($actions as &$action) {
            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-1 week', strtotime($data['date']) )), '$lte' => new MongoDate(strtotime($data['date']))));
            $r1 =  $this->mongo_db->get('playbasis_action_log');

            $action['value'] = (int)count($r1);

            $action['circle'] = $action['color']."Circle";
            $action['class'] = $action['icon'];

            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-2 week', strtotime($data['date']) )), '$lte' => new MongoDate(strtotime ( '-1 week', strtotime($data['date']) ))));
            $r2 =  $this->mongo_db->get('playbasis_action_log');

            $action['count_of_last_week'] =  (int)count($r2);

            //zero check
            $rateFactor = ($action['count_of_last_week'])? $action['count_of_last_week'] : 1;

            //advancement rate
            $action['advancement_direction'] = ($action['value'] - $action['count_of_last_week']) >= 0 ? '+' : '-';
            $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_week']);
            $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_week'])*100)/$rateFactor));
        }

        return $actions;
    }

    public function getMonthlyActionmeaturement($data) {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','action_id','name','color','icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $actions =  $this->mongo_db->get('playbasis_action_to_client');

        foreach ($actions as &$action) {
            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-1 month' . $data['date'] )), '$lte' => new MongoDate(strtotime ($data['date']))));
            $r1 =  $this->mongo_db->get('playbasis_action_log');

            $action['value'] = (int)count($r1);

            $action['circle'] = $action['color']."Circle";
            $action['class'] = $action['icon'];

            $this->mongo_db->select(array('_id','action_id','name','color','icon'));
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $this->mongo_db->where('action_id', new MongoID($action['_id']));
            $this->mongo_db->where('date_added', array('$gt' => new MongoDate(strtotime ( '-2 month' . $data['date'] )), '$lte' => new MongoDate(strtotime ( '-1 month' . $data['date'] ))));
            $r2 =  $this->mongo_db->get('playbasis_action_log');

            $action['count_of_last_month'] =  (int)count($r2);

            //zero check
            $rateFactor = ($action['count_of_last_month'])? $action['count_of_last_month'] : 1;

            //advancement rate
            $action['advancement_direction'] = ($action['value'] - $action['count_of_last_month']) >= 0 ? '+' : '-';
            $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_month']);
            $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_month'])*100)/$rateFactor));
        }

        return $actions;
    }

    public function LeaderBoard($data){
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', 'exp');
        $r =  $this->mongo_db->get('playbasis_reward');

        $this->mongo_db->select(array('_id','pb_player_id','value'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', new MongoID($r[0]['_id']));
        $this->mongo_db->order_by(array('value' => -1));

        if (isset($data['limit'])) {
            $this->mongo_db->limit((int)$data['limit']);
        }else{
            $this->mongo_db->limit(10);
        }

        if (isset($data['start'])) {
            $this->mongo_db->offset((int)$data['start']);
        }else{
            $this->mongo_db->offset(0);
        }

        $result =  $this->mongo_db->get('playbasis_reward_to_player');

        $LeaderBoardList = array();

        foreach ($result as $player) {
            $info = $this->getPlayerInfo($player['pb_player_id']);

            array_push($LeaderBoardList, array(
                    'player_id'   => $info[0]['_id'],
                    'player_name' => $info[0]['first_name'] .' '. $info[0]['last_name'],
                    'exp'         => $info[0]['exp'],
                    'level'       => $info[0]['level'],
                    'point'       => $player['value'],
                    'image'       => $info[0]['image'],
                    'date_added'  => $info[0]['date_added']
                )
            );
        }

        return $LeaderBoardList;
    }

    public function getPlayerInfo($pb_player_id){
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id','pb_player_id','first_name','last_name','exp','level','image','date_added'));
        $this->mongo_db->where('_id', new MongoID($pb_player_id));
        $result =  $this->mongo_db->get('playbasis_player');

        return $result;
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

    //utility fill gap
    private function fillGap($dataArray , $data) {

        $resultArray = array();

        if (isset($data['date_start']) && isset($data['date_expire'])) {

            $diff_time = ($data['date_expire'] - $data['date_start'])*1000;

            $interval = ceil($diff_time/86400000);

            for($i = $interval; $i >= 0; $i--) {
                $minus = '-'.$i.' day';
                $count = 0;
                foreach ($dataArray as $value) {
                    if ($value['fulldate'] == date('Y-m-d', strtotime($minus, $data['date_expire']))) {
                        $count = $value['count']; break;
                    }
                }
                $resultArray[$i] = array(
                    'date' => strtotime($minus, $data['date_expire']),
                    'count' => $count,
                    'fulldate' => date('Y-m-d', strtotime($minus, $data['date_expire']))
                );
            }
        }else{
            for($i = 30; $i >= 0; $i--) {
                $count = 0;
                foreach ($dataArray as $value) {
                    if ($value['fulldate'] == date('Y-m-d', strtotime("-".$i." day"))) {
                        $count = $value['count']; break;
                    }
                }
                $resultArray[$i] = array(
                    'date' => strtotime("-".$i." day"),
                    'count' => $count,
                    'fulldate' => date('Y-m-d', strtotime("-".$i." day")),
                );
            }
        }

        if (isset($data['date_start']) && isset($data['date_expire'])) {
            (int)$dateCount = date("t", $data['date_expire'] - $data['date_start']);
        }
        else {
            (int)$dateCount = date("t", strtotime($data['year'] . "-" . $data['month'] . "-01"));
        }

        return $resultArray;
    }
}
?>