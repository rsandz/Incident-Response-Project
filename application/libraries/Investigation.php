<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Investigation Library
 * =====================
 * @author Ryan Sandoval
 * @version 1.0
 * @package Investigation
 * @dependencies TODO
 *
 * This library contains the functionality that allows app
 * to perform automatic investigation and report creation.
 */
class Investigation
{
	/** @var object Code Igniter Instance */
	protected $CI;

	/** 
	 * User ID for creating new incidents and logging.
	 * @var int
	 */
	protected $user_id;

	/**
	 * Logs incident creation into action log if set to TRUE
	 * @var boolean
	 */
	protected $logging;

	/**
	 * Initializes the Investigation Library
	 * @param int  $user_id The User ID used for logging and creating new incidents.
	 * @param boolean $logging Set to TRUE to enable logging functionalities.
	 */
	public function __construct($user_id = NULL, $logging = TRUE)
	{
        $this->CI =& get_instance();
        //The library will check to see if user_id was passed. Otherwise, use the one
        //set in the session.
        $this->user_id = $user_id ?: $this->CI->session->user_id;

        //Load the model
        $this->CI->load->model('investigation/investigation_model');

        //Load Logging if logging is TRUE
        if ($this->logging = $logging)
        {
        	//$this->load->library('logging');
        }
	}

	/**
	 * Use to create a new incident, either automatically 
	 * 	(with scripts) or manually (with forms)
	 * Formats the data then passes it to the investigation model.
	 * @param  array $insert_data The data array to insert
	 * @return boolean            True if successful
	 */
	public function new_incident($insert_data)
	{
		//Assume it was automated if not explicitly declared
		if (!isset($insert_data['was_automated']))
		{
			$insert_data['was_automated']  = TRUE;
		}

		//If no user id is set in 'created_by' key,
		//and it was not automated, then the current user
		//will be set.
		if (!isset($insert_data['created_by']) && !$insert_data['was_automated'])
		{
			$insert_data['created_by'] = $this->CI->session->user_id;
		}

		//Other info for the incident. TODO

		//Put into Database
		$this->CI->investigation_model->insert_incident($insert_data);

		//Put into Log
		//TODO: Hook up to logging lib

		return TRUE;

	}

	/**
	 * Returns Data for the most recent investigations.
	 * The amount fetched is configured in $per_page set in appconfig.
	 *
	 * @param  integer $offset Offset for rows to fetch. Can use for pagination
	 * @return array          The array contains the following:
	 *                            'data' => sql object
	 *                            'total_rows' => Total results if query was not limited.
	 *                            					i.e. All possible results
	 *                            'num_rows' => Number of rows in the sql object
	 */
	public function recent_incidents($offset = 0)
	{
		$data = $this->CI->investigation_model->get_incidents($offset);
		return array( 
			'data' => $data, 
			'total_rows' => $this->CI->investigation_model->total_rows,
			'num_rows' => $data->num_rows()
		);
	}

}

/* End of file Investigation.php */
/* Location: ./application/libraries/Investigation.php */
