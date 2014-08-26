<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

define('DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET', true);

class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
	    $this->load->model('Client_model');
	    $this->load->model('Plan_model');

        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("account", $lang['folder']);
    }

    /*
        playbasis_client - store client's subscription status
        playbasis_permission - store the current plan of a client
        playbasis_plan - store plan details and plan price (note: also 'display' flags, which is to determine whether to feature the plan in sign-up page of playbasis.com)
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
        $this->data['heading_title'] = $this->lang->line('account_title');
        $this->data['text_no_results'] = $this->lang->line('text_no_results');
        $this->data['main'] = 'account';
        $this->data['form'] = 'account/subscribe';

	    /* find details of the subscribed plan of the client */
	    $plan_subscription = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
	    $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
	    if (!array_key_exists('price', $plan)) {
		    $plan['price'] = DEFAULT_PLAN_PRICE;
	    }
	    $price = $plan['price'];
	    $this->session->set_userdata('plan', $plan);
	    $plan_days_total = array_key_exists('limit_others', $plan) && array_key_exists('trial', $plan['limit_others']) ? $plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
	    $plan_free_flag = $price <= 0;
	    $plan_paid_flag = !$plan_free_flag;
	    $plan_trial_flag = $plan_paid_flag && $plan_days_total > 0;

	    /* find details of the client */
	    $client = $this->Client_model->getClientById($this->User_model->getClientId());
	    // "date_start" and "date_expire" will be set when we receive payment confirmation in each month
	    // So if whenever payment fails, the two fields would not be updated, which results in block API usage.
	    // In addition, "date_expire" will include additional days for grace period.
	    $date_start = array_key_exists('date_start', $client) ? $client['date_start']->sec : null;
	    $date_expire = array_key_exists('date_expire', $client) ? $client['date_expire']->sec : null;
	    // Whenever we set "date_billing", it means that the client has already set up subscription.
	    // The date will be immediately after the trial period (if exits),
	    // of which the date is the first day of the client in billing period of the plan.
	    // After the billing period has ended, "date_billing" is unset from client's record,
	    // so the client has to extend the subscription before contract expires.
	    $date_billing = array_key_exists('date_billing', $client) ? $client['date_billing']->sec : null;
	    $days_remaining = $this->find_diff_in_days(time(), $date_billing);

	    $this->data['client'] = $client;
	    $this->data['client']['valid'] = ($plan_free_flag || ($date_billing && $this->check_valid_payment($client)));
	    $this->data['client']['trial_remaining_days'] = $days_remaining;
	    $this->data['client']['date_billing'] = $date_billing;
	    $this->data['client']['date_start'] = $date_start;
	    $this->data['client']['date_expire'] = $date_expire;
	    $this->data['client']['date_added'] = $client['date_added']->sec;
	    $this->data['client']['date_modified'] = $client['date_modified']->sec;
	    $this->data['plan'] = $plan;
	    $this->data['plan']['free_flag'] = $plan_free_flag;
	    $this->data['plan']['paid_flag'] = $plan_paid_flag;
	    $this->data['plan']['trial_flag'] = $plan_trial_flag;
	    $this->data['plan']['trial_total_days'] = $plan_days_total;
	    $this->data['plan']['date_added'] = $plan_subscription['date_added']->sec;
	    $this->data['plan']['date_modified'] = $plan_subscription['date_modified']->sec;

	    $this->load->vars($this->data);
	    $this->render_page('template');
    }

	public function subscribe() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['heading_title'] = $this->lang->line('channel_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');
		$this->data['main'] = 'account_purchase';
		$this->data['form'] = 'account/purchase';

		$plan = $this->session->userdata('plan');
		$free_flag = ($plan['price'] <= 0);

		if ($free_flag) {
			$this->data['plans'] = $this->Plan_model->listDisplayPlans();
		}

		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function cancel_subscription() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['heading_title'] = $this->lang->line('cancel_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->data['message'] = null;

			$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
			$channel = $this->input->post('channel');

			if($this->form_validation->run() && $this->data['message'] == null){
				switch ($channel) {
					case PAYMENT_CHANNEL_PAYPAL:
						$this->data['main'] = 'account_cancel_subscription_paypal';
						break;
					default:
						$this->data['message'] = 'Invalid payment channel';
						break;
				}
			}
		} else {
			$this->data['main'] = 'account_cancel_subscription';
			$this->data['form'] = 'account/cancel_subscription';
		}

		$this->load->vars($this->data);
		$this->render_page('template');
	}

	public function purchase() {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['heading_title'] = $this->lang->line('order_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		$this->form_validation->set_rules('plan', $this->lang->line('form_package'), 'trim|required');
		$this->form_validation->set_rules('months', $this->lang->line('form_months'), 'trim|required|numeric');
		$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
		$success = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->data['message'] = null;

			$plan_id = $this->input->post('plan');
			$months = $this->input->post('months');
			$channel = $this->input->post('channel');
			if ($months) $months = intval($months);
			if ($months <= 0) $this->data['message'] = 'Parameter "months" has to be greater than zero'; // manual validation (> 0)

			if($this->form_validation->run() && $this->data['message'] == null){
				$ci =& get_instance();
				$selected_plan = $this->Plan_model->getPlanById(new MongoId($plan_id));
				if (!array_key_exists('price', $selected_plan)) {
					$selected_plan['price'] = DEFAULT_PLAN_PRICE;
				}
				/* find number of trial days */
				$days_total = array_key_exists('limit_others', $selected_plan) && array_key_exists('trial', $selected_plan['limit_others']) ? $selected_plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
				/* because we want to bill after usage, we have to adjust trial period to +1 month */
				$date_today = time();
				$date_trial_end = strtotime("+".$days_total." day", $date_today);
				$date_after_first_month = strtotime("+1 month", $date_trial_end);
				$days = $this->find_diff_in_days($date_today, $date_after_first_month);
				/* set the parameters for PayPal */
				$this->data['params'] = array(
					'plan_id' => $selected_plan['_id'],
					'price' => $selected_plan['price'],
					'months' => $months,
					'trial_days' => $days > MAX_ALLOWED_TRIAL_DAYS ? MAX_ALLOWED_TRIAL_DAYS : $days,
					'callback' => $ci->config->config['server'].'notification',
				);
				switch ($channel) {
					case PAYMENT_CHANNEL_PAYPAL:
						$this->data['main'] = 'account_purchase_paypal';
						$success = true;
						break;
					default:
						$this->data['message'] = 'Invalid payment channel';
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
		$this->data['heading_title'] = $this->lang->line('congrat_title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');
		$this->data['main'] = 'account_purchase_paypal_done';

		/* clear basket in the session */
		$this->session->set_userdata('plan', null);

		$this->load->vars($this->data);
		$this->render_page('template');
	}

	private function find_diff_in_days($from, $to) {
		$_from = new DateTime(date("Y-m-d", $from));
		$_to = new DateTime(date("Y-m-d", $to));
		$interval = $_from->diff($_to);
		return intval($interval->format('%R%a'));
	}

	private function check_valid_payment($client) {
		$date_start = array_key_exists('date_start', $client) ? $client['date_start']->sec : null;
		$date_expire = array_key_exists('date_expire', $client) ? $client['date_expired']->sec : null;
		$t = time();
		return ($date_start ? $date_start <= $t : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET) && ($date_expire ? $t <= $date_expire : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET);
	}

    private function validateAccess(){
        return true;
    }
}
?>
