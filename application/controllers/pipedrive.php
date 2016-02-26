<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
define('PD_BASE_URL', 'https://api.pipedrive.com/v1/');

class Pipedrive extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tool/error', 'error');
    }

    public function send_post()
    {
        $required = $this->input->checkParam(array(
            'company',
            'person',
            'email',
            'users_size',
            'budget',
            'url'
        ));
        if ($required) {
            $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
        }
        $orgName = $this->input->post('company');
        $personName = $this->input->post('person');
        $email = $this->input->post('email');
        $userSize = $this->input->post('users_size');
        $budget = $this->input->post('budget');
        $url = $this->input->post('url');
        $token = $this->auth();
        if (!$token) {
            $this->response('pipedrive err: unable to login', 200);
        }
        $org = $this->createOrg($token, $orgName);
        if (!$token) {
            $this->response('pipedrive err: unable create organization', 200);
        }
        $person = $this->createPerson($token, $personName, $org, $email);
        if (!$person) {
            $this->response('pipedrive err: unable create person', 200);
        }
        $title = "Deal: $orgName - $url - $userSize users";
        $result = $this->createDeal($token, $title, $person, $org, $budget);
        $this->response($result, 200);
    }

    private function auth()
    {
        $data = array(
            'email' => 'rob@playbasis.com',
            'password' => 'r0b3rt21play'
        );
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/json",
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($opts);
        $file = file_get_contents(PD_BASE_URL . 'authorizations', false, $context);
        $obj = json_decode($file, true);
        if (!$obj['success']) {
            return false;
        }
        return $obj['data'][0]['api_token'];
    }

    private function createOrg($token, $name)
    {
        $data = array(
            'name' => $name
        );
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/json",
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($opts);
        $file = file_get_contents(PD_BASE_URL . 'organizations?api_token=' . $token, false, $context);
        $obj = json_decode($file, true);
        if (!$obj['success']) {
            return false;
        }
        return $obj['data']['id'];
    }

    private function createPerson($token, $name, $orgId, $email)
    {
        $data = array(
            'name' => $name,
            'org_id' => $orgId,
            'email' => $email
        );
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/json",
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($opts);
        $file = file_get_contents(PD_BASE_URL . 'persons?api_token=' . $token, false, $context);
        $obj = json_decode($file, true);
        if (!$obj['success']) {
            return false;
        }
        return $obj['data']['id'];
    }

    private function createDeal($token, $title, $personId, $orgId, $value)
    {
        $value = $value * 30;
        $data = array(
            'title' => $title,
            'value' => $value,
            'currency' => 'THB',
            'user_id' => 98788,
            'person_id' => $personId,
            'org_id' => $orgId
        );
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/json",
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($opts);
        $file = file_get_contents(PD_BASE_URL . 'deals?api_token=' . $token, false, $context);
        $obj = json_decode($file, true);
        return $obj['success'];
    }
}

?>