<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function getApiInfo($data)
    {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array(
            'site_id',
            'client_id'
        ));
        $this->mongo_db->where(array(
            'api_key' => $data['key'],
            'status' => true,
            'deleted' => false
        ));
        
        if (isset($data['secret'])){
            $this->mongo_db->where('api_secret', $data['secret']);
        }
        $this->mongo_db->limit(1);
        $cl_info = $this->mongo_db->get('playbasis_platform_client_site');
        if ($cl_info) {
            $this->mongo_db->select(array(
                '_id',
                'client_id',
                'site_name'
            ));
            $this->mongo_db->where(array(
                '_id' => $cl_info[0]['site_id'],
                'client_id' => $cl_info[0]['client_id'],
                'status' => true
            ));
            $result = $this->mongo_db->get('playbasis_client_site');
            if ($result) {
                $result = $result[0];
                $result['site_id'] = $result['_id'];
                $result['platform_id'] = $cl_info[0]['_id'];
                unset($result['_id']);
                return $result;
            }
        }

        return array();
    }

    public function generateToken($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $token = $this->getToken($data);
        if ($token) {
            $result['token'] = $token['token'];
//          $result['date_expire'] = date('Y-m-d H:i:s', $token['date_expire']->sec);
            $result['date_expire'] = datetimeMongotoReadable($token['date_expire']);
            return $result;
        }
        return $this->renewToken($data);
    }

    private function token($key, $secret, $r)
    {
        return hash('sha1', $key . time() . $secret . $r);
    }

    private function getToken($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array(
            'token',
            'date_expire'
        ));
        $this->mongo_db->where(array(
            'site_id' => $data['site_id'],
            'client_id' => $data['client_id'],
            'platform_id' => $data['platform_id'],
        ));
        $this->mongo_db->where_gt('date_expire', new MongoDate(time()));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_token');
        return $results ? $results[0] : array();
    }

    public function renewToken($data)
    {
        $token = array();
        $r = rand();
        $newToken = $this->token($data['key'], $data['secret'], $r);
        $oldToken = $this->getToken($data);
        if ($oldToken) {
            $oldToken = $oldToken['token'];
        }
        if ($newToken == $oldToken) {
            $newToken = $this->token($data['key'], $data['secret'], $r << 1);
        } // prevent duplicate tokens being returned
        $token['token'] = $newToken;
        $client_expire = defined('TOKEN_CLIENT_EXPIRE') ? TOKEN_CLIENT_EXPIRE : (3 * 24 * 3600);  //default 3 days
        $expire = new MongoDate(time() + $client_expire);
        $updated = array();
        foreach (self::$dblist as $key => $value) {
            //keep track of which db is already updated
            if (isset($updated[$value])) {
                continue;
            }
            $updated[$value] = true;
            //delete old token
            $this->set_site_mongodb($key);
            $this->mongo_db->where(array(
                'site_id' => $data['site_id'],
                'client_id' => $data['client_id'],
                'platform_id' => $data['platform_id'],
            ));
            $this->mongo_db->delete_all('playbasis_token');
            //insert new token
            $this->mongo_db->insert('playbasis_token', array(
                'client_id' => $data['client_id'],
                'site_id' => $data['site_id'],
                'platform_id' => $data['platform_id'],
                'token' => $token['token'],
                'date_expire' => $expire
            ));
        }
//      $token['date_expire'] = date('Y-m-d H:i:s', $expire->sec);
        $token['date_expire'] = datetimeMongotoReadable($expire);
        return $token;
    }


    public function generatePlayerToken($data)
    {
        $this->set_site_mongodb($data['site_id']);
        $token = $this->getPlayerToken($data);
        if ($token) {
            $result['token'] = $token['token'];
            $result['refresh_token'] = $token['refresh_token'];
            $result['date_expire'] = datetimeMongotoReadable($token['date_expire']);
            return $result;
        }
        return $this->renewPlayerToken($data);
    }

    public function renewPlayerToken($data)
    {
        $token = array();
        $r = rand();
        $newToken = $this->playerToken($data['key'], $data['pb_player_id'], $r);
        $oldToken = $this->getPlayerToken($data);
        if ($oldToken) {
            $oldToken = $oldToken['token'];
        } else {
            $token['refresh_token'] = $this->playerToken($data['key'], $data['pb_player_id'], $r << 1, $data['password']);
        }

        if ($newToken == $oldToken) {
            $newToken = $this->playerToken($data['key'], $data['pb_player_id'], $r << 1);
        } // prevent duplicate tokens being returned

        $token['token'] = $newToken;
        $player_expire = defined('TOKEN_CLIENT_EXPIRE') ? TOKEN_PLAYER_EXPIRE : (3 * 24 * 3600);  //default 3 days
        $expire = new MongoDate(time() + $player_expire);
        $check_player = $this->getPlayerToken($data,false,false);
        if($check_player){
            $token['refresh_token'] = $check_player['refresh_token'];
            $this->mongo_db->where(array(
                'client_id' => $data['client_id'],
                'site_id' => $data['site_id'],
                'platform_id' => $data['platform_id'],
                'pb_player_id' => $data['pb_player_id']
            ));
            $this->mongo_db->set('token', $token['token']);
            $this->mongo_db->set('date_expire', $expire);
            $this->mongo_db->update('playbasis_player_token');
        } else {
            $this->mongo_db->insert('playbasis_player_token', array(
                'client_id' => $data['client_id'],
                'site_id' => $data['site_id'],
                'platform_id' => $data['platform_id'],
                'pb_player_id' => $data['pb_player_id'],
                'token' => $token['token'],
                'refresh_token' => $token['refresh_token'],
                'date_expire' => $expire
            ));
        }

        $token['date_expire'] = datetimeMongotoReadable($expire);
        return $token;
    }

    private function playerToken($key, $player_id, $r, $password = false)
    {
        if ($password){
            $token = hash('sha1', $key . time() . $player_id . $password . $r);
        } else {
            $token = hash('sha1', $key . time() . $player_id . $r);
        }
        return $token;
    }

    public function revokePlayerToken($data)
    {
        $this->mongo_db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'platform_id' => $data['platform_id'],
            'pb_player_id' => $data['pb_player_id']
        ));
        $this->mongo_db->set('date_expire', new MongoDate(strtotime("-1 day", time())));
        $this->mongo_db->update('playbasis_player_token');
    }

    public function getPlayerToken($data, $refresh_token = false, $check_player = true)
    {
        $this->set_site_mongodb($data['site_id']);
        $this->mongo_db->select(array(
            'token',
            'refresh_token',
            'date_expire'
        ));
        $this->mongo_db->where(array(
            'site_id' => $data['site_id'],
            'client_id' => $data['client_id'],
            'platform_id' => $data['platform_id'],
            'pb_player_id' => $data['pb_player_id']
        ));

        if($check_player){
            if ($refresh_token) {
                $this->mongo_db->where('refresh_token', $refresh_token);
            } else {
                $this->mongo_db->where_gt('date_expire', new MongoDate(time()));
            }
        }

        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_player_token');
        return $results ? $results[0] : array();
    }

    public function findToken($token)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array(
            'client_id',
            'site_id'
        ));
        $this->mongo_db->where(array(
            'token' => $token,
        ));
        $this->mongo_db->where_gt('date_expire', new MongoDate(time()));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_token');
        if ($result && $result[0]) {
            $info = $result[0];
            $info['_id'] = $info['site_id'];
            unset($info['site_id']);
            $this->set_site_mongodb($info['_id']);
            $this->mongo_db->select(array(
                'site_name'
            ));
            $this->mongo_db->where($info);
            $this->mongo_db->limit(1);
            $result = $this->mongo_db->get('playbasis_client_site');
            $result = array_merge($info, ($result) ? $result[0] : array());
            $result['site_id'] = $result['_id'];
            unset($result['_id']);
            return $result;
        }
        return null;
    }

    public function createToken($client_id, $site_id)
    {
        $info = array(
            'client_id' => $client_id,
            '_id' => $site_id
        );
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'site_name'
        ));
        $this->mongo_db->where($info);
        $result = $this->mongo_db->get('playbasis_client_site');
        if ($result && $result[0]) {
            $result = array_merge($info, $result[0]);
            $result['site_id'] = $result['_id'];
            unset($result['_id']);
            return $result;
        }
        return null;
    }

    public function createTokenFromAPIKey($apiKey)
    {
        $this->set_site_mongodb(0);

        $this->mongo_db->select(array(
            'site_id',
            'client_id'
        ));
        $this->mongo_db->where(array(
            'api_key' => $apiKey,
            'status' => true,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $cl_info = $this->mongo_db->get('playbasis_platform_client_site');
        if ($cl_info) {
            $this->mongo_db->select(array(
                '_id',
                'client_id',
                'site_name'
            ));
            $this->mongo_db->where(array(
                '_id' => $cl_info[0]['site_id'],
                'client_id' => $cl_info[0]['client_id'],
                'status' => true
            ));
            $this->mongo_db->limit(1);
            $result = $this->mongo_db->get('playbasis_client_site');
            if ($result) {
                $result = $result[0];
                $result['site_id'] = $result['_id'];
                $result['platform_id'] = $cl_info[0]['_id'];
                unset($result['_id']);
                return $result;
            }
        }

        return array();
    }

    function getApikeyBySite ($site_id){
        $this->mongo_db->select(array(
            'api_key'
        ));
        $this->mongo_db->where(array(
            'site_id' => new MongoId($site_id),
            'status' => true,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $result =  $this->mongo_db->get('playbasis_platform_client_site');
        return isset($result[0]['api_key']) ? $result[0]['api_key'] : null;
    }

    function getClientSiteByApiKey ($api_key){
        $this->mongo_db->select(array(
            'client_id',
            'site_id'
        ));
        $this->mongo_db->where(array(
            'api_key' => $api_key,
            'status' => true,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $result =  $this->mongo_db->get('playbasis_platform_client_site');
        return $result ? $result[0] : null;
    }

    public function getOnePlatform($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'status' => true,
            'deleted' => false
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_platform_client_site');
        return $results ? $results[0] : array();
    }
}

?>
