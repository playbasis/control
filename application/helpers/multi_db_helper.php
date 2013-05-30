<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function multi_db_load($mdl)
{
	$dblist = array(
		0 => 'developer',
		1 => 'default'
	);
	$multiDBs = array();
	foreach($dblist as $key => $value)
	{
		$multiDBs[$key] = $mdl->load->database($value, TRUE);
	}
	return $multiDBs;
}
?>