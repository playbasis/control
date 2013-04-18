<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Playbasis extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->model('social_model');
	}
	public function test()
	{
		$this->load->view('playbasis/apitest');
	}
	public function fb()
	{
		$this->load->view('playbasis/fb');
	}
	public function login()
	{
		$this->load->view('playbasis/login');
	}
}
?>