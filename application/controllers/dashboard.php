<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("home", $lang['folder']);
    }

    public function index(){

        $this->load->model('Statistic_model');

        $lang = get_lang($this->session, $this->config);

        $this->data['sample_start_date'] = date("m/d/Y", strtotime("-30days"));
        $this->data['sample_end_date'] = date("m/d/Y", strtotime("today"));

        $this->data['lang'] = $lang['folder'];
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['main'] = 'dashboard';
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->load->vars($this->data);
        $this->load->view('template');
    }



}