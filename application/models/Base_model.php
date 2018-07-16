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
class Base_Model extends MY_Model {

	/**
	 * Constructor for the base model
	 *
	 * Loads necessary resources.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
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

}

/* End of file Base_model.php */
/* Location: ./application/models/Base_model.php */