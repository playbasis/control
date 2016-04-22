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

define('ANY_STRING','([a-zA-Z0-9-%_:\.]+)');
define('ANY_NUMBER','([0-9]+)');

$route['default_controller'] = "dashboard/home";
$route['404_override'] = '';

$route['login'] = 'user/login';
$route['register'] = 'user/register';
$route['logout'] = 'user/logout';
$route['pending_users'] = 'user/list_pending_users';
$route['enable_user'] = 'user/enable_user';
$route['forgot_password'] = '/user/forgot_password';
$route['reset_password'] = '/user/reset_password';
$route['block'] = "user/block";
$route['report/rewards_badges'] = 'report_reward';
$route['report/goods'] = 'report_goods';
$route['report/registration'] = 'report_registration';
$route['report/quest'] = 'report_quest';
$route['report/quiz'] = 'report_quiz';
$route['first_app'] = 'account/first_app';
$route['domain'] = 'app';
$route['referral/'.ANY_STRING] = 'user/referral/$1';
$route['referral'] = 'user/referral';
$route['merchant_verify'] = 'user/merchant';
$route['merchant_logout'] = 'user/merchant_logout';
$route['cms_login'] = 'user/cms_login';
$route['player/password/reset/completed'] = 'user/player_reset_password_complete';
$route['player/password/reset/'.ANY_STRING] = 'user/player_reset_password/$1';
$route['player/password/reset'] = 'user/player_reset_password';
$route['player/email/verify/'.ANY_STRING] = 'user/player_verify_email/$1';
$route['player/email/verify/completed'] = 'user/player_verify_email_complete';

$route['404_override'] = 'error/error_404';

/* End of file routes.php */
/* Location: ./application/config/routes.php */