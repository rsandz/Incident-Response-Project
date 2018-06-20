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
		$data['tables'] = ['actions', 'action_types', 'action_logs', 'teams', 'projects', 'users'];

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
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
	public function modify_selection($table)
	{
		//load the table helper class
		$this->load->helper('table_helper');
		
		$data = $this->modify_model->get_modify_table($table, 0);

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

	public function modify_form($table, $key)
	{
		$this->form_validation->set_rules('modify', 'Modify', 'required'); //Modify button needs to be clicked

		//Required form validations per table are set in the configurations TODO: ADD CONFIG

		if (!$this->form_validation->run())
		{
			$this->show_form($table, $key);
			echo 'Validation fail';
		}
		else
		{
			echo json_encode($_POST);
			//Get the data
			$field_data = $this->search_model->get_field_data($table);
			$insert_data = array();

			foreach ($field_data as $field)
			{
				$insert_data[$field->name] = $this->input->post($field->name, TRUE); //Gets the corrseponding updated values in the post array
			}
			echo '<br>insert: '.json_encode($insert_data);

			die();
			//Update the table
			$this->search_model->update();
		}
	}

	public function show_form($table, $key)
	{
		//First get column data. This will be used to format the form.
		$columns = $this->search_model->get_field_data($table, TRUE);

		//Get primary key name
		$primary_key = $this->modify_model->get_primary_key_name($table);

		//Get the current values of the row.
		$query = $this->search_model->get_items($table, array($primary_key => $key))[0];
		echo json_encode($query);

		foreach ($columns as $index => $column)
		{
			$data['fields'][$index] = new stdClass(); //A Class will be used to hold the data of a field
			$field =& $data['fields'][$index];

			$field->name = $column->name;

			//Conditions for certain fields. i.e. Passwords
			
			switch ($column->name)
			{
				case $primary_key: //Prevent Direct edit of password
				case 'password': //Prevent chaning of primary key
					$field->form = form_input($column->name, $query->{$column->name}, 'class="input" disabled'); 
					continue(2);
				default:
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
					$field->form = 'enum';
					break;
				case 'int':
					$field->form = "<input class='input' value='{$query->{$column->name}} '>";
				default:
					$field->form = form_input($column->name, 1, 'class="input"');
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
		$this->load->view('modify/form', $data);
		$this->load->view('templates/footer', $data);
	}

}

/* End of file Modify.php */
/* Location: ./application/controllers/Modify.php */