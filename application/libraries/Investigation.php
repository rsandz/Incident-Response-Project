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

	protected $IL_name = NULL;
	protected $IL_date = NUll;
	protected $IL_time = NUll;
	protected $IL_desc = '';
	protected $IL_auto = TRUE;
	protected $IL_user = NUll;

	protected $errors = array();

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
        $this->CI->load->model('Investigation/investigation_model');

        //Load Logging if logging is TRUE
        if ($this->logging = $logging)
        {
        	//$this->load->library('logging');
        }
	}

	/**
	 * Sets the name of the incident
	 * @param  string $name The name
	 * @return Investigation       Method Chaining
	 */
	public function name($name)
	{
		if(empty($name))
		{
			$this->error('Name Field received an empty string.');
			return $this;
		}

		$this->IL_name = $name;
		return $this;
	}

	/**
	 * Sets the date of the incident
	 * @param  string $date The Date
	 * @return Investigation       Method Chaining
	 */
	public function date($date)
	{
		if (empty($date))
		{
			$this->error('Date Field received an empty string');
			return $this;
		}

		//PHP auto-format
		$date = strtotime($date);
		$date = date('Y-m-d', $date);

		$this->IL_date = $date;
		return $this;
	}

	/**
	 * Sets the time of the incident
	 * @param  string $time Time
	 * @return Investigation      Method Chaining
	 */
	public function time($time)
	{
		if (empty($time))
		{
			$this->error('Time Field received an empty string');
			return $this;
		}

		//PHP auto-format
		$time = strtotime($time);
		$time = date('H:i:s', $time);

		$this->IL_time = $time;
		return $this;
	}

	/**
	 * Sets the incident description
	 * @param  string $desc The description
	 * @return Investigation    Method Chaining
	 */
	public function desc($desc)
	{
		$this->IL_desc .= $desc;
		return $this;
	}

	/**
	 * Sets whether log was automated or not
	 * @param  boolean $value Whether it was automated or not
	 * @return Investigation        Method chaining
	 */
	public function auto($value)
	{
		$this->IL_auto = filter_var($value, FILTER_VALIDATE_BOOLEAN);
		if (!isset($this->IL_auto))
		{
			$this->error('Automated Field did received incorrect data');
		}
		return $this;
	}

	/**
	 * Sets the user
	 * @param  string $users User ID
	 * @return Investigation        Method Chaining
	 */
	public function user($user)
	{
		if (empty($users))
		{
			$this->error('User Field reveived an empty string');
			return $this;
		}
		
		$this->IL_user = $user;
		return $this;
	}


	/**
	 * Use to create a new incident/
	 * Formats the data then passes it to the investigation model.
	 * @param  array $insert_data The data array to insert
	 * @return boolean            True if successful
	 */
	public function create()
	{
		//Assume it was automated if not explicitly declared
		if (!isset($this->IL_auto))
		{
			$this->IL_auto  = TRUE;
		}

		//If no user id is set in 'created_by' key,
		//and it was not automated, then the current user
		//will be set.
		if (!isset($this->IL_user) && !$this->IL_auto)
		{
			$this->IL_user =  $this->CI->session->user_id;
		}

		//Other info for the incident. TODO
		
		//Create the array
		$insert_data = array(
			'incident_name' => $this->IL_name,
			'incident_date' => $this->IL_date,
			'incident_time' => $this->IL_time,
			'incident_desc' => $this->IL_desc,
			'was_automated' => $this->IL_auto,
			'created_by'	=> $this->IL_user
		);

		//Put into Database
		$this->CI->investigation_model->insert_incident($insert_data);

		$this->notify_admin_new($this->CI->investigation_model->insert_id);
		//Put into Log
		//TODO: Hook up to logging lib

		return TRUE;

	}

	public function investigate($incident_id)
	{

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
		$data = $this->CI->investigation_model->get_all_incidents($offset);
		return array( 
			'data' => $data, 
			'total_rows' => $this->CI->investigation_model->total_rows,
			'num_rows' => $data->num_rows()
		);
	}


	/**
	 * Notifies admins on new incidents if they have chosen to receive
	 * notifications on new incidents
	 * @param  integer $incident_id The ID of the incident
	 * @return boolean              TRUE on success
	 */
	public function notify_admin_new($incident_id)
	{
		$this->CI->load->model('Settings/admin_model', 'admin_settings');
		//Get all the users with notify_new_incident_on
		
		//Load Library and Configs
		$this->CI->load->library('email');
		$this->CI->load->config('email');
		$this->CI->load->config('incidents', TRUE);

		$users_send_to = $this->CI->admin_settings->get_notify_new_incidents();
		$incident_summary = $this->incident_summary($incident_id);

		foreach ($users_send_to as $user)
		{
			//Generate the email
			$this->CI->email->from($this->CI->config->item('smtp_user'), 'Incident Manager');
			$this->CI->email->to($user->email);
			
			$this->CI->email->subject('New Incident');
			
			//Message formatting
			$message = $this->CI->config->item('new_incident_body', 'incidents');
			$message = str_replace('{name}', $user->name, $message);
			$message = str_replace('{link}', "PlaceHolder", $message);
			$message = str_replace('{summary}', $incident_summary, $message);

			$this->CI->email->message($message);

			//Send it out!
			$this->CI->email->send();

			return TRUE;
		}
	}

	/**
	 * Creates an HTML string summary of an incident
	 * @param  integer $incident_id The incident to make the summary for
	 * @return string               The HTML string
	 */
	public function incident_summary($incident_id)
	{
		$incident = $this->CI->investigation_model->get_incident($incident_id);

		$name = $incident->incident_name;
		$id = $incident->incident_id;
		$date = $incident->incident_date;
		$time = $incident->incident_time;
		$desc = $incident->incident_desc;

		$summary = "
		<h3>Incident #{$id}: {$name}</h3>
		<ul>
			<li>Date of Occurence: {$date}</li>
			<li>Time of Occurence: {$time}</li>
		</ul>
		<h3>Description: </h3>
		<p>{$desc}</p>
		";

		return $summary;
	}


	/**
	 * Gets the errors, as a string
	 * @return string The errors
	 */
	public function get_errors()
	{
		$string = '';
		foreach ($this->errors as $index => $error_msg)
		{
			$string .= "Error #{$index}: {$error_msg}<br>";
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

/* End of file Investigation.php */
/* Location: ./application/libraries/Investigation.php */
