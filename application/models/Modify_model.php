<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modify_model extends CI_Model {

	public function __construct()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->helper('inflector');
		$this->load->library('table');

		$this->load->model('logging_model');
		$this->load->model('search_model');
		
	}

	public function get_modify_table($table, $offset = 0)
	{
		$this->load->helper('table_helper');
		$per_page  =$this->config->item('per_page');

		//Conditions and data formatting for certain tables
		
		//Format the table
		$this->search_model->execute_table_filters($table); //Applies table filters as stated in view_tables.php
		$data['heading'] = $this->search_model->get_table_headings($table);
		array_push($data['heading'], ''); //Add a column for edit button

		//Get Table DATA
		$query = $this->search_model->get_items_raw($table);
		$data['table_data'] = array_slice($query->result_array(), $offset, $per_page); //Offset for pagination

		//Censoring Password Hashes - See configuration for disabling this
		if (in_array('Password', $data['heading']) && !$this->config->item('show_hashes')) 
		{
			foreach ($data['table_data'] as &$row)
			{
				$row['password'] = '***********';
			}
		}

		//In order to modify the selected row, the primary key must be acquired. 
		$data['primary_key'] = $this->get_primary_key_name($table);

		//Push an edit button onto each row
		foreach ($data['table_data'] as &$row)
		{
			$row = array_merge($row, array('Edit' => anchor("Modify/{$table}/{$row[$data['primary_key']]}", 'Edit')));
		}

		//Create The table
		$data['table'] = generate_table($data);
		$data['num_rows'] = $query->num_rows();
		$data['table_name'] = humanize($table);

		return $data;
	}

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

	public function update($table, $data, $key)
	{
		$primary_key = $this->get_primary_key_name();
		//Update the Table
		return $this->db->where($primary_key, $key)
			->update($able, $data);
	}
}

/* End of file Modify_model.php */
/* Location: ./application/models/Modify_model.php */