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
		'name' => 'required',
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
 *	Dropdown Configuration
 *	======================
 *
 *	This will declare which fields are drop downs in the modify form.
 *	It will also declare the option to be seen by the end user and its value.
 *
 * The value in the dropdown will be the field name. (i.e. type_id)
 *
 * $config['dropdown_config'] = array(
 * 	'field_name' => array(
 * 		'table' => 'Name of the table to get data from'
 * 		'text_column' => 'Name of the column that the user will see (i.e. type_name)',
 * 	)
 * );
 */

$config['dropdown_config'] = array(
  	'type_id' => array(
  		'table' => 'action_types',
  		'text_column' => 'type_name',
  	),

  	'project_id' => array(
  		'table' => 'projects',
  		'text_column' => 'project_name',
  	),

  	'action_id' => array(
  		'table' => 'actions',
  		'text_column' => 'action_name',
  	),
 );