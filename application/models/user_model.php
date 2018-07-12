<?php 
class User_model extends CI_Model {

	/**
	 * Constructor for this class
	 * Loads Resources
	 */
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
	}

	public function recovery_data($email)
	{
		//Get User information
		$this->db->select(array(
			'name', 'user_id', 'password'
		))
			->where('email', $email);

		$query = $this->db->get('users');

		if ($query->num_rows() !== 1)
		{
			show_error('Multiple Emails Found in Database. Please contact your administrator.');
			return FALSE;
		}
		else
		{
			$data['name'] = $query->row()->name;
			$data['user_id'] = $query->row()->user_id;

			//Generate code to access password recovery form
			$data['email_code'] = crypt($email.$query->row()->password, $this->config->item('salt'));

			return $data;

		}

	}	

	public function user_email($user_id)
	{
		return $this->db->where('user_id', $user_id)->get('users')->row()->email;
	}

	public function get_reset_hash($user_id, $email_code)
	{
		$query = $this->db->where('user_id', $user_id)->get('users');
		if ($query->num_rows() !== 1)
		{
			return FALSE;
		}
		$query = $query->row();

		return crypt($email_code.$query->password.$query->email, $this->config->item('salt'));
	}

	public function validate_reset_hash($user_id, $email_code, $reset_hash)
	{
		$email = $this->user_email($user_id);
		$query = $this->db->where('user_id', $user_id)->get('users')->row();
		if (password_verify($email_code.$query->password.$email, $reset_hash))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function reset_password($user_id, $password)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update('users', array('password' => crypt($password, $this->config->item('salt'))));

		return TRUE;
	}


}