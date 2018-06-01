<?php 
class User_model extends CI_Model {
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database

		$this->load->model('search_model');
	}
	/**
	 * Database interaction for logging in the user. Also sets session data on logon.
	 * @return string Returns 'Loged_in' if user logged in. Otherwise, the error string.
	 */
	public function login_user() {
		//Load Session library
		$this->load->library('session');
		//Validate user
		$this->db->where('email', $this->input->post('email'));
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$stored_pass = $query->row()->password;
			if ($stored_pass === crypt($this->input->post('password'), $stored_pass))
				{
					$query_result = $query->row_array();
					$sess_data = array(
						'email' => $query_result['email'],
						'name' 	=> $query_result['name'],
						'user_id' => $query_result['user_id'],
						'privileges' => $query_result['privileges'],
						'logged_in' => TRUE);
					$this->session->set_userdata($sess_data);
					return 'Loged_in';
				}
				else
				{
					return 'Incorrect Credentials';
				}
			
		} else {
			return 'No User Found';
		}
	}

	public function email_in_database($email)
	{
		$this->db->where('email', $email);

		if ($this->db->count_all_results('users') == 1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
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
		return $this->search_model->get_items_raw('users', array('user_id' => $user_id))->row()->email;
	}

	public function get_reset_hash($user_id, $email_code)
	{
		$query = $this->search_model->get_items_raw('users', array('user_id' => $user_id));
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
		$query = $this->search_model->get_items_raw('users', array('user_id' => $user_id))->row();
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