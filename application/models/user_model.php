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
    private $mobile;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');

        if ($this->session->userdata('user_id')) {
            $this->user_id = $this->session->userdata('user_id');
            $this->username = $this->session->userdata('username');
            $this->user_group_id = $this->session->userdata('user_group_id');
            $this->database = $this->session->userdata('database');
            $this->client_id = $this->session->userdata('client_id');
            $this->site_id = $this->session->userdata('site_id');
            $this->permission = $this->session->userdata('permission');
            $this->mobile = $this->session->userdata('mobile');

            if ($this->session->userdata('ip') != $_SERVER['REMOTE_ADDR']) {
                // update new ip //
                $this->mongo_db->where('_id', $this->user_id);
                $this->mongo_db->set('ip', db_clean($_SERVER['REMOTE_ADDR'], 30));
                $this->mongo_db->update('user');
                // end update new ip //
            }
        } else {
            $this->logout();
            return;
        }

    }

    public function getTotalNumUsers()
    {
        $this->set_site_mongodb($this->site_id);

        return $this->mongo_db->count('user');
    }

    public function getUserInfo($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $results = $this->mongo_db->get('user');

        return $results ? $results[0] : null;
    }

    public function editUser($user_id, $data)
    {
        $this->set_site_mongodb($this->site_id);

        $find_salt = $this->getUserInfo($user_id);
        $salt = $find_salt['salt'];

        $check_email = isset($data['email']) && !is_null($data['email']) && !$this->findEmail($data);
        $check_update = false;
        $this->mongo_db->where('_id', new MongoID($user_id));

        if ($data['user_group']) {
            $this->mongo_db->set('user_group_id', new MongoID($data['user_group']));
            $check_update = true;
        } else {
            $this->mongo_db->set('user_group_id', null);
        }

        // if(isset($data['username']) && !is_null($data['username'])){
        //     $this->mongo_db->set('username', $data['username']);
        //     $check_update = true;
        // }

        if (isset($data['firstname']) && !is_null($data['firstname'])) {
            $this->mongo_db->set('firstname', $data['firstname']);
            $check_update = true;
        }

        if (isset($data['lastname']) && !is_null($data['lastname'])) {
            $this->mongo_db->set('lastname', $data['lastname']);
            $check_update = true;
        }

        if ($check_email) {
            $this->mongo_db->set('email', $data['email']);
            $this->mongo_db->set('username', $data['email']);
            $check_update = true;
        }

        if (isset($data['status']) && !is_null($data['status'])) {
            $this->mongo_db->set('status', (bool)$data['status']);
            $check_update = true;
        }

        if (isset($data['image']) && !is_null($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            $check_update = true;
        }

        if ($data['password'] == $data['confirm_password']) {
            if (trim($data['password']) != "" && trim($data['confirm_password'] != "")) {
                $this->mongo_db->set('password', dohash($data['password'], $salt));
                $check_update = true;
            }
        }

        if ($check_update) {
            $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
            return $this->mongo_db->update('user');
        }
        return false;
    }

    public function insertUser()
    {
        $this->set_site_mongodb($this->site_id);

        $regex = new MongoRegex("/^" . preg_quote(utf8_strtolower($this->input->post('email'))) . "$/i");
        $this->mongo_db->where('username', $regex);

        if ($this->mongo_db->count('user') == 0) {
            if ($this->input->post('user_group')) {
                $user_group_id = new MongoID($this->input->post('user_group'));
            } else {
                $user_group_id = null;
            }

            // $username = $this->input->post('username');
            $username = $this->input->post('email');
            $firstname = $this->input->post('firstname');
            $email = $this->input->post('email');
            $lastname = $this->input->post('lastname');

            if ($this->User_model->getClientId()) {
                $status = false;
            } else {
                $status = $this->input->post('status');
                if ($status == "1") {
                    $status = true;
                } else {
                    $status = false;
                }
            }

            $ip = $_SERVER['REMOTE_ADDR'];
            $salt = get_random_password(10, 10);

            $insert_password = $this->input->post('password');

            if($insert_password != ""){
                $password = dohash($insert_password, $salt);
            } else {
                $randdom_password = get_random_password(8, 8);
                $password = dohash($randdom_password, $salt);
            }


            $date_added = new MongoDate(strtotime(date("Y-m-d H:i:s")));

            $random_key = get_random_password(8, 8);

            $data = array(
                'user_group_id' => $user_group_id,
                'username' => $username,
                'password' => $password,
                'salt' => $salt,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'code' => "",
                'ip' => $ip,
                'status' => $status,
                'database' => "core",
                'date_added' => $date_added,
                'random_key' => $random_key,
                'password_key' => null
            );

            $this->load->library('parser');
            $this->load->library('email');
            $vars = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $email,
                'password' => $insert_password,
                'key' => $random_key,
                'url' => ($insert_password == "") ? site_url('user_password/?key=')  : site_url('enable_user/?key='),
                'base_url' => site_url()
            );

            if ($insert_password == "") {
                $htmlMessage = $this->parser->parse('emails/user_activated.html', $vars, true);
            } else {
                $htmlMessage = $this->parser->parse('emails/user_activateaccountwithpassword.html', $vars, true);
            }

            $this->email($email, '[Playbasis] Please activate your account', $htmlMessage);

            return $this->mongo_db->insert('user', $data);
        } else {
            return false;
        }
    }

    private function email($to, $subject, $message)
    {
        $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');
        $this->amazon_ses->to($to);
        $this->amazon_ses->bcc(array(EMAIL_FROM));
        $this->amazon_ses->subject($subject);
        $this->amazon_ses->message($message);
        $this->amazon_ses->send();
    }

    public function addUserToClient($data)
    {
        $this->set_site_mongodb($this->site_id);

        $data_insert = array(
            'client_id' => new MongoID($data['client_id']),
            'user_id' => new MongoID($data['user_id']),
            'status' => true
        );
        return $this->mongo_db->insert('user_to_client', $data_insert);
    }

    public function listPendingUsers()
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('status', false);
        $this->mongo_db->order_by(array('date_added' => -1));
        $results = $this->mongo_db->get("user");
        return $results;
    }

    public function enableUser($user_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('status', true);
        $this->mongo_db->set('random_key', null);
        $this->mongo_db->update('user');
    }

    public function getById($user_id)
    {
        $this->set_site_mongodb(0); // use default in case of pending users
        $this->mongo_db->where('_id', new MongoID($user_id));
        $results = $this->mongo_db->get("user");
        return ($results && count($results) > 0) ? $results[0] : null;
    }

    public function fetchAllUsers($data)
    {
        $this->set_site_mongodb($this->site_id);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('username', $regex);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results = $this->mongo_db->get("user");

        return $results;
    }

    public function getTotalUserByClientId($data)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $user_data = $this->mongo_db->count("user_to_client");

        return $user_data;
    }

    public function getUserByClientId($data)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $user_data = $this->mongo_db->get("user_to_client");

        return $user_data;
    }

    public function getClientIdByUserId($user_id)
    {
        $this->set_site_mongodb($this->site_id);
        $this->mongo_db->where('user_id', $user_id);
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get("user_to_client");
        return $result ? $result[0]['client_id'] : null;
    }

    public function getUserGroups()
    {
        $this->set_site_mongodb($this->site_id);

        return $this->mongo_db->get("user_group");
    }

    public function getUserGroupNameForUser($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $usergroups = $this->mongo_db->get('user_group');
        $user = $this->getUserInfo($user_id);

        $usergroup_name = "";
        foreach ($usergroups as $usergroup) {
            if ($usergroup['_id'] == $user['user_group_id']) {
                $usergroup_name = $usergroup['name'];
                break;
            }
        }

        return $usergroup_name;
    }

    public function deleteUser($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->delete("user");
        $this->mongo_db->where('user_id', new MongoID($user_id));
        $this->mongo_db->delete("user_to_client");
    }

    public function login($u, $p, &$is_locked = false)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array('_id', 'salt', 'locked', 'login_attempt', 'user_group_id'));
        $regex = array('$regex' => new MongoRegex("/^" . preg_quote(db_clean($u, 255)) . "$/i"));
        $this->mongo_db->where('username', $regex);
        $this->mongo_db->limit(1);
        $Q = $this->mongo_db->get('user');
        $user_info = $Q[0];
        $is_locked = (isset($user_info['locked']) && $user_info['locked']) ? $user_info['locked'] : false;

        if (count($Q) > 0 && !$is_locked) {
            $row = $Q[0];

            $this->mongo_db->select(array('_id', 'user_id', 'username', 'user_group_id', 'database', 'ip' , 'last_app'));
            $this->mongo_db->where('username', $regex);
            $this->mongo_db->where('password', db_clean(dohash($p, $row['salt']), 40));
            $this->mongo_db->where('status', true);
            $this->mongo_db->limit(1);
            $Q = $this->mongo_db->get('user');

            if (count($Q) > 0) {

                // update new salt
                $salt = get_random_password(10, 10);
                $data = array(
                    'salt' => db_clean($salt, 40),
                    'password' => db_clean(dohash($p, $salt), 40)
                );
                $this->mongo_db->where('username', $regex);
                $this->mongo_db->set('last_login', date('Y-m-d H:i:s'));
                $this->mongo_db->set($data);
                $this->mongo_db->update('user');

                // $this->user_id
                $row = $Q[0];
                $this->user_id = $row['_id'];
                $this->username = $row['username'];
                $this->user_group_id = $row['user_group_id'];
                $this->database = $row['database'];
                $ip = $row['ip'];

                $this->resetLoginAttempt($row['_id']);

                // $this->client_id
                $this->mongo_db->select(array('client_id'));
                $this->mongo_db->where('user_id', new MongoID($this->user_id));
                $this->mongo_db->where('status', true);
                $this->mongo_db->limit(1);
                $Q1 = $this->mongo_db->get('user_to_client');
                if (count($Q1) > 0) {
                    $row1 = $Q1[0];
                    $this->client_id = $row1['client_id'];
                } else {
                    $this->client_id = null;
                }

                // $this->site_id
                if ($this->client_id) {
                    $this->site_id = isset($row['last_app']) && !empty($row['last_app']) ? $row['last_app'] : $this->fetchSiteId($this->client_id);
                    $this->set_last_app();
                }

                // $this->permission
                if ($this->user_group_id == $this->getAdminGroupID()) {
                    // Login as Playbasis admin
                    $this->mongo_db->select(array('permission'));
                    $this->mongo_db->where('_id', $this->user_group_id);
                    $this->mongo_db->limit(1);
                    $Q3 = $this->mongo_db->get('user_group');
                    if (count($Q3) > 0) {
                        $row3 = $Q3[0];
                        $permissions = $row3['permission'];
                        if (is_array($permissions)) {
                            foreach ($permissions as $key => $value) {
                                $this->permission[$key] = $value;
                            }
                        }
                    } else {
                        $this->logout();
                        return false;
                    }
                } elseif (!$this->site_id) {
                    // For first login
                    if ($this->client_id) {
                        $plan = $this->getPlan();
                        $features = $plan['feature_to_plan'];
                        $has_account = false;
                        $has_app = false;
                        foreach ($features as $feature) {
                            $this->mongo_db->select(array('link'));
                            $this->mongo_db->where('_id', $feature);
                            $this->mongo_db->where('status', true);
                            $this->mongo_db->limit(1);
                            $Q4 = $this->mongo_db->get('playbasis_feature');
                            $access[] = $Q4[0]['link'];
                            $modify[] = $Q4[0]['link'];
                            if ($Q4[0]['link'] == 'account') {
                                $has_account = true;
                            }
                            if ($Q4[0]['link'] == 'app') {
                                $has_app = true;
                            }
                        }
                        if (!$has_account) {
                            $access[] = 'account';
                            $modify[] = 'account';
                        }
                        if (!$has_app) {
                            $access[] = 'app';
                            $modify[] = 'app';
                        }
                        $this->permission['access'] = $access;
                        $this->permission['modify'] = $modify;
                    }
                } else {
                    // Login as Client user
                    $this->mongo_db->select(array('permission'));
                    $this->mongo_db->where('_id', $this->user_group_id);
                    $this->mongo_db->limit(1);
                    $Q3 = $this->mongo_db->get('user_group_to_client');
                    if (count($Q3) > 0) {
                        $row3 = $Q3[0];
                        $permissions = $row3['permission'];
                        if (is_array($permissions)) {
                            foreach ($permissions as $key => $value) {
                                $this->permission[$key] = $value;
                            }
                        }
                    } else {
                        $this->mongo_db->where('status', true);
                        $this->mongo_db->where('site_id', new MongoID($this->site_id));
                        $this->mongo_db->where('client_id', new MongoID($this->client_id));
                        $this->mongo_db->order_by(array('sort_order' => 1));
                        $results = $this->mongo_db->get("playbasis_feature_to_client");
                        foreach ($results as $key => $value) {
                            $access[$key] = $value['link'];
                            $modify[$key] = $value['link'];
                        }
                        $this->permission['access'] = $access;
                        $this->permission['modify'] = $modify;
                    }
                }

                if ($this->getAdminGroupID() || $this->client_id) {
                    $this->mobile = $this->findMobileByClientId($this->client_id);

                    $this->set_site_mongodb($this->site_id);

                    $this->session->set_userdata('multi_login', $this->setMultiLoginKey($this->user_id));
                    $this->session->set_userdata('user_id', $this->user_id);
                    $this->session->set_userdata('username', $this->username);
                    $this->session->set_userdata('user_group_id', $this->user_group_id);
                    $this->session->set_userdata('database', $this->database);
                    $this->session->set_userdata('client_id', $this->client_id);
                    $this->session->set_userdata('site_id', $this->site_id);
                    $this->session->set_userdata('permission', $this->permission);
                    $this->session->set_userdata('ip', $ip);
                    $this->session->set_userdata('mobile', $this->mobile);
                } else {
                    $this->logout();
                }

            } else {
                if ($user_info['user_group_id'] != $this->getAdminGroupID()) {
                    $this->increaseLoginAttempt($user_info['_id']);
                    $limit = defined('LIMIT_USER_LOGIN_ATTEMPT') ? LIMIT_USER_LOGIN_ATTEMPT : 3;
                    if (isset($user_info['login_attempt']) && ($user_info['login_attempt'] + 1 >= $limit)) {
                        $this->lockUser($user_info['_id']);
                    }
                }

                $this->logout();
            }
        }
    }

    public function cms_login($u, $p)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array('salt'));
        $regex = array('$regex' => new MongoRegex("/^" . preg_quote(db_clean($u, 255)) . "$/i"));
        $this->mongo_db->where('username', $regex);
        $this->mongo_db->limit(1);
        $Q = $this->mongo_db->get('user');

        if (count($Q) > 0) {
            $row = $Q[0];

            $this->mongo_db->select(array('_id', 'user_id', 'username', 'user_group_id', 'database', 'ip'));
            $this->mongo_db->where('username', $regex);
            $this->mongo_db->where('password', db_clean(dohash($p, $row['salt']), 40));
            $this->mongo_db->where('status', true);
            $this->mongo_db->limit(1);
            $Q = $this->mongo_db->get('user');

            if (count($Q) > 0) {

                // update new salt
                $salt = get_random_password(10, 10);
                $data = array(
                    'salt' => db_clean($salt, 40),
                    'password' => db_clean(dohash($p, $salt), 40)
                );
                $this->mongo_db->where('username', $regex);
                $this->mongo_db->set('last_login', date('Y-m-d H:i:s'));
                $this->mongo_db->set($data);
                $this->mongo_db->update('user');

                // $this->user_id
                $row = $Q[0];
                $this->user_id = $row['_id'];
                $this->username = $row['username'];
                $this->user_group_id = $row['user_group_id'];
                $this->database = $row['database'];
                $ip = $row['ip'];

                // $this->permission
                $this->mongo_db->select(array('permission'));
                $this->mongo_db->where('_id', $this->user_group_id);
                $this->mongo_db->limit(1);
                $Q3 = $this->mongo_db->get('user_group');
                if (count($Q3) > 0) {
                    $row3 = $Q3[0];
                    $permissions = $row3['permission'];
                    if (is_array($permissions)) {
                        foreach ($permissions as $key => $value) {
                            $this->permission[$key] = $value;
                        }
                    }
                } else {
                    $this->logout();
                    return false;
                }

                // $this->client_id
                $this->mongo_db->select(array('client_id'));
                $this->mongo_db->where('user_id', new MongoID($this->user_id));
                $this->mongo_db->where('status', true);
                $this->mongo_db->limit(1);
                $Q1 = $this->mongo_db->get('user_to_client');
                if (count($Q1) > 0) {
                    $row1 = $Q1[0];
                    $this->client_id = $row1['client_id'];
                } else {
                    $this->client_id = null;
                }

                if ($this->getAdminGroupID() || $this->client_id) {

                    // $this->site_id
                    $this->site_id = $this->fetchSiteId($this->client_id);
                    $this->mobile = $this->findMobileByClientId($this->client_id);

                    $this->set_site_mongodb($this->site_id);

                    $this->session->set_userdata('multi_login', $this->setMultiLoginKey($this->user_id));
                    $this->session->set_userdata('user_id', $this->user_id);
                    $this->session->set_userdata('username', $this->username);
                    $this->session->set_userdata('user_group_id', $this->user_group_id);
                    $this->session->set_userdata('database', $this->database);
                    $this->session->set_userdata('client_id', $this->client_id);
                    $this->session->set_userdata('site_id', $this->site_id);
                    $this->session->set_userdata('permission', $this->permission);
                    $this->session->set_userdata('ip', $ip);
                    $this->session->set_userdata('mobile', $this->mobile);
                } else {
                    $this->logout();
                    return false;
                }


            } else {
                $this->logout();
                return false;
            }

            return $this->user_id;
        }
    }

    public function set_last_app(){
        if($this->site_id){
            $this->mongo_db->set('last_app', new MongoId($this->site_id));
            $this->mongo_db->where('_id', new MongoId($this->user_id));
            $this->mongo_db->update('user');
        }
    }
    public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('client_id');
        $this->session->unset_userdata('site_id');
        $this->session->unset_userdata('user_group_id');
        $this->session->unset_userdata('database');
        $this->session->unset_userdata('permission');
        $this->session->unset_userdata('ip');
        $this->session->unset_userdata('mobile');
        $this->session->unset_userdata('admin_group_id');
        $this->session->unset_userdata('multi_login');

        $this->user_id = '';
        $this->username = '';
        $this->client_id = '';
        $this->site_id = '';
        $this->user_group_id = '';
        $this->database = '';
        $this->permission = '';
        $this->mobile = '';
        $this->admin_group_id = '';
    }

    public function force_login($user_id)
    {

        $this->set_site_mongodb(0);

        $this->mongo_db->select(array('_id', 'user_id', 'username', 'user_group_id', 'database', 'ip'));
        $this->mongo_db->where('_id', $user_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);
        $Q = $this->mongo_db->get('user');

        if (count($Q) > 0) {

            // update last_login
            $this->mongo_db->where('_id', $user_id);
            $this->mongo_db->set('last_login', date('Y-m-d H:i:s'));
            $this->mongo_db->update('user');

            // $this->user_id
            $row = $Q[0];
            $this->user_id = $row['_id'];
            $this->username = $row['username'];
            $this->user_group_id = $row['user_group_id'];
            $this->database = $row['database'];
            $ip = $row['ip'];

            // $this->client_id
            $this->mongo_db->select(array('client_id'));
            $this->mongo_db->where('user_id', new MongoID($this->user_id));
            $this->mongo_db->where('status', true);
            $this->mongo_db->limit(1);
            $Q1 = $this->mongo_db->get('user_to_client');
            if (count($Q1) > 0) {
                $row1 = $Q1[0];
                $this->client_id = $row1['client_id'];
            } else {
                $this->client_id = null;
            }

            // $this->permission
            if ($this->client_id) {
                $plan = $this->getPlan();
                $features = $plan['feature_to_plan'];
                $has_account = false;
                $has_app = false;
                foreach ($features as $feature) {
                    $this->mongo_db->select(array('link'));
                    $this->mongo_db->where('_id', $feature);
                    $this->mongo_db->where('status', true);
                    $this->mongo_db->limit(1);
                    $Q4 = $this->mongo_db->get('playbasis_feature');
                    $access[] = $Q4[0]['link'];
                    $modify[] = $Q4[0]['link'];
                    if ($Q4[0]['link'] == 'account') {
                        $has_account = true;
                    }
                    if ($Q4[0]['link'] == 'app') {
                        $has_app = true;
                    }
                }
                if (!$has_account) {
                    $access[] = 'account';
                    $modify[] = 'account';
                }
                if (!$has_app) {
                    $access[] = 'app';
                    $modify[] = 'app';
                }
                $this->permission['access'] = $access;
                $this->permission['modify'] = $modify;
            }

            if ($this->getAdminGroupID() || $this->client_id) {

                // $this->site_id
                $this->site_id = $this->fetchSiteId($this->client_id);
                $mobile = $this->findMobileByClientId($this->client_id);

                $this->set_site_mongodb($this->site_id);

                $this->session->set_userdata('multi_login', $this->setMultiLoginKey($this->user_id));
                $this->session->set_userdata('user_id', $this->user_id);
                $this->session->set_userdata('username', $this->username);
                $this->session->set_userdata('user_group_id', $this->user_group_id);
                $this->session->set_userdata('database', $this->database);
                $this->session->set_userdata('client_id', $this->client_id);
                $this->session->set_userdata('site_id', $this->site_id);
                $this->session->set_userdata('permission', $this->permission);
                $this->session->set_userdata('ip', $ip);
                $this->session->set_userdata('mobile', $mobile);

                return true;
            }
        }
        return false;
    }

    public function hasPermission($key, $value)
    {
        if (isset($this->permission[$key])) {
            return in_array($value, $this->permission[$key]);
        } else {
            return false;
        }
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function isLogged()
    {
        return $this->user_id;
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getSiteId()
    {
        return $this->site_id;
    }

    public function getUserGroupId()
    {
        return $this->user_group_id;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getClientDatabase()
    {
        return isset($this->database) ? $this->database : "core";
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getAdminGroupID()
    {
        $this->set_site_mongodb($this->site_id);

        if ($this->session->userdata('admin_group_id')) {
            return $this->session->userdata('admin_group_id');
        }

        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('name', 'Top Administrator');
        $this->mongo_db->limit(1);
        $Q = $this->mongo_db->get('user_group');

        $this->admin_group_id = null;
        if ($Q) {
            $this->admin_group_id = $Q[0]['_id'];
        }

        $this->session->set_userdata('admin_group_id', $this->admin_group_id);

        return $this->admin_group_id;
    }

    public function updateSiteId($site_id)
    {
        $this->set_site_mongodb($this->site_id);

        if ($this->checkSiteId($site_id) > 0) {
            $this->site_id = new MongoId($site_id);
            $this->session->set_userdata('site_id', $this->site_id);
        }

        return true;
    }

    public function fetchSiteId($client_id)
    {
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);
        $Q2 = $this->mongo_db->get('playbasis_client_site');

        if (count($Q2) > 0) {
            $row2 = $Q2[0];
            $site_id = $row2['_id'];
        } else {
            $site_id = null;
        }

        return $site_id;
    }

    private function checkSiteId($site_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($site_id));
        $this->mongo_db->where('client_id', new MongoID($this->client_id));
        return $this->mongo_db->count('playbasis_client_site');
    }

    public function checkRandomKey($random_key, $clear = true)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('random_key', $random_key);
        $user = $this->mongo_db->get('user');

        if ($user) {
            if ($clear) {
                $this->mongo_db->where('_id', new MongoID($user[0]['_id']));
                $this->mongo_db->set('status', true);
                $this->mongo_db->set('random_key', null);
                $this->mongo_db->update('user');
            }
            return $user;
        } else {
            return false;
        }
    }

    public function findEmail($data)
    {
        $this->set_site_mongodb($this->site_id);

        $email = $data['email'];

        $this->mongo_db->where('email', $email);
        $this->mongo_db->where('status', true);
        $user = $this->mongo_db->get('user');

        if ($user) {
            return $user;
        } else {
            return false;
        }

    }

    public function insertRandomPasswordKey($random_key, $user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('password_key', $random_key);

        $this->mongo_db->update('user');

    }

    public function checkRandomPasswordKey($random_key)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('password_key', $random_key);
        $user = $this->mongo_db->get('user');

        if ($user) {
            $this->mongo_db->where('_id', new MongoID($user[0]['_id']));
            $this->mongo_db->set('password_key', null);
            $this->mongo_db->update('user');
            return $user;
        } else {
            return false;
        }
    }

    public function insertNewPassword($user_id, $new_password)
    {
        $this->set_site_mongodb($this->site_id);

        $find_salt = $this->getUserInfo($user_id);
        $salt = $find_salt['salt'];
        $password = dohash($new_password, $salt);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('password', $password);
        return $this->mongo_db->update('user');

    }

    public function disableUser($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('user');
    }

    public function getMultiLogin($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $key = $this->mongo_db->get('user');

        return $key ? $key[0]['multi_login'] : null;
    }

    public function setMultiLoginKey($user_id)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', new MongoID($user_id));
        $hashed = do_hash(get_random_password() . $user_id);
        $this->mongo_db->set('multi_login', $hashed);
        $this->mongo_db->update('user');
        return $hashed;
    }

    public function get_api_key_secret($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('client_id', new MongoID($client_id));
            $this->mongo_db->where('site_id', new MongoID($site_id));
            $this->mongo_db->limit(1);
        } catch (Exception $e) {
        }

        $result = $this->mongo_db->get("playbasis_platform_client_site");

        return $result ? $result[0] : null;
    }

    public function isAdmin()
    {
        return $this->getAdminGroupID() == $this->getUserGroupId();
    }

    public function getPlan()
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('client_id', new MongoID($this->client_id));
        $this->mongo_db->order_by(array('date_modified' => -1)); // ensure we use only latest record, assumed to be the current chosen plan
        $this->mongo_db->limit(1);
        $permission = $this->mongo_db->get('playbasis_permission');

        $permission = $permission ? $permission[0] : array();

        $plan = array();
        if ($permission) {
            $this->mongo_db->where('_id', $permission['plan_id']);
            $plan = $this->mongo_db->get('playbasis_plan');
        }

        $plan = $plan ? $plan[0] : array();

        return $plan;
    }

    public function findMobileByClientId($client_id)
    {
        $this->set_site_mongodb($this->site_id);
        $this->mongo_db->select(array('mobile'));
        $this->mongo_db->where('_id', $client_id);
        $this->mongo_db->where('mobile', array('$regex' => new MongoRegex("/^\+[0-9]+/")));
        $results = $this->mongo_db->get('playbasis_client');
        return $results ? $results[0]['mobile'] : null;
    }

    public function updateMobile($client_id, $mobile)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('_id', $client_id);
        $this->mongo_db->set('mobile', $mobile);
        $this->mongo_db->update('playbasis_client');

        if ($this->client_id == $client_id) {
            $this->mobile = $mobile;
            $this->session->set_userdata('mobile', $this->mobile);
        }
    }

    public function usedMobile($mobile)
    {
        $this->set_site_mongodb($this->site_id);

        $this->mongo_db->where('mobile', $mobile);
        return $this->mongo_db->count('playbasis_client');
    }

    public function increaseLoginAttempt( $user_id)
    {
        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->inc('login_attempt', 1);
        $this->mongo_db->update("user");
    }

    public function resetLoginAttempt( $user_id)
    {
        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('login_attempt', 0);
        $this->mongo_db->update("user");
    }

    public function lockUser( $user_id)
    {
        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('locked', true);
        $this->mongo_db->update("user");
    }

    public function unlockPlayer( $user_id)
    {
        $this->mongo_db->where('_id', new MongoID($user_id));
        $this->mongo_db->set('locked', false);
        $this->mongo_db->set('login_attempt', 0);
        $this->mongo_db->update("user");
    }
}

?>
