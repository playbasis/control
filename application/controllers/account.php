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

        if(!$this->User_model->isLogged()){
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

	    // register select plan imm -> keep date
	    // 1st page show only active plans

	    // billing every month (using due date when select plan)

	    // get current credit of the client
	    $client = $this->Client_model->getClientById($this->User_model->getClientId());
	    // get current plan
	    // get due date of the client
	    $plan_registration = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
	    // get plan's price
	    $plan = $this->Plan_model->getPlanById($plan_registration['plan_id']);

	    // select #months to pay
	    // select channel for payment
	    // pay via paypal

	    // payment history

	    if (!array_key_exists('credit', $client)) {
		    $client['credit'] = 1234;
	    }
	    if (!array_key_exists('price', $plan)) {
		    $plan['price'] = 99;
	    }
	    $this->data['client'] = $client;
	    $this->data['plan'] = $plan;
	    $this->data['plan']['registration_date_added'] = $plan_registration['date_added']->sec;
	    $this->data['plan']['registration_date_modified'] = $plan_registration['date_modified']->sec;
	    $this->data['main'] = 'account';
	    $this->data['form'] = 'account/add_credit';
/*print('<pre>');print_r($this->data['client']);print('</pre>');
print('email = '.$this->data['client']['email']);
print('credit = '.$this->data['client']['credit']);
print('<pre>');print_r($this->data['plan']);print('</pre>');
print('plan = '.$this->data['plan']['_id']);
print('price = '.$this->data['plan']['price']);*/
	    $this->session->set_userdata('price', $this->data['plan']['price']);
//print('date = '.$this->data['plan']['registration_date_modified']);
	    $this->load->vars($this->data);
	    $this->render_page('template');

	    // playbasis_client => store current credit amount
	    // playbasis_permission => to find associated plan of a client
	    // playbasis_plan => store plan details and price
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

			if($this->form_validation->run() && $this->data['message'] == null){
				$credit = $this->input->post('credit');
				$this->session->set_userdata('credit', $credit);
				$channel = $this->input->post('channel');
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

	public function payment_notification() {
		$raw_post_data = file_get_contents('php://input');
print_r($raw_post_data);
		// insert into playbasis_payment_log and update credit in playbasis_client
		//redirect('/account', 'refresh');
	}

    private function validateAccess(){
        return true;
    }
}
?>
