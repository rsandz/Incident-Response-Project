<?php

/**
 * Email Configuration
 *
 * Plase see code igniter documentation for email.
 * @see https://www.codeigniter.com/userguide3/libraries/email.html
 */

$config['protocol']    = 'smtp';
$config['smtp_user']    = '';
$config['smtp_pass']    = '';
$config['smtp_host']    = '';
$config['smtp_port']    = '';
$config['smtp_timeout'] = '7';
$config['charset']    = 'utf-8';
$config['newline']    = "\r\n";
$config['mailtype'] = 'html'; // Leave as is.
$config['validation'] = TRUE; // bool whether to validate email or not