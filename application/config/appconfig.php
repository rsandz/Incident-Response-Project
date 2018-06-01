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

/**
 *  Salt
 *
 * 	For hashing, this salt will be used.
 */

$config['salt'] = 'ifft';

/**=========================================
 *		Email Configuration
 * =========================================
 */

/**
 * Password recovery Email
 */

$config['recovery_email'] = 'ryan_sandoval@live.com';
$config['recovery_email_name'] = 'Password Manager'; 