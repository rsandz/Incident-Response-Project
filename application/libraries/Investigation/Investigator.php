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
		$data['last_10_table'] =$this->last_10_logs();
		$data['past_week_search'] = $this->past_week_search();
		$data['past_month_search'] = $this->past_month_search();

		$html = $this->CI->load->view('incidents/templates/report', $data, TRUE);
		return $html;
	}

	public function last_10_logs()
	{
		$table_data = $this->CI->search_model
					->to_date(date('Y-m-d'), $this->date_time)
					->pagination(10)
					->user_lock(FALSE)
					->select('name, action, time, date')
					->search();
		return $this->CI->table->my_generate($table_data);
	}

	public function past_week_search()
	{
		$data['query'] = $this->CI->search_model
				->from_date(date('Y-m-d', $this->date_time - 604800)) //7 Days * 24 Hours * 60 mins * 60 sec
				->to_date(date('Y-m-d'), $this->date_time)
				->export_query();
		$data['title'] = 'Past Week';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}
	
	public function past_month_search()
	{
		$data['query'] = $this->CI->search_model
				->from_date(date('Y-m-d', $this->date_time - 2678400 )) //31 Days * 24 Hours * 60 mins * 60 sec
				->to_date(date('Y-m-d'), $this->date_time)
				->export_query();
		$data['title'] = 'Past Month';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}

}

/* End of file Investigator.php */
/* Location: ./application/libraries/Investigation/Investigator.php */
