<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_get_model extends CI_Model {

	/**
	 * Form_get Model Initialization
	 */
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
	}

	public function get_item_info($table, $id) 
	{
		$data['desc'] = $this->get_item_desc($table, $id) ?: 'No Description';
		$data['type'] = $this->get_action_type($table, $id) ?: 'No Type';

		return $data;
	}

	public function get_item_desc($table, $id)
	{
		//Get Description column
		$fields = $this->db->list_fields($table);
		foreach($fields as $index => $field)
		{
			if (preg_match('/desc/', $field))
			{
				$desc_field_name = $field;
			}
		}

		if (!isset($desc_field_name))
		{
			//No Description column
			return NULL;
		}

		$query = $this->db->where($this->get_primary_key_name($table), $id)
				->get($table)->row();
		
		//Validate $query. If its empty, then return null
		if (!isset($query))
		{
			return NULL;
		}
		return $query->$desc_field_name;
	}

	public function get_action_type($table, $id)
	{
		$query = $this->db->where($this->get_primary_key_name($table), $id)
							->get($table)->row();
		
		//Validate $query. If its empty, then return null
		if (!isset($query))
		{
			return NULL;
		}

		if (property_exists($query, 'type_id'))
		{
			//Get action type name
			$type_query = $this->db->where('type_id', $query->type_id)
						->get('action_types')
						->row();
			return $type_query->type_name;
		}
		else
		{
			return NULL;
		}
	}

	public function get_primary_key_name($table)
	{
		$query = $this->db->field_data($table, TRUE); //Get all columns

		foreach ($query as $column)
		{
			if ($column->primary_key)
			{
				return $column->name;
			}
		}

		return FALSE; //If not primary key
	}

	public function get_active_actions($type_id, $project_id)
	{
		$this->db->where('type_id', $type_id)
				->group_start()
					->or_where('project_id', $project_id)
					->or_where('is_global', 1)
				->group_end()
				->where('is_active', 1)
				->select(array('action_name', 'action_id'));

		$results = $this->db->get('actions')->result();

		//Validate $query. If its empty, then return null
		if (!isset($results))
		{
			return NULL;
		}

		$actions = array(); //array(action_id => action_name)
		foreach($results as $result)
		{
			$actions[$result->action_id] = $result->action_name;
		}

		return $actions;
	}

	public function active_actions_form($type_id, $project_id)
	{
		$action_options = $this->get_active_actions($type_id,  $project_id);
		
		if (empty($action_options)) $action_options[''] = 'No Actions';

		return form_dropdown('action', $action_options, NULL, 'id = "action-selector"');
	}

	public function team_leaders_select($is_admin = NULL)
	{
		if(!isset($admin)) //Check if admin is not overwritten
		{
			$is_admin = check_admin();
		}

		if (!$is_admin)
		{
			return form_dropdown('team_leader', array($this->session->user_id => $this->session->name), NULL, 'class="select"');
		}

		$users = $this->db
			->or_where('privileges', 'team_leader')
			->or_where('privileges', 'admin')
			->select(array('name', 'user_id'))
			->get('users')->result();

		if (empty($users))	
		{
			return NULL;
		}

		foreach($users as $user)			
		{
			$options[$user->user_id] = $user->name;
		}

		return form_dropdown('team_leader', $options, NULL, 'class="select"');
	}
}

/* End of file Form_get_model.php */
/* Location: ./application/models/Form_get_model.php */