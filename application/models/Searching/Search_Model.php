<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Search Model
 * ============
 * @author Ryan Sandoval
 * @package Search
 *
 * The search model allows for searching the action log for
 * specific data. It can search by filters and by keywords.
 *
 * The filters available are:
 * 	- Date (Starting and Ending)
 * 	- Action Types
 * 	- Projects
 * 	- Teams
 * 	- Users
 *
 * Keyword Searching also has the following options:
 * 	- keywords_in 	Where to search the keywords in
 * 					i.e. user_name, team_name
 * 					See the method for all the options
 * 	- keyword_type  Whether 'all' keywords must be present to 
 * 					be a valid result
 * 					OR
 * 					Whether 'any' keyword can be present to be
 * 					a valid result
 *
 * The model uses a query builder system that is heavily 
 * inspired by the codeigniter database class.
 *
 * For example:
 * 		...search_model
 * 			->action_types(1)
 * 			->users(array(1,4,5))
 * 			->from_date('January 12, 2019')
 * 			->search()
 * 			
 * 	Will search for logs with 
 * 		action type ID 1 
 * 		AND user ID 1 or 4 or 5
 * 		AND being on the Date January 12, 2019
 *
 * To actually call the search, you must use search()
 * This will return the object that db->get() returns but with all
 * the logs that matches the search.
 */
class Search_model extends MY_Model {

	protected $SB_keywords       = array();
	protected $SB_keywords_in    = array();
	protected $SB_keyword_type   = 'any';
	
	protected $SB_from_date      = NULL;
	protected $SB_to_date        = NULL;
	protected $SB_action_types   = array();
	protected $SB_projects       = array();
	protected $SB_teams          = array();
	protected $SB_users          = array();
	
	protected $SB_null_projects  = TRUE;
	protected $SB_null_teams     = TRUE;
	
	protected $sort_array		 = array('log_date' => 'DESC', 'log_time' => 'DESC');

	protected $pagination_offset = 0;
	protected $pagination_limit  = 0;
	public 	  $unpaginated_rows  = NULL;
	
	/** @var boolean Whether to lock searches to the current user */
	protected $user_lock         = TRUE;
	protected $curr_user;

	/** @var array Stores Data on last query */
	public $debug = array();

	public function __construct()
	{
		parent::__construct();

        //Reset The stored data
        $this->reset();

        if ($this->authentication->check_admin())
        {
        	$this->user_lock = FALSE;
        }

        $this->curr_user = $this->session->user_id;
        //Set Values to Default
        $this->reset();
	}


	/**
	 * Sets the keywords
	 * @param  array|string $keywords Keywords to search for
	 * * @return Search_Model		Method Chaining
	 */
	public function keywords($keywords)
	{
		$parsed_keywords = $this->parse_keywords($keywords);
		$this->SB_keywords = $parsed_keywords;
		return $this;
	}

	/**
	 * Parses the given string into an array.
	 * Intended to use for interpreting the keyword data from keyword search.
	 * 
	 * @param  string $string The string to parse
	 * @return array          String turned into an array
	 */
	public function parse_keywords($string) 
	{
		//TODO: add tags in the future. i.e. not: Bob (Interpreted as - rows without keyword 'bob') 
	
		$keywords = explode(' ', $string);

		foreach ($keywords as $key => $keyword) 
		{
			if ($keyword == "")
			{
				unset($keywords[$key]);
			}
		}

		return $keywords;
	}

	/**
	 * Where to search the keywords in?
	 * Note: Calling more than once appends to the list
	 * See valid columns in the code to see what you can search in
	 * @param  array|string $columns 'all' will search in all columns
	 *                               array will search in all columns in array
	 *                               string will search in that column provided
	 * @return Search_Model		Method Chaining
	 */
	public function keywords_in($columns)
	{
		//Empty Storage
		$this->SB_keywords_in = array();

		$valid_columns = array(
				'user_name', 
				'team_name', 
				'project_name', 
				'action_name', 
				'type_name', 
				'log_desc'
			);

		if ($columns == 'all')
		{
			//'all' support
			$columns = $valid_columns;
		}
		elseif (!is_array($columns))
		{
			//String Support - Turn it into array
			$columns = array($columns);
		}

		//Validate/Format and insert
		foreach ($columns as $column) 
		{

			//Validate Column
			if (!in_array($column, $valid_columns))
			{
				$this->error("Invalid Column `{$column}` to search keywords in.");
				continue;
			}

			//For Agregate Functions
			if ($column == 'user_name')
			{
				$column = 'CONCAT(first_name, " ", last_name)';
			}

			$this->SB_keywords_in[] = $column;
		}

		return $this;
	}

	/**
	 * Search type for the keywords.
	 * Can either be:
	 * 	- 'any'  		Valid result as long ANY key word is matched
	 * 	- 'all'			Only valid if matches all keywords
	 * @param  string $type Type of search. See above
	 * @return Search_Model		Method Chaining
	 */
	public function keywords_type($type)
	{
		if ($type !== 'any' && $type !== 'all')
		{
			$this->error('Invalid Keyword Type. Use either "all" or "any"');
			return $this;
		}

		$this->SB_keyword_type = $type;

		return $this;
	}

	/**
	 * Sets the starting date boundry for the search
	 * Automatically converted to proper format
	 * 	i.e. July 20 2018, 2018-07-20 are both valid
	 * @param  string $date The date
	 * @return Search_Model       Method Chaining
	 */
	public function from_date($date)
	{
		if (empty($date))
		{
			return $this;
		}
		//PHP auto-format
		$date = strtotime($date);
		$date = date('Y-m-d', $date);

		$this->SB_from_date = $date;
		return $this;
	}

	/**
	 * Sets the ending date boundry for the search
	 * Automatically converted to proper format
	 * 	i.e. July 20 2018, 2018-07-20 are both valid
	 * @param  string $date The date
	 * @return Search_Model       Method chaining
	 */
	public function to_date($date)
	{
		if (empty($date))
		{
			return $this;
		}

		//PHP auto-format
		$date = strtotime($date);
		$date = date('Y-m-d', $date);

		$this->SB_to_date = $date;
		return $this;
	}

	/**
	 * Sets the action type
	 * @param  array|string|int $types Array of action type IDs or an action ID
	 * @return Search_Model        Method Chaining
	 */
	public function action_types($types)
	{
		if (empty($types))
		{
			return $this;
		}

		if (is_array($types))
		{
			$this->SB_action_types = array_merge($this->SB_action_types, $types);
		}
		else
		{
			$this->SB_action_types[] = $types;
		}
		return $this;
	}

	/**
	 * Sets the projects
	 * @param  array|string|int $projects  Array of project IDs, or a project ID
	 * @return Search_Model           Method Chaining
	 */
	public function projects($projects)
	{
		if (empty($projects))
		{
			return $this;
		}

		if (is_array($projects))
		{
			$this->SB_projects = array_merge($this->SB_projects, $projects);
		}
		else
		{
			$this->SB_projects[] = $projects;
		}
		return $this;
	}

	/**
	 * Sets whether to search for logs with no projects
	 * @param  boolean $value TRUE to search. FALSE to not search
	 * @return Search_Model        Method Chaining
	 */
	public function null_projects($value = TRUE)
	{
		if ($value == TRUE OR $value == FALSE)
		{
			$this->SB_null_projects = $value;
		}
		else
		{
			$this->error('Null Projects did not receive a proper Boolean');
		}

		return $this;
	}

	/**
	 * Sets whether to search for logs with no teams
	 * @param  boolean $value TRUE to search. FALSE to not
	 * @return Search_Model        Method Chaining
	 */
	public function null_teams($value = TRUE)
	{
		if ($value == TRUE OR $value == FALSE)
		{
			$this->SB_null_teams = $value;
		}
		else
		{
			$this->error('Null Teams did not receive a proper Boolean');
		}

		return $this;
	}

	/**
	 * Sets the teams
	 * @param  array|string|int $teams Array of team IDS or an ID
	 * @return Search_Model        Method Chaining
	 */
	public function teams($teams)
	{
		if (empty($teams))
		{
			return $this;
		}
		if (is_array($teams))
		{
			$this->SB_teams = array_merge($this->SB_teams, $teams);
		}
		elseif (!empty($teams))
		{
			$this->SB_teams[] = $teams;
		}
		return $this;
	}

	/**
	 * Sets the user
	 * @param  array|string|int $users Array of user IDs or an ID
	 * @return Search_Model        Method Chaining
	 */
	public function users($users)
	{
		//Validate with user lock...
		if ($this->user_lock)
		{
			$this->apply_user_lock();
			return $this;
		}

		if (empty($users))
		{
			return $this;
		}
		if (is_array($users))
		{
			$this->SB_users = array_merge($this->SB_users, $users);
		}
		elseif (!empty($users))
		{
			$this->SB_users[] = $users;
		}
		return $this;
	}

	/**
	 * Sorts the results according to arguments
	 * @param  array $sort_array An array containing the parameter to sort by as the key 
	 *                           and the sorting direction as the value.
	 *                           Valid sorting parameters are:
	 *                           	- date
	 *                           	- time
	 *                           Valid Directions are 'asc' or 'desc',
	 *                           which are asceding and descending respectively
	 
	 *                           Example: array('date' => 'desc', 'time' => 'desc') 
	 *                           Sorts by date descending first then by time descending
	 * @return Search_model      Method Chaining
	 */
	public function sort($sort_array)
	{
		//Clear Defaul Sort
		$this->sort_array = array();

		foreach ($sort_array as $param => $direction)
		{
			//Validation//
			
			//Parameters
			switch (strtolower($param))
			{
				case 'date':
					$sort_parameter = 'log_date';
					break;
				case 'time':
					$sort_parameter = 'log_time';
					break;
				case 'id':
					$sort_parameter = 'log_id';
					break;
				default:
					$sort_parameter = 'log_date';
					break;
			}

			//Direction
			switch (strtolower($direction))
			{
				case 'asc':
					$sort_direction = 'ASC';
					break;
				case 'desc':
					$sort_direction = 'DESC';
					break;
				default:
					$sort_direction = 'DESC';
			}

			//Store the data
			$this->sort_array[$sort_parameter] = $sort_direction;
		}
		

		return $this;
	}

	/**
	 * Paginated the results by applying a limit and an offset
	 * @param  int $limit  How many results to get
	 * @param  int $offset The row offset when searching
	 * @return Search_Model         Method Chaining
	 */
	public function pagination($limit = 0, $offset = 0)
	{
		$this->pagination_limit = $limit;
		$this->pagination_offset = $offset;

		return $this;
	}

	/**
	 * Sets whether to lock search to current user
	 * @param boolean $value Lock user?	
	 * @return Search_Model         Method Chaining
	 */
	public function user_lock($value)
	{
		$this->user_lock = $value;
		return $this;
	}

	/**
	 * Runs the query based on the stored data
	 * @param boolean $reset Clears the stored data if TRUE
	 * @return object DB result object containing the matching logs.
	 */
	public function search($reset = TRUE)
	{
		$this->join_tables();
		$this->db->from('action_log');
		$this->column_select();
		
		$this->apply_filters();

		//Get number of rows while not yet paginated
		$this->unpaginated_rows = $this->db->count_all_results('', FALSE);

		//Apply Pagination
		$this->apply_pagination();

		//Set Debug info
		$this->set_debug();

		//Get search results
		$result = $this->db->get();

		if ($reset)
		{
			$this->reset();
		}

		return $result;
	}

	/**
	 * Applies the table column filters. (i.e all the 'where' and 'like' filters)
	 * @return void
	 */
	public function apply_filters()
	{
		//Lock users before setting 'where' for users
		$this->apply_user_lock();

		$this->apply_keywords();
		$this->apply_dates();
		$this->apply_projects();
		$this->apply_teams();
		$this->apply_types();
		$this->apply_users();
		$this->apply_sort();
	}

	/**
	 * Sets the model to only find data that belongs to the current user
	 * @return void 
	 */
	public function apply_user_lock()
	{
		if($this->user_lock)
		{
			$this->SB_users = $this->curr_user;
		}
	}

	/**
	 * Applies the keywords filters
	 * @return void
	 */
	public function apply_keywords()
	{
		if (empty($this->SB_keywords))
		{
			return;
		}

		switch ($this->SB_keyword_type) 
		{
			case 'any':
				foreach ($this->SB_keywords as $keyword) 
				{
					//Will be using 'OR' between keywords		
					$this->db->or_group_start();
					
					foreach ($this->SB_keywords_in as $column) 
					{
						$this->db->or_like($column, $keyword);
					}
					$this->db->group_end();

				}
				break;
			
			case 'all':
				foreach ($this->SB_keywords as $keyword) 
				{
					//Will be using 'AND' between keywords		
					$this->db->group_start();
					foreach ($this->SB_keywords_in as $column) 
					{
						$this->db->or_like($column, $keyword);
					}
					$this->db->group_end();

				}
				break;

			default:
				$this->error('Invalid Keyword Type. Use either "all" or "any"');
				break;
		}
	}

	/**
	 * Applies the date filters.
	 * @return void 
	 */
	public function apply_dates()
	{

		//If 'to' and 'from' dates are the same,
		if($this->SB_from_date == $this->SB_to_date && !empty($this->SB_from_date))
		{
			$this->db->where('log_date', (string) $this->SB_from_date);
			return;
		}

		if (!empty($this->SB_from_date))
		{
			$this->db->where('log_date >=', (string) $this->SB_from_date);
		}

		if (!empty($this->SB_to_date))
		{
			$this->db->where('log_date <=', (string) $this->SB_to_date);
		}


	}

	/**
	 * Applies the project filters.
	 * @return void 
	 */
	public function apply_projects()
	{
		if (!empty($this->SB_projects) && count($this->SB_projects) > 0)
		{
			$this->db->where_in('action_log.project_id', $this->SB_projects);
		}
	}

	/**
	 * Applies the action types filters
	 * @return void 
	 */
	public function apply_types()
	{
		if (!empty($this->SB_action_types))
		{
			$this->db->where_in('actions.type_id', $this->SB_action_types);
		}
	}

	/**
	 * Applies the team Filters
	 * @return void 
	 */
	public function apply_teams()
	{
		if (!empty($this->SB_teams))
		{
			$this->db->where_in('action_log.team_id', $this->SB_teams);
		}
	}

	/**
	 * Applies the user filters
	 * @return void 
	 */
	public function apply_users()
	{
		if (!empty($this->SB_users))
		{
			$this->db->where_in('action_log.user_id', $this->SB_users);
		}
	}

	/**
	 * Applies the sorting settings set
	 * @return void
	 */
	public function apply_sort()
	{
		foreach($this->sort_array as $param => $direction)
		{
			$this->db->order_by($param, $direction);
		}
	}

	/**
	 * Applies the pagination limit and offset
	 * @return void 
	 */
	public function apply_pagination()
	{
		$this->db->limit($this->pagination_limit, $this->pagination_offset);
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
			->join('users','users.user_id = action_log.user_id', 'left');

		return TRUE;
	}

	/**
	 * Calls the db functions to select the columns and give them a humanized name
	 * @return void 
	 */
	public function column_select()
	{
		$this->db->select(
			array(
				'CONCAT(first_name, " ", last_name) as Name', 
				'action_name as Action Name', 
				'type_name as Type', 
				'project_name as Project', 
				'team_name as Team', 
				'log_desc as Description', 
				'hours as Hours', 
				'log_date as Date', 
				'log_time as Time'
			)
		);
	}

	/**
	 * Exports the currently stored query data into a json string.
	 * @return string JSON containing the exported data
	 */
	public function export_query()
	{
		$to_export = array(
			'SB_keywords',
			'SB_keywords_in',
			'SB_keyword_type',
			'SB_from_date',
			'SB_to_date',
			'SB_action_types',
			'SB_projects',
			'SB_teams',
			'SB_users',
			'SB_null_teams',
			'SB_null_projects',
			'sort_array'
		);

		foreach ($to_export as $property_name)
		{
			$export[$property_name] = $this->{$property_name};
		}

		return json_encode($export);
	}

	/**
	 * Imports a JSONized search query
	 * @param  string $json The JSON string
	 * @return void       
	 */
	public function import_query($json)
	{
		if(empty($json))
		{
			$this->error('Import JSON is empty');
			return;
		}
		$import = json_decode($json);

		foreach ($import as $property_name => $value)
		{
			$this->{$property_name} = $value;
		}
	}

	/**
	 * Sets the debug data to aid in debugging
	 * Call before searching (i.e. calling search(), get_logs(), etc.)
	 * 	- Sets the following:
	 * 		Last SQL query - The query code itself
	 * 		export - The JSON export string
	 */
	public function set_debug()
	{
		//Remember last query string
		$this->debug['sql_query'] = $this->db->get_compiled_select('', FALSE);

		//Remember last export string
		$this->debug['export'] = $this->export_query();
	}

	/**
	 * Returns a string containing Debug Data
	 * Can just be echoed
	 * @return string Denug Data
	 */
	public function get_debug()
	{
		$string = '';
		foreach ($this->debug as $name => $content)
		{
			$string .= "<b>{$name}:</b> {$content} <br><br>";
		}
		return $string;
	}

	/**
	 * Resets the stored search query.
	 * @return void 
	 */
	public function reset()
	{
		$this->SB_keywords      = array();
		$this->keywords_in('all');
		$this->keywords_type('any');
		
		$this->SB_from_date     = NULL;
		$this->SB_to_date       = NULL;
		$this->SB_action_types  = array();
		$this->SB_projects      = array();
		$this->SB_teams         = array();
		$this->SB_users         = array();
		
		$this->SB_null_projects = TRUE;
		$this->SB_null_teams    = TRUE;
		
		$this->sort_array		= array('log_date' => 'DESC', 'log_time' => 'DESC');
	}
	
}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */