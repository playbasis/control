<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Utility extends CI_Model{
	public function getEventMessage($eventType){
		switch($eventType){
			case 'badge'	:	return 'earned new badge'; break;
			case 'exp'		:   return 'earned some exp'; break;
			case 'point'	:	return 'earned some point'; break;
			case 'level'	:	return 'earned new level'; break;
			case 'login'	:   return 'logged in'; break;
			case 'logout'	:   return 'logged out'; break;
			default :
				return 'this is default message';
		}
	}
}
?>