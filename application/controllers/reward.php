<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Reward extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('reward_model');
		$this->load->model('badge_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/respond', 'resp');
	}
	public function index_get()
	{
		$reward = array();
		foreach ($this->reward_model->listRewards($this->validToken) as $key => $value) {
			array_push($reward, $value['name']);
		}
		array_push($reward, 'level');
		$this->response($this->resp->setRespond($reward), 200);
	}
	public function pointLog_get()
	{
		$log = array();
		$prev = null;
		foreach ($this->reward_model->rewardLog($this->validToken, 'point', $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('point' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			array_push($log, array($key => array('point' => $value['value'])));
		}
		$this->response($this->resp->setRespond($log), 200);
	}
	public function expLog_get()
	{
		$log = array();
		$prev = null;
		foreach ($this->reward_model->rewardLog($this->validToken, 'exp', $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('exp' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			array_push($log, array($key => array('exp' => $value['value'])));
		}
		$this->response($this->resp->setRespond($log), 200);
	}
	public function badgeLog_get()
	{
		$log = array();
		$prev = null;
		foreach ($this->badge_model->getAllBadges($this->validToken) as $key => $v) {
			$badge_id = $v['badge_id'];
			foreach ($this->reward_model->badgeLog($this->validToken, $badge_id, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
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
					if (array_key_exists($key, $log)) {
						$log[$key][$badge_id] = $value['value'];
					} else {
						$log[$key] = array($badge_id => $value['value']);
					}
					if (array_key_exists('', $log[$key])) unset($log[$key]['']);
				}
			}
		}
		ksort($log);
		$log2 = array();
		if (!empty($log)) foreach ($log as $key => $value) {
			array_push($log2, array($key => $value));
		}
		$this->response($this->resp->setRespond($log2), 200);
	}
	public function levelLog_get()
	{
		$log = array();
		$prev = null;
		foreach ($this->reward_model->levelupLog($this->validToken, $this->input->get('from'), $this->input->get('to')) as $key => $value) {
			$key = $value['_id'];
			if ($prev) {
				$d = date('Y-m-d', strtotime('+1 day', strtotime($prev)));
				while (strtotime($d) < strtotime($key)) {
					array_push($log, array($d => array('level' => 0)));
					$d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
				}
			}
			$prev = $key;
			array_push($log, array($key => array('level' => $value['value'])));
		}
		$this->response($this->resp->setRespond($log), 200);
	}
}
?>