<?php

/*
| --------------------------------------------------------------
|			Authentication Configuration
| --------------------------------------------------------------
|
| This is coniguration for the authentication library.
| You will be able to change the following functionalities in 
| this file:
| 	- Login Configuration
| 	- Password Recovery Configuration
|
*/

/*
| ---------------------------------------------------------------
| Specific Errors
| ---------------------------------------------------------------
| 
| Wether to display a generalized error or a specific error when 
| authentication fails.
| i.e. 'Incorrect Credentials' vs. 'Incorrect Email'
|
| Type: boolean
*/

$config['specific_errors'] = TRUE;

/*
| -----------------------------------------------------------------
| Messages
| -----------------------------------------------------------------
|
| Here you can change the messages when specific authentication
| errors occurs.
*/

$config['messages'] = array(
	'invalid_email' => 'The Email Could not be found in the Database',
	'invalid_pass' => 'Incorrect Password',
	'general_error' => 'Invalid Credentials',
	'reason_not_logged_in' => 'You were redirected here since you were not logged in.'
);

/*
| -----------------------------------------------------------------
| Default Salt
| -----------------------------------------------------------------
|
| The Default salt in case it isn't set in appconfig
 */

$config['default_salt'] = 'ifttca1';

/*
| -------------------------------------------------------------------
| Recover Email Content
| -------------------------------------------------------------------
|
| The email to send when recovering password
|
| Use a formatted string with HTML:
|  {name} will be replaced by user's name.
|  {link} will be replaced by the reset password form url
 */

$config['recover_email_content'] = "
	<p>Hello {name},</p>
	<p>
		You have requested to change your password. <br>
		If this was not you, someone may be trying to gain access to your account. Please notify administration immediately.<br>
		Please click on the following link to reset your password: <br>
	</p>
	<p>{link}</p>
	<p></p>
	<p>Kind Regards, <br>
	Password Manager</p>";