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

			$type_id = $this->db->where('type_name', 'create')->get('action_types')->row()->type_id;

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
				'action_id' => $this->db->where('action_name', 'Created '.$data)->get('actions')->row()->action_id,
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
	 * Inserts into action log a team modifying action. i.e. User added/Removed to team
	 *
	 * @param string $team_name The team that was modified
	 * @param string $user_name The name of the user that had their team modified
	 * @param string $type Type of modification. Use 'add' or 'remove'
	 */
	public function log_team_action($team_name, $user_name, $type)
	{
		//Check if the 'modify' action type exists
		$action_type_id = $this->make_log_action_type('modify');

		//Check if the user added/removed actions exists
		if ($type == 'add' )
		{
			$action_id = $this->make_log_action('User Added to Team', $action_type_id);
			//Create the log
			$log_data = array(
				'action_id' => $action_id,
				'log_date' => date('Y-m-d'),
				'log_time' => date('H:i'),
				'log_desc' => "$user_name added to (Team) $team_name",
				'team_id' => NULL,
				'user_id' => $this->session->user_id
			);

			$this->log_action('form', $log_data);
		}
		elseif ($type == 'remove')
		{
			$action_id = $this->make_log_action('User Removed from Team', $action_type_id);
			//Create the log
			$log_data = array(
				'action_id' => $action_id,
				'log_date' => date('Y-m-d'),
				'log_time' => date('H:i'),
				'log_desc' => "$user_name removed from (Team) $team_name",
				'team_id' => NULL,
				'user_id' => $this->session->user_id
			);

			$this->log_action('form', $log_data);
		}
		else
		{
			log_message('error', 'Invalid log_team_action() type.');
			return FALSE;
		}
	}


	public function make_log_action_type($type_name)
	{
		if(!$this->search_model->data_exists('action_types', array('type_name' => $type_name)))
		{
			$insert_data = array(
				'type_name' => $type_name,
				'is_active' => 0
			);

			$this->log_item('action_types', $insert_data);
		}

		return $this->db->where('type_name', $type_name)->get('action_types')->row()->type_id;
	}

	/**
	 * Creates an action for use with logging. (i.e. create and modify logs)
	 * If the action already exsists, will instead return that action's id
	 * @param  string $action_name The name of the action
	 * @param  string|int $type_id    The ID of the action's action_type
	 * @return string             The ID of the action
	 */
	public function make_log_action($action_name, $type_id)
	{
		if (!$this->search_model->data_exists('actions', array('action_name' => $action_name)))
		{
			$insert_data = array(
				'action_name' => $action_name,
				'type_id' => $type_id,
				'action_desc' => NULL,
				'project_id' => NULL,
				'is_active' => FALSE
			);

			$this->log_item('actions', $insert_data);
		}
		return $this->db->where('action_name', $action_name)->get('actions')->row()->action_id;
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
}