<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        if($this->input->get('site_id')){
            $this->User_model->updateSiteId($this->input->get('site_id'));
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("home", $lang['folder']);
    }

    public function index(){

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->load->model('User_model');
        $this->load->model('Statistic_model');
        $this->load->model('Player_model');

        $this->data['sample_start_date'] = date("m/d/Y", strtotime("-30days"));
        $this->data['sample_end_date'] = date("m/d/Y", strtotime("today"));

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

//        $this->data['weekly_events'] = $this->Statistic_model->getWeeklyActionmeaturement($data);
//        $this->data['monthly_events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);
//        $this->data['daily_events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $cached_player_point = array();

        $this->data['leaderboards'] = array();
        if($client_id){
            $players = $this->Statistic_model->getLeaderboardByExp($data['limit'], $data['client_id'], $data['site_id']);
            if ($players) foreach ($players as $player) {
                $this->data['leaderboards'][] = array(
                    'player_id' => $player['_id'],
                    'name' => $player['first_name'] .' '. $player['last_name'],
                    'image' => $player['image'],
                    'exp' => $player['exp'],
                    'email' => $player['email'],
                    'level' => $player['level'],
                    'point' => $this->get_player_point_cache($client_id, $site_id, $player['_id'], $cached_player_point),
                    'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_added']))),
                    'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_modified'])))
                );
            }
        }

        $data['sort'] = 'date_added';
        $data['order'] = 'DESC';

        if($client_id){
            $results = $this->Player_model->getPlayers($data);

            foreach ($results as $player) {
                $action = array();

                $this->data['players'][] = array(
                    'pb_player_id'   => $player['_id'],
                    'username' => $player['username'],
                    'first_name'   => $player['first_name'],
                    'last_name' => $player['last_name'],
                    'nickname' => $player['nickname'],
                    'image'     => $player['image'],
                    'status'     => $player['status'],
                    'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_added']))),
                    'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_modified']))),
                    'exp'      => $player['exp'],
                    'email' => $player['email'],
                    'level' => $player['level'],
                    'point' => $this->get_player_point_cache($client_id, $site_id, $player['_id'], $cached_player_point),
                    'action'     => $action
                );
            }
        }

        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
        $this->data['api_key'] = $result['api_key'];

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'dashboard';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function home(){

        /* check to see if 'dashboard' menu is enabled for non-superadmin users */
        if ($this->User_model->getUserGroupId() != $this->User_model->getAdminGroupID()) {
            $this->load->model('Feature_model');
            $user_plan = $this->User_model->getPlan();
            if (!$user_plan) redirect('/logout', 'refresh');
            if ($user_plan['_id'] != FREE_PLAN || $this->User_model->getMobile()) {
                if ($this->User_model->getSiteId()) {
                    $features = $this->Feature_model->getFeatureBySiteId($this->User_model->getClientId(), $this->User_model->getSiteId());
                    $is_default_enabled = $this->is_default_enabled($features);
                    if (!$is_default_enabled) {
                        $second_default = $this->find_second_default($features);
                        if ($second_default){
                            redirect('/'.$second_default, 'refresh');
                        }else{
                            redirect('/block', 'refresh');
                        }
                    }
                } else {
                    $user_plan = $this->User_model->getPlan();
                    if (!empty($user_plan)) {
                        if (array_key_exists('feature_to_plan', $user_plan)) {
                            if (is_array($user_plan['feature_to_plan']) && count($user_plan['feature_to_plan']) > 0) {
//                            $value = $this->Feature_model->getFeatureName("Account");
//                            if($value) redirect('/'.$value['link'], 'refresh'); /* if it isn't, then we select second menu for the user */
                                redirect('account/first_app', 'refresh');
                            }
                        }
                    }
                }
            } else {
                /* if you are free accounts, then you have to set up your mobile number */
                redirect('account/setup_mobile', 'refresh');
            }
        }

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
            die();
        }

        $this->load->model('User_model');
        $this->load->model('Statistic_model');
        $this->load->model('Player_model');

        $this->data['sample_start_date'] = date("m/d/Y", strtotime("-30days"));
        $this->data['sample_end_date'] = date("m/d/Y", strtotime("today"));

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

//        $this->data['weekly_events'] = $this->Statistic_model->getWeeklyActionmeaturement($data);
//        $this->data['monthly_events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);
//        $this->data['daily_events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $cached_player_point = array();

        $this->data['leaderboards'] = array();
        if($client_id){
            $this->Statistic_model->set_read_preference_secondary();
            $players = $this->Statistic_model->getLeaderboardByExp($data['limit'], $data['client_id'], $data['site_id']);
            $this->Statistic_model->set_read_preference_primary();
            if ($players) foreach ($players as $player) {
                $this->data['leaderboards'][] = array(
                    'player_id' => $player['_id'],
                    'name' => $player['first_name'] .' '. $player['last_name'],
                    'image' => $player['image'],
                    'exp' => $player['exp'],
                    'email' => $player['email'],
                    'level' => $player['level'],
                    'point' => $this->get_player_point_cache($client_id, $site_id, $player['_id'], $cached_player_point),
                    'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_added']))),
                    'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_modified'])))
                );
            }
        }

        $data['sort'] = 'date_added';
        $data['order'] = 'DESC';

        if($client_id){
            $results = $this->Player_model->getPlayers($data);

            foreach ($results as $player) {
                $action = array();

                $this->data['players'][] = array(
                    'pb_player_id'   => $player['_id'],
                    'username' => $player['username'],
                    'first_name'   => $player['first_name'],
                    'last_name' => $player['last_name'],
                    'nickname' => $player['nickname'],
                    'image'     => $player['image'],
                    'status'     => $player['status'],
                    'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_added']))),
                    'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($player['date_modified']))),
                    'exp'      => $player['exp'],
                    'email' => $player['email'],
                    'level' => $player['level'],
                    'point' => $this->get_player_point_cache($client_id, $site_id, $player['_id'], $cached_player_point),
                    'action'     => $action
                );
            }
        }

        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(), $this->User_model->getSiteId());
        $this->data['api_key'] = $result['api_key'];

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'dashboard';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function liveFeed() {
        $this->load->view('live_feed');
    }

    private function get_player_point_cache($client_id, $site_id, $pb_player_id, $cache) {
        $key = $pb_player_id->{'$id'};
        if (!array_key_exists($key, $cache)) {
            $point = $this->Player_model->getPlayerPoint(array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'pb_player_id' => $pb_player_id
            ));
            $cache[$key] = $point;
        }
        return $cache[$key];
    }

    private function is_default_enabled($features) {
        if (!$features) return false;
        if (is_array($features)) foreach ($features as $feature) {
            if (array_key_exists('link', $feature) && $feature['link'] == '/') return true;
        }
        return false;
    }

    private function find_second_default($features) {
        if (!$features) return null;
        if (is_array($features)) foreach ($features as $feature) {
            if (array_key_exists('link', $feature) && $feature['link'] != '/' &&  $this->User_model->hasPermission('access', $feature['link'])) return $feature['link'];
        }
        return null;
    }

    private function getAge($birthdate) {
        $now = new DateTime();
        $birthdate = $this->datetimeMongotoReadable($birthdate);
        $oDateBirth = new DateTime($birthdate);
        $oDateInterval = $now->diff($oDateBirth);

        return $oDateInterval->y;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }

        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', '/') &&  $this->Feature_model->getFeatureExistByClientId($client_id, '/')) {
            return true;
        } else {
            return false;
        }
    }
}