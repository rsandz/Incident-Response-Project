<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model
 * ==========
 * @author Ryan Sandoval
 *
 * The base model contains functions that would be useful
 * in many database interactions. Other models should extend
 * this.
 */
class MY_Model extends CI_Model {

	/** @var string Holds the most previous error */
	protected $errors = array();

	/**
	 * Constructor for the base model
	 *
	 * Loads necessary resources.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->db->flush_cache();
	}

	/**
	 *	Querries if data provided is already in table.
	 *
	 *	@param string $table Table to check where data is in
	 *	@param array  $data  Associative array with column-value pairs
	 * 
	 *	@return boolean The Data if in database. False if not.
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
	 * Gets the name of the primary key field/column for a specified table
	 * @param  string $table The table name
	 * @return string        The name of the Primary key or False if ther is none
	 */
	public function get_primary_key_name($table)
	{
		$query = $this->db->field_data($table, TRUE); //Get all columns

		foreach ($query as $column)
		{
			if ($column->primary_key)
			{
				return $column->name;
			}
		}

		return FALSE; //If no primary key
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
	 * Get the last error that occured in the database class.
	 * @return array Contains message and error number(code)
	 */
	public function get_db_errors()
	{
		return $this->db->error();
		$errors['model'] = $this->error;
	}

	/**
	 * Get last error that occured in the model
	 * @return string Error
	 */
	public function get_model_errors()
	{
		$string = '';
		foreach ($this->errors as $index => $error_msg)
		{
			$string .= "Error #{$index}: {$error_msg}<br>";
		}
		return $string;
	}

	/**
	 * Gets the most recent error that has occured.
	 * @return array Array containing:
	 *               - db
	 *               	- error number and message
	 *               - model
	 *               	- error message
	 */
	public function get_errors()
	{
		return array('db' => $this->get_db_errors(), 'model' => $this->get_model_errors());
	}

	/**
	 * Adds a new error to the error array
	 * @param  string $new_error Error message
	 * @return void            
	 */
	public function error($new_error)
	{
		$this->errors[] = $new_error;
		log_message('error', $new_error);
	}

}

/* End of file Base_model.php */
/* Location: ./application/models/Base_model.php */