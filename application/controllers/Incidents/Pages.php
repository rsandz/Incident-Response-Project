<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pages Controller
 * ================
 * @author Ryan Sandoval
 * @package Investigation
 * 
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
		$this->load->library('investigation');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
		$this->authentication->check_admin();
	}

	/**
	 * Main page for the Incidents functionality.
	 * Shows a recent incidents table, along with a control panel.
	 * @return [type] [description]
	 */
	public function index()
	{
		$data = array('title' => 'Incidents Overview');

		//Get Recent Incidents
		$recent_incidents = $this->investigation->recent_incidents(); // Contains 'num_rows' & 'data'

		//Tabulate the Data
		$this->load->library('table');
		$data['incidents_table'] = $this->table->my_generate($recent_incidents['data']);
		$data['total_rows'] = $recent_incidents['total_rows'];

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/main');
		$this->load->view('templates/footer');
	}

	/**
	 * The controller for the create incident page
	 * Contains the form for creating the incident
	 */
	public function create_incident()
	{
		//Load Resources
		$this->load->library('form_validation');

		$this->form_validation->set_rules('incident_name', 'Incident Name', 'required');
		$this->form_validation->set_rules('incident_date', 'Incident Date', 'required');

		if ($this->form_validation->run())
		{
			//Validation good, put into database
			$this->investigation
				->name($this->input->post('incident_name', TRUE))
				->date($this->input->post('incident_date', TRUE))
				->time($this->input->post('incident_time', TRUE))
				->desc($this->input->post('incident_desc', TRUE))
				->auto(FALSE)
				->create();

			//Success Page
			$data['title'] = 'Incident Created';
			$data['success_msg'] = 'The Incident has been created';
			$data['success_back_url'] = site_url('Incidents');
			
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('templates/success');
			$this->load->view('templates/footer');
		}
		else
		{
			$data = array('title' => 'Incidents');

			//Generate Error
			$data['errors'] = $this->load->view('templates/errors', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('incidents/create');
			$this->load->view('templates/footer');
		}
	}

}

/* End of file Pages.php */
/* Location: ./application/controllers/Incidents/Pages.php */