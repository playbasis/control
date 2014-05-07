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

	public function elapsed_time($key = "default") {
		static $last = array();
		$now = microtime(true);
		$ret = null;
		if (!array_key_exists($key, $last)) $last[$key] = null;
		if ($last[$key] != null) $ret = $now - $last[$key];
		$last[$key] = $now;
		return $ret;
	}

	public function url_exist($url, $prefix='') {
		if (substr($url, 0, 4) != 'http') $url = $prefix.$url;
		$file_headers = @get_headers($url);
		log_message('debug', 'url = '.print_r($url, true).', header = '.print_r($file_headers[0], true));
		return strpos($file_headers[0], ' 20') || strpos($file_headers[0], ' 30');
	}

	public function save_file($dir, $file, $content, $mode=0755) {
		if (!is_dir($dir)) {
			mkdir($dir, $mode, true);
		}
		file_put_contents("$dir/$file", $content);
	}

	public function email($from, $to, $subject, $message) {
		$this->amazon_ses->from($from);
		$this->amazon_ses->to($to);
		$this->amazon_ses->subject($subject);
		$this->amazon_ses->message($message);
		return $this->amazon_ses->send();
	}
}
?>