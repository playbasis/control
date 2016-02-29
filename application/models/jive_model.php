<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jive_model extends MY_Model
{

    protected $events = array(
        array(
            'id' => 'jive:user_account_created',
            'type' => 'user_account',
            'description' => 'User account has been created'
        ),
        array(
            'id' => 'jive:user_account_deleted',
            'type' => 'user_account',
            'description' => 'User account has been deleted'
        ),
        array(
            'id' => 'jive:user_account_disabled',
            'type' => 'user_account',
            'description' => 'User account has been disabled'
        ),
        array(
            'id' => 'jive:user_account_enabled',
            'type' => 'user_account',
            'description' => 'User account has been enabled'
        ),
        array(
            'id' => 'jive:user_account_invisible',
            'type' => 'user_account',
            'description' => 'User account has been invisible'
        ),
        array(
            'id' => 'jive:user_account_visible',
            'type' => 'user_account',
            'description' => 'User account has been visible'
        ),
        array(
            'id' => 'jive:user_profile_modified',
            'type' => 'user_account',
            'description' => 'User profile has been modified'
        ),
        array(
            'id' => 'jive:user_type_modified',
            'type' => 'user_account',
            'description' => 'User type has been modified'
        ),
        array('id' => 'jive:user_session_login', 'type' => 'user_session', 'description' => 'User has logged in'),
        array('id' => 'jive:user_session_logout', 'type' => 'user_session', 'description' => 'User has logged out'),
        array(
            'id' => 'jive:user_membership_added',
            'type' => 'user_membership',
            'description' => 'User membership has been added'
        ),
        array(
            'id' => 'jive:user_membership_removed',
            'type' => 'user_membership',
            'description' => 'User membership has been removed'
        ),
        array(
            'id' => 'jive:social_group_created',
            'type' => 'social_group',
            'description' => 'Social group has been created'
        ),
        array(
            'id' => 'jive:social_group_renamed',
            'type' => 'social_group',
            'description' => 'Social group has been renamed'
        ),
        array(
            'id' => 'jive:social_group_deleted',
            'type' => 'social_group',
            'description' => 'Social group has been deleted'
        ),
        array(
            'id' => 'jive:stream_config_created',
            'type' => 'stream',
            'description' => 'Stream config has been created'
        ),
        array(
            'id' => 'jive:stream_config_modified',
            'type' => 'stream',
            'description' => 'Stream config has been modified'
        ),
        array(
            'id' => 'jive:stream_config_deleted',
            'type' => 'stream',
            'description' => 'Stream config has been deleted'
        ),
        array(
            'id' => 'jive:stream_association_added',
            'type' => 'stream',
            'description' => 'Stream association has been added'
        ),
        array(
            'id' => 'jive:stream_association_removed',
            'type' => 'stream',
            'description' => 'Stream association has been removed'
        ),
        array('id' => 'jive:webhook_created', 'type' => 'webhook', 'description' => 'Webhook has been created'),
        array('id' => 'jive:webhook_deleted', 'type' => 'webhook', 'description' => 'Webhook has been deleted'),
        array('id' => 'jive:webhook_enabled', 'type' => 'webhook', 'description' => 'Webhook has been enabled'),
        array('id' => 'jive:webhook_disabled', 'type' => 'webhook', 'description' => 'Webhook has been disabled'),
    );

    public function hasValidRegistration($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        return $this->mongo_db->count("playbasis_jive_to_client") > 0;
    }

    public function getRegistration($site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->select(array('jive_tenant_id', 'jive_client_id', 'jive_client_secret', 'jive_url', 'token'));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_jive_to_client");
        return $results ? $results[0] : null;
    }

    public function updateToken($client_id, $site_id, $token)
    {
        $this->set_site_mongodb($site_id);
        $d = new MongoDate(time());
        $token['date_start'] = $d;
        $token['date_expire'] = new MongoDate($d->sec + $token['expires_in']);
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where_ne('deleted', true);
        $this->mongo_db->set('client_id', $client_id); // update missing client_id as well
        $this->mongo_db->set('token', $token);
        $this->mongo_db->set('date_modified', $d);
        $this->mongo_db->update("playbasis_jive_to_client");
    }

    public function listEvents($site_id, $per_page, $offset)
    {
        return array_slice($this->events, $offset, $per_page);
    }

    public function totalEvents($site_id)
    {
        return count($this->events);
    }

    public function getEventType($eventId)
    {
        foreach ($this->events as $event) {
            if ($event['id'] == $eventId) {
                return $event['type'];
            }
        }
        return false;
    }
}

?>