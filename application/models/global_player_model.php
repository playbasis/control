<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Global_Player_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('mongo_db');
    }

    public function testAction()
    {
        return 'test action';
    }

    public function loginAction($data, $type)
    {
        if ($type == "login") {

            $this->mongo_db->select(array('salt'));
            $this->mongo_db->where(array(
                'username' => $data['username'],
            ));
            $this->mongo_db->limit(1);
            $user = $this->mongo_db->get('global_player');
            $user = $user[0];
            $this->mongo_db->select(array('email'));
            $this->mongo_db->where(array(
                'username' => $data['username'],
                'password' => db_clean(dohash($data['password'], $user['salt']), 40)
            ));
            $this->mongo_db->limit(1);
            $id = $this->mongo_db->get('global_player');
            return $id;
        } else {
            $this->mongo_db->select(array('_id'));
            $this->mongo_db->where(array(
                'player_id' => $data['player_id'],
                'social_name' => $type,
            ));
            $this->mongo_db->limit(1);
            $token = $this->mongo_db->get('global_token');

            if (!$token) {
                if ($token == $data['token']) {
                    return true;
                }
            } else {


            }
        }

        return false;
    }

    /**
     * @param $data
     * @param $social_data
     */
    public function createGlobalPlayer($data, $social_data)
    {
        $mongoDate = new MongoDate(time());

        $salt = get_random_password(10, 10);

        $insert_password = $data['password'];

        $password = dohash($insert_password, $salt);

        if (!$social_data) {
            $social = array(
                'provider' => $social_data['provider'],
                'id' => $social_data['id'],
                'username' => $social_data['username'],
                'access_token' => $social_data['access_token'],
                'token_expire' => $social_data['token_expire'],
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate
            );

            return $this->mongo_db->insert('global_player', array(

                'username' => $data['username'],
                'password' => $password,
                'salt' => $salt,
                'first_name' => (isset($data['first_name'])) ? $data['first_name'] : $data['username'],
                'last_name' => (isset($data['last_name'])) ? $data['last_name'] : null,
                'nickname' => (isset($data['nickname'])) ? $data['nickname'] : null,
                'gender' => (isset($data['gender'])) ? intval($data['gender']) : 0,
                'birth_date' => (isset($data['birth_date'])) ? new MongoDate(strtotime($data['birth_date'])) : null,
                'image' => $data['image'],
                'email' => $data['email'],
                'status' => true,
                'social' => $social,
                'phone_number' => (isset($data['phone_number'])) ? $data['phone_number'] : null,
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate,

            ));
        } else {
            array_push($social, $social_data['profile']);
            array_push($social, $social_data['accessCredentials']);
            $profile = $social_data['profile'];
            return $this->mongo_db->insert('global_player', array(

                'first_name' => $profile['name']['givenName'],
                'last_name' => $profile['name']['familyName'],
                'gender' => (isset($profile['gender'])) ? intval($profile['gender']) : 0,
                'birth_date' => (isset($profile['birthday'])) ? new MongoDate(strtotime($profile['birthday'])) : null,
                'image' => $profile['photo'],
                'email' => $profile['email'],
                'status' => true,
                'social' => $social,
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate,

            ));
        }
    }

    public function updateGlobalPlayer($id, $fieldData)
    {
        if (!$id) {
            return false;
        }

        if (isset($fieldData['gender'])) {
            $fieldData['gender'] = intval($fieldData['gender']);
        }
        if (isset($fieldData['birth_date'])) {
            $fieldData['birth_date'] = new MongoDate(strtotime($fieldData['birth_date']));
        }

        $fieldData['date_modified'] = new MongoDate(time());
        $this->mongo_db->where('_id', $id);
        $this->mongo_db->set($fieldData);
        return $this->mongo_db->update('global_player');
    }

    public function readGlobalPlayer($id, $fields = null)
    {
        if (!$id) {
            return array();
        }
        if ($fields) {
            $this->mongo_db->select($fields);
        }
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('_id', $id);
        $result = $this->mongo_db->get('global_player');
        if (!$result) {
            return $result;
        }
        $result = $result[0];
        if (isset($result['date_added'])) {
            $result['registered'] = datetimeMongotoReadable($result['date_added']);
            unset($result['date_added']);
        }
        if (isset($result['birth_date']) && $result['birth_date']) {
            $result['birth_date'] = date('Y-m-d', $result['birth_date']->sec);
        }
        return $result;
    }

    private function getToken($data)
    {
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where(array(
            'player_id' => $data['player_id'],
            'social_name' => $data['type'],
        ));
        $this->mongo_db->limit(1);
        return $this->mongo_db->get('global_token');
    }

    public function requestClientSite($data)
    {
        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('global_player_to_client', array(

            'player_id' => new MongoId($data['player_id']),
            'client_id' => new MongoId($data['client_id']),
            'site_id' => new MongoId($data['site_id']),
            'status' => false,
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate,

        ));

    }

    public function searchClient($data)
    {
        $this->mongo_db->select(null);
        $this->mongo_db->where(array(
            'company' => $data
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_client');
        return $results;
    }

    public function searchSite($client_id)
    {
        $this->mongo_db->select(null);
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id)
        ));
        $this->mongo_db->limit(10);
        $results = $this->mongo_db->get('playbasis_client_site');
        return $results;
    }

    public function searchFeatureForClient($client_id, $site_id)
    {
        $clientFeature = array();
        $this->mongo_db->select('feature_id');
        $this->mongo_db->where(array(
            'client_id' => new MongoId($client_id),
            'site_id' => new MongoId($site_id)
        ));
        $allfeature = $this->mongo_db->get('playbasis_feature_to_client');

        foreach ($allfeature as $feature) {
            array_push($clientFeature, $feature['feature_id']);
        }

        $this->mongo_db->select(null);
        $this->mongo_db->where_in('_id', $clientFeature);
        $features = $this->mongo_db->get('playbasis_feature');

        $menus = array();
        foreach ($features as $feature) {
            if (array_key_exists('dash', $feature)) {
                if ($feature['dash'] == 1) {
                    array_push($menus, $feature);
                }
            }

        }
        return $menus;
    }

    public function chooseService($data)
    {
        $mongoDate = new MongoDate(time());

        $this->mongo_db->insert('playbasis_service_to_player', array(

            'player_id' => new MongoId($data['player_id']),
            'site_id' => new MongoId($data['site_id']),
            'feature_id' => new MongoId($data['feature_id']),
            'service_id' => $data['service_id'],
            'status' => $data['status'],
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate,

        ));
    }

    public function storeDeviceToken($data)
    {
        $mongoDate = new MongoDate(time());

        $this->mongo_db->select(null);
        $this->mongo_db->where(array(
            'player_id' => new MongoId($data['player_id']),
            'site_id' => new MongoId($data['site_id']),
            'device_token' => $data['device_token']
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('global_player_device');
        if (!$results) {
            $this->mongo_db->insert('global_player_device', array(

                'player_id' => new MongoId($data['player_id']),
                'site_id' => new MongoId($data['site_id']),
                'device_token' => $data['device_token'],
                'device_description' => $data['device_description'],
                'device_name' => $data['device_name'],
                'status' => true,
                'date_added' => $mongoDate,
                'date_modified' => $mongoDate,
                'type' => $data['type']

            ));
        }

    }

}