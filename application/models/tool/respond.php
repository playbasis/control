<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Respond extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	public function setRespond($data){
		$respondData  = array();

		$respondData['success'] 	= true;
		$respondData['error_core'] 	= '0000';
		$respondData['message'] 	= 'Success';
		$respondData['response'] 	= $data;
		$respondData['timestamp'] 	= (int)time();
		$respondData['time'] 		= date('r e');

		return $respondData;
	}
}
?>