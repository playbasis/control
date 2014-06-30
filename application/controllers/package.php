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
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }
        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("package", $lang['folder']);
    }

    public function index(){
    	$client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

    	$currentPlan = $this->Package_model->getCurrentPlan($client_id, $site_id)[0];

    	$currentLimitPlayers = $this->Package_model->getLimitPlayers($client_id, $site_id)[0];


    	$this->data['currentPlan'] = $currentPlan;
    	$this->data['currentLimitPlayers'] = $currentLimitPlayers;
    	$this->data['main'] = 'package';
    	$this->load->vars($this->data);
    	$this->render_page('template');

    }


}