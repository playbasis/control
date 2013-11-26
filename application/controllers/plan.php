<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Plan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Plan_model');

        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("plan", $lang['folder']);
    }

    public function index() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
    }

    public function page($offset=0) {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    public function insert() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'plan/insert';

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $this->Plan_model->addPlan($this->input->post());

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/plan', 'refresh');
            }
        }

        $this->getForm();
    }

    public function update($plan_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'plan/update/'.$plan_id;

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|min_length[2]|max_length[255]|xss_clean|check_space');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if($this->form_validation->run() && $this->data['message'] == null){
                $this->Plan_model->editPlan($plan_id, $this->input->post());

                $this->session->data['success'] = $this->lang->line('text_success');

                redirect('/plan', 'refresh');
            }
        }

        $this->getForm($plan_id);
    }

    public function delete() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $plan_id) {
                $c = $this->Plan_model->getClientByPlan($plan_id);
                if(empty($c)){
                    $this->Plan_model->deletePlan($plan_id);
                }else{
                    $p = $this->Plan_model->getPlan($plan_id);
                    $this->error['warning'] = $p['name']." ".$this->lang->line('error_plan_client_inuse');
                }
            }

            $this->session->data['success'] = $this->lang->line('text_success');

        }

        $this->getList(0);
    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('badge/page');

        $this->load->model('Domain_model');

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
        } else {
            $sort = 'name';
        }

        if ($this->input->get('order')) {
            $order = $this->input->get('order');
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page ;

        $data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        $total = $this->Plan_model->getTotalPlans($data);

        $results = $this->Plan_model->getPlans($data);

        if ($results) {
            foreach ($results as $result) {
                $this->data['plans'][] = array(
                    'plan_id' => $result['_id'],
                    'name' => $result['name'],
                    'description' => $result['description'],
                    'status' => $result['status'],
                    'selected'    => null
                );
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();

        $this->data['main'] = 'plan';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
//        $this->render_page('plan');
    }

    private function getForm($plan_id=null) {

        if ($plan_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
            $plan_info = $this->Plan_model->getPlan($plan_id);
        }

        if ($this->input->post('status')) {
            $this->data['status'] = $this->input->post('status');
        } elseif (!empty($plan_info)) {
            $this->data['status'] = $plan_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if ($this->input->post('sort_order')) {
            $this->data['sort_order'] = $this->input->post('sort_order');
        } elseif (!empty($plan_info)) {
            $this->data['sort_order'] = $plan_info['sort_order'];
        } else {
            $this->data['sort_order'] = 1;
        }

        if ($this->input->post('name')) {
            $this->data['name'] = $this->input->post('name');
        } elseif (!empty($plan_info)) {
            $this->data['name'] = $plan_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if ($this->input->post('description')) {
            $this->data['description'] = $this->input->post('description');
        } elseif (!empty($plan_info)) {
            $this->data['description'] = $plan_info['description'];
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('feature_data')) {
            $this->data['feature_data'] = $this->input->post('feature_data');
        } elseif (!empty($plan_info)){
            $this->data['feature_data'] = $plan_info["feature_to_plan"];
        } else {
            $this->data['feature_data'] = array();
        }

        if ($this->input->post('action_data')) {
            $this->data['action_data'] = $this->input->post('action_data');
        } elseif (!empty($plan_info)){
            $this->data['action_data'] = $plan_info["action_to_plan"];
        } else {
            $this->data['action_data'] = array();
        }

        if ($this->input->post('jigsaw_data')) {
            $this->data['jigsaw_data'] = $this->input->post('jigsaw_data');
        } elseif (!empty($plan_info)){
            $this->data['jigsaw_data'] = $plan_info["jigsaw_to_plan"];
        } else {
            $this->data['jigsaw_data'] = array();
        }

        if ($this->input->post('reward_data')) {
            $this->data['reward_data'] = $this->input->post('reward_data');
        } elseif (!empty($plan_info)){
            $this->data['reward_data'] = $plan_info["reward_to_plan"];
        } else {
            $this->data['reward_data'] = array();
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $data = array();

        $this->data['plan_features'] = array();
        $features = $this->Plan_model->getFeatures($data);

        if ($features) {
            foreach ($features as $feature) {

                $this->data['plan_features'][] = array(
                    'feature_id' => $feature['_id'],
                    'name' => $feature['name'],
                    'description' => $feature['description']
                );
            }
        }

        $this->data['plan_actions'] = array();
        $actions = $this->Plan_model->getActions($data);

        if ($actions) {
            foreach ($actions as $action) {
                $this->data['plan_actions'][] = array(
                    'action_id' => $action['_id'],
                    'name' => $action['name'],
                    'description' => $action['description']
                );
            }
        }

        $this->data['plan_jigsaws'] = array();
        $jigsaws = $this->Plan_model->getJigsaws($data);

        if ($jigsaws) {
            foreach ($jigsaws as $action) {
                $this->data['plan_jigsaws'][] = array(
                    'jigsaw_id' => $action['_id'],
                    'name' => $action['name'],
                    'description' => $action['description']
                );
            }
        }

        $this->data['plan_rewards'] = array();
        $rewards = $this->Plan_model->getRewards($data);

        if ($rewards) {
            foreach ($rewards as $reward) {
                $limit = null;
                foreach($plan_info["reward_to_plan"] as $reward_to_plan){
                    if($reward_to_plan['reward_id'] == $reward['_id']){
                        $limit = $reward_to_plan['limit'];
                    }
                }
                $this->data['plan_rewards'][] = array(
                    'reward_id' => $reward['_id'],
                    'name' => $reward['name'],
                    'description' => $reward['description'],
                    'limit' => $limit
                );
            }
        }

        $this->data['main'] = 'plan_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'plan')) {
            return true;
        } else {
            return false;
        }
    }
}
?>