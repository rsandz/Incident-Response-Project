<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_Model extends MY_Model {

	/** @var int The ID of the action_type to filter by */
	protected $type_id;

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

		if (!empty($this->type_id))
		{
			//Lock action Type
			$this->db->where('type_id', $this->type_id);
		}

		if($result = $this->db->get('actions'))
		{
			$this->reset();
			return $result->row();
		}
		else
		{
			$this->reset();
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

		if (!empty($this->type_id))
		{
			//Lock action Type
			$this->db->where('type_id', $this->type_id);
		}

		if($result = $this->db->get('actions'))
		{
			$this->reset();
			return $result->row();
		}
		else
		{
			$this->reset();
			return FALSE;
		}		
	}

	/**
	 * Use this to lock the action type when searching for actions.
	 * @param  string|int $identifier Either Action Name or action type
	 * @param  string     $type       EIther 'id' or 'name'
	 * @return void             
	 */
	public function type($identifier, $type = 'id')
	{
		switch($type)
		{
			case 'id':
				$this->type_id = $identifier;
				break;
			case 'name':
				$id = $this->db
					->where('type_name', $identifier)
					->select('type_id')
					->get('action_types')
					->row()->type_id;
				$this->type_id = $id;
				break;
			default:
				$this->error = 'Invalid `type` to retreive action type.';
		}
	}

	/**
	 * Clears the stored action type
	 * @return void 
	 */
	public function reset()
	{
		$this->type_id = '';
	}
}

/* End of file Action_Model.php */
/* Location: ./application/models/Logging/Action_Model.php */
