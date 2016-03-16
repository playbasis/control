<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

define('MAX_EXECUTION_TIME', 0);
define('MAX_MEMORY', '256M');

define('SITE_ID_DEMO', '52ea1eac8d8c89401c0000e5');
define('SITE_ID_MTD', '52ea1ec18d8c897807000077');
define('SITE_ID_PLAYBOY', '52ea1eff8d8c89642100006d');
define('SITE_ID_TRUE', '52ea1ebd8d8c89001a00004e');
define('SITE_ID_BURUFLY', '52ea1eac8d8c89401c0000e7');
define('SITE_ID_ASSOPOKER', '53a9422f988040355a8b45d3');
define('SITE_ID_TRUE_MONEY', '5423ce3dbe120b680f8b456c');
define('SITE_ID_COMPARE_AND_SHARE', '5461fd2d99804019418b5025');
define('SITE_ID_CHIANGMAI_U', '5424045598804099678b457b');

define('CANNOT_VIEW_EMAIL', 'CANNOT_VIEW_EMAIL');
define('NUMBER_OF_PLAYERS', 20);
define('NUMBER_OF_ACTIONS', -1);
define('NUMBER_OF_BADGES', -1);
define('NUMBER_OF_ITEMS', -1);
define('NUMBER_OF_FEEDS_PER_SITE', 1);
define('FEED_CACHE_IN_MINUTES', 30);
define('FEED_CHARACTER_LIMIT', 256);
define('ITEM_DATE_NOT_CONFIG', 'Not Set');
define('ITEM_QTY_NOT_CONFIG', 'Inf.');
define('REPORT_DATE_FORMAT', 'd M Y');

define('PERCENT_UPDOWN_HIGH', 0.5);

class Report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->model('action_model');
        $this->load->model('badge_model');
        $this->load->model('client_model');
        $this->load->model('email_model');
        $this->load->model('goods_model');
        $this->load->model('player_model');
        $this->load->model('reward_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/utility', 'utility');
        $this->load->library('mongo_db');
        $this->load->library('parser');
        $this->load->library('mpdf');
        $this->load->library('rssparser');
    }

    public function generate($ref = null)
    {
        $msg = array();
        $this->utility->elapsed_time('report');

        /* init */
        $allowed_site_ids = array( // true = send email, false = will not send
            SITE_ID_DEMO => true,
            SITE_ID_TRUE_MONEY => true,
        );
        $to_pbteam_email = array(
            'devteam@playbasis.com',
            'notjiam@gmail.com',
            'pascal@playbasis.com',
            'mariya.v@playbasis.com'
        );
        $conf = array(
            'static_image_url' => $this->config->item('STATIC_IMG_PATH'),
            'dynamic_image_url' => $this->config->item('IMG_PATH'),
            'disable_url_exists' => $this->config->item('REPORT_SKIP_CHECK_IMG_PATH'),
            'report_dir' => $this->config->item('REPORT_DIR'),
            'report_url' => $this->config->item('REPORT_URL'),
            'report_pdf' => $this->config->item('REPORT_PDF'),
            'report_email' => $this->config->item('REPORT_EMAIL'),
            'report_email_client' => $this->config->item('REPORT_EMAIL_CLIENT'),
        );

        set_time_limit(MAX_EXECUTION_TIME);
        ini_set('memory_limit', MAX_MEMORY);

        /* set from-to dates */
        $to = $ref ? $ref : date('Y-m-d', strtotime('-1 day', time())); // yesterday is default
        $from = date('Y-m-d', strtotime('-1 week', strtotime($to)));
        $from2 = date('Y-m-d', strtotime('-1 week', strtotime($from)));
        log_message('debug', "from2 = $from2, from = $from, to = $to");

        /* pre-calculated info */
        $clients = array();
        $sites = $this->client_model->listSites();
        foreach ($sites as $site) {
            $client_id = $site['client_id'];
            if (array_key_exists((string)$client_id, $clients)) {
                continue;
            }
            $clients[(string)$client_id] = $this->client_model->getById($client_id);
        }
        log_message('debug', 'clients = ' . print_r($clients, true));

        /* query and process data */
        $this->utility->elapsed_time('master_init');
        $master = $this->master_init($conf, $to, $from, $from2);
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('master_init') . ' sec (master_init)');
        foreach ($sites as $site) {
            $client_id = $site['client_id'];
            $site_id = $site['_id'];

            if (!array_key_exists((string)$site_id, $allowed_site_ids)) {
                continue;
            }

            log_message('debug', 'site = ' . print_r($site, true));
            $this->utility->elapsed_time('data');
            $params = $this->build_data($conf, $clients[(string)$client_id], $site, $to, $from, $from2);
            log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('data') . ' sec (build_data)');
            log_message('debug', 'params = ' . print_r($params, true));

            $html = $this->parser->parse('report.html', $params, true);
            $this->utility->save_file($conf['report_dir'] . $params['DIR'], $params['FILE'],
                str_replace('{' . CANNOT_VIEW_EMAIL . '}', '', $html));
            //log_message('debug', 'html = '.print_r($html, true));

            $pdf_html = $this->parser->parse('report_pdf.html', $params, true);
            $this->utility->save_file($conf['report_dir'] . $params['DIR'],
                str_replace('.html', '.pdf.html', $params['FILE']), $pdf_html);
            //log_message('debug', 'pdf_html = '.print_r($pdf_html, true));

            if ($conf['report_pdf']) {
                $pdf = $this->utility->html2mpdf($pdf_html, true);
                $this->utility->save_file($conf['report_dir'] . $params['DIR'],
                    str_replace('.html', '.pdf', $params['FILE']), $pdf);
                log_message('debug', 'pdf = DONE');
            }

            $this->master_accumulate($conf, $master, $params);

            if (!$allowed_site_ids[(string)$site_id]) {
                continue;
            }

            if ($conf['report_email']) {
                $this->utility->elapsed_time('email');
                $email_to = array_merge(
                    $conf['report_email_client'] ? array($params['CLIENT_EMAIL']) : array(),
                    $to_pbteam_email
                );
                $subject = '[Playbasis] Weekly Report for ' . $params['SITE_NAME'] . ' (' . $params['FROM'] . ' - ' . $params['TO'] . ')';
                $message = str_replace('{' . CANNOT_VIEW_EMAIL . '}',
                    '<tr><td align="center"><span style="color: #999999;font-size: 13px">If you cannot view this email, please <a href="' . $params['REPORT_URL'] . '" style="color: #0a92d9;font-size: 13px">click here</a></span></td></tr>',
                    $html);
                $file_path = $conf['report_dir'] . $params['DIR'] . '/' . str_replace('.html', '.pdf', $params['FILE']);
                $file_name = 'report-' . $params['SITE_NAME'] . '-' . str_replace('.html', '.pdf', $params['FILE']);
                $resp = $this->utility->email(EMAIL_FROM, $email_to, $subject, $message,
                    'If you cannot view this email, please visit ' . $params['REPORT_URL'],
                    $conf['report_pdf'] ? array($file_path => $file_name) : array());
                $this->email_model->log(EMAIL_TYPE_REPORT, $client_id, $site_id, $resp, EMAIL_FROM, $email_to, $subject,
                    $message, 'If you cannot view this email, please visit ' . $params['REPORT_URL'], array());
                log_message('debug', 'email = ' . print_r($resp, true));
                log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('email') . ' sec (email)');
            }
        }
        $this->master_finalize($conf, $master);
        $master_clients = $master['CLIENTS'];
        usort($master_clients, 'compare_SITE_NAME_asc');
        $master['CLIENTS'] = $master_clients;
        log_message('debug', 'master = ' . print_r($master, true));

        $html = $this->parser->parse('report_master.html', $master, true);
        $this->utility->save_file($conf['report_dir'] . $master['DIR'], $master['FILE'],
            str_replace('{' . CANNOT_VIEW_EMAIL . '}', '', $html));
        log_message('debug', 'html = ' . print_r($html, true));

        $pdf_html = $this->parser->parse('report_master_pdf.html', $master, true);
        $this->utility->save_file($conf['report_dir'] . $master['DIR'],
            str_replace('.html', '.pdf.html', $master['FILE']), $pdf_html);
        log_message('debug', 'pdf_html = ' . print_r($pdf_html, true));

        if ($conf['report_pdf']) {
            $pdf = $this->utility->html2mpdf($pdf_html, true);
            $this->utility->save_file($conf['report_dir'] . $master['DIR'],
                str_replace('.html', '.pdf', $master['FILE']), $pdf);
            log_message('debug', 'pdf = DONE');
        }

        if ($conf['report_email']) {
            $this->utility->elapsed_time('email');
            $email_to = $to_pbteam_email;
            $subject = '[Playbasis] Weekly Master Report' . ' (' . $master['FROM'] . ' - ' . $master['TO'] . ')';
            $message = str_replace('{' . CANNOT_VIEW_EMAIL . '}',
                '<tr><td align="center"><span style="color: #999999;font-size: 13px">If you cannot view this email, please <a href="' . $master['REPORT_URL'] . '" style="color: #0a92d9;font-size: 13px">click here</a></span></td></tr>',
                $html);
            $file_path = $conf['report_dir'] . $master['DIR'] . '/' . str_replace('.html', '.pdf', $master['FILE']);
            $file_name = 'report-master-' . str_replace('.html', '.pdf', $master['FILE']);
            $resp = $this->utility->email(EMAIL_FROM, $email_to, $subject, $message,
                'If you cannot view this email, please visit ' . $master['REPORT_URL'],
                $conf['report_pdf'] ? array($file_path => $file_name) : array());
            $this->email_model->log(EMAIL_TYPE_REPORT, null, null, $resp, EMAIL_FROM, $email_to, $subject, $message,
                'If you cannot view this email, please visit ' . $master['REPORT_URL'], array());
            log_message('debug', 'email = ' . print_r($resp, true));
            log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('email') . ' sec (email)');
        }

        $msg['elapsed_time'] = $this->utility->elapsed_time('report');
        $this->response($this->resp->setRespond($msg), 200);
    }

    private function build_data($conf, $client, $site, $to, $from, $from2)
    {
        $params = array(
            'STATIC_IMAGE_URL' => $conf['static_image_url'],
            'DYNAMIC_IMAGE_URL' => $conf['dynamic_image_url'],
            'CLIENT_ID' => $site['client_id'],
            'CLIENT_NAME' => $client['first_name'],
            'CLIENT_EMAIL' => $client['email'],
            'SITE_ID' => $site['_id'],
            'SITE_NAME' => $site['site_name'],
            'FROM' => date(REPORT_DATE_FORMAT, strtotime('+1 day', strtotime($from))),
            'TO' => date(REPORT_DATE_FORMAT, strtotime($to)),
        );
        $params['DIR'] = $params['CLIENT_ID'] . '/' . $params['SITE_ID'];
        $params['FILE'] = "$to.html";
        $params['REPORT_URL'] = $conf['report_url'] . $params['DIR'] . '/' . $params['FILE'];

        $opts = array('client_id' => $site['client_id'], 'site_id' => $site['_id']);

        // PLAYERS
        $this->utility->elapsed_time('PLAYERS');
        log_message('debug', 'PLAYERS start');
        $params = array_merge($params,
            $this->get_stat('TOTAL_USER_', $conf, $this->player_model->new_registration($opts, null, $to),
                $this->player_model->new_registration($opts, null, $from)));
        $params = array_merge($params, $this->get_stat('NEW_USER_', $conf,
            $this->player_model->new_registration($opts, date('Y-m-d', strtotime('+1 day', strtotime($from))), $to),
            $this->player_model->new_registration($opts, date('Y-m-d', strtotime('+1 day', strtotime($from2))),
                $from)));
        $params = array_merge($params, $this->get_stat('DAU_', $conf,
            $this->player_model->daily_active_user_per_day($opts, date('Y-m-d', strtotime('+1 day', strtotime($from))),
                $to),
            $this->player_model->daily_active_user_per_day($opts, date('Y-m-d', strtotime('+1 day', strtotime($from2))),
                $from)));
        $params = array_merge($params, $this->get_stat('MAU_', $conf,
            $this->player_model->monthy_active_user_per_day($opts, date('Y-m-d', strtotime('+1 day', strtotime($from))),
                $to), $this->player_model->monthy_active_user_per_day($opts,
                date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from)));
        log_message('debug', 'PLAYERS end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('PLAYERS') . ' sec (PLAYERS)');

        // ACTIONS
        $this->utility->elapsed_time('ACTIONS');
        log_message('debug', 'ACTIONS start');
        $arr = array();
        $actions = $this->action_model->listActions($opts);
        if (is_array($actions)) {
            foreach ($actions as $action) {
                $stat = $this->get_stat('', $conf, $this->action_model->actionLogPerAction($opts, $action['name'],
                    date('Y-m-d', strtotime('+1 day', strtotime($from))), $to),
                    $this->action_model->actionLogPerAction($opts, $action['name'],
                        date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from));
                if ($stat['TOTAL_NUM'] == 0 && $stat['TOTAL_PREV_NUM'] == 0) {
                    continue;
                } // skip inactive data points
                $arr[] = array_merge(
                    array(
                        'IMAGE_SRC' => $conf['static_image_url'] . 'images/' . str_replace('-alt', '',
                                $action['icon']) . '.gif',
                        'NAME' => $action['name'],
                    ),
                    $stat
                );
            }
        }
        usort($arr, 'compare_TOTAL_NUM_desc');
        $params['ACTIONS'] = array();
        foreach ($arr as $i => $each) {
            $each['BG_COLOR'] = ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : '');
            $params['ACTIONS'][] = $each;
            if (NUMBER_OF_ACTIONS > 0 && $i == (NUMBER_OF_ACTIONS - 1)) {
                break;
            }
        }
        log_message('debug', 'ACTIONS end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('ACTIONS') . ' sec (ACTIONS)');

        // BADGES
        $this->utility->elapsed_time('BADGES');
        log_message('debug', 'BADGES start');
        $arr = array();
        $badges = $this->badge_model->getAllBadges($opts);
        if (is_array($badges)) {
            foreach ($badges as $badge) {
                $stat = $this->get_stat('', $conf, $this->reward_model->badgeLog($opts, $badge['badge_id'],
                    date('Y-m-d', strtotime('+1 day', strtotime($from))), $to),
                    $this->reward_model->badgeLog($opts, $badge['badge_id'],
                        date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from));
                if ($stat['TOTAL_NUM'] == 0 && $stat['TOTAL_PREV_NUM'] == 0) {
                    continue;
                } // skip inactive data points
                $arr[] = array_merge(
                    array(
                        'IMAGE_SRC' => $badge['image'],
                        'NAME' => $badge['name'],
                    ),
                    $stat
                );
            }
        }
        usort($arr, 'compare_TOTAL_NUM_desc');
        $params['BADGES'] = array();
        foreach ($arr as $i => $each) {
            $each['BG_COLOR'] = ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : '');
            $params['BADGES'][] = $each;
            if (NUMBER_OF_BADGES > 0 && $i == (NUMBER_OF_BADGES - 1)) {
                break;
            }
        }
        log_message('debug', 'BADGES end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('BADGES') . ' sec (BADGES)');

        // ITEMS
        $this->utility->elapsed_time('ITEMS');
        log_message('debug', 'ITEMS start');
        $arr = array();
        /* process group */
        $results = $this->goods_model->getGroupsAggregate($opts['site_id']);
        $ids = array();
        $group_name = array();
        foreach ($results as $i => $result) {
            $group = $result['_id']['group'];
            $quantity = $result['quantity'];
            $list = $result['list'];
            $first = array_shift($list); // skip first one
            $group_name[$first->{'$id'}] = array('group' => $group, 'quantity' => $quantity);
            $ids = array_merge($ids, $list);
        }
        $goodsList = $this->goods_model->getAllGoods($opts, $ids);
        $goodsList_ids = array_map('convert_id_to_mongoId', $goodsList);
        $items = array_merge($this->goods_model->listActiveItems(array_merge($opts, array('in' => $goodsList_ids)),
            date('Y-m-d', strtotime('+1 day', strtotime($from))), $to),
            $this->goods_model->listExpiredItems(array_merge($opts, array('in' => $goodsList_ids)),
                date('Y-m-d', strtotime('+1 day', strtotime($from))), $to));
        foreach ($items as $item) {
            $goods_criteria = $item['redeem'];
            $goods_id = array_key_exists('group', $item) ? array_map('index_goods_id',
                $this->goods_model->listGoodsIdsByGroup($opts['client_id'], $opts['site_id'],
                    $item['group'])) : $item['goods_id'];
            $goods_qty_redeemed = $this->goods_model->redeemLogCount($opts, $goods_id);
            $goods_qty_remain = array_key_exists('group',
                $item) ? $group_name[$item['_id']->{'$id'}]['quantity'] : $item['quantity'];
            $goods_qty = $goods_qty_remain !== null ? $goods_qty_remain + $goods_qty_redeemed : $goods_qty_remain;
            $goods_players_can_redeem = $this->player_model->playerWithEnoughCriteria($opts, $goods_criteria);
            $arr[] = array_merge(
                array(
                    'IMAGE_SRC' => $conf['disable_url_exists'] || $this->utility->url_exists($item['image'],
                        $conf['dynamic_image_url']) ? $conf['dynamic_image_url'] . $item['image'] : $conf['static_image_url'] . 'images/no_image.jpg',
                    'NAME' => array_key_exists('group', $item) ? $item['group'] : $item['name'],
                    'START_DATE' => ($item['date_start'] ? date(REPORT_DATE_FORMAT,
                        $item['date_start']->sec) : ITEM_DATE_NOT_CONFIG),
                    'EXPIRATION_DATE' => ($item['date_expire'] ? date(REPORT_DATE_FORMAT,
                        $item['date_expire']->sec) : ITEM_DATE_NOT_CONFIG),
                    'PEOPLE_CAN_REDEEM' => number_format($goods_players_can_redeem),
                    'QTY_REDEEMED' => number_format($goods_qty_redeemed),
                    'QTY_TOTAL' => ($goods_qty != null || $goods_qty === 0 ? number_format($goods_qty) : ITEM_QTY_NOT_CONFIG),
                    'QTY_REMAIN' => $goods_qty_remain != null ? number_format($goods_qty_remain) : ITEM_QTY_NOT_CONFIG,
                ),
                $this->get_stat('', $conf, $this->goods_model->redeemLog($opts, $goods_id,
                    date('Y-m-d', strtotime('+1 day', strtotime($from))), $to),
                    $this->goods_model->redeemLog($opts, $goods_id,
                        date('Y-m-d', strtotime('+1 day', strtotime($from2))), $from))
            );
        }
        usort($arr, 'compare_TOTAL_NUM_desc');
        $params['ITEMS'] = array();
        foreach ($arr as $i => $each) {
            $each['BG_COLOR'] = ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : '');
            $params['ITEMS'][] = $each;
            if (NUMBER_OF_ITEMS > 0 && $i == (NUMBER_OF_ITEMS - 1)) {
                break;
            }
        }
        log_message('debug', 'ITEMS end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('ITEMS') . ' sec (ITEMS)');

        // PLAYERS (ranking)
        $this->utility->elapsed_time('RANKING');
        log_message('debug', 'RANKING start');
        $players = $this->player_model->getLeaderboardByLevelForReport(NUMBER_OF_PLAYERS, $site['client_id'],
            $site['_id']);
        $params['PLAYERS'] = array();
        if (is_array($players)) {
            foreach ($players as $i => $player) {
                $name = $player['first_name'] . ' ' . $player['last_name'];
                $name_str = trim($name);
                $params['PLAYERS'][] = array(
                    'BG_COLOR' => ($i % 2 == 0 ? 'bgcolor="#f5f5f5"' : ''),
                    'ROW' => $i + 1,
                    'IMAGE_SRC' => $conf['disable_url_exists'] || $this->utility->url_exists($player['image'],
                        $conf['dynamic_image_url']) ? $player['image'] : $conf['static_image_url'] . 'images/user_no_image.jpg',
                    'NAME' => (!empty($name_str) ? $name : $player['username']),
                    'EXP' => number_format($player['exp']),
                    'LEVEL' => number_format($player['level']),
                );
            }
        }
        log_message('debug', 'RANKING end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('RANKING') . ' sec (RANKING)');

        // FEEDS
        $this->utility->elapsed_time('FEEDS');
        log_message('debug', 'FEEDS start');
        $params['FEEDS'] = array();
        $rssparser = $this->rssparser;
        foreach (array(
                     'http://www.gamification.co/feed/',
                     'http://www.entrepreneur.com/feeds/tags/gamification/1908.rss'
                 ) as $url) {
            $feed = $rssparser->set_feed_url($url)->set_cache_life(FEED_CACHE_IN_MINUTES)->getFeed(NUMBER_OF_FEEDS_PER_SITE);
            if (is_array($feed)) {
                foreach ($feed as $item) {
                    $params['FEEDS'][] = array(
                        'FEED_TITLE' => $item['title'],
                        'FEED_DESCRIPTION' => substr($item['description'], 0, FEED_CHARACTER_LIMIT),
                        'FEED_AUTHOR' => $item['author'],
                        'FEED_DATE' => date(REPORT_DATE_FORMAT, strtotime($item['pubDate'])),
                        'FEED_DATE_NUM' => strtotime($item['pubDate']),
                        'FEED_LINK' => $item['link'],
                    );
                }
            }
        }
        usort($params['FEEDS'], 'compare_FEED_DATE_NUM_desc');
        log_message('debug', 'FEEDS end');
        log_message('debug', 'Elapsed time = ' . $this->utility->elapsed_time('FEEDS') . ' sec (FEEDS)');

        return $params;
    }

    private function get_stat($prefix, $conf, $curr, $prev)
    {
        $smm = get_sum_min_max($curr);
        $smm_prev = get_sum_min_max($prev);
        $best = $smm['max'];
        $params = $this->get_basic_stat($prefix, $conf, $smm['sum'], $smm_prev['sum']);
        $params[$prefix . 'BEST_DAY'] = ($best != null ? '(' . date(REPORT_DATE_FORMAT,
                strtotime($curr[$best]['_id'])) . ')' : '');
        $params[$prefix . 'BEST_VALUE'] = $best != null ? number_format($curr[$best]['value']) : 0;
        return $params;
    }

    private function get_basic_stat($prefix, $conf, $curr_val, $prev_val)
    {
        return array(
            $prefix . 'TOTAL' => number_format($curr_val),
            $prefix . 'TOTAL_PREV' => number_format($prev_val),
            $prefix . 'TOTAL_NUM' => $curr_val,
            $prefix . 'TOTAL_PREV_NUM' => $prev_val,
            $prefix . 'UPDOWN' => ($prev_val != 0 ? '<img src="' . $conf['static_image_url'] . '/images/icon-' . ($prev_val <= $curr_val ? 'up' : 'down') . ($prev_val * PERCENT_UPDOWN_HIGH <= $curr_val && $curr_val <= (1 + PERCENT_UPDOWN_HIGH) * $prev_val ? '1' : '2') . '.gif">' : ($curr_val != 0 ? '<span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span>' : '')),
            $prefix . 'PERCENT' => ($prev_val != 0 || $curr_val != 0 ? '<strong style="font-size:12px;color:' . ($prev_val <= $curr_val ? '#95cc00' : 'red') . '">' . number_format(($prev_val != 0 ? ($curr_val - $prev_val) / (1.0 * $prev_val) : 1) * 100,
                    2) . '%</strong>' : ''),
            $prefix . 'AVERAGE' => number_format($curr_val / 7.0, 2),
            $prefix . 'AVERAGE_PREV' => number_format($prev_val / 7.0, 2),
            $prefix . 'AVERAGE_NUM' => $curr_val / 7.0,
            $prefix . 'AVERAGE_PREV_NUM' => $prev_val / 7.0,
        );
    }

    private function master_init($conf, $to, $from, $from2)
    {
        $params = array(
            'STATIC_IMAGE_URL' => $conf['static_image_url'],
            'DYNAMIC_IMAGE_URL' => $conf['dynamic_image_url'],
            'FROM' => date(REPORT_DATE_FORMAT, strtotime('+1 day', strtotime($from))),
            'TO' => date(REPORT_DATE_FORMAT, strtotime($to)),
            'GLOBAL_TOTAL_USER_TOTAL_NUM' => $this->player_model->new_registration_all_customers(null, $to,
                array(SITE_ID_TRUE)),
            'GLOBAL_TOTAL_USER_TOTAL_PREV_NUM' => $this->player_model->new_registration_all_customers(null, $from,
                array(SITE_ID_TRUE)),
            'GLOBAL_NEW_USER_TOTAL_NUM' => $this->player_model->new_registration_all_customers(date('Y-m-d',
                strtotime('+1 day', strtotime($from))), $to, array(SITE_ID_TRUE)),
            'GLOBAL_NEW_USER_TOTAL_PREV_NUM' => $this->player_model->new_registration_all_customers(date('Y-m-d',
                strtotime('+1 day', strtotime($from2))), $from, array(SITE_ID_TRUE)),
            'GLOBAL_DAU_AVERAGE_NUM' => 0,
            'GLOBAL_DAU_AVERAGE_PREV_NUM' => 0,
            'GLOBAL_MAU_AVERAGE_NUM' => 0,
            'GLOBAL_MAU_AVERAGE_PREV_NUM' => 0,
            'CLIENTS' => array(),
        );
        $params['DIR'] = 'master';
        $params['FILE'] = "$to.html";
        $params['REPORT_URL'] = $conf['report_url'] . $params['DIR'] . '/' . $params['FILE'];
        return $params;
    }

    private function master_accumulate($conf, &$master, $params)
    {
        $master['GLOBAL_DAU_AVERAGE_NUM'] += $params['DAU_AVERAGE_NUM'];
        $master['GLOBAL_DAU_AVERAGE_PREV_NUM'] += $params['DAU_AVERAGE_PREV_NUM'];
        $master['GLOBAL_MAU_AVERAGE_NUM'] += $params['MAU_AVERAGE_NUM'];
        $master['GLOBAL_MAU_AVERAGE_PREV_NUM'] += $params['MAU_AVERAGE_PREV_NUM'];
        $actions = get_sum_curr_prev($params['ACTIONS']);
        $badges = get_sum_curr_prev($params['BADGES']);
        $items = get_sum_curr_prev($params['ITEMS']);
        $client = array_merge(
            $params,
            $this->get_basic_stat('ACTIONS_', $conf, $actions['curr'], $actions['prev']),
            $this->get_basic_stat('BADGES_', $conf, $badges['curr'], $badges['prev']),
            $this->get_basic_stat('ITEMS_', $conf, $items['curr'], $items['prev'])
        );
        $client['TOTAL_USER_UPDOWN'] = str_replace('.gif', '-black.gif', $client['TOTAL_USER_UPDOWN']);
        array_push($master['CLIENTS'], $client);
    }

    private function master_finalize($conf, &$master)
    {
        $master = array_merge(
            $master,
            $this->get_basic_stat('GLOBAL_TOTAL_USER_', $conf, $master['GLOBAL_TOTAL_USER_TOTAL_NUM'],
                $master['GLOBAL_TOTAL_USER_TOTAL_PREV_NUM']),
            $this->get_basic_stat('GLOBAL_NEW_USER_', $conf, $master['GLOBAL_NEW_USER_TOTAL_NUM'],
                $master['GLOBAL_NEW_USER_TOTAL_PREV_NUM']),
            $this->get_basic_stat('GLOBAL_DAU_', $conf, $master['GLOBAL_DAU_AVERAGE_NUM'],
                $master['GLOBAL_DAU_AVERAGE_PREV_NUM']),
            $this->get_basic_stat('GLOBAL_MAU_', $conf, $master['GLOBAL_MAU_AVERAGE_NUM'],
                $master['GLOBAL_MAU_AVERAGE_PREV_NUM'])
        );
    }
}

function get_sum_min_max($arr)
{
    $max = null;
    $min = null;
    $sum = 0;
    if (is_array($arr)) {
        foreach ($arr as $key => $each) {
            if ($each['value'] != 'SKIP') {
                if ($max == null || $each['value'] > $arr[$max]['value']) {
                    $max = $key;
                }
                if ($min == null || $each['value'] < $arr[$min]['value']) {
                    $min = $key;
                }
                $sum += $each['value'];
            }
        }
    }
    return array(
        'sum' => $sum,
        'max' => $max,
        'min' => $min,
    );
}

function get_sum_curr_prev($arr)
{
    $curr = 0;
    $prev = 0;
    if (is_array($arr)) {
        foreach ($arr as $each) {
            $curr += $each['TOTAL_NUM'];
            $prev += $each['TOTAL_PREV_NUM'];
        }
    }
    return array('curr' => $curr, 'prev' => $prev);
}

function compare_TOTAL_NUM_desc($a, $b)
{
    if ($a['TOTAL_NUM'] == $b['TOTAL_NUM']) {
        return ($a['NAME'] < $b['NAME'] ? -1 : 1);
    }
    return -1 * (($a['TOTAL_NUM'] < $b['TOTAL_NUM']) ? -1 : 1);
}

function compare_FEED_DATE_NUM_desc($a, $b)
{
    if ($a['FEED_DATE_NUM'] == $b['FEED_DATE_NUM']) {
        return 0;
    }
    return -1 * (($a['FEED_DATE_NUM'] < $b['FEED_DATE_NUM']) ? -1 : 1);
}

function compare_SITE_NAME_asc($a, $b)
{
    $a_lower = strtolower($a['SITE_NAME']);
    $b_lower = strtolower($b['SITE_NAME']);
    if ($a_lower == $b_lower) {
        return 0;
    }
    return $a_lower < $b_lower ? -1 : 1;
}

function convert_id_to_mongoId($obj)
{
    return new MongoID($obj['_id']);
}

function index_goods_id($obj)
{
    return $obj['goods_id'];
}

?>