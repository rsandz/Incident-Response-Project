<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model {

	/**
	 * Constructs the Search model.
	 * Loads the necessary models needed to run the controller
	 */
	public function __construct()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->library('table');

		$this->load->model('logging_model');
	}

	/**
	 * Searches for querries matching filters
	 * @param  array $query Contains the filters passed in the query
	 * @return array        The array of log ids that match the filters.
	 */
	public function filter_search($query) 
	{
		$this->join_tables();
		$this->apply_query_filters($query);
		$this->db->select('action_log.log_id');
		$filter_ids = array();
		foreach ($this->db->get('action_log')->result() as $result)
		{
			$filter_ids[] = $result->log_id;
		}
		
		return $filter_ids;
	}

	/**
	 * Applies the keywords filters in $query
	 * @param  array $keywords Array containing the search query conditions @see search_helper->query_from_post()
	 */
	public function apply_query_keywords($query)
	{
		if (empty($query['keywords']))
		{
			return FALSE;
		}

		switch ($query['ksearch_type']) {
			case 'any':
				foreach ($query['keywords'] as $keyword) 
				{
					//Will be using 'OR' between keywords		
					$this->db->or_group_start();
					foreach ($query['keyword_filters'] as $column) 
					{
						$this->db->or_like($column, $keyword);
					}
					$this->db->group_end();

				}
				break;
			
			case 'all':
				foreach ($query['keywords'] as $keyword) 
				{
					//Will be using 'AND' between keywords		
					$this->db->group_start();
					foreach ($query['keyword_filters'] as $column) 
					{
						$this->db->or_like($column, $keyword);
					}
					$this->db->group_end();

				}
				break;

			default:
				show_error('Invalid Search Type');
				break;
		}
	}

	/**
	 * Applies the search filters in $query to the db driver.
	 * @param  array $query The $query array. @see filter_search()
	 * @return boolean      True if Sucesssful
	 */
	public function apply_query_filters($query)
	{
		// Date Filter - Uses from_date and to_date
		if (!empty($query['from_date']))
		{
			$this->db->where('log_date >=',$query['from_date']);
		}

		if (!empty($query['to_date']))
		{
			$this->db->where('log_date <=',$query['to_date']);
		}

		// Type Filter - Based on type
		if (!empty($query['action_types']))
		{
			$this->db->group_start();
			foreach ($query['action_types'] as $action_type) 
			{
				$this->db->or_where('actions.type_id', $action_type);
			}
			$this->db->group_end();
		}
		
		// Team Filter
		if (!empty($query['teams']))
		{
			$this->db->group_start();

			if (is_array($query['teams']))
			{
				foreach ($query['teams'] as $team) 
				{
					$this->db->or_where('action_log.team_id', $team);
				}
			}
			else
			{
				//Support for 1 number and not array
				$this->db->where('action_log.team_id', $query['teams']);
			}

			if (isset($query['null_teams']) && $query['null_teams']) //Checks if isset and TRUE
			{
				$this->db->or_where('action_log.team_id IS NULL');
			}

			$this->db->group_end();	
		}
		else
		{
			if (isset($query['null_teams']) && $query['null_teams'])
			{
				$this->db->where('action_log.team_id IS NULL');
			}
		}

		// Project Filter
		if (!empty($query['projects']))
		{
			$this->db->group_start();
			if (is_array($query['projects']))
			{
				foreach ($query['projects'] as $project) 
				{
					$this->db->or_where('action_log.project_id', $project);
				}
			}
			else
			{
				//Support if we just get 1 num and not a string
				$this->db->where('action_log.project_id', $query['projects']);
			}

			if (isset($query['null_projects']) && $query['null_projects']) //Checks if isset and TRUE
			{
				$this->db->or_where('action_log.project_id IS NULL');
			}

			$this->db->group_end();
		}
		else
		{
			if (isset($query['null_projects']) && $query['null_projects'])
			{
				$this->db->or_where('action_log.project_id IS NULL');
			}
		}
		
		//User Filter
		if (isset($query['users']))
		{
			$this->db->group_start();

			if (is_array($query['users']))
			{
				foreach ($query['users'] as $user_id) 
				{
					$this->db->or_where('action_log.user_id', $user_id);
				}
			}
			else
			{
				//Support for just a string too
				$this->db->where('action_log.user_id', $query['users']);
			}
			$this->db->group_end();
		}

		return TRUE;
	}

	/**
	 * Calls a bunch of join commands, so its easier to search through all the tables.
	 * 
	 * @return boolean Returns TRUE if successful
	 */
	public function join_tables()
	{
		//Join all tables so that we can query information all at the same time.
		$this->db
			->join('actions','actions.action_id = action_log.action_id')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->join('projects','projects.project_id = action_log.project_id', 'left')
			->join('teams','teams.team_id = action_log.team_id', 'left')
			->join('users','users.user_id = action_log.user_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');

		return TRUE;
	}

	/**
	 * Searches for rows in the log table that matches the filters provided
	 * @param  array $keywords An array of the keywords
	 * @param  array $columns  The column to search the keyword in
	 * @return array           Array of log ids that match the filter
	 */
	public function keyword_search($query) 
	{
		if (isset($query['keyword_filters']) && isset($query['keywords']))
		{
			$this->join_tables();
			$this->apply_query_keywords($query);	
		}
		
		$this->db->select('action_log.log_id');
		foreach ($this->db->get('action_log')->result() as $result)
		{
			$filter_ids[] = $result->log_id;
		}

		return $filter_ids;
	}
	
	/**
	 * Creates a logs table based on the provied log ids
	 * @param  array  $log_ids An array of log ids
	 * @param  integer $offset  The pagination offset
	 * @return array           Array containing table data
	 *                          - ['table'] for the table html
	 *                          - ['num_rows'] The nynber of rows in the table (Useful in pagination)
	 *                          - ['heading'] - Array containing tahble headings
	 */
	public function get_logs_table($log_ids, $offset = 0)
	{
		$this->load->library('table');
		$per_page  =$this->config->item('per_page');

		//Join and select
		$this->db
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');

		$this->sql_commands_for_table('logs');

		if (!is_array($log_ids) OR count($log_ids) < 1)
		{
				$data['table'] = 'No Results';
				return $data;
		}
		else
		{
			$this->db->where_in('log_id', $log_ids);
		}

		// Privilege Filter
		// Editable in appconfig
		switch ($this->config->item('search_privileges')[$this->session->privileges]) 
		{
			case 'user_only':
				$this->db->where('users.user_id', $this->session->user_id);
				break;
			case 'team_only':
				$this->db->where('users.team_id', 1);
				break;
			case 'all':
				break;
			default:
				show_error('Not Authorized', 403);
				break;
		}

		//Get entries
		$matches = $this->db->get('action_log');

		$data['num_rows'] = $matches->num_rows();

		if ($data['num_rows'] == 0)
		{
			$data['table'] = 'No Results';
			return $data;
		}

		//Make the Table
		$table_data = array_slice($matches->result_array(), $offset, $per_page); //Offset for pagination

		$this->table->heading_from_config('logs');
		$data['table'] = $this->table->my_generate($table_data);
		
		return $data;

	}

	/**
	 * Will get all table rows and return a HTML table string. Used for the view tables @see (Search/view_tables)
	 * @param  string $table  The table name
	 * @param  offset $offset Offset for pagination
	 * @return string         HTML string for the table
	 */
	public function tabulate_table($table, $offset)
	{
		$per_page  =$this->config->item('per_page');

		//Conditions and data formatting for certain tables
		
		//Format the table
		$this->sql_commands_for_table($table); //Applies table filters as stated in view_tables.php

		//Get Table DATA
		$query = $this->db->get($table);
		$table_data = array_slice($query->result_array(), $offset, $per_page); //Offset for pagination

		//Censoring Password Hashes - See configuration for disabling this
		if ($this->config->item('show_hashes') && $this->db->field_exists('password', $table)) 
		{
			foreach ($table_data as &$row)
			{
				$row['password'] = '***********';
			}
		}
		//Create The table
		$data['table'] = $this->table->my_generate($table_data, $query->list_fields());
		$data['num_rows'] = $query->num_rows();
		$data['table_name'] = $table;

		return $data;
	}


		/**
		 *	Querries if data provided is already in table.
		 *
		 *	@param string $table Table to check where data is in
		 *	@param array  $data  Associative array with column-value pairs
		 * 
		 *	@return boolean True if in database. False if not.
		 */
		public function data_exists($table, $data) 
		{
			$this->db->where($data);
		
			if (!empty($this->db->get($table)->row()))
			{
				return $this->db->get($table)->row();

			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Reads through a filter array and executes the contents
		 * Array must be of the form:
		 *
		 * $commands['command'] = array(condition => condition);
		 * i.e. $commands['join']  = array(
		 * 			'users' => 'users.user_id = action_log.user_id'
		 * 			'teams' => 'teams.team_id = action_log.team_id'
		 * 		)
		 * 		$commands['where'] = array('team_id' => 2, user_id => 3)
		 * 		$commands['select'] = ('user_id', 'name')
		 * 
		 * @return boolean   True if Sucessful
		 */
		public function execute_filters($commands) 
		{
			try 
			{
				if (is_array($commands['where']))
				{
					foreach ($commands['where'] as $key => $value) 
					{
						if (strtoupper(strtok($key, ' ')) == 'OR') //Handles OR modifiers too.
						{
							$this->db->or_where(strtok(' '), $value);
						}
						else
						{
							$this->db->where($key, $value);
						}		
					}
				}
				elseif (is_string($commands['where']))
				{
					//If string, will feed entire string into Code Igniter's where method
					$this->db->where($commands['where']);
				}
				
				if (is_array($commands['select']))
				{
					foreach($commands['select'] as $select)
					{
							$this->db->select($select);
					}
				}

				if (is_array($commands['join']))
				{
					foreach ($commands['join'] as $table => $condition) 
					{
						if (is_array($condition)) //If $condition is an array, we must unpack it
						{
							$this->db->join($table, ...$condition);
							/*
							This allows us to create LEFT joins and RIGHT joins.
							syntax is commands['join'] = array('table' => array['condition', join type])
							*/
						}
						else //No need to unpack
						{
							$this->db->join($table, $condition);
						}
					}
				}

				log_message('info', 'Filters Executed');
				return True;
			} 
			catch (Exception $e) 
			{
				log_message('error', 'Error Excecuting filters. \n'.$e);
				return FALSE;
			}
			
		}

		/**
		 * Applies the table filters for a specific table as stated in config/view_tables.php
		 *
		 * @param string $table The Table name in config/view_tables.php
		 * 
		 * @return boolean True if successful or no filters were found
		 */
		public function sql_commands_for_table($table) 
		{
			//Load Config
			if (!$this->load->config('view_tables', TRUE)) //loads config too
			{
				log_message('error', 'View Tables configuration was not loaded sucessfully. Table formatting may be unexpected.');
			}

			$commands['join'] = isset($this->config->item($table, 'view_tables')['join']) ? 
									$this->config->item($table, 'view_tables')['join'] : NULL;
			$commands['select'] = isset($this->config->item($table, 'view_tables')['select']) ? 
									$this->config->item($table, 'view_tables')['select'] : NULL;
			$commands['where'] = isset($this->config->item($table, 'view_tables')['where']) ? 
									$this->config->item($table, 'view_tables')['where'] : NULL;

			if ($commands['join'] == NULL && $commands['select'] == NULL && $commands['where'] == NULL)
			{
				log_message('info', 'No config for Table: '.$table);
				return TRUE;
			}

			return $this->execute_filters($commands);
		}

		/**
		 * Gets a Codeignitor query object from a database given parameters, without calling the result() method.
		 *
		 * @param  string $table Table string
	 	 * @param  Mixed  $where Associative array of column-value pairs or string
	 	 * query
	 	 * @param  Mixed  $select Array or String of columns to select
	 	 * @param  array  $join Associative array of table as key and join conditios
	 	 * as value
		 * 
		 * @return object The query object returned by code ignitor's db->get()
		 */
		public function get_items_raw($table, $where = NULL, $select = NULL, array $join = NULL)
		{
			// Turn into array, so that it can be passed into execute_filters()
			$commands['where'] = $where;
			$commands['select'] = $select;
			$commands['join'] = $join;

			$this->execute_filters($commands);

			return $this->db->get($table);
		}

		/**
		 * Gets items from database given some parameters. Unlike raw, returns query->results()
		 * 
		 * @param  string $table Table string
		 * @param  Mixed  $where Associative array of column-value pairs or string
		 * query
		 * @param  Mixed  $select Array or String of columns to select
		 * @param  array  $join Associative array of table as key and join conditios
		 * as value
		 * @return array             result_array of results
		 */
		public function get_items($table, $where = NULL, $select = NULL, array $join = NULL) 
		{
			return $this->get_items_raw($table, $where, $select, $join)->result();
		}

		/**
		 *	Gets and Returns Table Fields Data
		 *
		 *	@param $table Table name to get field data.
		 *	@param Boolean $keep_ids Whether to keep or remove the id fields. i.e. if true, user_id will not be in the returned object
		 * 	
		 *	@return object Field data object as per Code Ignitor's structure. Also includes enum values for enum type columns.
		 *	               False if failed.
		 */
		public function get_field_data($table, $keep_ids = FALSE)
		{
			if ($this->db->table_exists($table))
			{	
				$field_data = $this->db->field_data($table);

				foreach ($field_data as $key => &$field) 
				{
					if ($field->type == 'enum')
					{
						$enum_vals = $this->get_enum_vals($table, $field->name);
						$field->enum_vals = $enum_vals;
					}

					if (stripos($field->name, 'id') && !$keep_ids)
					{
						unset($field_data[$key]);
					}
				}

				return $field_data;
			}
			else
			{
				return FALSE;
			}
		}

	
	/**
	 * Gets the Enumeartion Values for a given field in a table
	 * 
	 * @param  string $table Table name in database
	 * @param  string $field Field name in Database
	 * @return array         Returns all enum values as array of strings
	 */
	public function get_enum_vals($table, $field) 
	{
		$query = $this->db->query('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$field.'"')->row()->Type;
		preg_match_all("/'.*?'/", $query, $results); //Reg match to get quoted enum values

		//Stripping Quotes
		$this->load->helper('string');
		
		foreach ($results[0] as &$result)//[0] is for array of full matches;
		{
			$result = strip_quotes($result);
		} 

		return $results[0];
	}

	/**
	 * Gets the projects available to the user
	 * @param boolean $admin_mode If TRUE, will also get inactive projects
	 * @return array             Code Igniter db results that contains the projects
	 */
	public function get_projects($admin_mode = FALSE)
	{
		if (!$admin_mode)
		{
			//Only get active projects
			$this->db->where('is_active', 1);
		}

		//Get the projects
		return $this->db->join('users', 'users.user_id = projects.project_leader', 'left')
			->select('project_name, project_id, users.name as project_leader_name, project_desc')
			->get('projects')->result();
	}

	/**
	 * Get the teams that the provided user id is in
	 * @param boolean $admin_mode If true, will ignore the user_id and get all the teams
	 * @param  string $user_id User Id of the user in question. 
	 * @return array           Code Igniter db results array that contains the teams
	 *                         that the user is in.
	 */
	public function get_user_teams($user_id, $admin_mode = FALSE)
	{
		if (!$admin_mode)
		{
			$query = $this->get_items('user_teams', array('user_id' => $user_id), 'team_id');
			$team_ids = array_map(function($x){return $x->team_id;}, $query);
			$this->db->where_in('team_id', $team_ids);
		}
		else
		{
			//User id can't be null!
			if (!$user_id) return NULL;
		}

		return $this->db
					->join('users', 'users.user_id = teams.team_leader', 'left')
					->select('team_name, team_id, team_desc, users.name as team_leader_name')
					->get('teams')
					->result();
	}
	
	/**
	 * Gets the users in a team
	 * @param  string|array $team The team(s) name that will be querried for users
	 * @return array       	Code Igniter db results array that contains the users
	 *                      in the team. Data taken from users table.
	 */
	public function get_team_users($team_id)
	{
		if (is_array($team_id))
		{
			$user_ids = $this->db->where('team_id', $team_id)->get('user_teams');
			if (count($user_ids) > 0)
			{
				return $this->db->where_in('user_id', $user_ids)->get('users');
			}
			else
			{
				return NULL;
			}

		}
		else
		{
			$query = $this->db->where('team_id', $team_id)->get('user_teams')->result();
			$user_ids = array_map(function($x) {return $x->user_id;}, $query);
			if (count($user_ids) > 0)
			{
				return $this->db->where_in('user_id', $user_ids)->get('users')->result();
			}
			else
			{
				return NULL;
			}
		}
	}

	public function get_team_info($team_id)
	{
		$query = $this->db->where('team_id', $team_id)->get('teams')->row(); 
		$data['team_name'] = $query->team_name; //Get team name
		$data['team_id'] = $query->team_id; //Get Team ID

		$data['team_members_raw'] = $this->get_team_users($team_id); //CI result array of obj

		//Get amount of members
		if (isset($data['team_members_raw']))
		{
			$data['num_members'] = count($data['team_members_raw']);
		}
		else
		{
			$data['num_members'] = 0; //team_members_raw is NULL, so no users
		}

		//Get team logs total
		$data['team_logs'] = $this->db->select('COUNT(*) as count')
									->where('team_id', $team_id)
									->get('action_log')
									->row()
									->count;

		//Get Team leader
		$query2 = $this->db->where('user_id', $query->team_leader)->get('users')->row();
		if (isset($query2))
		{
			$data['team_leader_name'] = $query2->name;
		}
		else
		{
			$data['team_leader_name'] = NULL;
		}

		if (isset($data['team_members_raw']))
		{
			foreach($data['team_members_raw'] as $team_member)
			{
				$data['team_members'][$team_member->name] = $team_member->user_id;
			}
		}

		return $data;
	}

	/**
	 * Gets the users not in the twam
	 * @param  int $team_id The id of the team to test
	 * @return array          A CI db results array of objects containing the users not in the team.
	 */
	public function get_users_not_in_team($team_id)
	{
		$users_in_team = array_map(
			function ($user) {return $user->user_id;},
			$this->db->where('team_id', $team_id)->get('user_teams')->result());

		if (count($users_in_team) > 0)
		{
			return $this->db->where_not_in('user_id', $users_in_team)->get('users')->result();
		}
		else
		{
			return $this->db->get('users')->result();
		}
	}

	/**
	 * Gets the user's name from id
	 * @param  int $user_id Id of the user
	 * @return string          User's name
	 */
	public function get_user_name($user_id)
	{
		return $this->db
			->select('name')
			->where('user_id', $user_id)
			->get('users')
			->row()
			->name;
	}

	/**
	 * Gets the team name from the team id
	 * @param  int $team_id The ID of the team
	 * @return string          THe team Name
	 */
	public function get_team_name($team_id)
	{
		return $this->db
			->select('team_name')
			->where('team_id', $team_id)
			->get('teams')
			->row()
			->team_name;
	}

	/**
	 * Get project name from project ID
	 * @param  int $project_id The ID of the project
	 * @return string             THe project name
	 */
	public function get_project_name($project_id)
	{
		return $this->db
			->select('project_name')
			->where('project_id', $project_id)
			->get('projects')
			->row()
			->project_name;
	}

	/**
	 * Get the action type name from the action type ID
	 * @param  int $type_id The action ype ID
	 * @return string          The action type name
	 */
	public function get_type_name($type_id)
	{
		return $this->db
			->select('type_name')
			->where('type_id', $type_id)
			->get('action_types')
			->row()
			->type_name;
	}
}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */