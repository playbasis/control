<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

$checkpid = '/home/ubuntu/spid.txt';

if(file_exists($checkpid)){
    die("Already running!");
}
file_put_contents($checkpid, getmypid());

/***** CONNECTION TO MONGODB *****/
$username = "user" ;
$password = "pass" ;
$database_mongo = "core";
//$connection = new MongoClient("mongodb://$username:$password@localhost/myproject");
$connection = new MongoClient("mongodb://localhost/".$database_mongo);
$db = $connection->$database_mongo;


$collection_player = $db -> playbasis_player;
$players = $collection_player->find();
//$p = $collection_player->findOne(array('_id' => new MongoId("5215f8ae6d6cfb001e00023b") ,
//										'client_id' => new MongoId("5215f8ab6d6cfb001e0000c6") ,
//										'site_id' => new MongoId("5215f8ab6d6cfb001e0000d2")));

$sum = array();

foreach($players as $p){

	$collection_summary = $db -> playbasis_summary_of_player_beta;
	
	$item = array(
				'_id' => new MongoId($p['_id']),
				'client_id' => new MongoId($p['client_id']),
				'site_id' => new MongoId($p['site_id']),
				'cl_player_id' => $p['cl_player_id']
  				);	
	
	$player = $collection_summary->findOne($item);
	
	$collection_action_to_client = $db -> playbasis_action_to_client;
	$actions = $collection_action_to_client->find(array('client_id' => new MongoId($p['client_id']) ,
											'site_id' => new MongoId($p['site_id'])) );
	
	$collection_action_log = $db -> playbasis_action_log;
	
	$action = array();
	
	foreach($actions as $a){
		//$action = array();
		
		$action['action_'.$a['action_id']]['action_id'] = $a['action_id'];
		$action['action_'.$a['action_id']]['action_name'] = $a['name'];
		//$c = 0;
		$c = $collection_action_log->count(
								array(
									'action_id' => new MongoId($a['action_id']) , 
									'client_id' => new MongoId($p['client_id']) ,
									'site_id' => new MongoId($p['site_id']),
									'pb_player_id' => new MongoId($p['_id']),
									//'date_added' => array('$gte' => new MongoDate(strtotime(date("Y-m-d", strtotime("-1 days")))), '$lt' => new MongoDate(strtotime(date("Y-m-d"))))
									'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d")))) //first time
									//'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d", strtotime('2014-01-01'))))) //first time for test
									)
								);
		if(isset($player['value']['action_'.$a['action_id']]['action_value'])){
			$result_action = $player['value']['action_'.$a['action_id']]['action_value'] + $c;
		}else{
			$result_action =  $c;
		}
		$action['action_'.$a['action_id']]['action_value'] = $result_action;

	}
	
	$collection_reward = $db -> playbasis_reward;
	$rewards = $collection_reward->find();
	
	$collection_reward_to_player = $db -> playbasis_reward_to_player;
	
	$reward = array();
		
	foreach($rewards as $r){
		//$reward = array();
		
		$reward['reward_'.$r['_id']]['reward_id'] = $r['_id'];
		if($r['name'] == "badge"){
			$match = array(
						'$match' => array(
							'reward_id' => null , 
							'client_id' => new MongoId($p['client_id']) ,
							'site_id' => new MongoId($p['site_id']),
							'pb_player_id' => new MongoId($p['_id']),
							//'date_added' => array('$gte' => new MongoDate(strtotime(date("Y-m-d", strtotime("-1 days")))), '$lt' => new MongoDate(strtotime(date("Y-m-d"))))
							'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d")))) //first time
							//'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d", strtotime('2014-01-01'))))) //first time for test
						));
		}else{
			$match = array(
						'$match' => array(
							'reward_id' => new MongoId($r['_id']) , 
							'client_id' => new MongoId($p['client_id']) ,
							'site_id' => new MongoId($p['site_id']),
							'pb_player_id' => new MongoId($p['_id']),
							//'date_added' => array('$gte' => new MongoDate(strtotime(date("Y-m-d", strtotime("-1 days")))), '$lt' => new MongoDate(strtotime(date("Y-m-d"))))
							'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d")))) //first time
							//'date_added' => array('$lt' => new MongoDate(strtotime(date("Y-m-d", strtotime('2014-01-01'))))) //first time for test
						));
		}
		
		$c = $collection_reward_to_player->aggregate(
								array(
									$match
									,
									array(
									'$group' => array(
										'_id' => array('reward_id' => '$reward_id' , 
														'client_id' => '$client_id' ,
														'site_id' => '$site_id',
														'pb_player_id' => '$pb_player_id'),
										'value' => array('$sum' => '$value' )				
									))
								)
							); 			
		$value_reward = isset($c['result'][0]['value'])? $c['result'][0]['value'] : 0;		
		
		if(isset($player['value']['reward_'.$r['_id']]['reward_value'])){
			$result_reward = $player['value']['reward_'.$r['_id']]['reward_value'] + $value_reward;
		}else{
			$result_reward =  $value_reward;
		}
		
		$reward['reward_'.$r['_id']]['reward_value'] = $result_reward;

	}
	
	$p['value'] = $action + $reward;

	$item = array(
				'_id' => new MongoId($p['_id']),
				'client_id' => new MongoId($p['client_id']),
				'site_id' => new MongoId($p['site_id']),
				'cl_player_id' => $p['cl_player_id']
  				);	
	$set = array(
				'_id' => new MongoId($p['_id']),
				'client_id' => new MongoId($p['client_id']),
				'site_id' => new MongoId($p['site_id']),
				'cl_player_id' => $p['cl_player_id'],
				'twitter_id' => $p['twitter_id'],
				'facebook_id' => $p['facebook_id'],
				'instagram_id' => $p['instagram_id'],
				'username' => $p['username'],
				'password' => $p['password'],
				'first_name' => $p['first_name'],
				'last_name' => $p['last_name'],
				'image' => $p['image'],
				'exp' => new MongoInt32($p['exp']),
				'level' => new MongoInt32($p['level']),
				'email' => $p['email'],
				'birth_date' => isset($p['birth_date']->sec)?new MongoDate(strtotime(date("Y-m-d", $p['birth_date']->sec))):new MongoDate(strtotime(date("Y-m-d", strtotime($p['birth_date'])))),
				'gender' => new MongoInt32($p['gender']),
				'nickname' => $p['nickname'],
				'status' => (bool)($p['status']),
				'date_added' => isset($p['date_added']->sec)?new MongoDate(strtotime(date("Y-m-d", $p['date_added']->sec))):new MongoDate(strtotime(date("Y-m-d", strtotime($p['date_added'])))),
				'date_modified' => isset($p['date_modified']->sec)?new MongoDate(strtotime(date("Y-m-d", $p['date_modified']->sec))):new MongoDate(strtotime(date("Y-m-d", strtotime($p['date_modified'])))),
				'value' => $p['value']
				);
	$option = array("upsert" => true);
	$collection_summary->update($item,$set,$option);
				
	
}

unlink ($checkpid);

?>