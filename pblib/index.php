<!doctype html>
<html>
<head>
<title>Playbasis API</title>
</head>
<body>
<?php
require_once "playbasis.php";
$pb = new Playbasis();

$res = $pb->auth('abc','abcde');
echo 'auth:';
var_dump($res);

$user = ''.time();
$playerId = 'id'.$user;
$res = $pb->register($playerId, 'usr'.$user, 'usr'.$user.'@pbapp.net', 'http://farm9.staticflickr.com/8175/7931499456_c169c5d7aa_q.jpg');
echo'register:';
var_dump($res);

$playerId = '1';

$res = $pb->login($playerId);
echo 'login:';
var_dump($res);

$res = $pb->logout($playerId);
echo 'logout:';
var_dump($res);

$res = $pb->points($playerId);
echo 'points:';
var_dump($res);

$res = $pb->point($playerId, 'exp');
echo 'point:';
var_dump($res);

$res = $pb->actionLastPerformed($playerId);
echo 'actionLastPerformed:';
var_dump($res);

$res = $pb->actionLastPerformedTime($playerId, 'like');
echo 'actionLastPerformedTime:';
var_dump($res);

$res = $pb->actionPerformedCount($playerId, 'like');
echo 'actionPerformedCount:';
var_dump($res);

$res = $pb->badgeOwned($playerId);
echo 'badgeOwned:';
var_dump($res);

$res = $pb->rank('exp', 10);
echo 'rank:';
var_dump($res);

$res = $pb->player($playerId);
echo 'player:';
var_dump($res);

$res = $pb->badges();
echo 'badges:';
var_dump($res);

$res = $pb->badge(2);
echo 'badge:';
var_dump($res);

$res = $pb->badgeCollections();
echo 'badgeCollections:';
var_dump($res);

$res = $pb->badgeCollection(1);
echo 'badgeCollection:';
var_dump($res);

$res = $pb->actionConfig();
echo 'actionConfig:';
var_dump($res);

$res = $pb->rule($playerId, 'like');
echo 'rule:';
var_dump($res);
?>
</body>
</html>