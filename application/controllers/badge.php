<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Badge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_Model');
        if(!$this->User_Model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Badge_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("badge", $lang['folder']);
    }

    public function index() {

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

        $this->getList();
    }

    private function getList() {

        $this->load->model('Badge_model');
        $this->load->model('Image_model');

        $client_id = $this->User_Model->getClientId();
        $site_id = $this->User_Model->getSiteId();
        $setting_group_id = $this->User_Model->getAdminGroupID();

        $this->data['badges'] = array();
        $this->data['user_group_id'] = $this->User_Model->getUserGroupId();
        $slot_total = 0;
        $this->data['slots'] = $slot_total;

        if ($this->User_Model->getUserGroupId() == $setting_group_id) {

            $results = $this->Badge_model->getBadges();

            foreach ($results as $result) {
                $action = array();

                if ($result['image'] && (S3_IMAGE . $result['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $result['image'] != 'HTTP/1.0 403 Forbidden')) {
                    $image = $this->Image_model->resize($result['image'], 50, 50);
                } else {
                    $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                }

                $this->data['badges'][] = array(
                    'badge_id' => $result['badge_id'],
                    'name' => $result['name'],
                    'hint' => $result['hint'],
                    'quantity' => $result['quantity'],
                    'status' => $result['status'],
                    'image' => $image,
                    'sort_order'  => $result['sort_order'],
                    'selected' => isset($this->request->post['selected']) && in_array($result['_id'], $this->request->post['selected']),
                    'action' => $action
                );
            }
        }
        else {

            $this->load->model('Reward_model');

            $badges = $this->Badge_model->getBadgeBySiteId($site_id);

            $reward_limit_data = $this->Reward_model->getBadgeRewardBySiteId($site_id);
            $badge_total = count($badges);

            if ($reward_limit_data) {

                $slot_total = $reward_limit_data[0]['limit'] - $badge_total;

                $this->data['slots'] = $slot_total;
                $this->data['no_image'] = $this->Image_model->resize('no_image.jpg', 50, 50);

                foreach ($badges as $badge) {
                    $action = array();

                    if ($badge['image'] && (S3_IMAGE . $badge['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge['image'] != 'HTTP/1.0 403 Forbidden')) {
                        $image = $this->Image_model->resize($badge['image'], 50, 50);
                    }
                    else {
                        $image = $this->Image_model->resize('no_image.jpg', 50, 50);
                    }

                    $this->data['badges'][] = array(
                        'badge_id' => $badge['badge_id'],
                        'name' => $badge['name'],
                        'hint' => $badge['hint'],
                        'quantity' => $badge['quantity'],
                        'image' => $image,
                        'href' => $action
                    );
                }

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

        $this->data['main'] = 'badge';
        $this->data['setting_group_id'] = $setting_group_id;

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    public function update() {
        $this->load->language('badge/badge');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('badge/badge');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() && $this->checkOwnerBadge($this->request->get['badge_id'])) {
            $this->model_badge_badge->editBadge($this->request->get['badge_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('badge/badge', 'site_id=' . $this->request->post['site_id'] . '&token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('badge/badge');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('badge/badge');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $badge_id) {
                if($this->checkOwnerBadge($badge_id)){
                    $this->model_badge_badge->deleteBadge($badge_id);
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('badge/badge', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }
}