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
	 * @param string $action For use with 'create logging'. Determnes what item was created.
	 *                       (i.e. User, action, Project)
	 * @return True on Sucess. False on failure
	 *
	 */	
	public function log_action($log_type, $action = NULL) { 
		if ($log_type == 'form')
		{
			$data = array(
				'action_id' => $this->input->post('action', TRUE),
				'log_desc'  => $this->input->post('desc', TRUE),
				'log_date'  => $this->input->post('date', TRUE),
				'log_time'  => $this->input->post('time', TRUE),
				'team_id'   => $this->input->post('team', TRUE),
				'user_id'   => $this->session->user_id
				);
			return $this->db->insert('action_log', $data);
		}
		elseif($log_type == 'create')
		{	
			//See if there is a 'create' action type in the first place
			if(!$this->search_model->data_exists('action_types', array('type_name' => 'create')))
			{
				$insert_data = array(
					'type_name' => 'Create',
					'is_active' => 0
				);

				$this->log_item('action_types', $insert_data);
			}

			$type_id = $this->search_model->get_items('action_types', array('type_name' => 'create'))[0]->type_id;

			//See if create action is in the actions table in the first place
			if (!$this->search_model->data_exists('actions', array('action_name' => 'Created '.$action)))
			{
				$insert_data = array(
					'action_name' => 'Created '.$action,
					'type_id' => $type_id,
					'action_desc' => 'Created '.$action.' using the create page.',
					'project_id' => NULL,
					'is_active' => FALSE
				);

				$this->log_item('actions', $insert_data);
			}

			$data = array(
				'action_id' => $this->search_model->get_items('actions', array('action_name' => 'Created '.$action))[0]->action_id,
				'log_date' => date('Y-m-d'),
				'log_time' => date('H:i'),
				'team_id' => NULL,
				'user_id' => $this->session->user_id
				);
			return $this->db->insert('action_log', $data);
		}
		else
		{
			return 'Invalid Log Type';
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
	 * @param  int|null $limit       Limits items gotten from entries
	 * @param  Boolean  $filter_user True if to filter by user ID
	 * @return array 				 Contains Table and Number of Rows                
	 */	
	public function get_entries_table( int $limit = NULL, $filter_user = TRUE, int $offset = 0) {
		//load required libraries
		if ($this->load->is_loaded('table') == FALSE) $this->load->library('table');

		$this->db
			->select('action_name, type_name, project_name, team_name, log_desc, log_date, log_time')
			->join('actions','actions.action_id = action_log.action_id')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->join('projects','projects.project_id = actions.project_id', 'left')
			->join('teams','teams.team_id = action_log.team_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');;

		if ($filter_user)
		{
			$this->db->where('user_id', $this->session->user_id);
			$this->table->set_heading(array('Action Name','Action Type', 'Project', 'Team', 'Log Description', 'Log Date', 'Log Time'));
		}
		else
		{
			$this->db->select('name')
			->join('users','action_log.user_id=users.user_id');
			$this->table->set_heading(array('Action Name', 'Action Type', 'Project', 'Team', 'Log Description', 'Log Date', 'Log Time', 'Name'));
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