<?php 
class user_model extends CI_Model {
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
	}

	public function login_user() {
		//Load Session library
		$this->load->library('session');
		//Validate user
		$this->db->where('email', $this->input->post('email'));
		$this->db->where('password', $this->input->post('password'));
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$query_result = $query->row_array();
			$sess_data = array(
				'email' => $query_result['email'],
				'name' 	=> $query_result['name'],
				'user_id' => $query_result['user_id'],
				'privileges' => $query_result['privileges'],
				'logged_in' => TRUE);
			$this->session->set_userdata($sess_data);

			return 'Loged_in';
		} else {
			return 'Incorrect Login Credentials';
		}
	}


}