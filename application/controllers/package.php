<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Package extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Package_model');
        $this->load->model('Reward_model');
        $this->load->model('Player_model');
        $this->load->model('Client_model');
        $this->load->model('Plan_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("package", $lang['folder']);
    }

    public function index()
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
        $currentPlan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
        $currentLimitPlayers = $this->Plan_model->getPlanLimitById($plan_subscription["plan_id"], "others", "player");
        $num_users = $this->Player_model->getTotalPlayers($site_id, $client_id);

        $rewards = array();
        foreach ($currentPlan['reward_to_plan'] as $i => $reward) {
            $theReward = $this->Reward_model->getReward($reward['reward_id']);
            $rewards[$i]['name'] = $theReward['name'];
            $rewards[$i]['limit'] = $reward['limit'];
        }

        $this->data['num_users'] = $num_users;
        $this->data['currentPlan'] = $currentPlan;
        $this->data['rewards'] = $rewards;
        $this->data['currentLimitPlayers'] = $currentLimitPlayers;
        $this->data['main'] = 'package';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function plans()
    {
        $allPlans = $this->Package_model->getAllPlans();

        $this->data['allPlans'] = $allPlans;

        $this->data['main'] = 'plans';
        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function billings()
    {
        $this->data['main'] = 'package_billing';
        $this->load->vars($this->data);
        $this->render_page('template');
    }
}