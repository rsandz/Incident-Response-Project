<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
class Search extends MY_Controller {

	/**
	 * Constructor for the Search Controller
	 * Loads SOME of the required libraries, helpers and models. Also contains a check for user_login
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->helper('form');
		$this->load->helper('search');
		
		$this->load->model('get_model');

		$this->authentication->check_login(TRUE);
		$this->load->model('Searching/search_model');
		$this->search_model->user_lock(!$this->authentication->check_admin());


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
			//Get form Data
			$is_admin = $this->authentication->check_admin();
			if (!$is_admin)
			{
				$user_id = $this->session->user_id;
			}
			else
			{
				$user_id = NULL;
			}
			
			$data['action_types'] = $this->get_model->get_action_types($is_admin);
			$data['teams'] = $this->get_model->get_teams($is_admin);
			$data['projects'] = $this->get_model->get_projects($is_admin);
			$data['users'] = $this->get_model->get_users($user_id);
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
		
		// If there is a post request method, then it is a new query. Otherwise, its just a pagination request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
		{
			if(!empty($this->input->post('query', TRUE)))
			{
				//Use a query inside post since query is defined
				$this->search_model->import_query($this->input->post('query', TRUE));

				if (!empty($this->input->post('from_date' , TRUE)))
				{
					$this->search_model->from_date($this->input->post('from_date' , TRUE));
				}
				if(!empty($this->input->post('to_date', TRUE)))
				{
					$this->search_model->to_date($this->input->post('to_date', TRUE));
				}
			}
			elseif (!empty($this->input->post('query_index', TRUE)))
			{
				//Query is stored in the session data. So get it from there
				$query = $this->session->{'query_'.$index};
				$this->search_model->import_query($query);

				if (!empty($this->input->post('from_date' , TRUE)))
				{
					$this->search_model->from_date($this->input->post('from_date' , TRUE));
				}
				if(!empty($this->input->post('to_date', TRUE)))
				{
					$this->search_model->to_date($this->input->post('to_date', TRUE));
				}
			}
			else
			{
				$this->search_model
					->keywords($this->input->post('keywords', TRUE))
					->keywords_in($this->input->post('kfilters'), TRUE)
					->keywords_type($this->input->post('ksearch_type', TRUE))
					->from_date($this->input->post('from_date' , TRUE))
					->to_date($this->input->post('to_date', TRUE))
					->action_types($this->input->post('action_types[]', TRUE))
					->users($this->input->post('users[]', TRUE))
					->projects($this->input->post('projects[]', TRUE))
					->teams($this->input->post('teams[]', TRUE))
					->null_projects($this->input->post('null_projects', TRUE))
					->null_teams($this->input->post('null_teams', TRUE));
			}

			$offset = 0; //Reset Offset
		}
		elseif (!empty($this->session->flashed_search_query))
		{
			//Search Query was falshed so use that
			$query = $this->session->flashed_search_query;
			$this->search_model->import_query($query);
		}
		else
		{
			// Just retreive last_search_query
			$query = $this->session->last_search_query;
			$this->search_model->import_query($query);
		}

		//Store query
		$query = $this->search_model->export_query();
		$this->session->set_userdata('last_search_query', $query);

		//Getting the back URL
		if (!empty($this->input->post('back_url', TRUE)))
		{
			$back_url = $this->input->post('back_url', TRUE);
			$this->session->set_userdata('back_url', $back_url);
		}
		else
		{
			$back_url = $this->session->back_url;
		}
		$data['back_url'] = $back_url;
		
		//Apply pagination
		$this->search_model->pagination(
			$this->config->item('per_page'), $offset
		);

		//Apply the sort order
		$this->search_model->sort(get_search_sort());

		//Get the data
		$search_data = $this->search_model->search();
		$data['num_rows'] = $this->search_model->unpaginated_rows;
		
		//Uncomment to show debug info on search page
		// echo $this->search_model->get_debug();
		
		//Turn Data into Table
		$data['table'] = $this->table->my_generate($search_data);

		//Get sort options
		$data['sort_options'] = get_sort_dropdown();

		$this->load->library('pagination');
		$data['page_links'] = $this->pagination->my_create_links($data['num_rows'], 'Search/result/');

		$data['title'] = 'Search Results';
		$data['header'] = array(
			'colour' => 'is-info',
			'text'   => 'Results'
		);
		
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

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('search/tabs', $data);
			$this->load->view('search/view_tables/table_selection');
		}
		else
		{
			$data['table_data'] = $this->get_model->get_all_entries($table, $offset);
			$data['num_rows'] = $this->get_model->total_rows;
			$data['table_name'] = humanize($table);
			$data['table'] = $this->table->my_generate($data['table_data']);

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
}

/* End of file Search.php */
/* Location: ./application/controllers/Search.php */