<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Widget extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('App_model');
        $this->load->model('Custompoints_model');
        $this->load->model('Widget_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("widget", $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function index()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['platform_data'] = array();
        $site_data = array();
        $points_data = array();
        $plan_widget = array();

        if ($client_id) {
            $site_data = $this->App_model->getAppsBySiteId($site_id);
            $platform_data = $this->App_model->getPlatformWithType($site_id, 'web');

            //force use first web app platform
            $this->data['platform_data'] = $platform_data;

            $points_data = $this->Custompoints_model->getCustompoints(array(
                'client_id' => $client_id,
                'site_id' => $site_id
            ));

            $this->load->model('Client_model');
            $this->load->model('Plan_model');
            $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
            // get Plan display
            $plan_widget = $this->Plan_model->getPlanDisplayWidget($plan_subscription["plan_id"]);
        }

        $this->data['plan_widget'] = $plan_widget;
        $this->data['site_data'] = $site_data;
        $this->data['points_data'] = $points_data;
        $this->data['main'] = 'widget';
        $this->render_page('template');
    }

    public function preview()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $site_data = array();
        $points_data = array();

        if ($client_id) {
            $site_data = $this->App_model->getAppsBySiteId($site_id);
            $points_data = $this->Custompoints_model->getCustompoints(array(
                'client_id' => $client_id,
                'site_id' => $site_id
            ));
        }

        $this->data['site_data'] = $site_data;
        $this->data['points_data'] = $points_data;
        $this->render_page('widget_preview');
    }

    public function social_login()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['platform_data'] = array();
        $site_data = array();
        $sw_data = array();
        $plan_widget = array();

        if ($client_id) {
            $site_data = $this->App_model->getAppsBySiteId($site_id);
            $platform_data = $this->App_model->getPlatformWithType($site_id, 'web');

            //force use first web app platform
            $this->data['platform_data'] = $platform_data;

            $w_data = array(
                'client_id' => $client_id,
                'site_id' => $site_id,
            );
            $sw_data = $this->Widget_model->getWidgetSocialsSite($w_data);

            $this->load->model('Client_model');
            $this->load->model('Plan_model');
            $plan_subscription = $this->Client_model->getPlanByClientId($client_id);
            // get Plan display
            $plan_widget = $this->Plan_model->getPlanDisplayWidget($plan_subscription["plan_id"]);

            if (!(isset($plan_widget['social']) && $plan_widget['social'])) {
                redirect('/widget', 'refresh');
            }
        }

        $sw_ready = array();

        $callback = '';
        foreach ($sw_data as $sw) {
            $sw_prepare = array();
            $sw_prepare['key'] = $sw['key'];
            $sw_prepare['secret'] = $sw['secret'];
            $sw_prepare['sort_order'] = $sw['sort_order'];
            $sw_prepare['status'] = $sw['status'];
            $callback = $sw['callback'] && $sw['callback'] != '' ? $sw['callback'] : $callback;
            $sw_ready[$sw['provider']] = $sw_prepare;
        }
        $this->data['plan_widget'] = $plan_widget;
        $this->data['social_widget'] = $sw_ready;
        $this->data['callback'] = $callback;
        $this->data['site_data'] = $site_data;

        $this->data['main'] = 'widget_social_login';
        $this->render_page('template');
    }

    public function social_manage()
    {
        $data = $this->input->post('socials');
        $data_callback = $this->input->post('socials_callback');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($data) {
            foreach ($data as $d) {
                $s_data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'provider' => $d['name'],
                    'key' => $d['key'],
                    'secret' => $d['secret'],
                    'sort_order' => $d['sort_order'],
                    'status' => $d['status'],
                    'callback' => $data_callback
                );
                $this->Widget_model->updateWidgetSocials($s_data);
            }
        }
        $this->output->set_output(json_encode(array('status' => 'success')));
    }
}

?>