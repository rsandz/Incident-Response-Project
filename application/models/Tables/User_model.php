<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('Table_base.php');

/**
 * User Model 
 * ==========
 * @author Ryan Sandoval
 * @version 1.0
 *
 * User Table operations
 */
class User_model extends Table_base {

	/** @var array Contains sort values */
	protected $sort;
	
	/**
	 * Custom Sort function for project model
	 */
	protected function apply_sort()
	{
		 switch ($this->sort['sort_field'])
		 {
			 case 'log_num':
				 $this->db
				 ->join('action_log', '`action_log`.`user_id` = `users`.`user_id`', 'left')
				 ->order_by('COUNT(`log_id`)', $this->sort['sort_dir'])
				 ->group_by('`users`.`user_id`');
				 break;
			 default:
				 parent::apply_sort();
				 break;
		 }
	}

	/*
	----------------------
		Get Functions
	----------------------
	*/

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
	 * Gets Users with ids. Otherwise, gets all users
	 * @param mixed $user_id Either a single user ID, or array of user IDs
	 * @return object db result object for that user(s)
	 */
	public function get($user_id = NULL)
	{
		$this->apply_sort();
		if (isset($user_id))
		{
			if (is_array($user_id))
			{
				//Use where_in
				$this->db->where_in('user_id', $user_id);
			}
			else
			{
				$this->db->where('user_id', $user_id);
			}
		}

		return $this->db
			->select('*, CONCAT(first_name, " ", last_name) as name')
			->get('users')
			->result();
	}

	/**
	 * Gets the user row in the database by their email
	 * @param  int $email User email
	 * @return object  The User Row Object
	 */
	public function get_by_email($email)
	{
		$this->apply_sort();
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
		$this->apply_sort();
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

	/*
	----------------------
		Make Functions
	----------------------
	*/

	/**
	 * Makes a new user in the table
	 * @param  array $insert_data Associative array containing the data
	 * @param boolean $validate Whether to validate before Inserting
	 * @return integer            The ID of the newly created user.
	 */
	public function make($insert_data, $validate = FALSE)
	{
		if ($validate && !$this->validate_insert_data($insert_data))
		{
			return FALSE;
		}
		$this->db->insert('users', $insert_data);
		return $this->db->insert_id();
	}	

	/**
	 * Validates User insert data
	 * Email must not already exsist
	 * @param $insert_data
	 * @return boolean If valid (TRUE) or not (FALSE)
	 */
	public function valid_insert_data($insert_data)
	{
		//Check if user already exists
		$check_array = array(
			'email' => $insert_data['email'],
		);
		if ($this->data_exists('users', $check_array))
		{
			return FALSE;
		}
		return TRUE;
	}
}

/* End of file User_model.php */
/* Location: ./application/models/Logging/User_model.php */
