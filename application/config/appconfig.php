<?php

/* ====================================================
/				 App Configuration
/ =====================================================
/ Configuration Specific to the app will be Placed here
*/

/**
 * Timezone Config
 *
 * Refer to PHP documentation for the possible values.
 */

$config['timezone'] = 'America/Edmonton';

/**
 * Per Page
 *
 * The amount of rows to display in various tables in the website
 * This is used by the code ignitor pagination
 * 
 * By Default, it is 10 rows per page.
 * 
 */

$config['per_page'] = 10;

/**=========================================
 * 				Security
 * =========================================
 *
 * Configurations related to website
 * security will be placed here
 */

/**
 *  Salt
 *
 * 	For hashing, this salt will be used.
 */

$config['salt'] = 'ifft';

/**
 * Show Password Hashes
 *
 * Whether to show password hashes in tables.
 */

$config['show_hashes'] = FALSE;

/**
 * Search Viewing privileges
 *
 * Specific privileges can be set to only view certain results
 * These results are:
 * 	- 'user_only' - Can only view search results that match current user_id
 * 	- 'team_only' - Can only view search results that have the same team_ids as current teams
 * 	- 'all' - Can view all search results
 */

$config['search_privileges'] = array(
	'user' => 'user_only',
	'team_leader' => 'team_only',
	'admin' => 'all'
);

/**=========================================
 *		Email Configuration
 * =========================================
 *
 * Please also @see config/email.php to setup 
 * the server needed to send the email.
 */

/**
 * Recovery Message
 *
 * The message to send when recovering password
 * DO NOT CHANGE THE FORMAT FOR THE URL!
 *
 * Use a formatted string with HTML:
 *  {name} will be replaced by user's name.
 *  {link} will be replaced by the reset password form url
 *  {recovery_name} will be replaced by the name of the password manager. i.e. th recovery_name in config/email.php
 */

$config['recovery_message'] = "
	<p>Hello {name},</p>
	<p>
		You have requested to change your password. <br>
		If this was not you, someone may be trying to gain access to your account. Please notify administration immediately.<br>
		Please click on the following link to reset your password: <br>
	</p>
	<p>{link}</p>
	<p></p>
	<p>Kind Regards, <br>
	{recovery_name}</p>";
