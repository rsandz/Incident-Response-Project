<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics_model extends CI_Model {

	public function __constructor()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->helper('inflector');
		$this->load->model('search_model');
	}

	public function get_stats($table)
	{
		if (is_array($table))
		{
			//For arrays
			$tables = $table;
			foreach ($tables as $table)
			{
				$data[$table]['num_rows'] = $this->db->count_all($table);
			}
		}
		else
		{
			//Not Array. Single Table
			$data['num rows'] = $this->db->count_all($table);
		}

		return $data;
	}

	public function get_log_frequency($type, $user_id = NULL)
	{
		$user_id = $user_id ?: $this->session->user_id;

		$this->db->where('user_id', $user_id);

		//Get total logs
		$data['total_logs'] = $this->db->count_all_results('action_log');

		switch ($type)
		{
			case 'daily':
				//Get Logs per Day
				$this->db->where('user_id', $user_id)
					->group_by('log_date')
					->select('log_date, COUNT(log_id) AS amount')
					->order_by('log_date', 'DESC');
				$data['logData'] = $this->db->get('action_log')->result();
			break;

			case 'weekly':
				//Get Logs per Week
				$this->db->where('user_id', $user_id)
					->group_by('WEEKOFYEAR(log_date)')
					->select('DATE_SUB(log_date, INTERVAL (WEEKDAY(log_date) - 1) DAY) AS log_date, COUNT(log_id) AS amount')
					->order_by('log_date', 'DESC');
				$data['logData'] = $this->db->get('action_log')->result();
				break;

			case 'monthly':
				//Get Logs per Month
				$this->db->where('user_id', $user_id)
					->group_by('MONTH(log_date)')
					->select('CONCAT(MONTHNAME(log_date), " ", YEAR(log_date)) AS log_date, COUNT(log_id) AS amount')
					->order_by('YEAR(log_date)', 'ASC')
					->order_by('MONTH(log_date)', 'ASC');
				$data['logData'] = $this->db->get('action_log')->result();
				break;

			case 'yearly':
				//Get Logs per Year
				$this->db->where('user_id', $user_id)
					->group_by('YEAR(log_date)')
					->select('YEAR(log_date) AS log_date, COUNT(log_id) AS amount')
					->order_by('YEAR(log_date)', 'ASC');
				$data['logData'] = $this->db->get('action_log')->result();
				break;

			default:
				show_error('Incorrect date interval');
				break;
		}

		return $data;

	}



}

/* End of file Statistics_model.php */
/* Location: ./application/models/Statistics_model.php */