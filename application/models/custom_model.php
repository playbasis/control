<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_model extends MY_Model
{
    //DBS
    public function getTrafficAndUnique($data)
    {
        $default = array(
            'site_id' => new MongoId($data['site_id']),
            'client_id' => new MongoId($data['client_id']),
            'uri' => $data['uri'],
            'request.action' => $data['action'],
            'response.response.events.0.event_type' => 'REWARD_RECEIVED'
        );

        $date_added = array();
        if (isset($data['from']) && $data['from'] != '' && isset($data['to']) && $data['to'] != '') {
            $date_added['$gte'] = new MongoDate(strtotime($data['from']));
            $date_added['$lte'] = new MongoDate(strtotime($data['to']));
        }

        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);

        $results = $this->mongo_db->aggregate('playbasis_web_service_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array(
                        '_id' => array('$dayOfMonth' => '$date_added'),
                        'traffic' => array('$push' => '$request.player_id'),
                        'unique' => array('$addToSet' => '$request.player_id')
                    )
                ),
                array(
                    '$sort' => array('_id' => -1)
                ),
            )
        );
        return $results ? $results['result'] : array();
    }

    public function getConcurrentUser($data)
    {
        $default = array(
            'site_id' => new MongoId($data['site_id']),
            'client_id' => new MongoId($data['client_id']),
            'uri' => $data['uri'],
            'request.action' => $data['action'],
            'response.response.events.0.event_type' => 'REWARD_RECEIVED'
        );

        $date_added = array();
        if (isset($data['from']) && $data['from'] != '' && isset($data['to']) && $data['to'] != '') {
            $date_added['$gte'] = new MongoDate(strtotime($data['from']));
            $date_added['$lte'] = new MongoDate(strtotime($data['to']));
        }

        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);

        $results = $this->mongo_db->aggregate('playbasis_web_service_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array(
                        '_id' => array( 'day' => array('$dayOfMonth' => '$date_added') ,
                                       'hour' => array('$hour' => '$date_added'),
                                       'minutes' => array('$minute' => '$date_added')),
                        'n' => array('$sum' => 1)
                    )
                ),
                array(
                    '$sort' => array('_id' => -1)
                ),
            )
        );
        return $results ? $results['result'] : array();
    }

    public function getGameReport($data)
    {
        $default = array(
            'site_id' => new MongoId($data['site_id']),
            'client_id' => new MongoId($data['client_id']),
            'uri' => $data['uri'],
            'request.action' => $data['action'],
            'response.response.events.0.event_type' => 'REWARD_RECEIVED'
        );
        
        if(isset($data['game_name'])){
            $default['request.game_name'] = $data['game_name'];
        }

        if(isset($data['ios']))
        {
            $apple = new MongoRegex("/.*apple.*/i");
            $ios = new MongoRegex("/.*ios.*/i");
            if($data['ios']){
                $default = array_merge(array('$or' => [array('agent' => $apple) , array('agent' => $ios)]),$default);
            } else {
                $default = array_merge(array('$and' => [array('agent' => array('$not' => $apple)) , array('agent' => array('$not' => $ios))]),$default);
            }
        }

        $date_added = array();
        if (isset($data['from']) && $data['from'] != '' && isset($data['to']) && $data['to'] != '') {
            $date_added['$gte'] = new MongoDate(strtotime($data['from']));
            $date_added['$lte'] = new MongoDate(strtotime($data['to']));
        }

        $match = array_merge($date_added ? array('date_added' => $date_added) : array(), $default);

        $results = $this->mongo_db->aggregate('playbasis_web_service_log',
            array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array(
                        '_id' => array('$dayOfMonth' => '$date_added'),
                        'n' => array('$sum' => 1),
                        'dbs' => array('$sum' => array('$cond' => [array('$eq' => [ '$request.currency', 'dbs-dollar' ] ), 1, 0 ])),
                        'compass' => array('$sum' => array('$cond' => [array('$eq' => [ '$request.currency', 'compass-dollar' ] ), 1, 0 ])),
                    )
                ),
                array(
                    '$sort' => array('_id' => -1)
                ),
            )
        );
        return $results ? $results['result'] : array();
    }
}
