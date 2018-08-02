<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Investigate_base.php');
use Carbon\Carbon;

/**	
 * Investigator Library
 * ====================
 * @author Ryan Sandoval
 * @version 1.0
 * @package Investigation
 * 
 * The Investigation Library handles incidents after they have 
 * been created. Its main purpose is to create reports that
 * will be displayed to the end user.
 */

 //TODO IMPLEMENT CARBON
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
		$data['past_week_hrs_logs'] = $this->past_week_hrs_logs();

		$html = $this->CI->load->view('incidents/templates/report', $data, TRUE);
		$html .= $this->CI->load->view('stats/graph-search-form', $data, TRUE);
		return $html;
	}

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

	/**	
	 * 
	 */
	public function past_week_hrs_logs()
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

	/**
	 * Gets a search query for a week before the incident and then
	 * creates an HTML string that shows a link to the search.
	 * Edit the style at 'application/views/incidents/templates/search-box.php'
	 * @return string
	 */
	public function past_week_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
			->to_date($dateTime->format('Y-m-d'))
			->from_date($dateTime->subWeek()->format('Y-m-d')) 
			->export_query();
		$data['title'] = 'Past Week';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}
	
	/**
	 * Gets a search query for a month before the incident and then
	 * creates an HTML string that shows a link to the search.
	 * Edit the style at 'application/views/incidents/templates/search-box.php'
	 * @return string
	 */
	public function past_month_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
				->to_date($dateTime->format('Y-m-d'))
				->from_date($dateTime->subWeek()->format('Y-m-d'))
				->export_query();
		$data['title'] = 'Past Month';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}
	
	public function past_3days_search()
	{
		//Make a clone so we can modify without afecting original
		$dateTime = clone $this->date_time; 
		$data['query'] = $this->CI->search_model
				->to_date($dateTime->format('Y-m-d'))
				->from_date($dateTime->subDays(3)->format('Y-m-d')) 
				->export_query();
		$data['title'] = 'Past 3 Days';
		return $this->CI->load->view('incidents/templates/search-box', $data, TRUE);
	}

}

/* End of file Investigator.php */
/* Location: ./application/libraries/Investigation/Investigator.php */
