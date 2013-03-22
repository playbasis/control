<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//define('ANY_STRING','([a-zA-Z0-9-_]+)');
//define('ANY_NUMBER','([0-9]+)');

$route['default_controller'] = "welcome/playbasis";

//auth API
$route['Auth'] = 'auth';

//player API
$route['Player/([a-zA-Z0-9-_]+)'] = 'player/index/$1';
$route['Player'] = 'player/index/';

$route['Player/([a-zA-Z0-9-_]+)/register'] = 'player/register/$1';
$route['Player/register'] = 'player/register';

$route['Player/([a-zA-Z0-9-_]+)/login'] = 'player/login/$1';
$route['Player/login'] = 'player/login';
$route['Player/([a-zA-Z0-9-_]+)/logout'] = 'player/logout/$1';
$route['Player/logout'] = 'player/logout';

$route['Player/rank/([a-zA-Z0-9-_]+)/([0-9]+)'] = 'player/rank/$1/$2';
$route['Player/rank/([a-zA-Z0-9-_]+)'] = 'player/rank/$1/20';
$route['Player/rank'] = 'player/rank/0/0';

$route['Player/([a-zA-Z0-9-_]+)/points'] = 'player/points/$1';
$route['Player/points'] = 'player/points';
$route['Player/([a-zA-Z0-9-_]+)/point/([a-zA-Z0-9-_]+)'] = 'player/point/$1/$2';
$route['Player/point/([a-zA-Z0-9-_]+)'] = 'player/point/0/$1';
$route['Player/([a-zA-Z0-9-_]+)/point'] = 'player/point/$1/0';
$route['Player/point'] = 'player/point/';

$route['Player/([a-zA-Z0-9-_]+)/action/([a-zA-Z0-9-_]+)/(time|count)'] = 'player/action/$1/$2/$3';
$route['Player/action/([a-zA-Z0-9-_]+)/(time|count)'] = 'player/action/0/$1/$2';
$route['Player/([a-zA-Z0-9-_]+)/action/(time|count)'] = 'player/action/$1/0/$2';
$route['Player/action'] = 'player/action/';

$route['Player/([a-zA-Z0-9-_]+)/badge'] = 'player/badge/$1';
$route['Player/badge'] = 'player/badge/0';

//badge API
$route['Badge/collection/([0-9]+)'] = 'badge/getcollection/$1';
$route['Badge/collection'] = 'badge/getcollection';
$route['Badge/([0-9]+)'] = 'badge/index/$1';
$route['Badge'] = 'badge/index';

//engine API
$route['Engine/actionConfig']	= 'engine/getActionConfig';
$route['Engine/rule/([a-zA-Z0-9-_]+)'] = 'engine/rule/$1';
$route['Engine/rule']	= 'engine/rule/0';

//misc
$route['test']	= 'playbasis/test';
$route['fb'] = 'playbasis/fb';
$route['login'] = 'playbasis/login';

//dummy 
$route['dummy/dummyPlayer/([0-9]+)/([0-9]+)']	= 'dummy/dummyPlayer/$1/$2/$3';
$route['dummy/([0-9]+)/([0-9]+)/([0-9]+)']	= 'dummy/index/$1/$2/$3';
$route['dummy/:any']	= 'dummy/error';

$route['404_override'] = '';
/* End of file routes.php */
/* Location: ./application/config/routes.php */