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

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("home", $lang['folder']);
    }

    public function index(){

        if(!$this->validateAccess()){
            echo $this->lang->line('error_access');
            return false;
        }

        $this->load->model('User_model');
        $this->load->model('Domain_model');
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
//
//        $this->data['monthly_events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);
//
//        $this->data['daily_events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $this->data['leaderboards'] = array();

        if($client_id){
            $leaderboards = $this->Statistic_model->LeaderBoard($data);

            if ($leaderboards) {
                foreach ($leaderboards as $leaderboard) {

                    $info = $this->Player_model->getPlayerById($leaderboard['pb_player_id']);

                    $this->data['leaderboards'][] = array(
                        'player_id' => $leaderboard['pb_player_id'],
                        'name' => $info['first_name'] .' '. $info['last_name'],
                        'image' => $info['image'],
                        'exp' => $info['exp'],
                        'email' => $info['email'],
                        'level' => $info['level'],
                        'point' => $leaderboard['value'],
                        'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($info['date_added']))),
                        'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($info['date_modified'])))
                    );
                }
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
                    'action'     => $action
                );
            }
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'dashboard';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('dashboard');
    }

    public function home(){

        $this->load->model('User_model');
        $this->load->model('Domain_model');
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
//
//        $this->data['monthly_events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);
//
//        $this->data['daily_events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $this->data['leaderboards'] = array();

        if($client_id){
            $leaderboards = $this->Statistic_model->LeaderBoard($data);

            if ($leaderboards) {
                foreach ($leaderboards as $leaderboard) {

                    $info = $this->Player_model->getPlayerById($leaderboard['pb_player_id']);

                    $this->data['leaderboards'][] = array(
                        'player_id' => $leaderboard['pb_player_id'],
                        'name' => $info['first_name'] .' '. $info['last_name'],
                        'image' => $info['image'],
                        'exp' => $info['exp'],
                        'email' => $info['email'],
                        'level' => $info['level'],
                        'point' => $leaderboard['value'],
                        'date_added' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($info['date_added']))),
                        'last_active' => date($this->lang->line('date_format_short'), strtotime($this->datetimeMongotoReadable($info['date_modified'])))
                    );
                }
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
                    'point' => $player['points'],
                    'action'     => $action
                );
            }
        }

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
        if ($this->User_model->hasPermission('access', 'dashboard')) {
            return true;
        } else {
            return false;
        }
    }
}