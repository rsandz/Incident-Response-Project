<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Investigate_base.php');

class Investigator extends Investigate_base
{
	/** @var int The incident's ID*/
	protected $incident_id;

	/** @var object The incident db result obj */
	protected $incident;

	/** @var int Timestamp of incident */
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
		$data['title'] = $this->incident_title($this->incident_id);
		$data['summary'] = $this->incident_info($this->incident_id);

		$html = $this->CI->load->view('incidents/templates/report', $data, TRUE);
		return $html;
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
