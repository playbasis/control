<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';
class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
	    $this->load->model('Client_model');
	    $this->load->model('Plan_model');
	    $this->load->model('Payment_model');

	    $router =& load_class('Router', 'core');
	    $method = $router->fetch_method();

        if(!$this->User_model->isLogged() && !in_array($method, array('paypal_notification'))){
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("account", $lang['folder']);
    }

    public function index() {

        if(!$this->validateAccess()){
            echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
        }

        $this->data['meta_description'] = $this->lang->line('meta_description');
        $this->data['title'] = $this->lang->line('title');
        $this->data['heading_title'] = $this->lang->line('heading_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');

	    $client = $this->Client_model->getClientById($this->User_model->getClientId());
	    $plan_registration = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
	    $plan = $this->Plan_model->getPlanById($plan_registration['plan_id']);

	    if (!array_key_exists('credit', $client)) {
		    $client['credit'] = 0; // default credit
	    }
	    if (!array_key_exists('price', $plan)) {
		    $plan['price'] = 99; // default plan price
	    }
	    $this->data['client'] = $client;
	    $this->data['plan'] = $plan;
	    $this->data['plan']['registration_date_added'] = $plan_registration['date_added']->sec;
	    $this->data['plan']['registration_date_modified'] = $plan_registration['date_modified']->sec;
	    $this->data['main'] = 'account';
	    $this->data['form'] = 'account/add_credit';
	    $this->session->set_userdata('price', $this->data['plan']['price']);
	    $this->load->vars($this->data);
	    $this->render_page('template');

	    // playbasis_client => store current credit amount
	    // playbasis_permission => to find associated plan of a client
	    // playbasis_plan => store plan details and price with active flags
	    // playbasis_payment_log => store payment transactions done by clients
	    // playbasis_payment_chennel => store all payment channels
    }

	public function add_credit() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['payment_title'] = $this->lang->line('payment_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		$this->data['main'] = 'account_purchase';
		$this->data['form'] = 'account/purchase';
		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function purchase() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['order_title'] = $this->lang->line('order_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		$this->form_validation->set_rules('credit', $this->lang->line('form_credit_to_add'), 'trim|required');
		$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
		$success = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->data['message'] = null;

			$credit = $this->input->post('credit');
			$channel = $this->input->post('channel');
			if ($credit) $credit = intval($credit);
			if ($credit <= 0) $this->data['message'] = 'Credit has to be greater than zero'; // manual validation (> 0)

			if($this->form_validation->run() && $this->data['message'] == null){
				$ci =& get_instance();
				$this->session->set_userdata('credit', $credit);
				$this->session->set_userdata('channel', $channel);
				$this->session->set_userdata('callback', $ci->config->config['server'].'notification');
				$this->Payment_model->add_credit_event($credit, $channel, 'Pending');
				switch ($channel) {
					case 'paypal':
						$this->data['main'] = 'account_purchase_paypal';
						$this->data['form'] = 'account/purchase';
						$success = true;
						break;
					default:
						break;
				}
			}
		}
		if (!$success) {
			$this->data['payment_title'] = $this->lang->line('payment_title');
			$this->data['main'] = 'account_purchase';
			$this->data['form'] = 'account/purchase';
		}
		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function paypal_done() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['wait_title'] = $this->lang->line('wait_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		/* clear basket in the session */
		$this->session->set_userdata('credit', null);
		$this->session->set_userdata('channel', null);
		$this->session->set_userdata('callback', null);

		$this->data['main'] = 'account_purchase_paypal_done';
		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function paypal_notification() {
		// TODO: handle IPN message
log_message('error', '--------- paypal_notification');

log_message('error', '_SERVER = '.print_r($_SERVER, true));
log_message('error', '_GET = '.print_r($_GET, true));
log_message('error', '_POST = '.print_r($_POST, true));

log_message('error', 'server = '.print_r($this->input->server() , true));
log_message('error', 'get = '.print_r($this->input->get() , true));
log_message('error', 'post = '.print_r($this->input->post() , true));
log_message('error', 'user_agent = '.print_r($this->input->user_agent() , true));

		$credit = 123; // from IPN
		$channel = 'paypal';
		$this->Payment_model->add_credit_event($credit, $channel, 'completed');

$body = file_get_contents('php://input');
log_message('error', 'body = '.print_r($body, true));
	}

    private function validateAccess(){
        return true;
    }
}
?>
