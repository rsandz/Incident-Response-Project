<?php

class logging_model extends CI_model {
	
	/**
	 * Logging Model Initialization
	 */
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->model('search_model');

		
	}
	/**
	 * Logs an action in action log table. Can log what was in a form, or whatever a
	 * user created. 
	 * ----------------------------------------------------------------------------------
	 * For a user 'create' log, the created entity (i.e. 'User') must be given and 
	 * the method will first check to see if the action (i.e. "Create User" action) is 
	 * in the action table.
	 * It will then proceed to log the action in the action log.
	 * 
	 * @param string $log_type What to log. 
	 *                         (i.e. 'form' for action log and 'create' for create logs)
	 * @param string $data If the log type is form, this will be the log form data. 
	 *                     If the log type is create, this will be the create type (i.e. User, Team, Project, etc.)
	 * @param string $name FOr use with the create log type. This is the name of the item created. i.e. If you create a user named 'Bob'
	 *                     then 'Bob' should be passed in here
	 * @return True on Sucess. False on failure
	 *
	 */	
	public function log_action($log_type, $data = NULL, $name = NULL) { 
		if ($log_type == 'form')
		{
			return $this->db->insert('action_log', $data);
		}
		elseif($log_type == 'create')
		{	
			//See if there is a 'create' action type in the first place (i.e. Action Type Name = create)
			if(!$this->search_model->data_exists('action_types', array('type_name' => 'create')))
			{
				$insert_data = array(
					'type_name' => 'Create',
					'is_active' => 0
				);

				$this->log_item('action_types', $insert_data);
			}

			$type_id = $this->search_model->get_items('action_types', array('type_name' => 'create'))[0]->type_id;

			//See if create action is in the actions table in the first place (Action name = Create ****)
			if (!$this->search_model->data_exists('actions', array('action_name' => 'Created '.$data)))
			{
				$insert_data = array(
					'action_name' => 'Created '.$data,
					'type_id' => $type_id,
					'action_desc' => 'Created '.$data.' using the create page.',
					'project_id' => NULL,
					'is_active' => FALSE
				);

				$this->log_item('actions', $insert_data);
			}

			$insert_data = array(
				'action_id' => $this->search_model->get_items('actions', array('action_name' => 'Created '.$data))[0]->action_id,
				'log_date' => date('Y-m-d'),
				'log_time' => date('H:i'),
				'log_desc' => 'Inserted '.$name.' into '.$data.' table.',
				'team_id' => NULL,
				'user_id' => $this->session->user_id
				);
			return $this->db->insert('action_log', $insert_data);
		}
		else
		{
			show_error('Invalid Log Type');
			return FALSE; //Invalid Log type
		}
	}

	/**
	 * Logs an item into the database
	 * 
	 * @param  string $table      Table name
	 * @param  Array $data Data to insert
	 * @return Mixed             Returns an error string if something went wrong
	 */
	public function log_item($table, $data, $update = FALSE)
	{
		if ($this->search_model->data_exists($table, $data))
		{
			$existing_data = $this->search_model->data_exists($table, $data);

			if ($update)
			{
				$field_data = $this->get_field_data();
				foreach ($field_data as $field)
				{
					if ($field->primary_key === 1) 
					{
						$id_name = $field->name;
					}
				}
				$this->db->update($table, $data, $id_name.' = '.$data[$id_name]);
			}
			else
			{
			return 'Data exists already. Aborting.';
			}
		}
		else
		{
			return $this->db->insert($table, $data);
		}
	}


	/**
	 * Get entries from the action log
	 * @param  int  $offset 		 Offset for pagination
	 * @return array 				 Contains Table and Number of Rows                
	 */	
	public function get_my_entries_table(int $offset = 0) {
		//load required libraries
		if ($this->load->is_loaded('table') == FALSE) $this->load->library('table');
		$this->load->helper('table_helper');
		$this->load->config('appconfig');

		$this->db
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->where('user_id', $this->session->user_id);

		$this->search_model->execute_table_filters('prev_entries'); 
		//Prev Entries is a config field. The search model will try to find it in the table config.

		$query = $this->db->get('action_log');
		$data['table_data'] = array_slice($query->result_array(), $offset, $this->config->item('per_page'));

		$data['num_rows'] = $query->num_rows();
		$data['heading'] = $this->search_model->get_table_headings('prev_entries');

		$data['table'] = generate_table($data);
		return $data;
	}


}