<?php
class logging_model extends CI_model {
	public function __construct() {
		parent:: __construct();
		$this->load->database(); //load database
		$this->load->library('session');
	}
	public function log_action() { //inserts the log into the data base
		$data = array(
			'action_id' => $this->input->post('action'),
			'log_desc' => $this->input->post('desc'),
			'log_date' => $this->input->post('date'),
			'log_time' => $this->input->post('time'),
			'project_id' => $this->input->post('project'),
			'team_id' => $this->input->post('team'),
			'user_id' => $this->session->user_id
			);
		return $this->db->insert('action_log', $data);
	}

	public function get_info($table) { //Gets and returns table array
		$query = $this->db->get($table);
		return $query->result_array();
	}


	public function get_entries_table() {

		/*
			TODO:
				- Store Table in session **
				- Query only when logging in and after form submission
		*/

		$this->db->where('user_id', $this->session->user_id)
			->select('action_name, project_name, log_desc, log_date, log_time')
			->join('actions','actions.action_id = action_log.action_id')
			->join('projects','projects.project_id = action_log.project_id')
			->order_by( 'log_date', 'log_time', 'ASC')
			->limit(10);
		
		$sess_data = array(
			'prev_entries' => $this->db->get('action_log'));

			$this->session->set_userdata($sess_data); 
		//load required libraries
		if ($this->load->is_loaded('table') == FALSE) {
			$this->load->library('table');
		}

		$prev_entries = $this->session->prev_entries;

		/*
			TABLE AESTHETICS SETUP
		*/

		$template = array(
        'table_open'            => '<table class="table is-striped is-fullwidth">',

        'thead_open'            => '<thead class="thead">',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr class="tr">',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th class="th">',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody class="tbody">',
        'tbody_close'           => '</tbody>',

        'row_start'             => '<tr class="tr">',
        'row_end'               => '</tr>',
        'cell_start'            => '<td class="td">',
        'cell_end'              => '</td>',

        'table_close'           => '</table>'
		);

		$this->table->set_template($template);

		$this->table->set_heading(array('Action Name','Project', 'Log Description', 'Log Date', 'Log Time'));
		return $this->table->generate($prev_entries);
	}




}