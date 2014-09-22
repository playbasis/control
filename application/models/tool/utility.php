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
        if(count($to) > 10){
            $email_prepare = array();
            $emai_small_set = array();
            foreach($to as $e){
                if(count($emai_small_set) > 9){
                    $email_prepare[] = $emai_small_set;
                    $emai_small_set = array();
                }
                $emai_small_set[] = $e;
            }

            $message_response = array();

            foreach($email_prepare as $email_small){
                $message_response[] = $this->_email(array(
                    'from' => $from,
                    'to' => $email_small,
                    'subject' => $subject,
                    'message' => $message,
                    'message_alt' => $message_alt,
                    'attachments' => $attachments,
                ));
                sleep(1);
            }

            return $message_response;
        }else{
            return $this->_email(array(
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'message' => $message,
                'message_alt' => $message_alt,
                'attachments' => $attachments,
            ));
        }
	}

	/* require: $this->load->library('amazon_ses'); */
	public function email_with_cc($from, $to, $cc, $subject, $message, $message_alt=null, $attachments=array()) {
        if(count($to) > 10 || count($cc) > 10){
            $email_prepare_to = array();
            $emai_small_set_to = array();
            foreach($to as $e){
                if(count($emai_small_set_to) > 9){
                    $email_prepare_to[] = $emai_small_set_to;
                    $emai_small_set_to = array();
                }
                $emai_small_set_to[] = $e;
            }

            $email_prepare_cc = array();
            $emai_small_set_cc = array();
            foreach($cc as $e){
                if(count($emai_small_set_cc) > 9){
                    $email_prepare_cc[] = $emai_small_set_cc;
                    $emai_small_set_cc = array();
                }
                $emai_small_set_cc[] = $e;
            }

            $message_response = array();

            if(count($email_prepare_to) > count($email_prepare_cc)){
                $i = 0;
                foreach($email_prepare_to as $email_small){
                    $email_data = array(
                        'from' => $from,
                        'to' => $email_small,
                        'subject' => $subject,
                        'message' => $message,
                        'message_alt' => $message_alt,
                        'attachments' => $attachments,
                    );

                    if(isset($email_prepare_cc[$i])){
                        $email_data['cc'] = $email_prepare_cc[$i];
                    }

                    $message_response[] = $this->_email($email_data);
                    $i++;
                    sleep(1);
                }
            }else{
                $i = 0;
                foreach($email_prepare_cc as $email_small){
                    $email_data = array(
                        'from' => $from,
                        'cc' => $email_small,
                        'subject' => $subject,
                        'message' => $message,
                        'message_alt' => $message_alt,
                        'attachments' => $attachments,
                    );

                    if(isset($email_prepare_to[$i])){
                        $email_data['to'] = $email_prepare_to[$i];
                    }

                    $message_response[] = $this->_email($email_data);
                    $i++;
                    sleep(1);
                }
            }

            return $message_response;
        }else{
            return $this->_email(array(
                'from' => $from,
                'to' => $to,
                'cc' => $cc,
                'subject' => $subject,
                'message' => $message,
                'message_alt' => $message_alt,
                'attachments' => $attachments,
            ));
        }
	}

	/* require: $this->load->library('amazon_ses'); */
	public function email_bcc($from, $bcc, $subject, $message, $message_alt=null, $attachments=array()) {
        if(count($bcc) > 10){
            $email_prepare = array();
            $emai_small_set = array();
            foreach($bcc as $e){
                if(count($emai_small_set) > 9){
                    $email_prepare[] = $emai_small_set;
                    $emai_small_set = array();
                }
                $emai_small_set[] = $e;
            }

            $message_response = array();

            foreach($email_prepare as $email_small){
                $message_response[] = $this->_email(array(
                    'from' => $from,
                    'bcc' => $email_small,
                    'subject' => $subject,
                    'message' => $message,
                    'message_alt' => $message_alt,
                    'attachments' => $attachments,
                ));
                sleep(1);
            }

            return $message_response;
        }else{
            return $this->_email(array(
                'from' => $from,
                'bcc' => $bcc,
                'subject' => $subject,
                'message' => $message,
                'message_alt' => $message_alt,
                'attachments' => $attachments,
            ));
        }
	}

	/* require: $this->load->library('amazon_ses'); */
	public function _email($data) {
		if (!is_array($data)) return null; // error
		foreach ($data as $key => $value) {
			switch ($key) {
			case 'from':        $this->amazon_ses->from($value); break;
			case 'to':          $this->amazon_ses->to($value); break;
			case 'cc':          $this->amazon_ses->cc($value); break;
			case 'bcc':         $this->amazon_ses->bcc($value); break;
			case 'subject':     $this->amazon_ses->subject($value); break;
			case 'message':     $this->amazon_ses->message($value); break;
			case 'message_alt': $this->amazon_ses->message_alt($value); break;
			case 'attachment':  $this->amazon_ses->attachment($value); break;
			default: break;
			}
		}
//		$this->amazon_ses->bcc(EMAIL_DEBUG_MODE);
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