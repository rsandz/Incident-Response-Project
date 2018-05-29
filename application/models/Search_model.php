<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model {

	public function __construct()
	{
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->helper('inflector');
		$this->load->library('table');

		$this->load->model('logging_model');
	}

	public function filter_search($query) 
	{
		// Join statements
		$this->db
			->join('actions','actions.action_id = action_log.action_id', 'left')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->select('action_log.log_id');

		// Date Filter - Uses from_date and to_date
		
		if ($query['from_date'] !== "")
		{
			$this->db->where('log_date >=',$query['from_date']);
		}

		if (($query['to_date']) !== "")
		{
			$this->db->where('log_date <=',$query['to_date']);
		}

		// Type Filter - Based on type
		
		$this->db->group_start();
		foreach ($query['action_types'] as $action_type) 
		{
			$this->db->or_where('actions.type_id', $action_type);
		}
		$this->db->group_end();

		// Team Filter
		
		$this->db->group_start();
		if (is_array($query['teams']))
		{
			foreach ($query['teams'] as $team) 
			{
				$this->db->or_where('action_log.team_id', $team);
			}
				
		}

		if ($query['null_teams'])
		{
			$this->db->or_where('action_log.team_id IS NULL');
		}
		$this->db->group_end();	
		//////////////////////////////////////////

		// Project Filter
		
		$this->db->group_start();

		if (is_array($query['projects']))
		{
			foreach ($query['projects'] as $project) 
			{
				$this->db->or_where('action_log.project_id', $project);
			}
		}

		if ($query['null_projects'])
		{
			$this->db->or_where('action_log.project_id IS NULL');
		}

		$this->db->group_end();
		///////////////////////////////////////

		////////////////////////////////
		//Get ids that match criteria //
		////////////////////////////////
		$this->db->select('log_id');

		$filter_ids  = array();
		foreach ($this->db->get('action_log')->result() as $result)
		{
			array_push($filter_ids, $result->log_id);
		}
		
		return $filter_ids;
	}

	public function keyword_search($keywords, $columns) 
	{
		if (!isset($columns))
		{
			return FALSE;
		}

		//NOTICE! I didn't check if $keywords is empty because codeigniter still puts into the query a wild card if the like condition is null. So what ends up happening is that we get all the results if $keyword is empty, WHICH is EXACTLY what we want!

		//Join all tables so that we can query information all at the same time.
		$this->db
			->join('actions','actions.action_id = action_log.action_id')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->join('projects','projects.project_id = actions.project_id', 'left')
			->join('teams','teams.team_id = action_log.team_id', 'left')
			->join('users','users.user_id = action_log.user_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC')
			->select('action_log.log_id');

		foreach ($keywords as $keyword) 
		{
			//Will be using 'OR' between keywords		
			$this->db->or_group_start();
			foreach ($columns as $column) 
			{
				$this->db->or_like($column, $keyword);
			}
			$this->db->group_end();

		}
		$data = array();
		foreach ($this->db->get('action_log')->result() as $result)
		{
			array_push($data, $result->log_id);
		}

		return $data;
	}
	
	public function get_logs($log_ids, $offset = 0)
	{
		$this->load->helper('table_helper');

		//Logs per page
		$per_page = 10;
		$data['per_page'] = 10;

		//Join and select
		$this->db
			->select('name, action_name, type_name, project_name, team_name, log_desc, log_date, log_time')
			->join('actions','actions.action_id = action_log.action_id')
			->join('users','users.user_id = action_log.user_id', 'left')
			->join('action_types','actions.type_id = action_types.type_id', 'left')
			->join('projects','projects.project_id = actions.project_id', 'left')
			->join('teams','teams.team_id = action_log.team_id', 'left')
			->order_by( 'log_date', 'DESC')
			->order_by( 'log_time', 'DESC');

		if (is_array($log_ids))
		{
			if (count($log_ids) > 0)
			{
				$this->db->where_in('log_id', $log_ids);
			}
			else
			{
				$data['table'] = 'No Results';
				return $data;
			}
		}
		else
		{
			$data['table'] = 'No Results';
			return $data;
		}

		//Get entries
		$matches = $this->db->get('action_log');

		$data['num_rows'] = $matches->num_rows();
		$data['table_data'] = array_slice($matches->result_array(), $offset, $per_page); //Offset for pagination
		$data['heading'] = array('Name', 'Action Name', 'Action Type', 'Project', 'Team', 'Log Description', 'Log Date', 'Log Time');

		$data['table'] = generate_table($data);
		
		return $data;

	}

	public function get_table_data($table, $offset, $per_page)
	{
		$this->load->helper('inflector');
		$this->load->helper('table_helper');

		//Get Table DATA
		$query = $this->logging_model->get_items_raw($table);
		$data['table_data'] = array_slice($query->result_array(), $offset, $per_page); //Offset for pagination

		$column_headings = $this->logging_model->get_field_data($table, TRUE);

		$data['heading'] = array();

		foreach ($column_headings as $heading) 
		{
			array_push($data['heading'], humanize($heading->name));
		}

		//Create The table
		$data['table'] = generate_table($data);
		$data['num_rows'] = $query->num_rows();
		$data['table_name'] = $table;

		return $data;
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


}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */