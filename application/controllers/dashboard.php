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

        $this->data['weekly_events'] = null;//$this->Statistic_model->getWeeklyActionmeaturement($data);

        $this->data['monthly_events'] = null;//$this->Statistic_model->getMonthlyActionmeaturement($data);

        $this->data['daily_events'] = null;//$this->Statistic_model->getDailyActionmeaturement($data);

        $leaderboards = $this->Statistic_model->LeaderBoard($data);

        $this->data['leaderboards'] = array();

        if ($leaderboards) {
            foreach ($leaderboards as $leaderboard) {
                $this->data['leaderboards'][] = array(
                    'player_id' => $leaderboard['player_id'],
                    'name' => $leaderboard['player_name'],
                    'image' => $leaderboard['image'],
                    'exp' => $leaderboard['exp'],
                    'level' => $leaderboard['level'],
                    'point' => $leaderboard['point'],
                    'date_added' => date('M d, Y H:i', strtotime($leaderboard['date_added']))
                );
            }
        }

        $data['sort'] = 'date_added';
        $data['order'] = 'DESC';

        $results = $this->Player_model->getPlayers($data);

        foreach ($results as $player) {
            $action = array();

            $this->data['players'][] = array(
                'pb_player_id'   => $player['pb_player_id'],
                'username' => $player['username'],
                'first_name'   => $player['first_name'],
                'last_name' => $player['last_name'],
                'nickname' => $player['nickname'],
                'image'     => $player['image'],
                'status'     => $player['status'],
                'date_added' => date($this->lang->line('date_format_short'), strtotime($player['date_added'])),
                'exp'      => $player['exp'],
                'action'     => $action
            );
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
}