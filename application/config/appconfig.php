<?php

/* 
| ====================================================
|	 App Configuration
| ====================================================
| Configuration Specific to the app will be Placed here
*/

/*
| Per Page
|
| The amount of rows to display in various tables in the website
| This is used by the code ignitor pagination
| 
| By Default, it is 10 rows per page.
| 
*/

$config['per_page'] = 10;

/*
| =========================================
| 				Security
| =========================================
|
| Configurations related to website
| security will be placed here
*/

/*
|  Salt
|
| 	For hashing, this salt will be used.
*/

$config['salt'] = 'ifft';

/*
| Show Password Hashes
|
| Whether to show password hashes in tables.
*/

$config['show_hashes'] = FALSE;

/*
| Search Viewing privileges
|
| Specific privileges can be set to only view certain results
| These results are:
| 	- 'user_only' - Can only view search results that match current user_id
| 	- 'team_only' - Can only view search results that have the same team_ids as current teams
| 	- 'all' - Can view all search results
*/

$config['search_privileges'] = array(
	'user' => 'user_only',
	'team_leader' => 'team_only',
	'admin' => 'all'
);
