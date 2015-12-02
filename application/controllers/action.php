<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Action extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('action_model');
		$this->load->model('client_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/respond', 'resp');
	}
	public function index_get()
	{
		$action = array();
		foreach ($this->action_model->listActions($this->validToken) as $key => $value) {
			array_push($action, $value['name']);
		}
		$this->response($this->resp->setRespond($action), 200);
	}

	public function usedonly_get()
	{
		$action = array();
		foreach ($this->action_model->listActionsOnlyUsed($this->validToken) as $key => $value) {
			array_push($action, $value['name']);
		}
		$this->response($this->resp->setRespond($action), 200);
	}

	public function log_get()
	{
		// Limit
		$limit = $this->client_model->getPlanLimitById(
			$this->client_plan,
			'others',
			'insight'
		);

		$now = new Datetime();
		$startDate      = new DateTime($this->input->get('from', TRUE));
		$endDate        = new DateTime($this->input->get('to', TRUE));

		$log = array();
		$prev = null;
		$this->action_model->set_read_preference_secondary();
		foreach ($this->action_model->actionLog(
				$this->validToken,
				$startDate->format('Y-m-d'),
				$endDate->format('Y-m-d')) as $key => $value) {
			$dDiff = $now->diff(new DateTime($value["_id"]));
			if ($limit && $dDiff->days > $limit) {
				continue;
			}
			$key = $value['_id'];
			if ($prev) {
				$d = $prev;
				while (strtotime($d) <= strtotime($key)) {
					if (!array_key_exists($d, $log)) $log[$d] = array('' => true); // force output to be "{}" instead of "[]"
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			if ($value['value'] != 'SKIP') {
				$log[$key] = $value['value'];
				if (array_key_exists('', $log[$key])) unset($log[$key]['']);
			}
		}
		$this->action_model->set_read_preference_primary();
		ksort($log);
		$log2 = array();
		if (!empty($log)) foreach ($log as $key => $value) {
			array_push($log2, array($key => $value));
		}
		$this->response($this->resp->setRespond($log2), 200);
	}

	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
			);
		$token = $this->auth_model->getApiInfo($credential);
		echo '<br>findAction:<br>';
		$result = $this->action_model->findAction(array_merge($token, array('action_name'=>'like')));
		print_r($result);
		echo '</pre>';
	}
}
?>
