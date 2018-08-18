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
class User_model extends MY_Model {

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
	 * Gets the user row in the database by their ID
	 * @param  int $id User ID
	 * @return object  The User Row Object
	 */
	public function get_by_id($id)
	{
		$this->db->select('*, CONCAT(first_name, " ", last_name) as name');
		return $this->db->where('user_id', $id)->get('users')->row();
	}

	/**
	 * Gets the user row in the database by their email
	 * @param  int $email User email
	 * @return object  The User Row Object
	 */
	public function get_by_email($email)
	{
		$this->db->select('*, CONCAT(first_name, " ", last_name) as name');
		return $this->db->where('email', $email)->get('users')->row();
	}

	/**
	 * Gets the user row in the database by their name
	 * @param  int $name User name
	 * @return object  The User Row Object
	 */
	public function get_by_name($name)
	{
		$this->db->select('*, CONCAT(first_name, " ", last_name) as name');
		return $this->db->where('name', $name)->get('users')->row();
	}

	/**
	 * Updates the user info in the database base on the user_id given
	 *
	 * @param int $user_id The ID of the user to update
	 * @param array $update_data The data to update the user with
	 * @return boolean TRUE if successful
	 */
	public function update($user_id, $update_data)
	{
		return $this->db
			->where('user_id', $user_id)
			->update('users', $update_data);
	}

	/**
	 * Makes a new user in the table
	 * @param  array $insert_data Associative array containing the data
	 * @return integer            The ID of the newly created user.
	 */
	public function make($insert_data)
	{
		//Check if user already exists
		$check_array = array(
			'first_name' => $insert_data['first_name'],
			'last_name' => $insert_data['last_name'],
			'email' => $insert_data['email'],
		);

		if ($this->data_exists('users', $check_array))
		{
			return FALSE;
		}

		$this->db->insert('users', $insert_data);

		return $this->db->insert_id();
	}	
}

/* End of file User_model.php */
/* Location: ./application/models/Logging/User_model.php */
