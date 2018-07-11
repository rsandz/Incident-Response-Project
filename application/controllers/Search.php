<?php
/**
 * Search Controller
 * =================
 * Written by: Ryan Sandoval, May 2018
 *
 * This is the controller for the search functions of the application.
 *
 *  It handles two types of searching:
 * 	1. Keyword Searching
 * 		- Uses the MySQL 'like' command to look for keyword instances in rows. i.e. "SELECT * FROM table WHERE column LIKE %keyword%".
 * 			Can be filtered to search for keywords only in certain columns (e.g. names only). 
 * 			For more information on the way code igniter handeles the 'like' query, 
 * 			@see https://www.codeigniter.com/userguide3/database/query_builder.html#looking-for-similar-data
 * 			
 * 	2. Filter Searching
 * 		- Uses checkboxes for user to select. If the checkbox for a certain value is checked, (i.e. project1) then the search query will return
 * 			results on rows with that value (project1). If the checkbox is not checked, then the search will not return any rows with that keyword. 
 * 			(e.g. if 'project2' is checkbox is not checked, then no results will contain project2)
 * 	
 * 	These 2 results are combined by intersection. So, a result will only be shown if it shows up in the valid results for both keyword searching and
 * 		filter searching
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	/**
	 * Constructor for the Search Controller
	 * Loads SOME of the required libraries, helpers and models. Also contains a check for user_login
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->helper('url');
		
		$this->load->model('statistics_model');
		$this->load->model('search_model');

		$this->load->helper('user');
		check_login(TRUE);
	}

	/**
	 * Main Method for the search page. 
	 * Shows Interface for searching by keyword and filters.
	 * 	
	 */
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

		if ($this->form_validation->run()) {
			$this->search(); //Show search Matches
		} else {
			//Get the action types
			
			$data['action_types'] = $this->search_model->get_items('action_types');
			$data['teams'] = $this->search_model->get_items('teams');
			$data['projects'] = $this->search_model->get_items('projects');
			$data['users'] = $this->search_model->get_items('users');

			//Load the form
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('search/tabs');
			$this->load->view('search/search-form', $data);
		}
		
	}

	/**
	 * Shows the results of a search querry
	 * @param  integer $offset Offset for pagination. @see search_model->get_table_data() and codeigniter pagination documentation 
	 */
	public function search($offset = 0)
	{
		$user_lock = ($this->session->privileges === 'admin') ? FALSE : TRUE;
		$this->load->helper('search_helper');
		// If there is a post request method, then it is a new query. Otherwise, its just a pagination request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
		{
			if(!empty($this->input->post('query', TRUE)))
			{
				//Use the premade query already in post array
				//This query must be posted in json format
				$query = query_in_post();
			}
			elseif(!empty($this->input->post('query_index', TRUE)))
			{
				//Used a stored query since query_index is defined
				$query = query_from_session($this->input->post('query_index', TRUE));
			}
			else
			{
				$query = query_from_post();
			}
			$this->session->set_userdata('search_query', $query);
			$offset = 0; //Reset Offset
		}
		else
		{
			$query = $this->session->search_query;
			// Retrieve array for query
		}
		//Keywords
		$keyword_ids = $this->search_model->keyword_search($query);
		
		//Filters
		$filter_ids = $this->search_model->filter_search($query);

		//Intersect ids
		if (!empty($keyword_ids) && !empty($filter_ids))
		{
			$match_ids = array_intersect($keyword_ids, $filter_ids);
			$data = $this->search_model->get_logs_table($match_ids, $offset); //Gets Tables for logs

		}
		else
		{
			$data = $this->search_model->get_logs_table($filter_ids OR $keyword_ids, $offset); //One of the ids will be chosen
		}

		$data['title'] = 'Search Results';
		$data['header'] = array(
			'colour' => 'is-info',
			'text'   => 'Results'
		);
		$data['query'] = $query;

		//No pagination needed if there are no results! Otherwise, will cause error if pagination is called.
		if ($data['table'] !== 'No Results') 
		{
			$this->load->library('pagination');
			$data['page_links'] = $this->pagination->my_create_links($data['num_rows'], 'search/result/');
		}
		
		$data['back_url'] = $query['back_url'];
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('search/view-logs', $data);
		$this->load->view('templates/footer', $data);

	}

	/**
	 * Controller for the view tables tab.
	 * 
	 * @param  string  $table  The name of the table to show. If Null, will show the table selection screen
	 * @param  integer $offset Offset for pagination. @see search_model->get_table_data() and codeigniter pagination documentation 
	 */
	public function view_tables($table = NULL, $offset = 0) 
	{
		$this->load->library('pagination');

		if ($table === NULL)
		{
			//Display Dashboard for which table to view.
			$data = array(
				'title' => 'View Tables',
				'header' => array(
					'text' => 'View Tables'
					),
				'tables' => array('actions', 'action_types', 'teams', 'projects', 'users', 'user_teams')
			);
			
			//Get Statistics
			
			$data['stats'] = $this->statistics_model->get_table_stats($data['tables']);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('search/tabs', $data);
			$this->load->view('search/view_tables/table_selection');
		}
		else
		{
			$data = $this->search_model->tabulate_table($table, $offset);
			$data['page_links'] = $this->pagination->my_create_links($data['num_rows'], 'Search/view_tables/'.$table);

			$data['title'] = 'View Tables';
			$data['header']['text'] = 'View Table';

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('search/tabs', $data);
			$this->load->view('search/view_tables/view-table', $data);
		}

	}

	/**
	 * Returns True if the give '$to_date' is after the '$from_date'.
	 * '$from_date' is retrieved using the post array while '$to_date' must be recieved as an argument
	 * 
	 * Function used in the code igniter form validation. 
	 * @see https://www.codeigniter.com/userguide3/libraries/form_validation.html#callbacks-your-own-validation-methods doc
	 * for more information on custom callbacks
	 * 
	 * @param  string $to_date Date to check
	 * @return Boolean         True if valid, False if not
	 */
	public function validate_date($to_date)
	{
		$from_date = strtotime($this->input->post('from_date', TRUE));
		$to_date = strtotime($to_date);

		$date_diff = $to_date - $from_date;

		if ($date_diff < 0)
		{
			$this->form_validation->set_message(
				'validate_date', 'Invalid Date Interval - <strong>From Date</strong> is greater than <strong>To Date</strong>'
			);
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

/* End of file Search.php */
/* Location: ./application/controllers/Search.php */