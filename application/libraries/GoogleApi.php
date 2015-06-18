<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once ("Google/autoload.php");

define('APPLICATION_NAME', 'Playbasis Dashboard');
define('SCOPES', implode(' ', array(
    Google_Service_Calendar::CALENDAR)
));

class GoogleApi
{
    protected $_ci;
    protected $_config;
    protected $_client;

    function __construct($config=array())
    {
        $this->_ci =& get_instance();
        $this->_config = $config;
    }

    public function initialize($clientId, $clientSecret, $redirectUri=null) {
        $this->_client = $this->getClient($clientId, $clientSecret, $redirectUri);
        return $this;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    private function getClient($clientId, $clientSecret, $redirectUri=null) {
        $client = new Google_Client();
        $client->setApplicationName(APPLICATION_NAME);
        $client->setScopes(SCOPES);
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        if ($redirectUri) $client->setRedirectUri($redirectUri);
        $client->setAccessType('offline');
        return $client;
    }

    public function setAccessToken($accessToken) {
        $this->_client->setAccessToken(is_string($accessToken) ? $accessToken : json_encode($accessToken));
        // Refresh the token if it's expired.
        if ($this->_client->isAccessTokenExpired()) {
            $this->_client->refreshToken($this->_client->getRefreshToken());
        }
        return $this;
    }

    public function createAuthUrl() {
        return $this->_client->createAuthUrl();
    }

    public function authenticate($authCode) {
        $accessToken = $this->_client->authenticate($authCode);
        $this->setAccessToken($accessToken);
        return json_decode($accessToken);
    }

    public function calendar() {
        return new Google_Service_Calendar($this->_client);
    }

    public function listEvents($gcal, $calendarId, &$syncToken, $maxResults=10) {
        $events = array();
        $optParams = array(
            'maxResults' => $maxResults,
            //'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            //'timeMin' => date('c'),
        );
        if ($syncToken) {
            $optParams = array_merge($optParams, array('syncToken' => $syncToken));
        }
        $eventList = $gcal->events->listEvents($calendarId, $optParams);
        while (true) {
            foreach ($eventList->getItems() as $eventEntry) {
                array_push($events, $eventEntry);
            }
            $pageToken = $eventList->getNextPageToken();
            if ($pageToken) {
                if (isset($optParams['pageToken'])) {
                    $optParams['pageToken'] = $pageToken;
                } else {
                    $optParams = array_merge($optParams, array('pageToken' => $pageToken));
                }
                $eventList = $gcal->events->listEvents($calendarId, $optParams);
            } else {
                break;
            }
        }
        $syncToken = $eventList->getNextSyncToken();
        return $events;
    }

    public function listCalendars($gcal) {
        $calendarList = $gcal->calendarList->listCalendarList();
        $l = array();
        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {
                array_push($l, array('id' => $calendarListEntry->getId(), 'summary' => $calendarListEntry->getSummary(), 'description' => $calendarListEntry->getDescription()));
            }
            $pageToken = $calendarList->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $gcal->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }
        return $l;
    }

    public function watchCalendar($gcal, $calendarId, $data) {
        $model = new Google_Service_Calendar_Channel();
        $model->setId($data['site_id'].'');
        $model->setType('web_hook');
        $model->setAddress($data['callback_url']);
        $gcal->events->watch($calendarId, $model);
    }

    public function unwatchCalendar($gcal, $channelId, $resourceId) {
        $model = new Google_Service_Calendar_Channel();
        $model->setId($channelId);
        $model->setResourceId($resourceId);
        $gcal->channels->stop($model);
    }
}