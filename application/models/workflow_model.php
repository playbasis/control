<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workflow_model extends MY_Model
{
    public function getTotalPlayerByApprovalStatus($client_id, $site_id, $approval_status, $data)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);

        $this->mongo_db->where('approve_status', $approval_status);

        if (isset($data['filter_name']) && $data['filter_name']) {
            $filter = array();
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            //$this->mongo_db->where('name', $regex);
            $filter[]=array('first_name' => $regex);
            $filter[]=array('last_name' => $regex);
            $this->mongo_db->where(array('$or' => $filter));
        }

        if (isset($data['filter_id']) && $data['filter_id']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_id'])) . "/i");
            $this->mongo_db->where('cl_player_id', $regex);
            //$or_where[]=array('cl_player_id' => $regex);
        }

        if (isset($data['filter_email']) && $data['filter_email']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_email'])) . "/i");
            $this->mongo_db->where('email', $regex);
            //$or_where[]=array('email' => $regex);
        }

        if (isset($data['filter_tag']) && $data['filter_tag']) {
            $this->mongo_db->where_in('tags', $data['filter_tag']);
        }

        $results = $this->mongo_db->count("playbasis_player");
        return $results;
    }

    public function getPlayerByApprovalStatus($client_id, $site_id, $approval_status, $data)
    {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);

        $this->mongo_db->where('approve_status', $approval_status);

        if (isset($data['filter_name']) && $data['filter_name']) {
            $filter = array();
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            //$this->mongo_db->where('name', $regex);
            $filter[]=array('first_name' => $regex);
            $filter[]=array('last_name' => $regex);
            $this->mongo_db->where(array('$or' => $filter));
        }

        if (isset($data['filter_id']) && $data['filter_id']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_id'])) . "/i");
            $this->mongo_db->where('cl_player_id', $regex);
            //$or_where[]=array('cl_player_id' => $regex);
        }

        if (isset($data['filter_email']) && $data['filter_email']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_email'])) . "/i");
            $this->mongo_db->where('email', $regex);
            //$or_where[]=array('email' => $regex);
        }

        if (isset($data['filter_tag']) && $data['filter_tag']) {
            $this->mongo_db->where_in('tags', $data['filter_tag']);
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

        return $this->mongo_db->get('playbasis_player');
    }

    public function getTotalPendingPlayer($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('approve_status', 'pending');

        $results = $this->mongo_db->count("playbasis_player");
        return $results;
    }


    public function getOrganizationToPlayer($client_id, $site_id, $player_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where(array(
            'pb_player_id' => $player_id,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));
        $results = $this->mongo_db->get("playbasis_store_organize_to_player");
        return $results;
    }

    public function getRole($client_id, $site_id, $player_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('roles'));

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('pb_player_id', new MongoId($player_id));
        $this->mongo_db->where('node_id', new MongoId($node_id));

        $results = $this->mongo_db->get("playbasis_store_organize_to_player");
        return $results;
    }

    public function editOrganizationOfPlayer($client_id, $site_id, $org_id, $user_id, $node_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', new MongoID($org_id));

        $this->mongo_db->set('pb_player_id', new MongoID($user_id));
        $this->mongo_db->set('node_id', new MongoID($node_id));
        return $this->mongo_db->update('playbasis_store_organize_to_player');
    }

    public function createPlayer($client_id, $site_id, $data)
    {
        $status = $this->_api->register($data['cl_player_id'], $data['username'], $data['email'], $data);

        if (isset($status->success) && $status->success && $data && isset($data['approve_status']) && $data['approve_status'] == 'approved') {
            $player = $this->findPlayerByClPlayerId($client_id, $site_id, $data['cl_player_id']);
            if ($player) {
                /* system automatically send an email to notify the player that account is approved */
                $site_data = $this->findClientSite($client_id, $site_id);
                $this->load->library('parser');
                $vars = array(
                    'sitename' => $site_data['site_name'],
                    'site_logo' => isset($site_data['image']) && !empty($site_data['image']) ? $site_data['image'] : "image/beforelogin/email-header-top.gif",
                    'site_color' => ((isset($site_data['app_color']) && !empty($site_data['app_color'])) &&
                                     (isset($site_data['image']) && !empty($site_data['image']))) ? $site_data['app_color'] : "#86559c",
                    'firstname' => $player['first_name'],
                    'lastname' => $player['last_name'],
                    'username' => $player['username'],
                    'password' => $data['password'],
                    'base_url' => site_url()
                );
                if (!$data['password']) { // if password is input, we have a chance to send email with password
                    $random_key = $this->generatePasswordResetCode($player['_id']);
                    $vars = array_merge($vars, array(
                        'key' => $random_key,
                        'url' => site_url('player/password/reset/'),
                    ));
                }
                $htmlMessage = $this->parser->parse($data['password'] ? 'emails/player_activatedwithpassword.html' : 'emails/player_activated.html', $vars, true);
                $result = $this->_api->emailPlayer($data['cl_player_id'], 'Your Account is Activated', $htmlMessage);
            }
        }

        return $status;
    }

    public function addPlayerToNode($player_id, $node_id)
    {
        $status = $this->_api->addPlayerToNode($player_id, $node_id);
        return $status;
    }

    public function setPlayerRole($player_id, $node_id, $role)
    {
        $status = $this->_api->setPlayerRole($player_id, $node_id, array('role' => $role));
        return $status;
    }

    public function editPlayer($client_id, $site_id, $player_id, $data)
    {
        if ($data && isset($data['approve_status']) && $data['approve_status'] == 'approved') {
            $player = $this->findPlayerByClPlayerId($client_id, $site_id, $player_id);
            if ($player && (!isset($player['approve_status']) || $player['approve_status'] != 'approved')) { // detect approve_status changed
                /* system automatically send an email to notify the player that account is approved */
                $site_data = $this->findClientSite($client_id, $site_id);
                $random_key = $this->generatePasswordResetCode($player['_id']);
                $this->load->library('parser');
                $vars = array(
                    'sitename' => $site_data['site_name'],
                    'site_logo' => isset($site_data['image']) && !empty($site_data['image']) ? $site_data['image'] : "image/beforelogin/email-header-top.gif",
                    'site_color' => ((isset($site_data['app_color']) && !empty($site_data['app_color'])) &&
                                     (isset($site_data['image']) && !empty($site_data['image']))) ? $site_data['app_color'] : "#86559c",
                    'firstname' => $player['first_name'],
                    'lastname' => $player['last_name'],
                    'username' => $player['username'],
                    'key' => $random_key,
                    'url' => site_url('player/password/reset/'),
                    'base_url' => site_url()
                );
                $htmlMessage = $this->parser->parse('emails/player_activated.html', $vars, true);
                $result = $this->_api->emailPlayer($player_id, 'Your Account is Activated', $htmlMessage);
            }
        }

        if ( isset($data['tags']) && $data['tags'] == "") {
            $data['tags'] = "null";
        }

        $status = $this->_api->updatePlayer($player_id, $data);
        return $status;
    }

    public function clearPlayerRole($player_id, $node_id, $role)
    {
        $status = $this->_api->unsetPlayerRole($player_id, $node_id, array('role' => $role));
        return $status;
    }

    public function approvePlayer($client_id, $site_id, $user_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approve_status', "approved");
        $this->mongo_db->set('date_approved', new MongoDate());
        $ret = $this->mongo_db->update('playbasis_player');

        /* system automatically send an email to notify the player that account is approved */
        $player = $this->findPlayerById($client_id, $site_id, new MongoID($user_id));
        if ($player) {
            $site_data = $this->findClientSite($client_id, $site_id);
            $random_key = $this->generatePasswordResetCode($player['_id']);
            $this->load->library('parser');
            $vars = array(
                'sitename' => $site_data['site_name'],
                'site_logo' => isset($site_data['image']) && !empty($site_data['image']) ? $site_data['image'] : "image/beforelogin/email-header-top.gif",
                'site_color' => ((isset($site_data['app_color']) && !empty($site_data['app_color'])) &&
                                 (isset($site_data['image']) && !empty($site_data['image']))) ? $site_data['app_color'] : "#86559c",
                'firstname' => $player['first_name'],
                'lastname' => $player['last_name'],
                'username' => $player['username'],
                'key' => $random_key,
                'url' => site_url('player/password/reset/'),
                'base_url' => site_url()
            );
            $htmlMessage = $this->parser->parse('emails/player_activated.html', $vars, true);
            $result = $this->_api->emailPlayer($player['cl_player_id'], 'Your Account is Activated', $htmlMessage);
        }

        return $ret;
    }

    public function rejectPlayer($client_id, $site_id, $user_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('approve_status', "rejected");
        $this->mongo_db->set('date_approved', new MongoDate());
        return $this->mongo_db->update('playbasis_player');
    }

    public function deletePlayer($player_id)
    {
        $status = $this->_api->deletePlayer($player_id);
        return $status;
    }

    public function unlockPlayer($client_id, $site_id, $user_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('_id', new MongoID($user_id));

        $this->mongo_db->set('locked', false);
        $this->mongo_db->set('login_attempt', 0);
        return $this->mongo_db->update('playbasis_player');
    }

    public function getTotalLockedPlayer($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where(array(
            'locked' => true,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));

        $results = $this->mongo_db->count("playbasis_player");

        return $results;
    }

    public function getTotalLockedPlayerWithFilter($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where(array(
            'locked' => true,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));

        $filter = array();

        if (isset($data['filter_name']) && $data['filter_name']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            //$this->mongo_db->where('name', $regex);
            $filter[]=array('first_name' => $regex);
            $filter[]=array('last_name' => $regex);
        }

        if($filter)
            $this->mongo_db->where(array('$or' => $filter));

        if (isset($data['filter_id']) && $data['filter_id']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_id'])) . "/i");
            $this->mongo_db->where('cl_player_id', $regex);
            //$or_where[]=array('cl_player_id' => $regex);
        }

        if (isset($data['filter_email']) && $data['filter_email']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_email'])) . "/i");
            $this->mongo_db->where('email', $regex);
            //$or_where[]=array('email' => $regex);
        }

        if (isset($data['filter_tag']) && $data['filter_tag']) {
            $this->mongo_db->where_in('tags', $data['filter_tag']);
        }

        $results = $this->mongo_db->count("playbasis_player");

        return $results;
    }

    public function getLockedPlayer($client_id, $site_id, $data)
    {
        $this->set_site_mongodb($site_id);
        //$this->mongo_db->select(array('email','first_name','last_name','username','image','exp','level','date_added','date_modified'));
        $this->mongo_db->where(array(
            'locked' => true,
            'site_id' => $site_id,
            'client_id' => $client_id
        ));

        $filter = array();

        if (isset($data['filter_name']) && $data['filter_name']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            //$this->mongo_db->where('name', $regex);
            $filter[]=array('first_name' => $regex);
            $filter[]=array('last_name' => $regex);
        }

        if($filter)
            $this->mongo_db->where(array('$or' => $filter));

        if (isset($data['filter_id']) && $data['filter_id']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_id'])) . "/i");
            $this->mongo_db->where('cl_player_id', $regex);
            //$or_where[]=array('cl_player_id' => $regex);
        }

        if (isset($data['filter_email']) && $data['filter_email']) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_email'])) . "/i");
            $this->mongo_db->where('email', $regex);
            //$or_where[]=array('email' => $regex);
        }

        if (isset($data['filter_tag']) && $data['filter_tag']) {
            $this->mongo_db->where_in('tags', $data['filter_tag']);
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

        return $this->mongo_db->get('playbasis_player');
    }

    private function findClientSite($client_id, $site_id) {
        $this->mongo_db->where(array(
            '_id' => $site_id,
            'client_id' => $client_id
        ));
        $results = $this->mongo_db->get("playbasis_client_site");
        return $results && isset($results[0]) ? $results[0] : null;
    }

    private function findPlayerById($client_id, $site_id, $pb_player_id) {
        $this->mongo_db->where(array(
            '_id' => $pb_player_id
        ));
        $results = $this->mongo_db->get("playbasis_player");
        return $results && isset($results[0]) ? $results[0] : null;
    }

    private function findPlayerByClPlayerId($client_id, $site_id, $cl_player_id) {
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'cl_player_id' => $cl_player_id
        ));
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_player");
        return $results && isset($results[0]) ? $results[0] : null;
    }

    private function existsPasswordResetCode($code)
    {
        $this->mongo_db->where('code', $code);
        $this->mongo_db->limit(1);
        return $this->mongo_db->count('playbasis_player_password_reset') > 0;
    }

    private function generatePasswordResetCode($pb_player_id)
    {
        $code = null;
        for ($i = 0; $i < 2; $i++) {
            $code = get_random_code(8, false, true, true);
            if (!$this->existsPasswordResetCode($code)) {
                break;
            }
        }
        if (!$code) {
            throw new Exception('Cannot generate unique player code');
        }

        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $records = $this->mongo_db->get('playbasis_player_password_reset');
        if (!$records) {
            $this->mongo_db->insert('playbasis_player_password_reset', array(
                'pb_player_id' => $pb_player_id,
                'code' => $code,
                'date_expire' => new MongoDate(strtotime("+1 day")),
            ));
        } else {
            $this->mongo_db->where('pb_player_id', $pb_player_id);
            $this->mongo_db->set('code', $code);
            $this->mongo_db->set('date_expire', new MongoDate(strtotime("+1 day")));
            $this->mongo_db->update('playbasis_player_password_reset');
        }
        return $code;
    }
}