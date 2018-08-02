<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modify Controller
 * =================
 * @author  Ryan Sandoval, June 2018
 *
 * Handles admin database modification after the data has been created. 
 *
 * The admin will be able select a table that they would like to modify.
 * This table is then displayed, which will have a column with an edit button
 * Upon clicking the search button, the user is directed to a modifcation form.
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
class Modify extends MY_Controller {

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
		$this->load->library('log_builder', NULL, 'lb');
		$this->load->helper('form');

		$this->load->model('modify_model');

		$this->authentication->check_admin(); //Must be admin to acess admin modify tables
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
		$this->load->view('admin/tabs');
		$this->load->view('modify/modify-selection', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Displays the table that the user has selected to modify. This table has a column that says edit,
	 * which allows the user to edit that certain row. 
	 * @param  int $table The Table id of the table to display
	 */
	public function modify_selection($table, $offset = 0)
	{
		$this->load->library('pagination');

		$data = $this->modify_model->get_modify_table($table, $offset);
		$data['page_links']  = $this->pagination->my_create_links($data['num_rows'], "Modify/table/{$table}");

		$data['header'] = array(
			'text' => "Modify ".humanize($table),
			'colour' => 'is-info');
		$data['title'] = "Modify ".humanize($table);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
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
		$this->config->load('modify_config');

		//Get field Data (Columns of the table)
		$field_data = $this->modify_model->get_field_data($table, TRUE);

		$this->form_validation->set_rules('modify', 'Modify', 'required'); //Modify button needs to be clicked

		//Get the validation rules for the form (Located in Config) 
		$modify_rules = @$this->config->item('modify_rules')[$table];
		if(!isset($modify_rules))
		{
			log_message('error', "The validation rules for {$table} could not be found. Setting all fields to required");
			foreach ($field_data as $field)
			{
				$this->form_validation->set_rules($field->name, humanize($field->name), 'required');
			}
		}
		else
		{
			//Set the rules if found
			foreach ($modify_rules as $field => $rule)
			{
				$this->form_validation->set_rules($field, humanize($field), $rule);
			}
		}

		if (!$this->form_validation->run())
		{
			//Validation Failed
			$this->show_form($table, $key);
		}
		else
		{
			$update_data = array();

			foreach ($field_data as $field)
			{
				$update_data[$field->name] = $this->input->post($field->name, TRUE); //Gets the corrseponding updated values in the post array
				if ($update_data[$field->name] == '') $update_data[$field->name] = NULL;
			}

			//Update the table
			if ($this->modify_model->update($table, $update_data, $key))
			{
				//Success!
				
				//Log it
				$this->lb
					->sys_action('Modification')
					->date('now')
					->desc("Modified Item #{$key} on Table `{$table}`")
					->log();

				$data['title'] = "Modify ".humanize($table);

				//Make a table to display changes
				$this->load->library('table');
				$update_table = $this->table->my_generate(
						array(array_values($update_data)), //2D Array. An array of columns (update_data) within array of rows;
						array_map(function($x) {return humanize($x);}, array_keys($update_data))
					);

				//Make the Success Page
				$data['success_msg'] = "Item #{$key} in Table `{$table}` has been updated with the following:" ;
				$data['success_body'] = $update_table;
				$data['success_back_url'] = site_url('Modify/table/'.$table);

				$this->load->view('templates/header', $data);
				$this->load->view('templates/hero-head', $data);
				$this->load->view('templates/navbar', $data);
				$this->load->view('templates/success', $data);
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
		
		$data['fields'] = $this->modify_model->get_modify_form($table, $key);

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
		$this->load->view('modify/form', $data);
		$this->load->view('templates/footer', $data);
	}
}

/* End of file Modify.php */
/* Location: ./application/controllers/Modify.php */