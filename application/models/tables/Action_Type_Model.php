<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_Type_Model extends MY_Model {

	public function __construct()
	{
		parent::__construct();

		//Initialization
		$this->sys_type_name = 'System';
	}

	/**
	 * Create a new action type and then insert it based
	 * on the insert array given.
	 * @return integer The ID of the action type created
	 */
	public function make($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'type_name' => $insert_data['type_name'],
		);

		if ($this->data_exists('action_types', $check_array))
		{
			return FALSE;
		}

		$this->db->insert('action_types', $insert_data);

		return $this->db->insert_id();
	}

	/**
	 * Gets the action type by name
	 * @param  string $name Name
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name)
	{
		$this->db->where('type_name', $name);
		if($result = $this->db->get('action_types'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the action type by ID
	 * @param  integer $id The action type's ID
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id)
	{
		$this->db->where('type_id', $id);
		if($result = $this->db->get('action_types'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
	}

	/**
	 * Creates the System Action Type
	 * @return void
	 */
	public function make_sys_type()
	{
		$insert_data = array(
			'type_name' => $this->sys_type_name,
			'is_active' => 0
		);

		return $this->db
			->insert('action_types', $insert_data);
	}

	/**
	 * Gets the System Action Type
	 * @param boolean $automake If TRUE, will automatically
	 *                          insert a system action type if it
	 *                          is non-existant
	 * @return integer|boolean The ID of the System Action Type. Or 
	 *                         FALSE if automake was FALSE and the 
	 *                         system type is non existent
	 */
	public function get_sys_type($automake = TRUE)
	{
		$type_row = $this->db
			->where('type_name', $this->sys_type_name)
			->get('action_types')
			->row();

		if (empty($type_row) && $automake)
		{
			//Make it!
			$this->make_sys_type();
			return $this->db->insert_id();
		}
		elseif (!empty($type_row))
		{
			return $type_row->type_id;
		}
		else
		{
			return FALSE;
		}
	}

}

/* End of file Action_Type_Model.php */
/* Location: ./application/models/Logging/Action_Type_Model.php */