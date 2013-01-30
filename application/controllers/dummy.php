<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dummy extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('dummy_model');
	}

	public function dummyPlayer(){
		
		# need name and picture for process dummy_player_data.txt @ root level of directory 

		//shuffle($name);
		echo "<p>PROGRESS : </p>";
		for($i=1;$i<=6098;$i++){
			$player = array_shift($name);
			//$player = 'Lonny Edmundo Mooney|M';
			$playerInfo = explode('|',$player);
			$playerName = explode(' ',$playerInfo[0]);
			//var_dump($playerInfo);
			//var_dump($playerName);
			//die();
			$date = $this->randomDateApply();
			$data = array(
				'client_id'		=> 1,
				'site_id'		=> 1,
				'cl_player_id'	=> $i,
				'username'		=> strtolower($playerName[0]),
				'password'		=> md5($playerName[0]),
				'first_name'	=> $playerName[0],
				'last_name'		=> isset($playerName[2])? $playerName[1] . " " .$playerName[2] : $playerName[1],
				'image'			=> array_shift($picture),
				'email'			=> $playerName[0] . "." . $playerName[1]  . "@demo.com",
				'birth_date'	=> $this->randomBirthDate(),
				'gender'		=> $this->gender($playerInfo[1]),
				'nickname'		=> strtolower($playerName[0]),
				'date_added'	=> $date,
				'date_modified'	=> $date,
			);

			//die();
			$this->dummy_model->dummyAddPlayer($data);
		}
		echo "<span>*</span><br/><h3>DONE</h3>";
	}

	private function randomDateApply(){
		$timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'),strtotime('2013-01-25 12:59:59'));
		return date('Y-m-d H:i:s',$timeStamp);
	}

	private function randomBirthDate(){
		$timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'),strtotime('2013-01-25 12:59:59'));
		return date('Y-m-d',$timeStamp);
	}

	private function gender($s){
		if($s=='U')
			return 0;
		if($s=='M')
			return 1;

		return 2;
	}

	
	public function index($count=1){

		$actionList = array();
		$playerList = array();
		//curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);

		for($i=0;$i<$count;$i++){
			//init
			if(!$actionList)
				$actionList = $this->getAction();				
			if(!$playerList)
				$playerList = $this->getPlayerList();

			$player = array_shift($playerList);
			//change system time  ## DANGER HABBIT
			$this->makeTime();
			

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/index.php/Engine/rule?player_id=".$player['cl_player_id']."&action=".$actionList[mt_rand(0,count($actionList)-1)]['name']);
			

			// grab URL and pass it to the browser
			curl_exec($ch);
			
		}
		curl_close($ch);
	}

	private function getAction(){
		return $this->dummy_model->getActionToClient(1,1); #(client_id,site_id)

		//return $actionList[mt_rand(0,count($actionList)-1)]['name'];
	}

	private function getPlayerList(){	
		return $this->dummy_model->getRandomPlayer(1,1); #(client_id,site_id)

	}

	private function makeTime(){
		// $timeStamp = strtotime('2013-01-01');
		// $timeStamp = mt_rand(strtotime('2013-01-01'),time());
		$timeStamp = mt_rand(strtotime('2013-01-24 00:00:00'),strtotime('2013-01-25 12:59:59'));
		$date = date('m-d-Y',$timeStamp);
		$time = date('H:i:s',$timeStamp);
		shell_exec("date $date");
		shell_exec("time $time");	
	} 		
}
?>


