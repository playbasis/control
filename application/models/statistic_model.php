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
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('date_added', 'pb_player_id'));
        $this->mongo_db->where('date_added',
            array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $Q = $this->mongo_db->get('playbasis_player');

        $register_player = array();

        foreach ($Q as $r) {
            $register_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($register_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $register_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($register_player);
        return $this->fillGap($register_player, $data);
    }

    public function getPlayerRewardPointStat($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'reward_id'));
        $this->mongo_db->where('name', array('$nin' => array('exp', 'badge')));
        $this->mongo_db->where('status', true);
        $Q = $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach ($Q as $r) {
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added', 'pb_player_id'));
        $this->mongo_db->where('date_added',
            array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q = $this->mongo_db->get('playbasis_reward_to_player');

        $reward_point_player = array();

        foreach ($Q as $r) {
            $reward_point_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_point_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_point_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_point_player);
        return $this->fillGap($reward_point_player, $data);
    }

    public function getPlayerRewardBadgeStat($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'reward_id'));
        $this->mongo_db->where('name', 'badge');
        $this->mongo_db->where('status', true);
        $Q = $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach ($Q as $r) {
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added', 'pb_player_id'));
        $this->mongo_db->where('date_added',
            array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q = $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_badge_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_badge_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_badge_player);
        return $this->fillGap($reward_badge_player, $data);
    }

    public function getPlayerRewardLevelStat($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'reward_id'));
        $this->mongo_db->where('name', 'exp');
        $this->mongo_db->where('status', true);
        $Q = $this->mongo_db->get('playbasis_reward');

        $reward_id = array();

        foreach ($Q as $r) {
            array_push($reward_id, new MongoID($r['_id']));
        }

        $this->mongo_db->select(array('date_added', 'pb_player_id'));
        $this->mongo_db->where('date_added',
            array('$gt' => new MongoDate($data['date_start']), '$lte' => new MongoDate($data['date_expire'])));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('reward_id', array('$in' => $reward_id));
        $Q = $this->mongo_db->get('playbasis_reward_to_player');

        $reward_badge_player = array();

        foreach ($Q as $r) {
            $reward_badge_player[date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added'])))] = array(
                'fulldate' => date("Y-m-d", strtotime($this->datetimeMongotoReadable($r['date_added']))),
                'count' => isset($reward_badge_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]) ? $reward_badge_player[date("Y-m-d",
                        strtotime($this->datetimeMongotoReadable($r['date_added'])))]['count'] + 1 : 1,
                'date' => strtotime($this->datetimeMongotoReadable($r['date_added'])),
            );
        }
        ksort($reward_badge_player);
        return $this->fillGap($reward_badge_player, $data);
    }

    public function getLogClient($data, $action, $start, $end)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'action_id', 'name', 'color', 'icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('action_id', new MongoID($action['_id']));
        if ($start) {
            $start = strtotime($start, strtotime($data['date']));
        } else {
            $start = strtotime($data['date']);
        }
        if ($end) {
            $end = strtotime($end, strtotime($data['date']));
        } else {
            $end = strtotime($data['date']);
        }
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($start), '$lte' => new MongoDate($end)));
        $r = $this->mongo_db->get('playbasis_action_log');

        return $r;
    }

    public function getCountLogClient($data, $action, $start, $end)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'action_id', 'name', 'color', 'icon'));
        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('action_id', $action['action_id']);
        if ($start) {
            $start = strtotime($start, strtotime($data['date']));
        } else {
            $start = strtotime($data['date']);
        }
        if ($end) {
            $end = strtotime($end, strtotime($data['date']));
        } else {
            $end = strtotime($data['date']);
        }
        $this->mongo_db->where('date_added', array('$gt' => new MongoDate($start), '$lte' => new MongoDate($end)));
        $r = $this->mongo_db->count('playbasis_action_log');

        return $r;
    }

    public function getDailyActionmeaturement($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'action_id', 'name', 'color', 'icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $actions = $this->mongo_db->get('playbasis_action_to_client');

        foreach ($actions as &$action) {

            $r1 = $this->getCountLogClient($data, $action, '-1 day', null);

            $action['value'] = (int)$r1;

            $action['circle'] = $action['color'] . "Circle";
            $action['class'] = $action['icon'];

            $r2 = $this->getCountLogClient($data, $action, '-2 day', '-1 day');

            $action['count_of_last_day'] = (int)$r2;

            //zero check
            $rateFactor = ($action['count_of_last_day']) ? $action['count_of_last_day'] : 1;

            //advancement rate
            $action['advancement_direction'] = ($action['value'] - $action['count_of_last_day']) >= 0 ? '+' : '-';
            $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_day']);
            $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_day']) * 100) / $rateFactor));
        }

        return $actions;
    }

    public function getWeeklyActionmeaturement($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('_id', 'action_id', 'name', 'color', 'icon'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $actions = $this->mongo_db->get('playbasis_action_to_client');

        foreach ($actions as &$action) {

            $r1 = $this->getCountLogClient($data, $action, '-1 week', null);

            $action['value'] = (int)$r1;

            $action['circle'] = $action['color'] . "Circle";
            $action['class'] = $action['icon'];

            $r2 = $this->getCountLogClient($data, $action, '-2 week', '-1 week');

            $action['count_of_last_week'] = (int)$r2;

            //zero check
            $rateFactor = ($action['count_of_last_week']) ? $action['count_of_last_week'] : 1;

            //advancement rate
            $action['advancement_direction'] = ($action['value'] - $action['count_of_last_week']) >= 0 ? '+' : '-';
            $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_week']);
            $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_week']) * 100) / $rateFactor));
        }

        return $actions;
    }

    public function getMonthlyActionmeaturement($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if ($data['client_id']) {
            $this->mongo_db->select(array('_id', 'action_id', 'name', 'color', 'icon'));

            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
            $actions = $this->mongo_db->get('playbasis_action_to_client');

            foreach ($actions as &$action) {

                $r1 = $this->getCountLogClient($data, $action, '-1 month', null);

                $action['value'] = (int)$r1;

                $action['circle'] = $action['color'] . "Circle";
                $action['class'] = $action['icon'];

                $r2 = $this->getCountLogClient($data, $action, '-2 month', '-1 month');

                $action['count_of_last_month'] = (int)$r2;

                //zero check
                $rateFactor = ($action['count_of_last_month']) ? $action['count_of_last_month'] : 1;

                //advancement rate
                $action['advancement_direction'] = ($action['value'] - $action['count_of_last_month']) >= 0 ? '+' : '-';
                $action['advancement_difference'] = abs($action['value'] - $action['count_of_last_month']);
                $action['advancement_rate'] = abs(floor((($action['value'] - $action['count_of_last_month']) * 100) / $rateFactor));
            }

            return $actions;
        } else {
            return array();
        }
    }

    /* getLeaderboardByPoint */
    public function LeaderBoard($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $reward_filter = "point";
        if (isset($data['reward_filter'])) {
            $reward_filter = $data['reward_filter'];
        }

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', $reward_filter);
        $r = $this->mongo_db->get('playbasis_reward');

        $this->mongo_db->select(array('_id', 'pb_player_id', 'value'));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        if ($r) {
            $this->mongo_db->where('reward_id', new MongoID($r[0]['_id']));
        }
        $this->mongo_db->order_by(array('value' => -1));

        if (isset($data['limit'])) {
            $this->mongo_db->limit((int)$data['limit']);
        } else {
            $this->mongo_db->limit(10);
        }

        if (isset($data['start'])) {
            $this->mongo_db->offset((int)$data['start']);
        } else {
            $this->mongo_db->offset(0);
        }

        $LeaderBoardList = $this->mongo_db->get('playbasis_reward_to_player');

        return $LeaderBoardList;
    }

    public function getLeaderboardByExp($limit, $client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'email',
            'first_name',
            'last_name',
            'username',
            'image',
            'exp',
            'level',
            'date_added',
            'date_modified'
        ));
        $this->mongo_db->where(array(
            'status' => true,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $this->mongo_db->order_by(array('exp' => -1));
        $this->mongo_db->limit($limit);
        return $this->mongo_db->get('playbasis_player');
    }
    
    public function getActionData($client_id, $site_id, $action, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_validated_action_log', array(
            array(
                '$match' => array(
                    'action_name' => $action,
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }

    public function getTopGoodsData($client_id, $site_id, $from, $to)
    {
        $data =  $this->mongo_db->aggregate('playbasis_reward_status_to_player', array(
            array(
                '$match' => array(
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                    'status' => 'approve'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array('reward_id' => '$reward_id', 'reward_name' => '$reward_name'),
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('value' => -1),
            )
        ));

        return $data['result'];
    }

    public function getGoodsByRewardRedeem($client_id, $site_id, $reward_id)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('redeem.custom.'.$reward_id, 1);
        $result = $this->mongo_db->get('playbasis_goods_distinct_to_client');
        return $result? $result[0]['name'] : null;
    }

    public function getGoodsSuperData($client_id, $site_id, $action, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_reward_status_to_player', array(
            array(
                '$match' => array(
                    'reward_id' => $action,
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                    'status' => 'approve'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }

    public function getGoodsMonthlyData($client_id, $site_id, $action, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_reward_status_to_player', array(
            array(
                '$match' => array(
                    'reward_id' => $action,
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                    'status' => 'approve'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }

    public function getTopBadgeData($client_id, $site_id, $from, $to)
    {
        $data =  $this->mongo_db->aggregate('playbasis_event_log', array(
            array(
                '$match' => array(
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'event_type' => 'REWARD',
                    'reward_type' => 'BADGE',
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to))
                ),
            ),
            array(
                '$group' => array(
                    '_id' => '$item_id',
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('value' => -1),
            )
        ));

        return $data['result'];
    }

    public function getBadgeNameToClient($client_id, $site_id, $badge_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('badge_id', new MongoID($badge_id));
        $results = $this->mongo_db->get("playbasis_badge_to_client");

        return $results ? $results[0]['name'] : null;
    }

    public function getBadgeData($client_id, $site_id, $badge_id, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_event_log', array(
            array(
                '$match' => array(
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'event_type' => 'REWARD',
                    'reward_type' => 'BADGE',
                    'item_id' => $badge_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }

    public function getRegisterData($client_id, $site_id, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_player', array(
            array(
                '$match' => array(
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'added_by_script' => array('$ne' => true),
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }

    public function getMGMData($client_id, $site_id, $action, $from, $to, $type=null)
    {
        if($type == 'day'){
            $id = array(
                "hour" => array('$hour' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } elseif($type == 'month'){
            $id = array(
                "year" => array('$year' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "month" => array('$month' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ])),
                "date" => array('$dayOfMonth' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        } else {
            $id = array(
                "day" => array('$dayOfWeek' => array('$add' => [ '$date_added', 8 * 60 * 60 * 1000 ]))
            );
        }
        $data =  $this->mongo_db->aggregate('playbasis_validated_action_log', array(
            array(
                '$match' => array(
                    'action_name' => $action,
                    'site_id' => $site_id,
                    'client_id' => $client_id,
                    'date_added' => array('$gte' => new MongoDate($from), '$lte' => new MongoDate($to)),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => $id,
                    'value' => array('$sum' => 1)
                )
            ),
            array(
                '$sort' => array('_id' => -1),
            )
        ));

        return $data['result'];
    }
    
//    public function getPlayerInfo($pb_player_id){
////
//        $this->mongo_db->select(array('_id','pb_player_id','first_name','last_name','exp','level','image','email','date_added','date_modified'));
//        $this->mongo_db->where('_id', new MongoID($pb_player_id));
//        $result =  $this->mongo_db->get('playbasis_player');
//
//        return $result ? $result[0] : null;
//    }

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
    private function fillGap($dataArray, $data)
    {

        $resultArray = array();

        if (isset($data['date_start']) && isset($data['date_expire'])) {

            $diff_time = ($data['date_expire'] - $data['date_start']) * 1000;

            $interval = ceil($diff_time / 86400000);

            for ($i = $interval; $i >= 0; $i--) {
                $minus = '-' . $i . ' day';
                $count = 0;
                foreach ($dataArray as $value) {
                    if ($value['fulldate'] == date('Y-m-d', strtotime($minus, $data['date_expire']))) {
                        $count = $value['count'];
                        break;
                    }
                }
                $resultArray[$i] = array(
                    'date' => strtotime($minus, $data['date_expire']),
                    'count' => $count,
                    'fulldate' => date('Y-m-d', strtotime($minus, $data['date_expire']))
                );
            }
        } else {
            for ($i = 30; $i >= 0; $i--) {
                $count = 0;
                foreach ($dataArray as $value) {
                    if ($value['fulldate'] == date('Y-m-d', strtotime("-" . $i . " day"))) {
                        $count = $value['count'];
                        break;
                    }
                }
                $resultArray[$i] = array(
                    'date' => strtotime("-" . $i . " day"),
                    'count' => $count,
                    'fulldate' => date('Y-m-d', strtotime("-" . $i . " day")),
                );
            }
        }

        if (isset($data['date_start']) && isset($data['date_expire'])) {
            (int)$dateCount = date("t", $data['date_expire'] - $data['date_start']);
        } else {
            (int)$dateCount = date("t", strtotime($data['year'] . "-" . $data['month'] . "-01"));
        }

        return $resultArray;
    }
}

?>