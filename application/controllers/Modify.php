<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modify Controller
 * =================
 * @author  Ryan Sandoval, June 2018
 *
 * Handles all database modification after the data has been created. 
 *
 * The user will be able select a table that they would like to modify.
 * This table is then displayed, which will have a column with an edit button
 * Upon clickin the search button, the user is directed to a modifcation form.
 *
 * Modification form
 * -----------------
 *
 * The form will have:
 * 	 text input boxes if the sql field's datatype is varchar or similar.
 * 	 checkboxes if the the sql field's datatype is boolean or similar.
 * 	 number input boxes if the sql field's datatype is int or similar.
 * 	 radio selection if the sql field's datatype is enum.
 * 
 */
class Modify extends CI_Controller {

	/**
	 * Constructor method for Modify Controller
	 *
	 * Loads all the necessary libraries, models, etc.
	 * Also handles if user is logged in or not.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('user_helper');
		
		$this->load->model('search_model');
		$this->load->model('modify_model');

		check_admin();
	}

	/**
	 * Main page for the Modify Controller
	 */
	public function index()
	{
		$data['header'] = array(
			'text' => 'Modify',
			'colour' => 'is-info');
		$data['title'] = 'Modify';
		$data['tables'] = ['actions', 'action_types', 'action_log', 'teams', 'projects', 'users'];

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('modify/tabs', $data);
		$this->load->view('modify/modify-selection', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Displays the table that the user has selected to modify. This table has a column that says edit,
	 * which allows the user to edit that certain row. 
	 * TODO: **The edit button should have a hyperlink that requests the edit form using a get request. 
	 * 		Validation of the user will happen when the form is submitted or recieved.
	 * @param  [type] $table [description]
	 * @return [type]        [description]
	 */
	public function modify_selection($table, $offset = 0)
	{
		//load the table helper class
		$this->load->helper('table_helper');
		
		$data = $this->modify_model->get_modify_table($table, $offset);
		$data['page_links']  = get_pagelinks($data, "Modify/table/{$table}");

		$data['header'] = array(
			'text' => "Modify ".humanize($table),
			'colour' => 'is-info');
		$data['title'] = "Modify ".humanize($table);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('modify/tabs', $data);
		$this->load->view('modify/view-table', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Controls and initializes the mofidy form for any table
	 * @param  string $table The table which will be modified
	 * @param  string $key   The primary key of the row to be modified
	 */
	public function modify_form($table, $key)
	{
		//Get field Data
		$field_data = $this->search_model->get_field_data($table);

		$this->form_validation->set_rules('modify', 'Modify', 'required'); //Modify button needs to be clicked

		//Get the validation rules for the form
		//Required form validations per table are set in the configurations
		$this->config->load('modify_config');
		$modify_rules = @$this->config->item('modify_rules')[$table]; //Error supressed

		if(!isset($modify_rules))
		{
			log_message('error', "The validation rules for $table could not be found. Setting all fields to required");
			foreach ($field_data as $field)
			{
				$this->form_validation->set_rules($field->name, humanize($field->name), 'required');
			}
		}
		else
		{
			foreach ($modify_rules as $field => $rule)
			{
				$this->form_validation->set_rules($field, humanize($field), $rule);
			}
		}

		if (!$this->form_validation->run())
		{
			$this->show_form($table, $key);
		}
		else
		{
			$update_data = array();

			foreach ($field_data as $field)
			{
				$update_data[$field->name] = $this->input->post($field->name, TRUE); //Gets the corrseponding updated values in the post array
			}

			//Update the table
			if ($this->modify_model->update($table, $update_data, $key))
			{
				//Sucess!
				
				$data['header'] = array(
					'text' => "Modify ".humanize($table),
					'colour' => 'is-info');
				$data['title'] = "Modify ".humanize($table);
				$data['table'] = $table;
				$data['key'] =$key;
				$foo = array(
						'table_data' => array_values($update_data),
						'heading' => array_keys($update_data)
					);

				//Make a table to display changes
				$this->load->helper('table_helper');
				$data['update_data_table'] = generate_table($foo = array(
						//Must Surround the data in an array first since Code Igniter tries to iterate through 'table_data'
						'table_data' => array(array_values($update_data)), 
						'heading' => array_map(function($x) {return humanize($x);}, array_keys($update_data))
					)
				);

				$this->load->view('templates/header', $data);
				$this->load->view('templates/hero-head', $data);
				$this->load->view('templates/navbar', $data);
				$this->load->view('modify/tabs', $data);
				$this->load->view('modify/success', $data);
				$this->load->view('templates/footer', $data);
			}
			else
			{
				//Failure
				show_error('The item was not updated due to an unexpected error.');
			}
		}
	}

	/**
	 * Creates the Form Fields to manage rows in a table.
	 * Used in the general table modify system
	 * @param  string $table The table whos row will be modfied
	 * @param  string $key   The primary key of the row to be modified
	 */
	public function show_form($table, $key)
	{
		//Load resources
		$this->load->config('modify_config');

		//First get column data. This will be used to format the form.
		$columns = $this->search_model->get_field_data($table, TRUE);

		//Get primary key name
		$primary_key = $this->modify_model->get_primary_key_name($table);

		//Get the current values of the row.
		//Note: To get the current value of a cloumn in the selected row, use $query->$column_name
		$query = $this->search_model->get_items($table, array($primary_key => $key))[0];

		foreach ($columns as $index => $column)
		{
			$data['fields'][$index] = new stdClass(); //A Class will be used to hold the data of a field
			$field =& $data['fields'][$index];

			$field->name = $column->name;

			//Conditions for certain fields. i.e. Passwords, relational ids (e.g. team_id)
			switch ($column->name)
			{
				case $primary_key: //Prevent Direct edit of password
				case 'password': //Prevent chaning of primary key
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

		//Additional Data
		$data['header'] = array(
			'text' => 'Modify',
			'colour' => 'is-info');
		$data['title'] = 'Modify';
		$data['table']  = $table;
		$data['key'] = $key;

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('modify/tabs', $data);
		$this->load->view('modify/form', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Multifunctional Team Management Method
	 * --------------------------------------
	 * 
	 * Displays a team selection page so that the user can manage the teams if no $team parameter is
	 * not present in the URI
	 *
	 * If the $team parameter is defined in the URI, then it takes the user to the management screen
	 * 
	 * @param  string $team The team id to manage
	 */
	public function manage_teams($team_id = NULL)
	{
		if (!isset($team_id)) //Then we are selecting the team first
		{
			$data['header'] = array(
				'text' => 'Manage Teams',
				'colour' => 'is-info');
			$data['title'] = 'Manage Teams';

			$data['teams'] = $this->search_model->get_user_teams($this->session->user_id);
			$data['team_modify_links'] = array_map(function($x)
				{
					return anchor("manage_teams/{$x->team_id}", "Manage", 'class="button is-info"');
				},
				$data['teams']);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('modify/tabs', $data);
			$this->load->view('modify/manage_teams/teams-selection', $data);
			$this->load->view('templates/footer', $data);
		}
		else
		{
			//Display Team management
			$data = $this->search_model->get_team_info($team_id);
			echo json_encode($data);
			$data['header'] = array(
				'text' => 'Manage Teams',
				'colour' => 'is-info');
			$data['title'] = 'Manage Team';

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('modify/tabs', $data);
			$this->load->view('modify/manage_teams/modify_team', $data);
			$this->load->view('templates/footer', $data);
		}
	}

	public function add_users($team)
	{
		$data['header'] = array(
			'text' => 'Add Users',
			'colour' => 'is-info');
		$data['title'] = 'Manage Team Users';
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('modify/tabs', $data);
		$this->load->view('templates/footer', $data);
	}

	public function remove_users($team)
	{
		$data['header'] = array(
			'text' => 'Remove Users',
			'colour' => 'is-info');
		$data['title'] = 'Manage Team Users';
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/footer', $data);
	}
}

/* End of file Modify.php */
/* Location: ./application/controllers/Modify.php */