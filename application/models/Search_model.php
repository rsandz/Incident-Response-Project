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
			->join('actions','actions.action_id = action_log.action_id');

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
		
		foreach ($query['action_types'] as $action_type) 
		{
			$this->db->or_where('type_id', $action_type);
		}
		

		//Get ids that match criteria
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

		$matches = array_slice($matches->result_array(), $offset, $per_page); //Offset for pagination
		$this->table->set_heading(array('Name', 'Action Name', 'Action Type', 'Project', 'Team', 'Log Description', 'Log Date', 'Log Time'));
		
		///////////////////////////
		//TABLE AESTHETICS SETUP //
		///////////////////////////

		$template = array(
        'table_open'            => '<table class="table is-striped is-fullwidth">',

        'thead_open'            => '<thead class="thead">',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr class="tr">',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th class="th">',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody class="tbody">',
		'tbody_close'		 	=> '</tbody>',

        'row_start'             => '<tr class="tr">',
        'row_end'               => '</tr>',
        'cell_start'            => '<td class="td">',
        'cell_end'              => '</td>',

        'table_close'           => '</table>'
		);

		$this->table->set_template($template);

		$data['table'] = $this->table->generate($matches);
		return $data;

	}

}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */