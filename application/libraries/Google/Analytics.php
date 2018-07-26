<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analytics
{
	protected $CI;
	protected $auth_file;
	protected $client;
	protected $analytics;
	
	protected $metrics_array = array();
	protected $date_range;
	protected $view_id;

	protected $reports;

	protected $data;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		
		//Load the Configuration
		$this->CI->load->config('analytics');
		
		// Use the developers console and download your service account
		// credentials in JSON format. 
		$this->auth_file = $this->CI->config->item('auth_file');
		
		//Init CLient
		$this->client = new Google_Client();
		$this->client->setApplicationName("Hello Analytics Reporting");
		$this->client->setAuthConfig($this->auth_file);
		$this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
		
		//Init Analytics
		$this->analytics = new Google_Service_AnalyticsReporting($this->client);
		
		//Initialize defaults for the stored data
		$this->reset();
		
	}
	
	/**
	* Sets the date range for the analytics
	*
	* If only the from date is provided. (To date is null, or not provided), then
	* the data range will simply be the from_date.
	*   Example: date_range('yeterday') will set the date range to yesterday
	* @param  string $from_date The start date for the google analytics query
	* @param  string $to_date   The end date for the gogle analytics query. Optional
	* @return Analytics         Method Chaining
	*/
	public function date($from_date, $to_date = null)
	{
		if (!isset($to_date)) {
			$this->date_range->setStartDate($from_date);
			$this->date_range->setEndDate($from_date);
		} else {
			$this->date_range->setStartDate($from_date);
			$this->date_range->setEndDate($to_date);
		}
		
		return $this;
	}
	
	/**
	* Sets the metrics to search for using analytics
	*
	* @param  array|string $metrics
	*     The metrics to search as an:
	*     - Associative array containing keys as the metric expression
	*         and value as the alias
	*       - String for the expression
	* @param string $alias
	*     The alias for the metric being retrieve only use if $metrics
	*     (1st param)is a string
	*
	* @return Analytics             For Method chaining
	*/
	public function metrics($metrics, $alias)
	{
		if (!is_array($metrics)) {
			//Convert into an array containing one element
			$metrics = array($metrics);
		}
		
		foreach ($metrics as $metric) {
			$metric_obj = new Google_Service_AnalyticsReporting_Metric();
			$metric_obj->setExpression($metric);
			$metric_obj->setAlias($alias);
			
			$this->metrics_array[] = $metric_obj;
		}
		
		return $this;
	}
	
	/**
	* Sets the View ID for the Analytics request
	*
	* @param string $view_id The View Id you want to set it to
	* @return Analytics Method Chaining
	*/
	public function view_id($view_id)
	{
		$this->view_id = $view_id;
		return $this;
	}
	
	/**
	* Gets the analytics for the current stored data
	*
	* @return void
	*/
	public function get_analytics()
	{
		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($this->view_id);
		$request->setDateRanges($this->date_range);
		$request->setMetrics($this->metrics_array);
		
		$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
		$body->setReportRequests($request);
		$this->reports = $this->analytics->reports->batchGet($body);
	}

	/**
	 * Parses the Stored Report
	 *
	 * @return void
	 */
	public function parse_report()
	{
		foreach ($this->reports as $report)
		{
			$header = $report->getColumnHeader();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();
			
			$metrics = $rows[0]->getMetrics(); //Only wany first row of metrics
			$values = $metrics[0]->getValues(); //Only want first row of values

			//Values contains the values for each metricheader (aka metric expressions)
			foreach ($values as $index => $value)
			{
				$metric_name = $metricHeaders[$index]->name;

				//Sets the data as 'metric_name' => 'value' 
				$data[$metric_name] =  $value;
			}
		}

		$this->data =  $data;
	}

	/**
	 * Gets the report based on the currently stored values.
	 * Returns an array containing the metrics and their values
	 *
	 * @return array The associative of metrics and their values
	 */
	public function get_report()
	{
		$this->get_analytics();
		$this->parse_report();
		
		return $this->data;
	}
	
	/**
	* Resets all the stored Data to their defaults according
	* to the $to_reset array set below
	* @return void
	*/
	public function reset()
	{
		$to_reset = array(
			'metrics_array' => array(),
			'date_range' => new Google_Service_AnalyticsReporting_DateRange(),
			'view_id'	=> $this->CI->config->item('default_view')
		);
		
		foreach ($to_reset as $field => $value) {
			$this->{$field} = $value;
		}
	}
	
}

/* End of file G_analytics.php */
/* Location: ./application/libraries/Google/G_analytics.php */
