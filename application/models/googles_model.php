<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('EXPIRED_WITHIN_SEC', 2 * 24 * 60 * 60);

class Googles_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function getRegistration($site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('google_client_id', 'google_client_secret', 'google_url', 'token'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_google_to_client");
        return $results ? $results[0] : null;
    }

    public function getSubscription($site_id, $channel_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('channel_id', $channel_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_google_subscription");
        return $results ? $results[0] : null;
    }

    public function updateWebhook($site_id, $channel_id, $resource_id, $resource_uri, $date_expire)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('channel_id', $channel_id);
        $this->mongo_db->set('resource_id', $resource_id);
        $this->mongo_db->set('resource_uri', $resource_uri);
        $this->mongo_db->set('date_expire', $date_expire);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update('playbasis_google_subscription');
    }

    public function insertEvents($site_id, $calendar_id, $events)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $l = array();
        foreach ($events as $event) {
            array_push($l, array(
                'site_id' => $site_id,
                'calendar_id' => $calendar_id,
                'event_id' => $event['event_id'],
                'event' => $event['event'],
                'date_added' => $d,
                'date_modified' => $d,
            ));
        }
        return $this->mongo_db->batch_insert('playbasis_calendar_events', $l, array("w" => 0, "j" => false, "continueOnError" => true));
    }

    public function getEvent($site_id, $calendar_id, $event_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('calendar_id', $calendar_id);
        $this->mongo_db->where('event_id', $event_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_calendar_events");
        return $results ? $results[0] : null;
    }

    public function insertOrUpdateEvent($site_id, $calendar_id, $event)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $event_id = $event['event_id'];
        $entry = $this->getEvent($site_id, $calendar_id, $event_id);
        if (!$entry) {
            $this->mongo_db->insert('playbasis_calendar_events', array(
                'site_id' => $site_id,
                'calendar_id' => $calendar_id,
                'event_id' => $event_id,
                'event' => $event['event'],
                'date_added' => $d,
                'date_modified' => $d,
            ));
        } else {
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('calendar_id', $calendar_id);
            $this->mongo_db->where('event_id', $event_id);
            $this->mongo_db->set('event', $event['event']);
            $this->mongo_db->set('date_modified', $d);
            $this->mongo_db->update('playbasis_calendar_events');
        }
    }

    public function removeEvent($site_id, $calendar_id, $event_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('calendar_id', $calendar_id);
        $this->mongo_db->where('event_id', $event_id);
        return $this->mongo_db->delete('playbasis_calendar_events');
    }

    public function storeSyncToken($site_id, $calendar_id, $syncToken)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        $entry = $this->getSyncToken($site_id, $calendar_id);
        if (!$entry) {
            $this->mongo_db->insert('playbasis_calendar_token', array(
                'site_id' => $site_id,
                'calendar_id' => $calendar_id,
                'sync_token' => $syncToken,
                'date_added' => $d,
                'date_modified' => $d
            ));
        } else {
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('calendar_id', $calendar_id);
            $this->mongo_db->set('sync_token', $syncToken);
            $this->mongo_db->set('date_modified', $d);
            $this->mongo_db->update('playbasis_calendar_token');
        }
    }

    public function getSyncToken($site_id, $calendar_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('calendar_id', $calendar_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_calendar_token");
        return $results ? $results[0]['sync_token'] : null;
    }
}

?>