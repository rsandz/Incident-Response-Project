<?php 

/*
| ---------------------------------------------
|   Analytics Configuration
| ---------------------------------------------
| Please note that analytics settings are split between
| the analytics_metrics table in the database and this
| configuration file
*/

/* 
| ----------------------------------------------
|   The Default View ID 
| ----------------------------------------------
*/

$config['view_id'] = "178427523";

/*
| -----------------------------------------------
|   Service Account Authentication File Location
| -----------------------------------------------
*/
 
$config['auth_file'] = APPPATH . '/../assets/googleAuth/DrFehmiWeb-1c08d45de240.json';

/*
| ------------------------------------------------
|    Valid Metrics
| ------------------------------------------------
| 
| Contains the valid metrics that the user can chooose
| as a condition to generate an incident.
|
| Must be an Associative array conaining key, value pairs
| where the keys are the metric id according to google
| and the values are the display name that the user will see
*/

$config['valid_metrics'] = array(
    'ga:users'      => 'Users',
    'ga:sessions'   => 'Sessions',
    'ga:bounces'    => 'Bounces',
    'ga:bounceRate' => 'Bounce Rate',

);