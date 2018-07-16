<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Statistics Model
 * ================
 * @author Ryan Sandoval
 *
 * This handles database interation pertaining to 
 * statistics. i.e. Count(*) for a specific time
 * interval.
 */
class Statistics_model extends MY_Model {

	/**
	 * Loads the necessary resources to run the statistics model. 
	 */
	public function __construct()
	{
		parent:: __construct();

		$this->load->model('search_model');
	}

	public function get_log_frequency($type, $query)
	{
		$this->search_model->join_tables();
		$this->search_model->apply_query_filters($query);
		$this->search_model->apply_query_keywords($query);

		//Get total logs
		$data['total_logs'] = $this->db->count_all_results('action_log');

		$this->search_model->join_tables();
		$this->search_model->apply_query_filters($query);
		$this->search_model->apply_query_keywords($query);

		//Get the statistics data
		$data['stats'] = $this->get_log_frequency_data($type);
		//Also return the query in the data
		$data['query'] = $query;
		
		return $data;
	}

	public function get_hours($type, $query)
	{
		$this->search_model->join_tables();
		$this->search_model->apply_query_filters($query);
		$this->search_model->apply_query_keywords($query);

		//Get total hours
		$data['total'] = $this->db->count_all_results('action_log');

		$this->search_model->join_tables();
		$this->search_model->apply_query_filters($query);
		$this->search_model->apply_query_keywords($query);

		//Get the data
		$data['stats'] = $this->get_hours_data($type);
		//Also return the query in the data
		$data['query'] = $query;

		return $data;
	}

	/**
	 * Gets log frequency data based on the interval type (Defined by $type)
	 *
	 * Use db->where, db->where_in before calling this method to
	 * get data on specific conditions.
	 * 
	 * @param  string $type The date interval type
	 * @return array       Results Array of objects
	 */
	public function get_log_frequency_data($type)
	{
		switch ($type)
		{
			case 'daily':
				//Get Logs per Day
				$data = $this->get_daily_log_frequency();
			break;

			case 'weekly':
				//Get Logs per Week
				$data = $this->get_weekly_log_frequency();
				break;

			case 'monthly':
				//Get Logs per Month
				$data = $this->get_monthly_log_frequency();
				break;

			case 'yearly':
				//Get Logs per Year
				$data = $this->get_yearly_log_frequency();
				break;

			default:
				show_error('Incorrect date interval');
				break;
		}

		return $data;
	}

	/**
	 * Gets hours data based on the interval type (Defined by $type)
	 *
	 * Use db->where, db->where_in before calling this method to
	 * get data on specific conditions.
	 * 
	 * @param  string $type The time interval
	 * @return array       Results array of objects. See CI Docs.
	 */
	public function get_hours_data($type)
	{
		switch ($type)
		{
			case 'daily':
				//Get Logs per Day
				$data = $this->get_daily_hours();
			break;

			case 'weekly':
				//Get Logs per Week
				$data = $this->get_weekly_hours();
				break;

			case 'monthly':
				//Get Logs per Month
				$data = $this->get_monthly_hours();
				break;

			case 'yearly':
				//Get Logs per Year
				$data = $this->get_yearly_hours();
				break;

			default:
				show_error('Incorrect date interval');
				break;
		}

		return $data;
	}

	/**
	 * Gets the daily hours
	 * Can be made to get hours data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 */
	public function get_daily_hours($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('log_date')
			->select('log_date AS x, SUM(hours) AS y')
			->order_by('log_date', 'DESC');
		return $this->db->get('action_log')->result();
	}
	/**
	 * Gets the weekly hours
	 * Can be made to get hours data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of hours are named as y
	 */
	public function get_weekly_hours($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('WEEKOFYEAR(log_date)')
			->select('DATE_SUB(log_date, INTERVAL (WEEKDAY(log_date) - 1) DAY) AS x, SUM(hours) AS y')
			->order_by('log_date', 'DESC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the monthly hours
	 * Can be made to get hours data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of hours are named as y
	 */
	public function get_monthly_hours($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('MONTH(log_date)')
			->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, SUM(hours) AS y')
			->order_by('YEAR(log_date)', 'ASC')
			->order_by('MONTH(log_date)', 'ASC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the yearly hours
	 * Can be made to get hours data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of hours are named as y
	 */
	public function get_yearly_hours($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('YEAR(log_date)')
			->select('YEAR(log_date) AS x, SUM(hours) AS y')
			->order_by('YEAR(log_date)', 'ASC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the daily logs frequency
	 * Can be made to get logs frequency data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of logs are named as y
	 */
	public function get_daily_log_frequency($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('log_date')
			->select('log_date AS x, COUNT(*) AS y')
			->order_by('log_date', 'DESC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the weekly logs frequency
	 * Can be made to get logs frequency data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of logs are named as y
	 */
	public function get_weekly_log_frequency($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('MONTH(log_date)')
			->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, COUNT(*) AS y')
			->order_by('YEAR(log_date)', 'ASC')
			->order_by('MONTH(log_date)', 'ASC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the monthly logs frequency
	 * Can be made to get logs frequency data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of logs are named as y
	 */
	public function get_monthly_log_frequency($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('MONTH(log_date)')
			->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS x, COUNT(*) AS y')
			->order_by('YEAR(log_date)', 'ASC')
			->order_by('MONTH(log_date)', 'ASC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Gets the yearly logs frequency
	 * Can be made to get logs frequency data using where conditions
	 * @param  array $where Where conditions
	 * @return array        Code Igniter result object array
	 *                      Note the following conventions:
	 *                      	- Date intervals are named as x
	 *                      	- Amount of logs are named as y
	 */
	public function get_yearly_log_frequency($where = NULL)
	{
		$this->user_id_lock();
		if (!empty($where))
		{
			$this->db->where($where);
		}
		$this->db
			->group_by('YEAR(log_date)')
			->select('YEAR(log_date) AS x, COUNT(*) AS y')
			->order_by('YEAR(log_date)', 'ASC');
		return $this->db->get('action_log')->result();
	}

	/**
	 * Use to lock the search to the current user_id if 
	 * the user is not admin
	 * @param boolean|string $check_privilege If given a string, will check the user privilege to match 
	 *                                        the given string.
	 *                                        If set to FALSE, will lock the current user_id regardless
	 * 
	 * @return boolean True if Sucessful.
	 */
	public function user_id_lock($match_privilege = 'admin')
	{
		if ($this->session->privileges !== $match_privilege)
		{
			//lock user_id
			$this->db->where('user_id', $this->session->user_id);
		}

		return TRUE;
	}

	/**
	 * Get previous user entries from the action log
	 * @param  int  $offset 		 Offset for pagination
	 * @return array 				 Contains Table and Number of Rows                
	 */	
	public function get_my_entries_table(int $offset = 0) {
		//load required libraries
		$this->load->model('search_model');
		$this->load->library('table');
		$this->load->config('appconfig');

		$this->db
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->where('user_id', $this->session->user_id);

		$this->search_model->sql_commands_for_table('prev_entries'); 
		//Prev Entries is a config field. The search model will try to find it in the table config.

		$query = $this->db->get('action_log');

		$table_data = array_slice($query->result_array(), $offset, $this->config->item('per_page'));

		$this->table->heading_from_config('prev_entries');
		return $this->table->my_generate($table_data);
	}

}
/* End of file Statistics_model.php */
/* Location: ./application/models/Statistics_model.php */