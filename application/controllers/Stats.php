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
		$this->load->model('logging_model');
		$this->load->model('search_model');
		$this->load->helper('form');

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
		check_login(TRUE);
		
		$this->load->helper('form');

		$data['title'] = 'My Statistics';
		$data['header']['text'] = "My Statistics";

		$data['interval_options'] = array(
			'daily' => 'Daily',
			'weekly' => 'Weekly',
			'monthly' => 'Monthly',
			'yearly' => 'Yearly'
		);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('stats/tabs', $data);
		$this->load->view('stats/mystats', $data);
		$this->load->view('templates/footer');
	}

	
	public function project_stats()
	{
		check_login(TRUE);
	}

}

/* End of file stats.php */
/* Location: ./application/controllers/stats.php */