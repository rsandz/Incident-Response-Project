<?php

/**
 * Email Configuration
 *
 * Plase see code igniter documentation for email.
 * @see https://www.codeigniter.com/userguide3/libraries/email.html
 */

$config['smtp_user']    = 'rsandovatest@gmail.com';
$config['smtp_pass']    = 'Blueman8427';
$config['protocol']    = 'smtp';
$config['smtp_host']    = 'ssl://smtp.gmail.com';
$config['smtp_port']    = '465';
$config['smtp_timeout'] = '7';
$config['charset']    = 'utf-8';
$config['newline']    = "\r\n";
$config['mailtype'] = 'html'; // Leave as is.
$config['validation'] = TRUE; // bool whether to validate email or not

//=====================================================================

$config['recovery_name']  = 'Password Manager'; // Name to associate with when email is sent