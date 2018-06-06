<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model {

	public function __construct()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->helper('inflector');
		$this->load->library('table');

		$this->load->model('logging_model');
	}

	public function filter_search($query) 
	{
		// Join statements
		$this->db
			->join('actions','actions.action_id = action_log.action_id', 'left')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->select('action_log.log_id');

		// Date Filter - Uses from_date and to_date
		if ($query['from_date'] !== "")
		{
			$this->db->where('log_date >=',$query['from_date']);
		}

		if (($query['to_date']) !== "")
		{
			$this->db->where('log_date <=',$query['to_date']);
		}

		// Type Filter - Based on type
		if (is_array($query['action_types']))
		{
			$this->db->group_start();
			foreach ($query['action_types'] as $action_type) 
			{
				$this->db->or_where('actions.type_id', $action_type);
			}
			$this->db->group_end();
		}
		
		// Team Filter
		if (is_array($query['teams']))
		{
			$this->db->group_start();
			foreach ($query['teams'] as $team) 
			{
				$this->db->or_where('action_log.team_id', $team);
			}

			if ($query['null_teams'])
			{
				$this->db->or_where('action_log.team_id IS NULL');
			}

			$this->db->group_end();	
		}
		else
		{
			if ($query['null_teams'])
			{
				$this->db->where('action_log.team_id IS NULL');
			}
		}

		// Project Filter
		if (is_array($query['projects']))
		{
			$this->db->group_start();
			foreach ($query['projects'] as $project) 
			{
				$this->db->or_where('action_log.project_id', $project);
			}

			if ($query['null_projects'])
			{
				$this->db->or_where('action_log.project_id IS NULL');
			}

			$this->db->group_end();
		}
		else
		{
			if ($query['null_projects'])
			{
				$this->db->or_where('action_log.project_id IS NULL');
			}
		}
		
		//User Filter
		if (is_array($query['users']))
		{
			$this->db->group_start();
			foreach ($query['users'] as $user_id) 
			{
				$this->db->or_where('action_log.user_id', $user_id);
			}
			$this->db->group_end();
		}
		
		$this->db->select('log_id');

		$filter_ids  = array();
		foreach ($this->db->get('action_log')->result() as $result)
		{
			array_push($filter_ids, $result->log_id);
		}
		
		return $filter_ids;
	}

	/**
	 * [keyword_search description]
	 * @param  [type] $keywords [description]
	 * @param  [type] $columns  [description]
	 * @return [type]           [description]
	 */
	public function keyword_search($keywords, $columns, $ksearch_type) 
	{
		if (!isset($columns))
		{
			return FALSE;
		}

		//NOTICE! I didn't check if $keywords is empty because codeigniter still puts into the query a wild card if the like condition is null. So what ends up happening is that we get all the results if $keyword is empty, WHICH is EXACTLY what we want!

		//Join all tables so that we can query information all at the same time.
		$this->db
			->join('actions','actions.action_id = action_log.action_id')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->join('projects','projects.project_id = action_log.project_id', 'left')
			->join('teams','teams.team_id = action_log.team_id', 'left')
			->join('users','users.user_id = action_log.user_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->select('action_log.log_id');

		switch ($ksearch_type) {
			case 'any':
				foreach ($keywords as $keyword) 
				{
					//Will be using 'OR' between keywords		
					$this->db->or_group_start();
					foreach ($columns as $column) 
					{
						$this->db->or_like($column, $keyword);
					}
					$this->db->group_end();

				}
				break;
			
			case 'all':
				foreach ($keywords as $keyword) 
				{
					//Will be using 'AND' between keywords		
					$this->db->group_start();
					foreach ($columns as $column) 
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
			
		$data = array();
		foreach ($this->db->get('action_log')->result() as $result)
		{
			array_push($data, $result->log_id);
		}

		return $data;
	}
	
	/**
	 * [get_logs description]
	 * @param  [type]  $log_ids [description]
	 * @param  integer $offset  [description]
	 * @return [type]           [description]
	 */
	public function get_logs_table($log_ids, $offset = 0)
	{
		$this->load->helper('table_helper');
		$per_page  =$this->config->item('per_page');

		//Join and select
		$this->db
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');

		$this->execute_table_filters('logs');

		if (!is_array($log_ids) OR count($log_ids) < 1)
		{
				$data['table'] = 'No Results';
				return $data;
		}
		$this->db->where_in('log_id', $log_ids);

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

		$data['table_data'] = array_slice($matches->result_array(), $offset, $per_page); //Offset for pagination
		$data['heading'] = array('Name', 'Action Name', 'Action Type', 'Project', 'Team', 'Log Description', 'Log Date', 'Log Time');

		$data['table'] = generate_table($data);
		
		return $data;

	}

	/**
	 * Will get all table rows and return a HTML table string. Used for the view tables @see (Search/view_tables)
	 * @param  string $table  The table name
	 * @param  offset $offset Offset for pagination
	 * @return string         HTML string for the table
	 */
	public function get_table_data($table, $offset)
	{
		$this->load->helper('inflector');
		$this->load->helper('table_helper');
		$per_page  =$this->config->item('per_page');

		//Conditions and data formatting for certain tables
		
		//Format the table
		$this->execute_table_filters($table); //Applies table filters as stated in view_tables.php
		$data['heading'] = $this->get_table_headings($table);

		//Get Table DATA
		$query = $this->get_items_raw($table);
		$data['table_data'] = array_slice($query->result_array(), $offset, $per_page); //Offset for pagination

		//Censoring Password Hashes - See configuration for disabling this
		if (in_array('Password', $data['heading']) && !$this->config->item('show_hashes')) 
		{
			foreach ($data['table_data'] as &$row)
			{
				$row['password'] = '***********';
			}
		}

		//Create The table
		$data['table'] = generate_table($data);
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
		public function execute_table_filters($table) 
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

	public function get_table_headings($table)
	{
		if ($this->config->load('view_tables'))

		$headings = array();
		if (isset($this->config->item($table, 'view_tables')['headings']))
		{
			$column_headings = $this->config->item($table, 'view_tables')['headings'];
		}
		elseif ($this->get_field_data($table) !== FALSE)
		{
			$column_headings = array_map(
			function($item) 
			{
				return humanize($item->name);
			},
			$this->get_field_data($table, TRUE));	
		}
		elseif (isset($this->config->item($table, 'view_tables')['select']))
		{
			$column_headings = $this->config->item($table, 'view_tables')['select'];
		}
		else
		{
			show_error('No table heading can be created.');
		}

		foreach ($column_headings as $heading) 
		{
			array_push($headings, humanize($heading));
		}

		return $headings;
	}


}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */