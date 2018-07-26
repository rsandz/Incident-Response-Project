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

/*
| -------------------------------
| URI typography for this project
| -------------------------------
|
| Please try to follow this format:
| siteurl.com/Controller/method/param/param2/...
|
| Note: Controller is capitalized and method is not.
*/

//////////////////////////
//REMOVE BEFORE DEPLOY! //
//////////////////////////
$route['Setup'] = 'Setup';

//=================================

/*
	Account ROuting
 */

$route['Account'] = 'User/Account';
$route['Account/settings'] = 'User/Account/settings';
$route['Account/admin-settings'] = 'User/Account/admin_settings';

/*
	Incidents Routing
 */

$route['Incidents'] = 'Incidents/Pages';
$route['Incidents/create'] = 'Incidents/Pages/create_incident';
$route['Incidents/analytics-settings'] = 'Incidents/Pages/analytics_settings';
$route['Incidents/report/select'] = 'Incidents/Pages/view_incidents';
$route['Incidents/report/select/(:any)'] = 'Incidents/Pages/view_incidents/$1';
$route['Incidents/report/(:any)'] = 'Incidents/Pages/report/$1';

/*
	Login and Visitor Routes
 */
$route['Welcome'] = 'Welcome/index';
$route['login'] = 'User/Login/login';
$route['logout'] = 'User/Login/logout';

/*
	Help Routes
 */
$route['Help/markups'] = 'Help/markups';

/*
	Password Recovery Routes
 */
$route['recover'] = 'User/recover_password';
$route['recover-form/(:any)/(:any)'] = 'User/recover_form/$1/$2';

/*
	Admin Routes
 */
$route['Admin'] = 'Admin/index';
$route['Admin/view-logs'] = 'Admin/view_logs/0';
$route['Admin/view-logs/(:any)'] = 'Admin/view_logs/$1';

/*
	Logging Routes
 */
$route['Logging'] = 'Logging/log';
$route['Logging/(:any)'] = 'Logging/log';

/*
	Create Routes
 */
$route['Create'] = 'Create/index/action';
$route['Create/(:any)'] = 'Create/index/$1';

/*
	Stats Routes
 */
$route['Stats/my_stats'] = 'Stats/my_stats';
$route['Stats/project_stats/(:any)'] = 'Stats/project_stats/$1';
$route['Stats/team_stats/(:any)'] = 'Stats/team_stats/$1';
$route['Stats/custom/create/(:any)'] = 'Stats/create_custom/$1';
$route['Stats/custom/(:any)'] = 'Stats/custom_stats/$1';

/*
	Search Routes
 */
$route['Search/result/(:any)'] = 'Search/search/$1';
$route['Search/result'] = 'Search/search';
$route['Search'] = 'Search/index';

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
	Default Route
 */
$route['default_controller'] = 'Welcome/index';