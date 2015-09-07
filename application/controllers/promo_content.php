<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';


class Promo_content extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Promo_content_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("promo_content", $lang['folder']);
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

        $this->getList(0);
    }

    public function insert()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'promo_content/insert';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

//        $promo_contents = $this->Promo_content_model->countPromoContents($client_id, $site_id);

        $this->load->model('Permission_model');
        $this->load->model('Plan_model');

        // Get Limit
//        $plan_id = $this->Permission_model->getPermissionBySiteId($site_id);
//        $limit_promo_content = $this->Plan_model->getPlanLimitById($plan_id, 'others', 'promo_content');

        $this->data['message'] = null;

//        if ($limit_promo_content && $promo_contents >= $limit_promo_content) {
//            $this->data['message'] = $this->lang->line('error_promo_contents_limit');
//        }

        $this->form_validation->set_rules('name', $this->lang->line('entry_name'),
            'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('entry_description'),
            'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('date_start', $this->lang->line('entry_date_start'),
            'trim|required|xss_clean');
        $this->form_validation->set_rules('date_end', $this->lang->line('entry_date_end'), 'trim|required|xss_clean');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateModify()) {
                $this->error['message'] = $this->lang->line('error_permission');
            }

            if ($this->form_validation->run()) {
                $promo_content_data = $this->input->post();

                $data['client_id'] = $this->User_model->getClientId();
                $data['site_id'] = $this->User_model->getSiteId();
                $data['name'] = $promo_content_data['name'];
                $data['desc'] = $promo_content_data['description'];
                $data['date_start'] = $promo_content_data['date_start'];
                $data['date_end'] = $promo_content_data['date_end'];
                $data['image'] = $promo_content_data['image'];
                $data['status'] = true;

                $insert = $this->Promo_content_model->insertPromoContent($data);
                if ($insert) {
                    redirect('/promo_content', 'refresh');
                }
            }
        }
        $this->getForm();
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

        $this->getList($offset);
    }

    public function getList($offset)
    {
        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->load->library('pagination');

        $config['per_page'] = NUMBER_OF_RECORDS_PER_PAGE;

        $filter = array(
            'limit' => $config['per_page'],
            'start' => $offset,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'sort' => 'sort_order'
        );

        if (isset($_GET['filter_name'])) {
            $filter['filter_name'] = $_GET['filter_name'];
        }

        $config['base_url'] = site_url('promo_content/page');
        $config["uri_segment"] = 3;
        $config['total_rows'] = 0;

        if ($client_id) {
            // todo: insert data query here to show in dashboard list
        }

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

        $this->data['main'] = 'promo_content';
        $this->render_page('template');
    }

    public function getForm($promo_content_id = null)
    {
        $this->data['main'] = 'promo_content_form';

        $this->load->vars($this->data);

        if ($this->input->post('image')) {
            $this->data['image'] = $this->input->post('image');
//        } elseif (!empty($badge_info)) {
//            $this->data['image'] = $badge_info['image'];
        } else {
            $this->data['image'] = 'no_image.jpg';
        }

        if ($this->data['image']){
            $info = pathinfo($this->data['image']);
            if(isset($info['extension'])){
                $extension = $info['extension'];
                $new_image = 'cache/' . utf8_substr($this->data['image'], 0, utf8_strrpos($this->data['image'], '.')).'-100x100.'.$extension;
                $this->data['thumb'] = S3_IMAGE.$new_image;
            }else{
                $this->data['thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
            }
        }else{
            $this->data['thumb'] = S3_IMAGE."cache/no_image-100x100.jpg";
        }

        $this->render_page('template');
    }

    private function validateModify()
    {
        if ($this->User_model->hasPermission('modify', 'promo_content')) {
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
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access',
                'promo_content') && $this->Feature_model->getFeatureExistByClientId($client_id, 'promo_content')
        ) {
            return true;
        } else {
            return false;
        }
    }

}

function index_badge_id($obj)
{
    return $obj['badge_id'];
}

function index_reward_id($obj)
{
    return $obj['reward_id'];
}