<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Investigate_base.php');
use Carbon\Carbon;

/**	
 * Investigator Library
 * ====================
 * @author Ryan Sandoval
 * @version 1.1
 * @package Investigation
 * 
 * The Investigation Library handles incidents after they have 
 * been created. Its main purpose is to create reports that
 * will be displayed to the end user.
 * 
 */

class Investigator extends Investigate_base
{
	/** @var int The incident's ID*/
	protected $incident_id;

	/** @var CI_DB_result The incident db result obj */
	protected $incident;

	/** 
	 * @var Carbon Carbon Object of the dateTime of the incident 
	 * NOTE: Do not call add() or subtract() directly on this property.
	 */
	protected $date_time;

	/** @var int The amount of relevant logs to display */
	protected $relevant_logs_amount = 10;

	/**
	 * Constructor for the Investigator library
	 * 
	 * Loads all the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();

		$this->CI->load->model('Searching/search_model');
		$this->CI->load->model('Stats/statistics_model');
		$this->CI->load->model('Investigation/investigator_model');
		$this->CI->load->library('table');
		$this->CI->load->library('chart');
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

		$this->date_time = new Carbon($this->incident->incident_date.$this->incident->incident_time);
	}

	/**
	 * Creates the report and returns the report as a HTML
	 * If you would like to style the report, go to:'application/views/incidents/templates'
	 * 
	 * @return string
	 */
	public function get_html_report()
	{
		$data['title'] = $this->incident_title($this->incident_id);
		$data['summary'] = $this->incident_info($this->incident_id);
		$data['last_10_table'] =$this->past_10_logs();
		$data['past_week_search'] = $this->past_week_search();
		$data['past_month_search'] = $this->past_month_search();
		$data['past_3days_search'] = $this->past_3days_search();
		$data['past_week_all_stats'] = $this->past_week_all_stats();
		$data['relevant_logs'] = $this->relevant_logs();

		$html = $this->CI->load->view('incidents/templates/report', $data, TRUE);
		$html .= $this->CI->load->view('stats/graph-search-form', $data, TRUE);
		return $html;
	}

	// -------------------------------------------------------------

	/*
		Statistics Tables Methods
	*/

	/**
	 * Gets the data for the last 10 logs before the incident and creates
	 * an HTML table string out of them.
	 * @return string
	 */
	public function past_10_logs()
	{
		$table_data = $this->CI->search_model
					->to_date($this->date_time->format('Y-m-d'))
					->pagination(10)
					->user_lock(FALSE)
					->select('name, action, time, date')
					->search();
		return $this->CI->table->my_generate($table_data);
	}

	// -------------------------------------------------------------

	/*
		Chart Methods
		========================
	*/

	/**	
	 * Creates the HTML chart string for the past week
	 * logs and hours.
	 */
	public function past_week_all_stats()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data = $this->CI->statistics_model
				->to_date($dateTime->format('Y-m-d'))
				->from_date($dateTime->subWeek()->format('Y-m-d')) 
				->metrics('hours')
				->labels('Hours')
				->metrics('logs')
				->labels('Logs')
				->interval_type('daily')
				->get();
		
		return $this->CI->chart
			->title('Past Week Logs and Hours')
			->chart_data($data)
			->generate_static();
	}
	
	//----------------------------------------------------------------------
	
	/* 
		Quick Search Methods
		====================
		Edit style at 'application/views/incidents/templates/search-box.php'
	*/

	/**
	 * Gets a search query for a week before the incident and then
	 * creates an HTML string that shows a link to the search.
	 * @return string
	 */
	public function past_week_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
			->to_date($dateTime->format('Y-m-d'))
			->from_date($dateTime->subWeek()->format('Y-m-d')) 
			->export_query(TRUE);
		$data['title'] = 'Past Week';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}
	
	/**
	 * Gets a search query for a month before the incident and then
	 * creates an HTML string that shows a link to the search.
	 * @return string
	 */
	public function past_month_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
				->to_date($dateTime->format('Y-m-d'))
				->from_date($dateTime->subWeek()->format('Y-m-d'))
				->export_query(TRUE);
		$data['title'] = 'Past Month';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}
	
	/**
	 * Gets a search query for 3 days before the incident and then
	 * creates an HTML string that shows a link to the search.
	 * @return string
	 */
	public function past_3days_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
				->to_date($dateTime->format('Y-m-d'))
				->from_date($dateTime->subDays(3)->format('Y-m-d')) 
				->export_query(TRUE);
		$data['title'] = 'Past 3 Days';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}

	//-------------------------------------------------------------------------

	/**
	 * Gets the logs relevant to the incident.
	 * @return string HTML string to display the table
	 */
	public function relevant_logs()
	{
		$dateTime = clone $this->date_time;
		$scored_logs = $this->CI->investigator_model->score_logs_relevancy($this->date_time);

		//Get top relevant Logs
		$relevant_log_ids = array();
		foreach ($scored_logs->result() as $log)
		{
			$relevant_log_ids[] = $log->log_id;
		}
		$imploded_log_ids = implode(', ', $relevant_log_ids);
		
		//Get the log data to tabulate
		$result = $this->CI->search_model
			->user_lock(FALSE)
			->pagination($this->relevant_logs_amount)
			->to_date($dateTime->format('Y-m-d'))
			->custom_sort("FIELD(`log_id`, {$imploded_log_ids})")
			->search_for_logs($relevant_log_ids);

		return $this->CI->table->my_generate($result);
	}

}

/* End of file Investigator.php */
/* Location: ./application/libraries/Investigation/Investigator.php */
