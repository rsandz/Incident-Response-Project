<?php
class logging_model extends CI_model {
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->library('session');
	}
	public function log_action() { //inserts the log into the data base
		$data = array(
			'action_id' => $this->input->post('action'),
			'log_desc' => $this->input->post('desc'),
			'log_date' => $this->input->post('date'),
			'user_id' => $this->session->user_id
			);
		return $this->db->insert('action_log', $data);
	}
	public function get_actions() { //Get all posible actions
		$query = $this->db->get('actions');
		return $query->result_array();
	}

}