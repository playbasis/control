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

        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Workflow_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("workflow", $lang['folder']);
        $this->load->model('Store_org_model');
        $this->load->model('Feature_model');
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($player['cl_player_id'] );
            if($result->success){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer( $player['cl_player_id']);
                    if(!$result->success){
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

        $this->getPlayerList("approved");

    }

    public function rejected() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($player['cl_player_id'] );
            if($result->success){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/rejected', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer( $player['cl_player_id']);
                    if(!$result->success){
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

        $this->getPlayerList("rejected");

    }

    public function pending() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($player['cl_player_id'] );
            if($result->success){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/pending', 'refresh');
        }
        // incase: select player(s) to approve/reject/delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action')=="approve"){
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->approvePlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_approve'));
                redirect('/workflow/pending', 'refresh');
            }
            elseif($this->input->post('action')=="reject") {
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->rejectPlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_reject'));
                redirect('/workflow/pending', 'refresh');
            }
            elseif($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer( $player['cl_player_id']);
                    if(!$result->success){
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

        $this->getPlayerList("pending");

    }
    public function locked() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
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

        if(!$this->validateModify()){
            $this->error['warning'] = $this->lang->line('error_permission');
        }

        // incase: click delete direct player
        if($this->input->post('user_id')){
            $player = $this->Player_model->getPlayerById($this->input->post('user_id'), $site_id);
            $result = $this->Workflow_model->deletePlayer($player['cl_player_id'] );
            if($result->success){
                $this->session->set_flashdata('success', $this->lang->line('text_success_delete'));
            }else{
                $this->session->set_flashdata('fail', $this->lang->line('text_fail_delete'));
            }
            redirect('/workflow/locked', 'refresh');
        }
        // incase: select player(s) to delete
        elseif ($this->input->post('selected') && $this->error['warning'] == null) {
            $selectedUsers = $this->input->post('selected');

            if ($this->input->post('action')=="unlock"){
                foreach ($selectedUsers as $selectedUser){
                    $this->Workflow_model->unlockPlayer($client_id, $site_id, $selectedUser);
                }

                $this->session->set_flashdata('success', $this->lang->line('text_success_approve'));
                redirect('/workflow/locked', 'refresh');
            }
            elseif($this->input->post('action')=="delete") {
                foreach ($selectedUsers as $selectedUser){
                    $player = $this->Player_model->getPlayerById($selectedUser, $site_id);
                    $result = $this->Workflow_model->deletePlayer( $player['cl_player_id']);
                    if(!$result->success){
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

        $this->getPlayerList("approved",true);

    }

    private function getPlayerList($status , $locked=false) {
        $this->data['player_list'] = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $this->data['tab_status'] =  $locked? "locked":$status;
        if ($locked){
            $this->data['player_list'] = $this->Workflow_model->getLockedPlayer($client_id,$site_id,$status);
        }elseif($status=='pending') {
            $this->data['player_list'] = $this->Workflow_model->getPendingPlayer($client_id,$site_id);
        }else{
            $this->data['player_list'] = $this->Workflow_model->getPlayerByApprovalStatus($client_id,$site_id,$status);
        }


        if ($this->User_model->hasPermission('access','store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
            foreach($this->data['player_list'] as &$player){
                $org_info = $this->Workflow_model->getOrganizationToPlayer($client_id,$site_id,$player['_id']);
                foreach($org_info as $org){
                    $role_string='';
                    if(isset($org['roles'])&&!empty($org['roles'])){
                        $array = array_keys($org['roles']);
                        foreach($array as $role){
                            if($role_string == ''){
                                $role_string = $role;
                            }else{
                                $role_string = $role_string.', '.$role;
                            }
                        }
                    }

                    $node_info = $this->Store_org_model->retrieveNodeById($org['node_id']);

                    if(!isset($player['organization'])){
                        $player['organization']=$node_info['name'].' ('.$role_string.')';
                    }else{
                        $player['organization']=$player['organization'].'<br>'.$node_info['name'].' ('.$role_string.')';
                    }
                }
            }
        }else{
            $this->data['org_status'] = false;
        }

        $pending_count = count($this->Workflow_model->getPendingPlayer($client_id,$site_id));
        $this->data['pending_count'] =$pending_count;
        $this->data['locked_count'] = count($this->Workflow_model->getLockedPlayer($client_id,$site_id,$status));


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

        $this->data['main'] = 'workflow';

        $this->load->vars($this->data);
        $this->render_page('template');
    }

    private function findPbPlayerId($player_id)
    {
        //$this->load->model('Player_model');
        $pb_player_id = $this->Player_model->getPlaybasisId( array(
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

    public function edit_account($user_id) {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_edit');
        $this->data['form'] = 'workflow/edit_account/'.$user_id;
        $this->data['action'] = 'edit';

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $this->input->post();
            $check_status = true;

            // initial validate whether role is set without node selection
            if($this->User_model->hasPermission('access','store_org') && $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org') ) {
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

            if($check_status == true){
                $status = $this->Workflow_model->editPlayer($data['cl_player_id'], $data);

                if ($status->success) {
                    if ($this->User_model->hasPermission('access', 'store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org'))
                    {
                        foreach($data['organize_id'] as $i => $org_id){
                            //Add player to node
                            if (isset($data['organize_node'][$i]) && !empty($data['organize_node'][$i]) && $status->success) {

                                $pb_player_id = $this->findPbPlayerId($data['cl_player_id']);
                                if($org_id == "") {// this player has never been added to any node
                                    $this->Workflow_model->addPlayerToNode($data['cl_player_id'], $data['organize_node'][$i]);
                                }else{ //this player has been added to some node
                                    $this->Workflow_model->editOrganizationOfPlayer($client_id, $site_id, $data['organize_id'][$i], $pb_player_id, $data['organize_node'][$i]);
                                }

                                //set role of player
                                if (isset($data['organize_role'][$i]) && !empty($data['organize_role'][$i])) {

                                    $temp = $this->Workflow_model->getRole($client_id, $site_id,$pb_player_id,$data['organize_node'][$i]);
                                    if($temp != null) {
                                        $this->Workflow_model->clearPlayerRole($client_id, $site_id, $pb_player_id, $data['organize_node'][$i]);
                                    }

                                    $role_array = explode(",", $data['organize_role'][$i]);
                                    foreach ($role_array as $role) {
                                        $role = str_replace(' ', '', $role);
                                        $status = $this->Workflow_model->setPlayerRole($data['cl_player_id'], $data['organize_node'][$i], $role);
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
                    $_POST['organize_node'][$i] = "";
                    $this->data['message'] = $status->message;
                }
            }
        }
        $this->getForm($user_id);
    }

    public function create_account() {
        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title_create');
        $this->data['form'] = 'workflow/create_account/';
        $this->data['action'] = 'create';

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $this->input->post();
            //set value of username to equal to cl_player_id
            $data['username'] = $data['cl_player_id'];

            if($data['password']!=$data['confirm_password']){
                $this->data['message'] = $this->lang->line('text_fail_confirm_password');
                if(!isset($_POST['organize_node'][0]))
                    $_POST['organize_node'][0] = "";
            }elseif($this->User_model->hasPermission('access','store_org') &&
                    $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org') &&
                    (isset($data['organize_role'][0]) && !empty($data['organize_role'][0]))&&
                    !(isset($data['organize_node'][0]) && !empty($data['organize_node'][0]))) {
                $_POST['organize_node'][0] = "";
                $this->data['message'] = $this->lang->line('text_fail_set_role');

            }else{
                $status = $this->Workflow_model->createPlayer($data);

                if($status->success) {
                    if ($this->User_model->hasPermission('access','store_org') &&
                        $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
                    ) {
                        //Add player to node
                        if (isset($data['organize_node'][0]) && !empty($data['organize_node'][0])) {
                            $status = $this->Workflow_model->addPlayerToNode($data['cl_player_id'], $data['organize_node'][0]);
                            if ($status->success) {
                                //set role of player
                                if (isset($data['organize_role'][0]) && !empty($data['organize_role'][0])) {
                                    $role_array = explode(",", $data['organize_role'][0]);
                                    $status1 = null;
                                    foreach ($role_array as $role) {
                                        $role = str_replace(' ', '', $role);
                                        $status1 = $this->Workflow_model->setPlayerRole($data['cl_player_id'], $data['organize_node'][0], $role);
                                        if (!$status1->success) {
                                            break;
                                        }
                                    }
                                    if ($status1->success) {
                                        // created player, added player to the node and set role of the player
                                        $this->session->set_flashdata('success', $this->lang->line('text_success_create'));
                                        redirect('/workflow', 'refresh');
                                    } else {
                                        // failed to set role of player
                                        $this->data['message'] = $status1->message;
                                    }
                                } else {
                                    //created player and added player to the node but did not set role of the player
                                    $this->session->set_flashdata('success', $this->lang->line('text_success_create_without_role_setting'));
                                    redirect('/workflow', 'refresh');
                                }
                            } else {
                                //failed to add player to node
                                $this->data['message'] = $status->message;
                            }
                        }else{
                            //created player with enabled store org feature but did not add the player to any node
                            $this->session->set_flashdata('success', $this->lang->line('text_success_create_without_org_setting'));
                            redirect('/workflow', 'refresh');
                        }
                    }else{
                        // created player with disabled store org feature
                        $this->session->set_flashdata('success', $this->lang->line('text_success_create'));
                        redirect('/workflow', 'refresh');
                    }
                }else{
                    // failed to create player
                    if(!isset($_POST['organize_node'][0]))
                        $_POST['organize_node'][0] = "";
                    $this->data['message'] = $status->message;
                }
            }
        }

        $this->getForm();
    }

    public function getForm($user_id=0){

        if ($this->User_model->hasPermission('access','store_org') &&
            $this->Feature_model->getFeatureExistByClientId($this->User_model->getClientId(), 'store_org')
        ) {
            $this->data['org_status'] = true;
        }else{
            $this->data['org_status'] = false;
        }

        $this->data['requester'] = array();

        if (isset($_POST['username'])) {
            $this->data['requester'] = $_POST;

            if($this->data['org_status']) {
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

        }elseif($user_id !=0){
            $this->data['requester'] = $this->Player_model->getPlayerById($user_id);
            if ($this->data['org_status']){
                $client_id = $this->User_model->getClientId();
                $site_id = $this->User_model->getSiteId();

                $org_info = $this->Workflow_model->getOrganizationToPlayer($client_id, $site_id, new MongoId($user_id));
                if(isset($org_info)&&!empty($org_info)){

                    foreach($org_info as $org){
                        $this->data['organize_id'][] = $org['_id'];
                        $this->data['organize_node'][] = $org['node_id'];
                        $node = $this->Store_org_model->retrieveNodeById(new MongoId($org['node_id']));
                        $this->data['organize_type'][] = $node["organize"];

                        if(isset($org['roles'])&&!empty($org['roles'])) {
                            $array_role = array_keys($org['roles']);
                            $role_string = '';
                            foreach($array_role as $role){
                                if($role_string == ''){
                                    $role_string = $role;
                                }else{
                                    $role_string = $role_string.','.$role;
                                }
                            }
                            $this->data['organize_role'][] = $role_string;
                        }else{
                            $this->data['organize_role'][] = "";
                        }
                    }
                }else{
                    $this->data['organize_id'][] = "";
                    $this->data['organize_type'][] = "";
                    $this->data['organize_node'][] = "";
                    $this->data['organize_role'][] = "";
                }
            }
        }else{
            $this->data['requester'] = array('approve_status'=>'approved','gender'=>'male');
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

    private function validateModify() {

        if ($this->User_model->hasPermission('modify', 'user')) {
            return true;
        } else {
            return false;
        }
    }

    private function validateAccess(){
        if($this->User_model->isAdmin()){
            return true;
        }
        $this->load->model('Feature_model');
        $client_id = $this->User_model->getClientId();

        if ($this->User_model->hasPermission('access', 'goods') &&  $this->Feature_model->getFeatureExistByClientId($client_id, 'workflow')) {
            return true;
        } else {
            return false;
        }
    }




}
