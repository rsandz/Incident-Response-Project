<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Investigation Model
 * ===================
 * @author Ryan Sandoval
 * @version 1.0
 * @package Investigation
 * 
 * The investigation model handles all database interaction regarding
 * Incidents and their Investigation
 */
class Investigation_model extends MY_Model {

	/**
	 * Total Rows if get is not limited.
	 * @var int
	 */
	public $total_rows;

	/**
	 * Constructor for the Investigation Model
	 * Loads all the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Inserts the incident into the the incident table
	 * @param  array $insert_data Associative array of the insert data
	 * @return boolean              TRUE if succesful. False if not
	 */
	public function insert_incident($insert_data)
	{
		return $this->db->insert('incidents', $insert_data);
	}

	/**
	 * Gets the incidents based on an offset and limit.
	 * @param  integer $offset Offset for the rows to fetch. See MySql OFFSET
	 * @param  integer  $limit Amount of incidents to fetch. See MySql LIMIT
	 * @return object          Result object from db->get()
	 */
	public function get_incidents($offset = 0, $limit = NULL)
	{
		$limit = $limit ?: $this->config->item('per_page');

		$this->db->from('incidents');

		//Find the total rows in incidents
		$this->total_rows = $this->db->count_all();
		$this->sql_commands_for_table('incidents');

		//Set limit and offset
		$this->db->limit($limit, $offset);
		return $this->db->get();
	}

}

/* End of file Investigation_model.php */
/* Location: ./application/models/investigation/Investigation_model.php */