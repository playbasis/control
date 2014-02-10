<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

/***** CONNECTION TO MONGODB *****/
$username = "mpuser" ;
$password = "mpuser01!" ;
$database_mongo = "core";
//$connection = new MongoClient("mongodb://$username:$password@localhost/".$database_mongo);
$connection = new MongoClient("mongodb://localhost/".$database_mongo);
$db = $connection->$database_mongo;

$collection = $db -> user_group;
$collection2 = $db -> user;
$collection3 = $db -> playbasis_feature;
$collection4 = $db -> playbasis_badge;
$collection5 = $db -> playbasis_reward;
$collection6 = $db -> playbasis_action;
$collection7 = $db -> playbasis_jigsaw;
$collection8 = $db -> playbasis_plan;
$collection9 = $db -> playbasis_client_site;
$collection10 = $db -> playbasis_client;
$collection11 = $db -> playbasis_reward_to_client;
$collection11a = $db -> playbasis_badge_to_client;
$collection11b = $db -> playbasis_feature_to_client;
$collection12 = $db -> playbasis_action_to_client;
$collection13 = $db -> playbasis_facebook_page_to_client;
$collection14 = $db -> playbasis_player;
$collection15 = $db -> playbasis_reward_to_player;
$collection16 = $db -> playbasis_badge_to_player;
$collection17 = $db -> playbasis_action_log;
$collection18 = $db -> playbasis_event_log;
$collection19 = $db -> playbasis_rule;
$collection20 = $db -> playbasis_exp_table;
$collection21 = $db -> playbasis_token;
$collection22 = $db -> playbasis_game_jigsaw_to_client;
$collection23 = $db -> playbasis_client_exp_table;

$collection17->ensureIndex(array("pb_plyer_id" => 1));
$collection17->ensureIndex(array("pb_plyer_id" => 1, "client_id" => 1, "site_id" => 1));
$collection17->ensureIndex(array("action_id" => 1, "pb_plyer_id" => 1, "client_id" => 1, "site_id" => 1));
$collection17->ensureIndex(array("action_id" => 1, "date_added" => 1, "client_id" => 1, "site_id" => 1));

$collection12->ensureIndex(array("client_id" => 1, "site_id" => 1));
$collection12->ensureIndex(array("client_id" => 1, "action_id" => 1), array("unique" => 1));

$collection19->ensureIndex(array("client_id" => 1, "site_id" => 1));

$collection15->ensureIndex(array("client_id" => 1, "site_id" => 1, "reward_id" => 1, "pb_player_id" => 1));
$collection15->ensureIndex(array("pb_player_id" => 1, "badge_id" => 1));
$collection15->ensureIndex(array("pb_player_id" => 1));
$collection15->ensureIndex(array("pb_player_id" => 1, "reward_id" => 1));

$collection11a->ensureIndex(array("site_id" => 1, "badge_id" => 1, "deleted" => 1));
$collection11a->ensureIndex(array("client_id" => 1, "site_id" => 1, "badge_id" => 1, "deleted" => 1));

$collection14->ensureIndex(array("client_id" => 1, "site_id" => 1));
$collection14->ensureIndex(array("level" => 1));
?>