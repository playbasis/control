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

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("plan", $lang['folder']);
    }

    public function index()
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList(0);
    }

    public function page($offset = 0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList($offset);

    }

    public function insert()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'plan/insert';

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('entry_description'),
            'trim|min_length[2]|max_length[1000]|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('entry_price'), 'trim|required|numeric');
        $this->form_validation->set_rules('display', $this->lang->line('entry_display'), 'trim|required');
        $this->form_validation->set_rules('status', $this->lang->line('entry_status'), 'trim|required');
        $this->form_validation->set_rules('limit_num_client', $this->lang->line('entry_limit_num_clients'), 'numeric');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            foreach ($this->input->post('reward_data') as $reward) {
                if (!is_numeric($reward['limit']) && $reward['limit'] != null) {
                    $this->data['message'] = "Please provide only numeric characters in the Rewards";
                    break;
                }
            }

            $plan_name = $this->input->post('name');

            if ($this->Plan_model->checkPlanExistsByName($plan_name)) {
                $this->data['message'] = "The Plan name provided already exists.";
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                $this->Plan_model->addPlan($this->input->post());

                $this->session->set_flashdata('success', $this->lang->line('text_success'));

                redirect('/plan', 'refresh');

            }
        }

        $this->getForm();
    }

    public function update($plan_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'plan/update/' . $plan_id;

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('entry_description'),
            'trim|min_length[2]|max_length[1000]|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('entry_price'), 'trim|required|numeric');
        $this->form_validation->set_rules('display', $this->lang->line('entry_display'), 'trim|required');
        $this->form_validation->set_rules('status', $this->lang->line('entry_status'), 'trim|required');
        $this->form_validation->set_rules('limit_num_client', $this->lang->line('entry_limit_num_clients'),
            'trim|numeric');

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {

            $this->data['message'] = null;

            if (!$this->validateModify()) {
                $this->data['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run() && $this->data['message'] == null) {
                if (PAYMENT_CHANNEL_DEFAULT == PAYMENT_CHANNEL_STRIPE) {
                    $limit_other = $this->input->post('limit_others');
                    $trial_days = $limit_other['trial']['limit'];
                    $valid = $this->updatePlanInStripe($plan_id, $this->input->post('name'),
                        intval($this->input->post('price')), $trial_days);
                    if (!$valid) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail_update'));
                        redirect('/plan', 'refresh');
                    }
                }
                $this->Plan_model->editPlan($plan_id, $this->input->post());

                $this->session->data['success'] = $this->lang->line('text_success');

                //Save into clients too
                $this->load->model("Client_model");
                $clients_by_plan = $this->Plan_model->getClientByPlan($plan_id); // returns: client_id, site_id, date_added, date_modified
                $this->Client_model->editClientsPlan($clients_by_plan, $plan_id); // update many clients at once
                $this->session->set_flashdata('success', $this->lang->line('text_success_update'));
                redirect('/plan', 'refresh');
            }

        }

        $this->getForm($plan_id);
    }

    public function delete()
    {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        $this->load->model('Client_model');

        if ($this->input->post('selected') && $this->error['warning'] == null) {
            foreach ($this->input->post('selected') as $plan_id) {

                $all_clients_in_plan = $this->Plan_model->getClientByPlan($plan_id);

                $c = array();

                foreach ($all_clients_in_plan as $client) {
                    $the_client_id = $client['client_id'];

                    $temp = $this->Client_model->getClient($the_client_id);
                    if (!$temp['deleted']) {
                        $c[] = $temp;
                    }
                }

                if (empty($c)) {
                    $this->Plan_model->deletePlan($plan_id);

                } else {
                    $p = $this->Plan_model->getPlan($plan_id);
                    $this->session->set_flashdata('fail', $this->lang->line('text_fail'));
                    redirect('/plan', 'refresh');
                }
            }
            $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            redirect('/plan', 'refresh');
        }

        $this->getList(0);
    }

    private function getList($offset)
    {

        $per_page = NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        $config['base_url'] = site_url('plan/page');

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

        $limit = isset($params['limit']) ? $params['limit'] : $per_page;

        $data = array(
            'sort' => $sort,
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
                    'trial' => array_key_exists('limit_others', $result) && array_key_exists('trial',
                        $result['limit_others']) ? $result['limit_others']['trial'] : DEFAULT_TRIAL_DAYS,
                    'price' => array_key_exists('price', $result) ? $result['price'] : DEFAULT_PLAN_PRICE,
                    'display' => array_key_exists('display', $result) ? $result['display'] : DEFAULT_PLAN_DISPLAY,
                    'status' => $result['status'],
                    'selected' => ($this->input->post('selected') && in_array($result['_id'],
                            $this->input->post('selected'))),
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

        $config['num_links'] = NUMBER_OF_ADJACENT_PAGES;

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = "<li class='page_index_nav next'>";
        $config['next_tag_close'] = "</li>";

        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = "<li class='page_index_nav prev'>";
        $config['prev_tag_close'] = "</li>";

        $config['num_tag_open'] = '<li class="page_index_number">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page_index_number active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page_index_nav next">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page_index_nav prev">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['pagination_links'] = $this->pagination->create_links();
        $this->data['pagination_total_pages'] = ceil(floatval($config["total_rows"]) / $config["per_page"]);
        $this->data['pagination_total_rows'] = $config["total_rows"];

        $this->data['main'] = 'plan';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function getForm($plan_id = null)
    {

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
            $this->data['description'] = htmlentities($this->input->post('description'));
        } elseif (!empty($plan_info)) {
            $this->data['description'] = htmlentities($plan_info['description']);
        } else {
            $this->data['description'] = '';
        }

        if ($this->input->post('price')) {
            $this->data['price'] = htmlentities($this->input->post('price'));
        } elseif (!empty($plan_info)) {
            $this->data['price'] = htmlentities(array_key_exists('price',
                $plan_info) ? $plan_info['price'] : DEFAULT_PLAN_PRICE);
        } else {
            $this->data['price'] = '';
        }

        if ($this->input->post('display')) {
            $this->data['display'] = htmlentities($this->input->post('display'));
        } elseif (!empty($plan_info)) {
            $this->data['display'] = htmlentities(array_key_exists('display',
                $plan_info) ? $plan_info['display'] : DEFAULT_PLAN_DISPLAY);
        } else {
            $this->data['display'] = DEFAULT_PLAN_DISPLAY;
        }

        if ($this->input->post('limit_num_client')) {
            $this->data['limit_num_client'] = $this->input->post('limit_num_client');
        } elseif (!empty($plan_info)) {
            $this->data['limit_num_client'] = $plan_info['limit_num_client'];
        } else {
            $this->data['limit_num_client'] = null;
        }

        if ($this->input->post('feature_data')) {
            $this->data['feature_data'] = $this->input->post('feature_data');
        } elseif (!empty($plan_info)) {
            if (isset($plan_info['feature_to_plan'])) {
                $this->data['feature_data'] = $plan_info['feature_to_plan'];
            } else {
                $this->data['feature_data'] = array();
            }
        } else {
            $this->data['feature_data'] = array();
        }

        if ($this->input->post('action_data')) {
            $this->data['action_data'] = $this->input->post('action_data');
        } elseif (!empty($plan_info)) {
            if (isset($plan_info['action_to_plan'])) {
                $this->data['action_data'] = $plan_info["action_to_plan"];
            } else {
                $this->data['action_data'] = array();
            }
        } else {
            $this->data['action_data'] = array();
        }

        if ($this->input->post('jigsaw_data')) {
            $this->data['jigsaw_data'] = $this->input->post('jigsaw_data');
        } elseif (!empty($plan_info)) {
            if (isset($plan_info["jigsaw_to_plan"])) {
                $this->data['jigsaw_data'] = $plan_info["jigsaw_to_plan"];
            } else {
                $this->data['jigsaw_data'] = array();
            }
        } else {
            $this->data['jigsaw_data'] = array();
        }

        if ($this->input->post('reward_data')) {
            $this->data['reward_data'] = $this->input->post('reward_data');
        } elseif (!empty($plan_info)) {
            $this->data['reward_data'] = $plan_info["reward_to_plan"];
        } else {
            $this->data['reward_data'] = array();
        }

        // default limit noti must has these fields
        $default_limit_noti = array(
            "sms" => 0,
            "email" => 0,
            "push" => 0
        );
        if ($this->input->post('limit_noti')) {
            $this->data['limit_noti'] = $this->input->post('limit_noti');
        } elseif (!empty($plan_info) && isset($plan_info['limit_notifications'])) {
            $this->data['limit_noti'] = $plan_info["limit_notifications"];
            // merge with default, prevent missing fields
            $this->data["limit_noti"] = array_merge(
                $default_limit_noti,
                $this->data["limit_noti"]
            );
        } else {
            // Client don't have this field in DB, use default
            $this->data['limit_noti'] = $default_limit_noti;
        }

        // default limit others must has these fields
        $default_limit_others = array(
            "app" => null,
            "platform" => null,
            "rule" => null,
            "goods" => null,
            "redeem" => null,
            "insight" => null,
            "mission" => null,
            "player" => null,
            "quest" => null,
            "quiz" => null,
            "custompoint" => null,
            "trial" => null,
            "user" => null,
            "image" => null
        );
        if ($this->input->post('limit_others')) {
            $this->data['limit_others'] = $this->input->post('limit_others');
        } elseif (!empty($plan_info) && isset($plan_info['limit_others'])) {
            $this->data['limit_others'] = $plan_info["limit_others"];
            // merge with default, prevent missing fields
            foreach ($this->data['limit_others'] as $k => $v) {
                if (!array_key_exists($k, $default_limit_others)) {
                    unset($this->data['limit_others'][$k]);
                }
            }
            $this->data["limit_others"] = array_merge($default_limit_others, $this->data["limit_others"]);
        } else {
            // Client don't have this field in DB, use default
            $this->data["limit_others"] = $default_limit_others;
        }

        if ($this->input->post('limit_req')) {
            $this->data['limit_req'] = $this->input->post('limit_req');
        } elseif (!empty($plan_info) && isset($plan_info['limit_requests'])) {
            $limit_req = array();
            foreach ($plan_info['limit_requests'] as $key => $value) {
                $limit_req[] = array(
                    'field' => $key,
                    'limit' => $value
                );
            }
            $this->data['limit_req'] = $limit_req;
        } else {
            $this->data['limit_req'] = array();
        }

        $default_limit_widgets = array(
            "social" => null,
            "leaderboard" => null,
            "livefeed" => null,
            "feed" => null,
            "profile" => null,
            "userbar" => null,
            "achievement" => null,
            "quest" => null,
            "quiz" => null,
            "rewardstore" => null,
            "treasure" => null,
            "trackevent" => null,
        );

        $default_limit_cms = array(
            "article" => null,
            "news" => null,
            "event" => null,
        );
        if ($this->input->post('limit_widget')) {
            $this->data['limit_widget'] = $this->input->post('limit_widget');
        } elseif (!empty($plan_info) && isset($plan_info['limit_widget'])) {
            $this->data['limit_widget'] = $plan_info["limit_widget"];
            // merge with default, prevent missing fields
            foreach ($this->data['limit_widget'] as $k => $v) {
                if (!array_key_exists($k, $default_limit_widgets)) {
                    unset($this->data['limit_widget'][$k]);
                }
            }
            $this->data["limit_widget"] = array_merge($default_limit_widgets, $this->data["limit_widget"]);
        } else {
            // Client don't have this field in DB, use default
            $this->data["limit_widget"] = $default_limit_widgets;
        }

        if ($this->input->post('limit_cms')) {
            $this->data['limit_cms'] = $this->input->post('limit_cms');
        } elseif (!empty($plan_info) && isset($plan_info['limit_cms'])) {
            $this->data['limit_cms'] = $plan_info["limit_cms"];
            // merge with default, prevent missing fields
            foreach ($this->data['limit_cms'] as $k => $v) {
                if (!array_key_exists($k, $default_limit_cms)) {
                    unset($this->data['limit_cms'][$k]);
                }
            }
            $this->data["limit_cms"] = array_merge($default_limit_cms, $this->data["limit_cms"]);
        } else {
            // Client don't have this field in DB, use default
            $this->data["limit_cms"] = $default_limit_cms;
        }

        $this->data['client_id'] = $this->User_model->getClientId();
        $this->data['site_id'] = $this->User_model->getSiteId();

        $data = array("filter_status" => true);

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
                if (!empty($plan_info)) {
                    foreach ($plan_info["reward_to_plan"] as $reward_to_plan) {
                        if ($reward_to_plan['reward_id'] == $reward['_id']) {
                            $limit = $reward_to_plan['limit'];
                        }
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

        $this->data['clients_in_plan'] = $this->getClientsByPlanId($plan_id);

        $this->data['main'] = 'plan_form';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'plan')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess()
    {
        if ($this->User_model->isAdmin()) {
            return true;
        }
        if ($this->User_model->hasPermission('access', 'plan')) {
            return true;
        } else {
            return false;
        }
    }

    public function getClientsByPlanId($plan_id)
    {

        $allClientsInThisPlan = $this->Plan_model->getClientByPlanOnlyClient($plan_id);

        $listOfClients = array();
        $this->load->model('Client_model');
        foreach ($allClientsInThisPlan as $client) {
            $get_client = $this->Client_model->getClient($client['client_id']);
            if ($get_client != null) {
                if (!$get_client['deleted']) {
                    $listOfClients[] = $get_client;
                }
            }
        }
        return $listOfClients;
    }

    private function updatePlanInStripe($plan_id, $name, $price, $trial_days)
    {
        require_once(APPPATH . '/libraries/stripe/init.php');
        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
        $plan = null;
        try {
            $plan = \Stripe\Plan::retrieve($plan_id);
        } catch (Exception $e) {
            /* this plan is not being used, so it can be updated */
            return true;
        }
        try {
            if ($plan->name != $name) {
                $plan->name = $name;
            }
            if ($plan->amount != $price * 100) {
                $plan->amount = $price * 100;
            }
            if ($plan->trial_period_days != $trial_days) {
                $plan->trial_period_days = $trial_days;
            }
            $plan->save();
        } catch (Exception $e) {
            /* from https://stripe.com/docs/api/php#update_plan */
            /* it is about to update a plan which is being used */
            /* however, by design, Stripe only allow "name" to be updated */
            return false;
        }
        return true;
    }
}

?>
