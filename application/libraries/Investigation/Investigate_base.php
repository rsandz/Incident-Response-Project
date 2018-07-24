<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Investigate Base
 * ================
 * @author Ryan Sandoval
 * @package Investigation
 * @version 1.0
 * @uses Investigation_model
 * 
 * Base Library Class used for the investigation functionality.
 * Contains several common functions as well as the initialization
 * of the resources used in the Investigation package:
 * 	- Investigation Model
 */
class Investigate_base
{
	/** @var object Code Igniter Instance */
	protected $CI;

	/** @var array Array of errors */
	protected $errors = array();

	/**
	 * Constructor for the investigation Base
	 * Loads the necessary Resources
	 */
	public function __construct()
	{
        $this->CI =& get_instance();

        //Load the model
        $this->CI->load->model('Investigation/investigation_model');
	}

	/**
	 * Creates an HTML string summary of an incident
	 * @param  integer $incident_id The incident to make the summary for
	 * @return string               The HTML string
	 */
	public function incident_summary($incident_id)
	{
		$incident = $this->CI->investigation_model->get_incident($incident_id);
		$data['name'] = $incident->incident_name;

		$data['id'] = $incident->incident_id;

		$raw_date = strtotime($incident->incident_date);
		$data['date'] = date('F j, Y', $raw_date);

		$raw_time = strtotime($incident->incident_time);
		$data['time'] = date('g:i a', $raw_time);
		
		$data['desc'] = $incident->incident_desc;

		$summary = $this->CI->load->view('incidents/templates/summary', $data, TRUE);
		
		return $summary;
	}
	
	/**
	 * Gets the errors, as a string
	 * @return string The errors
	 */
	public function get_errors()
	{
		$string = '<b>Investigate Package Errors:</b> <br>';
		foreach ($this->errors as $index => $error_msg)
		{
			$string .= "<b>Error #{$index}:</b> {$error_msg}<br>";
		}
		return $string;
	}

	/**
	 * Adds an error to the error array
	 * @return void
	 */
	protected function error($error)
	{
		$this->errors[] = $error;
	}

}

/* End of file Investigate_base.php */
/* Location: ./application/libraries/Investigation/Investigate_base.php */
