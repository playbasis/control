<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Utility extends CI_Model{
	public function getEventMessage($eventType){
		switch($eventType){
			case 'badge'	:
			case 'exp'		:
			case 'point'	:
			case 'level'	:
			default :
				return '';
		}
	}
}
?>