<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Sms extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $setting_group_id = $this->User_model->getAdminGroupID();
        if($this->User_model->getUserGroupId() != $setting_group_id){
            $user_plan = $this->User_model->getPlan();

            if(!is_null($user_plan['limit_notifications']['sms'])){
                redirect('/', 'refresh');
            }
        }

        $this->load->model('Sms_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("sms", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index() {
        $this->setup();
    }

    public function setup() {


        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->form_validation->set_rules('sms-mode', $this->lang->line('sms-mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-account_sid', $this->lang->line('sms-account_sid'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-auth_token', $this->lang->line('sms-auth_token'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-number', $this->lang->line('sms-number'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sms-name', $this->lang->line('sms-name'), 'trim|required|xss_clean');

        $setting_group_id = $this->User_model->getAdminGroupID();
        if($this->User_model->getUserGroupId() != $setting_group_id){
            $this->data['sms'] = $this->Sms_model->getSMSClient($this->User_model->getClientId());
        }else{
            $this->data['sms'] = $this->Sms_model->getSMSMaster() ;
        }

        if($this->input->post()){
            if($this->form_validation->run()){

                $sms_data = $this->input->post();
                if($this->User_model->getClientId()){

                    $sms_data['client_id'] = $this->User_model->getClientId();
                    $sms_data['site_id'] = $this->User_model->getSiteId();
                    $this->Sms_model->updateSMS($sms_data);

                }else{
                    $this->Sms_model->updateSMS($sms_data);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
            }
        }

        $this->data['main'] = 'sms';
        $this->load->vars($this->data);
        $this->render_page('template');
    }
}
?>