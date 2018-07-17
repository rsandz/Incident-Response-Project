<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model for the Authentication Library
 * =========================================
 * @author Ryan Sandoval
 * @package Authentication
 * @version 1.0
 *
 * Allows for Database access for the authentication library.
 * 
 */
class User_model extends CI_Model {

	/**
	 * Construcor for the Authentication User Model
	 * Loads the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Attempts to get the user's row in the database
	 * @param string $identifier An Identifier for the user. Can be email or ID
	 * @param string $type Method to get user. Can be ID or Email
	 * @return Mixed        Result Object if successful. NULL otherwise.
	 */
	public function get_user($identifier, $type = 'email')
	{
		$this->db->select('*, CONCAT(first_name, " ", last_name) as name');

		switch ($type)
		{
			case 'email': return $this->db->where('email', $identifier)->get('users')->row();
			case 'id': return $this->db->where('user_id', $identifier)->get('users')->row();
		}
	}

	/**
	 * Updates the user info in the database base on the user_id given
	 *
	 * @param int $user_id The ID of the user to update
	 * @param array $update_data The data to update the user with
	 * @return boolean TRUE if successful
	 */
	public function update_user($user_id, $update_data)
	{
		return $this->db
			->where('user_id', $user_id)
			->update('users', $update_data);
	}
}

/* End of file User_model.php */
/* Location: ./application/models/authentication/User_model.php */