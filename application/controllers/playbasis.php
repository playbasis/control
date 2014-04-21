<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('STATIC_IMAGE_URL', 'http://admin.pbapp.net');
define('DYNAMIC_IMAGE_URL', 'http://images.pbapp.net');

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
function cmp2($a, $b) {
	if ($a[1] == $b[1]) {
		return 0;
	}
	return -1*(($a[1] < $b[1]) ? -1 : 1);
}
function cmp3($a, $b) {
	if ($a['FEED_DATE'] == $b['FEED_DATE']) {
		return 0;
	}
	return -1*(($a['FEED_DATE'] < $b['FEED_DATE']) ? -1 : 1);
}
function cmp4($a, $b) {
	if ($a['ITEM_TOTAL'] == $b['ITEM_TOTAL']) {
		return 0;
	}
	return -1*(($a['ITEM_TOTAL'] < $b['ITEM_TOTAL']) ? -1 : 1);
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
	public function report($ref = null)
	{
		$to = $ref ? $ref : date('Y-m-d', strtotime('-1 day', time())); // default reference date is yesterday
		$from = date('Y-m-d', strtotime('-1 week', strtotime($to)));
		$from2 = date('Y-m-d', strtotime('-1 week', strtotime($from)));
		$data = array();
		echo "<pre>from2 = $from2, from = $from, to = $to</pre>";
		foreach ($this->client_model->listClients() as $c) {
			$client_id = $c['_id'];
			$client_name = $c['first_name'].' '.$c['last_name'];
			$client_company = $c['company'];
			$client_email = $c['email'];

			if (!in_array($client_id, array(new MongoId('52ea1eab8d8c89401c0000d9'), new MongoId('52ea1ec18d8c89780700006f')))) continue;
			$data['client_id'] = $client_id;

			foreach ($this->client_model->listSites($client_id) as $s) {
				$site_id = $s['_id'];
				$site_name = $s['site_name'];

				if (!in_array($site_id, array(new MongoId('52ea1eac8d8c89401c0000e5'), new MongoId('52ea1ec18d8c897807000077')))) continue;
				$data['site_id'] = $site_id;

				echo '<pre>';var_dump($c);echo '</pre>';
				echo '<pre>';var_dump($s);echo '</pre>';

				// params
				$params = array(
					'STATIC_IMAGE_URL' => STATIC_IMAGE_URL,
					'DYNAMIC_IMAGE_URL' => DYNAMIC_IMAGE_URL,
					'CLIENT_NAME' => $client_company,
					'SITE_NAME' => $site_name,
					'FROM' => date('d M Y', strtotime('+1 day', strtotime($from))),
					'TO' => date('d M Y', strtotime($to)),
				);

				// new users
				$curr = $this->player_model->new_registration($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'new regis1 = '; var_dump($curr);echo '</pre>';
				$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
				$best = $sum_max[1];
				$params['NEW_USER_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
				$params['NEW_USER_TOTAL'] = $sum_max[0];
				$prev = $this->player_model->new_registration($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'new regis2 = '; var_dump($prev);echo '</pre>';
				$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
				$params['NEW_USER_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['NEW_USER_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['NEW_USER_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
				$params['NEW_USER_PERCENT'] = ($sum_max[0] != 0 || $params['NEW_USER_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['NEW_USER_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['NEW_USER_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
				$params['NEW_USER_AVERAGE'] = number_format($params['NEW_USER_TOTAL']/7.0, 2);
				$params['NEW_USER_BEST_VALUE'] = $best != -1 ? $curr[$best]['value'] : 0;

				// DAU
				$curr = $this->player_model->daily_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'DAU1 = ';var_dump($curr);echo '</pre>';
				$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
				$best = $sum_max[1];
				$params['DAU_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
				$params['DAU_TOTAL'] = $sum_max[0];
				$prev = $this->player_model->daily_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'DAU2 = ';var_dump($prev);echo '</pre>';
				$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
				$params['DAU_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['DAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['DAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
				$params['DAU_PERCENT'] = ($sum_max[0] != 0 || $params['DAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['DAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['DAU_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
				$params['DAU_AVERAGE'] = number_format($params['DAU_TOTAL']/7.0, 2);
				$params['DAU_BEST_VALUE'] = $best != -1 ? $curr[$best]['value'] : 0;

				// MAU
				$curr = $this->player_model->monthy_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to); //echo '<pre>';echo 'MAU1 = ';var_dump($curr);echo '</pre>';
				$sum_max = get_sum_and_max($curr); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $curr[$sum_max[1]] : "",true);echo '</pre>';
				$best = $sum_max[1];
				$params['MAU_BEST_DAY'] = ($best != -1 ? date('d M Y', strtotime($curr[$best]['_id'])) : '');
				$params['MAU_TOTAL'] = $sum_max[0];
				$prev = $this->player_model->monthy_active_user_per_day($data, date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from); //echo '<pre>';echo 'MAU2 = ';var_dump($prev);echo '</pre>';
				$sum_max = get_sum_and_max($prev); //echo '<pre>';echo 'sum = '.$sum_max[0].', max = '.print_r($sum_max[1] != -1 ? $prev[$sum_max[1]] : "",true);echo '</pre>';
				$params['MAU_UPDOWN'] = ($sum_max[0] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($sum_max[0] <= $params['MAU_TOTAL'] ? 'up' : 'down').'.gif">' : ($params['MAU_TOTAL'] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : ''));
				$params['MAU_PERCENT'] = ($sum_max[0] != 0 || $params['MAU_TOTAL'] != 0 ? '<strong style="font-size:12px;color:'.($sum_max[0] <= $params['MAU_TOTAL'] ? '#95cc00' : 'red').'">'.number_format(($sum_max[0] != 0 ? ($params['MAU_TOTAL'] - $sum_max[0])/(1.0*$sum_max[0]) : 1)*100, 2).'%</strong>' : '');
				$params['MAU_AVERAGE'] = number_format($params['MAU_TOTAL']/7.0, 2);
				$params['MAU_BEST_VALUE'] = $best != -1 ? $curr[$best]['value'] : 0;

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
				usort($result, 'cmp2');
				//echo '<pre>';var_dump($result);echo '</pre>';
				$params['ACTIONS'] = array();
				if (is_array($result)) foreach ($result as $i => $action) {
					$params['ACTIONS'][] = array(
						'ACTION_BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
						'ACTION_IMAGE' => str_replace('-alt', '', $action[0]['icon']),
						'ACTION_NAME' => $action[0]['name'],
						'ACTION_TOTAL' => $action[1],
						'ACTION_UPDOWN' => ($action[2] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($action[2] <= $action[1] ? 'up' : 'down').'.gif">' : ($action[1] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
						'ACTION_PERCENT' => ($action[2] != 0 || $action[1] != 0 ? '<strong style="font-size:12px;color:'.($action[2] <= $action[1] ? '#95cc00' : 'red').'">'.number_format(($action[2] != 0 ? ($action[1] - $action[2])/(1.0*$action[2]) : 1)*100, 2).'%</strong>' : ''),
						'ACTION_AVERAGE' => number_format($action[1]/7.0, 2),
					);
					//if ($i == 4) break;
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
				usort($result, 'cmp2');
				//echo '<pre>';var_dump($result);echo '</pre>';
				$params['BADGES'] = array();
				if (is_array($result)) foreach ($result as $i => $badge) {
					$params['BADGES'][] = array(
						'BADGE_BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
						'BADGE_IMAGE_SRC' => $badge[0]['image'],
						'BADGE_NAME' => $badge[0]['name'],
						'BADGE_TOTAL' => $badge[1],
						'BADGE_UPDOWN' => ($badge[2] != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($badge[2] <= $badge[1] ? 'up' : 'down').'.gif">' : ($badge[1] != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
						'BADGE_PERCENT' => ($badge[2] != 0 || $badge[1] != 0 ? '<strong style="font-size:12px;color:'.($badge[2] <= $badge[1] ? '#95cc00' : 'red').'">'.number_format(($badge[2] != 0 ? ($badge[1] - $badge[2])/(1.0*$badge[2]) : 1)*100, 2).'%</strong>' : ''),
						'BADGE_AVERAGE' => number_format($badge[1]/7.0, 2),
					);
					//if ($i == 4) break;
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
						'ITEM_BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
						'ITEM_IMAGE_SRC' => url_exist($goods_image) ? DYNAMIC_IMAGE_URL.'/images/'.$goods_image : STATIC_IMAGE_URL.'/images/no_image.jpg',
						'ITEM_NAME' => $goods_name,
						'ITEM_START_DATE' => ($goods_start_date ? date('d M Y', $goods_start_date->sec) : "Not Set"),
						'ITEM_EXPIRATION_DATE' => ($goods_expiration_date ? date('d M Y', $goods_expiration_date->sec) : "Not Set"),
						'ITEM_TOTAL' => $curr,
						'ITEM_UPDOWN' => ($prev != 0 ? '<img src="'.STATIC_IMAGE_URL.'/images/icon-'.($prev <= $curr ? 'up' : 'down').'.gif">' : ($curr != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
						'ITEM_PERCENT' => ($prev != 0 || $curr != 0 ? '<strong style="font-size:12px;color:'.($prev <= $curr ? '#95cc00' : 'red').'">'.number_format(($prev != 0 ? ($curr - $prev)/(1.0*$prev) : 1)*100, 2).'%</strong>' : ''),
						'ITEM_PEOPLE_CAN_REDEEM' => $goods_players_can_redeem,
						'ITEM_QTY_REDEEMED' => $goods_qty_redeemed,
						'ITEM_QTY_TOTAL' => ($goods_qty != null || $goods_qty === 0 ? $goods_qty : 'Inf.'),
						'ITEM_QTY_REMAIN' => $goods_qty_remain,
					);
				}
				usort($params['ITEMS'], 'cmp4');

				// rank
				$players = $this->player_model->getLeaderboardByLevel(20, $client_id, $site_id);
				//echo '<pre>';var_dump($players);echo '</pre>';
				$params['PLAYERS'] = array();
				if (is_array($players)) foreach ($players as $i => $player) {
					$params['PLAYERS'][] = array(
						'PLAYER_ROW' => $i+1,
						'PLAYER_IMAGE_SRC' => url_exist($player['image']) ? $player['image'] : STATIC_IMAGE_URL.'/images/user_no_image.jpg',
						'PLAYER_NAME' => $player['first_name'].' '.$player['last_name'],
						'PLAYER_EXP' => $player['exp'],
						'PLAYER_LEVEL' => $player['level'],
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
				usort($params['FEEDS'], 'cmp3');

				// html
				$this->load->library('parser');
				$message = $this->parser->parse('report.html', $params, true);
				file_put_contents('weekly-report_'.$client_id.'-'.$site_id.'.html', $message);

				// email
				$subject = "[Playbasis] Weekly Report for $site_name";
				$this->amazon_ses->from('info@playbasis.com');
				//$this->amazon_ses->to($client_email);
				$this->amazon_ses->to(array('devteam@playbasis.com','tanawat@playbasis.com','notjiam@gmail.com'));
				$this->amazon_ses->subject($subject);
				$this->amazon_ses->message($message);
				$resp = $this->amazon_ses->send();
				echo '<pre>';var_dump($resp);echo '</pre>';
			}
		}
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