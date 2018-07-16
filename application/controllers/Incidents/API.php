<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Controller
 * ==============
 * @author Ryan Sandoval
 * @version 1.0
 * @api
 * 
 * This contains all the methods for outside interfacing with the app
 */
class API extends CI_Controller {

	/**
	 * Constructor for the API Controller.
	 *
	 * Loads all necessary resources
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_model');
		$this->load->model('Form_get_model');
		$this->load->model('search_model');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
		if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') 
		{
			//If not acessed by post or get, redirect away
			redirect('home','refresh');
			show_error('No access allowed', 403);
		}
	}

	/**
	 * Index (Main) Method for site/api/
	 * Just Redirects the user away since normal users shouldn't be here
	 */
	public function index()
	{
		redirect('home','refresh');
		show_error('No access allowed', 403);
	}

	/**
	 * API endpoint for new incidents and existing incident queries
	 * 
	 * For new incidents:
	 * 	- The following must be provided within the incident json file.
	 * 		{
	 * 			name
	 * 			date
	 * 			time
	 * 			type
	 * 			desc
	 * 		}
	 * 
	 * @api
	 * 
	 */
	public function incidents()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			//Create a new Incident
		}
		else
		{
			//Return Information on incidents
		}
	}

}

/* End of file API.php */
/* Location: ./application/controllers/API.php */