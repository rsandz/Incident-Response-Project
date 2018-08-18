<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends MY_Model {

	/**
	 * Makes a project based on the inserted data
	 * @param  array $insert_data Associative array of the data
	 * @return integer            The integer ID of the project just created
	 */
	public function make($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'project_name' => $insert_data['project_name'],
		);

		if ($this->data_exists('projects', $check_array))
		{
			return FALSE;
		}

		$this->db->insert('projects', $insert_data);

		return $this->db->insert_id();
	}

	/**
	 * Gets the project by name
	 * @param  string $name Name
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name)
	{
		$this->db->where('project_name', $name);
		if($result = $this->db->get('projects'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the project by ID
	 * @param  integer $id The project's ID
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id)
	{
		$this->db->where('project_id', $id);
		if($result = $this->db->get('projects'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
	}

}

/* End of file Project_model.php */
/* Location: ./application/models/tables/Project_model.php */