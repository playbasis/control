<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Utility extends CI_Model
{
	public function getEventMessage($eventType, $amount = 'some', $pointName = 'points', $badgeName = 'a', $newLevel = '', $objectiveName = '', $goodsName = '')
	{
		switch($eventType)
		{
		case 'badge':
			return "earned $badgeName badge";
		case 'point':
			return "earned $amount $pointName";
		case 'level':
			return ($newLevel) ? "is now level $newLevel" : 'gained a level';
		case 'login':
			return 'logged in';
		case 'logout':
			return 'logged out';
		case 'objective':
			return 'completed an objective "'.$objectiveName.'"';
        case 'goods':
            return "redeem $goodsName goods";
		default:
			return 'did a thing';
		}
	}
}
?>