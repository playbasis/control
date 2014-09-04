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

	public function url_exists($url, $prefix='') {
		if (substr($url, 0, 4) != 'http') $url = $prefix.$url;
		$file_headers = @get_headers($url);
		log_message('debug', 'url = '.print_r($url, true).', header = '.print_r($file_headers[0], true));
		return strpos($file_headers[0], ' 20') || strpos($file_headers[0], ' 30');
	}

	public function save_dir($dir, $mode=0755) {
		if (!is_dir($dir)) {
			mkdir($dir, $mode, true);
		}
	}

	public function save_file($dir, $file, $content, $mode=0755) {
		$this->save_dir($dir, $mode);
		file_put_contents("$dir/$file", $content);
	}

	/* http://stackoverflow.com/questions/19083175/generate-random-string-in-php-for-file-name */
	public function random_string($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));
		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}

	/* require: $this->load->library('amazon_ses'); */
	public function email($from, $to, $subject, $message, $message_alt=null, $attachments=array()) {
		log_message('debug', 'from = '.$from);
		log_message('debug', 'to = '.implode(',', $to));
		log_message('debug', 'subject = '.$subject);
		log_message('debug', 'message = '.$message);
		log_message('debug', 'message_alt = '.$message_alt);
		log_message('debug', 'count(attachments) = '.count($attachments));
		$this->amazon_ses->from($from);
		$this->amazon_ses->to($to);
		$this->amazon_ses->subject($subject);
		$this->amazon_ses->message($message);
		if (!empty($message_alt)) $this->amazon_ses->message_alt($message_alt);
		if (!empty($attachments)) $this->amazon_ses->attachment($attachments);
		$this->amazon_ses->debug(true);
		$response = $this->amazon_ses->send();
		log_message('info', 'response = '.$response);
		return $response;
	}

	/* http://mpdf1.com/manual/index.php?tid=125 */
	public function html2mpdf($html, $output=false) {
		require_once(APPPATH.'/libraries/mpdf/mpdf.php');
		$mpdf = new mPDF('s','A4','','',25,15,21,22,10,10);
		$mpdf->WriteHTML($html);
		return $output ? $mpdf->Output('', 'S') : null;
	}

	public function find_diff_in_days($from, $to) {
		return intval($this->find_diff_in_fmt($from, $to, '%r%a'));
	}

	public function find_diff_in_fmt($from, $to, $fmt) {
		$_from = new DateTime(date("Y-m-d", $from));
		$_to = new DateTime(date("Y-m-d", $to));
		$interval = $_from->diff($_to);
		return $interval->format($fmt);
	}
}
?>