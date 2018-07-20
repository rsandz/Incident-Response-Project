<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Searching/Search_Model.php');

/**
 * Statistics Model
 * ================
 * @author Ryan Sandoval
 * @package Search
 * @uses Search_Model
 *
 * The statistics model allows for the generation of log frequency and hours
 * statistics based on the action log table. The statistics can be displayed 
 * on different time intervals, which are: Daily, Weekly, Monthly, Yearly.
 *
 * The statistics model extends the search model, so it must be loaded first
 * before this can be used. This may be done through CI's loader or simply
 * by using require_once(). 
 *
 * Since this model extends the search model, filters for the statistics can
 * be set using the standard search model methods. For example:
 * 		` ...->statistics_mode->keywords('Blue'); `
 * 	The above will show statistics on logs that contain the keyword 'Blue'
 *
 * For more information on the search_model's filters, @see Search_Model
 *
 * -------------------------------------------------------------------------
 *
 * Data Returns:
 *
 * The Statistics model follows the following conventions:
 * After calling a get_ command to get data (i.e. get_hours), the function 
 * should return an array containing the following:
 * 		- 'total' 	The total amount of data (# of logs, # of hours, etc.) that 	
 * 					that was retrieved.
 * 		- 'query' 	Should contain $this->export_query()
 * 					This is used for creating a search query when someone clicks 
 * 					on a graph. (You should do this even if you're not graphing
 * 					the data)
 * 		- 'stats'	This should contain the Data itself in a result array form.
 * 					(use ...->result() after a get statement)
 * 					Each element in the result array should contain:
 * 						x: The x-axis value (i.e. Day)
 * 						y: The y-axis value (i.e. Hours, # of Logs)
 * 					Example: `stats[15]->x` Should give the x-value of the 15 
 * 								row that matched the filters.
 * 
 *			
 */
class Statistics_model extends Search_Model {

	/**
	 * Loads the necessary resources to run the statistics model. 
	 */
	public function __construct()
	{
		parent:: __construct();
	}

	/**
	 * Gets the log frequency data
	 * @param  string $type Time interval type (i.e. 'Daily, Weekly, Monthly, Yearly')
	 * @return array        Array conaining data
	 */
	public function get_log_frequency($type)
	{
		$this->join_tables();
		$this->db->from('action_log');

		//Set the selection
		$this->select_log_frequency_data($type);

		//User lock sets user to current user
		if($this->user_lock)
		{
			$this->SB_users = $this->session->user_id;
		}

		//Apply where and like filters
		$this->apply_filters();

		//Get total logs
		$data['total_logs'] = $this->db->count_all_results('', FALSE);

		//Export the query so it can be used in searching later
		$data['query'] = $this->export_query();

		//Set Dubug info
		$this->set_debug();

		//Get the statistics data
		$data['stats'] = $this->db->get()->result();

		$this->reset();

		return $data;
	}

	/**
	 * Gets the hours data
	 * @param  string $type Time interval type (i.e. 'Daily, Weekly, Monthly, Yearly')
	 * @return array        Array conaining data
	 */
	public function get_hours($type)
	{
		$this->join_tables();
		$this->db->from('action_log');

		//Apply where and like filters
		$this->apply_filters();

		//Set the selection
		$this->select_hours_data($type);

		//Export the query so it can be used in searching later
		$data['query'] = $this->export_query();

		//Debug info
		$this->set_debug();

		//Get total hours
		$data['total'] = $this->db->count_all_results('', FALSE);

		//Get the data
		$data['stats'] = $this->db->get()->result();

		$this->reset();

		return $data;
	}


	public function select_log_frequency_data($type)
	{
		switch ($type)
		{
			case 'daily':
				$this->db
					->group_by('log_date')
					->select('log_date AS x, COUNT(*) AS y')
					->order_by('log_date', 'DESC');
			break;

			case 'weekly':
				$this->db
					->group_by('MONTH(log_date)')
					->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, COUNT(*) AS y')
					->order_by('YEAR(log_date)', 'ASC')
					->order_by('MONTH(log_date)', 'ASC');
				break;

			case 'monthly':
				$this->db
					->group_by('MONTH(log_date)')
					->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, COUNT(*) AS y')
					->order_by('YEAR(log_date)', 'ASC')
					->order_by('MONTH(log_date)', 'ASC');
				break;

			case 'yearly':
				$this->db
					->group_by('YEAR(log_date)')
					->select('YEAR(log_date) AS x, COUNT(*) AS y')
					->order_by('YEAR(log_date)', 'ASC');
				break;

			default:
				show_error('Incorrect date interval');
				break;
		}
	}

	public function select_hours_data($type)
	{
		switch ($type)
		{
			case 'daily':
				$this->db
					->group_by('log_date')
					->select('log_date AS x, SUM(hours) AS y')
					->order_by('log_date', 'DESC');
				break;

			case 'weekly':
				$this->db
					->group_by('WEEKOFYEAR(log_date)')
					->select('DATE_SUB(log_date, INTERVAL (WEEKDAY(log_date) - 1) DAY) AS x, SUM(hours) AS y')
					->order_by('log_date', 'DESC');
				break;

			case 'monthly':
				$this->db
					->group_by('MONTH(log_date)')
					->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, SUM(hours) AS y')
					->order_by('YEAR(log_date)', 'ASC')
					->order_by('MONTH(log_date)', 'ASC');
				break;

			case 'yearly':
				$this->db
					->group_by('YEAR(log_date)')
					->select('YEAR(log_date) AS x, SUM(hours) AS y')
					->order_by('YEAR(log_date)', 'ASC');
				break;

			default:
				show_error('Incorrect date interval');
				break;
		}
	}
}
/* End of file Statistics_model.php */
/* Location: ./application/models/Statistics_model.php */