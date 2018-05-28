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

		$this->form_validation->set_rules('kfilters[]', 'Keyword Filters', 'trim|required');
		$this->form_validation->set_rules('to_date', 'Date', 'callback_validate_date');
		$this->form_validation->set_rules('action_types[]', 'Action Type', 'required');

		if ($this->form_validation->run()) {
			$this->results(FALSE); //Show search Matches
		} else {
			//Get the action types
			
			$data['action_types'] = $this->logging_model->get_items('action_types');

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
				'keywords' => $this->parseResults($this->input->post('keywords')),
				'keyword_filters' => $this->input->post('kfilters') !== NULL ? $this->input->post('kfilters') : NULL,

				'from_date' => (string)$this->input->post('from_date'),
				'to_date' => (string)$this->input->post('to_date'),

				'action_types' => $this->input->post('action_types[]'),
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

		$this->view_logs($data);
		
	}


	private function parseResults($string) 
	{
		//TODO: add tags in the future. i.e. not:
		return explode(' ', $string);
	}

	public function validate_date($to_date)
	{
		$from_date = strtotime($this->input->post('from_date'));
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

	/**
	 * Views the entire action log
	 *
	 * @see index() 
	 * 
	 * @param array $data Data Array containing page and search data.
	 * @param int $page Page Number.
	 */
	public function view_logs($data) 
	{

		$this->load->library('pagination');
		
		$config['base_url']       = site_url('Search/results');
		$config['total_rows']     = $data['num_rows'];
		$config['per_page']       = $data['per_page'];
		$config['num_tag_open']   = '<div class="pagination-link">';
		$config['num_tag_close']  = '</div>';
		$config['cur_tag_open']   = '<div class="pagination-link is-current">';
		$config['cur_tag_close']  = '</div>';
		$config['next_link']      = 'Next';
		$config['next_tag_open']  = '<div class="pagination-next">';
		$config['next_tag_close'] = '</div>';
		$config['prev_link']      = 'Previous';
		$config['prev_tag_open']  = '<div class="pagination-previous">';
		$config['prev_tag_close'] = '</div>';
		$config['first_tag_open']  = '<div class="pagination-next">';
		$config['first_tag_close'] = '</div>';
		$config['last_tag_open']  = '<div class="pagination-previous">';
		$config['last_tag_close'] = '</div>';

		$this->pagination->initialize($config);

		$data['page_links'] = $this->pagination->create_links();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('search/view-logs', $data);
		$this->load->view('templates/footer', $data);
		$this->load->view('js/page-link-fix');
	}

}

/* End of file Search.php */
/* Location: ./application/controllers/Search.php */