<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Statistic extends CI_Controller
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
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function getStatisticData(){
        $this->load->model('User_model');
        $this->load->model('Statistic_model');

        if ($this->input->get('date_start')) {
            $date_start = strtotime($this->input->get('date_start'));
        } else {
            $date_start = strtotime(' -30 day');
        }

        if ($this->input->get('date_expire')) {
            $date_expire = strtotime($this->input->get('date_expire'));
        } else {
            $date_expire = strtotime('today');
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'month' => date('n'),
            'year' => date('Y'),
            'date_start' => $date_start,
            'date_expire' => $date_expire
        );

        $json = array();

        $register = $this->Statistic_model->getNewRegister($data);
        foreach ($register as $key => $value) {
            $json['register'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $rewards = $this->Statistic_model->getPlayerRewardPointStat($data);
        foreach ($rewards as $key => $value) {
            $json['points'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $badges = $this->Statistic_model->getPlayerRewardBadgeStat($data);
        foreach ($badges as $key => $value) {
            $json['badges'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $levelup = $this->Statistic_model->getPlayerRewardLevelStat($data);
        foreach ($levelup as $key => $value) {
            $json['levelup'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $this->output->set_output(json_encode($json));
    }

    public function getDailyActionmeaturement(){
        $this->load->model('User_model');
        $this->load->model('Statistic_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getWeeklyActionmeaturement(){
        $this->load->model('User_model');
        $this->load->model('Statistic_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getWeeklyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getMonthlyActionmeaturement(){
        $this->load->model('User_model');
        $this->load->model('Statistic_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }
}