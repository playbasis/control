<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Auth extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('client_model');
        $this->load->model('energy_model');
        $this->load->model('player_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->library('form_validation');
    }

    public function index_post()
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'api_secret'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $API['key'] = $this->input->post('api_key');
        $API['secret'] = $this->input->post('api_secret');
        $clientInfo = $this->auth_model->getApiInfo($API);
        if ($clientInfo) {
            $token = $this->auth_model->generateToken(array_merge($clientInfo, $API));
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }

    public function renew_post()
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'api_secret'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $API['key'] = $this->input->post('api_key');
        $API['secret'] = $this->input->post('api_secret');
        $clientInfo = $this->auth_model->getApiInfo($API);
        if ($clientInfo) {
            $token = $this->auth_model->renewToken(array_merge($clientInfo, $API));
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }

    public function player_post($player_id)
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'password'
        ));

        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $clientInfo = $this->auth_model->getApiInfo(array('key' => $this->input->post('api_key')));
        $clientInfo['key'] = $this->input->post('api_key');
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($clientInfo, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        } else {
            $clientInfo['pb_player_id'] = $pb_player_id;
            $clientInfo['password'] = do_hash($this->input->post('password'));
        }
        $player = $this->player_model->checkPlayerPassword($clientInfo);
        if(!$player) {
            $this->response($this->error->setError('PASSWORD_INCORRECT'), 200);
        }

        if ($clientInfo) {
            $token = $this->auth_model->generatePlayerToken($clientInfo);
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }

    public function player_renew_post($player_id)
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'refresh_token'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        $clientInfo = $this->auth_model->getApiInfo(array('key' => $this->input->post('api_key')));
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($clientInfo, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        } else {
            $clientInfo['pb_player_id'] = $pb_player_id;
            $clientInfo['key'] = $this->input->post('api_key');
            $clientInfo['refresh_token'] = $this->input->post('refresh_token');
        }

        $player = $this->auth_model->getPlayerToken($clientInfo, $this->input->post('refresh_token'));
        if(!$player) {
            $this->response($this->error->setError('REFRESH_TOKEN_INCORRECT'), 200);
        }

        if ($clientInfo) {
            $token = $this->auth_model->renewPlayerToken($clientInfo);
            $this->response($this->resp->setRespond($token), 200);
        } else {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }
    }

    public function player_revoke_post($player_id)
    {
        if (!$this->validToken){
            $this->response($this->error->setError('INVALID_TOKEN'), 200);
        }

        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }

        $this->auth_model->revokePlayerToken(array_merge($this->validToken,array('pb_player_id' => $pb_player_id)));
        $this->response($this->resp->setRespond(), 200);
    }

    public function player_register_post($player_id)
    {
        $required = $this->input->checkParam(array(
            'api_key',
            'password',
        ));

        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }

        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        }
        $api_key = $this->input->post('api_key');
        $clientInfo = $this->auth_model->getApiInfo(array('key' => $api_key));

        if (!$clientInfo) {
            $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET', $required), 200);
        }

        if (!$this->validClPlayerId($player_id)) {
            $this->response($this->error->setError('USER_ID_INVALID'), 200);
        }

        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($clientInfo, array(
            'cl_player_id' => $player_id
        )));

        if ($pb_player_id) {
            $this->response($this->error->setError('USER_ALREADY_EXIST'), 200);
        }

        $playerInfo = array(
            'email' => $this->input->post('email') ? $this->input->post('email') : "qa1+" .$player_id. "@playbasis.com" ,
            'image' => $this->input->post('image') ? $this->input->post('image') : "https://www.pbapp.net/images/default_profile.jpg",
            'username' => $this->input->post('username') ? $this->input->post('username') : $player_id,
            'player_id' => $player_id
        );

        //check if username is already exist in this site
        $player = $this->player_model->getPlayerByUsername($clientInfo['site_id'], $playerInfo['username']);
        if ($player) {
            $this->response($this->error->setError('USERNAME_ALREADY_EXIST'), 200);
        }

        //check if email is already exist in this site
        $player = $this->player_model->getPlayerByEmail($clientInfo['site_id'], $playerInfo['email']);
        if ($player) {
            $this->response($this->error->setError('EMAIL_ALREADY_EXIST'), 200);
        }

        $firstName = $this->input->post('first_name');
        if ($this->utility->is_not_empty($firstName)) {
            $playerInfo['first_name'] = $firstName;
        }
        $lastName = $this->input->post('last_name');
        if ($this->utility->is_not_empty($lastName)) {
            $playerInfo['last_name'] = $lastName;
        }
        $nickName = $this->input->post('nickname');
        if ($this->utility->is_not_empty($nickName)) {
            $playerInfo['nickname'] = $nickName;
        }
        $phoneNumber = $this->input->post('phone_number');
        if ($phoneNumber) {
            if ($this->validTelephonewithCountry($phoneNumber)) {
                $playerInfo['phone_number'] = $phoneNumber;
            } else {
                $this->response($this->error->setError('USER_PHONE_INVALID'), 200);
            }
        }
        $playerInfo['tags'] = $this->input->post('tags') && !is_null($this->input->post('tags')) ? explode(',', $this->input->post('tags')) : null;
        $facebookId = $this->input->post('facebook_id');
        if ($facebookId) {
            $playerInfo['facebook_id'] = $facebookId;
        }
        $twitterId = $this->input->post('twitter_id');
        if ($twitterId) {
            $playerInfo['twitter_id'] = $twitterId;
        }
        $instagramId = $this->input->post('instagram_id');
        if ($instagramId) {
            $playerInfo['instagram_id'] = $instagramId;
        }
        if ($this->password_validation($clientInfo['client_id'], $clientInfo['site_id'],
            $playerInfo['username'])
        ) {
            $this->player_model->unlockPlayer($clientInfo['site_id'], $pb_player_id);
            $password = $this->input->post('password');
            if ($password) {
                $playerInfo['password'] = do_hash($password);
            }
        } else {
            $this->response($this->error->setError('FORM_VALIDATION_FAILED', $this->validation_errors()[0]), 200);
        }
        $gender = $this->input->post('gender');
        if ($this->utility->is_not_empty($gender)) {
            $playerInfo['gender'] = $gender;
        }
        $birthdate = $this->input->post('birth_date');
        if ($birthdate) {
            $timestamp = strtotime($birthdate);
            $playerInfo['birth_date'] = date('Y-m-d', $timestamp);
        }
        $approve_status = $this->input->post('approve_status');
        if ($approve_status) {
            $playerInfo['approve_status'] = $approve_status;
        }
        $device_id = $this->input->post('device_id');
        if ($device_id) {
            $playerInfo['device_id'] = $device_id;
        }
        $referral_code = $this->input->post('code');
        $anonymous = $this->input->post('anonymous');

        if ($anonymous && $referral_code) {
            $this->response($this->error->setError('ANONYMOUS_CANNOT_REFERRAL'), 200);
        }

        // check referral code
        $playerA = null;
        if ($referral_code) {
            $playerA = $this->player_model->findPlayerByCode($clientInfo["site_id"], $referral_code,
                array('cl_player_id'));
            if (!$playerA) {
                $this->response($this->error->setError('REFERRAL_CODE_INVALID'), 200);
            }
        }

        //check anonymous feature depend on plan
        if ($anonymous) {
            $clientData = array(
                'client_id' => $clientInfo['client_id'],
                'site_id' => $clientInfo['site_id']
            );
            $result = $this->client_model->checkFeatureByFeatureName($clientData, "Anonymous");
            if ($result) {
                $playerInfo['anonymous'] = $anonymous;
            } else {
                $this->response($this->error->setError('ANONYMOUS_NOT_FOUND'), 200);
            }
        }

        // get plan_id
        $plan_id = $this->client_model->getPlanIdByClientId($clientInfo["client_id"]);
        try {
            $player_limit = $this->client_model->getPlanLimitById(
                $this->client_plan,
                "others",
                "player");
        } catch (Exception $e) {
            $this->response($this->error->setError('INTERNAL_ERROR'), 200);
        }

        $pb_player_id = $this->player_model->createPlayer(array_merge($clientInfo, $playerInfo), $player_limit);

        /* trigger reward for referral program (if any) */
        if ($playerA) {

            // [rule] A invite B
            $this->utility->request('engine', 'json', http_build_query(array(
                'api_key' => $api_key,
                'pb_player_id' => $playerA['_id'] . '',
                'action' => ACTION_INVITE,
                'pb_player_id-2' => $pb_player_id . ''
            )));


            // [rule] B invited by A
            $this->utility->request('engine', 'json', http_build_query(array(
                'api_key' => $api_key,
                'pb_player_id' => $pb_player_id . '',
                'action' => ACTION_INVITED,
                'pb_player_id-2' => $playerA['_id'] . ''
            )));
        }


        $this->utility->request('engine', 'json', http_build_query(array(
            'api_key' => $api_key,
            'pb_player_id' => $pb_player_id . '',
            'action' => ACTION_REGISTER
        )));

        /* Automatically energy initialization after creating a new player*/
        foreach ($this->energy_model->findActiveEnergyRewardsById($clientInfo['client_id'], $clientInfo['site_id']) as $energy) {
            $energy_reward_id = $energy['reward_id'];
            $energy_max = (int)$energy['energy_props']['maximum'];
            $batch_data = array();
            if ($energy['type'] == 'gain') {
                array_push($batch_data, array(
                    'pb_player_id' => $pb_player_id,
                    'cl_player_id' => $player_id,
                    'client_id' => $clientInfo['client_id'],
                    'site_id' => $clientInfo['site_id'],
                    'reward_id' => $energy_reward_id,
                    'value' => $energy_max,
                    'date_cron_modified' => new MongoDate(),
                    'date_added' => new MongoDate(),
                    'date_modified' => new MongoDate()
                ));
            } elseif ($energy['type'] == 'loss') {
                array_push($batch_data, array(
                    'pb_player_id' => $pb_player_id,
                    'cl_player_id' => $player_id,
                    'client_id' => $clientInfo['client_id'],
                    'site_id' => $clientInfo['site_id'],
                    'reward_id' => $energy_reward_id,
                    'value' => 0,
                    'date_cron_modified' => new MongoDate(),
                    'date_added' => new MongoDate(),
                    'date_modified' => new MongoDate()
                ));
            }
            if (!empty($batch_data)) {
                $this->energy_model->bulkInsertInitialValue($batch_data);
            }
        }
        if ($pb_player_id) {
            $this->response($this->resp->setRespond(), 200);
        } else {
            $this->response($this->error->setError('LIMIT_EXCEED'), 200);
        }
    }

    private function validClPlayerId($cl_player_id)
    {
        return (!preg_match("/^([a-zA-Z0-9-_=]+)+$/i", $cl_player_id)) ? false : true;
    }

    private function password_validation($client_id, $site_id, $inhibited_str = '')
    {
        $return_status = false;
        $setting = $this->player_model->getSecuritySetting($client_id, $site_id);
        if (isset($setting['password_policy'])) {
            $password_policy = $setting['password_policy'];
            $rule = 'trim|xss_clean';
            if ($password_policy['min_char'] && $password_policy['min_char'] > 0) {
                $rule = $rule . '|' . 'min_length[' . $password_policy['min_char'] . ']';
            }
            if ($password_policy['alphabet'] && $password_policy['numeric']) {
                $rule = $rule . '|callback_require_at_least_number_and_alphabet';
            } elseif ($password_policy['alphabet']) {
                $rule = $rule . '|callback_require_at_least_alphabet';
            } elseif ($password_policy['numeric']) {
                $rule = $rule . '|callback_require_at_least_number';
            }

            if ($password_policy['user_in_password'] && ($inhibited_str != '')) {
                $rule = $rule . '|callback_word_in_password[' . $inhibited_str . ']';
            }
            $this->form_validation->set_rules('password', 'password', $rule);
            if ($this->form_validation->run()) {
                $return_status = true;
            } else {
                $return_status = false;
            }
        } else {
            $return_status = true;
        }
        return $return_status;

    }
    /*public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        echo '<br>getApiInfo:<br>';
        $result = $this->auth_model->getApiInfo($credential);
        print_r($result);
        echo 'site id: ' . $result['site_id']->{'$id'};
        echo '<br>';
        echo 'client id: ' . $result['client_id']->{'$id'};
        echo '<br>';
        echo '<br>generateToken:<br>';
        $result = $this->auth_model->generateToken(array_merge($result, $credential));
        print_r($result);
        echo '<br>findToken:<br>';
        $result = $this->auth_model->findToken($result['token']);
        print_r($result);
        echo '<br>createToken:<br>';
        $result = $this->auth_model->createToken($result['client_id'], $result['site_id']);
        print_r($result);
        echo '<br>createTokenFromAPIKey<br>';
        $result = $this->auth_model->createTokenFromAPIKey($credential['key']);
        print_r($result);
        echo '</pre>';
    }*/
}

?>
