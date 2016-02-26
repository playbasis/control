<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'dummy_player_data.php';

class Dummy extends CI_Controller
{/*
        public function __construct()
        {
            parent::__construct();
            $this->load->model('dummy_model');
            $this->load->model('auth_model');
        }
        public function dummyPlayer($clientId, $siteId)
        {
            $name = $GLOBALS['name'];
            $picture = $GLOBALS['picture'];
            shuffle($name);
            echo "<p>PROGRESS : </p>";
            for($i = 1; $i <= 100; $i++)
            {
                $player = array_shift($name);
                $playerInfo = explode('|', $player);
                $playerName = explode(' ', $playerInfo[0]);
                $date = $this->randomDateApply();
                $data = array(
                    'client_id' => $clientId,
                    'site_id' => $siteId,
                    'cl_player_id' => $i,
                    'username' => strtolower($playerName[0]),
                    'password' => md5($playerName[0]),
                    'first_name' => $playerName[0],
                    'last_name' => isset($playerName[2]) ? $playerName[1] . " " . $playerName[2] : $playerName[1],
                    'image' => array_shift($picture),
                    'email' => $playerName[0] . "." . $playerName[1] . "@demo.com",
                    'birth_date' => $this->randomBirthDate(),
                    'gender' => $this->gender($playerInfo[1]),
                    'nickname' => strtolower($playerName[0]),
                    'date_added' => $date,
                    'date_modified' => $date
                );
                $this->dummy_model->dummyAddPlayer($data);
            }
            echo "<h3>DONE</h3>";
        }
        private function randomDateApply()
        {
            $timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'), strtotime('2013-01-25 12:59:59'));
            return date('Y-m-d H:i:s', $timeStamp);
        }
        private function randomBirthDate()
        {
            $timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'), strtotime('2013-01-25 12:59:59'));
            return date('Y-m-d', $timeStamp);
        }
        private function gender($s)
        {
            if($s == 'U')
                return 0;
            if($s == 'M')
                return 1;
            return 2;
        }
        public function index($record = 1, $clientId, $siteId)
        {
            if($record > 2000)
            {
                die('input Error,Record too big please try 1 - 2000 record.');
            }
            if($record < 1)
            {
                die('input Error,Record can\'t be 0  big please try 1 - 2000 record.');
            }
            $configArray = array(
                'client_id' => $clientId,
                'site_id' => $siteId,
                'limit' => $record
            );
            $actionList = array();
            $playerList = array();
            $token = $this->dummy_model->getToken($configArray);
            //check token, also renew token if expire
            if(!$token)
            {
                $API = $this->dummy_model->getKeySecret($configArray);
                $this->auth_model->generateToken(array_merge($API, $configArray));
                $token = $this->dummy_model->getToken($configArray);
            }
            //start curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            for($i = 0; $i < $record; $i++)
            {
                if(!$actionList)
                    $actionList = $this->getAction($configArray);
                if(!$playerList)
                    $playerList = $this->getPlayerList($configArray);
                $player = array_shift($playerList);
                //change system time  ## DANGER ##
                $this->makeTime();
                $url = base_url() . "/index.php/Engine/rule";
                $postData = array(
                    'player_id' => $player['cl_player_id'],
                    'action' => $actionList[mt_rand(0, count($actionList) - 1)]['name'],
                    'url' => urlencode('http://dummysite.pb'),
                    'token' => $token
                );
                // set URL and other options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type : application/x-www-form-urlencoded; charset=utf-8'
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_exec($ch);
            }
            curl_close($ch);
        }
        // route error exception
        public function error()
        {
            die('Route error, please check URL format.');
        }
        private function getAction($configArray)
        {
            return $this->dummy_model->getActionToClient($configArray);
        }
        private function getPlayerList($configArray)
        {
            return $this->dummy_model->getRandomPlayer($configArray);
        }
        private function makeTime()
        {
            $timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'), strtotime('2013-01-25 12:59:59'));
            $date = date('m-d-Y', $timeStamp);
            $time = date('H:i:s', $timeStamp);
            shell_exec("date $date");
            shell_exec("time $time");
        }
        public function pathInfo()
        {
            var_dump('BASE_PATH : ' . BASEPATH);
            var_dump('APP_PATH : ' . APPPATH);
            var_dump('FC_PATH : ' . FCPATH);
            var_dump('BASE_URL : ' . base_url());
        }
*/
}

?>
