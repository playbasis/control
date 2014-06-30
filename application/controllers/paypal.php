<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

require_once APPPATH . '/libraries/paypal.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\CreditCard;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;

class Paypal extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('player_model');
		$this->load->model('tracker_model');
		$this->load->model('point_model');
		$this->load->model('action_model');
        $this->load->model('level_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/utility', 'utility');
		$this->load->model('tool/respond', 'resp');
		$this->load->model('tool/node_stream', 'node');

        /* $this->paypal = PaypalHelper::getApiContext(); */
	}

    public function index_get()
    {
        PaypalHelper::createInvoice();
    }
}
