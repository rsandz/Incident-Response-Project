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

	/** @var integer ID of the incident that just got inserted */
	public $insert_id;

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
		$result = $this->db->insert('incidents', $insert_data);
		$this->insert_id = $this->db->insert_id(); // Set last incident ID
		return $result;
	}

	public function get_incident($incident_id)
	{
		$this->db->where('incident_id', $incident_id);
		return $this->db->get('incidents')->row();
	}

	/**
	 * Gets the incidents based on an offset and limit.
	 * @param  integer $offset 		Offset for the rows to fetch. See MySql OFFSET
	 * @param  integer  $limit 		Amount of incidents to fetch. See MySql LIMIT
	 * @param  boolean $return_id 	Whether to return the incident_id field in the object
	 * @return object          		Result object from db->get()
	 */
	public function get_all_incidents($offset = 0, $limit = NULL, $return_id = FALSE)
	{
		$limit = $limit ?: $this->config->item('per_page');

		$this->db->from('incidents');

		$this->sql_commands_for_table('incidents');
		if ($return_id)
		{
			$this->db->select('incident_id as ID');
		}

		//Find the total rows in incidents
		$this->total_rows = $this->db->count_all_results('', FALSE);
		//Set limit and offset
		$this->db->limit($limit, $offset);
		$this->db->order_by('incident_date', 'DESC');
		$this->db->order_by('incident_time', 'DESC');
		return $this->db->get();
	}

	/**
	 * Gets the Data needed to create the table that allows the user 
	 * to select which incident's report to view.
	 * @return object Object to be passed onto the table library
	 */
	public function report_table_data($offset = 0)
	{
		$incidents = $this->get_all_incidents($offset, NULL, TRUE);
		//Add the report link by replacing the ID field
		foreach($incidents->result() as &$incident)
		{
			$report_url = site_url('Incidents/report/'.$incident->ID);
			$incident->ID = "<a href='$report_url' class='button is-primary'>Link</a>";
		}
		//Add View report to the field data
		return $incidents;
	}
	
	/**	
	 * Gets the statistics for the incidents
	 */
	public function get_incident_stats()
	{
		$total_incidents = $this->db->count_all('incidents');
		$last_incident_raw = $this->db
							->order_by('created_on', 'DESC')
							->select('CONCAT("Incident #", `incident_id`, ": ", `incident_name`) as last_incident')
							->get('incidents')
							->row();
		if (empty($last_incident_raw))
		{
			$last_incident = 'None. Hooray!';
		}
		else
		{
			$last_incident = $last_incident_raw->last_incident;
		}
		return array('total_incidents' => $total_incidents, 'last_incident' => $last_incident);
	}


}

/* End of file Investigation_model.php */
/* Location: ./application/models/investigation/Investigation_model.php */