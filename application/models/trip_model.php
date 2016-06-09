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
        );
        return $this->mongo_db->insert('playbasis_trip', $insert_data);
    }

    public function getTrip($client_id, $site_id, $finished=false, $pb_player_id=null, $trip_id=null)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        if($pb_player_id){
            $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        }
        if($trip_id)
        {
            $this->mongo_db->where('_id', new MongoID($trip_id));
        }
        if($finished){
            $this->mongo_db->where('finished', $finished);
        }

        return $this->mongo_db->get('playbasis_trip');
    }

    public function updateTrip($client_id, $site_id, $pb_player_id, $trip_id , $finished)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($pb_player_id));
        $this->mongo_db->where('_id', new MongoID($trip_id));
        $this->mongo_db->set('finished', $finished);

        return $this->mongo_db->update('playbasis_trip');
    }

    public function addDriveLog($data)
    {
        $insert_data = array(
            'client_id' => new MongoID($data['client_id']),
            'site_id'   => new MongoID($data['site_id']),
            'trip_id'   => new MongoID($data['trip_id']),
            'finished'  => false,
            'speed'     => (isset($data['speed']) && !empty($data['speed'])) ? $data['speed'] : 0,
            'latitude'  => (isset($data['latitude']) && !empty($data['latitude'])) ? $data['latitude'] : "",
            'longitude'  => (isset($data['longitude']) && !empty($data['longitude'])) ? $data['longitude'] : "",
            'altitude'  => (isset($data['altitude']) && !empty($data['altitude'])) ? $data['altitude'] : "",
            'rpm'       => (isset($data['rpm']) && !empty($data['rpm'])) ? $data['rpm'] : 0,
            'distance'  => (isset($data['distance']) && !empty($data['distance'])) ? $data['distance'] : 0,
            'timestamp' => new MongoDate()
        );

        return $this->mongo_db->insert('playbasis_drive_log', $insert_data);
    }

    public function getDriveLog($client_id, $site_id, $trip_id)
    {
        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('trip_id', new MongoID($trip_id));

        return $this->mongo_db->get('playbasis_drive_log');
    }
}