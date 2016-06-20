<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trip_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function addTrip($client_id, $site_id, $pb_player_id)
    {
        $insert_data = array(
            'client_id' => new MongoID($client_id),
            'site_id' => new MongoID($site_id),
            'pb_player_id' => new MongoID($pb_player_id),
            'finished' => false,
            'date_start' => new MongoDate(),
            'date_end' => ""
        );
        return $this->mongo_db->insert('playbasis_trip_to_player', $insert_data);
    }

    public function finishTrip($client_id, $site_id,  $trip_id )
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('_id', new MongoID($trip_id));
        $this->mongo_db->set('finished', true);
        $this->mongo_db->set('date_end', new MongoDate());

        return $this->mongo_db->update('playbasis_trip_to_player');
    }

    public function getTrip($client_id, $site_id, $finished=null, $pb_player_id=null, $trip_id=null)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        if($pb_player_id){
            $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        }
        if($trip_id)        {
            $this->mongo_db->where('_id', new MongoID($trip_id));
        }
        if(!is_null($finished)) {
            $this->mongo_db->where('finished', $finished);
        }

        $this->mongo_db->order_by(array('date_start' => -1));

        return $this->mongo_db->get('playbasis_trip_to_player');
    }

    public function addTripLog($data)
    {
        $insert_data = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id'   => new MongoID($data['site_id']),
            'trip_id'   => new MongoID($data['trip_id']),
            'speed'     => (isset($data['speed'])) ? $data['speed'] : null,
            'latitude'  => (isset($data['latitude'])) ? $data['latitude'] : null,
            'longitude' => (isset($data['longitude'])) ? $data['longitude'] : null,
            'altitude'  => (isset($data['altitude'])) ? $data['altitude'] : null,
            'rpm'       => (isset($data['rpm'])) ? $data['rpm'] : null,
            'distance'  => (isset($data['distance'])) ? $data['distance'] : null,
            'speed_limit'  => (isset($data['speed_limit'])) ? $data['speed_limit'] : null,
            'timestamp' => new MongoDate()
        );

        return $this->mongo_db->insert('playbasis_trip_log', $insert_data);
    }

    public function getTripLog($client_id, $site_id, $trip_id, $select = array())
    {
        $this->mongo_db->select($select,array('_id','client_id','site_id','trip_id'));
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('trip_id', new MongoID($trip_id));

        $this->mongo_db->order_by(array('_id' => 1)); // order from the oldest to newer

        return $this->mongo_db->get('playbasis_trip_log');
    }
}