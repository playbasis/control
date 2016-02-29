<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * OAuth 2.0 authorisation server controller
 *
 * @author              Alex Bilbie | www.alexbilbie.com | alex@alexbilbie.com
 * @copyright        Copyright (c) 2012, Alex Bilbie.
 * @license             http://www.opensource.org/licenses/mit-license.php
 * @link                https://github.com/alexbilbie/CodeIgniter-OAuth-2.0-Server
 * @version             Version 0.2
 */
class Oauth extends CI_Controller
{

    /**
     * __construct function.
     *
     * @access public
     */
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('oauth_server');

        // Initialise a session item to keep IE happy
        $this->session->set_userdata('init', uniqid());
    }

    /**
     * This is the function that users are sent to when they first enter the flow
     */
    function index()
    {
        // Get query string parameters
        $params = array();

        // Client id
        if ($client_id = $this->input->get('client_id')) {
            $params['client_id'] = trim($client_id);
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See client_id.',
                null, array(), 400);
        }

        // Client redirect uri
        if ($redirect_uri = $this->input->get('redirect_uri')) {
            $params['redirect_uri'] = trim($redirect_uri);
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See redirect_uri.',
                null, array(), 400);
            return;
        }

        // Validate the response type
        if ($response_type = $this->input->get('response_type')) {
            $response_type = trim($response_type);
            $valid_response_types = array('code', 'token'); // array to allow for future expansion

            if (!in_array($response_type, $valid_response_types)) {
                $this->_fail('unsupported_response_type',
                    'The authorization server does not support obtaining an authorization code using this method. Supported response types are \'' . implode('\' or ',
                        $valid_response_types) . '\'.', null, array(), 400);
                return;
            } else {
                $params['response_type'] = $response_type;
            }
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See response_type.',
                null, array(), 400);
            return;
        }

        // Validate client_id and redirect_uri
        $client_details = $this->oauth_server->validate_client($params['client_id'], null,
            $params['redirect_uri']); // returns object or FALSE

        if ($client_details === false) {
            $this->_fail('unauthorized_client',
                'The client is not authorized to request an authorization code using this method.', null, array(), 403);
            return;
        } else {
            // The client is valid, save the details to the session
            $this->session->set_userdata('client_details', $client_details);
        }


        // Get and validate the scope(s)
        if ($scope_string = $this->input->get('scope')) {
            $scopes = explode(',', $scope_string);
            $params['scope'] = $scopes;
        } else {
            $params['scope'] = array();
        }

        // Check scopes are valid
        if (count($params['scope']) > 0) {
            foreach ($params['scope'] as $s) {
                $exists = $this->oauth_server->scope_exists($s);
                if (!$exists) {
                    $this->_fail('invalid_scope',
                        'The requested scope is invalid, unknown, or malformed. See scope \'' . $s . '\'.', null,
                        array(), 400);
                    return;
                }
            }
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See scope.',
                null, array(), 400);
            return;
        }

        // The client is valid, save the details to the session
        $this->session->set_userdata('client_details', $client_details);

        // Get the scope
        if ($state = $this->input->get('state')) {
            $params['state'] = trim($state);
        } else {
            $params['state'] = '';
        }

        // Save the params in the session
        $this->session->set_userdata(array('params' => $params));

        // Redirect the user to sign in
        redirect(site_url(array('oauth', 'sign_in')), 'location');
    }

    function sign_in()
    {
        $user_id = $this->session->userdata('user_id');
        $client = $this->session->userdata('client_details');

        // Check if user is signed in, if so redirect them on to /authorise
        if ($user_id && $client) {
            redirect(site_url(array('oauth', 'authorise')), 'location');
        }

        // Check there is are client parameters are stored
        if ($client == false) {
            $this->_fail('invalid_request', 'No client details have been saved. Have you deleted your cookies?', null,
                array(), 400);
            return;
        }

        // Errors
        $vars = array(
            'error' => false,
            'error_messages' => array(),
            'client_name' => $client->name
        );

        // If the form has been posted
        if ($this->input->post('validate_user')) {
            $u = trim($this->input->post('username', true));
            $p = trim($this->input->post('password', true));

            // Validate username and password
            if ($u == false || empty($u)) {
                $vars['error_messages'][] = 'The username field should not be empty';
                $vars['error'] = true;
            }

            if ($p == false || empty($p)) {
                $vars['error_messages'][] = 'The password field should not be empty';
                $vars['error'] = true;
            }

            // Check login and get credentials
            if ($vars['error'] == false) {
                $user = $this->oauth_server->validate_user($u, $p);

                if ($user == false) {
                    $vars['error_messages'][] = 'Invalid username and/or password';
                    $vars['error'] = true;
                } else {
                    $this->session->set_userdata(array(
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'non_ad_user' => true
                    ));
                }
            }

            // If there is no error then the user has successfully signed in
            if ($vars['error'] == false) {
                redirect(site_url(array('oauth', 'authorise')), 'location');
            }
        }

        $this->load->view('oauth_auth_server/sign_in', $vars);
    }


    /**
     * Sign the user out of the SSO service
     */
    function sign_out()
    {
        $this->session->sess_destroy();

        if ($redirect_uri = $this->input->get('redirect_uri')) {
            redirect($redirect_uri);
        } else {
            $this->load->view('oauth_auth_server/sign_out');
        }

    }


    /**
     * When the user has signed in they will be redirected here to approve the application
     */
    function authorise()
    {
        $user_id = $this->session->userdata('user_id');
        $client = $this->session->userdata('client_details');
        $params = $this->session->userdata('params');

        // Check if the user is signed in
        if ($user_id == false) {
            $this->session->set_userdata('sign_in_redirect', array('oauth', 'authorise'));
            redirect(site_url(array('oauth', 'sign_in')), 'location');
        }

        // Check the client params are stored
        if ($client == false) {
            $this->_fail('invalid_request', 'No client details have been saved. Have you deleted your cookies?', null,
                array(), 400);
            return;
        }

        // Check the request parameters are still stored
        if ($params == false) {
            $this->_fail('invalid_request', 'No client details have been saved. Have you deleted your cookies?', null,
                array(), 400);
            return;
        }

        // Has the user authorised the application?
        $doauth = $this->input->post('doauth');
        if ($doauth) {
            switch ($doauth) {
                // The user has approved the application.
                case "Approve":
                    $authorised = false;
                    $action = 'newrequest';
                    break;

                // The user has denied the application
                case "Deny":

                    $error_params = array(
                        'error' => 'access_denied',
                        'error_description' => 'The resource owner or authorization server denied the request.'
                    );
                    if ($params['state']) {
                        $error_params['state'] = $params['state'];
                    }

                    $redirect_uri = $this->oauth_server->redirect_uri($params['redirect_uri'], $error_params);
                    $this->session->unset_userdata(array(
                        'params' => '',
                        'client_details' => '',
                        'sign_in_redirect' => ''
                    ));
                    redirect($redirect_uri, 'location');

                    break;

            }
        } else {
            // Does the user already have an access token?
            $authorised = $this->oauth_server->access_token_exists($user_id, $client->client_id);

            if ($authorised) {
                $match = $this->oauth_server->validate_access_token($authorised->access_token, $params['scope']);
                $action = $match ? 'finish' : 'approve';
            } else {
                // Can the application be auto approved?
                $action = ($client->auto_approve == 1) ? 'newrequest' : 'approve';
            }
        }

        switch ($action) {
            case 'approve':

                $requested_scopes = $params['scope'];
                $scopes = $this->oauth_server->scope_details($requested_scopes);

                $vars = array(
                    'client_name' => $client->name,
                    'scopes' => $scopes
                );

                $this->load->view('oauth_auth_server/authorise', $vars);

                break;

            case 'newrequest':

                $code = $this->oauth_server->new_auth_code($client->client_id, $user_id, $params['redirect_uri'],
                    $params['scope'], $authorised->access_token);

                $this->fast_code_redirect($params['redirect_uri'], $params['state'], $code);

                break;

            case 'finish':

                $code = $this->oauth_server->new_auth_code($client->client_id, $user_id, $params['redirect_uri'],
                    $params['scope'], $authorised->access_token);

                $this->fast_token_redirect($params['redirect_uri'], $params['state'], $code);

                break;
        }
    }

    /**
     * Generate a new access token
     */
    function access_token()
    {
        // Get post query string parameters
        $params = array();

        // Client id
        if ($client_id = $this->input->post('client_id')) {
            $params['client_id'] = trim($client_id);
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See client_id.',
                null, array(), 400, 'json');
            return;
        }

        // Client secret
        if ($client_secret = $this->input->post('client_secret')) {
            $params['client_secret'] = trim($client_secret);
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See client_secret.',
                null, array(), 400, 'json');
            return;
        }

        // Client redirect uri
        if ($redirect_uri = $this->input->post('redirect_uri')) {
            $params['redirect_uri'] = urldecode(trim($redirect_uri));
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See redirect_uri.',
                null, array(), 400, 'json');
            return;
        }

        if ($code = $this->input->post('code')) {
            $params['code'] = trim($code);
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See code.',
                null, array(), 400, 'json');
            return;
        }

        // Validate the grant type
        if ($grant_type = $this->input->post('grant_type')) {
            $grant_type = trim($grant_type);

            if (!in_array($grant_type, array('authorization_code'))) {
                $this->_fail('invalid_request',
                    'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See grant_type.',
                    null, array(), 400, 'json');
                return;
            } else {
                $params['grant_type'] = $grant_type;
            }
        } else {
            $this->_fail('invalid_request',
                'The request is missing a required parameter, includes an invalid parameter value, or is otherwise malformed. See grant_type.',
                null, array(), 400, 'json');
            return;
        }

        // Validate client_id and redirect_uri
        $client_details = $this->oauth_server->validate_client($params['client_id'], $params['client_secret'],
            $params['redirect_uri']); // returns object or FALSE

        if ($client_details === false) {
            $this->_fail('unauthorized_client',
                'The client is not authorized to request an authorization code using this method', null, array(), 403,
                'json');
            return;
        }

        // Respond to the grant type
        switch ($params['grant_type']) {
            case "authorization_code":

                // Validate the auth code
                $session = $this->oauth_server->validate_auth_code($params['code'], $params['client_id'],
                    $params['redirect_uri']);

                if ($session === false) {
                    $this->_fail('invalid_request', 'The authorization code is invalid.', null, array(), 403, 'json');
                    return;
                }

                // Generate a new access_token (and remove the authorise code from the session)
                $access_token = $this->oauth_server->get_access_token($session->id);

                // Send the response back to the application
                $this->_response(array('access_token' => $access_token));
                return;

                break;
        }
    }

    /**
     * Generates a new auth code and redirects the user
     * Used in the web-server flow
     *
     * @access private
     * @param string $redirect_uri
     * @param string $state
     * @param string $code
     * @return void
     */
    private function fast_code_redirect($redirect_uri = '', $state = '', $code = '')
    {
        $redirect_uri = $this->oauth_server->redirect_uri($redirect_uri, array('code' => $code, 'state' => $state));
        $this->session->unset_userdata(array('params' => '', 'client_details' => '', 'sign_in_redirect' => ''));
        redirect($redirect_uri, 'location');
    }

    /**
     * Generates a new auth access token and redirects the user
     * Used in the user-agent flow
     *
     * @access private
     * @param string $redirect_uri
     * @param string $state
     * @param string $code
     * @return void
     */
    private function fast_token_redirect($redirect_uri = '', $state = '', $code = '')
    {
        $redirect_uri = $this->oauth_server->redirect_uri($redirect_uri, array('code' => $code, 'state' => $state),
            '#');
        $this->session->unset_userdata(array('params' => '', 'client_details' => '', 'sign_in_redirect' => ''));
        redirect($redirect_uri, 'location');
    }


    /**
     * Show an error message
     *
     * @access private
     * @param mixed $msg
     * @return string
     */

    private function _fail($error, $description, $url = null, $params = array(), $status = 400, $output = 'html')
    {
        if ($url) {
            $error_params = array(
                'error' . $error,
                'error_description' . urlencode($description)
            );

            $params = array_merge($params, $error_params);

            $this->oauth_server->redirect_uri($url, $params);

        } else {
            switch ($output) {
                case 'html':
                default:
                    show_error('[OAuth error: ' . $error . '] ' . $description, $status);
                    break;
                case 'json':
                    $this->output->set_status_header($status);
                    $this->output->set_output(json_encode(array(
                        'error' => 1,
                        'error_description' => '[OAuth error: ' . $error . '] ' . $description,
                        'access_token' => null
                    )));
                    break;
            }

        }
    }


    /**
     * JSON response
     *
     * @access private
     * @param mixed $msg
     * @return string
     */
    private function _response($msg)
    {
        $msg['error'] = 0;
        $msg['error_description'] = '';
        $this->output->set_status_header('200');
        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($msg));
    }

}