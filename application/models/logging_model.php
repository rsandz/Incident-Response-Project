<?php

class logging_model extends CI_model {
	/**
	 * Logging Model Initialization
	 */
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->library('session');
	}
	/**
	 *	Logs an action in action log table
	 *
	 * @return True on Sucess. False on failure
	 *
	 */
	public function log_action() { 
		$data = array(
			'action_id' => $this->input->post('action'),
			'log_desc' => $this->input->post('desc'),
			'log_date' => $this->input->post('date'),
			'log_time' => $this->input->post('time'),
			'project_id' => $this->input->post('project'),
			'team_id' => $this->input->post('team'),
			'user_id' => $this->session->user_id
			);
		return $this->db->insert('action_log', $data);
	}

	/**
	 * Logs an item into the database
	 * 
	 * @param  [string] $table      [Table name]
	 * @param  [object] $field_data [Field data]
	 * @return [Mixed]             [Returns error string if failed]
	 */
	public function log_item($table, $field_data)
	{
		$data = array();
		foreach ($field_data as $field)
		{
			$data[$field->name] = $this->input->post($field->name);
		}

		if ($this->data_exists($table, $data))
		{
			return 'Data exists already. Aborting.';
		}
		else
		{
			return $this->db->insert($table, $data);
		}
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
			return TRUE;

		}
		else
		{
			return FALSE;
		}
	}

	/**
	 *	Returns all the data in a database table
	 *
	 *	@param string $table Database table to grab data from.
	 *
	 *	@return array An array of the table's rows
	 */

	public function get_info($table) { 
		
		$query = $this->db->get($table);
		return $query->result_array();
	}

	/**
	 *	Gets and Returns Table Fields Data
	 *
	 *	@param $table Table name to get field data.
	 * 	
	 *	@return object Field data object as per Code Ignitor's structure. Also includes enum values for enum type columns.
	 */

	public function get_field_data($table)
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

				if (stripos($field->name, 'id'))
				{
					unset($field_data[$key]);
				}
			}

			return $field_data;
		}
		else
		{
			return NULL;
		}
	}

	
	/**
	 * Gets the Enumeartion Values for a given field in a table
	 * 
	 * @param  string $table Table name in database
	 * @param  string $field Field name in Database
	 * @return array         Returns
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
	 * Get entries from the action log
	 * @param  int|null $limit       Limits items gotten from entries
	 * @param  Boolean  $filter_user True if to filter by user ID
	 * @return array 				 Contains Table and Number of Rows                
	 */	
	public function get_entries_table( int $limit = NULL, $filter_user = TRUE, int $offset = 0) {
		//load required libraries
		if ($this->load->is_loaded('table') == FALSE) $this->load->library('table');

		$this->db
			->select('action_name, project_name, log_desc, log_date, log_time')
			->join('actions','actions.action_id = action_log.action_id')
			->join('projects','projects.project_id = action_log.project_id')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');;

		if ($filter_user)
		{
			$this->db->where('user_id', $this->session->user_id);
			$this->table->set_heading(array('Action Name','Project', 'Log Description', 'Log Date', 'Log Time'));
		}
		else
		{
			$this->db->select('name')
			->join('users','action_log.user_id=users.user_id');
			$this->table->set_heading(array('Action Name','Project', 'Log Description', 'Log Date', 'Log Time', 'Name'));
		}
		
		$prev_entries = $this->db->get('action_log');
		$data['total_rows'] = $prev_entries->num_rows();

		$prev_entries_array = array_slice($prev_entries->result_array(), $offset, $limit);

		
		///////////////////////////
		//TABLE AESTHETICS SETUP //
		///////////////////////////

		$template = array(
        'table_open'            => '<table class="table is-striped is-fullwidth">',

        'thead_open'            => '<thead class="thead">',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr class="tr">',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th class="th">',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody class="tbody">',
		'tbody_close'		 	=> '</tbody>',

        'row_start'             => '<tr class="tr">',
        'row_end'               => '</tr>',
        'cell_start'            => '<td class="td">',
        'cell_end'              => '</td>',

        'table_close'           => '</table>'
		);

		$this->table->set_template($template);

		$data['num_rows'] = $prev_entries->num_rows();
		$data['table'] = $this->table->generate($prev_entries_array);
		return $data;
	}

	




}