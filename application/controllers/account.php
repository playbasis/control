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

		$this->purchase = array('subscribe', 'upgrade', 'downgrade');
	}

	/*
		playbasis_client - store client's subscription status
		playbasis_permission - store the current plan of a client
		playbasis_plan - store plan details and plan price (note: also 'display' flags, which is to determine whether to feature the plan in sign-up page of playbasis.com)
		playbasis_payment_log - store payment transactions (due to their subscription plan)
		playbasis_notification_log - store all PayPal IPN messages
		playbasis_payment_channel - store all available payment channels
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
		// So if whenever payment fails, the two fields would not be updated, which results in blocking the usage of API.
		// In addition, "date_expire" will include extra days to cover grace period.
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
		$this->purchase(PURCHASE_SUBSCRIBE);
	}

	public function upgrade() {
		$this->purchase(PURCHASE_UPGRADE);
	}

	public function downgrade() {
		$this->purchase(PURCHASE_DOWNGRADE);
	}

	private function purchase($mode) {

		if(!$this->validateAccess()){
			echo "<script>alert('".$this->lang->line('error_access')."'); history.go(-1);</script>";
		}

		$this->data['meta_description'] = $this->lang->line('meta_description');
		$this->data['title'] = $this->lang->line('title');
		$this->data['text_no_results'] = $this->lang->line('text_no_results');

		$client = $this->Client_model->getClientById($this->User_model->getClientId());

		$success = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->data['message'] = null;

			$this->form_validation->set_rules('plan', $this->lang->line('form_package'), 'trim|required');
			$this->form_validation->set_rules('months', $this->lang->line('form_months'), 'trim|required|numeric');
			$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
			$plan_id = $this->input->post('plan');
			$months = $this->input->post('months');
			$channel = $this->input->post('channel');
			if ($months) $months = intval($months);
			if ($months <= 0) $this->data['message'] = 'Parameter "months" has to be greater than zero'; // manual validation (> 0)
			if (!$this->check_valid_payment_channel($channel)) $this->data['message'] = 'Invalid payment channel';

			if($this->form_validation->run() && $this->data['message'] == null){
				$ci =& get_instance();

				$selected_plan = $this->Plan_model->getPlanById(new MongoId($plan_id));
				if (!array_key_exists('price', $selected_plan)) {
					$selected_plan['price'] = DEFAULT_PLAN_PRICE;
				}

				$trial_days = 0;
				$modify = PAYPAL_MODIFY_EITHER_NEW_SUBSCRIPTION_OR_MODIFY;
				switch ($mode) {
				case PURCHASE_SUBSCRIBE:
					/* find number of trial days */
					$days_total = array_key_exists('limit_others', $selected_plan) && array_key_exists('trial', $selected_plan['limit_others']) ? $selected_plan['limit_others']['trial'] : DEFAULT_TRIAL_DAYS;
					$date_today = time();
					$date_trial_end = strtotime("+".$days_total." day", $date_today);
					$date_after_first_month = strtotime("+1 month", $date_trial_end); /* because we want to bill after usage, we have to adjust trial period to +1 month */
					$trial_days = $this->find_diff_in_days($date_today, $date_after_first_month);

					/* allow subscribers to sign up for new subscriptions only */
					$modify = PAYPAL_MODIFY_NEW_SUBSCRIPTION_ONLY;
					break;
				case PURCHASE_UPGRADE:
				case PURCHASE_DOWNGRADE:
					/* find number of trial days */
					$date_billing = array_key_exists('date_billing', $client) ? $client['date_billing']->sec : null;
					$days_remaining = $this->find_diff_in_days(time(), $date_billing);
					$trial_days = $days_remaining >= 0 ? $days_remaining : 0;

					/* allow subscribers to modify their current subscriptions only */
					$modify = PAYPAL_MODIFY_CURRENT_SUBSCRIPTION_ONLY;

					/* save selected plan_id into 'next_plan_id', not change plan until we receive IPN for confirmation */
					// TODO:
					break;
				default:
					log_message('error', 'Invalid mode = '.$mode);
					break;
				}

				/* set the parameters for PayPal */
				$this->data['params'] = array(
					'plan_id' => $selected_plan['_id'],
					'price' => $selected_plan['price'],
					'months' => $months,
					'trial_days' => $trial_days > MAX_ALLOWED_TRIAL_DAYS ? MAX_ALLOWED_TRIAL_DAYS : $trial_days,
					'callback' => $ci->config->config['server'].'notification',
					'modify' => $modify,
				);

				$success = true;
			}

			$this->data['heading_title'] = $this->lang->line('order_title');
			$this->data['main'] = 'account_purchase_paypal';
		}
		if (!$success) {
			$this->data['mode'] = $mode;
			$this->data['plans'] = $this->Plan_model->listDisplayPlans();
			switch ($mode) {
			case PURCHASE_UPGRADE:
			case PURCHASE_DOWNGRADE:
				$date_billing = array_key_exists('date_billing', $client) ? $client['date_billing']->sec : null;
				$date_billing_end = strtotime("+".(MONTHS_PER_PLAN+1)." month", $date_billing); /* because we want to bill after usage, we have to adjust period to +1 month */
				$months = $this->find_diff_in_months(time(), $date_billing_end); /* calculate remaining months */
				$this->data['months'] = ($months >= 0 ? $months : 0); // TODO: when months < 0, we should redirect user to a page to extend (basically sign up a new subscription)
				break;
			case PURCHASE_SUBSCRIBE:
			default:
				$this->data['months'] = MONTHS_PER_PLAN;
				break;
			}
			$this->data['heading_title'] = $this->lang->line('channel_title');
			$this->data['main'] = 'account_purchase';
			$this->data['form'] = 'account/'.$this->purchase[$mode];
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
		$this->data['text_no_results'] = $this->lang->line('text_no_results');
		$this->data['heading_title'] = $this->lang->line('cancel_title');

		$success = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->data['message'] = null;

			$this->form_validation->set_rules('channel', $this->lang->line('form_channel'), 'trim|required');
			$channel = $this->input->post('channel');
			if (!$this->check_valid_payment_channel($channel)) $this->data['message'] = 'Invalid payment channel';

			if($this->form_validation->run() && $this->data['message'] == null){
				$this->data['main'] = 'account_cancel_subscription_paypal';
				$success = true;
			}
		}
		if (!$success) {
			$this->data['main'] = 'account_cancel_subscription';
			$this->data['form'] = 'account/cancel_subscription';
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

	private function find_diff_in_months($from, $to) {
		return intval($this->find_diff_in_fmt($from, $to, '%r%m'));
	}

	private function find_diff_in_days($from, $to) {
		return intval($this->find_diff_in_fmt($from, $to, '%r%a'));
	}

	private function find_diff_in_fmt($from, $to, $fmt) {
		$_from = new DateTime(date("Y-m-d", $from));
		$_to = new DateTime(date("Y-m-d", $to));
		$interval = $_from->diff($_to);
		return $interval->format($fmt);
	}

	private function check_valid_payment($client) {
		$date_start = array_key_exists('date_start', $client) ? $client['date_start']->sec : null;
		$date_expire = array_key_exists('date_expire', $client) ? $client['date_expired']->sec : null;
		$t = time();
		return ($date_start ? $date_start <= $t : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET) && ($date_expire ? $t <= $date_expire : DEFAULT_VALID_STATUS_IF_DATE_IS_NOT_SET);
	}

	private function check_valid_payment_channel($channel) {
		return in_array($channel, array(PAYMENT_CHANNEL_PAYPAL));
	}

	private function validateAccess(){
		return true;
	}
}
?>
