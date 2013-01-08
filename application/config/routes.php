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

$route['default_controller'] = "welcome";

//Game Editor
$route['Geditor/rules'] = 'Geditor/rules';
$route['Geditor/rules/([0-9]+)'] = 'Geditor/rules/$1';							#get all rule for each site
$route['Geditor/rules/([0-9]+)/([0-9]+)'] = 'Geditor/rules/$1/$2';   			#get specific rule for each site 
$route['Geditor/rules/([0-9]+)/jigsaws'] = 'Geditor/jigsaws/$1';				#get ganme jigsaws relate to each rule
$route['Geditor/rules/([0-9]+)/update/status'] = 'Geditor/ruleStatus/$1';		#update rules status
$route['Geditor/rules/add'] = 'Geditor/addRule';								#add new rule

//API
#client
$route['Auth'] = 'auth';						#request token

#player
$route['Player/([0-9]+)'] = 'player/index/$1';						#get player information
$route['Player/([a-zA-Z0-9]+)/register'] = 'player/register/$1';	#register player to playbasis system
$route['Player/([a-zA-Z0-9]+)/login'] = 'player/login/$1';			#login player to playbasis system

$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */