<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MODIFY MODEL
 * ============
 * @author Ryan Sandoval, July 03, 2018
 *
 * This model handles database interactions/logic that requires modification
 * of inserted data. Examples the required operations on inserted data include:
 * 	- Direct Table modification via the admin modify table
 * 	- Team Addition/Removal
 */
class Modify_model extends MY_Model {

	/**
	 * Constructor for the modify model.
	 *
	 * Loads the resources and libraries that are used in the model
	 */
	public function __construct()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->library('table');

		$this->load->model('logging_model');
		$this->load->model('get_model');
		
		
	}

	/**
	* Creates the table (as HTML string) that the user should see when they are modifying a database table.
	*
	* Example: Providing `actions` as the table as angument will result in this function returning
	* 			a table with the columns in the `action` table along with a column that says edit.
	* @param  string  $table  The name of the table of which to modify
	* @param  integer $offset Offset for pagination. If '10' was the argument, then this table's first item
	*                         will be the 10th item in the database.
	* @return array          An associative array containing:
	*                           'primary_key' => The name of the primary key of the table
	*                           'table'       => The HTML string for the table
	*                           'num_rows'    => Number of rows in the table
	*                           'table_name'  => Humanized version of the table name
	*/
	public function get_modify_table($table, $offset = 0)
	{
		$per_page  = $this->config->item('per_page');

		//Conditions and data formatting for certain tables
		//Get Table DATA
		$data['primary_key'] = $this->get_primary_key_name($table);
		$query = $this->db->get($table);
		$table_data = array_slice($query->result_array(), $offset, $per_page); //Offset for pagination

		//Censoring Password Hashes - See configuration 'appconfig' for disabling this
		if (!$this->config->item('show_hashes') && $this->db->field_exists('password', $table)) 
		{
			foreach ($table_data as &$row)
			{
				$row['password'] = '***********';
			}
		}

		//Format the table
		$heading = array_map(function($x) {return humanize($x);}, $query->list_fields());
		array_push($heading, ''); //Add a column for edit button

		//Push an edit button onto each row
		foreach ($table_data as &$row)
		{
			$row = array_merge($row, array('Edit' => anchor("modify/{$table}/{$row[$data['primary_key']]}", 'Edit')));
		}

		//Create The table
		$this->load->library('table');
		$data['table'] = $this->table->my_generate($table_data, $heading);
		$data['num_rows'] = $query->num_rows();
		$data['table_name'] = humanize($table);

		return $data;
	}

	/**
	 * Creates the form (as HTML string) that the user should see when editing a row.
	 *
	 * The method will automatically create the proper input type for each column
	 * @param  string $table The name of the table that you want a form for.
	 * @param  int $key   The primary key of the row you want to edit. Used for populating the input fields
	 *                    with the current values
	 * @return Array     An array of objects containing the field data.
	 *                      Each of the objects in the array contains the properties:
	 *                      	name - The name of the field/column. 
	 *                      	form - The HTML string for the input field
	 */
	public function get_modify_form($table, $key)
	{
		//Load resources
		$this->load->config('modify_config');
		$this->load->model('search_model');

		//First get column data. This will be used to format the form.
		$columns = $this->search_model->get_field_data($table, TRUE);

		//Get primary key name
		$primary_key = $this->get_primary_key_name($table);

		//Get the current values of the row.
		//Note: To get the current value of a cloumn in the selected row, use $query->$column_name
		$query = $this->db->where($primary_key, $key)->get($table)->row();

		foreach ($columns as $index => $column)
		{
			$fields[$index]= new stdClass(); //A Class will be used to hold the data of a field
			$field =& $fields[$index];

			$field->name = $column->name;

			//Now create the correct form
			
			//Conditions for certain fields. i.e. Passwords, relational ids (e.g. team_id)
			switch ($column->name)
			{
				case $primary_key: //Prevent chagning of primary key
				case 'password': //Prevent Direct edit of password
					$field->form = form_input($column->name, $query->{$column->name}, 'class="input is-light" readonly'); 
					continue(2);
				default:
					continue;
			}

			if (array_key_exists($column->name, $this->config->item('dropdown_config'))) //If the current field is a dropdown
			{
				$config = $this->config->item('dropdown_config')[$column->name];

				//Get all action types and put into a dropdown
				$dropdown_data = $this->search_model->get_items($config['table']);

				$options = array();
				foreach ($dropdown_data as $item)
				{
					$options[$item->{$column->name}] = $item->{$config['text_column']};
				}
				$field->form = '<div class="select">';
				$field->form .= form_dropdown($column->name, $options, $query->{$column->name}, 'class="select"');
				$field->form .= '</div>';
				continue;
			}

			switch ($column->type) 
			{
				case 'varchar':
					$field->form = form_input($column->name, $query->{$column->name}, 'class="input"');
					break;
				case 'binary':
					$field->form = '<div class="field is-horizontal">';
					$field->form .= '<label class="radio">';
					$field->form .= form_radio($column->name, 1,  $query->{$column->name} == 1, 'class="radio"');
					$field->form .= 'True</label>';
					$field->form .= '<label class="radio">';
					$field->form .= form_radio($column->name, 0,  $query->{$column->name} != 1, 'class="radio"');
					$field->form .= 'False</label>';
					$field->form .= '</div>';

					break;
				case 'enum': 
					//This mess of a code just makes every enumerate value in a field display as  radio buttons
					$field->form = '<div class="field is-horizontal">';
					foreach ($column->enum_vals as $enum_val)
					{
						$field->form .= '<label class="radio">';
						$field->form .= form_radio($column->name, $enum_val, $query->{$column->name} == $enum_val,'class="radio"');
						$field->form .= humanize($enum_val).'</label>';
					}
					$field->form .= '</div>';
					break;
				case 'smallint':
				case 'int':
					$field->form = "<input class='input' value='{$query->{$column->name}}' type='number' name='$column->name'>";
					break;
				case 'date':
					$field->form = "<input class='input' value='{$query->{$column->name}}' type='date' name='$column->name'>";
					break;
				case 'time':
					$field->form = "<input class='input' value='{$query->{$column->name}}' type='time' name='$column->name'>";
					break;
				default:
					$field->form = form_input($column->name, 'error', 'class="input"');
					break;
			}
		}

		return $fields;
	}

	/**
	 * Utility class that gets the name of the primary key of a table
	 * @param  string $table The name of the table that you want a primary key name from
	 * @return strinf        The name of the primary key
	 */
	public function get_primary_key_name($table)
	{ 
		$query = $this->search_model->get_field_data($table, TRUE); //Get all columns

		foreach ($query as $column)
		{
			if ($column->primary_key)
			{
				return $column->name;
			}
		}

		show_error('Selected error does not have a primary key. Modification is not allowed.');
	}

	/**
	 * Updates the table based on the given arguments
	 * @param  string $table The name of the table to update
	 * @param  array $data  Associative array of data
	 * @param  int $key   The primary key integer. The method will automatically get
	 *                    the primary key name using get_primary_key_name()
	 * @return boolean        True if Sucessfule
	 */
	public function update($table, $data, $key)
	{
		$primary_key = $this->get_primary_key_name($table);
		//Update the Table
		return $this->db->where($primary_key, $key)
			->update($table, $data);
	}

	/**
	 * Add the user_id along with the team_id to the user_teams table,
	 * thus adding the user to the team.
	 * @param int $team_id The team id that the user will be added to.
	 * @param int $user_id The user id that will be added to the team.
	 *
	 * @return boolean True if sucessful. False if not
	 */
	public function add_to_team($team_id, $user_id)
	{
		//Get user and team name
		$query = $this->db->where('team_id', $team_id)->get('teams');
		$team_name = $query->row()->team_name;

		$query = $this->get_model->get_users($user_id);
		$user_name = $query[0]->name;

		//Insert to log that current user is adding a user to team
		$this->logging_model->log_team_action($team_name, $user_name, 'add');

		//Add to the user to team
		$insert_data = array(
			'user_id' => $user_id,
			'team_id' => $team_id
		);
		//Check Data Exists first
		if (!$this->search_model->data_exists('user_teams', $insert_data))
		{
			$query = $this->db->insert('user_teams', $insert_data);
			if (!$query)
			{
				return FALSE;
			}
		}

		return TRUE; //If all ran well
	}

	/**
	 * Removes the user from the selected team by removing them from 
	 * the user_teams table in the database.
	 * @param  int $team_id Team id where the user is to be removed
	 * @param  int $user_id User id to remove frm the team
	 * @return boolean          True if sucessful. False if not
	 */
	public function remove_from_team($team_id, $user_id)
	{
		//Get user and team name
		$query = $this->db->where('team_id', $team_id)->get('teams');
		$team_name = $query->row()->team_name;

		$query = $this->db->where('user_id', $user_id)->get('users');
		$user_name = $query->row()->name;

		//Insert to log that current user is removing a user from a team
		$this->logging_model->log_team_action($team_name, $user_name, 'remove');

		//Remove the user from the team
		$delete_data = array(
			'user_id' => $user_id,
			'team_id' => $team_id
		);

		//Check Data Exists first
		if ($this->search_model->data_exists('user_teams', $delete_data))
		{
			$query =  $this->db->delete('user_teams', $delete_data);
			if (!$query)
			{
				return FALSE;
			}
		}
		
		return TRUE; //If all ran well
	}
}

/* End of file Modify_model.php */
/* Location: ./application/models/Modify_model.php */