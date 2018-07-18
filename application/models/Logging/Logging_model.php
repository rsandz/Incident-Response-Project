<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logging_model extends MY_Model {

	public $last_log_id;

	/**
	 * Logging Model Initialization
	 */
	public function __construct() {
		parent:: __construct();
		$this->load->database();
	}

	/**
	 * Inserts a passed array into the action_logs table
	 * @param  array $insert_data The Data to insert
	 * @return boolean            TRUE if succesful. FALSE if not
	 */
	public function insert_log($insert_data)
	{
		$query = $this->db->insert('action_log', $insert_data);
		$this->last_log_id = $this->db->insert_id();
		return $query;
	}

}

/* End of file Logging_model.php */
/* Location: ./application/models/Logging/Logging_model.php */
