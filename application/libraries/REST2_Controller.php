<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';

/**
 * Playbasis REST2_Controller
 *
 * An extension on fully RESTful server implementation for implementing logging functinoality for Playbasis project.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Thanakij Pechprasarn
 * @version 		1.0.0
 */
abstract class REST2_Controller extends REST_Controller
{
	protected $validToken;
	protected $client_id;
	protected $site_id;
	private $log_id;

	/**
	 * Constructor function
	 * @todo Document more please.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Developers can extend this class and add a check in here.
	 */
	protected function early_checks()
	{
		$this->load->model('REST_model');
		$this->load->model('auth_model');
		/* [1] 1. Log request */
		$token = $this->input->post('token'); // token: POST
		$api_key = $this->input->get('api_key'); // api_key: GET/POST
		if (empty($api_key)) {
			$api_key = $this->input->post('api_key');
		}
		$this->validToken = !empty($token) ? $this->auth_model->findToken($token) : (!empty($api_key) ? $this->auth_model->createTokenFromAPIKey($api_key) : null);
		$this->client_id = !empty($this->validToken) ? $this->validToken['client_id'] : null;
		$this->site_id = !empty($this->validToken) ? $this->validToken['site_id'] : null;
		$this->log_id = $this->REST_model->logRequest(array(
			'client_id' => $this->client_id,
			'site_id' => $this->site_id,
			'api_key' => !empty($api_key) ? $api_key : null,
			'token' => !empty($token) ? $token : null,
			'class_name' => null,
			'class_method' => null,
			'method' => $this->request->method,
			'scheme' => $_SERVER['REQUEST_SCHEME'],
			'uri' => $this->uri->uri_string(),
			'query' => $_SERVER['QUERY_STRING'],
			'request' => $this->request->body,
			'response' => null,
			'format' => null,
			'ip' => $this->input->ip_address(),
			'agent' => array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : null,
		));
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
		/* [1] 2. Log class_name and method */
		$class_name = get_class($this);
		$this->REST_model->logResponse($this->log_id, $this->site_id, array(
			'class_name' => $class_name,
			'class_method' => $method[1],
		));
		try {
			/* [2] 1. Validate request (basic common validation for all controllers) */
			if (!in_array($class_name, array('Auth', 'Engine', 'Facebook', 'Geditor', 'Instagram', 'Janrain', 'Mobile', 'Pipedrive'))) { // every POST call (with exceptions like /Auth/) requires 'token'
				switch ($this->request->method) {
				case 'get': // every GET call requires 'api_key'
					$required = $this->input->checkParam(array(
						'api_key'
					));
					if ($required)
						$this->response($this->error->setError('PARAMETER_MISSING', $required), 200);
					if (!$this->validToken)
						$this->response($this->error->setError('INVALID_API_KEY_OR_SECRET'), 200);
					break;
				case 'post':
					$required = $this->input->checkParam(array(
						'token'
					));
					if ($required)
						$this->response($this->error->setError('TOKEN_REQUIRED', $required), 200);
					if (!$this->validToken)
						$this->response($this->error->setError('INVALID_TOKEN'), 200);
					break;
				}
			}
			/* [2] 2. Process request */
			call_user_func_array($method, $args);
		} catch (Exception $e) {
			$data = $e->getMessage(); // TODO: reformat output
			log_message('error', $data);
			/* [3] Log response (exception) */
			$output = $this->format_data($data, $this->response->format);
			$this->REST_model->logResponse($this->log_id, $this->site_id, array(
				'response' => $output,
				'format' => $this->response->format,
			));
			$this->response($data);
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

	/**
	 * Response
	 *
	 * Takes pure data and optionally a status code, then creates the response.
	 *
	 * @param array $data
	 * @param null|int $http_code
	 */
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

			$output = $this->format_data($data, $this->response->format);
		}

		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);

		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ( ! $this->_zlib_oc && ! $CFG->item('compress_output'))
		{
			header('Content-Length: ' . strlen($output));
		}

		/* [3] Log response (actual output) */
		$this->REST_model->logResponse($this->log_id, $this->site_id, array(
			'response' => $output,
			'format' => $this->response->format,
		));

		exit($output);
	}

}
