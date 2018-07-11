<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

//////////////////////////
//REMOVE BEFORE DEPLOY! //
//////////////////////////
$route['setup'] = 'Setup/index';

//=================================

/*
	Home, User and Visitor Routes
 */
$route['welcome'] = 'Welcome/index';
$route['home'] = 'User/index';
$route['Home'] = 'User/index';
$route['login'] = 'User/loginUI';
$route['logout'] = 'User/logout';
$route['User/my_info'] = 'User/my_info';

/*
	Help Routes
 */
$route['help/markups'] = 'Help/markups';

/*
	Password Recovery Routes
 */
$route['recover'] = 'User/recover_password';
$route['recover-form/(:any)/(:any)'] = 'User/recover_form/$1/$2';

/*
	Admin Routes
 */
$route['admin'] = 'Admin/index';
$route['admin/view-logs'] = 'Admin/view_logs/0';
$route['admin/view-logs/(:any)'] = 'Admin/view_logs/$1';

/*
	Logging Routes
 */
$route['logging'] = 'Logging/log';
$route['logging/(:any)'] = 'Logging/log';

/*
	Create Routes
 */
$route['create'] = 'Create/index/action';
$route['create/index'] = 'Create/index/action';
$route['create/index/(:any)'] = 'Create/index/$1';

/*
	Stats Routes
 */
$route['stats/my_stats'] = 'Stats/my_stats';
$route['stats/project_stats'] = 'Stats/project_stats';
$route['stats/project_stats/(:any)'] = 'Stats/project_stats/$1';
$route['stats/team_stats'] = 'Stats/team_stats';
$route['stats/team_stats/(:any)'] = 'Stats/team_stats/$1';
$route['stats/custom/create/(:any)'] = 'Stats/create_custom/$1';
$route['stats/custom/(:any)'] = 'Stats/custom_stats/$1';

/*
	Search Routes
 */
$route['search/result/(:any)'] = 'Search/search/$1';
$route['search/result'] = 'Search/search';
$route['search'] = 'Search/index';

/*
	Manage Routes
 */
$route['manage_teams'] = 'Manage/manage_teams';
$route['manage_teams/(:any)'] = 'Manage/manage_teams/$1';
$route['manage_teams/add_users/(:any)'] = 'Manage/add_users/$1';
$route['manage_teams/remove_users/(:any)'] = 'Manage/remove_users/$1';

$route['modify/view_tables'] = 'Modify/index';
$route['modify/table/(:any)'] = 'Modify/modify_selection/$1'; //For table selection
$route['modify/table/(:any)/(:any)'] = 'Modify/modify_selection/$1/$2'; //For pagination
$route['modify/(:any)/(:any)'] = 'Modify/modify_form/$1/$2'; //For the modify form


/*
	Ajax Routes
 */
$route['Ajax/(:any)/(:any)'] = 'Ajax/$1/$2';
$route['Ajax/(:any)'] = 'Ajax/$1';

/*
	Test Controller Route
 */
$route['test'] = 'Test/test';

/*
	Default Route
 */
$route['default_controller'] = 'Welcome/index';