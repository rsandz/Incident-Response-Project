<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stats Controller
 * ================
 * @author Ryan Sandoval, June 2018
 *
 * This controller handles the stats functionality of the app. This includes user and project stats
 *
 * Most of the stats display functionality is handles by javascript since I used chart.js to create graphs.
 * This controller mostly deals with displaying text data. 
 */
class Stats extends CI_Controller {

	/**
	 * Constructs the Stats class.
	 *
	 * Loads all the necessary libraries, helpers and other resources.
	 * Also sets the default timezone. To configure the time zone, see config/appconfig and 
	 * 	refer to the PHP documentation for timezone values..
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('search_model');
		$this->load->model('statistics_model');
		$this->load->helper('form');
		$this->config->load('stats_config');
		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE

		$this->load->helper('user');
		check_login(TRUE); //Redirect if not logged in.
	}

	/**
	 * Main page for the stat controller.
	 * Provides an overview of statistics
	 */
	public function index()
	{

		$data['header'] = array(
			'text' => 'Statistics',
			'colour' => 'is-info'
		);
		$data['title'] = 'Statistics';

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/main.php', $data);
		$this->load->view('templates/footer');
	}

	/**
	 * Controls the User Stats (My Stats) page.
	 * 
	 * Also sets the interval options available. 
	 * 	 ==DO NOT CHANGE INTERVAL OPTIONS==
	 */
	public function my_stats()
	{
		
		$this->load->helper('form');

		$data['title'] = 'My Statistics';
		$data['header']['text'] = "My Statistics";

		$data['interval_options'] = $this->config->item('interval_options');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/mystats', $data);
		$this->load->view('stats/graph-search-form', $data);
		$this->load->view('templates/footer');
	}

	/**
	 * Controller for project_statistics pages
	 * @param  int $project_id The project id of the project to displays stats for
	 *                         If this is not provided, the project selection page will be shown
	 */
	public function project_stats($project_id = NULL)
	{
		$data['project_id'] = $project_id;

		if (isset($project_id))
		{

			$data['projects'] = $this->search_model->get_projects(check_admin());
			
			$data['title'] = 'Project Statistics';
			$data['header']['text'] = "Project Statistics";

			$data['interval_options'] = $this->config->item('interval_options');

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('stats/tabs', $data);
			$this->load->view('stats/projectstats-view', $data);
			$this->load->view('stats/graph-search-form', $data);
			$this->load->view('templates/footer');
		}
		else
		{
			$data['projects'] = $this->search_model->get_projects(check_admin());

			$data['title'] = 'Project Statistics';
			$data['header']['text'] = "Project Statistics";

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('stats/tabs', $data);
			$this->load->view('stats/projstats-select', $data);
			$this->load->view('templates/footer');
		}
	}

	/**
	 * Controller for team statistics pages
	 * @param  int $team_id The team id of which to display stats for.
	 *                      If this is not provided, then the team selection page will be shown
	 */
	public function team_stats($team_id = NULL)
	{
		$data['team_id'] = $team_id;

		if (!isset($team_id)) //Then we are selecting the team first
		{
			$data['header'] = array(
				'text' => 'Select a Team',
				'colour' => 'is-info');
			$data['title'] = 'Teams Statistics';

			$data['teams'] = $this->search_model->get_user_teams($this->session->user_id, check_admin());

			$data['team_modify_links'] = array_map(function($x)
				{
					return anchor("stats/team_stats/$x->team_id", "View", 'class="button is-info"');
				},
				$data['teams']);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('stats/tabs', $data);
			$this->load->view('stats/teamstats-select', $data);
			$this->load->view('templates/footer', $data);
		}
		else
		{
			//Display Team Statistics
			$data['header'] = array(
				'text' => 'Team Statistic',
				'colour' => 'is-info');
			$data['title'] = 'Team Statistic';
			$data['interval_options'] = $this->config->item('interval_options');

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('stats/tabs', $data);
			$this->load->view('stats/teamstats-view', $data);
			$this->load->view('stats/graph-search-form', $data);
			$this->load->view('templates/footer', $data);
		}
	}

	/**
	 * Controller for custom stats pages
	 *
	 * The custom stats pages works like such:
	 * 	1. The user sends in a custom query. This is saved in the session data.
	 * 	2. When the user returns to the custom stats page, the user is able to view the
	 * 		stats about the custom querry they sent in.
	 * 	3. The user is able to edit the search query they sent each time.
	 * 	4. The user can send in multiple unrelated search queries. The queries are identified
	 * 		by their index number.
	 * @param int $index The index number of the custom stats. Used to select the correct query.
	 */
	public function custom_stats($index)
	{
		$this->load->helper('search_helper');
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			//Must be a new query, so save it.
			$query = query_from_post();
			$query['back_url'] = current_url(); //Set back url
			$this->session->set_userdata('query_'.$index, $query);
		}
		else
		{
			//Load the query
			$query = $this->session->{'query_'.$index};

			//No previous query? Then make the user make one
			if (empty($query))
			{
				redirect('stats/create_custom/'.$index,'refresh');
			}
		}
		//Display the stats
		$data['header'] = array(
			'text' => 'Custom Statistic '.$index,
			'colour' => 'is-info');
		$data['title'] = 'Custom Statistic '.$index;
		$data['interval_options'] = $this->config->item('interval_options');
		$data['index'] = $index; //The custom stat id number.
		$data['query_string'] = query_to_string($query);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/custom_stats-view');
		$this->load->view('stats/graph-search-form', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Page controller for creating custom stats queries
	 * @param  int $index The ID/index of the custom stat
	 */
	public function create_custom($index)
	{
		$data['header'] = array(
			'text' => 'Create Custom Statistic '.$index,
			'colour' => 'is-info');
		$data['title'] = 'Create Custom Statistic '.$index;
		$data['index'] = $index; //The custom stat id number.

		//Get things to populate the form
		$data['action_types'] = $this->search_model->get_items('action_types');
		$data['teams'] = $this->search_model->get_items('teams');
		$data['projects'] = $this->search_model->get_items('projects');
		$data['users'] = $this->search_model->get_items('users');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/custom_stats-create', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Page Controller for comparing custom stats 1 and custom stats 2
	 */
	public function compare()
	{
		//Display the stats
		$data['header'] = array(
			'text' => 'Compare Statistic ',
			'colour' => 'is-info');
		$data['title'] = 'Compare Statistic ';
		$data['interval_options'] = $this->config->item('interval_options');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/compare-view');
		$this->load->view('stats/graph-search-form');
		$this->load->view('templates/footer', $data);
	}

}

/* End of file stats.php */
/* Location: ./application/controllers/stats.php */