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
                'fulldate' => $this->datetimeMongotoReadable($r['date_added']),
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
        $this->mongo_db->where('status', 1);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, $r['_id']);
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_point_player = array();

        foreach ($Q as $r) {
            $reward_point_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => $this->datetimeMongotoReadable($r['date_added']),
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
        $this->mongo_db->where('status', 1);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, $r['_id']);
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => $this->datetimeMongotoReadable($r['date_added']),
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
        $this->mongo_db->where('status', 1);
        $Q =  $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach($Q as $r){
            array_push($reward_id, $r['_id']);
        }

        $this->mongo_db->select(array('date_added','pb_player_id'));
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q =  $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => $this->datetimeMongotoReadable($r['date_added']),
                'count' => isset($reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_badge_player);
        return $this->fillGap($reward_badge_player, $data);
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