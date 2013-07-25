<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class _main extends CI_Controller
{

    function header($meta = null)
    {
        $this->load->view('_header');
    }

    function footer()
    {
        $this->load->model('User_model');
        $this->load->model('Domain_model');

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();
        $this->data['domain'] = $this->Domain_model->getDomain($this->data['site_id']);
        $this->load->vars($this->data);
        $this->load->view('_footer');
    }

}