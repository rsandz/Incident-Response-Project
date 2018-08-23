<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_type_model extends MY_Model {

	/** @var boolean Whether Current user is admin or not */
	protected $admin_mode;

	public function __construct()
	{
		parent::__construct();

		//Initialization
		$this->sys_type_name = 'System';
		$this->admin_mode = $this->authentication->check_admin();
	}

	/*
	--------------------
		Make Methods
	--------------------
	*/

	/**
	 * Create a new action type and then insert it based
	 * on the insert array given.
	 * @param boolean $validate Whether to validate before Inserting
	 * @return integer The ID of the action type created
	 */
	public function make($insert_data, $validate = FALSE)
	{
		if ($validate && !$this->validate_insert_data($insert_data)) return FALSE;

		$this->db->insert('action_types', $insert_data);

		return $this->db->insert_id();
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
	 * Validates Action Type insert data
	 * ACtion type mustnot already exist
	 * @param $insert_data
	 * @return boolean If valid (TRUE) or not (FALSE)
	 */
	public function valid_insert_data($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'type_name' => $insert_data['type_name'],
		);

		if ($this->data_exists('action_types', $check_array))
		{
			return FALSE;
		}
		return TRUE;
	}

	/*
	---------------------
		Get Methods
	---------------------
	*/

	/**
	 * Gets all the action types that are active. If admin_mode is TRUE, 
	 * will also get inactive action types
	 * @param  boolean $admin_mode Gets inactive action types if TRUE
	 * @return array              Array of result objects.
	 */
	public function get($type_ids = NULL, $admin_mode = NULL)
	{
		//Admin Check
		if (!isset($admin_mode)) $admin_mode = $this->admin_mode;
		if (!$admin_mode)
		{
			$this->db->where('is_active', 1);
		}

		//Filter
		if (isset($type_ids))
		{
			if (is_array($type_ids))
			{
				$this->db->where_in($type_ids);
			}
			else
			{
				$this->db->where($type_ids);
			}
		}

		return $this->db->get('action_types')->result();
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

/* End of file Action_type_model.php */
/* Location: ./application/models/Logging/Action_type_model.php */