<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_Model extends MY_Model {

	/**
	 * Creates an action using an action name and type id
	 * 
	 * @param  array $insert_data The data to insert (Associative array)
	 * @return integer|boolean     The ID of the action. OR FALSE if it already exists
	 */
	public function make($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'action_name' => $insert_data['action_name'],
			'type_id' => $insert_data['type_id']
		);

		if ($this->data_exists('actions', $check_array))
		{
			return FALSE;
		}

		$this->db->insert('actions', $insert_data);

		return $this->db->insert_id();
	}

	/**
	 * Gets the action by name
	 * @param  string $name Name
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name)
	{
		$this->db->where('action_name', $name);
		if($result = $this->db->get('actions'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the action by ID
	 * @param  integer $id The action's ID
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id)
	{
		$this->db->where('action_id', $id);
		if($result = $this->db->get('actions'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
	}
}

/* End of file Action_Model.php */
/* Location: ./application/models/Logging/Action_Model.php */
