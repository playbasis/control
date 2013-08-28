<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_model extends MY_Model
{
    private $user_id;
    private $username;
    private $client_id;
    private $site_id;
    private $user_group_id;
    private $permission = array();
    private $database;
    private $admin_group_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');

        if($this->session->userdata('user_id')){
            $this->user_id = $this->session->userdata('user_id');
            $this->username = $this->session->userdata('username');
            $this->user_group_id = $this->session->userdata('user_group_id');
            $this->database = $this->session->userdata('database');
            $this->client_id = $this->session->userdata('client_id');
            $this->site_id = $this->session->userdata('site_id');
            $this->permission = $this->session->userdata('permission');

            if($this->session->userdata('ip') != $_SERVER['REMOTE_ADDR']){
                // update new ip //
                $this->mongo_db->where('_id', $this->user_id);
                $this->mongo_db->set('ip', db_clean($_SERVER['REMOTE_ADDR'], 30));
                $this->mongo_db->update('user');
                // end update new ip //
            }
        }else{
            $this->logout();
            return;
        }

    }
    public function login($u, $p)
    {

        $this->set_site_mongodb(0);
        $Q = $this->mongo_db->select(array('salt'))
            ->where('username', db_clean($u, 40))
            ->limit(1)
            ->get('user');

        if (count($Q) > 0) {
            $row = $Q[0];

            $this->mongo_db->select(array('_id','user_id','username','user_group_id','database','ip'));
            $this->mongo_db->where('username', db_clean($u, 20));
            $this->mongo_db->where('password', db_clean(dohash($p, $row['salt']), 40));
            $this->mongo_db->where('status', true);
            $this->mongo_db->limit(1);
            $Q = $this->mongo_db->get('user');

            if (count($Q) > 0) {

                $row = $Q[0];

                $this->user_id = $row['_id'];
                $this->username = $row['username'];
                $this->user_group_id = $row['user_group_id'];
                $this->database = $row['database'];
                $ip = $row['ip'];

                // update new salt //
                $salt = get_random_password(10, 10);
                $data = array('salt' => db_clean($salt, 40),
                    'password' => db_clean(dohash($p, $salt), 40)
                );
                $this->mongo_db->where('username', db_clean($u, 40));
                $this->mongo_db->set('last_login', date('Y-m-d H:i:s'));
                $this->mongo_db->set($data);
                $this->mongo_db->update('user');
                // end update new salt //

                $this->mongo_db->select(array('client_id'));
                $this->mongo_db->where('user_id', new MongoID($this->user_id));
                $this->mongo_db->where('status', true);
                $this->mongo_db->limit(1);
                $Q = $this->mongo_db->get('user_to_client');

                if(count($Q)>0){
                    $row1 = $Q[0];
                    $this->client_id = $row1['client_id'];
                }else{
                    $this->client_id = 0;
                }

                $this->mongo_db->select(array('_id'));
                $this->mongo_db->where('client_id', new MongoID($this->client_id));
                $this->mongo_db->where('status', true);
                $this->mongo_db->limit(1);
                $Q = $this->mongo_db->get('playbasis_client_site');

                if(count($Q)>0){
                    $row2 = $Q[0];
                    $this->site_id = $row2['_id'];
                }else{
                    $this->site_id = 0;
                }

                $this->mongo_db->select(array('permission'));
                $this->mongo_db->where('_id', $this->user_group_id);
                $this->mongo_db->limit(1);
                $Q = $this->mongo_db->get('user_group');

                if(count($Q)>0){
                    $row3 = $Q[0];
                    $permissions = unserialize($row3['permission']);
                }else{
                    $this->session->unset_userdata('user_id');
                    return;
                }

                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        $this->permission[$key] = $value;
                    }
                }

                $this->session->set_userdata('user_id',$this->user_id );
                $this->session->set_userdata('username',$this->username );
                $this->session->set_userdata('user_group_id',$this->user_group_id );
                $this->session->set_userdata('database',$this->database );
                $this->session->set_userdata('client_id',$this->client_id );
                $this->session->set_userdata('site_id',$this->site_id );
                $this->session->set_userdata('permission',$this->permission );
                $this->session->set_userdata('ip',$ip );

            } else {
                $this->session->unset_userdata('user_id');
            }
        }
    }

    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('client_id');
        $this->session->unset_userdata('site_id');
        $this->session->unset_userdata('user_group_id');
        $this->session->unset_userdata('database');
        $this->session->unset_userdata('permission');
        $this->session->unset_userdata('ip');

        $this->user_id = '';
        $this->username = '';
        $this->client_id = '';
        $this->site_id = '';
        $this->user_group_id = '';
        $this->database = '';
        $this->permission = '';
    }

    public function hasPermission($key, $value) {
        if (isset($this->permission[$key])) {
            return in_array($value, $this->permission[$key]);
        } else {
            return false;
        }
    }

    public function isLogged() {
        return $this->user_id;
    }

    public function getId() {
        return $this->user_id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getSiteId() {
        return $this->site_id;
    }

    public function getUserGroupId() {
        return $this->user_group_id;
    }

    public function getUserName() {
        return $this->username;
    }

    public function getClientDatabase() {
        return isset($this->database) ? $this->database : "core";
    }

    public function getAdminGroupID(){
        if($this->session->userdata('admin_group_id'))
            return $this->session->userdata('admin_group_id');

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', 'Top Administrator');
        $this->mongo_db->limit(1);
        $Q = $this->mongo_db->get('user_group');

        $row = $Q[0];
        $this->admin_group_id = $row['_id'];
        $this->session->set_userdata('admin_group_id',$this->admin_group_id );
        return $this->admin_group_id;
    }
}
?>