<?php

/*
 *	Modification Validation Rules
 * 	=============================
 *
 * 	This configuration file contains the form validation rules
 * 	during modification of a row.
 *
 * 	The rules should be entered as an associative array such that:
 *
 * 	config['modify_rules'][table] = array('field' => 'rule')
 *
 * 	or
 *
 * 	config['modify_rules'] = array(
 *  		'table' => array('field' => 'rule')
 * 		)
 *
 * 	See below for examples
 *	 
 */

$config['modify_rules'] = array(
	'actions' => array(
		'action_name' => 'required',
		'type_id' => 'required',
		'project_id' => 'required',
		'is_active' => 'required',
		'is_global' => 'required',
		'action_id' => 'required'
	),
	'users' => array(
		'email' => 'required|valid_email',
		'password' => 'required',
		'first_name' => 'required',
		'last_name' => 'required',
		'privileges' => 'required',
		'user_id' => 'required'
	),
	'teams' => array(
		'team_name' => 'required',
		'team_id' => 'required'
	),
	'projects' => array(
		'project_name' => 'required',
		'project_id' => 'required'
	),

);

/*
 *	Foreign Key Configuration
 *	======================
 *
 *	This will declare which fields are foreign Keys and will subsequently turn them into
 *	dropdown selection boxes.
 *
 * 	This is so that the user can select fields like project by name, instead of by id
 *
 * $config['foreign_key'] = array(
 * 	'foreign_key' => array(
 * 		'FK_table' => 'Name of the table that the foreign key maps to'
 * 		'display_column' => 'Name of the column from the reference/foreign table that the user will see (i.e. type_name)'
 * 	)
 * );
 */

$config['foreign_keys'] = array(
  	'type_id' => array(
  		'FK_table' => 'action_types',
  		'display_column' => 'type_name',
  	),

  	'project_id' => array(
  		'FK_table' => 'projects',
  		'display_column' => 'project_name',
  	),

  	'action_id' => array(
  		'FK_table' => 'actions',
  		'display_column' => 'action_name',
  	),

  	'project_leader' => array(
  		'FK_table' => 'users',
  		'display_column' => 'CONCAT(first_name, " ", last_name)'
  	),
 );