<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Utility extends CI_Model{
	public function getEventMessage($eventType, $amount='some', $pointName='points', $badgeName='a', $newLevel=''){
		switch($eventType){
			case 'badge'	:	return "earned $badgeName badge";
			case 'exp'		:   return "earned $amount exp";
			case 'point'	:	return "earned $amount $pointName";
			case 'level'	:	return ($newLevel) ? "is now level $newLevel" : 'gained a level';
			case 'login'	:   return 'logged in';
			case 'logout'	:   return 'logged out';
			default :
				return 'did a thing';
		}
	}
}
?>