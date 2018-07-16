<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This is the controller for the pages in the incidents functinalilty.
 * It will be able to handle:
 * 	- New Incident creation
 * 	- Historical Incidents
 * 	- Running investigations
 */
class Pages extends CI_Controller {

	/**
	 * Constructor for this controller.
	 * Loads the Neccessary resources/libraries
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_model');
		$this->load->model('search_model');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
		$this->authentication->check_admin();
	}

	public function index()
	{
		$data = array('title' => 'Incidents Overview');

		//Get Recent Incidents
		$data['recent_incidents'] = NULL;

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/main');
		$this->load->view('templates/footer');
	}

	public function create_incident()
	{
		$data = array('title' => 'Incidents');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/create');
		$this->load->view('templates/footer');
	}

}

/* End of file Pages.php */
/* Location: ./application/controllers/Incidents/Pages.php */