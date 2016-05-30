<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//define('STREAM_URL', 'https://dev.pbapp.net/activitystream/');
define('STREAM_URL', 'https://node.pbapp.net/activitystream/');
//define('STREAM_URL', 'http://localhost/activitystream/');
define('STREAM_PORT', 443);
//define('STREAM_PORT', 3000);
define('USERPASS', 'planes:capetorment852456');

class Node_stream extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function publish($data, $site_name, $site_id)
    {
        //get chanel name
        $chanelName = preg_replace('/(http[s]?:\/\/)?([w]{3}\.)?/', '', $site_name);
        $chanelName = preg_replace('/\//', '\\', $chanelName);
        $message = json_encode($this->activityFeedFormatter($data, $site_id));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, STREAM_URL . $chanelName);    // set url
        curl_setopt($ch, CURLOPT_PORT, STREAM_PORT);                // set port
        curl_setopt($ch, CURLOPT_HEADER, false);                    // turn off output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                // refuse response from called server
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/plain; charset=utf-8'                // set Content-Type
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL AGENT');            // set agent
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);                    // times for execute
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);                // times for try to connect
        curl_setopt($ch, CURLOPT_POST, true);                        // use POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);                // data
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);            // http authen
        curl_setopt($ch, CURLOPT_USERPWD, USERPASS);                // user password
        $res = curl_exec($ch);
        curl_close($ch);
    }

    private function activityFeedFormatter($data, $site_id)
    {
        $playerData = $this->getPlayerInfo($data['pb_player_id'], $site_id);
        $activityFormat = array(
            'published' => date('c'), //rfc3339, atom, ISO 8601
            'actor' => array(
                'objectType' => 'person',
                'id' => $playerData['cl_player_id'],
                'displayName' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                'image' => array(
                    'url' => $playerData['image'],
                    'width' => 150,
                    'height' => 150
                )
            ),
            'verb' => $data['action_name'],
            'verb_icon' => isset($data['action_icon']) ? $data['action_icon'] : 'fa-star',
            'object' => array(
                'message' => $data['message']
            ),
            'target' => array()
        );
        if (!$playerData['first_name']) {
            $activityFormat['actor']['displayName'] = $playerData['username'];
        }
        if (isset($data['badge'])) {
            $activityFormat['object']['badge'] = array(
                'id' => $data['badge']['badge_id'],
                'name' => $data['badge']['name'],
                'image' => array(
                    'url' => $data['badge']['image'],
                    'width' => 76,
                    'height' => 76
                )
            );
        } elseif (isset($data['goods'])) {
            $activityFormat['object']['goods'] = array(
                'id' => $data['goods']['goods_id'],
                'name' => $data['goods']['name'],
                'image' => array(
                    'url' => $data['goods']['image'],
                    'width' => 76,
                    'height' => 76
                )
            );
        } else {
            $activityFormat['object']['badge'] = null;
            $activityFormat['object']['goods'] = null;
        }
        $activityFormat['object']['level'] = isset($data['level']) ? $data['level'] : null;
        $activityFormat['object']['point'] = isset($data['point']) ? $data['point'] : null;
        $activityFormat['object']['mission'] = isset($data['mission']) ? $data['mission'] : null;
        $activityFormat['object']['quest'] = isset($data['quest']) ? $data['quest'] : null;
        $activityFormat['object']['quiz'] = isset($data['quiz']) ? $data['quiz'] : null;
        $activityFormat['object']['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $activityFormat['object']['objective'] = isset($data['objective']) ? $data['objective'] : null;
        return $activityFormat;
    }

    private function getPlayerInfo($pb_player_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'cl_player_id',
            'first_name',
            'last_name',
            'image',
            'username'
        ));
        $this->mongo_db->where('_id', $pb_player_id);
        $result = $this->mongo_db->get('playbasis_player');
        return ($result) ? $result[0] : $result;
    }
}

?>
