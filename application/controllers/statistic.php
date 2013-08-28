<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Statistic extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if(!$this->User_model->isLogged()){
            redirect('/login', 'refresh');
        }

        $this->load->model('Statistic_model');

        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
        $this->lang->load("form_validation", $lang['folder']);
    }

    public function getStatisticData(){

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

        $this->output->set_output(json_encode($json));
    }

    public function getDailyActionmeaturement(){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getDailyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getWeeklyActionmeaturement(){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getWeeklyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function getMonthlyActionmeaturement(){

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        $data = array(
            'client_id' => $client_id,
            'site_id' => $site_id,
            'date' => date('Y-m-d'),
            'start' => 0,
            'limit' => 5
        );

        $this->data['events'] = $this->Statistic_model->getMonthlyActionmeaturement($data);

        $this->load->vars($this->data);
        $this->load->view('carousel');
    }

    public function isotope(){
//        $this->benchmark->mark('isotope_start');

        include('action_data_log.php');

        $this->load->model('Player_model');
        $this->load->model('Badge_model');
        $this->load->model('Image_model');

        if ($this->input->get('filter_page')) {
            $page = $this->input->get('filter_page');
        } else {
            $page = 1;
        }

        if ($this->input->get('filter_name')) {
            $filter_name = $this->input->get('filter_name');
        } else {
            $filter_name = null;
        }

        $data = $this->filterData();

        $result = $this->Player_model->getIsotopePlayer($data);
//        $total_players = $this->Player_model->getIsotopeTotalPlayer($data);

        $total_players = $result['total'];
        $results = $result['result'];

        if(isset($data['limit'])){
            $limit = $data['limit'];
        }else{
            $limit = 100;
        }

        $this->current_page = $page;
        $this->limit = $limit;

        $players = array();

        if ($results) {
            foreach ($results as $result) {
                $actions = array();
                $player_action = $this->Player_model->getActionsByPlayerId($result['pb_player_id']);
                $event_log = $this->Player_model->getEventLog($result['pb_player_id'], 'logout');

                if ($player_action) {
                    foreach ($player_action as $action) {
                        $actions[] = array(
                            'action_id' => $action['action_id'],
                            'name' => $action['name'],
                            'value' => $action['total'],
                            'icon' => $action['icon']
                        );
                    }
                }

                $data_player = array('pb_player_id' => $result['pb_player_id']);
                $player_badge = $this->Player_model->getBadgeByPlayerId($data_player);

                $badges = array();
                if ($player_badge) {
                    foreach ($player_badge as $badge) {

                        $badge_info = $this->Badge_model->getBadge($badge['badge_id']);

                        if ($badge_info && (S3_IMAGE . $badge_info[0]['image'] != 'HTTP/1.1 404 Not Found' && S3_IMAGE . $badge_info[0]['image'] != 'HTTP/1.0 403 Forbidden')) {
                            $thumb = $this->Image_model->resize($badge_info[0]['image'], 40, 40);
                        } else {
                            $thumb = $this->Image_model->resize('no_image.jpg', 40, 40);
                        }

                        $badges[] = array(
                            'badge_id' => $badge['badge_id'],
                            'name' => $badge_info[0]['name'],
                            'image' => $thumb,
                            'quantity' => $badge['amount'],
                            'date_added' => $badge['date_added'],
                            'date_modified' => $badge['date_modified'],
                        );
                    }
                }

                $players[] = array(
                    'pb_player_id' => $result['pb_player_id'],
                    'firstname' => $result['first_name'],
                    'lastname' => $result['last_name'],
                    'nickname' => $result['nickname'],
                    'image' => $result['image'],
                    'points' => $result['value'],
                    'level' => $result['level'],
                    'exp' => $result['exp'],
                    'status' => $result['status'],
                    'email' => $result['email'],
                    'gender' => $result['gender'],
                    'last_active' => (isset($event_log[0]))? $event_log[0]['date_modified'] : '0 / 0 / 0',
                    'action' => $actions,
                    'badge' => $badges
                );

            }
        }

        $this->data['players'] = $players;

        $total_pages = ceil($total_players / $data['limit']);

        $this->data['total_pages'] = $total_pages;
        $this->data['current_page'] = $this->current_page;
        $this->data['limit'] = $this->limit;
        $this->data['total_players'] = $total_players;

        $this->load->library('parser');
        $html = $this->parser->parse('player_isotope', $this->data, TRUE);

        $data_html = array('html' => $html , 'current_page' => $this->current_page, 'total_page' => $total_pages, 'limit' => $this->limit, 'total_players' => $total_players);

//        $this->benchmark->mark('isotope_end');
//
//        echo "isotope : ".$this->benchmark->elapsed_time('isotope_start', 'isotope_end');

        $this->output->set_output(json_encode($data_html));
    }

    private function filterData() {
        include('action_data_log.php');

        if($this->input->get('limit')){
            $limit = $this->input->get('limit');
        }else{
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
                            'name' => (!empty($sort_explode[0]))? $sort_explode[0] : '',
                            'value' => (!empty($sort_explode[1]))? $sort_explode[1] : ''
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