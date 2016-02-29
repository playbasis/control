<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistic extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_model');
        $this->load->model('Plan_model');
        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }

        $this->load->model('Statistic_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function getStatisticData()
    {

        if ($this->input->get('date_start')) {
            $date_start = strtotime($this->input->get('date_start'));
        } else {
            $date_start = strtotime(' -30 day');
        }

        if ($this->input->get('date_expire')) {
            $date_expire = strtotime($this->input->get('date_expire'));
        } else {
            $date_expire = strtotime('today');
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'month' => date('n'),
            'year' => date('Y'),
            'date_start' => $date_start,
            'date_expire' => $date_expire
        );

        $json = array();

        $this->Statistic_model->set_read_preference_secondary();

        $register = $this->Statistic_model->getNewRegister($data);
        foreach ($register as $key => $value) {
            $json['register'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $rewards = $this->Statistic_model->getPlayerRewardPointStat($data);
        foreach ($rewards as $key => $value) {
            $json['points'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $badges = $this->Statistic_model->getPlayerRewardBadgeStat($data);
        foreach ($badges as $key => $value) {
            $json['badges'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $levelup = $this->Statistic_model->getPlayerRewardLevelStat($data);
        foreach ($levelup as $key => $value) {
            $json['levelup'][] = array(
                $value['date'],
                $value['count'],
                $value['fulldate']
            );
        }

        $this->Statistic_model->set_read_preference_primary();

        $this->output->set_output(json_encode($json));
    }

    public function getDailyActionmeaturement()
    {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->Statistic_model->set_read_preference_secondary();
        $this->data['events'] = $this->Statistic_model->getDailyActionmeaturement($data);
        $this->Statistic_model->set_read_preference_primary();

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getWeeklyActionmeaturement()
    {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->Statistic_model->set_read_preference_secondary();
        $this->data['events'] = $this->Statistic_model->getWeeklyActionmeaturement($data);
        $this->Statistic_model->set_read_preference_primary();

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getMonthlyActionmeaturement()
    {

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->Statistic_model->set_read_preference_secondary();
        $this->data['events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);
        $this->Statistic_model->set_read_preference_primary();

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function isotope()
    {

        include('action_data_log.php');

        $this->load->model('Player_model');
        $this->load->model('Badge_model');
        $this->load->model('Image_model');
        $this->load->model('Reward_model');

        if ($this->input->get('filter_page')) {
            $page = $this->input->get('filter_page');
        } else {
            $page = 1;
        }

        $data = $this->filterData();

        if ($this->input->get('filter_name')) {
            $data['filter_name'] = $this->input->get('filter_name');
        } else {
            $data['filter_name'] = null;
        }

        $reward = $this->Reward_model->getRewardByName('point');
        $reward_id = ($reward ? $reward[0]['_id'] : null);

        $this->Player_model->set_read_preference_secondary();
        $iso_data = $this->Player_model->getIsotopePlayer($data);
        $this->Player_model->set_read_preference_primary();

        $total_players = $iso_data['total'];
        $results = $iso_data['result'];

        if (isset($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = 100;
        }

        $this->current_page = $page;
        $this->limit = $limit;

        $players = array();

        if ($results) {
            $action_data = array();
            foreach ($results as $result) {

                $player_action = $this->Player_model->getActionsByPlayerId($result['_id'], $action_data);

                $data_player = array('pb_player_id' => $result['_id']);
                $player_badge = $this->Player_model->getBadgeByPlayerId($data_player);

                $badges = array();
                if ($player_badge) {
                    foreach ($player_badge as $badge) {

                        $badge_info = $this->Badge_model->getBadge($badge['badge_id']);

                        /*if ($badge_info){
                            $info = pathinfo($badge_info['image']);
                            $extension = $info['extension'];
                            $new_image = 'cache/' . utf8_substr($badge_info['image'], 0, utf8_strrpos($badge_info['image'], '.')).'-40x40.'.$extension;

                            $headers = get_headers(S3_IMAGE.$new_image, 1);
                            if($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.0 403 Forbidden'){
                                $thumb = $new_image;
                            }else{
                                $headers = get_headers(S3_IMAGE.$badge_info['image'], 1);
                                if($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.0 403 Forbidden') {
                                    $thumb = $this->Image_model->resize($badge_info['image'], 40, 40);
                                }else{
                                    $thumb = S3_IMAGE."cache/no_image-40x40.jpg";
                                }
                            }
                        } else {
                            $thumb = S3_IMAGE."cache/no_image-40x40.jpg";
                        }*/

                        if ($badge_info && isset($badge_info['image'])) {
                            $info = pathinfo($badge_info['image']);
                            if (isset($info['extension'])) {
                                $extension = $info['extension'];
                                $new_image = 'cache/' . utf8_substr($badge_info['image'], 0,
                                        utf8_strrpos($badge_info['image'], '.')) . '-40x40.' . $extension;
                                $thumb = S3_IMAGE . $new_image;
                            } else {
                                $thumb = S3_IMAGE . "cache/no_image-40x40.jpg";
                            }
                        } else {
                            $thumb = S3_IMAGE . "cache/no_image-40x40.jpg";
                        }

                        $badges[] = array(
                            'badge_id' => $badge['badge_id'],
                            'name' => $badge_info['name'],
                            'image' => $thumb,
                            'quantity' => $badge['value'],
                            'date_added' => $badge['date_added'],
                            'date_modified' => $badge['date_modified'],
                        );
                    }
                }

                $point = $this->Player_model->getPlayerPoint($data_player, $reward_id);

                $players[] = array(
                    'pb_player_id' => $result['_id'] . "",
                    'firstname' => $result['first_name'],
                    'lastname' => $result['last_name'],
                    'nickname' => $result['nickname'],
                    'image' => $result['image'],
                    'points' => $point,
                    'level' => $result['level'],
                    'exp' => $result['exp'],
                    'status' => $result['status'],
                    'email' => $result['email'],
                    'gender' => $result['gender'],
                    'date_added' => date($this->lang->line('date_format_short'),
                        strtotime($this->datetimeMongotoReadable($result['date_added']))),
                    'last_active' => date($this->lang->line('date_format_short'),
                        strtotime($this->datetimeMongotoReadable($result['date_modified']))),
                    'action' => $player_action,
                    'badges' => $badges
                );
            }
        }

        $this->data['players'] = $players;

        $total_pages = ceil($total_players / $data['limit']);

        $this->data['total_pages'] = $total_pages;
        $this->data['current_page'] = $this->current_page;
        $this->data['limit'] = $this->limit;
        $this->data['total_players'] = $total_players;

        $plan_subscription = $this->Client_model->getPlanByClientId($this->User_model->getClientId());
        $plan = $this->Plan_model->getPlanById($plan_subscription['plan_id']);
        $this->data['reset_quest'] = array_key_exists('reset_quest', $plan) && $plan['reset_quest'];

        $this->load->library('parser');
        $html = $this->parser->parse('player_isotope', $this->data, true);

        $data_html = array(
            'html' => $html,
            'current_page' => $this->current_page,
            'total_page' => $total_pages,
            'limit' => $this->limit,
            'total_players' => $total_players
        );

        $this->output->set_output(json_encode($data_html));
    }

    private function filterData()
    {
        include('action_data_log.php');

        if ($this->input->get('limit')) {
            $limit = $this->input->get('limit');
        } else {
            $limit = 100;
        }

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if ($this->input->get('filter_sort')) {
            $sort = explode('|', $this->input->get('filter_sort'));

            if (is_array($sort)) {
                foreach ($sort as $value) {
                    $sort_explode = explode(':', $value);

                    if (is_array($sort_explode)) {
                        $sort_data[] = array(
                            'name' => (!empty($sort_explode[0])) ? $sort_explode[0] : '',
                            'value' => (!empty($sort_explode[1])) ? $sort_explode[1] : ''
                        );
                    }
                }
            }

        } else {
            $sort_data = array();
        }

        if ($this->input->get('filter_page')) {
            $page = $this->input->get('filter_page');
        } else {
            $page = 1;
        }

        if ($this->input->get('filter_order')) {
            $order = $this->input->get('filter_order');
        } else {
            $order = 'ASC';
        }

        if ($this->input->get('sort')) {
            $sort = $this->input->get('sort');
        } else {
            $sort = '';
        }

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'filter_sort' => $sort_data,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
            'sort' => $sort
        );

        return $data;

    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }
}