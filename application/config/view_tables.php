<?php

/**
 * Configures the formatting and displayed data for Tables
 *
 *
 * The table formatting follows as:
 * $config['Table Name']['command'] = array(condition => condition);
 * 
 * i.e. $config['action_log']['join']  = array(
 * 			'users' => 'users.user_id = action_log.user_id'
 * 			'teams' => 'teams.team_id = array(action_log.team_id', left)
 * 		)
 * 		$config['action_log']['where'] = array('team_id' => 2, user_id => 3)
 * 		$config['action_log']['select'] = ('user_id', 'name')
 *
 * Headings can also be set manually rather than getting them automatically from the database.
 * Defining this will automatically disable the auto title maker for that table
 * e.g. $config['user_teams']['headings'] = array('name', 'user_id');
 * 	## MUST BE DONE IF TABLE FORMAT IS CHANGED ##
 * 
 * See below for examples
 */

/**
 * For Tables in Search/view_tables
 */

$config['user_teams']['join'] = array(
	'users' => 'users.user_id = user_teams.user_id',
	'teams' => 'teams.team_id = user_teams.team_id'
);

$config['user_teams']['select'] = array(
	'users.name', 'teams.team_name', 'users.user_id', 'teams.team_id', 'user_team_id'
);

$config['user_teams']['headings'] = array(
	'Name', 'Team Name', 'User Id', 'Team Id', 'User Team Id (Primary Key)'
);

/**
 * For the table created by the Searching. (Search/results)
 */

$config['logs']['select'] = array(
	'name', 'action_name', 'type_name', 'project_name', 'team_name', 'log_desc', 'hours', 'log_date', 'log_time'
);

$config['logs']['join'] = array(
	'actions' => 'actions.action_id = action_log.action_id',
	'users' => array('users.user_id = action_log.user_id', 'left'),
	'projects' => array('projects.project_id = action_log.project_id', 'left'),
	'teams' => array('teams.team_id = action_log.team_id', 'left'),
	'action_types' => array('action_types.type_id = actions.type_id')
);

/**
 * For Previous entries table shown in the user dashboard
 */

$config['prev_entries']['select'] = array(
	'action_name', 'type_name', 'project_name', 'team_name', 'log_desc', 'hours', 'log_date', 'log_time'
);

$config['prev_entries']['join'] = array(
	'actions' => 'actions.action_id = action_log.action_id',
	'action_types' => array('actions.type_id = action_types.type_id', 'left'),
	'projects'=> array('projects.project_id = action_log.project_id', 'left'),
	'teams' => array('teams.team_id = action_log.team_id', 'left')

);
