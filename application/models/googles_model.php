<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Googles_model extends MY_Model
{

    public function getRegistration()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('google_client_id', 'google_client_secret', 'google_url', 'token'));
        $this->mongo_db->where('site_id', $this->session->userdata('site_id'));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_google_to_client");
        return $results ? $results[0] : null;
    }

    public function insertRegistration($google_url, $google_client_id, $google_client_secret)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        return $this->mongo_db->insert('playbasis_google_to_client', array(
            'client_id' => $this->session->userdata('client_id'),
            'site_id' => $this->session->userdata('site_id'),
            'google_url' => $google_url,
            'google_client_id' => $google_client_id,
            'google_client_secret' => $google_client_secret,
            'date_added' => $d,
            'date_modified' => $d
        ));
    }

    public function updateToken($site_id, $token)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(time());
        $token['date_start'] = $d;
        $token['date_expire'] = new MongoDate($d->sec + $token['expires_in']);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->set('token', $token);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update("playbasis_google_to_client");
    }

    public function getSubscription($resource_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $this->session->userdata('client_id'));
        $this->mongo_db->where('site_id', $this->session->userdata('site_id'));
        $this->mongo_db->where('resource_id', $resource_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_google_subscription");
        return $results ? $results[0] : null;
    }

    public function insertWebhook($calendar_id, $channel_id, $callback_url)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $d = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        return $this->mongo_db->insert('playbasis_google_subscription', array(
            'client_id' => $this->session->userdata('client_id'),
            'site_id' => $this->session->userdata('site_id'),
            'channel_id' => $channel_id,
            'calendar_id' => $calendar_id,
            'callback_url' => $callback_url,
            'date_added' => $d,
            'date_modified' => $d
        ));
    }

    public function listWebhooks()
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $this->session->userdata('client_id'));
        $this->mongo_db->where('site_id', $this->session->userdata('site_id'));
        $this->mongo_db->where(array(
            '$or' => array(
                array('date_expire' => array('$gt' => new MongoDate(time()))),
                array('date_expire' => null)
            )
        ));
        return $this->mongo_db->get("playbasis_google_subscription");
    }

    public function removeWebhook($channel_id, $resource_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $this->session->userdata('client_id'));
        $this->mongo_db->where('site_id', $this->session->userdata('site_id'));
        $this->mongo_db->where('channel_id', $channel_id);
        $this->mongo_db->where('resource_id', $resource_id);
        $this->mongo_db->delete_all('playbasis_google_subscription');
    }
}

?>