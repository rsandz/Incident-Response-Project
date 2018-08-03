<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ryan Sandoval
 * 
 * Extends the CI_Table library.
 */
class MY_Table extends CI_Table {

	/**
	 * Gets the current amount of rows in the table.
	 */
	public function num_rows()
	{
		return count($this->rows);
	}

	/**
	 * Custom generate method. 
	 * 
	 * Takes template from the 'view_tables' config file
	 * Can also take headings in as an argument
	 * @param  mixed $table_data The table data that will be passed into the normal generate() method
	 * 							 Examples: 2D array. CI_db_result
	 * @param  array $heading    The custom headings that you would like to use.
	 * @return string            The HTML string of the table
	 */
	public function my_generate($table_data, $heading = NULL)
	{
		$CI =& get_instance();

		//Get Default Table template
		$CI->load->config('view_tables', 'view_tables');
		$this->set_template($CI->config->item('default_template') ?: $CI->config->item('default_template', 'view_tables'));
		if (isset($heading))
		{
			//Overwrite the current heading
			$this->set_heading(array_map(function($x) {return humanize($x);}, $heading));
		}

		//If the heading is empty, but table data is a db result object, table library
		//will automatically get the heading from the field names

		$table_html = $this->generate($table_data);
		
		return $table_html;
	}

	/**
	 * Generates the table html string along with other table data.
	 * This is useful if you're creating pagination for this data.
	 * 
	 * @param  mixed $table_data The table data that will be passed into the normal generate() method
	 * @param  array $heading    The custom headings that you would like to use.
	 * @return array 			 An array containing the following:
	 *                      		'table_html' => The table as an html string
	 *                      		'num_rows'	 => The amount of rows that the table has
	 */	
	public function my_generate_plus($table_data, $heading = NULL)
	{
		$data['table'] = $this->my_generate($table_data, $heading);
		$data['num_rows'] = $this->num_rows();

		return $data;
	}

	/**
	 * Automatically gets and sets table headings from the config file.
	 * Attempt to get the hadings from the following sources (in this order):
	 * 	1. The 'view_tables' config file (['headings'] index)
	 * 	2. The 'view_tables' config file (['select'] index)
	 *
	 * If the above fails, returns False. Otherwise True
	 * 
	 * @param  string $table The table name of which to get the headers for.
	 * @return array         The array containing the headers.
	 */		
	public function heading_from_config($table, $humanize = TRUE)
	{
		$CI =& get_instance();

		$CI->config->load('view_tables');
		$CI->load->helper('inflector');

		if (isset($CI->config->item($table, 'view_tables')['headings']))
		{
			$headings = $CI->config->item($table, 'view_tables')['headings'];
		}
		elseif (isset($CI->config->item($table, 'view_tables')['select']))
		{
			$headings = $CI->config->item($table, 'view_tables')['select'];
		}
		else
		{
			return FALSE;
		}

		if ($humanize)
		{
			foreach ($headings as $index => $heading) 
			{
				$headings[$index] = humanize($heading);
			}
		}

		$this->set_heading($headings);
		return TRUE;
	}
}

/* End of file MY_table.php */
/* Location: ./application/libraries/MY_table.php */