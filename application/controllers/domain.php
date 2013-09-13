<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Domain extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Domain_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("domain", $lang['folder']);
    }

    public function index() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList(0);
    }

    public function page($offset=0) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');

        $this->getList($offset);
    }

    private function getList($offset) {

        $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = site_url('domain/page');

        $this->load->model('Permission_model');

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();
        $setting_group_id = $this->User_model->getAdminGroupID();

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'domain_name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        $limit = isset($params['limit']) ? $params['limit'] : $per_page ;

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort'  => $sort,
            'order' => $order,
            'start' => $offset,
            'limit' => $limit
        );

        $total = $this->Domain_model->getTotalDomainsByClientId($client_id);

        $results = $this->Domain_model->getDomainsByClientId($data);

        if ($results) {
            foreach ($results as $result) {

                $plan_id = $this->Permission_model->getPermissionBySiteId($result['site_id']);

                $this->data['domain_list'][] = array(
                    'selected'    => isset($this->request->post['selected']) && in_array($result['sitel_id'], $this->request->post['selected']),
                    'site_id' => $result['site_id'],
                    'client_id' => $result['client_id'],
                    'plan_id' => $plan_id,
                    'domain_name' => $result['domain_name'],
                    'site_name' => $result['site_name'],
                    'keys' => $result['api_key'],
                    'secret' => $result['api_secret'],
                    'date_start' => $result['date_start'],
                    'date_expire' => $result['date_expire'],
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],
                    'date_modified' => $result['date_modified']
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

        $this->data['user_group_id'] = $this->User_model->getUserGroupId();
        $this->data['main'] = 'domain';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function reset() {
        $json = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->Domain_model->resetToken($this->input->post('site_id'));

            $json['success'] = $this->lang->line('text_success');

        }

        $this->output->set_output(json_encode($json));
    }
}
?>