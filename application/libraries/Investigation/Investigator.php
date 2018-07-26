<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Investigate_base.php');

class Investigator extends Investigate_base
{
	protected $incident_id;
	protected $incident;

	/**
	 * Timestamp of Date and Time of the incident
	 * @var number
	 */
	protected $date_time;

	/**
	 * Constructor for the Investigator library
	 * 
	 * Loads all the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();

		$this->CI->load->model('Searching/search_model');
		$this->CI->load->model('statistics_model');
		$this->CI->load->library('table');
	}

	/**
	 * Sets the incident
	 * @param  int $id The ID of the incident
	 * @return Investigator     Method Chaining
	 */
	public function incident($id)
	{
		if ($this->CI->investigation_model->data_exists('incidents', array('incident_id' => $id)))
		{
			$this->incident_id = $id;
		}
		else
		{
			$this->error('Incident does not exist');
			return $this;
		}

		$this->incident = $this->CI->investigation_model->get_incident($this->incident_id);

		$this->date_time = strtotime($this->incident->incident_date.$this->incident->incident_time);
	}

	public function get_html_report()
	{
		$data['summary'] = $this->incident_summary();
		$data['test'] = $this->CI->table->my_generate($this->past_week_logs());

		$html = $this->CI->load->view('incidents/templates/report', $data, TRUE);
		return $html;
	}

	/**
	 * Modifies the superclass' summary method
	 * @return string incident summary as a string
	 */
	public function incident_summary($incident_id = NUll)
	{
		if (!isset($incident_id))
		{
			$incident_id = $this->incident_id;
		}
		return parent::incident_summary($incident_id);
	}

	public function past_week_logs()
	{
		return $this->CI->search_model
			->from_date(date('Y-m-d', $this->date_time - 604800)) //7 Days * 24 Hours * 60 mins * 60 sec
			->to_date(date('Y-m-d'), $this->date_time)
			->search();
	}

}

/* End of file Investigator.php */
/* Location: ./application/libraries/Investigation/Investigator.php */
