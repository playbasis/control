<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('DEFAULT_PLAN_PRICE', 0); // default is free package
define('DEFAULT_TRIAL_DAYS', 0); // default is having no trial period

class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
	    $this->load->model('Client_model');
	    $this->load->model('Plan_model');

	    $router =& load_class('Router', 'core');
	    $method = $router->fetch_method();

        if(!$this->User_model->isLogged() && !in_array($method, array('paypal_notification'))){
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("account", $lang['folder']);
    }

    /*
        playbasis_client - store client's subscription status
        playbasis_permission - store the current plan of a client
        playbasis_plan - store plan details and plan price (note: plans with active flags will show in 1st page of playbasis.com)
        playbasis_payment_log - store payment transactions (due to their subscription plan)
        playbasis_notification_log - store all PayPal IPN messages
        playbasis_payment_channel - store all available payment channels
    */

    /*
        The most common payment statuses (https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=xpt/Help/popup/StatusTypes)

        Canceled: The sender canceled this payment.
        Completed (referring to a bank withdrawal): Money is being transferred to your bank account. Allow up to 7 days for this transfer to complete.
        Completed (referring to a payment): Money has been successfully sent to the recipient.
        Denied: The recipient chose not to accept this payment.
        Held: Money is being temporarily held. The sender may be disputing this payment, or the payment may be under review by PayPal.
        Pending: This payment is being processed. Allow up to 4 days for it to complete.
        Returned: Money was returned to the sender because the payment was unclaimed for 30 days.
        Unclaimed: The recipient hasn't yet accepted this payment.
    */

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
	    if (!array_key_exists('price', $plan)) {
		    $plan['price'] = DEFAULT_PLAN_PRICE;
	    }
	    $this->session->set_userdata('plan', $plan);
	    $trial_days = array_key_exists('limit_others', $plan) && array_key_exists('trial', $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
	    $remaining_days = $this->find_remaining_days_after_trial($plan_registration['date_modified']->sec, $trial_days);
	    $this->data['client'] = $client;
	    $this->data['client']['date_added'] = $client['date_added']->sec;
	    $this->data['client']['date_modified'] = $client['date_modified']->sec;
	    $this->data['client']['trial_flag'] = $remaining_days > 0;
	    $this->data['client']['trial_days'] = $trial_days;
	    $this->data['client']['trial_remaining_days'] = $remaining_days;
	    $this->data['plan'] = $plan;
	    $this->data['plan']['registration_date_added'] = $plan_registration['date_added']->sec;
	    $this->data['plan']['registration_date_modified'] = $plan_registration['date_modified']->sec;
	    $this->data['main'] = 'account';
	    $this->data['form'] = 'account/subscribe';
	    $this->session->set_userdata('price', $this->data['plan']['price']);
	    $this->load->vars($this->data);
	    $this->render_page('template');
    }

	public function subscribe() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['subscribe_title'] = $this->lang->line('subscribe_title');
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

		$this->form_validation->set_rules('months', $this->lang->line('form_months'), 'trim|required');
		$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
		$success = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->data['message'] = null;

			$months = $this->input->post('months');
			$channel = $this->input->post('channel');
			if ($months) $months = intval($months);
			if ($months <= 0) $this->data['message'] = 'Parameter "months" has to be greater than zero'; // manual validation (> 0)

			if($this->form_validation->run() && $this->data['message'] == null){
				$ci =& get_instance();
				$this->session->set_userdata('months', $months);
				$this->session->set_userdata('channel', $channel);
				$this->session->set_userdata('callback', $ci->config->config['server'].'notification');
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
		} else {
			$this->data['message'] = 'You can only access purchase page with POST method';
		}
		if (!$success) {
			$this->data['subscribe_title'] = $this->lang->line('subscribe_title');
			$this->data['main'] = 'account_purchase';
			$this->data['form'] = 'account/purchase';
		}
		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function paypal_completed() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['wait_title'] = $this->lang->line('wait_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		/* clear basket in the session */
		$this->session->set_userdata('plan', null);
		$this->session->set_userdata('months', null);
		$this->session->set_userdata('channel', null);
		$this->session->set_userdata('callback', null);

		$this->data['main'] = 'account_purchase_paypal_done';
		$this->load->vars($this->data);
		$this->render_page('template');
	}

	private function find_remaining_days_after_trial($date_added_sec, $days) {
		$begin = new DateTime(date("Y-m-d", $date_added_sec));
		$now = new DateTime(date("Y-m-d"));
		$interval = $begin->diff($now);
		$interval_ndays = intval($interval->format('%R%a'));
		return $days - $interval_ndays;
	}

    private function validateAccess(){
        return true;
    }
}
?>
