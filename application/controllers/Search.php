<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->helper('url');
		
		$this->load->model('logging_model');
		$this->load->model('search_model');

		if (!isset($this->session->user_id))
		{
			show_error('401 - Not Logged In', 401);
		} 
	}

	public function index()
	{
		//Data Setup
		$data['title'] = 'Search';
		$data['header'] = array(
			'colour' => 'is-info',
			'text'   => 'Search'
		);

		$this->form_validation->set_rules('keywords', 'Keywords', 'trim');
		$this->form_validation->set_rules('kfilters[]', 'Keyword Filters', 'trim|required');
		$this->form_validation->set_rules('to_date', 'Date', 'callback_validate_date');
		$this->form_validation->set_rules('action_types[]', 'Action Type', 'required');

		if ($this->form_validation->run()) {
			$this->results(FALSE); //Show search Matches
		} else {
			//Get the action types
			
			$data['action_types'] = $this->search_model->get_items('action_types');
			$data['teams'] = $this->search_model->get_items('teams');
			$data['projects'] = $this->search_model->get_items('projects');

			//Load the form
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('search/tabs');
			$this->load->view('search/search-form', $data);
		}
		
	}

	public function results($offset = 0)
	{
		// If offset is false, it is a new query. Otherwise, request is looking for next page of query.
		
		if ($offset === FALSE) 
		{
			//Create an array for the query
		
			$query = array(
				'keywords' => $this->parseResults($this->input->post('keywords', TRUE)),
				'keyword_filters' => $this->input->post('kfilters') !== NULL ? $this->input->post('kfilters', TRUE) : NULL,

				'from_date' => (string)$this->input->post('from_date' , TRUE),
				'to_date' => (string)$this->input->post('to_date', TRUE),

				'action_types' => $this->input->post('action_types[]', TRUE),

				'projects' => $this->input->post('projects[]', TRUE),
				'null_projects' => $this->input->post('null_projects', TRUE),
			
				'teams' => $this->input->post('teams[]', TRUE),
				'null_teams' => $this->input->post('null_teams', TRUE),
			);

			$this->session->set_userdata('search_query', $query);

			$offset = 0; //Reset Offset
		}
		else
		{
			$query = $this->session->search_query;
			// Retrieve array for query
		}

		

		//Keywords
		$keyword_ids = $this->search_model->keyword_search($query['keywords'], $query['keyword_filters']);
		
		//Filters
		$filter_ids = $this->search_model->filter_search($query);

		//Intersect ids
		$match_ids = array_intersect($keyword_ids, $filter_ids);

		$data = $this->search_model->get_logs($match_ids, $offset); //Gets Tables for logs

		$data['title'] = 'Search Results';
		$data['header'] = array(
			'colour' => 'is-info',
			'text'   => 'Results'
		);
		$data['query'] = $query;

		if ($data['table'] !== 'No Results') //No pages if there are no results! Otherwise, will cause error.
		{
			$this->load->helper('table_helper');
			$data['page_links'] = get_pagelinks($data, 'Search/results');
		}

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('search/view-logs', $data);
		$this->load->view('templates/footer', $data);
		$this->load->view('js/page-link-fix');
		
	}


	private function parseResults($string) 
	{
		//TODO: add tags in the future. i.e. not:
		$keywords = explode(' ', $string);

		foreach ($keywords as $key => $keyword) 
		{
			if ($keyword == "")
			{
				unset($keywords[$key]);
			}
		}

		return $keywords;
	}

	public function validate_date($to_date)
	{
		$from_date = strtotime($this->input->post('from_date', TRUE));
		$to_date = strtotime($to_date);

		$date_diff = $to_date - $from_date;

		if ($date_diff < 0)
		{
			$this->form_validation->set_message('validate_date', 'Invalid Date Interval - <strong>From Date</strong> is greater than <strong>To Date</strong>');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}


	////////////////
	//View Tables //
	////////////////

	public function view_tables($table = NULL, $offset = 0) 
	{

		if ($table === NULL)
		{
			//Display Dashboard for wish table to view.
			$data = array(
				'title' => 'View Tables',
				'header' => array(
					'text' => 'View Tables'
					),
				'tables' => array('actions', 'action_types', 'teams', 'projects', 'users')
			);
			
			//Get Statistics
			
			$data['stats'] = $this->search_model->get_stats($data['tables']);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('search/tabs', $data);
			$this->load->view('search/view_tables/table_selection');
		}
		else
		{
			$data = $this->search_model->get_table_data($table, $offset);
			$data['page_links'] = get_pagelinks($data, 'Search/view_tables/'.$table);

			$data['title'] = 'View Tables';
			$data['header']['text'] = 'View Table';

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('search/tabs', $data);
			$this->load->view('search/view_tables/view-table', $data);
		}

	}
}

/* End of file Search.php */
/* Location: ./application/controllers/Search.php */