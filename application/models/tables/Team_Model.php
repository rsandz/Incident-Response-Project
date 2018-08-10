<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team_model extends MY_Model {

	/**
	 * Makes a team based on the inserted data
	 * @param  array $insert_data Associative array of the data
	 * @return integer            The integer ID of the team just created
	 */
	public function make($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'team_name' => $insert_data['team_name'],
		);

		if ($this->data_exists('teams', $check_array))
		{
			return FALSE;
		}

		$this->db->insert('teams', $insert_data);

		return $this->db->insert_id();
	}

	/**
	 * Gets the team by name
	 * @param  string $name Name
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name)
	{
		$this->db->where('team_name', $name);
		if($result = $this->db->get('teams'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the team by ID
	 * @param  integer $id The team's ID
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id)
	{
		$this->db->where('team_id', $id);
		if($result = $this->db->get('teams'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
	}

}

/* End of file Team_model.php */
/* Location: ./application/models/tables/Team_model.php */