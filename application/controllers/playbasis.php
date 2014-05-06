<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('STATIC_IMAGE_URL', 'http://admin.pbapp.net');
define('DYNAMIC_IMAGE_URL', 'http://images.pbapp.net');
define('CANNOT_VIEW_EMAIL', 'CANNOT_VIEW_EMAIL');
define('NUMBER_OF_PLAYERS', 20);
define('NUMBER_OF_ACTIONS', -1);
define('NUMBER_OF_BADGES', -1);

function get_sum_and_max($arr) {
	$max = -1;
	$sum = 0;
	if (is_array($arr)) foreach ($arr as $key => $each) {
		if ($each['value'] != 'SKIP' && ($max == -1 || $each['value'] > $arr[$max]['value'])) {
			$max = $key;
		}
		$sum += $each['value'];
	}
	return array($sum, $max);
}
function action_badge_cmp($a, $b) {
	if ($a[1] == $b[1]) {
		return 0;
	}
	return -1*(($a[1] < $b[1]) ? -1 : 1);
}
function feed_cmp($a, $b) {
	if ($a['FEED_DATE'] == $b['FEED_DATE']) {
		return 0;
	}
	return -1*(($a['FEED_DATE'] < $b['FEED_DATE']) ? -1 : 1);
}
function item_cmp($a, $b) {
	if ($a['TOTAL'] == $b['TOTAL']) {
		return 0;
	}
	return -1*(($a['TOTAL'] < $b['TOTAL']) ? -1 : 1);
}
function url_exist($url) {
	$file_headers = @get_headers($url);
	return strpos($file_headers[0], ' 20') || strpos($file_headers[0], ' 30');
}

class Playbasis extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('client_model');
		$this->load->model('player_model');
		$this->load->model('action_model');
		$this->load->model('badge_model');
		$this->load->model('reward_model');
		$this->load->model('goods_model');
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
	private function saveFile($dir, $file, $content, $mode=0755) {
		if (!is_dir($dir)) {
			mkdir($dir, $mode, true);
		}
		file_put_contents("$dir/$file", $content);
	}
	private function email($from, $to, $subject, $message) {
		$this->amazon_ses->from($from);
		$this->amazon_ses->to($to);
		$this->amazon_ses->subject($subject);
		$this->amazon_ses->message($message);
		return $this->amazon_ses->send();
	}
	private function formatData(&$params) {
		$params['TOTAL_USER'] = number_format($params['TOTAL_USER']);
		$params['TOTAL_USER_PREV'] = number_format($params['TOTAL_USER_PREV']);
		$params['NEW_USER_TOTAL'] = number_format($params['NEW_USER_TOTAL']);
		$params['NEW_USER_TOTAL_PREV'] = number_format($params['NEW_USER_TOTAL_PREV']);
		$params['DAU_TOTAL'] = number_format($params['DAU_TOTAL']);
		$params['DAU_TOTAL_PREV'] = number_format($params['DAU_TOTAL_PREV']);
		$params['MAU_TOTAL'] = number_format($params['MAU_TOTAL']);
		$params['MAU_TOTAL_PREV'] = number_format($params['MAU_TOTAL_PREV']);
		if (is_array($params['ACTIONS'])) foreach ($params['ACTIONS'] as &$action) {
			$action['TOTAL'] = number_format($action['TOTAL']);
			$action['TOTAL_PREV'] = number_format($action['TOTAL_PREV']);
		}
		if (is_array($params['BADGES'])) foreach ($params['BADGES'] as &$action) {
			$action['TOTAL'] = number_format($action['TOTAL']);
			$action['TOTAL_PREV'] = number_format($action['TOTAL_PREV']);
		}
		if (is_array($params['ITEMS'])) foreach ($params['ITEMS'] as &$action) {
			$action['TOTAL'] = number_format($action['TOTAL']);
			$action['TOTAL_PREV'] = number_format($action['TOTAL_PREV']);
		}
	}
	private function getData($data, $c, $s, $to, $from, $from2) {
		$params = array(
			'STATIC_IMAGE_URL' => STATIC_IMAGE_URL,
			'DYNAMIC_IMAGE_URL' => DYNAMIC_IMAGE_URL,
			'CLIENT_ID' => $c['_id'],
			'CLIENT_NAME' => $c['first_name'],
			'CLIENT_EMAIL' => $c['email'],
			'SITE_ID' => $s['_id'],
			'SITE_NAME' => $s['site_name'],
			'FROM' => date('d M Y', strtotime('+1 day', strtotime($from))),
			'TO' => date('d M Y', strtotime($to)),
		);
		$params['DIR'] = $params['CLIENT_NAME']."/".$params['SITE_NAME'];
		$params['FILE'] = "$to.html";
		$params['REPORT_URL'] = "http://report.pbapp.net/".$params['DIR']."/".$params['FILE'];

		// total users
		$curr = $this->player_model->new_registration($data, null, $to); //echo '<pre>';echo 'total regis1 = '; var_dump($curr);echo '</pre>';
		$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
		$params['TOTAL_USER'] = $sum_max[0];
		$prev = $this->player_model->new_registration($data, null, $from); //echo '<pre>';echo 'total regis2 = '; var_dump($prev);echo '</pre>';
		$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
		$params['TOTAL_USER_PREV'] = $sum_max[0];
		$params['TOTAL_USER_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['TOTAL_USER'] ? 'up' : 'down').'.gif">' : ($params['TOTAL_USER'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$params['TOTAL_USER_PERCENT'] = ($sum_max[0] != 0 || $params['TOTAL_USER'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['TOTAL_USER'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['TOTAL_USER'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');

		// new users
		$curr = $this->player_model->new_registration($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'new regis1 = '; var_dump($curr);echo '</pre>';
		$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
		$best = $sum_max[1];
		$params['NEW_USER_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
		$params['NEW_USER_TOTAL'] = $sum_max[0];
		$prev = $this->player_model->new_registration($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'new regis2 = '; var_dump($prev);echo '</pre>';
		$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
		$params['NEW_USER_TOTAL_PREV'] = $sum_max[0];
		$params['NEW_USER_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['NEW_USER_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['NEW_USER_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$params['NEW_USER_PERCENT'] = ($sum_max[0] != 0 || $params['NEW_USER_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['NEW_USER_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['NEW_USER_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
		$params['NEW_USER_AVERAGE'] = number_format($params['NEW_USER_TOTAL']/7.0, 2);
		$params['NEW_USER_BEST_VALUE'] = $best != -1 ? number_format($curr[$best]['value']) : 0;

		// DAU
		$curr = $this->player_model->daily_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'DAU1 = ';var_dump($curr);echo '</pre>';
		$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
		$best = $sum_max[1];
		$params['DAU_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
		$params['DAU_TOTAL'] = $sum_max[0];
		$prev = $this->player_model->daily_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'DAU2 = ';var_dump($prev);echo '</pre>';
		$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
		$params['DAU_TOTAL_PREV'] = $sum_max[0];
		$params['DAU_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['DAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['DAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$params['DAU_PERCENT'] = ($sum_max[0] != 0 || $params['DAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['DAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['DAU_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
		$params['DAU_AVERAGE'] = number_format($params['DAU_TOTAL']/7.0, 2);
		$params['DAU_BEST_VALUE'] = $best != -1 ? number_format($curr[$best]['value']) : 0;

		// MAU
		$curr = $this->player_model->monthy_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'MAU1 = ';var_dump($curr);echo '</pre>';
		$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
		$best = $sum_max[1];
		$params['MAU_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
		$params['MAU_TOTAL'] = $sum_max[0];
		$prev = $this->player_model->monthy_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'MAU2 = ';var_dump($prev);echo '</pre>';
		$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
		$params['MAU_TOTAL_PREV'] = $sum_max[0];
		$params['MAU_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['MAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['MAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$params['MAU_PERCENT'] = ($sum_max[0] != 0 || $params['MAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['MAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['MAU_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
		$params['MAU_AVERAGE'] = number_format($params['MAU_TOTAL']/7.0, 2);
		$params['MAU_BEST_VALUE'] = $best != -1 ? number_format($curr[$best]['value']) : 0;

		// action
		$result = array();
		$actions = $this->action_model->listActions($data);
		foreach ($actions as $action) {
			$action_name = $action['name'];
			//echo '<pre>';echo $action_name;echo '</pre>';

			$curr = $this->action_model->actionLog($data, $action_name, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';var_dump($curr);echo '</pre>';
			$sum_max1 = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max1[0].', max = '.print_r($sum_max1[1] != -1 ? $curr[$sum_max1[1]] : "",true);echo '</pre>';

			$prev = $this->action_model->actionLog($data, $action_name, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';var_dump($prev);echo '</pre>';
			$sum_max2 = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max2[0].', max = '.print_r($sum_max2[1] != -1 ? $prev[$sum_max2[1]] : "",true);echo '</pre>';

			array_push($result, array($action, $sum_max1[0], $sum_max2[0]));
		}
		usort($result, 'action_badge_cmp');
		//echo '<pre>';var_dump($result);echo '</pre>';
		$params['ACTIONS'] = array();
		if (is_array($result)) foreach ($result as $i => $action) {
			$params['ACTIONS'][] = array(
				'BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
				'IMAGE' => str_replace('-alt', '', $action[0]['icon']),
				'NAME' => $action[0]['name'],
				'TOTAL' => $action[1],
				'TOTAL_PREV' => $action[2],
				'UPDOWN' => ($action[2] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($action[2] <= $action[1] ? 'up' : 'down').'.gif">' : ($action[1] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
				'PERCENT' => ($action[2] != 0 || $action[1] != 0 ? '<strong style="font-size:12px;color:'.($action[2] <= $action[1] ? '#95cc00' : 'red').'">'.number_format(($action[2] != 0 ? ($action[1] - $action[2])/(1.0*$action[2]) : 1)*100, 2).'%</strong>' : ''),
				'AVERAGE' => number_format($action[1]/7.0, 2),
			);
			if (NUMBER_OF_ACTIONS > 0 && $i == (NUMBER_OF_ACTIONS-1)) break;
		}

		// badge
		$result = array();
		$badges = $this->badge_model->getAllBadges($data);
		foreach ($badges as $badge) {
			$badge_id = $badge['badge_id'];
			$badge_name = $badge['name'];
			//echo '<pre>';echo $badge_name.' ('.$badge_id.')';echo '</pre>';

			$curr = $this->reward_model->badgeLog($data, $badge_id, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';var_dump($curr);echo '</pre>';
			$sum_max1 = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max1[0].', max = '.print_r($sum_max1[1] != -1 ? $curr[$sum_max1[1]] : "",true);echo '</pre>';

			$prev = $this->reward_model->badgeLog($data, $badge_id, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';var_dump($prev);echo '</pre>';
			$sum_max2 = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max2[0].', max = '.print_r($sum_max2[1] != -1 ? $prev[$sum_max2[1]] : "",true);echo '</pre>';

			array_push($result, array($badge, $sum_max1[0], $sum_max2[0]));
		}
		usort($result, 'action_badge_cmp');
		//echo '<pre>';var_dump($result);echo '</pre>';
		$params['BADGES'] = array();
		if (is_array($result)) foreach ($result as $i => $badge) {
			$params['BADGES'][] = array(
				'BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
				'IMAGE_SRC' => $badge[0]['image'],
				'NAME' => $badge[0]['name'],
				'TOTAL' => $badge[1],
				'TOTAL_PREV' => $badge[2],
				'UPDOWN' => ($badge[2] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($badge[2] <= $badge[1] ? 'up' : 'down').'.gif">' : ($badge[1] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
				'PERCENT' => ($badge[2] != 0 || $badge[1] != 0 ? '<strong style="font-size:12px;color:'.($badge[2] <= $badge[1] ? '#95cc00' : 'red').'">'.number_format(($badge[2] != 0 ? ($badge[1] - $badge[2])/(1.0*$badge[2]) : 1)*100, 2).'%</strong>' : ''),
				'AVERAGE' => number_format($badge[1]/7.0, 2),
			);
			if (NUMBER_OF_BADGES > 0 && $i == (NUMBER_OF_BADGES-1)) break;
		}

		// active items
		//$items = $this->goods_model->listActiveItems($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to);
		$items = array_merge($this->goods_model->listActiveItems($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to), $this->goods_model->listExpiredItems($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to));
		//echo '<pre>';echo 'active items = ';var_dump($items);echo '</pre>';
		$params['ITEMS'] = array();
		if (is_array($items)) foreach ($items as $i => $item) {
			$goods_id = $item['goods_id'];
			$goods_name = $item['name'];
			$goods_image = $item['image'];
			$goods_start_date = $item['date_start'];
			$goods_expiration_date = $item['date_expire'];
			$goods_qty = $item['quantity'];
			$goods_criteria = $item['redeem'];
			$sum_max = get_sum_and_max($this->goods_model->totalRedemption($data, $goods_id));
			$goods_qty_redeemed = $sum_max[0];
			$goods_qty_remain = ($goods_qty != null || $goods_qty === 0 ? $goods_qty - $goods_qty_redeemed : null);
			$goods_players_can_redeem = $this->player_model->playerWithEnoughCriteria($data, $goods_criteria);
			//echo '<pre>';echo $goods_name.' ('.$goods_id.'), qty = '.$goods_qty.', redeemed = '.$goods_qty_redeemed.', remain = '.$goods_qty_remain.', #players that can redeem this item = '.$goods_players_can_redeem;echo '</pre>';

			$curr = $this->goods_model->redeemLogCount($data, $goods_id, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo $curr;echo '</pre>';

			$prev = $this->goods_model->redeemLogCount($data, $goods_id, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo $prev;echo '</pre>';

			$params['ITEMS'][] = array(
				'BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
				'IMAGE_SRC' => url_exist($goods_image) ? DYNAMIC_IMAGE_URL.'/images/'.$goods_image : STATIC_IMAGE_URL.'/images/no_image.jpg',
				'NAME' => $goods_name,
				'START_DATE' => ($goods_start_date ? date('d M Y', $goods_start_date->sec) : "Not Set"),
				'EXPIRATION_DATE' => ($goods_expiration_date ? date('d M Y', $goods_expiration_date->sec) : "Not Set"),
				'TOTAL' => $curr,
				'TOTAL_PREV' => $prev,
				'UPDOWN' => ($prev != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($prev <= $curr ? 'up' : 'down').'.gif">' : ($curr != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
				'PERCENT' => ($prev != 0 || $curr != 0 ? '<strong style="font-size:12px;color:'.($prev <= $curr ? '#95cc00' : 'red').'">'.number_format(($prev != 0 ? ($curr - $prev)/(1.0*$prev) : 1)*100, 2).'%</strong>' : ''),
				'PEOPLE_CAN_REDEEM' => number_format($goods_players_can_redeem),
				'QTY_REDEEMED' => number_format($goods_qty_redeemed),
				'QTY_TOTAL' => ($goods_qty != null || $goods_qty === 0 ? number_format($goods_qty) : 'Inf.'),
				'QTY_REMAIN' => $goods_qty_remain,
			);
		}
		usort($params['ITEMS'], 'item_cmp');

		// rank
		$players = $this->player_model->getLeaderboardByLevel(NUMBER_OF_PLAYERS, $c['_id'], $s['_id']);
		//echo '<pre>';var_dump($players);echo '</pre>';
		$params['PLAYERS'] = array();
		if (is_array($players)) foreach ($players as $i => $player) {
			$name = $player['first_name'].' '.$player['last_name'];
			$_name = trim($name);
			$params['PLAYERS'][] = array(
				'PLAYER_ROW' => $i+1,
				'PLAYER_IMAGE_SRC' => url_exist($player['image']) ? $player['image'] : STATIC_IMAGE_URL.'/images/user_no_image.jpg',
				'PLAYER_NAME' => (!empty($_name) ? $name : $player['username']),
				'PLAYER_EXP' => number_format($player['exp']),
				'PLAYER_LEVEL' => number_format($player['level']),
			);
		}

		// Load RSS Parser
		$this->load->library('rssparser');
		$params['FEEDS'] = array();
		$rssparser = $this->rssparser;
		foreach (array('http://www.gamification.co/feed/', 'http://www.entrepreneur.com/feeds/tags/gamification/1908.rss') as $url) {
			$feed = $rssparser->set_feed_url($url)->set_cache_life(30)->getFeed(1);
			if (is_array($feed)) foreach ($feed as $item) {
				$params['FEEDS'][] = array(
					'FEED_TITLE' => $item['title'],
					'FEED_DESCRIPTION' => substr($item['description'], 0, 256),
					'FEED_AUTHOR' => $item['author'],
					'FEED_DATE' => date('Y-m-d', strtotime($item['pubDate'])),
					'FEED_LINK' => $item['link'],
				);
			}
		}
		usort($params['FEEDS'], 'feed_cmp');

		return $params;
	}
	private function get_sum_curr_prev($arr) {
		$curr = 0;
		$prev = 0;
		if (is_array($arr)) foreach ($arr as $each) {
			$curr += $each['TOTAL'];
			$prev += $each['TOTAL_PREV'];
		}
		return array($curr, $prev);
	}
	private function initData($from, $to) {
		$params = array(
			'STATIC_IMAGE_URL' => STATIC_IMAGE_URL,
			'DYNAMIC_IMAGE_URL' => DYNAMIC_IMAGE_URL,
			'FROM' => date('d M Y', strtotime('+1 day', strtotime($from))),
			'TO' => date('d M Y', strtotime($to)),
			'GLOBAL_TOTAL_USER' => 0,
			'GLOBAL_TOTAL_USER_PREV' => 0,
			'GLOBAL_NEW_USER_TOTAL' => 0,
			'GLOBAL_NEW_USER_TOTAL_PREV' => 0,
			'GLOBAL_DAU_TOTAL' => 0,
			'GLOBAL_DAU_TOTAL_PREV' => 0,
			'GLOBAL_MAU_TOTAL' => 0,
			'GLOBAL_MAU_TOTAL_PREV' => 0,
			'CLIENTS' => array(),
		);
		$params['DIR'] = 'master';
		$params['FILE'] = "$to.html";
		$params['REPORT_URL'] = "http://report.pbapp.net/".$params['DIR']."/".$params['FILE'];
		return $params;
	}
	private function incrementData(&$master, $params) {
		$master['GLOBAL_TOTAL_USER'] += $params['TOTAL_USER'];
		$master['GLOBAL_TOTAL_USER_PREV'] += $params['TOTAL_USER_PREV'];
		$master['GLOBAL_NEW_USER_TOTAL'] += $params['NEW_USER_TOTAL'];
		$master['GLOBAL_NEW_USER_TOTAL_PREV'] += $params['NEW_USER_TOTAL_PREV'];
		$master['GLOBAL_DAU_TOTAL'] += $params['DAU_TOTAL'];
		$master['GLOBAL_DAU_TOTAL_PREV'] += $params['DAU_TOTAL_PREV'];
		$master['GLOBAL_MAU_TOTAL'] += $params['MAU_TOTAL'];
		$master['GLOBAL_MAU_TOTAL_PREV'] += $params['MAU_TOTAL_PREV'];
		$actions = $this->get_sum_curr_prev($params['ACTIONS']);
		$badges = $this->get_sum_curr_prev($params['BADGES']);
		$items = $this->get_sum_curr_prev($params['ITEMS']);
		array_push($master['CLIENTS'], array(
			'CLIENT_ID' => $params['CLIENT_ID'],
			'CLIENT_NAME' => $params['CLIENT_NAME'],
			'CLIENT_EMAIL' => $params['CLIENT_EMAIL'],
			'SITE_ID' => $params['SITE_ID'],
			'SITE_NAME' => $params['SITE_NAME'],
			'TOTAL_USER' => number_format($params['TOTAL_USER']),
			'TOTAL_USER_PREV' => number_format($params['TOTAL_USER_PREV']),
			'TOTAL_USER_UPDOWN' => ($params['TOTAL_USER_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($params['TOTAL_USER_PREV'] <= $params['TOTAL_USER'] ? 'up' : 'down').'-black.gif">' : ($params['TOTAL_USER'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'TOTAL_USER_PERCENT' => ($params['TOTAL_USER_PREV'] != 0 || $params['TOTAL_USER'] != 0 ? '<strong style="font-size:12px;color:'.($params['TOTAL_USER_PREV'] <= $params['TOTAL_USER'] ? '#95cc00' : 'red').'">'.number_format(($params['TOTAL_USER_PREV'] != 0 ? ($params['TOTAL_USER'] - $params['TOTAL_USER_PREV'])/(1.0*$params['TOTAL_USER_PREV']) : 1)*100, 2).'%</strong>' : ''),
			'NEW_USER_TOTAL' => number_format($params['NEW_USER_TOTAL']),
			'NEW_USER_TOTAL_PREV' => number_format($params['NEW_USER_TOTAL_PREV']),
			'NEW_USER_TOTAL_UPDOWN' => ($params['NEW_USER_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($params['NEW_USER_TOTAL_PREV'] <= $params['NEW_USER_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['NEW_USER_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'NEW_USER_TOTAL_PERCENT' => ($params['NEW_USER_TOTAL_PREV'] != 0 || $params['NEW_USER_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($params['NEW_USER_TOTAL_PREV'] <= $params['NEW_USER_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($params['NEW_USER_TOTAL_PREV'] != 0 ? ($params['NEW_USER_TOTAL'] - $params['NEW_USER_TOTAL_PREV'])/(1.0*$params['NEW_USER_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : ''),
			'DAU_TOTAL' => number_format($params['DAU_TOTAL']),
			'DAU_TOTAL_PREV' => number_format($params['DAU_TOTAL_PREV']),
			'DAU_TOTAL_UPDOWN' => ($params['DAU_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($params['DAU_TOTAL_PREV'] <= $params['DAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['DAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'DAU_TOTAL_PERCENT' => ($params['DAU_TOTAL_PREV'] != 0 || $params['DAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($params['DAU_TOTAL_PREV'] <= $params['DAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($params['DAU_TOTAL_PREV'] != 0 ? ($params['DAU_TOTAL'] - $params['DAU_TOTAL_PREV'])/(1.0*$params['DAU_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : ''),
			'MAU_TOTAL' => number_format($params['MAU_TOTAL']),
			'MAU_TOTAL_PREV' => number_format($params['MAU_TOTAL_PREV']),
			'MAU_TOTAL_UPDOWN' => ($params['MAU_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($params['MAU_TOTAL_PREV'] <= $params['MAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['MAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'MAU_TOTAL_PERCENT' => ($params['MAU_TOTAL_PREV'] != 0 || $params['MAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($params['MAU_TOTAL_PREV'] <= $params['MAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($params['MAU_TOTAL_PREV'] != 0 ? ($params['MAU_TOTAL'] - $params['MAU_TOTAL_PREV'])/(1.0*$params['MAU_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : ''),
			'ACTIONS_TOTAL' => number_format($actions[0]),
			'ACTIONS_TOTAL_PREV' => number_format($actions[1]),
			'ACTIONS_TOTAL_UPDOWN' => ($actions[1] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($actions[1] <= $actions[0] ? 'up' : 'down').'.gif">' : ($actions[0] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'ACTIONS_TOTAL_PERCENT' => ($actions[1] != 0 || $actions[0] != 0 ? '<strong style="font-size:12px;color:'.($actions[1] <= $actions[0] ? '#95cc00' : 'red').'">'.number_format(($actions[1] != 0 ? ($actions[0] - $actions[1])/(1.0*$actions[1]) : 1)*100, 2).'%</strong>' : ''),
			'BADGES_TOTAL' => number_format($badges[0]),
			'BADGES_TOTAL_PREV' => number_format($badges[1]),
			'BADGES_TOTAL_UPDOWN' => ($badges[1] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($badges[1] <= $badges[0] ? 'up' : 'down').'.gif">' : ($badges[0] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'BADGES_TOTAL_PERCENT' => ($badges[1] != 0 || $badges[0] != 0 ? '<strong style="font-size:12px;color:'.($badges[1] <= $badges[0] ? '#95cc00' : 'red').'">'.number_format(($badges[1] != 0 ? ($badges[0] - $badges[1])/(1.0*$badges[1]) : 1)*100, 2).'%</strong>' : ''),
			'ITEMS_TOTAL' => number_format($items[0]),
			'ITEMS_TOTAL_PREV' => number_format($items[1]),
			'ITEMS_TOTAL_UPDOWN' => ($items[1] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($items[1] <= $items[0] ? 'up' : 'down').'.gif">' : ($items[0] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
			'ITEMS_TOTAL_PERCENT' => ($items[1] != 0 || $items[0] != 0 ? '<strong style="font-size:12px;color:'.($items[1] <= $items[0] ? '#95cc00' : 'red').'">'.number_format(($items[1] != 0 ? ($items[0] - $items[1])/(1.0*$items[1]) : 1)*100, 2).'%</strong>' : ''),
		));
	}
	private function finalizeData(&$master) {
		$master['GLOBAL_TOTAL_USER'] = number_format($master['GLOBAL_TOTAL_USER']);
		$master['GLOBAL_TOTAL_USER_PREV'] = number_format($master['GLOBAL_TOTAL_USER_PREV']);
		$master['GLOBAL_TOTAL_USER_UPDOWN'] = ($master['GLOBAL_TOTAL_USER_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($master['GLOBAL_TOTAL_USER_PREV'] <= $master['GLOBAL_TOTAL_USER'] ? 'up' : 'down').'.gif">' : ($master['GLOBAL_TOTAL_USER'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$master['GLOBAL_TOTAL_USER_PERCENT'] = ($master['GLOBAL_TOTAL_USER_PREV'] != 0 || $master['GLOBAL_TOTAL_USER'] != 0 ? '<strong style="font-size:12px;color:'.($master['GLOBAL_TOTAL_USER_PREV'] <= $master['GLOBAL_TOTAL_USER'] ? '#95cc00' : 'red').'">'.number_format(($master['GLOBAL_TOTAL_USER_PREV'] != 0 ? ($master['GLOBAL_TOTAL_USER'] - $master['GLOBAL_TOTAL_USER_PREV'])/(1.0*$master['GLOBAL_TOTAL_USER_PREV']) : 1)*100, 2).'%</strong>' : '');
		$master['GLOBAL_NEW_USER_TOTAL'] = number_format($master['GLOBAL_NEW_USER_TOTAL']);
		$master['GLOBAL_NEW_USER_TOTAL_PREV'] = number_format($master['GLOBAL_NEW_USER_TOTAL_PREV']);
		$master['GLOBAL_NEW_USER_TOTAL_AVERAGE'] = number_format($master['GLOBAL_NEW_USER_TOTAL']/7.0, 2);
		$master['GLOBAL_NEW_USER_TOTAL_UPDOWN'] = ($master['GLOBAL_NEW_USER_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($master['GLOBAL_NEW_USER_TOTAL_PREV'] <= $master['GLOBAL_NEW_USER_TOTAL'] ? 'up' : 'down').'.gif">' : ($master['GLOBAL_NEW_USER_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$master['GLOBAL_NEW_USER_TOTAL_PERCENT'] = ($master['GLOBAL_NEW_USER_TOTAL_PREV'] != 0 || $master['GLOBAL_NEW_USER_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($master['GLOBAL_NEW_USER_TOTAL_PREV'] <= $master['GLOBAL_NEW_USER_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($master['GLOBAL_NEW_USER_TOTAL_PREV'] != 0 ? ($master['GLOBAL_NEW_USER_TOTAL'] - $master['GLOBAL_NEW_USER_TOTAL_PREV'])/(1.0*$master['GLOBAL_NEW_USER_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : '');
		$master['GLOBAL_DAU_TOTAL'] = number_format($master['GLOBAL_DAU_TOTAL']);
		$master['GLOBAL_DAU_TOTAL_PREV'] = number_format($master['GLOBAL_DAU_TOTAL_PREV']);
		$master['GLOBAL_DAU_TOTAL_AVERAGE'] = number_format($master['GLOBAL_DAU_TOTAL']/7.0, 2);
		$master['GLOBAL_DAU_TOTAL_UPDOWN'] = ($master['GLOBAL_DAU_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($master['GLOBAL_DAU_TOTAL_PREV'] <= $master['GLOBAL_DAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($master['GLOBAL_DAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$master['GLOBAL_DAU_TOTAL_PERCENT'] = ($master['GLOBAL_DAU_TOTAL_PREV'] != 0 || $master['GLOBAL_DAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($master['GLOBAL_DAU_TOTAL_PREV'] <= $master['GLOBAL_DAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($master['GLOBAL_DAU_TOTAL_PREV'] != 0 ? ($master['GLOBAL_DAU_TOTAL'] - $master['GLOBAL_DAU_TOTAL_PREV'])/(1.0*$master['GLOBAL_DAU_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : '');
		$master['GLOBAL_MAU_TOTAL'] = number_format($master['GLOBAL_MAU_TOTAL']);
		$master['GLOBAL_MAU_TOTAL_PREV'] = number_format($master['GLOBAL_MAU_TOTAL_PREV']);
		$master['GLOBAL_MAU_TOTAL_AVERAGE'] = number_format($master['GLOBAL_MAU_TOTAL']/7.0, 2);
		$master['GLOBAL_MAU_TOTAL_UPDOWN'] = ($master['GLOBAL_MAU_TOTAL_PREV'] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($master['GLOBAL_MAU_TOTAL_PREV'] <= $master['GLOBAL_MAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($master['GLOBAL_MAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
		$master['GLOBAL_MAU_TOTAL_PERCENT'] = ($master['GLOBAL_MAU_TOTAL_PREV'] != 0 || $master['GLOBAL_MAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($master['GLOBAL_MAU_TOTAL_PREV'] <= $master['GLOBAL_MAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($master['GLOBAL_MAU_TOTAL_PREV'] != 0 ? ($master['GLOBAL_MAU_TOTAL'] - $master['GLOBAL_MAU_TOTAL_PREV'])/(1.0*$master['GLOBAL_MAU_TOTAL_PREV']) : 1)*100, 2).'%</strong>' : '');
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
	public function report($ref = null)
	{
		//set_time_limit(5*60);
		set_time_limit(0);

		/* init */
		$this->load->library('parser');

		$allowed_client_ids = array('52ea1eab8d8c89401c0000d9' /* demo */, '52ea1ec18d8c89780700006f' /* MTD */, '52ea1efe8d8c896421000064' /* Playboy */, '52ea1ebc8d8c89001a00004d' /* true */, '52ea1eab8d8c89401c0000db' /* burufly */);
		$allowed_site_ids = array('52ea1eac8d8c89401c0000e5' /* demo */, '52ea1ec18d8c897807000077' /* MTD */, '52ea1eff8d8c89642100006d' /* Playboy */, '52ea1ebd8d8c89001a00004e' /* true */, '52ea1eac8d8c89401c0000e7' /* burufly */);
		$not_allowed_email_client_ids = array('52ea1efe8d8c896421000064' /* Playboy */, '52ea1ebc8d8c89001a00004d' /* true */, '52ea1eab8d8c89401c0000db' /* burufly */);

		/* init, from-to */
		$to = $ref ? $ref : date('Y-m-d', strtotime('-1 day', time())); // default reference date is yesterday
		$from = date('Y-m-d', strtotime('-1 week', strtotime($to)));
		$from2 = date('Y-m-d', strtotime('-1 week', strtotime($from)));
		echo "<pre>from2 = $from2, from = $from, to = $to</pre>";

		/* query and process data */
		$master = $this->initData($from, $to);
		$clients = $this->client_model->listClients();
		$sites = array();
		foreach ($this->client_model->listAllSites() as $site) {
			$key = (string)$site['client_id'];
			if (array_key_exists($key, $sites)) {
				$sites[$key][] = $site;
			} else {
				$sites[$key] = array($site);
			}

		}
		echo '<pre>#clients = '.count($clients).'</pre>';
		echo '<pre>#sites = '.count($sites).'</pre>';
		$this->elapsed_time('report');
		if (is_array($clients)) foreach ($clients as $i => $c) {
			print "i = $i<br>";
			$this->elapsed_time($i);

			$client_id = $c['_id']; //echo '<pre>';var_dump($c);echo '</pre>';
			$key = (string)$client_id;
			if (!in_array($key, $allowed_client_ids)) continue;
			print('client_id = '.$key.'<br>');

			if (array_key_exists($key, $sites) && is_array($sites[$key])) foreach ($sites[$key] as $j => $s) {
				print "j = $j<br>";
				$site_id = $s['_id']; //echo '<pre>';var_dump($s);echo '</pre>';

				if (!in_array((string)$site_id, $allowed_site_ids)) continue;
				print('site_id = '.(string)$site_id.'<br>');

				$params = $this->getData(array('client_id' => $client_id, 'site_id' => $site_id), $c, $s, $to, $from, $from2); //echo '<pre>';var_dump($params);echo '</pre>';
				$this->incrementData($master, $params);
				$this->formatData($params);
				$html = $this->parser->parse('report.html', $params, true); //echo '<pre>';var_dump($html);echo '</pre>';
				$this->saveFile('report/'.$params['DIR'], $params['FILE'], str_replace('{'.CANNOT_VIEW_EMAIL.'}', '', $html));

				if (in_array((string)$client_id, $not_allowed_email_client_ids)) continue;

				/* email */
				$email_from = 'info@playbasis.com';
				//$email_to = $params['CLIENT_EMAIL'];
				$email_to = array('devteam@playbasis.com', 'tanawat@playbasis.com', 'notjiam@gmail.com');
				$subject = "[Playbasis] Weekly Report for ".$params['SITE_NAME'];
				$message = str_replace('{'.CANNOT_VIEW_EMAIL.'}', '<tr><td align="center"><span style="color: #999999;font-size: 13px">If you cannot view this email, please <a href="'.$params['REPORT_URL'].'" style="color: #0a92d9;font-size: 13px">click here</a></span></td></tr>', $html);
				$resp = $this->email($email_from, $email_to, $subject, $message); echo '<pre>';var_dump($resp);echo '</pre>';
			}
			print('Running time = '.$this->elapsed_time($i).' sec<br>');
		}
		$this->finalizeData($master);
		$html = $this->parser->parse('report_master.html', $master, true); //echo '<pre>';var_dump($html);echo '</pre>';
		$this->saveFile('report/'.$master['DIR'], $master['FILE'], str_replace('{'.CANNOT_VIEW_EMAIL.'}', '', $html));
		//echo '<pre>';var_dump($master);echo '</pre>';

		/* email */
		$email_from = 'info@playbasis.com';
		$email_to = array('devteam@playbasis.com', 'tanawat@playbasis.com', 'notjiam@gmail.com');
		$subject = "[Playbasis] Weekly Master Report";
		$message = str_replace('{'.CANNOT_VIEW_EMAIL.'}', '<tr><td align="center"><span style="color: #999999;font-size: 13px">If you cannot view this email, please <a href="'.$master['REPORT_URL'].'" style="color: #0a92d9;font-size: 13px">click here</a></span></td></tr>', $html);
		$resp = $this->email($email_from, $email_to, $subject, $message); echo '<pre>';var_dump($resp);echo '</pre>';
		print('Total running time = '.$this->elapsed_time('report').' sec<br>');
	}
	/*
    public function memtest()
    {
        $this->load->model('auth_model');
        $this->load->model('action_model');
        $this->load->model('badge_model');
        $this->load->model('client_model');
        $this->load->model('player_model');
        $this->load->model('point_model');

        $validToken = $this->auth_model->createTokenFromAPIKey('abc');
        $hasAction = $this->action_model->findAction(array_merge($validToken, array(
            'action_name' => 'read'
        )));

        echo "findAction model return : ".$hasAction."<br><br>";

        $API['key'] = "abc";
        $API['secret'] = "abcde";
        $clientInfo = $this->auth_model->getApiInfo($API);

        echo "getApiInfo model return : ".var_dump($clientInfo)."<br><br>";

        $token = $this->auth_model->generateToken(array_merge($clientInfo, $API));

        echo "generateToken model return : ".var_dump($token)."<br><br>";

        $findToken = $this->auth_model->findToken($token['token']);

        echo "findToken model return : ".var_dump($findToken)."<br><br>";

//        $createToken = $this->auth_model->createToken("1", "1");
//
//        echo "createToken model return : ".var_dump($createToken)."<br><br>";
//
//        $createTokenFromAPIKey = $this->auth_model->createTokenFromAPIKey("abc");
//
//        echo "createTokenFromAPIKey model return : ".var_dump($createTokenFromAPIKey)."<br><br>";

        $badge = $this->badge_model->getBadge(array_merge($validToken, array(
            'badge_id' => 1
        )));

        echo "getBadge model return : ".var_dump($badge)."<br><br>";

        $badges = $this->badge_model->getAllBadges(array_merge($validToken));

        echo "getBadgegetAllBadges model return : ".var_dump($badges)."<br><br>";

        $collection = $this->badge_model->getCollection(array_merge($validToken, array(
            'collection_id' => 1
        )));

        echo "getCollection model return : ".var_dump($collection)."<br><br>";

        $collections = $this->badge_model->getAllCollection(array_merge($validToken));

        echo "getAllCollection model return : ".var_dump($collections)."<br><br>";

        $ruleSet = $this->client_model->getRuleSet(array(
            'client_id' => 1,
            'site_id' => 1
        ));

        echo "getRuleSet model return : ".var_dump($ruleSet)."<br><br>";

        $getAcion = $this->client_model->getActionId(array(
            'client_id' => 1,
            'site_id' => 1,
            'action_name' => 'like'
        ));

        echo "getActionId model return : ".$getAcion."<br><br>";

        $ruleSet = $this->client_model->getRuleSetByActionId(array(
            'client_id' => 1,
            'site_id' => 1,
            'action_id' => 1
        ));

        echo "getRuleSetByActionId model return : ".var_dump($ruleSet)."<br><br>";

		$processor = $this->client_model->getJigsawProcessor(1,1);

        echo "getJigsawProcessor model return : ".$processor."<br><br>";

		$badgePlayer = $this->client_model->updateplayerBadge(1, 1, 1, 1);

        echo "updateplayerBadge model return : ".$badgePlayer."<br><br>";

        $lv = $this->client_model->updateExpAndLevel(1, 1, 1, array(
            'client_id' => $validToken['client_id'],
            'site_id' => $validToken['site_id']
        ));

        echo "updateExpAndLevel model return : ".$lv."<br><br>";

		$badge = $this->client_model->getBadgeById(1,1);

        echo "getBadgeById model return : ".var_dump($badge)."<br><br>";

        $player = $this->player_model->readPlayer(1, 1, array(
            'username',
            'first_name',
            'last_name',
            'gender',
            'image',
            'exp',
            'level',
            'date_added',
            'birth_date'
        ));

        echo "readPlayer model return : ".var_dump($player)."<br><br>";

        //$players = $this->player_model->readPlayers(1, array(
        //    'username',
        //    'first_name',
        //    'last_name',
        //    'gender',
        //    'image',
        //    'exp',
        //    'level',
        //    'date_added',
        //    'birth_date'
        //), 0, 10);

        //echo "readPlayers model return : ".var_dump($players)."<br><br>";

        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($validToken, array(
            'cl_player_id' => 1
        )));

        echo "getPlaybasisId model return : ".$pb_player_id."<br><br>";

		$cl_player_id = $this->player_model->getClientPlayerId(1,1);

        echo "getClientPlayerId model return : ".$cl_player_id."<br><br>";

		$points = $this->player_model->getPlayerPoints(1,1);

        echo "getPlayerPoints model return : ".var_dump($points)."<br><br>";

		$points = $this->player_model->getPlayerPoint(1,1,1);

        echo "getPlayerPoint model return : ".var_dump($points)."<br><br>";

		$actions = $this->player_model->getLastActionPerform(1,1);

        echo "getLastActionPerform model return : ".var_dump($actions)."<br><br>";

		$actions = $this->player_model->getActionPerform(1,1,1);

        echo "getActionPerform model return : ".var_dump($actions)."<br><br>";

		$actions = $this->player_model->getActionCount(1,1,1);

        echo "getActionCount model return : ".var_dump($actions)."<br><br>";

        $badge = $this->badge_model->getBadge(array_merge($validToken, array(
            'badge_id' => 1
        )));

        echo "getBadge model return : ".var_dump($badge)."<br><br>";

		$player = $this->player_model->getLastEventTime(1,1, 'LOGIN');

        echo "getLastEventTime model return : ".$player."<br><br>";

        $leaderboard = $this->player_model->getLeaderboard("point", 10, $validToken['client_id'], $validToken['site_id']);

        echo "getLeaderboard model return : ".var_dump($leaderboard)."<br><br>";

        $input = array_merge($validToken, array(
            'pb_player_id' => 1
        ));
        $point = $this->point_model->getRewardNameById(array_merge($input, array(
            'reward_id' => 1
        )));

        echo "getRewardNameById model return : ".$point."<br><br>";

        $input = array_merge($validToken, array(
            'reward_name' => 'point'
        ));
        $haspoint = $this->point_model->findPoint($input);

        echo "findPoint model return : ".$haspoint."<br><br>";

    }
	*/
}
?>