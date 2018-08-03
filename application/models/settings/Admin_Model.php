<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends MY_Model {

	/**
	 * Changes the notify on new incident setting
	 * @param  integer $user_id The ID of the user to update the setting for
	 * @param integer $value The value to set the setting too
	 * @return boolean			TRUE if successful. FALSE otherwise
	 */
	public function notify_new_incident($user_id, $value)
	{
		return $this->db->where('user_id', $user_id)
				->set(array('notify_new_incident' => $value))
				->update('admin_settings');
	}

	/**
	 * Changes the notify on investigation finish setting
	 * @param  int $user_id The ID of the user to update the setting for
	 * @param  integer $value   The value to set the setting to
	 * @return boolean          TRUE if successful, FALSE otherwise.
	 */
	public function notify_investigated($user_id, $value)
	{
		return $this->db->where('user_id', $user_id)
				->set(array('notify_investigated' => $value))
				->update('admin_settings');
	}

	/**
	 * Gets the current settings for the selected user
	 * @param  integer $user_id The ID of the user to get the settings for
	 * @return object 			db row object containint the settings for the user
	 */	
	public function get_current_settings($user_id)
	{
		if (!$this->data_exists('admin_settings', array('user_id' => $user_id)))
		{
			$this->create_setting($user_id);
		}
		$this->db->where('user_id', $user_id);
		return $this->db->get('admin_settings')->row();
	}

	/**
	 * Gets all the user's names and emails for the admins that
	 * have chosen to receive emails for new incidents.
	 * @return array Database result array containing user's name and email
	 */
	public function get_notify_new_incidents()
	{
		$this->db
			->join('users', 'users.user_id = admin_settings.user_id', 'left')
			->select('users.email, CONCAT(users.first_name, " ", users.last_name) AS name')
			->where('notify_new_incident', 1);
		return $this->db->get('admin_settings')->result();
	}

	/**
	 * Creates the admin setting row for a user
	 * @param  int $user_id The ID of the user to create a setting for.
	 * @return void          
	 */
	public function create_setting($user_id)
	{
		$this->db->insert('admin_settings', array('user_id' => $user_id));
	}

}

/* End of file Admin_Model.php */
/* Location: ./application/models/settings/Admin_Model.php */
