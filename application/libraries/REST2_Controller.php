<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';

/**
 * Playbasis REST2_Controller
 *
 * An extension on fully RESTful server implementation for implementing logging functionality for Playbasis API.
 *
 * @package            CodeIgniter
 * @subpackage        Libraries
 * @category        Libraries
 * @author            Thanakij Pechprasarn
 * @version         1.0.0
 */
abstract class REST2_Controller extends REST_Controller
{
    protected $validToken;
    protected $client_id;
    protected $site_id;
    protected $client_data;
    protected $client_date;
    protected $client_usage;
    protected $client_plan;
    protected $method_data;
    protected $app_enable;
    private $log_id;

    /**
     * Developers can extend this class and add a check in here.
     */
    protected function early_checks()
    {
        $token = $this->input->post('token'); // token: POST
        if(empty($token)){
            $token = $this->input->get('token');
        }
        $api_key = $this->input->get('api_key'); // api_key: GET/POST
        if (empty($api_key)) {
            $api_key = $this->input->post('api_key');
        }
        return $this->_early_checks($token, $api_key);
    }

    protected function _early_checks($token=null, $api_key=null)
    {
        /* 0.1 Load libraries */
        $this->load->model('rest_model');
        $this->load->model('auth_model');
        $this->load->model('setting_model');
        $this->load->model('client_model');
        $this->load->model('tool/error', 'error');

        /* 0.2 Adjust $this->request->body */
        if (!empty($this->request->body)) {
            if (is_array($this->request->body) && count(count($this->request->body) == 1) && array_key_exists(0, $this->request->body)) {
                $this->request->body = $this->request->body[0];
            }
            if (gettype($this->request->body) == 'string') {
                $str = trim($this->request->body);
                if ($str[0] == '{' && $str[strlen($str)-1] == '}') {
                    $this->request->body = $this->format->factory($this->request->body, 'json')->to_array();
                }
            }
        }

        /* 1.1 Log request */
        if (!$token && !$api_key) {
            $this->response($this->error->setError('PARAMETER_MISSING', array('api_key','token')), 200);
            return; // return early if neither token or api_key is found
        }
        $this->validToken = !empty($token) ? $this->auth_model->findToken($token) : (!empty($api_key) ? $this->auth_model->createTokenFromAPIKey($api_key) : null);
        $this->auth_method = $this->auth_model->auth_method;
        $this->client_id = !empty($this->validToken) ? $this->validToken['client_id'] : null;
        $this->site_id = !empty($this->validToken) ? $this->validToken['site_id'] : null;
        $this->app_enable = $this->setting_model->appStatus($this->client_id, $this->site_id);
        $client_setting = $this->setting_model->retrieveSetting($this->client_id,$this->site_id);
        $this->player_auth_enable = isset($client_setting['player_authentication_enable']) ? $client_setting['player_authentication_enable'] : false;
        $this->log_id = $this->rest_model->logRequest(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'api_key' => !empty($api_key) ? $api_key : null,
            'token' => !empty($token) ? $token : null,
            'class_name' => null,
            'class_method' => null,
            'method' => $this->request->method,
            'scheme' => isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http'),
            'uri' => $this->uri->uri_string(),
            'query' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null,
            'request' => !empty($this->request->body) ? $this->request->body : $_POST,
            'response' => null,
            'format' => null,
            'ip' => $this->input->ip_address(),
            'agent' => array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'server' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null
        ));

        /* 1.2 Client-Site Limit Requests */
        if (!$this->client_id || !$this->site_id) {
            return; // return early if not found client_id or site_id
        }

        /* 1.3 Check valid payment */
        $d = time();
        $this->client_date = $this->client_model->getClientStartEndDate($this->client_id);
        $flag = true; // default is assumed to be free, which is allowed to use API
        if ($this->client_date['date_start']) {
            $flag = $d >= $this->client_date['date_start']->sec;
        }
        if ($this->client_date['date_expire']) {
            $date_expire = strtotime("+".GRACE_PERIOD_IN_DAYS." day", $this->client_date['date_expire']->sec);
            $flag = $flag && ($d <= $date_expire);
        }
        if (!$flag) $this->response($this->error->setError("ACCESS_DENIED"), 200);

        /* 1.4 'requests' permission checking */
        $url = strtolower(preg_replace(
            "/(\w+)\/.*/", '${1}',
            $this->uri->uri_string()));
        if (substr($url, 0, 1) != "/") {
            $url = "/".$url;
        }
        try {
            $this->client_usage = $this->client_model->getClientSiteUsage($this->client_id, $this->site_id);
            $this->client_plan = $this->client_model->getPlanById($this->client_usage['plan_id']);
            $free_flag = !isset($this->client_plan['price']) || $this->client_plan['price'] <= 0;
            if ($free_flag) {
                $this->client_date = $this->client_model->adjustCurrentUsageDate($this->client_date['date_start']);
            }
            $this->client_data = array('date' => $this->client_date, 'usage' => $this->client_usage, 'plan' => $this->client_plan);
            $this->client_model->permissionProcess(
                $this->client_data,
                $this->client_id,
                $this->site_id,
                "requests",
                $url
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $this->response($this->error->setError(
                    "LIMIT_EXCEED", array()), 200);
            elseif ($e->getMessage() == "CLIENTSITE_NOTFOUND")
                $this->response($this->error->setError(
                    "CLIENTSITE_NOTFOUND", array()), 200);
            else {
                log_message('error', '[REST2::permissionProcess] error = '.$e->getMessage());
                $this->response($this->error->setError(
                    "INTERNAL_ERROR", array()), 200);
            }
        }

        /* 1.5 Check if mobile phone has been set-up for free accounts */
        if ($this->client_plan['_id'] == FREE_PLAN) {
            if (!$this->client_model->hasSetUpMobile($this->client_id) && time() >= strtotime(DATE_FREE_ACCOUNT_SHOULD_SETUP_MOBILE)) {
                $this->response($this->error->setError("SETUP_MOBILE"), 200);
            }
        }
    }

    protected function find_method_uri($method){
        $method_uri = "";
        if(is_array($method) && isset($method["parameters"]) && isset($method["URI"])){
            $method_uri = $method["URI"];
            foreach($method["parameters"] as $parameter){
                if($parameter["Required"] == "URI"){
                    if($parameter["Type"] == "integer" || $parameter["Type"] == "number"){
                        $method_uri = str_replace(":".$parameter["Name"], ANY_NUMBER, $method_uri);
                    }else{
                        $method_uri = str_replace(":".$parameter["Name"], ANY_STRING, $method_uri);
                    }
                }
            }
        }
        return $method_uri;
    }

    /**
     * Fire Method
     *
     * Fires the designated controller method with the given arguments.
     *
     * @param array $method The controller method to fire
     * @param array $args The arguments to pass to the controller method
     */
    protected function _fire_method($method, $args)
    {
        /* 1.2 Log class_name and method */
        $class_name = get_class($this);
        $this->rest_model->logResponse($this->log_id, $this->site_id, array(
            'class_name' => $class_name,
            'class_method' => $method[1],
        ));
        try {
            if (in_array($class_name, array('Auth', 'Quest', 'Quiz', 'Engine', 'Redeem', 'Game', 'Merchant')) && $this->app_enable === false && $this->request->method === "post"){
                $this->response($this->error->setError('SETTING_DISABLE'), 200);
            }
            /* 2.1 Validate request (basic common validation for all controllers) */
            if (!in_array($class_name, array('Auth', 'Facebook', 'Geditor', 'Instagram', 'Janrain', 'Mobile', 'Notification', 'Pipedrive', 'Playbasis'))) { // list of modules that don't require auth info
                // Check required parameter from pbapp.json
                if(isset($this->uri->router)) {
                    $json = file_get_contents(getcwd() . "/iodocs/public/data/pbapp.json");
                    $pbapp_data = json_decode($json, true);
                    $found_endpoint = false;
                    $missing_parameter = array();
                    if(isset($pbapp_data['endpoints']) && is_array($pbapp_data['endpoints'])) {
                        foreach ($pbapp_data['endpoints'] as $endpoint) {
                            if (in_array($method[0]->uri->segments[1], $endpoint['endpoint'])) {
                                foreach ($endpoint['methods'] as $end_method) {
                                    if (($this->uri->router == $this->find_method_uri($end_method)) && ($this->request->method == strtolower($end_method['HTTPMethod']))) {
                                        $found_endpoint = true;
                                        $this->method_data = $end_method;

                                        //TODO: condition below should be removed when API schema is done
                                        if (isset($this->method_data["response"])) {
                                            foreach ($end_method['parameters'] as $parameter) {
                                                // validate each parameter here
                                                if (strtoupper($parameter['Required']) == 'Y' && !isset($this->_args[$parameter['Name']])) {
                                                    array_push($missing_parameter, $parameter['Name']);
                                                }
                                            }
                                            if (isset($end_method['parameters_or'])) foreach ($end_method['parameters_or'] as $parameter_or) {
                                                $check_parameter_or = false;
                                                foreach ($parameter_or as $param_or) {
                                                    if (array_key_exists($param_or, $this->_args)) {
                                                        $check_parameter_or = true;
                                                    }
                                                }
                                                if (!$check_parameter_or) {
                                                    $param = implode(" or ", $parameter_or);
                                                    array_push($missing_parameter, $param);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                                if ($found_endpoint) break;
                            }
                        }
                    }
                    if (!$found_endpoint) {
                        //TODO: return error as below if the endpoint is not found in pbapp.json
                        //$this->response(array('status' => false, 'error' => 'Unknown method.'), 404);
                    }
                }

                $player_auth_failed = false;
                $ignore_player_auth = false;
                $auth_param_is_required = true;
                if($found_endpoint){
                    if( isset($this->method_data['PlayerAuthRequired']) && strtoupper($this->method_data['PlayerAuthRequired']) == 'Y'){
                        $player_id_to_check = null;
                        foreach($this->method_data['parameters'] as $parameter){
                            if($parameter['Name'] == $this->method_data['PlayerTokenCheckWith']){
                                if(strtoupper($parameter['Required'])  == "URI"){
                                    foreach(explode('/', $this->method_data['URI']) as $index => $key){
                                        if($key == ':'.$this->method_data['PlayerTokenCheckWith']){
                                            $player_id_to_check= $this->uri->segments[$index+1];
                                        }
                                    }
                                }else if(strtoupper($parameter['Required'])  == "Y"){
                                    $player_id_to_check = isset($_REQUEST[$this->method_data['PlayerTokenCheckWith']]) ? $_REQUEST[$this->method_data['PlayerTokenCheckWith']] : null;
                                }else{
                                    $player_id_to_check = isset($_REQUEST[$this->method_data['PlayerTokenCheckWith']]) ? $_REQUEST[$this->method_data['PlayerTokenCheckWith']] : null;
                                    $auth_param_is_required = false;
                                    if(is_null($player_id_to_check)){
                                        $ignore_player_auth = true;
                                    }
                                }
                                break;
                            }
                        }

                        if(!$ignore_player_auth) {
                            // get player_id attached in input token
                            $player_info = isset($this->validToken['pb_player_id']) ? $this->player_model->getPlayerByPlayer($this->site_id, $this->validToken['pb_player_id'], array('cl_player_id')) : array();

                            //verify if input player_id is valid with the input token
                            if (($this->auth_method == "player_token") && (!isset($player_info['cl_player_id']) || $player_info['cl_player_id'] != $player_id_to_check)) {
                                $player_auth_failed = true;
                            }
                        }
                    }
                }

                switch ($this->request->method) {
                    case 'get':
                        if(isset($this->player_auth_enable) && $this->player_auth_enable && isset($this->method_data['PlayerAuthRequired']) && (strtoupper($this->method_data['PlayerAuthRequired']) == 'Y') && $auth_param_is_required){
                            $required = $this->input->checkParam(array(
                                'token'
                            ));
                            if ($required)
                                $this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
                        }

                        if (!$this->validToken){
                            if($this->auth_method == "api_key"){
                                $this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
                            }else{
                                $this->response($this->error->setError('INVALID_TOKEN'), 200);
                            }
                        }

                        break;
                    case 'post': // every POST call requires 'token'
                        $required = $this->input->checkParam(array(
                            'token'
                        ));
                        if ($required)
                            $this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
                        if (!$this->validToken)
                            $this->response($this->error->setError('INVALID_TOKEN'), 200);
                        break;
                }

                if (!empty($missing_parameter)) {
                    $this->response($this->error->setError('PARAMETER_MISSING', $missing_parameter), 200);
                }
                if ($player_auth_failed) {
                    $this->response($this->error->setError('INVALID_TOKEN'), 200);
                }
            }
            /* 2.2 Process request */
            call_user_func_array($method, $args);
        } catch (Exception $e) {
            ini_set('mongo.allow_empty_keys', TRUE);
            $this->load->model('tool/error', 'error');
            $msg = $e->getMessage();
            log_message('error', $msg);
            /* 3.2 Log response (exception) */
            $data = $this->error->setError('INTERNAL_ERROR', $msg);
            $this->rest_model->logResponse($this->log_id, $this->site_id, array(
                'response' => $data,
                'format' => $this->response->format,
                'error' => $e->getTraceAsString(),
            ));
            $this->response($data, 200);
        }
    }

    private function format_data($data, $format)
    {
        $output = $data;

        // If the format method exists, call and return the output in that format
        if (method_exists($this, '_format_'.$format))
        {
            // Set the correct format header
            header('Content-Type: '.$this->_supported_formats[$format]);

            $output = $this->{'_format_'.$format}($data);
        }

        // If the format method exists, call and return the output in that format
        elseif (method_exists($this->format, 'to_'.$format))
        {
            // Set the correct format header
            header('Content-Type: '.$this->_supported_formats[$format]);

            $output = $this->format->factory($data)->{'to_'.$format}();
        }

        return $output;
    }

    private function is_type_match($expected_type, $parameter_type){
        $result = false;
        if($expected_type == $parameter_type){
            $result = true;
        }elseif($parameter_type == "array_list" || $parameter_type == "array_dynamic"){
            $result = ($expected_type == "array");
        }
        return $result;
    }

    /**
     * Response
     *
     * Takes pure data and optionally a status code, then creates the response.
     *
     * @param array $data
     * @param null|int $http_code
     */

    private function check_response(&$static_pointer_data, &$pointer_data, $data_head, $check_response, $check_head, &$response_result, &$is_error) {

        if(isset($check_response[$check_head]['-type'])){
            if ( !is_null($pointer_data[$data_head]) && !empty($pointer_data[$data_head]) && (((gettype($pointer_data[$data_head]) != $check_response[$check_head]["-type"])
                 && $check_response[$check_head]["-type"] != "any" && $check_response[$check_head]["-type"] != "continue" && $check_response[$check_head]["-type"] != "number")
                 || ($check_response[$check_head]["-type"] == "number" && gettype($pointer_data[$data_head]) != "integer" && gettype($pointer_data[$data_head]) != "double" && gettype($pointer_data[$data_head]) != "long"))
            ){
                $is_error = true;
                if (isset($this->_args["debug"]) && $this->_args["debug"] == DEBUG_KEY){
                    $static_pointer_data = $this->error->setError('INTERNAL_ERROR', "Response type invalid, ".strtoupper($data_head)." return type " .gettype($pointer_data[$data_head]). " instead of ". $check_response[$check_head]["-type"]);
                } else {
                    $static_pointer_data = $this->error->setError('INTERNAL_ERROR', "Response type invalid");
                }

            }else {
                $response_result[$data_head] = $pointer_data[$data_head];
                return $check_response[$check_head]["-type"];
            }

        }else{
            $response_result[$data_head] = is_null($pointer_data[$data_head]) ? null : array();
            if(!is_null($pointer_data[$data_head]) && !empty($pointer_data[$data_head])) {
                if (isset($check_response[$check_head][0])) {
                    foreach ($pointer_data[$data_head] as $key => $value) {
                        if($is_error){
                            break;
                        }
                        $this->check_response($static_pointer_data, $pointer_data[$data_head], $key, $check_response[$check_head], 0, $response_result[$data_head], $is_error);
                    }
                } else {
                    foreach ($check_response[$check_head] as $key => $value) {
                        if($is_error){
                            break;
                        }
                        $type = null;
                        $matches = preg_grep('/\b' . $key . '\b/', array_keys($pointer_data[$data_head]));

                        if (!$matches && ($key != "-optional") && ($key != "[a-zA-Z0-9-%_:\.]+")
                            && !(array_key_exists('-optional', $check_response[$check_head][$key])
                                && $check_response[$check_head][$key]['-optional'] == "true")
                            && !(array_key_exists('-type', $check_response[$check_head][$key]) && $check_response[$check_head][$key]['-type'] == "continue")
                            && !(isset($check_response[$check_head][$key][1]) && array_key_exists('-optional', $check_response[$check_head][$key][1]) && $check_response[$check_head][$key][1]['-optional'] == "true")
                        ) {
                            $is_error = true;
                            if (isset($this->_args["debug"]) && $this->_args["debug"] == DEBUG_KEY){
                                $static_pointer_data = $this->error->setError('INTERNAL_ERROR', "Response result(s) missing ".strtoupper($key));
                            } else {
                                $static_pointer_data = $this->error->setError('INTERNAL_ERROR', "Response result(s) missing");
                            }
                            break;
                        }
                        foreach ($matches as $match) {
                            if($is_error){
                                break;
                            }
                            $type = $this->check_response($static_pointer_data, $pointer_data[$data_head], $match, $check_response[$check_head], $key, $response_result[$data_head], $is_error);
                            unset($pointer_data[$data_head][$match]);
                        }

                        if ($type == "continue") {
                            break;
                        }
                    }
                }
            }
        }

        return "nothing";
    }

    public function response($data = array(), $http_code = null)
    {
        global $CFG;

        // If data is empty and not code provide, error and bail
        if (empty($data) && $http_code === null)
        {
            $http_code = 404;

            // create the output variable here in the case of $this->response(array());
            $output = NULL;
        }

        // Otherwise (if no data but 200 provided) or some data, carry on camping!
        else
        {
            // Is compression requested?
            if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE)
            {
                if (extension_loaded('zlib'))
                {
                    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
                    {
                        ob_start('ob_gzhandler');
                    }
                }
            }

            is_numeric($http_code) OR $http_code = 200;

            $response_result = array();
            $is_error = false;

            if($this->method_data && isset($this->method_data["response"]) && $data['success'] == true) {
                $this->check_response($data, $data, "response", $this->method_data, "response", $response_result, $is_error);
                if (!$is_error) {
                    $data["response"] = $response_result["response"];
                }
            }

            
            $output = $this->format_data($data, $this->response->format);
        }

        header('HTTP/1.1: ' . $http_code);
        header('Status: ' . $http_code);
        header('Access-Control-Allow-Origin: *'); // allow cross domain
        header_remove('Set-Cookie');

        // If zlib.output_compression is enabled it will compress the output,
        // but it will not modify the content-length header to compensate for
        // the reduction, causing the browser to hang waiting for more data.
        // We'll just skip content-length in those cases.
        if ( ! $this->_zlib_oc && ! $CFG->item('compress_output'))
        {
            header('Content-Length: ' . strlen($output));
        }
        /* 3.1 Log response (actual output) */
         ini_set('mongo.allow_empty_keys', TRUE); // allow empty keys to be inserted in MongoDB (for example, insight needs this)
        $this->rest_model->logResponse($this->log_id, $this->site_id, array(
            'response' => $data,
            'format' => $this->response->format,
        ));

        exit($output);
    }
}
?>
