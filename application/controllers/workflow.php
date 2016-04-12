<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Workflow extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_model');
        $this->load->model('Player_model');
        $this->load->model('User_model');
        $this->load->model('Permission_model');
        $this->load->model('App_model');

        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Workflow_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("workflow", $lang['folder']);
        $this->load->model('Store_org_model');
        $this->load->model('Feature_model');

        /* initialize $this->api */
        $result = $this->User_model->get_api_key_secret($this->User_model->getClientId(),
            $this->User_model->getSiteId());
        $this->_api = $this->playbasisapi;
        $platforms = $this->App_model->getPlatFormByAppId(array(
            'site_id' => $this->User_model->getSiteId(),
        ));
        $platform = isset($platforms[0]) ? $platforms[0] : null; // simply use the first platform
        if ($platform) {
            $this->_api->set_api_key($result['api_key']);
            $this->_api->set_api_secret($result['api_secret']);
            $pkg_name = isset($platform['data']['ios_bundle_id']) ? $platform['data']['ios_bundle_id'] : (isset($platform['data']['android_package_name']) ? $platform['data']['android_package_name'] : null);
            $this->_api->auth($pkg_name);
        }
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
        $this->data['form'] = 'workflow/';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if ($this->input->post('user_id')) {
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
            if (isset($result->success)) {
                if ($result->success) {
                    $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                } else {
                    $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                }
            } else {
                $this->session->set_flashdata("fail", $this->lang->line("text_fail_internal"));
            }
            redirect('/workflow', 'refresh');
        } // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action') == "delete") {
                foreach ($selectedUsers as $selectedUser) {
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
                    if (!$result->success) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                        redirect('/workflow', 'refresh');
                    }
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->getPlayerList("approved", 0);

    }

    public function rejected($offset =0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'workflow/rejected/';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if ($this->input->post('user_id')) {
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
            if ($result->success) {
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            } else {
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/rejected', 'refresh');
        } // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action') == "delete") {
                foreach ($selectedUsers as $selectedUser) {
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
                    if (!$result->success) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                        redirect('/workflow', 'refresh');
                    }
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow/rejected', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->getPlayerList("rejected", $offset);

    }

    public function pending($offset=0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'workflow/pending/';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if ($this->input->post('user_id')) {
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
            if ($result->success) {
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            } else {
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/pending', 'refresh');
        } // incase: select player(s) to approve/reject/delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action') == "approve") {
                foreach ($selectedUsers as $selectedUser) {
                    $this->Workflow_model->approvePlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_approve'));
                redirect('/workflow/pending', 'refresh');
            } elseif ($this->input->post('action') == "reject") {
                foreach ($selectedUsers as $selectedUser) {
                    $this->Workflow_model->rejectPlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_reject'));
                redirect('/workflow/pending', 'refresh');
            } elseif ($this->input->post('action') == "delete") {
                foreach ($selectedUsers as $selectedUser) {
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
                    if (!$result->success) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                        redirect('/workflow', 'refresh');
                    }
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow/pending', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->getPlayerList("pending",$offset);

    }

    public function locked($offset =0)
    {

        if (!$this->validateAccess()) {
            echo "<script>alert('" . $this->lang->line('error_access') . "'); history.go(-1);</script>";
            die();
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['form'] = 'workflow/locked/';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->error['warning'] = null;

        if (!$this->validateModify()) {
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if ($this->input->post('user_id')) {
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
            if ($result->success) {
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            } else {
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/locked', 'refresh');
        } // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action') == "unlock") {
                foreach ($selectedUsers as $selectedUser) {
                    $this->Workflow_model->unlockPlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_approve'));
                redirect('/workflow/locked', 'refresh');
            } elseif ($this->input->post('action') == "delete") {
                foreach ($selectedUsers as $selectedUser) {
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer($this->_api, $player['cl_player_id']);
                    if (!$result->success) {
                        $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
                        redirect('/workflow', 'refresh');
                    }
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
                redirect('/workflow/locked', 'refresh');
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->getPlayerList("approved", $offset, true);

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
        $this->data['form'] = 'workflow/page/';

        $this->getPlayerList("approved", $offset);
    }

    private function getPlayerList($status, $offset, $locked = false)
    {
        $per_page = 2 * NUMBER_OF_RECORDS_PER_PAGE;

        $this->load->library('pagination');

        if($locked){
            $config['base_url'] = site_url('workflow/locked');
        }elseif($status=="approved"){
            $config['base_url'] = site_url('workflow/page');
        }else{
            $config['base_url'] = site_url('workflow/'.$status);
        }

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
        } else {
            $sort = 'cl_player_id';
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

        if (isset($_GET['filter_name'])) {
            $data['filter_name'] = $_GET['filter_name'];
        }

        if (isset($_GET['filter_id'])) {
            $data['filter_id'] = $_GET['filter_id'];
        }

        if (isset($_GET['filter_email'])) {
            $data['filter_email'] = $_GET['filter_email'];
        }

        $this->data['player_list'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['tab_status'] = $locked ? "locked" : $status;
        if ($locked) {
            $this->data['player_list'] = $this->Workflow_model->getLockedPlayer($client_id, $site_id, $data);
        } elseif ($status == 'pending') {
            $this->data['player_list'] = $this->Workflow_model->getPendingPlayer($client_id, $site_id, $data);
        } else {
            $this->data['player_list'] = $this->Workflow_model->getPlayerByApprovalStatus($client_id, $site_id, $status, $data);
        }


        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
            foreach ($this->data['player_list'] as &$player) {
                $orgs = $this->Workflow_model->getOrganizationToPlayer($client_id, $site_id, $player['_id']);
                foreach ($orgs as $org) {
                    $role_string = '';
                    if (isset($org['roles']) && !empty($org['roles'])) {
                        $array = array_keys($org['roles']);
                        foreach ($array as $role) {
                            if ($role_string == '') {
                                $role_string = $role;
                            } else {
                                $role_string = $role_string . ', ' . $role;
                            }
                        }
                    }

                    $node_info = $this->Store_org_model->retrieveNodeById($org['node_id']);
                    $org_info = $this->Store_org_model->retrieveOrganizeById($node_info['organize']);

                    if (!isset($player['organization_node'])) {
                        $player['organization_node'] = $node_info['name'];
                        $player['organization_type'] = $org_info['name'];
                        $player['organization_role'] = $role_string;
                    } else {
                        $player['organization_node'] = $player['organization_node'] . '<hr>' . $node_info['name'];
                        $player['organization_type'] = $player['organization_type'] . '<hr>' . $org_info['name'];
                        $player['organization_role'] = $player['organization_role'] . '<hr>' . $role_string;
                    }
                }
            }
        } else {
            $this->data['org_status'] = false;
        }
        $this->data['pending_count'] = $this->Workflow_model->getTotalPendingPlayer($client_id, $site_id);
        $this->data['locked_count'] = $this->Workflow_model->getTotalLockedPlayer($client_id, $site_id);


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

        if(($locked)){
            $config['total_rows'] = $this->Workflow_model->getTotalLockedPlayer($client_id, $site_id);
        }elseif($status == "pending") {
            $config['total_rows'] = $this->Workflow_model->getTotalPendingPlayer($client_id, $site_id);
        }else{
            $config['total_rows'] = $this->Workflow_model->getTotalPlayerByApprovalStatus($client_id, $site_id, $status);
        }
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


        $this->data['main'] = 'workflow';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function findPbPlayerId($player_id)
    {
        //$this->load->model('Player_model');
        $pb_player_id = $this->Player_model->getPlaybasisId(array(
            'client_id' => $this->User_model->getClientId(),
            'site_id' => $this->User_model->getSiteId(),
            'cl_player_id' => $player_id
        ));
        if (!$pb_player_id) {
            $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            return $pb_player_id;
        }
        return $pb_player_id;
    }

    public function edit_account($user_id)
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_edit');
        $this->data['form'] = 'workflow/edit_account/' . $user_id;
        $this->data['action'] = 'edit';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();
            $check_status = true;

            // initial validate whether role is set without node selection
            if ($this->User_model->hasPermission('access',
                    'store_org') && $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(),
                    'store_org')
            ) {
                foreach ($data['organize_id'] as $i => $node) {
                    if ((isset($data['organize_role'][$i]) && !empty($data['organize_role'][$i])) && !(isset($data['organize_node'][$i]) && !empty($data['organize_node'][$i]))) {
                        //$_POST['organize_id'][$i] = "";
                        $_POST['organize_node'][$i] = "";
                        $check_status = false;
                        $this->data['message'] = $this->lang->line('text_fail_set_role');
                        break;
                    }
                }
            }

            if ($check_status == true) {
                $status = $this->Workflow_model->editPlayer($this->_api, $data['cl_player_id'], $data);
                if (isset($status->success)) {
                    if ($status->success) {
                        if ($this->User_model->hasPermission('access', 'store_org') &&
                            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(),
                                'store_org')
                        ) {
                            foreach ($data['organize_id'] as $i => $org_id) {
                                //Add player to node
                                if (isset($data['organize_node'][$i]) && !empty($data['organize_node'][$i]) && $status->success) {

                                    $pb_player_id = $this->findPbPlayerId($data['cl_player_id']);
                                    if ($org_id == "") {// this player has never been added to any node
                                        $this->Workflow_model->addPlayerToNode($this->_api, $data['cl_player_id'],
                                            $data['organize_node'][$i]);
                                    } else { //this player has been added to some node
                                        $this->Workflow_model->editOrganizationOfPlayer($client_id, $site_id,
                                            $data['organize_id'][$i], $pb_player_id, $data['organize_node'][$i]);
                                    }

                                    //set role of player
                                    if (isset($data['organize_role'][$i]) && !empty($data['organize_role'][$i])) {

                                        $temp = $this->Workflow_model->getRole($client_id, $site_id, $pb_player_id,
                                            $data['organize_node'][$i]);
                                        if ($temp != null) {
                                            $this->Workflow_model->clearPlayerRole($client_id, $site_id, $pb_player_id,
                                                $data['organize_node'][$i]);
                                        }

                                        $role_array = explode(",", $data['organize_role'][$i]);
                                        foreach ($role_array as $role) {
                                            $role = str_replace(' ', '', $role);
                                            $status = $this->Workflow_model->setPlayerRole($this->_api, $data['cl_player_id'],
                                                $data['organize_node'][$i], $role);
                                            if (!$status->success) {
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($status->success) {
                                // created player, added player to the node and set role of the player
                                $this->session->set_flashdata('success', $this->lang->line('text_success_edit'));
                                redirect('/workflow', 'refresh');
                            } else {
                                // failed to set role of player
                                $this->data['message'] = $status->message;
                            }

                        } else {
                            $this->session->set_flashdata('success', $this->lang->line('text_success_edit'));
                            redirect('/workflow', 'refresh');
                        }
                    } else {
                        if (!isset($_POST['organize_node'][0])) {
                            $_POST['organize_node'][0] = "";
                        }
                        $this->data['message'] = $status->message;
                    }
                } else {
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_internal"));
                    redirect('/workflow', 'refresh');
                }
            }
        }
        $this->getForm($user_id);
    }

    public function create_account()
    {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_create');
        $this->data['form'] = 'workflow/create_account/';
        $this->data['action'] = 'create';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();
            //set value of username to equal to cl_player_id
            $data['username'] = $data['cl_player_id'];

            if ($data['password'] != $data['confirm_password']) {
                $this->data['message'] = $this->lang->line('text_fail_confirm_password');
                if (!isset($_POST['organize_node'][0])) {
                    $_POST['organize_node'][0] = "";
                }
            } elseif ($this->User_model->hasPermission('access', 'store_org') &&
                $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org') &&
                (isset($data['organize_role'][0]) && !empty($data['organize_role'][0])) &&
                !(isset($data['organize_node'][0]) && !empty($data['organize_node'][0]))
            ) {
                $_POST['organize_node'][0] = "";
                $this->data['message'] = $this->lang->line('text_fail_set_role');

            } else {
                $status = $this->Workflow_model->createPlayer($this->_api, $data);
                if (isset($status->success)) {
                    if ($status->success) {
                        if ($this->User_model->hasPermission('access', 'store_org') &&
                            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(),
                                'store_org')
                        ) {
                            //Add player to node
                            if (isset($data['organize_node'][0]) && !empty($data['organize_node'][0])) {
                                $status = $this->Workflow_model->addPlayerToNode($this->_api, $data['cl_player_id'],
                                    $data['organize_node'][0]);
                                if ($status->success) {
                                    //set role of player
                                    if (isset($data['organize_role'][0]) && !empty($data['organize_role'][0])) {
                                        $role_array = explode(",", $data['organize_role'][0]);
                                        $status1 = null;
                                        foreach ($role_array as $role) {
                                            $role = str_replace(' ', '', $role);
                                            $status1 = $this->Workflow_model->setPlayerRole($this->_api, $data['cl_player_id'],
                                                $data['organize_node'][0], $role);
                                            if (!$status1->success) {
                                                break;
                                            }
                                        }
                                        if ($status1->success) {
                                            // created player, added player to the node and set role of the player
                                            $this->session->set_flashdata('success',
                                                $this->lang->line('text_success_create'));
                                            redirect('/workflow', 'refresh');
                                        } else {
                                            // failed to set role of player
                                            $this->data['message'] = $status1->message;
                                        }
                                    } else {
                                        //created player and added player to the node but did not set role of the player
                                        $this->session->set_flashdata('success',
                                            $this->lang->line('text_success_create_without_role_setting'));
                                        redirect('/workflow', 'refresh');
                                    }
                                } else {
                                    //failed to add player to node
                                    $this->data['message'] = $status->message;
                                }
                            } else {
                                //created player with enabled store org feature but did not add the player to any node
                                $this->session->set_flashdata('success',
                                    $this->lang->line('text_success_create_without_org_setting'));
                                redirect('/workflow', 'refresh');
                            }
                        } else {
                            // created player with disabled store org feature
                            $this->session->set_flashdata('success', $this->lang->line('text_success_create'));
                            redirect('/workflow', 'refresh');
                        }
                    } else {
                        // failed to create player
                        if (!isset($_POST['organize_node'][0])) {
                            $_POST['organize_node'][0] = "";
                        }
                        $this->data['message'] = $status->message;
                    }
                } else {
                    $this->session->set_flashdata("fail", $this->lang->line("text_fail_internal"));
                    redirect('/workflow', 'refresh');
                }
            }
        }

        $this->getForm();
    }

    public function getForm($user_id = 0)
    {

        if ($this->User_model->hasPermission('access', 'store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        } else {
            $this->data['org_status'] = false;
        }

        $this->data['requester'] = array();

        if (isset($_POST['username'])) {
            $this->data['requester'] = $_POST;

            if ($this->data['org_status']) {
                if (isset($_POST['organize_id']) && !empty($_POST['organize_id'])) {
                    $this->data['organize_id'] = $_POST['organize_id'];
                }
                if (isset($_POST['organize_type']) && !empty($_POST['organize_type'])) {
                    $this->data['organize_type'] = $_POST['organize_type'];
                }
                if (isset($_POST['organize_node']) && !empty($_POST['organize_node'])) {
                    $this->data['organize_node'] = $_POST['organize_node'];
                }
                if (isset($_POST['organize_role']) && !empty($_POST['organize_role'])) {
                    $this->data['organize_role'] = $_POST['organize_role'];
                }
            }

        } elseif ($user_id != 0) {
            $this->data['requester'] = $this->Player_model->getPlayerById($user_id);
            if ($this->data['org_status']) {
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $org_info = $this->Workflow_model->getOrganizationToPlayer($client_id, $site_id, new MongoId($user_id));
                if (isset($org_info) && !empty($org_info)) {

                    foreach ($org_info as $org) {
                        $this->data['organize_id'][] = $org['_id'];
                        $this->data['organize_node'][] = $org['node_id'];
                        $node = $this->Store_org_model->retrieveNodeById(new MongoId($org['node_id']));
                        $this->data['organize_type'][] = $node["organize"];

                        if (isset($org['roles']) && !empty($org['roles'])) {
                            $array_role = array_keys($org['roles']);
                            $role_string = '';
                            foreach ($array_role as $role) {
                                if ($role_string == '') {
                                    $role_string = $role;
                                } else {
                                    $role_string = $role_string . ',' . $role;
                                }
                            }
                            $this->data['organize_role'][] = $role_string;
                        } else {
                            $this->data['organize_role'][] = "";
                        }
                    }
                } else {
                    $this->data['organize_id'][] = "";
                    $this->data['organize_type'][] = "";
                    $this->data['organize_node'][] = "";
                    $this->data['organize_role'][] = "";
                }
            }
        } else {
            $this->data['requester'] = array('approve_status' => 'approved', 'gender' => 'male');
            if ($this->data['org_status']) {
                $this->data['organize_id'][] = "";
                $this->data['organize_type'][] = "";
                $this->data['organize_node'][] = "";
                $this->data['organize_role'][] = "";
            }
        }

        $this->data['main'] = 'workflow_form';
        $this->render_page('template');

    }

    private function validateModify()
    {

        if ($this->User_model->hasPermission('modify', 'user')) {
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
                'workflow') && $this->Feature_model->getFeatureExistByClientId($client_id, 'workflow')
        ) {
            return true;
        } else {
            return false;
        }
    }


}
