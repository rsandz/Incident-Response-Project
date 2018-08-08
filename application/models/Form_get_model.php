<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Form Get Model
 * ==============
 * @author Ryan Sandoval
 * 
 * This contains various get methods for forms.
 * Used in displaying descriptions for form fields or creating
 * some fields based on data currently stored in the database
 */
class Form_get_model extends MY_Model {

	/**
	 * Form_get Model Initialization
	 */
	public function __construct() {
		parent:: __construct();
	}

	public function get_item_info($table, $id) 
	{
		$data['desc'] = $this->get_item_desc($table, $id) ?: 'No Description';
		$data['type'] = $this->get_action_type($table, $id) ?: 'No Type';

		return $data;
	}

	/**	
	 * Gets the description of a specified item within 
	 * a specified table.
	 */
	public function get_item_desc($table, $id)
	{
		//Get Description column name
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

		//Get description itself
		$query = $this->db->where($this->get_primary_key_name($table), $id)
				->get($table)->row();
		
		//Validate $query. If it's empty, then return null
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

	public function get_active_actions($type_id, $project_id, $term)
	{
		$this->db->where('type_id', $type_id)
				->group_start()
					->or_where('project_id', $project_id)
					->or_where('is_global', 1)
				->group_end()
				->like('action_name', $term)
				->where('is_active', 1)
				->select(array('action_name', 'action_id'));

		$actions = $this->db->get('actions')->result();

		//Validate $query. If its empty, then return null
		if (empty($actions))
		{
			return array('results' => NULL);
		}

		//Format for select2 to parse
		foreach ($actions as $action)
		{
			$data['results'][] = array(
				'id' => $action->action_id,
				'text' => $action->action_name
			);
		}

		return $data;
	}

	public function team_leaders_select($is_admin = NULL)
	{
		if(!isset($admin)) //Check if admin is not overwritten
		{
			$is_admin = $this->authentication->check_admin();
		}

		if (!$is_admin)
		{
			return form_dropdown('team_leader', array($this->session->user_id => $this->session->name), NULL, 'class="select"');
		}

		$users = $this->db
			->or_where('privileges', 'team_leader')
			->or_where('privileges', 'admin')
			->select('user_id, CONCAT(first_name, " ", last_name) as name')
			->get('users')->result();

		if (empty($users))	
		{
			return NULL;
		}

		foreach($users as $user)			
		{
			$options[$user->user_id] = $user->name;
		}

		return form_dropdown('team_leader', $options, NULL, 'class="select init-select2"');
	}
}

/* End of file Form_get_model.php */
/* Location: ./application/models/Form_get_model.php */