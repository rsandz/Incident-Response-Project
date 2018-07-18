<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_Builder
{
	/** @var object Code Igniter Instance Reference */
	protected $CI;

	/*----------------
	 Data For the Log
	----------------*/

	/** @var integer Action ID of log */
	protected $log_action;

	/** @var string Description of the log */
	protected $log_desc = '';

	/** @var integer Amount of Hours in log */
	protected $log_hours = 0;

	/** @var integer ID of user making the log */
	protected $log_user;

	/** @var integer ID of team affiliated with the log */	
	protected $log_team;

	/** @var integer ID of project affiliated with the log */
	protected $log_project;

	/** @var date|string The Date of the log*/
	protected $log_date;

	/** @var time|string The time of the log */
	protected $log_time;

	/** @var int The ID of the system log action type */
	protected $sys_type_id;

	/** @var integer ID of the current user */
	protected $curr_user;

	/** @var array The log Data to insert */
	protected $log_data = array();

	public function __construct()
	{
        $this->CI =& get_instance();

        //Load the model
        $this->CI->load->model('Logging/logging_model');
        $this->CI->load->model('tables/action_type_model');
        $this->CI->load->model('tables/action_model');

        //Load the System Action Type
        $this->sys_type_id = $this->CI->action_type_model->get_sys_type();

        $this->curr_user = $this->CI->session->user_id;
	}

	/**
	 * Inserts the provided log data into the database,
	 * after performing validation.
	 * @param  array $insert_data Associative array of the data
	 * @return boolean            TRUE if successful. FALSE if not.
	 */
	public function quick_log($insert_data)
	{
		$this->fill_data($insert_data);
		if ($this->validate($insert_data))
		{
			$this->log_data = $insert_data;
			return $this->CI->logging_model->insert_log($insert_data);
		}
		else
		{
			log_message('error', 'Log Data Failed in Validation. Aborting log');
			return FALSE;
		}
	}

	/**
	 * Sets the log's action id by passing an integer ID
	 * @param integer $id  The ID Number of the action
	 * @return Log_Builder This library. Method chaining
	 */
	public function action_id($id)
	{
		$this->log_action = $id;
		return $this;
	}

	/**
	 * Sets the Action to a system action.
	 * A system action has its type set to 'System'
	 * @param  string $action_name The name of the action
	 * @return Log_Builder         Logging library. For method chaining
	 */
	public function sys_action($action_name)
	{
		$sys_action = $this->CI->action_model->get_by_name($action_name);
		if (!$sys_action) //No system action
		{
			$insert_array = array(
				'action_name' => $action_name,
				'type_id'     => $this->sys_type_id,
				'is_active'   => FALSE
			);
			$sys_action_id = $this->CI->action_model->make($insert_array); //make returns ID
		}
		else
		{
			$sys_action_id = $sys_action->action_id;
		}

		//Set Action ID to $sys_action_id
		$this->log_action = $sys_action_id;

		return $this;
	}

	/**
	 * Sets the description. Or if it is set, appends to it.
	 * @param string $desc Description
	 * @return Log_Builder This library. Method chaining
	 */
	public function desc($desc)
	{

		$this->log_desc .= $desc;
		return $this;
	}

	/**
	 * Set the hours
	 * @param  integer $hours Hours to log
	 * @return Log_Builder         This libary. For method Chaining
	 */
	public function hours($hours)
	{
		$this->log_hours = $hours;
		return $this;
	}

	/**
	 * Sets the user creating the log
	 * @param  mixed $identifier The Identifier for the user. (i.e. ID, name)
	 * @param  string $type      How to indterpret the identifier (i.e. 'id', 'name')
	 * @return Log_Builder       Method Chaining
	 */
	public function user($identifier, $type = 'id')
	{
		$this->log_user = $identifier;
		return $this;
	}

	/**
	 * Sets the team in the log
	 * @param  mixed $identifier The identifier for the team (i.e. ID, name)
	 * @param  string $type      How to interpret the identifier. (i.e. 'id', 'name')
	 * @return Log_Builder       Method Chaining
	 */
	public function team($identifier, $type = 'id')
	{
		$this->log_team = $identifier;
		return $this;
	}

	/**
	 * Sets the project in the log
	 * @param  mixed $identifier The identifier for the project (i.e. ID, name)
	 * @param  string $type      How to interpret the identifier. (i.e. 'id', 'name')
	 * @return Log_Builder       Method Chaining
	 */
	public function project($identifier, $type = 'id')
	{
		$this->log_project = $identifier;
		return $this;
	}

	/**
	 * Sets the Date of the log
	 * Can pass 'now' to set both the time and date to now
	 * @param  mixes $date The date
	 * @return void       
	 */
	public function date($date)
	{
		if ($date == 'now')
		{
			$this->log_date = date('Y-m-d');
			$this->log_time = date('H:i:s');
		}
		else
		{
			$this->log_date = $date;
		}
		return $this;
	}

	/**
	 * Sets the time of the log.
	 * Can pass 'now' to set both the time and date to now
	 * @param  mixed $time The time
	 * @return void       
	 */
	public function time($time)
	{
		if ($time == 'now')
		{
			$this->log_date = date('Y-m-d');
			$this->log_time = date('H:i:s');
		}
		else
		{
			$this->log_time = $time;
		}
		return $this;
	}

	/**
	 * Builds the log data array and stores it as
	 * a property. The log data array is what will
	 * be passed onto the model after validation.
	 * @return void 
	 */
	public function build_log()
	{
		$sources = array(
			'action_id' => $this->log_action,
			'log_desc' => $this->log_desc,
			'hours' => $this->log_hours,
			'user_id' => $this->log_user,
			'team_id' => $this->log_team,
			'project_id' => $this->log_project,
			'log_date' => $this->log_date,
			'log_time' => $this->log_time,
		);

		foreach ($sources as $field => $data)
		{
			if (isset($data))
			{
				$this->log_data[$field] = $data;
			}
		}
	}

	/**
	 * Builds and Inserts the log based on the current
	 * data stored in the library
	 * @return boolean TRUE if inserted successfully. FALSE Otherwise
	 */
	public function log()
	{
		$this->build_log();
		$this->log_data = $this->fill_data($this->log_data);
		
		if ($this->validate($this->log_data))
		{
			$result =  $this->CI->logging_model->insert_log($this->log_data);
		}
		else
		{
			log_message('error', 'Log Data Failed in Validation. Aborting log');
			$result = FALSE;
		}

		$this->reset_stored_data();
		return $result;
	}

	/**
	 * Validates that the insert data has the required fields.
	 *
	 * @param  array $insert_data Optional. Insert Data to validate
	 * @return boolean     TRUE if valid. FALSE if not
	 */
	protected function validate($insert_data)
	{
		//Check Required Fields
		$required_fields = array(
			'action_id', 'user_id', 'log_date', 'log_time' 
		);
		foreach ($required_fields as $field)
		{
			if (!isset($insert_data[$field]))
			{
				//If field not set,
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * If certain fields are mising, this will set
	 * them to the default
	 * @param array $insert_data Insert Data with potentially missing fields.
	 * @return array The insert data with default fields
	 */
	public function fill_data($insert_data)
	{
		$defaults = array(
			'hours' => 0,
			'user_id' => $this->curr_user
		);
		foreach ($defaults as $default => $value)
		{
			if (!isset($insert_data[$default]))
			{
				$insert_data[$default] = $value;
			}
		}
		return $insert_data;
	}

	/**
	 * Resets the stored log data to their default
	 * values
	 * @return void 
	 */
	public function reset_stored_data()
	{
		$to_reset = array(
			'log_action' => NULL,
			'log_desc' => NULL,
			'log_hours' => 0,
			'log_user' => NULL,
			'log_team' => NULL,
			'log_project' => NULL,
			'log_date' => NULL,
			'log_time' => NULL
		);

		foreach ($to_reset as $item => $default)
		{
			$this->{$item} = $default;
		}

		$this->log_data = array();
	}

}

/* End of file Log_Builder.php */
/* Location: ./application/libraries/Log_Builder.php */
