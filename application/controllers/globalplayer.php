<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

//require_once APPPATH . '/libraries/ApnsPHP/Autoload.php';
class GlobalPlayer extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('client_model');
        $this->load->model('player_model');
        $this->load->model('tracker_model');
        $this->load->model('point_model');
        $this->load->model('action_model');
        $this->load->model('level_model');
        $this->load->model('reward_model');
        $this->load->model('quest_model');
        $this->load->model('badge_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
    }

    public function index_get($player_id = '')
    {
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        //read player information
        $player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
            'username',
            'first_name',
            'last_name',
            'gender',
            'image',
            'exp',
            'level',
            'date_added',
            'birth_date'
        ));

        //get last login/logout
        $player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
        $player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id,
            'LOGOUT');
        $player['player']['cl_player_id'] = $player_id;
        $this->response($this->resp->setRespond($player), 200);
    }

    public function index_post($player_id = '')
    {
        if (!$player_id) {
            $this->response($this->error->setError('PARAMETER_MISSING', array(
                'player_id'
            )), 200);
        }
        //get playbasis player id
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        }
        //read player information
        $player['player'] = $this->player_model->readPlayer($pb_player_id, $this->site_id, array(
            'username',
            'first_name',
            'last_name',
            'gender',
            'image',
            'email',
            'phone_number',
            'exp',
            'level',
            'date_added',
            'birth_date'
        ));

        //get last login/logout
        $player['player']['last_login'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id, 'LOGIN');
        $player['player']['last_logout'] = $this->player_model->getLastEventTime($pb_player_id, $this->site_id,
            'LOGOUT');
        $player['player']['cl_player_id'] = $player_id;
        $this->response($this->resp->setRespond($player), 200);
    }

    public function mytest_get($data = '')
    {
        echo "mytest success";
    }

    public function register_post()
    {
        $playerInfo = array(
            'email' => $this->input->post('_id'),
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')

        );
        $this->global_player_model->createGlobalPlayer($playerInfo, null);

    }

    public function login_post()
    {
        $playerInfo = array(
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        );
        $result = $this->global_player_model->loginAction($playerInfo, 'login');
        //echo('login success');
        $result = $result[0];
        echo($result['_id']);
    }

    public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        $cl_player_id = 'test1234';
        $image = 'profileimage.jpg';
        $email = 'test123@email.com';
        $username = 'test-1234';
        $token = $this->auth_model->getApiInfo($credential);
        echo '<br>createPlayer:<br>';
        $pb_player_id = $this->player_model->createPlayer(array_merge($token, array(
            'player_id' => $cl_player_id,
            'image' => $image,
            'email' => $email,
            'username' => $username,
            'birth_date' => '1982-09-08',
            'gender' => 1
        )));
        print_r($pb_player_id);
        echo '<br>readPlayer:<br>';
        $result = $this->player_model->readPlayer($pb_player_id, $token['site_id'], array(
            'cl_player_id',
            'pb_player_id',
            'username',
            'email',
            'image',
            'date_added',
            'birth_date'
        ));
        print_r($result);
        echo '<br>updatePlayer:<br>';
        $result = $this->player_model->updatePlayer($pb_player_id, $token['site_id'], array(
            'username' => 'test-4567',
            'email' => 'test4567@email.com'
        ));
        $result = $this->player_model->readPlayer($pb_player_id, $token['site_id'], array(
            'username',
            'email'
        ));
        print_r($result);
        echo '<br>deletePlayer:<br>';
        $result = $this->player_model->deletePlayer($pb_player_id, $token['site_id']);
        print_r($result);
        echo '<br>';
        $cl_player_id = '1';
        echo '<br>getPlaybasisId:<br>';
        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($token, array(
            'cl_player_id' => $cl_player_id
        )));
        print_r($pb_player_id);
        echo '<br>getClientPlayerId:<br>';
        $cl_player_id = $this->player_model->getClientPlayerId($pb_player_id, $token['site_id']);
        print_r($cl_player_id);
        echo '<br>';
        echo '<br>getPlayerPoints:<br>';
        $result = $this->player_model->getPlayerPoints($pb_player_id, $token['site_id']);
        print_r($result);
        $reward_id = $this->point_model->findPoint(array_merge($token, array('reward_name' => 'exp')));
        echo '<br>getPlayerPoint:<br>';
        $result = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $token['site_id']);
        print_r($result);
        echo '<br>getLastActionPerform:<br>';
        $result = $this->player_model->getLastActionPerform($pb_player_id, $token['site_id']);
        print_r($result);
        echo '<br>getActionPerform:<br>';
        $action_id = $this->action_model->findAction(array_merge($token, array('action_name' => 'like')));
        $result = $this->player_model->getActionPerform($pb_player_id, $action_id, $token['site_id']);
        print_r($result);
        echo '<br>getActionCount:<br>';
        $result = $this->player_model->getActionCount($pb_player_id, $action_id, $token['site_id']);
        print_r($result);
        echo '<br>getBadge:<br>';
        $result = $this->player_model->getBadge($pb_player_id, $token['site_id']);
        print_r($result);
        echo '<br>getLastEventTime<br>';
        $result = $this->player_model->getLastEventTime($pb_player_id, $token['site_id'], 'LOGIN');
        print_r($result);
        echo '<br>';
        echo '<br>getLeaderboard<br>';
        $result = $this->player_model->getLeaderboard('exp', 20, $token['client_id'], $token['site_id']);
        print_r($result);
        echo '<br>getLeaderboards<br>';
        $result = $this->player_model->getLeaderboards(20, $token['client_id'], $token['site_id']);
        print_r($result);
        echo '</pre>';
    }

    private function validClPlayerId($cl_player_id)
    {
        return (!preg_match("/^([-a-z0-9_-])+$/i", $cl_player_id)) ? false : true;
    }

    private function validTelephonewithCountry($number)
    {
        return (!preg_match("/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d| 2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]| 4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/",
            $number)) ? false : true;
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else {
                if (get_class($item) === 'MongoDate') {
                    $item = datetimeMongotoReadable($item);
                }
            }
        }
    }
}

function index_cl_player_id($obj)
{
    return $obj['cl_player_id'];
}

?>
