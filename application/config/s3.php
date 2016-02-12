<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Use SSL
|--------------------------------------------------------------------------
|
| Run this over HTTP or HTTPS. HTTPS (SSL) is more secure but can cause problems
| on incorrectly configured servers.
|
*/

$config['use_ssl'] = TRUE;

/*
|--------------------------------------------------------------------------
| Verify Peer
|--------------------------------------------------------------------------
|
| Enable verification of the HTTPS (SSL) certificate against the local CA
| certificate store.
|
*/

//$config['verify_peer'] = TRUE;
$config['verify_peer'] = FALSE;

/*
|--------------------------------------------------------------------------
| Access Key
|--------------------------------------------------------------------------
|
| Your Amazon S3 access key.
|
*/

$config['access_key'] = 'AKIAIXA7TRPI53FODBGA';

/*
|--------------------------------------------------------------------------
| Secret Key
|--------------------------------------------------------------------------
|
| Your Amazon S3 Secret Key.
|
*/

$config['secret_key'] = 'OkPieozFKd62MA5eJgbD//HELeFmExg7ADdN/lse';

/*
|--------------------------------------------------------------------------
| Default Endpoint
|--------------------------------------------------------------------------
|
| Your Amazon S3 Default Endpoint.
|
*/

$config['endpoint'] = 's3-ap-southeast-1.amazonaws.com';

/*
|--------------------------------------------------------------------------
| Use Enviroment?
|--------------------------------------------------------------------------
|
| Get Settings from enviroment instead of this file? 
| Used as best-practice on Heroku
|
*/

$config['get_from_enviroment'] = FALSE;

/*
|--------------------------------------------------------------------------
| Access Key Name
|--------------------------------------------------------------------------
|
| Name for access key in enviroment
|
*/
$config['access_key_envname'] = 'S3_KEY';

/*
|--------------------------------------------------------------------------
| Access Key Name
|--------------------------------------------------------------------------
|
| Name for access key in enviroment
|
*/
$config['secret_key_envname'] = 'S3_SECRET';

/*
|--------------------------------------------------------------------------
| If get from enviroment, do so and overwrite fixed vars above
|--------------------------------------------------------------------------
|
*/

if ($config['get_from_enviroment']){
	$config['access_key'] = getenv($config['access_key_envname']);
	$config['secret_key'] = getenv($config['secret_key_envname']);

}
