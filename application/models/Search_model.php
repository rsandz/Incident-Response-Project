<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends MY_Model {

	/** @var int The number of rows if limit was not used. Good for pagination */
	public $total_rows;

	/**
	 * Constructs the Search model.
	 * Loads the necessary models needed to run the controller
	 */
	public function __construct()
	{
		parent::__construct();

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

		foreach ($this->db->get('action_log')->result() as $result)
		{
			$filter_ids[] = $result->log_id;
		}
		
		return isset($filter_ids) ? $filter_ids : array();
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

		return isset($filter_ids) ? $filter_ids : array();
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
	 * Gets log entries based on provided log ids
	 * @param  array  $log_ids An array of log ids
	 * @param  integer $offset The pagination offset
	 * @param  string $type Whether to return an array of objects or array of associative arrays
	 *
	 * @return array		   An array of objects resulting from db->get()
	 */
	public function get_log_entries($log_ids, $offset = 0, $type = 'array')
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
				return NULL;
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
		//Set Table
		$this->db->from('action_log');

		//Get entries
		$this->total_rows = $this->db->count_all_results('', FALSE);

		//Limit and Offset
		$this->db->limit($per_page, $offset);
		$query = $this->db->get();

		if ($query->num_rows() == 0)
		{
			return NULL;
		}
		
		return $query;
	}

	/**
	 * Will get all table rows and return a HTML table string. Used for the view tables @see (Search/view_tables)
	 * @param  string $table  The table name
	 * @param  offset $offset Offset for pagination
	 * @return string         HTML string for the table
	 */
	public function get_all_entries($table, $offset)
	{
		$per_page = $this->config->item('per_page');

		//Conditions and data formatting for certain tables
		
		//Format the table
		$this->sql_commands_for_table($table); //Applies table filters as stated in view_tables.php

		//Limits and Offset
		$this->db->limit($per_page, $offset);

		//Get Table DATA
		$this->total_rows = $this->db->count_all_results($table, FALSE);
		$query = $this->db->get($table);

		//Censoring Password Hashes - See configuration for disabling this
		if ($this->config->item('show_hashes') && $this->db->field_exists('password', $table)) 
		{
			foreach ($query->result() as &$row)
			{
				$row->password = '***********';
			}
		}

		return $query;
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
	
}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */